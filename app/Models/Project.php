<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'summary', 'description', 'category_id', 'problem_statement', 'proposed_solution', 'repository_url', 'demo_url', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function technologies()
    {
        return $this->belongsToMany(Technology::class, 'project_technologies');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(ProjectReaction::class);
    }

    public function collaborationRequests()
    {
        return $this->hasMany(CollaborationRequest::class);
    }

    public function updates()
    {
        return $this->hasMany(ProjectUpdate::class);
    }
}
