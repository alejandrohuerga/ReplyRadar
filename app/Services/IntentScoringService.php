<?php
namespace App\Services;

class IntentScoringService
{
    private array $buyerIntent;
    private array $spamSignals;
    private array $discussionSignals;
    private array $urgencySignals;
    private array $budgetSignals;
    private array $depthDetailIndicators;

    public function __construct()
    {
        $this->buyerIntent = [
            'looking for a tool'        => 95,  'looking for software'  => 95,
            'is there an app'           => 95,  'is there a tool'       => 95,
            'recommend a tool'          => 92,  'best tool for'         => 90,
            'what do you use for'       => 90,  'how do you handle'     => 88,
            'tired of paying'           => 95,  'too expensive'         => 88,
            'alternative to'            => 87,  'alternatives to'       => 87,
            'switched from'             => 85,  'cancelled my'          => 85,
            'frustrated with'           => 88,  'sick of'               => 85,
            'wish there was'            => 90,  'need something that'   => 88,
            'anyone use'                => 82,  'does anyone know'      => 82,
            'how much do you pay'       => 90,  'is it worth'           => 80,
            'worth the price'           => 82,  'pricing is insane'     => 92,
            'pricing too high'          => 92,  'cant afford'           => 88,
            "can't afford"              => 88,  'how do i'              => 75,
            'how can i'                 => 75,  'struggling with'       => 80,
            'help me'                   => 72,  'best way to'           => 70,
            'need help with'            => 78,  'trying to figure out'  => 80,
            'what is the best'          => 70,  'should i use'          => 72,
            'how do you'                => 68,  'looking for a way'     => 85,
            'looking for an app'        => 92,  'looking for software'  => 93,
            'looking for service'       => 88,  'need a tool'           => 90,
            'need an app'               => 90,  'need software'         => 88,
            'recommendations for'       => 85,  'suggestions for'       => 80,
            'what tool'                 => 75,  'what software'         => 75,
            'what platform'             => 72,  'best solution for'     => 85,
            'looking to replace'        => 90,  'thinking of switching' => 85,
            'moving from'               => 82,  'currently evaluating'  => 88,
            'comparing options'         => 78,  'weighing up'           => 75,
            'considering'               => 70,  'any recommendations'   => 82,
            'does anyone have experience with' => 78,
            'anyone tried'              => 76,  'worth it'              => 75,
            'is it any good'            => 72,  'honest review'         => 68,
            'pros and cons'             => 65,  'real user'             => 62,
            'would you recommend'       => 80,  'would you suggest'     => 78,
            'looking for feedback'      => 70,  'thinking about buying' => 88,
            'about to purchase'         => 92,  'ready to buy'          => 95,
            'decision help'             => 85,  'help me decide'        => 82,
        ];

        $this->spamSignals = [
            'top companies'         => -60, 'best companies'        => -55,
            'leading companies'     => -55, 'top 10'                => -40,
            'top 5'                 => -35, 'in 2025'               => -25,
            'in 2026'               => -25, 'hire us'               => -70,
            'our services'          => -65, 'contact us'            => -50,
            'get a quote'           => -60, 'click here'            => -70,
            'limited offer'         => -70, 'discount code'         => -65,
            'promo code'            => -65, 'sponsored'             => -80,
            'advertisement'         => -80, 'press release'         => -75,
            'ai tool spotlight'     => -70, 'tool spotlight'        => -60,
            'company review'        => -50, 'outsourcing companies' => -60,
            'development company'   => -45, 'software company'      => -40,
            'for hire'              => -45, 'i am available'        => -50,
            'my services'           => -55, 'freelance'             => -30,
            'i built'               => -15, 'i made'                => -10,
            'i created'             => -10, 'i launched'            => -15,
            'show hn:'              => -5,  'check out my'          => -20,
            'just launched'         => -15, 'we are excited'        => -10,
            'announcing'            => -15, 'we are hiring'         => -25,
            'seo agency'            => -50, 'digital agency'        => -45,
            'growth hack'           => -35, 'viral'                 => -20,
            'guaranteed'            => -40, 'act now'               => -60,
            'don\'t miss out'       => -55, 'exclusive offer'       => -60,
            '1 on 1'                => -30, 'book a call'           => -55,
            'free consultation'     => -50, 'schedule a demo'       => -45,
            'sign up now'           => -60, 'start your free trial' => -55,
            'artificial intelligence platform' => -30,
            'revolutionary'         => -25, 'game changing'         => -25,
            'cutting edge'          => -20, 'disruptive'            => -20,
        ];

        $this->discussionSignals = [
            'thoughts?'             => 15,  'advice?'               => 15,
            'experience?'           => 12,  'opinions?'             => 12,
            'what do you think'     => 10,  'anyone else'           => 12,
            'just me or'            => 10,  'am i the only'         => 10,
            'update:'               => 8,   'solved:'               => 8,
            'here is how'           => 5,   'just wanted to share'  => 5,
            'thought i would share' => 5,   'has anyone'            => 10,
            'has anyone else'       => 12,  'does anyone else'      => 12,
            'discussion'            => 5,   'curious'               => 8,
            'wondering if'          => 10,  'question'              => 5,
        ];

        $this->urgencySignals = config('replyradar.urgency_signals', []);
        $this->budgetSignals = config('replyradar.budget_signals', []);
        $this->depthDetailIndicators = config('replyradar.depth_signals.detail_indicators', []);
    }

