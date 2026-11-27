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
        if (
            $project->status !== 'published' &&
            $project->user_id !== auth()->id()
        ) {
            abort(404);
        }

        $project->load([
            'user',
            'category',
            'technologies',

            'comments' => function ($query) {
                $query->whereNull('parent_id')
                    ->latest();
            },

            'comments.user',
            'comments.replies.user',

            'updates' => function ($query) {
                $query->latest();
            },

            'updates.user',
        ]);

        $project->loadCount('reactions');

        // Obtener la reacción del usuario actual.
        $myReaction = $project->reactions()
            ->where('user_id', auth()->id())
            ->where('type', 'like')
            ->first();

        // Solicitud del usuario actual.
        $myCollaborationRequest = $project->collaborationRequests()
            ->where('user_id', auth()->id())
            ->first();

        // Solicitudes recibidas: solo para el propietario.
        $collaborationRequests = collect();

        if ($project->user_id === auth()->id()) {
            $collaborationRequests = $project->collaborationRequests()
                ->with('user')
                ->latest()
                ->get();
        }

        return view('projects.show', compact(
            'project',
            'myReaction',
            'myCollaborationRequest',
            'collaborationRequests'
        ));
    }

    public function create(?string $slug = null)
    {

        $project = null;

        if ($slug !== null) {
            $project = Project::where('slug', $slug)
                ->where('user_id', auth()->id())
                ->where('status', 'draft')
                ->firstOrFail();
        }

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $technologies = Technology::where('slug', '!=', '')
            ->orderBy('name')
            ->get(['id', 'name']);

        

        return view('projects.create', compact(
            'categories',
            'technologies',
            'project'
        ));
    }



    
    public function store(Request $request, MediaService $mediaService)
    {
        $validated = $request->validate([
            // ID opcional: si viene, es un proyecto existente
            'project_id' => ['nullable', 'integer'],

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
        $isNewProject = empty($validated['project_id']);

        try {

            /*
            |--------------------------------------------------------------------------
            | 1. Buscar o crear el proyecto
            |--------------------------------------------------------------------------
            */

            if (!$isNewProject) {

                // Buscar el proyecto y asegurarse de que pertenezca al usuario.
                $project = Project::where('id', $validated['project_id'])
                    ->where('user_id', $request->user()->id)
                    ->firstOrFail();

            }

            /*
            |--------------------------------------------------------------------------
            | 2. Guardar los datos del proyecto
            |--------------------------------------------------------------------------
            */

            $project = DB::transaction(function () use (
                $request,
                $validated,
                $project,
                $isNewProject
            ) {

                $data = [
                    'category_id' => $validated['category'],
                    'title' => $validated['title'],
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
                ];

                if ($isNewProject) {

                    // Solamente se genera un slug al crear un proyecto nuevo.
                    $data['user_id'] = $request->user()->id;

                    $data['slug'] = Str::slug($validated['title'])
                        . '-' . Str::lower(Str::ulid());

                    $project = Project::create($data);

                } else {

                    // Actualizar el proyecto existente, conservando su ID y slug.
                    $project->update($data);

                }

                // Sincronizar tecnologías sin duplicarlas.
                $project->technologies()->sync(
                    $validated['technologies'] ?? []
                );

                return $project;
            });

            /*
            |--------------------------------------------------------------------------
            | 3. Procesar portada si el usuario subió una nueva
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('cover')) {

                $media = $mediaService->uploadProjectCover(
                    $request->file('cover'),
                    $request->user(),
                    $project
                );

                $project->update([
                    'cover_media_id' => (string) $media->getKey(),
                ]);
            }

        } catch (Throwable $exception) {

            /*
            |--------------------------------------------------------------------------
            | 4. Limpiar la nueva imagen si ocurrió un error
            |--------------------------------------------------------------------------
            */

            if ($media) {
                try {
                    $mediaService->delete($media);
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 5. Eliminar el proyecto únicamente si era nuevo
            |--------------------------------------------------------------------------
            |
            | Si estábamos editando un proyecto existente, NO lo eliminamos
            | cuando ocurre un error.
            |
            */

            if ($project && $isNewProject) {
                try {
                    $project->delete();
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            throw $exception;
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Mensaje según la operación y el estado
        |--------------------------------------------------------------------------
        */

        if ($isNewProject) {

            $message = $project->status === 'published'
                ? '¡Tu proyecto se publicó correctamente!'
                : 'Tu proyecto se guardó como borrador.';

        } else {

            $message = $project->status === 'published'
                ? '¡Tu proyecto se actualizó y publicó correctamente!'
                : 'Tu proyecto se actualizó y guardó como borrador.';

        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', $message);
    }

    public function delete(Project $project, MediaService $mediaService)
    {
        // Verificar que el usuario sea el propietario del proyecto.
        if (auth()->id() !== $project->user_id) {
            abort(403, 'No tienes permiso para eliminar este proyecto.');
        }

        // Obtener la portada de MongoDB antes de eliminar el proyecto.
        $media = null;

        if ($project->cover_media_id) {
            $media = \App\Models\Media::find($project->cover_media_id);
        }

        // Guardar información de la portada antigua de SQL, si existiera.
        $oldCoverPath = $project->cover_image_path;

        // Eliminar el proyecto de SQL.
        $project->delete();

        // Eliminar la portada de MongoDB y el archivo físico.
        if ($media) {
            try {
                $mediaService->delete($media);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        // Compatibilidad con proyectos que usaban la portada antigua.
        if ($oldCoverPath) {
            try {
                \Illuminate\Support\Facades\Storage::disk('public')
                    ->delete($oldCoverPath);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return redirect()
            ->route('home')
            ->with('success', 'Tu proyecto se eliminó correctamente.');
    }

    public function myProjects(Request $request)
    {
        $user = $request->user();
        $projects = Project::where('user_id', $user->id)
            ->with('category')
            ->orderByDesc('created_at')
            ->get();

        return view('projects.myProjects', compact('projects'));
    }

}