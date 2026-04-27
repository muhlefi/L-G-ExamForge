<?php

namespace App\Services;

class PromptBuilder
{
    protected $jenisSoal;
    protected $jumlahSoal = 1;
    protected $tingkatSekolah;
    protected $topik;
    protected $mataPelajaran;
    protected $levelBloom;
    protected $denganPembahasan = false;
    protected $jumlahPilihan = 4;
    protected $batasanMateri = null;

    public function setJenisSoal($value) { $this->jenisSoal = $value; return $this; }
    public function setJumlahSoal($value) { $this->jumlahSoal = $value; return $this; }
    public function setTingkatSekolah($value) { $this->tingkatSekolah = $value; return $this; }
    public function setTopik($value) { $this->topik = $value; return $this; }
    public function setMataPelajaran($value) { $this->mataPelajaran = $value; return $this; }
    public function setLevelBloom($value) { $this->levelBloom = $value; return $this; }
    public function setJumlahPilihan($value) { $this->jumlahPilihan = (int) $value; return $this; }
    public function setBatasanMateri($value) { $this->batasanMateri = $value; return $this; }

    public function tambahkanPembahasan($value = true) { $this->denganPembahasan = $value; return $this; }

    public function build()
    {
        $jenisTeks = match($this->jenisSoal) {
            'pilgan' => "Pilihan Ganda ({$this->jumlahPilihan} opsi)",
            'isian'  => 'Isian Singkat',
            'esai'   => 'Esai',
            default  => $this->jenisSoal,
        };

        $prompt  = "Kamu adalah pakar pembuat soal standar Kurikulum Merdeka Indonesia.\n";
        $prompt .= "Buatkan tepat {$this->jumlahSoal} soal dalam Bahasa Indonesia.\n\n";
        $prompt .= "=== PARAMETER SOAL ===\n";
        $prompt .= "Mata Pelajaran : {$this->mataPelajaran}\n";
        $prompt .= "Topik          : {$this->topik}\n";
        $prompt .= "Jenis Soal     : {$jenisTeks}\n";
        $prompt .= "Jenjang        : " . strtoupper($this->tingkatSekolah) . "\n";
        $prompt .= "Level Bloom    : {$this->levelBloom}\n";

        if ($this->batasanMateri) {
            $prompt .= "Batasan Materi : {$this->batasanMateri}\n";
            $prompt .= "  → Soal HANYA boleh mencakup materi yang disebutkan di atas.\n";
        }

        if (stripos($this->mataPelajaran, 'matematika') !== false) {
            $prompt .= "\n[ATURAN MATEMATIKA] Fokuskan pada soal hitungan konkret. ";
            $prompt .= "Gunakan angka realistis, bukan hanya konsep teori.\n";
        }

        if ($this->denganPembahasan) {
            $prompt .= "\n[WAJIB] Sertakan pembahasan langkah demi langkah untuk setiap soal.\n";
        } else {
            $prompt .= "\n[WAJIB] Jangan sertakan pembahasan. Hanya soal dan jawaban.\n";
        }

        $prompt .= "\n=== FORMAT OUTPUT ===\n";
        $prompt .= "Kembalikan HANYA JSON murni (tanpa markdown, tanpa komentar).\n";

        if ($this->jenisSoal === 'pilgan') {
            $optionsJson = '[' . implode(', ', array_map(fn($i) => '"Opsi ' . chr(64 + $i) . '"', range(1, $this->jumlahPilihan))) . ']';
            $prompt .= "{\n  \"soal\": [\n    {\n";
            $prompt .= "      \"question_text\": \"teks soal\",\n";
            $prompt .= "      \"options\": {$optionsJson},\n";
            $prompt .= "      \"correct_answer\": \"opsi yang benar (tulis ulang teksnya)\"";
            if ($this->denganPembahasan) {
                $prompt .= ",\n      \"explanation\": \"pembahasan\"";
            }
            $prompt .= "\n    }\n  ]\n}";
        } else {
            $prompt .= "{\n  \"soal\": [\n    {\n";
            $prompt .= "      \"question_text\": \"teks soal\",\n";
            $prompt .= "      \"options\": null,\n";
            $prompt .= "      \"correct_answer\": \"jawaban singkat dan tepat\"";
            if ($this->denganPembahasan) {
                $prompt .= ",\n      \"explanation\": \"pembahasan\"";
            }
            $prompt .= "\n    }\n  ]\n}";
        }

        return ['full_prompt' => $prompt];
    }
}