    public function score(string $title, string $content = ''): float
    {
        $text  = strtolower($title . ' ' . $content);
        $titleLower = strtolower($title);
        $score = 20.0;

        $intentBonus = $this->detectBuyerIntent($text);
        $spamPenalty = $this->detectSpam($text);
        $discussionBonus = $this->detectDiscussion($text);
        $urgencyBonus = $this->detectUrgency($text);
        $budgetBonus = $this->detectBudget($text);
        $depthBonus = $this->detectDepth($titleLower, $content);
        $questionBonus = $this->detectQuestionType($titleLower);
        $qualityPenalty = $this->detectQualityIssues($title, $content);

        $score += $intentBonus;
        $score += $spamPenalty;
        $score += $discussionBonus;
        $score += $urgencyBonus;
        $score += $budgetBonus;
        $score += $depthBonus;
        $score += $questionBonus;
        $score += $qualityPenalty;

        return round(max(0, min(100, $score)), 1);
    }

    public function scoreUrgency(string $title, string $content = ''): float
    {
        $text = strtolower($title . ' ' . $content);
        $score = 0.0;

        foreach ($this->urgencySignals as $phrase => $value) {
            if (str_contains($text, $phrase)) {
                $score = max($score, $value);
            }
        }

        if (preg_match('/\b(urgent|asap|immediately|emergency|critical|desperate)\b/i', $text)) {
            $score = max($score, 85);
        }

        if (preg_match('/\b(now|today|this week)\b/i', $text) && preg_match('/\b(need|help|fix|solve|resolve)\b/i', $text)) {
            $score = max($score, 70);
        }

        if (str_contains($text, '!!') || str_contains($text, '???')) {
            $score += 15;
        }

        return round(max(0, min(100, $score)), 1);
    }

    public function scoreDepth(string $title, string $content = ''): float
    {
        $contentLength = strlen($content);
        $minLen = config('replyradar.depth_signals.context_length_min', 200);
        $maxLen = config('replyradar.depth_signals.context_length_max', 2000);

        $score = 0.0;

        if ($contentLength > 0) {
            $score += min(30, ($contentLength / $maxLen) * 30);
        }

        if ($contentLength > $minLen) {
            $score += 15;
        }
        if ($contentLength > 1000) {
            $score += 10;
        }

        $text = strtolower($content);
        $detailScore = 0;
        $detailCount = 0;
        foreach ($this->depthDetailIndicators as $phrase => $value) {
            if (str_contains($text, $phrase)) {
                $detailScore += $value;
                $detailCount++;
            }
        }
        if ($detailCount > 0) {
            $score += min(30, $detailScore);
        }

        $wordCount = str_word_count($content);
        if ($wordCount > 0) {
            $sentences = max(1, preg_match_all('/[.!?]+/', $content));
            $avgSentenceLength = $wordCount / $sentences;
            if ($avgSentenceLength > 15 && $avgSentenceLength < 40) {
                $score += 10;
            }
        }

        $paragraphs = preg_split('/\n\s*\n/', trim($content));
        if (count($paragraphs) >= 3) {
            $score += 5;
        }
        if (count($paragraphs) >= 5) {
            $score += 5;
        }

        return round(max(0, min(100, $score)), 1);
    }

