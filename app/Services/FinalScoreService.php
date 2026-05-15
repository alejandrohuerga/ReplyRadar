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
        // Si alguno de los dos scores principales es muy bajo,
        // el post no es relevante aunque el otro sea alto
        $coherencePenalty = $this->coherencePenalty($intentScore, $matchScore);

        $engagementScore = $this->calculateEngagement($redditScore, $numComments);

        $raw =
            ($intentScore    * $this->weights['intent_weight'])     +
            ($matchScore     * $this->weights['match_weight'])      +
            ($engagementScore * $this->weights['engagement_weight']) ;

        // Aplicar penalización de coherencia
        $final = $raw * $coherencePenalty;

        return round(min(100, max(0, $final)), 2);
    }

    // Si intent < 40 o match < 30, el score se recorta progresivamente
    private function coherencePenalty(float $intent, float $match): float
    {
        // Post con match muy bajo = irrelevante aunque hable de algo urgente
        if ($match < 20)  return 0.30;
        if ($match < 35)  return 0.55;
        if ($match < 50)  return 0.75;

        // Post con intent muy bajo = ruido aunque sea sobre el tema
        if ($intent < 20) return 0.40;
        if ($intent < 35) return 0.65;

        // Ambos moderados — sin penalización
        return 1.0;
    }

    private function calculateEngagement(int $score, int $comments): float
    {
        if ($score <= 0 && $comments <= 0) return 0;

        // Log scaling — posts con 1000 upvotes no deben dominar sobre posts con 50
        $logScore    = $score > 0    ? log10($score)    * 20 : 0;
        $logComments = $comments > 0 ? log10($comments) * 15 : 0;

        return min(100, $logScore + $logComments);
    }
}