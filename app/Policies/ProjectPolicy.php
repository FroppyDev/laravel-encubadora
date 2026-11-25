<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(?User $user, Project $project): bool
    {
        return $project->status === 'published' || ($user && ($user->id === $project->user_id || $user->isModerator()));
    }

    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    public function archive(User $user, Project $project): bool
    {
        return $this->update($user, $project) || $user->isModerator();
    }
}
