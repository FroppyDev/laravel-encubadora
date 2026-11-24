<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'media';

    protected $fillable = [
        'uploaded_by',
        'user_id',
        'project_ids',
        'type',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'alt',
    ];

    protected $casts = [
        'uploaded_by' => 'integer',
        'user_id' => 'integer',
        'project_ids' => 'array',
        'size' => 'integer',
    ];

    public function getUrlAttribute(): string
    {
        return \Illuminate\Support\Facades\Storage::disk(
            $this->disk
        )->url($this->path);
    }
}
