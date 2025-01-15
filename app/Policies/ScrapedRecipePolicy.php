<?php

namespace App\Policies;

use App\Models\ScrapedRecipe;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ScrapedRecipePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ScrapedRecipe $scrapedRecipe): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ScrapedRecipe $scrapedRecipe): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ScrapedRecipe $scrapedRecipe): bool
    {
        return true;
    }
}
