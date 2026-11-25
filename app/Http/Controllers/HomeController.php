<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Services\PlaceholderProjects;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\ProfileImageService;


class HomeController extends Controller
{
    /**
     * Mostrar la página principal.
     */

    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'tab' => ['nullable', Rule::in(['explore', 'popular', 'recent'])],
            'category' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')
                    ->where('is_active', true),
            ],
        ]);

        $search = trim($filters['search'] ?? '');
        $tab = $filters['tab'] ?? 'explore';
        $categoryId = $filters['category'] ?? null;

        $query = Project::with(['user:id,name', 'category'])
            ->withCount('reactions')
            ->where('status', 'published');

        // Aplicar búsqueda.
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $term = '%' . mb_strtolower($search) . '%';

                $query->whereRaw('LOWER(title) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(summary) LIKE ?', [$term]);
            });
        }

        // Filtrar por categoría.
        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
        }

        // Aplicar ordenamiento.
        switch ($tab) {
            case 'popular':
                $query->orderByDesc('reactions_count')
                    ->latest('created_at');
                break;

            case 'recent':
                $query->latest('created_at');
                break;

            default:
                $query->orderBy('title');
                break;
        }

        // Comprobar si existen proyectos publicados.
        $showPlaceholders = !Project::where('status', 'published')->exists();

        // Obtener proyectos reales o proyectos de ejemplo.
        $projects = $showPlaceholders
            ? app(PlaceholderProjects::class)->paginate($search, $tab)
            : $query->paginate(12)->withQueryString();

        // Obtener categorías activas.
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('home', compact(
            'projects',
            'search',
            'tab',
            'categoryId',
            'showPlaceholders',
            'categories'
        ));
    }


    /**
     * Mostrar la página de edición del perfil.
     */
    public function profile()
    {
        return view('profile.profile');
    }

    /**
     * Actualizar la información y las imágenes del perfil.
     */
    public function updateProfile(
        Request $request,
        ProfileImageService $imageService
    ) {
        // Validar todos los campos en un solo formulario.
        $data = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:2000'],

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

        $user = $request->user();

        // Actualizar información personal.
        $user->name = $data['name'];
        $user->bio = $data['bio'] ?? null;

        $user->save();

        // Preparar únicamente las imágenes que se hayan seleccionado.
        $images = array_intersect_key(
            $data,
            array_flip(['avatar', 'cover'])
        );

        // Actualizar imágenes si se seleccionó alguna.
        if (!empty($images)) {
            $imageService->update($user, $images);
        }

        return redirect()
            ->route('profile.profile')
            ->with(
                'profile_status',
                'Tu perfil se actualizó correctamente.'
            );
    }

}
