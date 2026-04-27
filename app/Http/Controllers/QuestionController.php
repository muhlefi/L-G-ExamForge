<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Batch;
use App\Models\QuestionGroup;
use App\Services\PromptBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QuestionController extends Controller
{
    /* ----------------------------------------------------------------
     | SHOW – detail sesi + kelompok soal
     | ---------------------------------------------------------------*/
    public function show(Batch $batch)
    {
        $batch->load('questionGroups.questions');
        $batches = Batch::latest()->get();
        return view('batches.show', compact('batch', 'batches'));
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
        ]);

        $group = $batch->questionGroups()->create([
            'name'             => $validated['name'],
            'type'             => $validated['type'],
            'amount'           => $validated['amount'],
            'options_count'    => $validated['options_count'],
            'cognitive_level'  => $validated['cognitive_level'],
            'with_explanation' => $request->boolean('with_explanation'),
            'status'           => 'pending',
        ]);

        return redirect()->route('questions.index', ['batch_id' => $batch->id])
                         ->with('success', "Kelompok \"{$group->name}\" berhasil ditambahkan.");
    }

    /* ----------------------------------------------------------------
     | GENERATE a specific group
     | ---------------------------------------------------------------*/
    public function generateGroup(Request $request, QuestionGroup $group)
    {
        $batch  = $group->batch;
        $apiKey = env('GEMINI_API_KEY');

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
            $response = Http::timeout(60)->withHeaders([
                'X-goog-api-key' => $apiKey,
                'Content-Type'   => 'application/json',
            ])->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent',
                [
                    'contents'        => [['parts' => [['text' => $promptData['full_prompt']]]]],
                    'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 8192],
                ]
            );

            if ($response->successful()) {
                $raw     = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                $clean   = trim(str_replace(['```json', '```'], '', $raw));
                $soalData = json_decode($clean, true);

                if (isset($soalData['soal']) && count($soalData['soal']) > 0) {
                    // Delete old questions if re-generating
                    $group->questions()->delete();

                    foreach ($soalData['soal'] as $item) {
                        $safeTopic = preg_replace('/[^A-Za-z0-9 ]/', '', $batch->topic);
                        $imageUrl  = "https://image.pollinations.ai/prompt/"
                            . urlencode("educational illustration of {$safeTopic} school book style")
                            . "?width=600&height=400&seed=" . rand(1, 99999) . "&nologo=true";

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
                            'image_url'         => $imageUrl,
                        ]);
                    }

                    $group->update(['status' => 'done']);
                    $count = count($soalData['soal']);
                    return redirect()
                        ->route('questions.index', ['batch_id' => $batch->id])
                        ->with('success', "{$count} soal berhasil digenerate untuk kelompok \"{$group->name}\"!");
                }

                return back()->with('error', 'AI tidak menghasilkan soal. Coba lagi.');
            }

            $errMsg = $response->json()['error']['message'] ?? 'Unknown error';
            return back()->with('error', "Gemini API Error: {$errMsg}");

        } catch (\Exception $e) {
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
        return redirect()->route('questions.index', ['batch_id' => $batchId])
                         ->with('success', 'Soal berhasil dihapus.');
    }

    /* ----------------------------------------------------------------
     | DELETE entire group
     | ---------------------------------------------------------------*/
    public function destroyGroup(QuestionGroup $group)
    {
        $batchId = $group->batch_id;
        $group->delete(); // cascades to questions
        return redirect()->route('questions.index', ['batch_id' => $batchId])
                         ->with('success', 'Kelompok soal berhasil dihapus.');
    }
}
