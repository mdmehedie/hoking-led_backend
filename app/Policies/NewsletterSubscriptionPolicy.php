<?php

namespace App\Policies;

use App\Models\NewsletterSubscription;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NewsletterSubscriptionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view newslettersubscription');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, NewsletterSubscription $model): bool
    {
        return $user->hasPermissionTo('view newslettersubscription');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create newslettersubscription');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NewsletterSubscription $model): bool
    {
        return $user->hasPermissionTo('edit newslettersubscription');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NewsletterSubscription $model): bool
    {
        return $user->hasPermissionTo('delete newslettersubscription');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, NewsletterSubscription $model): bool
    {
        return $user->hasPermissionTo('edit newslettersubscription');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, NewsletterSubscription $model): bool
    {
        return $user->hasPermissionTo('delete newslettersubscription');
    }
}