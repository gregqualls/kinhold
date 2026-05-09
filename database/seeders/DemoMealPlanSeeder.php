<?php

namespace Database\Seeders;

use App\Enums\MealSlot;
use App\Enums\TagScope;
use App\Models\FamilyRestaurant;
use App\Models\MealPlan;
use App\Models\MealPlanEntry;
use App\Models\MealPreset;
use App\Models\Recipe;
use App\Models\Restaurant;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoMealPlanSeeder extends Seeder
{
    use DemoFamilyContext;

    public function run(): void
    {
        $this->loadDemoContext();

        $family = $this->family();
        $now = Carbon::now();

        // ─────────────────────────────────────────────
        //  SYSTEM PRESETS
        // ─────────────────────────────────────────────

        $systemPresets = [
            ['label' => 'Fend for Yourself', 'icon' => 'utensils-crossed', 'sort_order' => 0, 'is_system' => true],
            ['label' => 'Eating Out',         'icon' => 'store',            'sort_order' => 1, 'is_system' => true],
            ['label' => 'Leftovers',          'icon' => 'package',          'sort_order' => 2, 'is_system' => true],
            ['label' => 'Takeaway',           'icon' => 'truck',            'sort_order' => 3, 'is_system' => true],
        ];

        $presets = [];
        foreach ($systemPresets as $def) {
            $preset = MealPreset::create(array_merge($def, ['family_id' => $family->id]));
            $presets[$def['label']] = $preset;
        }

        // ─────────────────────────────────────────────
        //  RESTAURANTS
        // ─────────────────────────────────────────────

        $pizzeria = Restaurant::create([
            'name' => 'Gusto Pizzeria',
            'address' => '123 Elm Street',
            'phone' => '555-0101',
            'image_url' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800&q=80',
        ]);
        FamilyRestaurant::create(['family_id' => $family->id, 'restaurant_id' => $pizzeria->id, 'is_favorite' => true]);
        $pizzeria->tags()->syncWithoutDetaching([$this->ensureFoodTag($family->id, 'Italian')->id]);

        $tacos = Restaurant::create([
            'name' => 'Taco Fiesta',
            'address' => '456 Oak Avenue',
            'phone' => '555-0202',
        ]);
        FamilyRestaurant::create(['family_id' => $family->id, 'restaurant_id' => $tacos->id]);
        $tacos->tags()->syncWithoutDetaching([$this->ensureFoodTag($family->id, 'Mexican')->id]);

        $pho = Restaurant::create([
            'name' => 'Pho Palace',
            'address' => '789 Maple Drive',
            'phone' => '555-0303',
            'image_url' => 'https://images.unsplash.com/photo-1555126634-323283e090fa?w=800&q=80',
        ]);
        FamilyRestaurant::create(['family_id' => $family->id, 'restaurant_id' => $pho->id, 'is_favorite' => true]);
        $pho->tags()->syncWithoutDetaching([$this->ensureFoodTag($family->id, 'Vietnamese')->id]);

        $sushi = Restaurant::create([
            'name' => 'Sakura Sushi',
            'address' => '321 Birch Boulevard',
            'phone' => '555-0404',
        ]);
        FamilyRestaurant::create(['family_id' => $family->id, 'restaurant_id' => $sushi->id]);
        $sushi->tags()->syncWithoutDetaching([$this->ensureFoodTag($family->id, 'Japanese')->id]);

        $burgers = Restaurant::create([
            'name' => 'Five Guys',
            'address' => '654 Pine Road',
            'phone' => '555-0505',
        ]);
        FamilyRestaurant::create(['family_id' => $family->id, 'restaurant_id' => $burgers->id]);
        $burgers->tags()->syncWithoutDetaching([$this->ensureFoodTag($family->id, 'American')->id]);

        // ─────────────────────────────────────────────
        //  LOOK UP DEMO RECIPES BY TITLE
        // ─────────────────────────────────────────────

        $recipesByTitle = Recipe::where('family_id', $family->id)
            ->get()
            ->keyBy('title');

        $recipe = fn (string $title) => $recipesByTitle->get($title)?->id;

        // ─────────────────────────────────────────────
        //  WEEK 1 MEAL PLAN
        // ─────────────────────────────────────────────

        $monday1 = $now->copy()->startOfWeek($family->getWeekStartDay())->toDateString();

        $plan1 = MealPlan::create([
            'family_id' => $family->id,
            'created_by' => $this->sarah->id,
            'week_start' => $monday1,
        ]);

        $days1 = collect(range(0, 6))->map(fn ($i) => Carbon::parse($monday1)->addDays($i));

        $week1Entries = [
            // Monday
            ['date' => $days1[0], 'slot' => MealSlot::Breakfast, 'custom_title' => 'Cereal & Fruit'],
            ['date' => $days1[0], 'slot' => MealSlot::Lunch,     'preset' => $presets['Fend for Yourself']],
            ['date' => $days1[0], 'slot' => MealSlot::Dinner,    'recipe_id' => $recipe('Spaghetti Bolognese'), 'custom_title' => 'Spaghetti Bolognese', 'assigned_cooks' => [$this->sarah->id]],

            // Tuesday
            ['date' => $days1[1], 'slot' => MealSlot::Breakfast, 'recipe_id' => $recipe('Avocado Toast with Poached Eggs'), 'custom_title' => 'Avocado Toast'],
            ['date' => $days1[1], 'slot' => MealSlot::Lunch,     'custom_title' => 'Peanut Butter Sandwiches'],
            ['date' => $days1[1], 'slot' => MealSlot::Dinner,    'recipe_id' => $recipe('Beef Tacos'), 'custom_title' => 'Taco Tuesday', 'assigned_cooks' => [$this->sarah->id, $this->mike->id]],

            // Wednesday
            ['date' => $days1[2], 'slot' => MealSlot::Breakfast, 'custom_title' => 'Overnight Oats'],
            ['date' => $days1[2], 'slot' => MealSlot::Lunch,     'preset' => $presets['Leftovers']],
            ['date' => $days1[2], 'slot' => MealSlot::Dinner,    'recipe_id' => $recipe('Chicken Stir-Fry'), 'custom_title' => 'Chicken Stir-Fry', 'assigned_cooks' => [$this->mike->id], 'notes' => 'Use the wok'],

            // Thursday
            ['date' => $days1[3], 'slot' => MealSlot::Breakfast, 'custom_title' => 'Scrambled Eggs & Toast'],
            ['date' => $days1[3], 'slot' => MealSlot::Lunch,     'custom_title' => 'Turkey Wraps'],
            ['date' => $days1[3], 'slot' => MealSlot::Dinner,    'recipe_id' => $recipe('Sheet Pan Salmon'), 'custom_title' => 'Sheet Pan Salmon', 'assigned_cooks' => [$this->sarah->id]],

            // Friday
            ['date' => $days1[4], 'slot' => MealSlot::Breakfast, 'preset' => $presets['Fend for Yourself']],
            ['date' => $days1[4], 'slot' => MealSlot::Lunch,     'preset' => $presets['Leftovers']],
            ['date' => $days1[4], 'slot' => MealSlot::Dinner,    'restaurant_id' => $pizzeria->id, 'notes' => 'Friday pizza night!'],

            // Saturday
            ['date' => $days1[5], 'slot' => MealSlot::Breakfast, 'recipe_id' => $recipe('Saturday Pancakes'), 'custom_title' => 'Saturday Pancakes', 'assigned_cooks' => [$this->mike->id], 'servings' => 5],
            ['date' => $days1[5], 'slot' => MealSlot::Lunch,     'custom_title' => 'BLT Sandwiches'],
            ['date' => $days1[5], 'slot' => MealSlot::Dinner,    'recipe_id' => $recipe('Homemade Pizza Night'), 'custom_title' => 'DIY Pizza Night', 'notes' => 'Everyone picks their toppings'],

            // Sunday
            ['date' => $days1[6], 'slot' => MealSlot::Breakfast, 'recipe_id' => $recipe('Blueberry Muffins'), 'custom_title' => 'Blueberry Muffins'],
            ['date' => $days1[6], 'slot' => MealSlot::Lunch,     'restaurant_id' => $pho->id, 'notes' => 'Sunday pho run'],
            ['date' => $days1[6], 'slot' => MealSlot::Dinner,    'recipe_id' => $recipe('Nigerian Jollof Rice'), 'custom_title' => 'Jollof Rice', 'assigned_cooks' => [$this->mike->id], 'notes' => "Adaeze's recipe — start the tomato base early"],
        ];

        foreach ($week1Entries as $i => $def) {
            $this->createEntry($plan1->id, $def, $i, $presets);
        }

        // ─────────────────────────────────────────────
        //  WEEK 2 MEAL PLAN
        // ─────────────────────────────────────────────

        $monday2 = Carbon::parse($monday1)->addWeek()->toDateString();

        $plan2 = MealPlan::create([
            'family_id' => $family->id,
            'created_by' => $this->mike->id,
            'week_start' => $monday2,
        ]);

        $days2 = collect(range(0, 6))->map(fn ($i) => Carbon::parse($monday2)->addDays($i));

        $week2Entries = [
            // Monday
            ['date' => $days2[0], 'slot' => MealSlot::Breakfast, 'custom_title' => 'Yogurt & Granola'],
            ['date' => $days2[0], 'slot' => MealSlot::Lunch,     'recipe_id' => $recipe('Miso Soup'), 'custom_title' => 'Miso Soup', 'notes' => 'Kenji makes this one'],
            ['date' => $days2[0], 'slot' => MealSlot::Dinner,    'recipe_id' => $recipe('Turkey & White Bean Chili'), 'custom_title' => 'Turkey Chili', 'assigned_cooks' => [$this->sarah->id], 'servings' => 6],

            // Tuesday
            ['date' => $days2[1], 'slot' => MealSlot::Breakfast, 'recipe_id' => $recipe('Banana Bread'), 'custom_title' => 'Banana Bread'],
            ['date' => $days2[1], 'slot' => MealSlot::Lunch,     'preset' => $presets['Leftovers'], 'notes' => 'Chili leftovers'],
            ['date' => $days2[1], 'slot' => MealSlot::Dinner,    'restaurant_id' => $tacos->id, 'notes' => 'Taco Tuesday out this week'],

            // Wednesday
            ['date' => $days2[2], 'slot' => MealSlot::Breakfast, 'custom_title' => 'Cereal & Orange Juice'],
            ['date' => $days2[2], 'slot' => MealSlot::Lunch,     'recipe_id' => $recipe('Caesar Salad'), 'custom_title' => 'Caesar Salad + grilled chicken'],
            ['date' => $days2[2], 'slot' => MealSlot::Dinner,    'recipe_id' => $recipe('Fried Rice'), 'custom_title' => 'Fried Rice', 'assigned_cooks' => [$this->mike->id], 'notes' => 'Use leftover rice from Sunday'],

            // Thursday
            ['date' => $days2[3], 'slot' => MealSlot::Breakfast, 'recipe_id' => $recipe('Avocado Toast with Poached Eggs'), 'custom_title' => 'Avocado Toast'],
            ['date' => $days2[3], 'slot' => MealSlot::Lunch,     'custom_title' => 'Ham & Cheese Panini'],
            ['date' => $days2[3], 'slot' => MealSlot::Dinner,    'recipe_id' => $recipe('Teriyaki Salmon Bowl'), 'custom_title' => 'Teriyaki Salmon Bowl', 'assigned_cooks' => [$this->sarah->id]],

            // Friday
            ['date' => $days2[4], 'slot' => MealSlot::Breakfast, 'preset' => $presets['Fend for Yourself']],
            ['date' => $days2[4], 'slot' => MealSlot::Lunch,     'preset' => $presets['Leftovers']],
            ['date' => $days2[4], 'slot' => MealSlot::Dinner,    'restaurant_id' => $sushi->id, 'notes' => 'Special Friday treat — Kenji picked it'],

            // Saturday
            ['date' => $days2[5], 'slot' => MealSlot::Breakfast, 'recipe_id' => $recipe('Saturday Pancakes'), 'custom_title' => 'Saturday Pancakes', 'assigned_cooks' => [$this->mike->id]],
            ['date' => $days2[5], 'slot' => MealSlot::Lunch,     'restaurant_id' => $burgers->id, 'notes' => 'Post-soccer treat for Kenji'],
            ['date' => $days2[5], 'slot' => MealSlot::Dinner,    'recipe_id' => $recipe('Beef Tacos'), 'custom_title' => 'Taco Night', 'assigned_cooks' => [$this->sarah->id]],

            // Sunday
            ['date' => $days2[6], 'slot' => MealSlot::Breakfast, 'recipe_id' => $recipe('Blueberry Muffins'), 'custom_title' => 'Blueberry Muffins', 'notes' => 'Naia bakes these'],
            ['date' => $days2[6], 'slot' => MealSlot::Lunch,     'preset' => $presets['Eating Out'], 'notes' => 'Family vote — wherever wins!'],
            ['date' => $days2[6], 'slot' => MealSlot::Dinner,    'recipe_id' => $recipe('Spaghetti Bolognese'), 'custom_title' => 'Bolognese Night', 'assigned_cooks' => [$this->sarah->id], 'notes' => 'Double batch for Monday leftovers'],
        ];

        foreach ($week2Entries as $i => $def) {
            $this->createEntry($plan2->id, $def, $i, $presets);
        }
    }

    private function createEntry(string $planId, array $def, int $sortOrder, array $presets): void
    {
        $entryData = [
            'meal_plan_id' => $planId,
            'date' => $def['date']->toDateString(),
            'meal_slot' => $def['slot']->value,
            'sort_order' => $sortOrder,
            'notes' => $def['notes'] ?? null,
            'servings' => $def['servings'] ?? null,
            'assigned_cooks' => $def['assigned_cooks'] ?? null,
            'recipe_id' => null,
            'restaurant_id' => null,
            'meal_preset_id' => null,
            'custom_title' => null,
        ];

        if (! empty($def['recipe_id'])) {
            $entryData['recipe_id'] = $def['recipe_id'];
        } elseif (isset($def['restaurant_id'])) {
            $entryData['restaurant_id'] = $def['restaurant_id'];
        } elseif (isset($def['preset'])) {
            $entryData['meal_preset_id'] = $def['preset']->id;
        } else {
            $entryData['custom_title'] = $def['custom_title'];
        }

        // If recipe_id is set but the recipe doesn't exist in DB, fall back to custom_title
        if (empty($entryData['recipe_id']) && empty($entryData['restaurant_id']) && empty($entryData['meal_preset_id'])) {
            $entryData['custom_title'] = $def['custom_title'] ?? null;
        }

        MealPlanEntry::create($entryData);
    }

    private function ensureFoodTag(string $familyId, string $name): Tag
    {
        return Tag::firstOrCreate(
            [
                'family_id' => $familyId,
                'name' => $name,
                'scope' => TagScope::Food->value,
            ],
            [
                'sort_order' => (Tag::where('family_id', $familyId)->max('sort_order') ?? 0) + 1,
            ]
        );
    }
}
