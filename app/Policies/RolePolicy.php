<?php

namespace App\Policies;

use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view role');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, $role): bool
    {
        return $user->hasPermissionTo('view role');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create role');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, $role): bool
    {
        return $user->hasPermissionTo('edit role');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, $role): bool
    {
        return $user->hasPermissionTo('delete role');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, $role): bool
    {
        return $user->hasPermissionTo('edit role');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, $role): bool
    {
        return $user->hasPermissionTo('delete role');
    }
}
