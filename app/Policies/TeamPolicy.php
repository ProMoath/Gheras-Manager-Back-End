<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isSupervisor();

    }

    /**
     * Determine whether the user can view the model.
     */
        public function view(User $user, Team $team): bool
        {
            return $user->isAdmin() || $user->isSupervisor() || $user->teams()->where('teams.id', $team->id)->exists();
        }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSupervisor();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        return $user->isAdmin() || $user->isSupervisor();

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        return $user->isAdmin() || $user->isSupervisor();

    }

}
