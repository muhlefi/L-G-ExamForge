<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Batch;
use App\Models\QuestionGroup;
use App\Services\PromptBuilder;
use App\Services\QuestionSvgRenderer;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class QuestionController extends Controller
{
    /* ----------------------------------------------------------------
     | SHOW – detail sesi + kelompok soal
     | ---------------------------------------------------------------*/
    public function show(Batch $batch)
    {
        $batch->load('questionGroups.questions');
        return view('batches.show', compact('batch'));
    }

    /* ----------------------------------------------------------------
     | ADD QUESTION GROUP to existing batch
     | ---------------------------------------------------------------*/
    public function storeGroup(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:50',
            'type'             => 'required|in:pilgan,isian,esai',
            'amount'           => 'required|integer|min:1|max:30',
            'options_count'    => 'required|integer|in:3,4,5',
            'cognitive_level'  => 'required|in:C1,C2,C3,C4,C5,C6',
            'with_explanation' => 'nullable|boolean',
            'with_image'       => 'nullable|boolean',
        ]);

        $group = $batch->questionGroups()->create([
            'name'             => $validated['name'],
            'type'             => $validated['type'],
            'amount'           => $validated['amount'],
            'options_count'    => $validated['options_count'],
            'cognitive_level'  => $validated['cognitive_level'],
            'with_explanation' => $request->boolean('with_explanation'),
            'with_image'       => $request->boolean('with_image'),
            'status'           => 'pending',
        ]);

        return redirect()->route('batches.show', $batch->id)
                         ->with('success', "Kelompok \"{$group->name}\" berhasil ditambahkan.");
    }

    /* ----------------------------------------------------------------
     | GENERATE a specific group
     | ---------------------------------------------------------------*/
    public function generateGroup(Request $request, QuestionGroup $group)
    {
        $batch  = $group->batch;
        $geminiConfig = config('services.gemini');
        $apiKey = (string) ($geminiConfig['api_key'] ?? '');
        $primaryModel = (string) ($geminiConfig['model'] ?? 'gemini-2.5-flash');
        $fallbackModels = is_array($geminiConfig['fallback_models'] ?? null)
            ? $geminiConfig['fallback_models']
            : [];
        $modelCandidates = array_values(array_unique(array_filter([
            $primaryModel,
            ...$fallbackModels,
        ])));
        $timeoutSeconds = max(30, (int) ($geminiConfig['timeout_seconds'] ?? 120));
        $connectTimeoutSeconds = max(5, (int) ($geminiConfig['connect_timeout_seconds'] ?? 15));
        $retryTimes = max(0, (int) ($geminiConfig['retry_times'] ?? 2));
        $retrySleepMs = max(0, (int) ($geminiConfig['retry_sleep_ms'] ?? 1500));
        $maxOutputTokens = max(1024, (int) ($geminiConfig['max_output_tokens'] ?? 4096));

        if (!$apiKey) {
            return back()->with('error', 'GEMINI_API_KEY belum diisi di .env');
        }

        $promptBuilder = (new PromptBuilder())
            ->setMataPelajaran($batch->subject)
            ->setJenisSoal($group->type)
            ->setJumlahSoal($group->amount)
            ->setTingkatSekolah($batch->school_level)
            ->setTopik($batch->topic)
            ->setLevelBloom($group->cognitive_level)
            ->setJumlahPilihan($group->options_count)
            ->setBatasanMateri($batch->material_scope);

        if ($group->with_explanation) {
            $promptBuilder->tambahkanPembahasan(true);
        }

        $promptData = $promptBuilder->build();

        try {
            $response = null;
            $lastError = null;
            $usedModel = $primaryModel;

            foreach ($modelCandidates as $model) {
                $usedModel = (string) $model;
                $response = Http::connectTimeout($connectTimeoutSeconds)
                    ->timeout($timeoutSeconds)
                    ->retry(
                        $retryTimes,
                        $retrySleepMs,
                        function (Throwable $exception, $request) {
                            if ($exception instanceof ConnectionException) {
                                return true;
                            }

                            if ($exception instanceof RequestException && $exception->response) {
                                $status = $exception->response->status();
                                return in_array($status, [408, 429, 500, 502, 503, 504], true);
                            }

                            return false;
                        },
                        false
                    )
                    ->withHeaders([
                        'X-goog-api-key' => $apiKey,
                        'Content-Type'   => 'application/json',
                    ])
                    ->post(
                        'https://generativelanguage.googleapis.com/v1beta/models/' . $usedModel . ':generateContent',
                        [
                            'contents'        => [['parts' => [['text' => $promptData['full_prompt']]]]],
                            'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => $maxOutputTokens],
                        ]
                    );

                if ($response->successful()) {
                    break;
                }

                $lastError = (string) ($response->json()['error']['message'] ?? ('HTTP ' . $response->status()));
                if (!$this->shouldTryNextGeminiModel($response->status(), $lastError)) {
                    break;
                }
            }

            if (!$response) {
                return back()->with('error', 'Gemini API Error: Tidak ada respons dari server.');
            }

            if ($response->successful()) {
                $raw     = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                $clean   = trim(str_replace(['```json', '```'], '', $raw));
                $soalData = json_decode($clean, true);

                if (isset($soalData['soal']) && count($soalData['soal']) > 0) {
                    // Delete old questions if re-generating
                    $group->questions()->delete();

                    foreach ($soalData['soal'] as $item) {
                        if (
                            !is_array($item) ||
                            !isset($item['question_text']) ||
                            !isset($item['correct_answer'])
                        ) {
                            continue;
                        }

                        Question::create([
                            'batch_id'          => $batch->id,
                            'question_group_id' => $group->id,
                            'subject'           => $batch->subject,
                            'question_text'     => $item['question_text'],
                            'type'              => $group->type,
                            'cognitive_level'   => $group->cognitive_level,
                            'school_level'      => $batch->school_level,
                            'topic'             => $batch->topic,
                            'options'           => $item['options'] ?? null,
                            'correct_answer'    => $item['correct_answer'],
                            'explanation'       => $item['explanation'] ?? null,
                            'image_url'         => $this->buildQuestionImageUrl($batch, $item, $group),
                        ]);
                    }

                    $count = $group->questions()->count();

                    if ($count < 1) {
                        return back()->with('error', 'Format output AI tidak valid. Coba generate ulang.');
                    }

                    $group->update(['status' => 'done']);
                    return redirect()
                        ->route('batches.show', $batch->id)
                        ->with('success', "{$count} soal berhasil digenerate untuk kelompok \"{$group->name}\"! (Model: {$usedModel})");
                }

                return back()->with('error', 'AI tidak menghasilkan soal. Coba lagi.');
            }

            $errMsg = $lastError ?? ($response->json()['error']['message'] ?? ('HTTP ' . $response->status()));
            return back()->with('error', "Gemini API Error: {$errMsg}");

        } catch (ConnectionException $e) {
            return back()->with('error', 'Koneksi ke Gemini timeout. Coba lagi, atau turunkan jumlah soal per group agar respons lebih cepat.');
        } catch (Throwable $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /* ----------------------------------------------------------------
     | CRUD – update single question
     | ---------------------------------------------------------------*/
    public function update(Request $request, Question $question)
    {
        $question->update($request->validate([
            'question_text'  => 'required|string',
            'correct_answer' => 'required|string',
            'explanation'    => 'nullable|string',
        ]));

        return back()->with('success', 'Soal berhasil diperbarui!');
    }

    /* ----------------------------------------------------------------
     | CRUD – delete single question
     | ---------------------------------------------------------------*/
    public function destroy(Question $question)
    {
        $batchId = $question->batch_id;
        $question->delete();
        return redirect()->route('batches.show', $batchId)
                         ->with('success', 'Soal berhasil dihapus.');
    }

    /* ----------------------------------------------------------------
     | DELETE entire group
     | ---------------------------------------------------------------*/
    public function destroyGroup(QuestionGroup $group)
    {
        $batchId = $group->batch_id;
        $group->delete(); // cascades to questions
        return redirect()->route('batches.show', $batchId)
                         ->with('success', 'Kelompok soal berhasil dihapus.');
    }

    private function buildQuestionImageUrl(Batch $batch, array $item, QuestionGroup $group): ?string
    {
        if (!$group->with_image) {
            return null;
        }

        $questionText = trim((string) ($item['question_text'] ?? ''));
        $questionText = preg_replace('/\s+/u', ' ', $questionText) ?? $questionText;
        $questionSnippet = Str::limit($questionText, 110, '');

        if ($questionSnippet === '') {
            return null;
        }

        $svg = app(QuestionSvgRenderer::class)->render(
            (string) $batch->subject,
            (string) $batch->topic,
            (string) $batch->school_level,
            $questionSnippet
        );

        $hash = sha1(implode('|', [
            (string) $batch->id,
            (string) $group->id,
            $questionSnippet,
            (string) $group->type,
            (string) $group->cognitive_level,
        ]));

        $path = 'question-visuals/' . $hash . '.svg';
        Storage::disk('public')->put($path, $svg);

        return Storage::disk('public')->url($path);
    }

    private function shouldTryNextGeminiModel(int $status, string $message): bool
    {
        if (in_array($status, [429, 500, 502, 503, 504], true)) {
            return true;
        }

        $msg = Str::lower($message);

        return str_contains($msg, 'high demand')
            || str_contains($msg, 'resource_exhausted')
            || str_contains($msg, 'temporarily unavailable')
            || str_contains($msg, 'quota');
    }
}
