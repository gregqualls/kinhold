<?php

namespace App\Policies;

use App\Models\Recipe;
use App\Models\User;

class RecipePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Recipe $recipe): bool
    {
        return $user->family_id === $recipe->family_id;
    }

    public function create(User $user): bool
    {
        return $user->isParent();
    }

    public function update(User $user, Recipe $recipe): bool
    {
        if ($user->family_id !== $recipe->family_id) {
            return false;
        }

        return $user->isParent();
    }

    public function delete(User $user, Recipe $recipe): bool
    {
        if ($user->family_id !== $recipe->family_id) {
            return false;
        }

        return $user->isParent();
    }

    public function restore(User $user, Recipe $recipe): bool
    {
        if ($user->family_id !== $recipe->family_id) {
            return false;
        }

        return $user->isParent();
    }

    public function rate(User $user, Recipe $recipe): bool
    {
        return $user->family_id === $recipe->family_id;
    }

    public function addCookLog(User $user, Recipe $recipe): bool
    {
        return $user->family_id === $recipe->family_id;
    }
}
