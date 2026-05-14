<?php
namespace App\Services;

class FinalScoreService
{
    private array $weights;

    public function __construct()
    {
        $this->weights = config('replyradar.scoring');
    }

    public function calculate(
        float $intentScore,
        float $matchScore,
        int   $redditScore,
        int   $numComments
    ): float {
        $engagementScore = $this->calculateEngagement($redditScore, $numComments);
        $qualityScore    = $this->calculateQuality($redditScore, $numComments);

        $final =
            ($intentScore    * $this->weights['intent_weight'])    +
            ($matchScore     * $this->weights['match_weight'])     +
            ($engagementScore * $this->weights['engagement_weight']) +
            ($qualityScore   * $this->weights['quality_weight']);

        return round(min(100, max(0, $final)), 2);
    }

    private function calculateEngagement(int $score, int $comments): float
    {
        if ($score <= 0) return 0;

        // Log scaling para evitar que posts virales dominen todo
        $logScore    = log10(max(1, $score)) * 25;
        $logComments = log10(max(1, $comments)) * 15;

        return min(100, $logScore + $logComments);
    }

    private function calculateQuality(int $score, int $comments): float
    {
        // Ratio comentarios/upvotes indica discusión real
        if ($score <= 0) return 50;
        $ratio = $comments / max(1, $score);
        return min(100, $ratio * 100);
    }
}
