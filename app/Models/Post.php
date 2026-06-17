<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'keyword_id', 'external_id', 'title', 'content',
        'subreddit', 'url', 'author', 'author_karma',
        'reddit_score', 'num_comments', 'intent_score',
        'match_score', 'engagement_score', 'urgency_score',
        'depth_score', 'freshness_score', 'competition_score',
        'op_engaged', 'final_score', 'content_hash', 'posted_at',
    ];

    protected $casts = [
        'posted_at'   => 'datetime',
        'op_engaged'  => 'boolean',
    ];

    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }

    public function getScoreLabelAttribute(): string
    {
        return match (true) {
            $this->final_score >= 80 => 'hot',
            $this->final_score >= 60 => 'warm',
            $this->final_score >= 40 => 'cool',
            default                  => 'cold',
        };
    }
}
