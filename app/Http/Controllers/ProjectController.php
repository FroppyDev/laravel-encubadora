<?php

namespace App\Http\Controllers;

use App\Models\Project;

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
}