<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required_without:comment_id|prohibits:comment_id|nullable|integer|exists:projects,id',
            'comment_id' => 'required_without:project_id|prohibits:project_id|nullable|integer|exists:comments,id',
            'reason' => 'required|string|max:255', 'details' => 'nullable|string|max:5000',
        ]);
        $project = ! empty($data['project_id']) ? Project::findOrFail($data['project_id']) : Comment::findOrFail($data['comment_id'])->project;
        Gate::authorize('view', $project);
        $target = ! empty($data['project_id']) ? ['project_id' => $data['project_id']] : ['comment_id' => $data['comment_id']];
        // createOrFirst recovers unique-key races inside a savepoint.
        $report = $request->user()->reports()->createOrFirst($target, $data);
        abort_unless($report->wasRecentlyCreated, 409, 'Ya reportaste este contenido.');

        return response()->json(['data' => $report->refresh()], 201);
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->isModerator(), 403);

        return Report::latest('id')->paginate(20);
    }

    public function update(Request $request, Report $report)
    {
        abort_unless($request->user()->isModerator(), 403);
        $data = $request->validate(['status' => 'required|in:reviewed,dismissed']);
        $report->status = $data['status'];
        $report->reviewed_by = $request->user()->id;
        $report->reviewed_at = now();
        $report->save();

        return response()->json(['data' => $report]);
    }
}
