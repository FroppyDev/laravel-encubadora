<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Media;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'summary',
        'description',
        'problem_statement',
        'proposed_solution',
        'cover_image_path',
        'cover_media_id',
        'repository_url',
        'demo_url',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(
            Technology::class,
            'project_technologies'
        );
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(ProjectReaction::class);
    }

    public function collaborationRequests(): HasMany
    {
        return $this->hasMany(CollaborationRequest::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(ProjectUpdate::class);
    }

    public function getCoverMediaAttribute(): ?\App\Models\Media
    {
        if (!$this->cover_media_id) {
            return null;
        }

        return Media::find($this->cover_media_id);
    }
}
