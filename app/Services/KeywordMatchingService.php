<?php
namespace App\Services;

class KeywordMatchingService
{
    private array $synonymMap;
    private array $config;

    private array $domainSynonyms;

    public function __construct()
    {
        $this->synonymMap = config('replyradar.keyword_synonyms', []);
        $this->config = config('replyradar.matching', []);

        $this->domainSynonyms = [
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
    }

    public function score(string $keyword, string $title, string $content = ''): float
    {
        $keyword      = strtolower(trim($keyword));
        $titleLower   = strtolower($title);
        $contentLower = strtolower($content);
        $fullText     = $titleLower . ' ' . $contentLower;
        $minWordLen   = $this->config['min_word_length'] ?? 3;

        $originalWords = array_filter(explode(' ', $keyword), fn($w) => strlen($w) >= $minWordLen);
        if (empty($originalWords)) return 0;

        $expandedTerms = $this->expandKeyword($keyword, $originalWords);

        $score = 0.0;

        // --- BLOQUE 1: Frase exacta ---
        if (str_contains($titleLower, $keyword)) {
            return min(100, ($this->config['exact_phrase_title_bonus'] ?? 90) + $this->densityBonus($fullText, $keyword));
        }
        if (str_contains($contentLower, $keyword)) {
            $score = max($score, ($this->config['exact_phrase_content_bonus'] ?? 70) + $this->densityBonus($fullText, $keyword));
        }

        // --- BLOQUE 2: N-gramas - coincidencia de subfrases significativas ---
        $ngramScore = $this->ngramScore($keyword, $titleLower, $contentLower, $originalWords);
        $score = max($score, $ngramScore);

        // --- BLOQUE 3: Todas las palabras en el título ---
        if (count($originalWords) > 0) {
            $titleHits = array_filter($originalWords, fn($w) => str_contains($titleLower, $w));
            if (count($titleHits) === count($originalWords)) {
                $proximity = $this->proximityScore($titleLower, $originalWords);
                $score = max($score, ($this->config['all_words_title_bonus'] ?? 75) + $proximity);
            }
        }

        // --- BLOQUE 4: Coincidencia semántica (sinónimos) ---
        $semanticScore = $this->semanticScore($expandedTerms, $originalWords, $titleLower, $contentLower);
        $score = max($score, $semanticScore);

        // --- BLOQUE 5: Dominio matching ---
        $domainScore = $this->domainMatchScore($originalWords, $titleLower, $contentLower);
        $score = max($score, $domainScore);

        // --- BLOQUE 6: Fallback ponderado ---
        if ($score < 20 && count($originalWords) > 0) {
            $titleMatches   = count(array_filter($originalWords, fn($w) => str_contains($titleLower, $w)));
            $contentMatches = count(array_filter($originalWords, fn($w) => str_contains($contentLower, $w)));
            $total          = count($originalWords);
            $weighted       = (($titleMatches * 2) + $contentMatches) / ($total * 3);
            $score          = max($score, round($weighted * ($this->config['partial_match_max'] ?? 40), 1));
        }

        return min(100, max(0, round($score, 1)));
    }

    private function ngramScore(string $keyword, string $title, string $content, array $words): float
    {
        if (count($words) < 2) return 0;

        $bestScore = 0.0;

        // Bigramas: pares de palabras consecutivas
        for ($i = 0; $i < count($words) - 1; $i++) {
            $bigram = $words[$i] . ' ' . $words[$i + 1];
            if (str_contains($title, $bigram)) {
                $bestScore = max($bestScore, 85 + $this->densityBonus($title, $bigram));
            } elseif (str_contains($content, $bigram)) {
                $bestScore = max($bestScore, 60);
            }
        }

        // Trigramas
        for ($i = 0; $i < count($words) - 2; $i++) {
            $trigram = $words[$i] . ' ' . $words[$i + 1] . ' ' . $words[$i + 2];
            if (str_contains($title, $trigram)) {
                $bestScore = max($bestScore, 92);
            } elseif (str_contains($content, $trigram)) {
                $bestScore = max($bestScore, 72);
            }
        }

        return $bestScore;
    }

    private function expandKeyword(string $keyword, array $originalWords): array
    {
        $expanded = [];

        foreach ($originalWords as $word) {
            $expanded[$word] = true;

            if (isset($this->synonymMap[$word])) {
                foreach ($this->synonymMap[$word] as $syn) {
                    $expanded[$syn] = true;
                }
            }

            foreach ($this->domainSynonyms as $domain => $synonyms) {
                if (in_array($word, $synonyms) || $word === $domain) {
                    foreach ($synonyms as $syn) {
                        $expanded[$syn] = true;
                    }
                }
            }
        }

        return array_keys($expanded);
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

            if (str_contains($title, $orig)) {
                $coveredInTitle = true;
            } elseif (str_contains($content, $orig)) {
                $coveredInContent = true;
            }

            if (!$coveredInTitle && !$coveredInContent) {
                $synonyms = $this->synonymMap[$orig] ?? $this->domainSynonyms[$orig] ?? [];
                foreach ($synonyms as $syn) {
                    if (strlen($syn) < 3) continue;
                    if (str_contains($title, $syn)) {
                        $coveredInTitle = true;
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

        $coverage = $coveredWords / $originalCount;
        $titleBonus = min(20, $titleCovered * 8);
        $synonymBoost = $this->config['synonym_match_boost'] ?? 0.7;

        $coveragePenalty = match (true) {
            $coverage >= 1.0   => 1.0,
            $coverage >= 0.75  => 0.85,
            $coverage >= 0.5   => 0.65,
            default            => 0.35,
        };

        $base = $coverage * 70 * $coveragePenalty * $synonymBoost;

        return round(min(100, $base + $titleBonus), 1);
    }

    private function domainMatchScore(array $words, string $title, string $content): float
    {
        $text = $title . ' ' . $content;
        $score = 0.0;

        foreach ($this->domainSynonyms as $domain => $synonyms) {
            foreach ($words as $word) {
                if (!in_array($word, $synonyms) && $word !== $domain) continue;

                $hits = 0;
                foreach ($synonyms as $syn) {
                    if (strlen($syn) < 3) continue;
                    if (str_contains($text, $syn)) {
                        $hits++;
                    }
                }

                if ($hits > 0) {
                    $domainScore = min(65, $hits * 8 + (str_contains($title, $word) ? 15 : 0));
                    $score = max($score, $domainScore);
                }
            }
        }

        return $score;
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
        $maxSpread = $this->config['proximity_max_spread'] ?? 40;

        return match (true) {
            $spread < 15  => 12,
            $spread < 25  => 8,
            $spread < $maxSpread => 5,
            default       => 2,
        };
    }
}
