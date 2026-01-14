<?php

namespace App\Policies;

use App\Models\TypeStructure;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TypeStructurePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the typeStructure can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the typeStructure can view the model.
     */
    public function view(User $user, TypeStructure $model): bool
    {
        return true;
    }

    /**
     * Determine whether the typeStructure can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the typeStructure can update the model.
     */
    public function update(User $user, TypeStructure $model): bool
    {
        return true;
    }

    /**
     * Determine whether the typeStructure can delete the model.
     */
    public function delete(User $user, TypeStructure $model): bool
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
     * Determine whether the typeStructure can restore the model.
     */
    public function restore(User $user, TypeStructure $model): bool
    {
        return false;
    }

    /**
     * Determine whether the typeStructure can permanently delete the model.
     */
    public function forceDelete(User $user, TypeStructure $model): bool
    {
        return false;
    }
}
