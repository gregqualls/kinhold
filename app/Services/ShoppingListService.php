<?php

namespace App\Services;

use App\Enums\ShoppingItemSource;
use App\Models\Family;
use App\Models\ProductCatalog;
use App\Models\Recipe;
use App\Models\ShoppingItem;
use App\Models\ShoppingList;
use App\Models\Staple;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ShoppingListService
{
    public function createList(Family $family, User $user, string $name, ?string $store = null): ShoppingList
    {
        $list = ShoppingList::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'name' => $name,
            'store_name' => $store,
        ]);

        $staples = Staple::where('family_id', $family->id)
            ->where('is_active', true)
            ->get();

        if ($staples->isNotEmpty()) {
            $now = now();
            ShoppingItem::insert($staples->map(fn (Staple $staple) => [
                'id' => (string) Str::uuid(),
                'shopping_list_id' => $list->id,
                'family_id' => $family->id,
                'added_by' => $user->id,
                'name' => $staple->name,
                'quantity' => $staple->default_quantity,
                'category' => $staple->category,
                'source' => ShoppingItemSource::Staple->value,
                'is_checked' => false,
                'has_on_hand' => false,
                'sort_order' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray());
        }

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
        ]);
    }

    public function addRecipeIngredients(ShoppingList $list, Recipe $recipe, User $user, ?string $mealPlanEntryId = null, ?string $neededDate = null): Collection
    {
        $recipe->load('ingredients');

        $items = collect();

        foreach ($recipe->ingredients as $ingredient) {
            $quantity = trim(($ingredient->quantity ?? '').' '.($ingredient->unit ?? ''));

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

            $items->push($item);
        }

        return $items;
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
