<?php

namespace App\Policies;

use App\Models\CertificationAward;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CertificationAwardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view certificationaward');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CertificationAward $certificationAward): bool
    {
        return $user->hasPermissionTo('view certificationaward');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create certificationaward');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CertificationAward $certificationAward): bool
    {
        return $user->hasPermissionTo('edit certificationaward');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CertificationAward $certificationAward): bool
    {
        return $user->hasPermissionTo('delete certificationaward');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CertificationAward $certificationAward): bool
    {
        return $user->hasPermissionTo('edit certificationaward');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CertificationAward $certificationAward): bool
    {
        return $user->hasPermissionTo('delete certificationaward');
    }
}
