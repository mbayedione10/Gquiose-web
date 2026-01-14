<?php

namespace App\Policies;

use App\Models\TypeAlerte;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TypeAlertePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the typeAlerte can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the typeAlerte can view the model.
     */
    public function view(User $user, TypeAlerte $model): bool
    {
        return true;
    }

    /**
     * Determine whether the typeAlerte can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the typeAlerte can update the model.
     */
    public function update(User $user, TypeAlerte $model): bool
    {
        return true;
    }

    /**
     * Determine whether the typeAlerte can delete the model.
     */
    public function delete(User $user, TypeAlerte $model): bool
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
     * Determine whether the typeAlerte can restore the model.
     */
    public function restore(User $user, TypeAlerte $model): bool
    {
        return false;
    }

    /**
     * Determine whether the typeAlerte can permanently delete the model.
     */
    public function forceDelete(User $user, TypeAlerte $model): bool
    {
        return false;
    }
}
