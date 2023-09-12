<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Structure;
use Illuminate\Auth\Access\HandlesAuthorization;

class StructurePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the structure can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the structure can view the model.
     */
    public function view(User $user, Structure $model): bool
    {
        return true;
    }

    /**
     * Determine whether the structure can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the structure can update the model.
     */
    public function update(User $user, Structure $model): bool
    {
        return true;
    }

    /**
     * Determine whether the structure can delete the model.
     */
    public function delete(User $user, Structure $model): bool
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
     * Determine whether the structure can restore the model.
     */
    public function restore(User $user, Structure $model): bool
    {
        return false;
    }

    /**
     * Determine whether the structure can permanently delete the model.
     */
    public function forceDelete(User $user, Structure $model): bool
    {
        return false;
    }
}
