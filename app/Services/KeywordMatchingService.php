<?php
namespace App\Services;

class KeywordMatchingService
{
    public function score(string $keyword, string $title, string $content = ''): float
    {
        $keyword  = strtolower(trim($keyword));
        $title    = strtolower($title);
        $content  = strtolower($content);
        $fullText = $title . ' ' . $content;

        $words      = array_filter(explode(' ', $keyword), fn($w) => strlen($w) > 2);
        $totalWords = count($words);

        if ($totalWords === 0) return 0;

        // 1. Coincidencia exacta de la frase completa en título → score máximo
        if (str_contains($title, $keyword)) {
            return min(100, 85 + $this->densityBonus($fullText, $keyword));
        }

        // 2. Coincidencia exacta en contenido
        if (str_contains($content, $keyword)) {
            return min(100, 65 + $this->densityBonus($fullText, $keyword));
        }

        // 3. Todas las palabras presentes en título
        $wordsInTitle = array_filter($words, fn($w) => str_contains($title, $w));
        if (count($wordsInTitle) === $totalWords) {
            return min(100, 55 + $this->proximityScore($title, $words));
        }

        // 4. Coincidencia parcial ponderada
        $titleMatches   = count(array_filter($words, fn($w) => str_contains($title, $w)));
        $contentMatches = count(array_filter($words, fn($w) => str_contains($content, $w)));

        // Título vale el doble que el contenido
        $weightedScore = (($titleMatches * 2) + $contentMatches) / (($totalWords * 2) + $totalWords);
        $base          = round($weightedScore * 50, 1);

        // Penalización si ninguna palabra del keyword aparece
        if ($titleMatches === 0 && $contentMatches === 0) return 0;

        return min(100, $base);
    }

    // Bonus por densidad: cuántas veces aparece el keyword en el texto
    private function densityBonus(string $text, string $keyword): float
    {
        $count = substr_count($text, $keyword);
        return min(15, $count * 5);
    }

    // Bonus por proximidad: las palabras del keyword están cerca entre sí en el título
    private function proximityScore(string $title, array $words): float
    {
        $positions = [];
        foreach ($words as $word) {
            $pos = strpos($title, $word);
            if ($pos !== false) $positions[] = $pos;
        }

        if (count($positions) < 2) return 5;

        $spread = max($positions) - min($positions);
        // Menor spread = palabras más juntas = más relevante
        return $spread < 20 ? 15 : ($spread < 50 ? 8 : 3);
    }
}