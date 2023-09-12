<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ville;
use Illuminate\Auth\Access\HandlesAuthorization;

class VillePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the ville can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the ville can view the model.
     */
    public function view(User $user, Ville $model): bool
    {
        return true;
    }

    /**
     * Determine whether the ville can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the ville can update the model.
     */
    public function update(User $user, Ville $model): bool
    {
        return true;
    }

    /**
     * Determine whether the ville can delete the model.
     */
    public function delete(User $user, Ville $model): bool
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
     * Determine whether the ville can restore the model.
     */
    public function restore(User $user, Ville $model): bool
    {
        return false;
    }

    /**
     * Determine whether the ville can permanently delete the model.
     */
    public function forceDelete(User $user, Ville $model): bool
    {
        return false;
    }
}
