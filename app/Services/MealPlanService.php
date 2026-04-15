<?php

namespace App\Services;

use App\Models\Family;
use App\Models\MealPlan;
use App\Models\MealPlanEntry;
use App\Models\ShoppingList;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class MealPlanService
{
    public function __construct(
        private ShoppingListService $shoppingListService,
    ) {}

    /**
     * Get or create the plan for the current week (Monday start).
     */
    public function getCurrentWeekPlan(Family $family, User $user): MealPlan
    {
        $monday = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();

        return $this->getOrCreatePlan($family, $user, $monday);
    }

    /**
     * Get or create a plan for a specific week.
     */
    public function getOrCreatePlan(Family $family, User $user, string $weekStart): MealPlan
    {
        return MealPlan::firstOrCreate(
            ['family_id' => $family->id, 'week_start' => $weekStart],
            ['created_by' => $user->id]
        )->load(['entries.recipe', 'entries.restaurant', 'entries.preset', 'shoppingList']);
    }

    /**
     * Add an entry to a meal plan. Triggers shopping list and task cascades.
     */
    public function addEntry(MealPlan $plan, array $data, User $user): MealPlanEntry
    {
        $entry = MealPlanEntry::create([
            'meal_plan_id' => $plan->id,
            'recipe_id' => $data['recipe_id'] ?? null,
            'restaurant_id' => $data['restaurant_id'] ?? null,
            'meal_preset_id' => $data['meal_preset_id'] ?? null,
            'custom_title' => $data['custom_title'] ?? null,
            'date' => $data['date'],
            'meal_slot' => $data['meal_slot'],
            'servings' => $data['servings'] ?? null,
            'assigned_cooks' => $data['assigned_cooks'] ?? null,
            'notes' => $data['notes'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        // Shopping list cascade: only for recipe entries
        if ($entry->recipe_id) {
            $list = $this->ensureShoppingList($plan, $user);
            $this->shoppingListService->addRecipeIngredients(
                $list,
                $entry->recipe,
                $user,
                $entry->id,
                $entry->date->toDateString()
            );
        }

        // Cook assignment cascade: create tasks for assigned cooks
        if (! empty($entry->assigned_cooks)) {
            $this->createCookTasks($entry, $plan, $user);
        }

        return $entry->load(['recipe', 'restaurant', 'preset']);
    }

    /**
     * Update an entry. Diff-based cascade for shopping items and tasks.
     */
    public function updateEntry(MealPlanEntry $entry, array $data, User $user): MealPlanEntry
    {
        $oldRecipeId = $entry->recipe_id;
        $oldServings = $entry->servings;
        $oldCooks = $entry->assigned_cooks ?? [];
        $plan = $entry->mealPlan;

        $entry->update($data);
        $entry->refresh();

        $newRecipeId = $entry->recipe_id;
        $recipeChanged = $oldRecipeId !== $newRecipeId;
        $servingsChanged = $oldServings !== $entry->servings;

        // Shopping list cascade
        if ($plan->shopping_list_id) {
            $list = $plan->shoppingList;

            if ($recipeChanged) {
                // Remove old recipe items
                if ($oldRecipeId) {
                    $this->shoppingListService->removeRecipeItems($list, $entry->id);
                }
                // Add new recipe items
                if ($newRecipeId) {
                    $this->shoppingListService->addRecipeIngredients(
                        $list,
                        $entry->recipe,
                        $user,
                        $entry->id,
                        $entry->date->toDateString()
                    );
                }
            } elseif ($servingsChanged && $newRecipeId) {
                // Re-add with new servings (remove + add)
                $this->shoppingListService->removeRecipeItems($list, $entry->id);
                $this->shoppingListService->addRecipeIngredients(
                    $list,
                    $entry->recipe,
                    $user,
                    $entry->id,
                    $entry->date->toDateString()
                );
            }
        }

        // Cook task cascade
        $newCooks = $entry->assigned_cooks ?? [];
        if ($oldCooks !== $newCooks) {
            $removed = array_diff($oldCooks, $newCooks);
            $added = array_diff($newCooks, $oldCooks);

            // Delete tasks for removed cooks
            if (! empty($removed)) {
                Task::where('source_type', MealPlanEntry::class)
                    ->where('source_id', $entry->id)
                    ->whereIn('assigned_to', $removed)
                    ->delete();
            }

            // Create tasks for newly added cooks
            foreach ($added as $cookId) {
                $this->createSingleCookTask($entry, $plan, $user, $cookId);
            }
        }

        // Update task titles if the entry type/recipe changed
        if ($recipeChanged) {
            Task::where('source_type', MealPlanEntry::class)
                ->where('source_id', $entry->id)
                ->update(['title' => $this->getCookTaskTitle($entry)]);
        }

        return $entry->load(['recipe', 'restaurant', 'preset']);
    }

    /**
     * Remove an entry and cascade-delete shopping items + tasks.
     */
    public function removeEntry(MealPlanEntry $entry): void
    {
        $plan = $entry->mealPlan;

        // Remove shopping items linked to this entry
        if ($entry->recipe_id && $plan->shopping_list_id) {
            $this->shoppingListService->removeRecipeItems($plan->shoppingList, $entry->id);
        }

        // Delete all linked cook tasks
        Task::where('source_type', MealPlanEntry::class)
            ->where('source_id', $entry->id)
            ->delete();

        $entry->delete();
    }

    /**
     * Move an entry to a different day/slot.
     */
    public function moveEntry(MealPlanEntry $entry, string $date, string $mealSlot): MealPlanEntry
    {
        $entry->update([
            'date' => $date,
            'meal_slot' => $mealSlot,
        ]);

        // Update linked tasks' due dates
        Task::where('source_type', MealPlanEntry::class)
            ->where('source_id', $entry->id)
            ->update(['due_date' => $date]);

        // Update shopping items' needed dates
        if ($entry->recipe_id && $entry->mealPlan->shopping_list_id) {
            $entry->mealPlan->shoppingList->items()
                ->where('meal_plan_entry_id', $entry->id)
                ->update(['needed_date' => $date]);
        }

        return $entry->fresh()->load(['recipe', 'restaurant', 'preset']);
    }

    /**
     * Force full regeneration of the shopping list from all recipe entries.
     */
    public function generateShoppingList(MealPlan $plan, User $user): void
    {
        $list = $this->ensureShoppingList($plan, $user);

        // Remove all meal-plan-sourced items
        $plan->entries()->whereNotNull('recipe_id')->get()->each(function ($entry) use ($list) {
            $this->shoppingListService->removeRecipeItems($list, $entry->id);
        });

        // Re-add all recipe ingredients
        $plan->entries()->whereNotNull('recipe_id')->with('recipe')->get()->each(function ($entry) use ($list, $user) {
            $this->shoppingListService->addRecipeIngredients(
                $list,
                $entry->recipe,
                $user,
                $entry->id,
                $entry->date->toDateString()
            );
        });
    }

    /**
     * Ensure the plan has a linked shopping list, creating one if needed.
     */
    private function ensureShoppingList(MealPlan $plan, User $user): ShoppingList
    {
        if ($plan->shopping_list_id && $plan->shoppingList) {
            return $plan->shoppingList;
        }

        $weekLabel = Carbon::parse($plan->week_start)->format('M j').' Groceries';
        $list = $this->shoppingListService->createList($plan->family, $user, $weekLabel);

        $plan->update(['shopping_list_id' => $list->id]);
        $plan->setRelation('shoppingList', $list);

        return $list;
    }

    /**
     * Create cook tasks for all assigned cooks on an entry.
     */
    private function createCookTasks(MealPlanEntry $entry, MealPlan $plan, User $user): void
    {
        foreach ($entry->assigned_cooks as $cookId) {
            $this->createSingleCookTask($entry, $plan, $user, $cookId);
        }
    }

    /**
     * Create a single cook task for one user.
     */
    private function createSingleCookTask(MealPlanEntry $entry, MealPlan $plan, User $user, string $cookId): void
    {
        $cookPoints = $plan->family->settings['cook_task_points'] ?? 5;

        Task::create([
            'family_id' => $plan->family_id,
            'created_by' => $user->id,
            'assigned_to' => $cookId,
            'title' => $this->getCookTaskTitle($entry),
            'due_date' => $entry->date,
            'points' => $cookPoints,
            'is_family_task' => false,
            'source_type' => MealPlanEntry::class,
            'source_id' => $entry->id,
        ]);
    }

    /**
     * Generate a task title based on the entry type.
     */
    private function getCookTaskTitle(MealPlanEntry $entry): string
    {
        if ($entry->recipe_id) {
            return 'Cook: '.($entry->recipe?->title ?? 'Unknown Recipe');
        }
        if ($entry->restaurant_id) {
            return 'Order: '.($entry->restaurant?->name ?? 'Unknown Restaurant');
        }
        if ($entry->meal_preset_id) {
            return 'Dinner: '.($entry->preset?->label ?? 'Unknown Preset');
        }

        return 'Prep: '.($entry->custom_title ?? 'Untitled');
    }
}
