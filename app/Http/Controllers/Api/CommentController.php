<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    public function index(Project $project)
    {
        Gate::authorize('view', $project);

        return $project->comments()->with('user:id,name')->orderBy('id')->paginate(20);
    }

    public function store(Request $request, Project $project)
    {
        abort_unless($project->status === 'published', 404);
        $data = $request->validate(['body' => 'required|string|max:5000', 'parent_id' => ['nullable', 'integer', Rule::exists('comments', 'id')->where('project_id', $project->id)->whereNull('parent_id')]]);
        $comment = new Comment($data);
        $comment->user()->associate($request->user());
        $project->comments()->save($comment);

        return response()->json(['data' => $comment->load('user:id,name')], 201);
    }

    public function update(Request $request, Comment $comment)
    {
        Gate::authorize('view', $comment->project);
        Gate::authorize('update', $comment);
        $comment->update($request->validate(['body' => 'required|string|max:5000']));

        return response()->json(['data' => $comment]);
    }

    public function destroy(Comment $comment)
    {
        Gate::authorize('view', $comment->project);
        Gate::authorize('delete', $comment);
        $comment->delete();

        return response()->noContent();
    }
}
