<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReactionController extends Controller
{
    public function store(Request $request, Project $project)
    {
        abort_unless($project->status === 'published', 404);
        $request->validate(['type' => 'sometimes|in:like']);
        // Query builder upsert keeps concurrent retries idempotent; identity is server-owned.
        $project->reactions()->upsert([['project_id' => $project->id, 'user_id' => $request->user()->id, 'type' => 'like', 'created_at' => now(), 'updated_at' => now()]], ['project_id', 'user_id', 'type'], ['updated_at']);

        return response()->json(['data' => $project->reactions()->where('user_id', $request->user()->id)->firstOrFail()]);
    }

    public function destroy(Request $request, Project $project)
    {
        Gate::authorize('view', $project);
        $project->reactions()->where('user_id', $request->user()->id)->delete();

        return response()->noContent();
    }
}
