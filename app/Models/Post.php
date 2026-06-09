<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'keyword_id', 'external_id', 'title', 'content',
        'title_en', 'content_en', 'title_es', 'content_es',
        'subreddit', 'url', 'author', 'author_karma',
        'reddit_score', 'num_comments', 'intent_score',
        'match_score', 'engagement_score', 'urgency_score',
        'depth_score', 'freshness_score', 'competition_score',
        'op_engaged', 'final_score', 'content_hash', 'posted_at',
    ];

    public function getLocalizedTitleAttribute(): string
    {
        $locale = app()->getLocale();
        $field = "title_{$locale}";
        $translated = $this->$field;
        if ($translated !== null) {
            return $translated;
        }
        $other = $locale === 'es' ? 'en' : 'es';
        return $this->{"title_{$other}"} ?? $this->title;
    }

    public function getLocalizedContentAttribute(): string
    {
        $locale = app()->getLocale();
        $field = "content_{$locale}";
        $translated = $this->$field;
        if ($translated !== null) {
            return $translated;
        }
        $other = $locale === 'es' ? 'en' : 'es';
        return $this->{"content_{$other}"} ?? $this->content ?? '';
    }

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
            $this->final_score >= 60 => 'hot',
            $this->final_score >= 40 => 'warm',
            $this->final_score >= 20 => 'cool',
            default                  => 'cold',
        };
    }
}
