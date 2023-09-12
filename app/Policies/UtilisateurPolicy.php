<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;

class UtilisateurPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the utilisateur can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the utilisateur can view the model.
     */
    public function view(User $user, Utilisateur $model): bool
    {
        return true;
    }

    /**
     * Determine whether the utilisateur can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the utilisateur can update the model.
     */
    public function update(User $user, Utilisateur $model): bool
    {
        return true;
    }

    /**
     * Determine whether the utilisateur can delete the model.
     */
    public function delete(User $user, Utilisateur $model): bool
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
     * Determine whether the utilisateur can restore the model.
     */
    public function restore(User $user, Utilisateur $model): bool
    {
        return false;
    }

    /**
     * Determine whether the utilisateur can permanently delete the model.
     */
    public function forceDelete(User $user, Utilisateur $model): bool
    {
        return false;
    }
}
