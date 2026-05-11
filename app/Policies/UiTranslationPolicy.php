<?php

namespace App\Policies;

use App\Models\UiTranslation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UiTranslationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view uitranslation');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UiTranslation $model): bool
    {
        return $user->hasPermissionTo('view uitranslation');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create uitranslation');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UiTranslation $model): bool
    {
        return $user->hasPermissionTo('edit uitranslation');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UiTranslation $model): bool
    {
        return $user->hasPermissionTo('delete uitranslation');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UiTranslation $model): bool
    {
        return $user->hasPermissionTo('edit uitranslation');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UiTranslation $model): bool
    {
        return $user->hasPermissionTo('delete uitranslation');
    }
}