<?php

namespace App\Services;

use App\Models\Media;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class MediaService
{
    public function uploadProjectCover(
        UploadedFile $file,
        User $user,
        Project $project
    ): Media {
        $disk = 'public';

        $filename = Str::uuid() . '.' . $file->extension();

        $path = $file->storeAs(
            'media/projects',
            $filename,
            $disk
        );

        try {
            return Media::create([
                'uploaded_by' => $user->id,
                'user_id' => $user->id,
                'project_ids' => [$project->id],
                'type' => 'project_cover',
                'disk' => $disk,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'alt' => 'Portada de ' . $project->title,
            ]);
        } catch (Throwable $exception) {
            Storage::disk($disk)->delete($path);

            throw $exception;
        }
    }

    public function delete(Media $media): void
    {
        Storage::disk($media->disk)->delete($media->path);

        $media->delete();
    }

    public function getUrl(Media $media): string
    {
        return Storage::disk($media->disk)->url($media->path);
    }
}
