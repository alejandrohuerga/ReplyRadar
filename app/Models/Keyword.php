<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    protected $fillable = ['project_id', 'term', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
