<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResponsePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the response can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the response can view the model.
     */
    public function view(User $user, Response $model): bool
    {
        return true;
    }

    /**
     * Determine whether the response can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the response can update the model.
     */
    public function update(User $user, Response $model): bool
    {
        return true;
    }

    /**
     * Determine whether the response can delete the model.
     */
    public function delete(User $user, Response $model): bool
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
     * Determine whether the response can restore the model.
     */
    public function restore(User $user, Response $model): bool
    {
        return false;
    }

    /**
     * Determine whether the response can permanently delete the model.
     */
    public function forceDelete(User $user, Response $model): bool
    {
        return false;
    }
}
