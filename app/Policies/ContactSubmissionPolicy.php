<?php

namespace App\Policies;

use App\Models\ContactSubmission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContactSubmissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view contactsubmission');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ContactSubmission $model): bool
    {
        return $user->hasPermissionTo('view contactsubmission');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create contactsubmission');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContactSubmission $model): bool
    {
        return $user->hasPermissionTo('edit contactsubmission');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContactSubmission $model): bool
    {
        return $user->hasPermissionTo('delete contactsubmission');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ContactSubmission $model): bool
    {
        return $user->hasPermissionTo('edit contactsubmission');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ContactSubmission $model): bool
    {
        return $user->hasPermissionTo('delete contactsubmission');
    }
}