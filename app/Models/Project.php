<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Project extends Model
{
    protected $fillable = ['user_id', 'name', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(Keyword::class);
    }

    public function posts(): HasManyThrough
    {
        return $this->hasManyThrough(Post::class, Keyword::class);
    }

    // Oportunidades rankeadas por final_score
    public function topOpportunities(int $limit = 50)
    {
        return $this->posts()
            ->orderByDesc('final_score')
            ->limit($limit)
            ->get();
    }
}
