<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the article can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the article can view the model.
     */
    public function view(User $user, Article $model): bool
    {
        return true;
    }

    /**
     * Determine whether the article can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the article can update the model.
     */
    public function update(User $user, Article $model): bool
    {
        return true;
    }

    /**
     * Determine whether the article can delete the model.
     */
    public function delete(User $user, Article $model): bool
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
     * Determine whether the article can restore the model.
     */
    public function restore(User $user, Article $model): bool
    {
        return false;
    }

    /**
     * Determine whether the article can permanently delete the model.
     */
    public function forceDelete(User $user, Article $model): bool
    {
        return false;
    }
}
