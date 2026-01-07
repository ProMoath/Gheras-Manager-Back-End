<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user,Project $project): bool
    {
        return $user->isAdmin($user) || $user->isSupervisor($user)
            || $user->id === $project->created_by
            || $user->id === $project->assignee_id
            || $user->teams()->where('team_id', $project->team_id)->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->isAdmin($user) || $user->isSupervisor($user)
            || $user->id === $project->created_by
            || $user->id === $project->assignee_id
            || $user->teams()->where('team_id', $project->team_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin($user) || $user->isSupervisor($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->isAdmin($user) || $user->isSupervisor($user)
            || $user->id === $project->created_by
            || $user->id === $project->assignee_id
            || $user->teams()->where('team_id', $project->team_id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->isAdmin($user) || $user->isSupervisor($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return $user->isAdmin($user) || $user->isSupervisor($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        //
    }
}
