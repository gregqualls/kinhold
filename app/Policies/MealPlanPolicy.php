<?php

namespace App\Policies;

use App\Models\MealPlan;
use App\Models\MealPlanEntry;
use App\Models\Restaurant;
use App\Models\User;

class MealPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MealPlan $plan): bool
    {
        return $user->family_id === $plan->family_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MealPlan $plan): bool
    {
        return $user->family_id === $plan->family_id;
    }

    public function delete(User $user, MealPlan $plan): bool
    {
        return $user->family_id === $plan->family_id && $user->isParent();
    }

    // Entry-level policies
    public function addEntry(User $user, MealPlan $plan): bool
    {
        return $user->family_id === $plan->family_id;
    }

    public function updateEntry(User $user, MealPlanEntry $entry): bool
    {
        return $user->family_id === $entry->mealPlan->family_id;
    }

    public function deleteEntry(User $user, MealPlanEntry $entry): bool
    {
        return $user->family_id === $entry->mealPlan->family_id;
    }

    // Preset policies (parent only for management)
    public function managePresets(User $user): bool
    {
        return $user->isParent();
    }

    // Restaurant policies
    public function manageRestaurants(User $user): bool
    {
        return true; // Any family member can manage restaurants
    }
}
