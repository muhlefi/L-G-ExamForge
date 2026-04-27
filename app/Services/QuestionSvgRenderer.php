<?php

namespace App\Services;

class QuestionSvgRenderer
{
    public function render(string $subject, string $topic, string $schoolLevel, string $questionText): string
    {
        $normalized = trim(preg_replace('/\s+/u', ' ', $questionText) ?? $questionText);
        $lower = mb_strtolower($normalized);

        if (str_contains($lower, 'garis bilangan')) {
            return $this->renderNumberLine($subject, $topic, $schoolLevel, $normalized);
        }

        if (
            str_contains($lower, 'koordinat') ||
            str_contains($lower, 'grafik') ||
            preg_match('/\by\s*=\s*[-+0-9x]/i', $normalized)
        ) {
            return $this->renderCartesian($subject, $topic, $schoolLevel, $normalized);
        }

        if (
            str_contains($lower, 'segitiga') ||
            str_contains($lower, 'persegi') ||
            str_contains($lower, 'persegi panjang') ||
            str_contains($lower, 'lingkaran')
        ) {
            return $this->renderGeometry($subject, $topic, $schoolLevel, $normalized);
        }

        if (
            str_contains($lower, 'tabel') ||
            str_contains($lower, 'frekuensi') ||
            str_contains($lower, 'data')
        ) {
            return $this->renderTable($subject, $topic, $schoolLevel, $normalized);
        }

        return $this->renderCard($subject, $topic, $schoolLevel, $normalized);
    }

    private function renderNumberLine(string $subject, string $topic, string $schoolLevel, string $question): string
    {
        preg_match_all('/-?\d+/', $question, $matches);
        $target = isset($matches[0][0]) ? (int) $matches[0][0] : 3;
        $target = max(-8, min(8, $target));

        $ticks = '';
        for ($i = -8; $i <= 8; $i++) {
            $x = 100 + (($i + 8) * 40);
            $ticks .= '<line x1="' . $x . '" y1="250" x2="' . $x . '" y2="270" stroke="#334155" stroke-width="1"/>';
            if ($i % 2 === 0) {
                $ticks .= '<text x="' . $x . '" y="292" text-anchor="middle" font-size="13" fill="#0f172a">' . $i . '</text>';
            }
        }

        $markerX = 100 + (($target + 8) * 40);
        $lines = $this->questionLines($question, 64, 2);

        return $this->svgWrapper(
            $subject,
            $topic,
            $schoolLevel,
            '
            <line x1="100" y1="260" x2="740" y2="260" stroke="#0f172a" stroke-width="2.5"/>
            <polygon points="740,260 730,255 730,265" fill="#0f172a"/>
            ' . $ticks . '
            <circle cx="' . $markerX . '" cy="260" r="9" fill="#2563eb"/>
            <text x="' . $markerX . '" y="235" text-anchor="middle" font-size="14" font-weight="700" fill="#1d4ed8">target</text>
            <text x="100" y="330" font-size="16" font-weight="700" fill="#0f172a">Garis Bilangan</text>
            <text x="100" y="356" font-size="14" fill="#334155">' . $this->escape($lines[0]) . '</text>
            <text x="100" y="380" font-size="14" fill="#334155">' . $this->escape($lines[1]) . '</text>
            '
        );
    }

    private function renderCartesian(string $subject, string $topic, string $schoolLevel, string $question): string
    {
        $grid = '';
        for ($i = 0; $i <= 10; $i++) {
            $pos = 170 + ($i * 46);
            $grid .= '<line x1="' . $pos . '" y1="120" x2="' . $pos . '" y2="410" stroke="#dbeafe" stroke-width="1"/>';
            $grid .= '<line x1="170" y1="' . $pos . '" x2="630" y2="' . $pos . '" stroke="#dbeafe" stroke-width="1"/>';
        }

        $lines = $this->questionLines($question, 64, 2);

        return $this->svgWrapper(
            $subject,
            $topic,
            $schoolLevel,
            '
            <rect x="170" y="120" width="460" height="290" rx="10" fill="#f8fbff" stroke="#bfdbfe"/>
            ' . $grid . '
            <line x1="170" y1="265" x2="630" y2="265" stroke="#0f172a" stroke-width="2"/>
            <line x1="400" y1="120" x2="400" y2="410" stroke="#0f172a" stroke-width="2"/>
            <line x1="216" y1="355" x2="584" y2="177" stroke="#2563eb" stroke-width="3"/>
            <circle cx="308" cy="311" r="6" fill="#1d4ed8"/>
            <circle cx="492" cy="221" r="6" fill="#1d4ed8"/>
            <text x="595" y="255" font-size="13" fill="#0f172a">x</text>
            <text x="410" y="136" font-size="13" fill="#0f172a">y</text>
            <text x="100" y="330" font-size="16" font-weight="700" fill="#0f172a">Bidang Koordinat</text>
            <text x="100" y="356" font-size="14" fill="#334155">' . $this->escape($lines[0]) . '</text>
            <text x="100" y="380" font-size="14" fill="#334155">' . $this->escape($lines[1]) . '</text>
            '
        );
    }

