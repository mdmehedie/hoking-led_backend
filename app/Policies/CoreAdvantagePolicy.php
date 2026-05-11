<?php

namespace App\Policies;

use App\Models\CoreAdvantage;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoreAdvantagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view coreadvantage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CoreAdvantage $model): bool
    {
        return $user->hasPermissionTo('view coreadvantage');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create coreadvantage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CoreAdvantage $model): bool
    {
        return $user->hasPermissionTo('edit coreadvantage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CoreAdvantage $model): bool
    {
        return $user->hasPermissionTo('delete coreadvantage');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CoreAdvantage $model): bool
    {
        return $user->hasPermissionTo('edit coreadvantage');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CoreAdvantage $model): bool
    {
        return $user->hasPermissionTo('delete coreadvantage');
    }
}