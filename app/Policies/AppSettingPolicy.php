<?php

namespace App\Policies;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppSettingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view appsetting');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AppSetting $appSetting): bool
    {
        return $user->hasPermissionTo('view appsetting');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create appsetting');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AppSetting $appSetting): bool
    {
        return $user->hasPermissionTo('edit appsetting');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AppSetting $appSetting): bool
    {
        return $user->hasPermissionTo('delete appsetting');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AppSetting $appSetting): bool
    {
        return $user->hasPermissionTo('edit appsetting');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AppSetting $appSetting): bool
    {
        return $user->hasPermissionTo('delete appsetting');
    }
}
