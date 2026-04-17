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
        //  SYSTEM PRESETS (seeded for every family)
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
        //  DEMO RESTAURANTS
        // ─────────────────────────────────────────────

        $pizzeria = Restaurant::create([
            'name' => 'Gusto Pizzeria',
            'address' => '123 Elm Street',
            'phone' => '555-0101',
        ]);
        FamilyRestaurant::create([
            'family_id' => $family->id,
            'restaurant_id' => $pizzeria->id,
            'is_favorite' => true,
        ]);
        $pizzeria->tags()->syncWithoutDetaching([$this->ensureFoodTag($family->id, 'Italian')->id]);

        $tacos = Restaurant::create([
            'name' => 'Taco Fiesta',
            'address' => '456 Oak Avenue',
            'phone' => '555-0202',
        ]);
        FamilyRestaurant::create([
            'family_id' => $family->id,
            'restaurant_id' => $tacos->id,
        ]);
        $tacos->tags()->syncWithoutDetaching([$this->ensureFoodTag($family->id, 'Mexican')->id]);

        // ─────────────────────────────────────────────
        //  LOOK UP DEMO RECIPES (created by DemoRecipeSeeder if it exists)
        // ─────────────────────────────────────────────

        $recipes = Recipe::where('family_id', $family->id)->limit(5)->get();

        // ─────────────────────────────────────────────
        //  CURRENT WEEK MEAL PLAN
        // ─────────────────────────────────────────────

        $monday = $now->copy()->startOfWeek($family->getWeekStartDay())->toDateString();

        $plan = MealPlan::create([
            'family_id' => $family->id,
            'created_by' => $this->sarah->id,
            'week_start' => $monday,
        ]);

        // Build a 6-day plan (Mon–Sat). One entry per meal slot per day.
        $days = [
            0 => Carbon::parse($monday),
            1 => Carbon::parse($monday)->addDay(),
            2 => Carbon::parse($monday)->addDays(2),
            3 => Carbon::parse($monday)->addDays(3),
            4 => Carbon::parse($monday)->addDays(4),
            5 => Carbon::parse($monday)->addDays(5),
        ];

        // Helper to pick a recipe id (null-safe) by index
        $recipeId = fn (int $i) => $recipes->count() > $i ? $recipes[$i]->id : null;

        $entries = [
            // Monday
            ['date' => $days[0], 'slot' => MealSlot::Breakfast, 'custom_title' => 'Cereal & Fruit'],
            ['date' => $days[0], 'slot' => MealSlot::Lunch,     'preset' => $presets['Fend for Yourself']],
            ['date' => $days[0], 'slot' => MealSlot::Dinner,
                'recipe_id' => $recipeId(0),
                'custom_title' => $recipeId(0) ? null : 'Spaghetti Bolognese',
                'assigned_cooks' => [$this->sarah->id],
            ],

            // Tuesday
            ['date' => $days[1], 'slot' => MealSlot::Breakfast, 'custom_title' => 'Avocado Toast'],
            ['date' => $days[1], 'slot' => MealSlot::Lunch,     'custom_title' => 'Peanut Butter Sandwiches'],
            ['date' => $days[1], 'slot' => MealSlot::Dinner,
                'recipe_id' => $recipeId(1),
                'custom_title' => $recipeId(1) ? null : 'Chicken Stir-Fry',
                'assigned_cooks' => [$this->mike->id],
                'notes' => 'Use the wok',
            ],

            // Wednesday
            ['date' => $days[2], 'slot' => MealSlot::Breakfast, 'custom_title' => 'Overnight Oats'],
            ['date' => $days[2], 'slot' => MealSlot::Lunch,     'preset' => $presets['Leftovers']],
            ['date' => $days[2], 'slot' => MealSlot::Dinner,    'restaurant_id' => $tacos->id, 'notes' => 'Family taco night!'],

            // Thursday
            ['date' => $days[3], 'slot' => MealSlot::Breakfast, 'custom_title' => 'Scrambled Eggs & Toast'],
            ['date' => $days[3], 'slot' => MealSlot::Lunch,     'custom_title' => 'Turkey Wraps'],
            ['date' => $days[3], 'slot' => MealSlot::Dinner,
                'recipe_id' => $recipeId(2),
                'custom_title' => $recipeId(2) ? null : 'Sheet Pan Salmon',
                'assigned_cooks' => [$this->sarah->id],
            ],

            // Friday
            ['date' => $days[4], 'slot' => MealSlot::Breakfast, 'preset' => $presets['Fend for Yourself']],
            ['date' => $days[4], 'slot' => MealSlot::Lunch,     'preset' => $presets['Leftovers']],
            ['date' => $days[4], 'slot' => MealSlot::Dinner,    'restaurant_id' => $pizzeria->id, 'notes' => 'Friday pizza night!'],

            // Saturday
            ['date' => $days[5], 'slot' => MealSlot::Breakfast,
                'recipe_id' => $recipeId(3),
                'custom_title' => $recipeId(3) ? null : 'Pancakes',
                'assigned_cooks' => [$this->mike->id],
                'servings' => 4,
            ],
            ['date' => $days[5], 'slot' => MealSlot::Lunch,     'custom_title' => 'BLT Sandwiches'],
            ['date' => $days[5], 'slot' => MealSlot::Dinner,    'preset' => $presets['Eating Out'], 'notes' => "Let's decide together"],
        ];

        foreach ($entries as $i => $def) {
            $entryData = [
                'meal_plan_id' => $plan->id,
                'date' => $def['date']->toDateString(),
                'meal_slot' => $def['slot']->value,
                'sort_order' => $i,
                'notes' => $def['notes'] ?? null,
                'servings' => $def['servings'] ?? null,
                'assigned_cooks' => $def['assigned_cooks'] ?? null,
                'recipe_id' => null,
                'restaurant_id' => null,
                'meal_preset_id' => null,
                'custom_title' => null,
            ];

            // Determine the single source
            if (! empty($def['recipe_id'])) {
                $entryData['recipe_id'] = $def['recipe_id'];
            } elseif (isset($def['restaurant_id'])) {
                $entryData['restaurant_id'] = $def['restaurant_id'];
            } elseif (isset($def['preset'])) {
                $entryData['meal_preset_id'] = $def['preset']->id;
            } else {
                $entryData['custom_title'] = $def['custom_title'];
            }

            MealPlanEntry::create($entryData);
        }
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
