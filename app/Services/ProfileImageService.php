<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ProfileImageService
{
    public function update(User $user, array $images): User
    {
        $fields = [
            'avatar' => ['avatar_path', 'users/avatars'],
            'cover' => ['cover_image_path', 'users/covers'],
        ];

        $newPaths = [];
        $oldPaths = [];

        try {
            foreach ($fields as $input => [$column, $directory]) {
                if (!isset($images[$input])) {
                    continue;
                }

                /** @var UploadedFile $file */
                $file = $images[$input];

                $path = $file->store($directory, 'public');

                if ($path === false) {
                    throw new RuntimeException('No se pudo guardar la imagen.');
                }

                $newPaths[] = $path;

                $previousPath = $user->{$column};

                if (
                    $previousPath &&
                    str_starts_with($previousPath, $directory . '/')
                ) {
                    $oldPaths[] = $previousPath;
                }

                $user->{$column} = $path;
            }

            $user->saveOrFail();

        } catch (\Throwable $exception) {

            try {
                Storage::disk('public')->delete($newPaths);
            } catch (\Throwable $cleanupException) {
                report($cleanupException);
            }

            throw $exception;
        }

        try {
            Storage::disk('public')->delete($oldPaths);
        } catch (\Throwable $cleanupException) {
            report($cleanupException);
        }

        return $user->fresh();
    }
}
