<?php

namespace App\Policies;

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TeamMemberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view teammember');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TeamMember $model): bool
    {
        return $user->hasPermissionTo('view teammember');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create teammember');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TeamMember $model): bool
    {
        return $user->hasPermissionTo('edit teammember');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TeamMember $model): bool
    {
        return $user->hasPermissionTo('delete teammember');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TeamMember $model): bool
    {
        return $user->hasPermissionTo('edit teammember');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TeamMember $model): bool
    {
        return $user->hasPermissionTo('delete teammember');
    }
}