<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Question;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the question can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the question can view the model.
     */
    public function view(User $user, Question $model): bool
    {
        return true;
    }

    /**
     * Determine whether the question can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the question can update the model.
     */
    public function update(User $user, Question $model): bool
    {
        return true;
    }

    /**
     * Determine whether the question can delete the model.
     */
    public function delete(User $user, Question $model): bool
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
     * Determine whether the question can restore the model.
     */
    public function restore(User $user, Question $model): bool
    {
        return false;
    }

    /**
     * Determine whether the question can permanently delete the model.
     */
    public function forceDelete(User $user, Question $model): bool
    {
        return false;
    }
}
