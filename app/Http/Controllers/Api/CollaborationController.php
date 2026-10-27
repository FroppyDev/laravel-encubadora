<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CollaborationRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CollaborationController extends Controller
{
    public function index(Request $request, Project $project)
    {
        Gate::authorize('view', $project);

        return $project->collaborationRequests()->with('user:id,name')
            ->when($request->user()->id !== $project->user_id, fn ($q) => $q->where('user_id', $request->user()->id))->latest('id')->paginate(20);
    }

    public function store(Request $request, Project $project)
    {
        abort_unless($project->status === 'published', 404);
        abort_if($project->user_id === $request->user()->id, 422, 'No puedes solicitar colaborar en tu propio proyecto.');
        $data = $request->validate(['message' => 'nullable|string|max:2000']);
        $entry = DB::transaction(function () use ($request, $project, $data) {
            // Serialize requests for this project, including the first insert.
            Project::whereKey($project->id)->lockForUpdate()->firstOrFail();
            $entry = $project->collaborationRequests()->where('user_id', $request->user()->id)->first();
            abort_if($entry && in_array($entry->status, ['pending', 'accepted']), 409, 'Ya existe una solicitud activa.');
            $entry ??= new CollaborationRequest;
            $entry->project()->associate($project);
            $entry->user()->associate($request->user());
            $entry->message = $data['message'] ?? null;
            $entry->status = 'pending';
            $entry->save();

            return $entry;
        });

        return response()->json(['data' => $entry], 201);
    }

    public function update(Request $request, CollaborationRequest $collaborationRequest)
    {
        $data = $request->validate(['status' => 'required|in:accepted,rejected,cancelled']);
        $entry = DB::transaction(function () use ($request, $collaborationRequest, $data) {
            $entry = CollaborationRequest::whereKey($collaborationRequest->id)->lockForUpdate()->firstOrFail();
            if ($data['status'] === 'cancelled') {
                abort_unless($entry->user_id === $request->user()->id, 403);
            } else {
                abort_unless($entry->project->user_id === $request->user()->id, 403);
            }
            abort_unless($entry->status === 'pending', 409, 'Solo se pueden resolver solicitudes pendientes.');
            $entry->status = $data['status'];
            $entry->save();

            return $entry;
        });

        return response()->json(['data' => $entry]);
    }
}