    private function renderGeometry(string $subject, string $topic, string $schoolLevel, string $question): string
    {
        $lines = $this->questionLines($question, 64, 2);

        return $this->svgWrapper(
            $subject,
            $topic,
            $schoolLevel,
            '
            <polygon points="220,360 320,180 420,360" fill="#dbeafe" stroke="#1d4ed8" stroke-width="3"/>
            <rect x="470" y="210" width="180" height="150" rx="8" fill="#dcfce7" stroke="#15803d" stroke-width="3"/>
            <circle cx="145" cy="220" r="58" fill="#fee2e2" stroke="#b91c1c" stroke-width="3"/>
            <text x="220" y="378" font-size="12" fill="#1e3a8a">A</text>
            <text x="318" y="168" font-size="12" fill="#1e3a8a">B</text>
            <text x="418" y="378" font-size="12" fill="#1e3a8a">C</text>
            <text x="100" y="330" font-size="16" font-weight="700" fill="#0f172a">Bangun Datar</text>
            <text x="100" y="356" font-size="14" fill="#334155">' . $this->escape($lines[0]) . '</text>
            <text x="100" y="380" font-size="14" fill="#334155">' . $this->escape($lines[1]) . '</text>
            '
        );
    }

    private function renderTable(string $subject, string $topic, string $schoolLevel, string $question): string
    {
        $lines = $this->questionLines($question, 64, 2);

        return $this->svgWrapper(
            $subject,
            $topic,
            $schoolLevel,
            '
            <rect x="140" y="150" width="560" height="220" rx="10" fill="#ffffff" stroke="#94a3b8" stroke-width="2"/>
            <line x1="140" y1="205" x2="700" y2="205" stroke="#94a3b8" stroke-width="2"/>
            <line x1="140" y1="260" x2="700" y2="260" stroke="#cbd5e1" stroke-width="1.5"/>
            <line x1="140" y1="315" x2="700" y2="315" stroke="#cbd5e1" stroke-width="1.5"/>
            <line x1="330" y1="150" x2="330" y2="370" stroke="#cbd5e1" stroke-width="1.5"/>
            <line x1="515" y1="150" x2="515" y2="370" stroke="#cbd5e1" stroke-width="1.5"/>
            <text x="170" y="186" font-size="15" font-weight="700" fill="#0f172a">Nilai</text>
            <text x="375" y="186" font-size="15" font-weight="700" fill="#0f172a">Frekuensi</text>
            <text x="560" y="186" font-size="15" font-weight="700" fill="#0f172a">f.x</text>
            <text x="100" y="330" font-size="16" font-weight="700" fill="#0f172a">Tabel Data</text>
            <text x="100" y="356" font-size="14" fill="#334155">' . $this->escape($lines[0]) . '</text>
            <text x="100" y="380" font-size="14" fill="#334155">' . $this->escape($lines[1]) . '</text>
            '
        );
    }

    private function renderCard(string $subject, string $topic, string $schoolLevel, string $question): string
    {
        $lines = $this->questionLines($question, 64, 4);

        return $this->svgWrapper(
            $subject,
            $topic,
            $schoolLevel,
            '
            <rect x="100" y="145" width="640" height="255" rx="14" fill="#f8fafc" stroke="#cbd5e1" stroke-width="2"/>
            <text x="130" y="190" font-size="15" font-weight="700" fill="#0f172a">Visual Ringkas Soal</text>
            <line x1="130" y1="205" x2="710" y2="205" stroke="#cbd5e1" stroke-width="1"/>
            <text x="130" y="240" font-size="14" fill="#334155">' . $this->escape($lines[0]) . '</text>
            <text x="130" y="270" font-size="14" fill="#334155">' . $this->escape($lines[1]) . '</text>
            <text x="130" y="300" font-size="14" fill="#334155">' . $this->escape($lines[2]) . '</text>
            <text x="130" y="330" font-size="14" fill="#334155">' . $this->escape($lines[3]) . '</text>
            '
        );
    }

    private function svgWrapper(string $subject, string $topic, string $schoolLevel, string $body): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<svg xmlns="http://www.w3.org/2000/svg" width="840" height="520" viewBox="0 0 840 520">'
            . '<defs>'
            . '<linearGradient id="bg" x1="0" x2="1" y1="0" y2="1">'
            . '<stop offset="0%" stop-color="#f8fafc"/>'
            . '<stop offset="100%" stop-color="#eef2ff"/>'
            . '</linearGradient>'
            . '</defs>'
            . '<rect x="0" y="0" width="840" height="520" fill="url(#bg)"/>'
            . '<rect x="50" y="40" width="740" height="70" rx="12" fill="#ffffff" stroke="#cbd5e1"/>'
            . '<text x="75" y="70" font-size="14" fill="#475569">Mata Pelajaran: ' . $this->escape($subject) . '</text>'
            . '<text x="75" y="92" font-size="13" fill="#64748b">Topik: ' . $this->escape($topic) . ' | Jenjang: ' . $this->escape($schoolLevel) . '</text>'
            . $body
            . '</svg>';
    }

    private function questionLines(string $question, int $lineLength, int $lineCount): array
    {
        $text = trim(preg_replace('/\s+/u', ' ', $question) ?? $question);
        if ($text === '') {
            return array_fill(0, $lineCount, '');
        }

        $words = preg_split('/\s+/', $text) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;
            if (mb_strlen($candidate) <= $lineLength) {
                $current = $candidate;
                continue;
            }

            $lines[] = $current;
            $current = $word;
            if (count($lines) >= $lineCount - 1) {
                break;
            }
        }

        if (count($lines) < $lineCount && $current !== '') {
            $lines[] = $current;
        }

        while (count($lines) < $lineCount) {
            $lines[] = '';
        }

        return $lines;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}

