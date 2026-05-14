<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'keyword_id', 'external_id', 'title', 'content',
        'subreddit', 'url', 'author', 'reddit_score',
        'num_comments', 'intent_score', 'match_score',
        'engagement_score', 'final_score', 'content_hash', 'posted_at',
    ];

    protected $casts = ['posted_at' => 'datetime'];

    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }

    public function getScoreLabelAttribute(): string
    {
        return match(true) {
            $this->final_score >= 80 => 'hot',
            $this->final_score >= 60 => 'warm',
            $this->final_score >= 40 => 'cool',
            default => 'cold',
        };
    }
}
