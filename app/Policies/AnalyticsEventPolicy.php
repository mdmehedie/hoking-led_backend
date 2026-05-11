<?php

namespace App\Policies;

use App\Models\AnalyticsEvent;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AnalyticsEventPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view analyticsevent');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AnalyticsEvent $model): bool
    {
        return $user->hasPermissionTo('view analyticsevent');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create analyticsevent');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AnalyticsEvent $model): bool
    {
        return $user->hasPermissionTo('edit analyticsevent');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AnalyticsEvent $model): bool
    {
        return $user->hasPermissionTo('delete analyticsevent');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AnalyticsEvent $model): bool
    {
        return $user->hasPermissionTo('edit analyticsevent');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AnalyticsEvent $model): bool
    {
        return $user->hasPermissionTo('delete analyticsevent');
    }
}