<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Models\Technology;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;


class ProjectController extends Controller
{
    public function show(Project $project)
    {
        $project->load([
            'user',
            'category',
            'technologies',
            'comments.user',
            'updates.user',
        ]);
        
        return view('projects.show', compact('project'));
    }

    public function create()
    {

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $technologies = Technology::where('slug', '!=', '')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('projects.create', compact('categories', 'technologies'));
    }



    
    public function store(Request $request, MediaService $mediaService)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'summary' => ['required', 'string', 'max:500'],

            'category' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')
                    ->where('is_active', true),
            ],

            'description' => ['required', 'string'],
            'problem_statement' => ['nullable', 'string'],
            'proposed_solution' => ['nullable', 'string'],

            'repository_url' => ['nullable', 'url', 'max:2048'],
            'demo_url' => ['nullable', 'url', 'max:2048'],

            'technologies' => ['nullable', 'array'],
            'technologies.*' => [
                'integer',
                'distinct',
                'exists:technologies,id',
            ],

            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'action' => [
                'required',
                Rule::in(['draft', 'published']),
            ],
        ]);

        $project = null;
        $media = null;

        try {
            // 1. Crear el proyecto y asociar sus tecnologías.
            $project = DB::transaction(function () use (
                $request,
                $validated
            ) {
                $slug = Str::slug($validated['title'])
                    . '-' . Str::lower(Str::ulid());

                $project = Project::create([
                    'user_id' => $request->user()->id,
                    'category_id' => $validated['category'],
                    'title' => $validated['title'],
                    'slug' => $slug,
                    'summary' => $validated['summary'],
                    'description' => $validated['description'],

                    'problem_statement' =>
                        $validated['problem_statement'] ?? null,

                    'proposed_solution' =>
                        $validated['proposed_solution'] ?? null,

                    'repository_url' =>
                        $validated['repository_url'] ?? null,

                    'demo_url' =>
                        $validated['demo_url'] ?? null,

                    'status' => $validated['action'],
                ]);

                $project->technologies()->sync(
                    $validated['technologies'] ?? []
                );

                return $project;
            });

            // 2. Subir la portada y registrar sus metadatos en MongoDB.
            if ($request->hasFile('cover')) {
                $media = $mediaService->uploadProjectCover(
                    $request->file('cover'),
                    $request->user(),
                    $project
                );

                // 3. Guardar la referencia de MongoDB en SQL.
                $project->update([
                    'cover_media_id' => (string) $media->getKey(),
                ]);
            }

        } catch (Throwable $exception) {

            // Limpiar la imagen si ya se había registrado en MongoDB.
            if ($media) {
                try {
                    $mediaService->delete($media);
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            // Eliminar el proyecto recién creado si falló la operación.
            if ($project) {
                try {
                    $project->delete();
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            throw $exception;
        }

        $message = $project->status === 'published'
            ? '¡Tu proyecto se publicó correctamente!'
            : 'Tu proyecto se guardó como borrador.';

        return redirect()
            ->route('projects.show', $project)
            ->with('success', $message);
    }

}