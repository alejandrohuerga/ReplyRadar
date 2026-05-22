<?php
namespace App\Services;

use Carbon\Carbon;

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
        int   $numComments,
        float $urgencyScore = 0,
        float $depthScore = 0,
        ?Carbon $postedAt = null,
        float $competitionScore = 0,
        bool  $opEngaged = false,
    ): float {
        $coherencePenalty  = $this->coherencePenalty($intentScore, $matchScore);
        $engagementScore  = $this->calculateEngagement($redditScore, $numComments);
        $freshnessScore   = $this->calculateFreshness($postedAt);
        $urgencyDepthAvg  = ($urgencyScore + $depthScore) / 2;

        $competitionBonus = $this->competitionAdjustment($competitionScore, $opEngaged);

        $weighted =
            ($intentScore        * $this->weights['intent_weight'])        +
            ($matchScore         * $this->weights['match_weight'])         +
            ($engagementScore    * $this->weights['engagement_weight'])     +
            ($freshnessScore     * $this->weights['freshness_weight'])      +
            ($urgencyDepthAvg    * $this->weights['urgency_depth_weight'])  +
            ($competitionBonus   * $this->weights['competition_penalty']);

        $final = $weighted * $coherencePenalty;

        return round(min(100, max(0, $final)), 2);
    }

    private function coherencePenalty(float $intent, float $match): float
    {
        if ($match < 15)  return 0.25;
        if ($match < 25)  return 0.40;
        if ($match < 40)  return 0.60;
        if ($match < 55)  return 0.80;

        if ($intent < 15) return 0.35;
        if ($intent < 25) return 0.55;
        if ($intent < 40) return 0.75;

        return 1.0;
    }

    private function calculateEngagement(int $score, int $comments): float
    {
        if ($score <= 0 && $comments <= 0) return 0;

        $logScore    = $score > 0    ? log10($score)    * 18 : 0;
        $logComments = $comments > 0 ? log10($comments) * 12 : 0;

        return min(100, $logScore + $logComments);
    }

    private function calculateFreshness(?Carbon $postedAt): float
    {
        if ($postedAt === null) return 50;

        $hoursAgo = $postedAt->diffInRealHours(now());
        $decayCfg = config('replyradar.time_decay');

        $decayHours = $decayCfg['decay_hours'] ?? 48;
        $maxDecay   = $decayCfg['max_decay'] ?? 0.20;
        $boostHours = $decayCfg['boost_hours'] ?? 6;

        if ($hoursAgo <= $boostHours) {
            $boost = 1 - ($hoursAgo / $boostHours) * 0.3;
            return round(min(100, 100 * $boost), 1);
        }

        $decay = max($maxDecay, 1 - ($hoursAgo / $decayHours));
        return round(min(100, 100 * $decay), 1);
    }

    private function competitionAdjustment(float $competitionScore, bool $opEngaged): float
    {
        $base = 50.0;

        if ($competitionScore <= 0) {
            $base = 80.0;
        } elseif ($competitionScore < 30) {
            $base = 70.0;
        } elseif ($competitionScore < 60) {
            $base = 50.0;
        } else {
            $base = 25.0;
        }

        if ($opEngaged) {
            $base += 15;
        }

        return $base;
    }
}
