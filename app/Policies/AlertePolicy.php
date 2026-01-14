<?php

namespace App\Policies;

use App\Models\Alerte;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlertePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the alerte can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the alerte can view the model.
     */
    public function view(User $user, Alerte $model): bool
    {
        return true;
    }

    /**
     * Determine whether the alerte can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the alerte can update the model.
     */
    public function update(User $user, Alerte $model): bool
    {
        return true;
    }

    /**
     * Determine whether the alerte can delete the model.
     */
    public function delete(User $user, Alerte $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the alerte can restore the model.
     */
    public function restore(User $user, Alerte $model): bool
    {
        return false;
    }

    /**
     * Determine whether the alerte can permanently delete the model.
     */
    public function forceDelete(User $user, Alerte $model): bool
    {
        return false;
    }
}
