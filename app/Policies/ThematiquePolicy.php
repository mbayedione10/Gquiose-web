<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Thematique;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThematiquePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the thematique can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the thematique can view the model.
     */
    public function view(User $user, Thematique $model): bool
    {
        return true;
    }

    /**
     * Determine whether the thematique can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the thematique can update the model.
     */
    public function update(User $user, Thematique $model): bool
    {
        return true;
    }

    /**
     * Determine whether the thematique can delete the model.
     */
    public function delete(User $user, Thematique $model): bool
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
     * Determine whether the thematique can restore the model.
     */
    public function restore(User $user, Thematique $model): bool
    {
        return false;
    }

    /**
     * Determine whether the thematique can permanently delete the model.
     */
    public function forceDelete(User $user, Thematique $model): bool
    {
        return false;
    }
}
