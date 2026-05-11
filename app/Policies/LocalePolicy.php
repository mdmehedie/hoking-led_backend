<?php

namespace App\Policies;

use App\Models\Locale;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LocalePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view locale');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Locale $model): bool
    {
        return $user->hasPermissionTo('view locale');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create locale');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Locale $model): bool
    {
        return $user->hasPermissionTo('edit locale');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Locale $model): bool
    {
        return $user->hasPermissionTo('delete locale');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Locale $model): bool
    {
        return $user->hasPermissionTo('edit locale');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Locale $model): bool
    {
        return $user->hasPermissionTo('delete locale');
    }
}