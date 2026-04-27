<?php

namespace Database\Seeders;

use App\Enums\ShoppingItemSource;
use App\Models\ShoppingItem;
use App\Models\ShoppingList;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoShoppingSeeder extends Seeder
{
    use DemoFamilyContext;

    public function run(): void
    {
        $this->loadDemoContext();

        $family = $this->family();
        $sarah = $this->sarah;
        $mike = $this->mike;

        $now = Carbon::now();

        // ─────────────────────────────────────────────
        //  ACTIVE WEEKLY GROCERIES LIST
        // ─────────────────────────────────────────────

        $weekly = ShoppingList::create([
            'family_id' => $family->id,
            'created_by' => $sarah->id,
            'name' => 'Weekly Groceries',
            'store_name' => 'Costco',
            'is_active' => true,
        ]);

        $weeklyItems = [
            // Produce
            ['name' => 'Bananas',           'quantity' => '6',          'category' => 'produce', 'is_recurring' => true],
            ['name' => 'Spinach',           'quantity' => '1 bag',      'category' => 'produce'],
            ['name' => 'Strawberries',      'quantity' => '1 lb',       'category' => 'produce'],
            ['name' => 'Bell peppers',      'quantity' => '3',          'category' => 'produce'],
            ['name' => 'Broccoli florets',  'quantity' => '2 cups',     'category' => 'produce'],
            ['name' => 'Lemons',            'quantity' => '4',          'category' => 'produce'],
            // Dairy
            ['name' => 'Whole milk',        'quantity' => '1 gallon',   'category' => 'dairy', 'is_recurring' => true],
            ['name' => 'Greek yogurt',      'quantity' => '32 oz',      'category' => 'dairy'],
            ['name' => 'Sharp cheddar',     'quantity' => '1 block',    'category' => 'dairy'],
            ['name' => 'Butter',            'quantity' => '1 lb',       'category' => 'dairy', 'is_recurring' => true],
            // Meat / Seafood
            ['name' => 'Ground beef',       'quantity' => '1 lb',       'category' => 'meat'],
            ['name' => 'Chicken breast',    'quantity' => '1.5 lb',     'category' => 'meat'],
            ['name' => 'Salmon fillets',    'quantity' => '4 (6oz)',    'category' => 'meat'],
            // Pantry
            ['name' => 'Spaghetti',         'quantity' => '1 lb',       'category' => 'pantry'],
            ['name' => 'Crushed tomatoes',  'quantity' => '28 oz',      'category' => 'pantry'],
            ['name' => 'Olive oil',         'quantity' => '1 bottle',   'category' => 'pantry', 'is_recurring' => true],
            ['name' => 'Soy sauce',         'quantity' => '1 bottle',   'category' => 'pantry'],
            // Bakery
            ['name' => 'Bread',             'quantity' => '1 loaf',     'category' => 'bakery', 'is_recurring' => true],
            ['name' => 'Tortillas',         'quantity' => '1 pack',     'category' => 'bakery'],
            // Snacks (already grabbed)
            ['name' => 'Granola bars',      'quantity' => '1 box',      'category' => 'snacks',  'is_checked' => true,
                'checked_by' => $mike->id, 'checked_at' => $now->copy()->subHours(2)],
            ['name' => 'Goldfish crackers', 'quantity' => '1 carton',   'category' => 'snacks',  'is_checked' => true,
                'checked_by' => $mike->id, 'checked_at' => $now->copy()->subHours(2)],
        ];

        foreach ($weeklyItems as $idx => $def) {
            $isRecurring = $def['is_recurring'] ?? false;
            ShoppingItem::create([
                'shopping_list_id' => $weekly->id,
                'family_id' => $family->id,
                'added_by' => $sarah->id,
                'name' => $def['name'],
                'quantity' => $def['quantity'] ?? null,
                'category' => $def['category'] ?? null,
                'is_checked' => $def['is_checked'] ?? false,
                'checked_by' => $def['checked_by'] ?? null,
                'checked_at' => $def['checked_at'] ?? null,
                'source' => $isRecurring ? ShoppingItemSource::Staple : ShoppingItemSource::Manual,
                'is_recurring' => $isRecurring,
                'default_quantity' => $isRecurring ? ($def['quantity'] ?? null) : null,
                'sort_order' => $idx,
            ]);
        }

        // ─────────────────────────────────────────────
        //  SECONDARY LIST: TARGET RUN
        // ─────────────────────────────────────────────

        $target = ShoppingList::create([
            'family_id' => $family->id,
            'created_by' => $mike->id,
            'name' => 'Target Run',
            'store_name' => 'Target',
            'is_active' => false,
        ]);

        $targetItems = [
            ['name' => 'Paper towels',      'quantity' => '6 pack',     'category' => 'household', 'is_recurring' => true],
            ['name' => 'Dish soap',         'quantity' => '1',          'category' => 'household'],
            ['name' => 'Trash bags',        'quantity' => '1 box',      'category' => 'household'],
            ['name' => 'Toothpaste',        'quantity' => '2 tubes',    'category' => 'personal-care'],
            ['name' => 'Shampoo',           'quantity' => '1',          'category' => 'personal-care'],
            ['name' => 'Lily\'s school glue', 'quantity' => '2 bottles',  'category' => 'school'],
            ['name' => 'Birthday card',     'quantity' => '1',          'category' => 'misc'],
        ];

        foreach ($targetItems as $idx => $def) {
            $isRecurring = $def['is_recurring'] ?? false;
            ShoppingItem::create([
                'shopping_list_id' => $target->id,
                'family_id' => $family->id,
                'added_by' => $mike->id,
                'name' => $def['name'],
                'quantity' => $def['quantity'] ?? null,
                'category' => $def['category'] ?? null,
                'is_checked' => false,
                'source' => $isRecurring ? ShoppingItemSource::Staple : ShoppingItemSource::Manual,
                'is_recurring' => $isRecurring,
                'default_quantity' => $isRecurring ? ($def['quantity'] ?? null) : null,
                'sort_order' => $idx,
            ]);
        }
    }
}
