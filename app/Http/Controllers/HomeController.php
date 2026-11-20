<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Services\PlaceholderProjects;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        ]);

        $search = trim($filters['search'] ?? '');
        $tab = $filters['tab'] ?? 'explore';

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
            : $query->orderByDesc('id')
                ->paginate(12)
                ->withQueryString();

        // Obtener categorías activas.
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('home', compact(
            'projects',
            'search',
            'tab',
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
     * Actualizar la información del perfil.
     */
    public function updateProfile(Request $request)
    {
        $data = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        $user->name = $data['name'];
        $user->bio = $data['bio'] ?? null;

        $user->save();

        return redirect()
            ->route('profile.profile')
            ->with(
                'profile_status',
                'Tu perfil se actualizó correctamente.'
            );
    }
}
