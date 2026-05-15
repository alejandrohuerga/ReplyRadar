<?php
namespace App\Services;

class KeywordMatchingService
{
    // Mapa de sinónimos y términos relacionados por dominio
    private array $synonymMap = [
        'pricing'      => ['price', 'prices', 'cost', 'costs', 'fee', 'fees', 'subscription',
                           'billing', 'payment', 'payments', 'plan', 'plans', 'tier', 'tiers',
                           'charge', 'charges', 'rate', 'rates', 'expensive', 'cheap', 'afford',
                           'budget', 'revenue', 'monetize', 'monetization'],

        'saas'         => ['software', 'app', 'tool', 'platform', 'service', 'product',
                           'startup', 'micro saas', 'microsaas', 'b2b', 'subscription',
                           'cloud', 'web app', 'webapp'],

        'marketing'    => ['growth', 'acquisition', 'traffic', 'seo', 'ads', 'campaign',
                           'funnel', 'conversion', 'leads', 'customers', 'users', 'audience',
                           'brand', 'content', 'social media', 'email', 'newsletter'],

        'development'  => ['coding', 'programming', 'developer', 'engineer', 'build',
                           'building', 'code', 'deploy', 'launch', 'ship', 'mvp'],

        'automation'   => ['automate', 'automated', 'workflow', 'zapier', 'integrate',
                           'integration', 'api', 'bot', 'script', 'schedule'],

        'analytics'    => ['metrics', 'data', 'tracking', 'dashboard', 'report', 'kpi',
                           'stats', 'statistics', 'measure', 'insight'],

        'customer'     => ['user', 'users', 'client', 'clients', 'buyer', 'buyers',
                           'audience', 'subscriber', 'subscribers', 'churn', 'retention'],

        'management'   => ['manage', 'managing', 'organize', 'organise', 'handle',
                           'track', 'tracking', 'monitor', 'workflow', 'process'],
    ];

    public function score(string $keyword, string $title, string $content = ''): float
    {
        $keyword     = strtolower(trim($keyword));
        $titleLower  = strtolower($title);
        $contentLower = strtolower($content);
        $fullText    = $titleLower . ' ' . $contentLower;

        // Expandir keyword con sinónimos
        $expandedTerms = $this->expandKeyword($keyword);
        $originalWords = array_filter(explode(' ', $keyword), fn($w) => strlen($w) > 2);

        $score = 0.0;

        // --- BLOQUE 1: Coincidencia exacta de la frase completa ---
        if (str_contains($titleLower, $keyword)) {
            return min(100, 90 + $this->densityBonus($fullText, $keyword));
        }
        if (str_contains($contentLower, $keyword)) {
            $score = max($score, 70 + $this->densityBonus($fullText, $keyword));
        }

        // --- BLOQUE 2: Todas las palabras originales en el título ---
        if (count($originalWords) > 0) {
            $titleHits = array_filter($originalWords, fn($w) => str_contains($titleLower, $w));
            if (count($titleHits) === count($originalWords)) {
                $score = max($score, 75 + $this->proximityScore($titleLower, $originalWords));
            }
        }

        // --- BLOQUE 3: Coincidencia semántica por sinónimos ---
        $semanticScore = $this->semanticScore($expandedTerms, $originalWords, $titleLower, $contentLower);
        $score = max($score, $semanticScore);

        // --- BLOQUE 4: Coincidencia parcial ponderada (fallback) ---
        if ($score === 0.0 && count($originalWords) > 0) {
            $titleMatches   = count(array_filter($originalWords, fn($w) => str_contains($titleLower, $w)));
            $contentMatches = count(array_filter($originalWords, fn($w) => str_contains($contentLower, $w)));
            $total          = count($originalWords);
            $weighted       = (($titleMatches * 2) + $contentMatches) / ($total * 3);
            $score          = round($weighted * 40, 1);
        }

        return min(100, max(0, round($score, 1)));
    }

    private function expandKeyword(string $keyword): array
    {
        $expanded = [];
        $words    = explode(' ', $keyword);

        foreach ($words as $word) {
            $expanded[] = $word;
            if (isset($this->synonymMap[$word])) {
                $expanded = array_merge($expanded, $this->synonymMap[$word]);
            }
        }

        return array_unique($expanded);
    }

    private function semanticScore(array $expandedTerms, array $originalWords, string $title, string $content): float
    {
        $originalCount = count($originalWords);
        if ($originalCount === 0) return 0;

        $coveredWords = 0;
        $titleCovered = 0;

        foreach ($originalWords as $orig) {
            $coveredInTitle   = false;
            $coveredInContent = false;

            // ¿Aparece la palabra original?
            if (str_contains($title, $orig)) {
                $coveredInTitle = true;
            } elseif (str_contains($content, $orig)) {
                $coveredInContent = true;
            }

            // ¿Aparece algún sinónimo DIRECTO de esa palabra concreta?
            if (!$coveredInTitle && !$coveredInContent) {
                $synonyms = $this->synonymMap[$orig] ?? [];
                foreach ($synonyms as $syn) {
                    if (strlen($syn) < 3) continue;
                    if (str_contains($title, $syn)) {
                        $coveredInTitle   = true;
                        break;
                    }
                    if (str_contains($content, $syn)) {
                        $coveredInContent = true;
                        break;
                    }
                }
            }

            if ($coveredInTitle || $coveredInContent) {
                $coveredWords++;
            }
            if ($coveredInTitle) {
                $titleCovered++;
            }
        }

        // Coverage: qué porcentaje de palabras del keyword están cubiertas
        $coverage    = $coveredWords / $originalCount;
        $titleBonus  = min(20, $titleCovered * 10);

        // Si no cubre todas las palabras del keyword, penalización progresiva
        $coveragePenalty = match(true) {
            $coverage >= 1.0 => 1.0,
            $coverage >= 0.5 => 0.7,
            default          => 0.4,
        };

        $base = $coverage * 70 * $coveragePenalty;

        return round(min(100, $base + $titleBonus), 1);
    }
    private function densityBonus(string $text, string $keyword): float
    {
        $count = substr_count($text, $keyword);
        return min(10, $count * 3);
    }

    private function proximityScore(string $title, array $words): float
    {
        $positions = [];
        foreach ($words as $word) {
            $pos = strpos($title, $word);
            if ($pos !== false) $positions[] = $pos;
        }
        if (count($positions) < 2) return 5;
        $spread = max($positions) - min($positions);
        return $spread < 15 ? 10 : ($spread < 40 ? 5 : 2);
    }
}