<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate(['search' => 'sometimes|string|max:100', 'category_id' => 'sometimes|integer', 'technology_id' => 'sometimes|integer']);

        return $this->query()->where('status', 'published')
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where(fn ($q) => $q->whereRaw('LOWER(title) LIKE ?', ['%'.strtolower($s).'%'])->orWhereRaw('LOWER(summary) LIKE ?', ['%'.strtolower($s).'%'])))
            ->when($filters['category_id'] ?? null, fn ($q, $id) => $q->where('category_id', $id))
            ->when($filters['technology_id'] ?? null, fn ($q, $id) => $q->whereHas('technologies', fn ($q) => $q->where('technologies.id', $id)))
            ->latest('id')->paginate(20)->withQueryString();
    }

    public function mine(Request $request)
    {
        return $this->query()->where('user_id', $request->user()->id)->latest('id')->paginate(20);
    }

    public function show(Project $project)
    {
        Gate::authorize('view', $project);

        return response()->json(['data' => $this->query()->findOrFail($project->id)]);
    }

    public function store(ProjectRequest $request)
    {
        $project = DB::transaction(function () use ($request) {
            $project = $request->user()->projects()->create($request->safe()->except('technology_ids') + ['slug' => Str::slug($request->title).'-'.Str::lower(Str::ulid())]);
            $project->technologies()->sync($request->validated('technology_ids', []));

            return $project;
        });

        return response()->json(['data' => $project->refresh()->load('category', 'technologies')], 201);
    }

    public function update(ProjectRequest $request, Project $project)
    {
        Gate::authorize('update', $project);
        DB::transaction(function () use ($request, $project) {
            $project->update($request->safe()->except('technology_ids'));
            if ($request->has('technology_ids')) {
                $project->technologies()->sync($request->validated('technology_ids'));
            }
        });

        return response()->json(['data' => $project->load('category', 'technologies')]);
    }

    public function archive(Project $project)
    {
        Gate::authorize('archive', $project);
        $project->update(['status' => 'archived']);

        return response()->noContent();
    }

    private function query()
    {
        return Project::with(['user:id,name', 'category', 'technologies'])->withCount(['comments', 'reactions']);
    }
}
