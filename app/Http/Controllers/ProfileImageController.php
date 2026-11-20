<?php

namespace App\Http\Controllers;

use App\Services\ProfileImageService;
use Illuminate\Http\Request;

class ProfileImageController extends Controller
{
    public function update(
        Request $request,
        ProfileImageService $imageService
    ) {
        $validated = $request->validateWithBag('images', [
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:max_width=2000,max_height=2000',
            ],
            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:max_width=4000,max_height=3000',
            ],
        ]);

        if (empty($validated)) {
            return back()->withErrors([
                'images' => 'Selecciona al menos una imagen.',
            ], 'images');
        }

        $imageService->update($request->user(), $validated);

        return redirect()
            ->route('profile.profile')
            ->with('images_status', 'Tus imágenes se actualizaron correctamente.');
    }
}