    private function detectBuyerIntent(string $text): float
    {
        $bestScore = 0;
        $matchCount = 0;

        foreach ($this->buyerIntent as $phrase => $value) {
            if (str_contains($text, $phrase)) {
                if ($value > $bestScore) {
                    $bestScore = $value;
                }
                $matchCount++;
            }
        }

        if ($matchCount === 0) return 0;

        $score = (float) $bestScore;
        if ($matchCount >= 3) {
            $score += 10;
        }
        if ($matchCount >= 5) {
            $score += 5;
        }

        return $score;
    }

    private function detectSpam(string $text): float
    {
        $penalty = 0;
        $matchCount = 0;

        foreach ($this->spamSignals as $phrase => $value) {
            if (str_contains($text, $phrase)) {
                $penalty += $value;
                $matchCount++;
            }
        }

        if ($matchCount >= 3) {
            $penalty *= 1.3;
        }

        return $penalty;
    }

    private function detectDiscussion(string $text): float
    {
        $bonus = 0;
        foreach ($this->discussionSignals as $phrase => $value) {
            if (str_contains($text, $phrase)) {
                $bonus += $value;
            }
        }
        return $bonus;
    }

    private function detectUrgency(string $text): float
    {
        $score = 0.0;
        foreach ($this->urgencySignals as $phrase => $value) {
            if (str_contains($text, $phrase)) {
                $score = max($score, $value);
            }
        }
        return $score;
    }

    private function detectBudget(string $text): float
    {
        $score = 0.0;
        foreach ($this->budgetSignals as $phrase => $value) {
            if (str_contains($text, $phrase)) {
                $score = max($score, $value);
            }
        }
        return $score;
    }

    private function detectDepth(string $titleLower, string $content): float
    {
        $contentLength = strlen($content);
        $score = 0.0;

        if ($contentLength > 0) {
            $score += min(10, $contentLength / 200);
        }
        if ($contentLength > 500) {
            $score += 5;
        }

        $detailCount = 0;
        foreach ($this->depthDetailIndicators as $phrase => $value) {
            if (str_contains($content, $phrase)) {
                $score += $value * 0.5;
                $detailCount++;
            }
        }

        $wordCount = str_word_count($content);
        if ($wordCount > 50) {
            $score += 5;
        }
        if ($wordCount > 100) {
            $score += 3;
        }

        return min(30, $score);
    }

    private function detectQuestionType(string $titleLower): float
    {
        $score = 0.0;

        if (str_ends_with(trim($titleLower), '?')) {
            $score += 10;
        }

        if (preg_match('/^(how|what|where|when|why|which|who|is|are|can|could|would|should|do|does|did|has|have)/i', trim($titleLower))) {
            $score += 8;
        }

        if (str_contains($titleLower, '?')) {
            $score += 5;
        }

        return $score;
    }

    private function detectQualityIssues(string $title, string $content): float
    {
        $penalty = 0.0;

        if ($title === strtoupper($title) && strlen($title) > 10) {
            $penalty -= 20;
        }

        if (str_word_count($title) < 3) {
            $penalty -= 15;
        }

        $contentLength = strlen($content);
        if ($contentLength > 0 && $contentLength < 50) {
            $penalty -= 5;
        }

        if (preg_match('/[A-Z]{5,}/', $title)) {
            $penalty -= 10;
        }

        if (preg_match('/(.)\1{5,}/', $title)) {
            $penalty -= 15;
        }

        return $penalty;
    }
}
