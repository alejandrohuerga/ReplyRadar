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

        $exactPhraseTitleBonus = $this->config['exact_phrase_title_bonus'] ?? 80;
        $exactPhraseContentBonus = $this->config['exact_phrase_content_bonus'] ?? 55;

        // --- BLOQUE 1: Frase exacta en título (máxima confianza) ---
        if (str_contains($titleLower, $keyword)) {
            return min(100, $exactPhraseTitleBonus + $this->densityBonus($fullText, $keyword));
        }

        // --- BLOQUE 1B: Frase exacta en contenido ---
        if (str_contains($contentLower, $keyword)) {
            $score = max($score, $exactPhraseContentBonus + $this->densityBonus($fullText, $keyword));
        }

        // --- BLOQUE 2: Todas las palabras exactas en el título ---
        if (count($originalWords) > 0) {
            $titleHits = array_filter($originalWords, fn($w) => str_contains($titleLower, $w));
            if (count($titleHits) === count($originalWords)) {
                $proximity = $this->proximityScore($titleLower, $originalWords);
                $allWordsBonus = $this->config['all_words_title_bonus'] ?? 60;
                $score = max($score, $allWordsBonus + $proximity);
            }
        }

        // --- BLOQUE 3: N-gramas (solo si hay 2+ palabras) ---
        if (count($originalWords) >= 2) {
            $ngramScore = $this->ngramScore($keyword, $titleLower, $contentLower, $originalWords);
            $score = max($score, $ngramScore);
        }

        // --- BLOQUE 4: Coincidencia semántica (sinónimos) ---
        $semanticScore = $this->semanticScore($expandedTerms, $originalWords, $titleLower, $contentLower);
        $score = max($score, $semanticScore);

        // --- BLOQUE 5: Fallback ponderado (solo palabras en título) ---
        if ($score < 15 && count($originalWords) > 0) {
            $titleMatches = count(array_filter($originalWords, fn($w) => str_contains($titleLower, $w)));
            $total        = count($originalWords);
            $weighted     = $titleMatches / $total;
            $partialMax   = $this->config['partial_match_max'] ?? 25;
            $score        = max($score, round($weighted * $partialMax, 1));
        }

        return min(100, max(0, round($score, 1)));
    }

    private function ngramScore(string $keyword, string $title, string $content, array $words): float
    {
        $bestScore = 0.0;

        for ($i = 0; $i < count($words) - 1; $i++) {
            $bigram = $words[$i] . ' ' . $words[$i + 1];
            if (str_contains($title, $bigram)) {
                $bestScore = max($bestScore, 70 + $this->densityBonus($title, $bigram));
            } elseif (str_contains($content, $bigram)) {
                $bestScore = max($bestScore, 45);
            }
        }

        if (count($words) >= 3) {
            for ($i = 0; $i < count($words) - 2; $i++) {
                $trigram = $words[$i] . ' ' . $words[$i + 1] . ' ' . $words[$i + 2];
                if (str_contains($title, $trigram)) {
                    $bestScore = max($bestScore, 78);
                } elseif (str_contains($content, $trigram)) {
                    $bestScore = max($bestScore, 55);
                }
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
            }

            if (!$coveredInTitle && !$coveredInContent) {
                $synonyms = $this->synonymMap[$orig] ?? [];
                foreach ($synonyms as $syn) {
                    if (strlen($syn) < 3) continue;
                    if (str_contains($title, $syn)) {
                        $coveredInTitle = true;
                        break;
                    }
                }
            }

            if (!$coveredInTitle && !$coveredInContent) {
                if (str_contains($content, $orig)) {
                    $coveredInContent = true;
                }
            }

            if ($coveredInTitle || $coveredInContent) {
                $coveredWords++;
            }
            if ($coveredInTitle) {
                $titleCovered++;
            }
        }

        if ($titleCovered === 0 && $coveredWords === 0) return 0;

        $coverage = $coveredWords / $originalCount;
        $titleBonus = min(15, $titleCovered * 5);
        $synonymBoost = $this->config['synonym_match_boost'] ?? 0.5;

        $coveragePenalty = match (true) {
            $coverage >= 1.0   => 0.85,
            $coverage >= 0.75  => 0.65,
            $coverage >= 0.5   => 0.45,
            default            => 0.20,
        };

        $base = $coverage * 50 * $coveragePenalty * $synonymBoost;

        return round(min(70, $base + $titleBonus), 1);
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
                    if (str_contains($title, $syn)) {
                        $hits++;
                    }
                }

                if ($hits > 0) {
                    $domainScore = min(40, $hits * 6 + (str_contains($title, $word) ? 10 : 0));
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
