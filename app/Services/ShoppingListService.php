<?php

namespace App\Services;

use App\Enums\ShoppingItemSource;
use App\Models\Family;
use App\Models\ProductCatalog;
use App\Models\Recipe;
use App\Models\ShoppingItem;
use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Support\Collection;

class ShoppingListService
{
    public function createList(Family $family, User $user, string $name, ?string $store = null): ShoppingList
    {
        $list = ShoppingList::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'name' => $name ?: ($store ?? 'Shopping List'),
            'store_name' => $store,
        ]);

        return $list->load('items');
    }

    public function addItem(ShoppingList $list, array $data, User $user): ShoppingItem
    {
        if (empty($data['category'])) {
            $data['category'] = $this->autoCategorize($data['name']);
        }

        return ShoppingItem::create([
            'shopping_list_id' => $list->id,
            'family_id' => $list->family_id,
            'added_by' => $user->id,
            'name' => $data['name'],
            'quantity' => $data['quantity'] ?? null,
            'category' => $data['category'] ?? null,
            'notes' => $data['notes'] ?? null,
            'source' => ShoppingItemSource::Manual,
            'is_recurring' => $data['is_recurring'] ?? false,
            'default_quantity' => $data['default_quantity'] ?? ($data['is_recurring'] ?? false ? ($data['quantity'] ?? null) : null),
        ]);
    }

    public function addRecipeIngredients(ShoppingList $list, Recipe $recipe, User $user, ?string $mealPlanEntryId = null, ?string $neededDate = null, ?array $ingredientIds = null): Collection
    {
        $recipe->load('ingredients');

        // Pre-load existing items on this list for dedup (case-insensitive by name)
        $existingItems = $list->items()->get()->keyBy(fn ($item) => strtolower(trim($item->name)));

        $items = collect();

        foreach ($recipe->ingredients as $ingredient) {
            // If specific ingredients were requested, skip the rest
            if ($ingredientIds !== null && ! in_array($ingredient->id, $ingredientIds)) {
                continue;
            }

            $quantity = trim(($ingredient->quantity ?? '').' '.($ingredient->unit ?? ''));
            $nameKey = strtolower(trim($ingredient->name));

            // Check if this ingredient already exists on the list
            $existing = $existingItems->get($nameKey);

            if ($existing) {
                // Aggregate: append recipe attribution, update quantity if possible
                $updates = [];

                // Append recipe name to attribution if not already there
                $currentAttribution = $existing->source_recipe_name ?? '';
                if ($currentAttribution && ! str_contains($currentAttribution, $recipe->title)) {
                    $updates['source_recipe_name'] = $currentAttribution.', '.$recipe->title;
                } elseif (! $currentAttribution) {
                    $updates['source_recipe_name'] = $recipe->title;
                }

                // Try to sum quantities if both are present and have the same unit
                $newQty = $this->tryAggregateQuantity($existing->quantity, $quantity);
                if ($newQty !== null) {
                    $updates['quantity'] = $newQty;
                }

                // Ensure source is recipe
                if ($existing->source !== ShoppingItemSource::Recipe) {
                    $updates['source'] = ShoppingItemSource::Recipe;
                    $updates['source_recipe_id'] = $recipe->id;
                }

                if (! empty($updates)) {
                    $existing->update($updates);
                }

                $items->push($existing->fresh());
            } else {
                // Create new item
                $item = ShoppingItem::create([
                    'shopping_list_id' => $list->id,
                    'family_id' => $list->family_id,
                    'added_by' => $user->id,
                    'name' => $ingredient->name,
                    'quantity' => $quantity ?: null,
                    'category' => $this->autoCategorize($ingredient->name),
                    'source' => ShoppingItemSource::Recipe,
                    'source_recipe_id' => $recipe->id,
                    'source_recipe_name' => $recipe->title,
                    'source_ingredient_id' => $ingredient->id,
                    'meal_plan_entry_id' => $mealPlanEntryId,
                    'needed_date' => $neededDate,
                ]);

                // Add to lookup so subsequent ingredients in the same recipe also dedup
                $existingItems->put($nameKey, $item);
                $items->push($item);
            }
        }

        return $items;
    }

    /**
     * Try to aggregate two quantity strings (e.g. "1 tbsp" + "1 tbsp" = "2 tbsp").
     * Returns the combined string if units match, or null if they can't be combined.
     */
    private function tryAggregateQuantity(?string $existing, ?string $new): ?string
    {
        if (! $existing || ! $new) {
            return null;
        }

        // Parse "number unit" pattern (e.g. "1.000 tbsp", "2 cups", "1")
        $pattern = '/^([\d.\/]+)\s*(.*)$/';

        if (! preg_match($pattern, trim($existing), $existingMatch)) {
            return null;
        }
        if (! preg_match($pattern, trim($new), $newMatch)) {
            return null;
        }

        $existingUnit = strtolower(trim($existingMatch[2] ?? ''));
        $newUnit = strtolower(trim($newMatch[2] ?? ''));

        // Units must match to aggregate
        if ($existingUnit !== $newUnit) {
            return null;
        }

        $existingNum = $this->parseFraction($existingMatch[1]);
        $newNum = $this->parseFraction($newMatch[1]);

        if ($existingNum === null || $newNum === null) {
            return null;
        }

        $sum = $existingNum + $newNum;

        // Format: use integer if whole, otherwise 2 decimal places
        $formatted = (floor($sum) == $sum) ? (string) (int) $sum : number_format($sum, 2);

        return $existingUnit ? "$formatted $existingUnit" : $formatted;
    }

    /**
     * Parse a number string that might be a fraction (e.g. "1/2", "1.5", "3").
     */
    private function parseFraction(string $value): ?float
    {
        $value = trim($value);

        if (is_numeric($value)) {
            return (float) $value;
        }

        // Handle fractions like "1/2"
        if (preg_match('/^(\d+)\/(\d+)$/', $value, $m)) {
            return (int) $m[2] !== 0 ? (int) $m[1] / (int) $m[2] : null;
        }

        return null;
    }

    public function removeRecipeItems(ShoppingList $list, string $mealPlanEntryId): void
    {
        $list->items()
            ->where('meal_plan_entry_id', $mealPlanEntryId)
            ->delete();
    }

    public function checkItem(ShoppingItem $item, User $user): void
    {
        $item->update([
            'is_checked' => true,
            'checked_by' => $user->id,
            'checked_at' => now(),
        ]);
    }

    public function uncheckItem(ShoppingItem $item): void
    {
        $item->update([
            'is_checked' => false,
            'checked_by' => null,
            'checked_at' => null,
        ]);
    }

    public function markOnHand(ShoppingItem $item): void
    {
        $item->update(['has_on_hand' => true]);
    }

    public function clearOnHand(ShoppingItem $item): void
    {
        $item->update(['has_on_hand' => false]);
    }

    /**
     * Clear checked items from a list. Non-recurring items are deleted.
     * Recurring items are reset to unchecked with their default quantity.
     *
     * @return array{cleared: int, recurring_reset: int}
     */
    public function clearChecked(ShoppingList $list): array
    {
        $checkedItems = $list->items()->where('is_checked', true)->get();

        $recurring = $checkedItems->where('is_recurring', true);
        $nonRecurring = $checkedItems->where('is_recurring', false);

        // Delete non-recurring checked items
        $cleared = $nonRecurring->count();
        if ($cleared > 0) {
            ShoppingItem::whereIn('id', $nonRecurring->pluck('id'))->delete();
        }

        // Reset recurring items back to unchecked
        $recurringReset = $recurring->count();
        foreach ($recurring as $item) {
            $item->update([
                'is_checked' => false,
                'checked_by' => null,
                'checked_at' => null,
                'has_on_hand' => false,
                'quantity' => $item->default_quantity ?? $item->quantity,
            ]);
        }

        return ['cleared' => $cleared, 'recurring_reset' => $recurringReset];
    }

    /**
     * Move an item from one list to another.
     */
    public function moveItem(ShoppingItem $item, ShoppingList $targetList): ShoppingItem
    {
        $item->update([
            'shopping_list_id' => $targetList->id,
        ]);

        return $item->fresh();
    }

    /**
     * Toggle the recurring flag on an item.
     * When setting to recurring, captures the current quantity as default.
     */
    public function toggleRecurring(ShoppingItem $item): ShoppingItem
    {
        $wasRecurring = $item->is_recurring;

        $updates = ['is_recurring' => ! $wasRecurring];

        // When making recurring, save current quantity as the default
        if (! $wasRecurring && $item->default_quantity === null) {
            $updates['default_quantity'] = $item->quantity;
        }

        // When un-recurring, clear the default quantity
        if ($wasRecurring) {
            $updates['default_quantity'] = null;
        }

        $item->update($updates);

        return $item->fresh();
    }

    /** @deprecated Use clearChecked() instead */
    public function completeTrip(ShoppingList $list): void
    {
        $list->update([
            'is_active' => false,
            'completed_at' => now(),
        ]);
    }

    private function autoCategorize(string $itemName): ?string
    {
        $catalogItem = ProductCatalog::whereRaw('LOWER(name) = ?', [strtolower($itemName)])->first();

        if ($catalogItem) {
            return $catalogItem->category;
        }

        $catalogItem = ProductCatalog::whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($itemName).'%'])->first();

        return $catalogItem?->category;
    }
}
