<?php

namespace App\Policies;

use App\Models\Suivi;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SuiviPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the suivi can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the suivi can view the model.
     */
    public function view(User $user, Suivi $model): bool
    {
        return true;
    }

    /**
     * Determine whether the suivi can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the suivi can update the model.
     */
    public function update(User $user, Suivi $model): bool
    {
        return true;
    }

    /**
     * Determine whether the suivi can delete the model.
     */
    public function delete(User $user, Suivi $model): bool
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
     * Determine whether the suivi can restore the model.
     */
    public function restore(User $user, Suivi $model): bool
    {
        return false;
    }

    /**
     * Determine whether the suivi can permanently delete the model.
     */
    public function forceDelete(User $user, Suivi $model): bool
    {
        return false;
    }
}
