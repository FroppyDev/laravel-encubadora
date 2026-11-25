<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UpdateController extends Controller
{
    public function index(Project $project)
    {
        Gate::authorize('view', $project);

        return $project->updates()->with('user:id,name')->latest('id')->paginate(20);
    }

    public function store(Request $request, Project $project)
    {
        Gate::authorize('update', $project);
        $entry = new ProjectUpdate($request->validate(['title' => 'required|string|max:180', 'body' => 'required|string|max:10000']));
        $entry->user()->associate($request->user());
        $project->updates()->save($entry);

        return response()->json(['data' => $entry], 201);
    }
}
