<?php

namespace Database\Seeders;

use App\Enums\RecipeSourceType;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class DemoRecipeSeeder extends Seeder
{
    use DemoFamilyContext;

    public function run(): void
    {
        $this->loadDemoContext();

        $family = $this->family();
        $sarah = $this->sarah;
        $mike = $this->mike;

        $tagByName = Tag::where('family_id', $family->id)
            ->where('scope', 'food')
            ->get()
            ->keyBy('name');

        $tagId = fn (string $name) => $tagByName->get($name)?->id;

        // ─────────────────────────────────────────────
        //  RECIPES
        // ─────────────────────────────────────────────

        // Public Unsplash photo URLs — Phase 1 demo images so the photo card
        // path can be visually verified without a storage upload flow. Real
        // recipes will use storage paths via the import / manual flows.
        $recipes = [
            [
                'title' => 'Spaghetti Bolognese',
                'description' => 'A classic family weeknight dinner — rich beef sauce simmered low and slow, tossed with pasta. Easy to double for leftovers.',
                'created_by' => $sarah->id,
                'servings' => 4,
                'prep_time_minutes' => 15,
                'cook_time_minutes' => 45,
                'total_time_minutes' => 60,
                'is_favorite' => true,
                'sort_order' => 0,
                'image_path' => 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=800&q=80',
                'instructions' => [
                    'Heat olive oil in a large pot over medium heat. Add diced onion, carrot, and celery. Cook 8 minutes until soft.',
                    'Add minced garlic; cook 1 minute until fragrant.',
                    'Add ground beef. Brown 6–8 minutes, breaking it apart with a wooden spoon. Drain excess fat.',
                    'Pour in red wine (if using); simmer 2 minutes.',
                    'Stir in crushed tomatoes, tomato paste, oregano, salt, and pepper. Reduce heat to low; simmer 30 minutes, stirring occasionally.',
                    'Meanwhile, cook spaghetti to al dente per package directions. Reserve 1/2 cup pasta water.',
                    'Toss pasta with sauce, adding a splash of pasta water if needed. Top with grated Parmesan and fresh basil.',
                ],
                'tags' => ['Dinner'],
                'ingredients' => [
                    ['name' => 'Olive oil',         'quantity' => 2,    'unit' => 'tbsp'],
                    ['name' => 'Yellow onion',      'quantity' => 1,    'unit' => null,    'preparation' => 'diced'],
                    ['name' => 'Carrot',            'quantity' => 1,    'unit' => null,    'preparation' => 'finely diced'],
                    ['name' => 'Celery stalk',      'quantity' => 1,    'unit' => null,    'preparation' => 'finely diced'],
                    ['name' => 'Garlic',            'quantity' => 3,    'unit' => 'cloves', 'preparation' => 'minced'],
                    ['name' => 'Ground beef',       'quantity' => 1,    'unit' => 'lb'],
                    ['name' => 'Red wine',          'quantity' => 0.5,  'unit' => 'cup',   'is_optional' => true],
                    ['name' => 'Crushed tomatoes',  'quantity' => 28,   'unit' => 'oz'],
                    ['name' => 'Tomato paste',      'quantity' => 2,    'unit' => 'tbsp'],
                    ['name' => 'Dried oregano',     'quantity' => 1,    'unit' => 'tsp'],
                    ['name' => 'Salt',              'quantity' => 1,    'unit' => 'tsp'],
                    ['name' => 'Black pepper',      'quantity' => 0.5,  'unit' => 'tsp'],
                    ['name' => 'Spaghetti',         'quantity' => 1,    'unit' => 'lb'],
                    ['name' => 'Parmesan',          'quantity' => 0.5,  'unit' => 'cup',   'preparation' => 'grated'],
                    ['name' => 'Fresh basil',       'quantity' => null, 'unit' => null,    'preparation' => 'for garnish', 'is_optional' => true],
                ],
            ],
            [
                'title' => 'Chicken Stir-Fry',
                'description' => 'Fast, weeknight-friendly stir-fry that comes together in 20 minutes. Use whatever vegetables you have on hand.',
                'created_by' => $mike->id,
                'servings' => 4,
                'prep_time_minutes' => 15,
                'cook_time_minutes' => 12,
                'total_time_minutes' => 27,
                'is_favorite' => true,
                'sort_order' => 1,
                'instructions' => [
                    'Whisk soy sauce, oyster sauce, honey, sesame oil, and cornstarch in a small bowl. Set aside.',
                    'Heat 1 tbsp vegetable oil in a wok or large skillet over high heat. Add chicken; stir-fry 4–5 minutes until golden. Transfer to a plate.',
                    'Add remaining oil, then garlic and ginger. Stir 20 seconds.',
                    'Add bell pepper, broccoli, and snap peas. Stir-fry 3–4 minutes until crisp-tender.',
                    'Return chicken to wok. Pour in sauce; toss until thickened, about 1 minute.',
                    'Serve over steamed jasmine rice. Garnish with scallions and sesame seeds.',
                ],
                'tags' => ['Dinner'],
                'ingredients' => [
                    ['name' => 'Chicken breast',    'quantity' => 1.5, 'unit' => 'lb',    'preparation' => 'sliced thin'],
                    ['name' => 'Soy sauce',         'quantity' => 0.25, 'unit' => 'cup'],
                    ['name' => 'Oyster sauce',      'quantity' => 2,   'unit' => 'tbsp'],
                    ['name' => 'Honey',             'quantity' => 1,   'unit' => 'tbsp'],
                    ['name' => 'Sesame oil',        'quantity' => 1,   'unit' => 'tsp'],
                    ['name' => 'Cornstarch',        'quantity' => 1,   'unit' => 'tbsp'],
                    ['name' => 'Vegetable oil',     'quantity' => 2,   'unit' => 'tbsp'],
                    ['name' => 'Garlic',            'quantity' => 3,   'unit' => 'cloves', 'preparation' => 'minced'],
                    ['name' => 'Fresh ginger',      'quantity' => 1,   'unit' => 'tbsp',  'preparation' => 'grated'],
                    ['name' => 'Bell pepper',       'quantity' => 1,   'unit' => null,    'preparation' => 'sliced'],
                    ['name' => 'Broccoli florets',  'quantity' => 2,   'unit' => 'cups'],
                    ['name' => 'Snap peas',         'quantity' => 1,   'unit' => 'cup'],
                    ['name' => 'Jasmine rice',      'quantity' => 2,   'unit' => 'cups',  'preparation' => 'cooked'],
                    ['name' => 'Scallions',         'quantity' => 2,   'unit' => null,    'preparation' => 'sliced'],
                    ['name' => 'Sesame seeds',      'quantity' => 1,   'unit' => 'tsp',   'is_optional' => true],
                ],
            ],
            [
                'title' => 'Sheet Pan Salmon',
                'description' => 'Easy sheet-pan dinner with lemony salmon and roasted veggies. Cleanup is a breeze.',
                'created_by' => $sarah->id,
                'servings' => 4,
                'prep_time_minutes' => 10,
                'cook_time_minutes' => 18,
                'total_time_minutes' => 28,
                'sort_order' => 2,
                'image_path' => 'https://images.unsplash.com/photo-1485921325833-c519f76c4927?w=800&q=80',
                'instructions' => [
                    'Preheat oven to 425°F. Line a large sheet pan with parchment.',
                    'Toss asparagus and cherry tomatoes with 1 tbsp olive oil, salt, and pepper. Spread on one half of the pan.',
                    'Place salmon fillets on the other half. Drizzle with remaining olive oil; top with lemon slices, garlic, and dill.',
                    'Roast 14–18 minutes until salmon flakes easily and vegetables are tender.',
                    'Squeeze fresh lemon over everything before serving.',
                ],
                'tags' => ['Dinner'],
                'ingredients' => [
                    ['name' => 'Salmon fillets',    'quantity' => 4,    'unit' => null,   'preparation' => '6 oz each'],
                    ['name' => 'Asparagus',         'quantity' => 1,    'unit' => 'lb',   'preparation' => 'trimmed'],
                    ['name' => 'Cherry tomatoes',   'quantity' => 1,    'unit' => 'pint'],
                    ['name' => 'Olive oil',         'quantity' => 3,    'unit' => 'tbsp'],
                    ['name' => 'Lemon',             'quantity' => 1,    'unit' => null,   'preparation' => 'sliced'],
                    ['name' => 'Garlic',            'quantity' => 2,    'unit' => 'cloves', 'preparation' => 'minced'],
                    ['name' => 'Fresh dill',        'quantity' => 2,    'unit' => 'tbsp'],
                    ['name' => 'Salt',              'quantity' => 1,    'unit' => 'tsp'],
                    ['name' => 'Black pepper',      'quantity' => 0.5,  'unit' => 'tsp'],
                ],
            ],
            [
                'title' => 'Saturday Pancakes',
                'description' => 'Mike\'s weekend pancake tradition. Fluffy, golden, and perfect with maple syrup and a pile of berries.',
                'created_by' => $mike->id,
                'servings' => 4,
                'prep_time_minutes' => 5,
                'cook_time_minutes' => 15,
                'total_time_minutes' => 20,
                'is_favorite' => true,
                'sort_order' => 3,
                'instructions' => [
                    'Whisk flour, sugar, baking powder, and salt in a large bowl.',
                    'In a separate bowl, whisk milk, eggs, melted butter, and vanilla.',
                    'Pour wet into dry; stir just until combined (lumps are fine — overmixing makes tough pancakes).',
                    'Heat a non-stick skillet or griddle over medium-low. Lightly butter the surface.',
                    'Pour 1/4 cup batter per pancake. Cook 2 minutes until bubbles form on top, then flip; cook 1–2 minutes more until golden.',
                    'Stack and serve with butter, maple syrup, and fresh berries.',
                ],
                'tags' => ['Breakfast'],
                'ingredients' => [
                    ['name' => 'All-purpose flour',  'quantity' => 1.5, 'unit' => 'cups'],
                    ['name' => 'Sugar',              'quantity' => 2,   'unit' => 'tbsp'],
                    ['name' => 'Baking powder',      'quantity' => 1,   'unit' => 'tbsp'],
                    ['name' => 'Salt',               'quantity' => 0.5, 'unit' => 'tsp'],
                    ['name' => 'Milk',               'quantity' => 1.25, 'unit' => 'cups'],
                    ['name' => 'Eggs',               'quantity' => 2,   'unit' => null,   'preparation' => 'large'],
                    ['name' => 'Butter',             'quantity' => 3,   'unit' => 'tbsp', 'preparation' => 'melted'],
                    ['name' => 'Vanilla extract',    'quantity' => 1,   'unit' => 'tsp'],
                    ['name' => 'Maple syrup',        'quantity' => null, 'unit' => null,   'preparation' => 'for serving'],
                    ['name' => 'Fresh berries',      'quantity' => null, 'unit' => null,   'preparation' => 'for serving', 'is_optional' => true],
                ],
            ],
            [
                'title' => 'Chocolate Chip Cookies',
                'description' => 'Crisp edges, chewy centers. The Johnsons\' go-to weekend baking project — Lily measures, Jake stirs, Emma judges.',
                'created_by' => $sarah->id,
                'servings' => 24,
                'prep_time_minutes' => 15,
                'cook_time_minutes' => 12,
                'total_time_minutes' => 27,
                'is_favorite' => true,
                'sort_order' => 4,
                'image_path' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=800&q=80',
                'instructions' => [
                    'Preheat oven to 375°F. Line two baking sheets with parchment.',
                    'Whisk flour, baking soda, and salt in a medium bowl.',
                    'In a stand mixer, cream butter, brown sugar, and granulated sugar on medium 3 minutes until light.',
                    'Add eggs one at a time, then vanilla. Mix until combined.',
                    'Reduce mixer to low. Add dry ingredients in two batches.',
                    'Fold in chocolate chips with a spatula.',
                    'Scoop 2-tbsp balls onto sheets, 2 inches apart. Bake 10–12 minutes until edges are golden but centers look slightly underdone.',
                    'Cool on the pan 5 minutes, then transfer to a rack.',
                ],
                'tags' => ['Dessert', 'Snack'],
                'ingredients' => [
                    ['name' => 'All-purpose flour',  'quantity' => 2.25, 'unit' => 'cups'],
                    ['name' => 'Baking soda',        'quantity' => 1,    'unit' => 'tsp'],
                    ['name' => 'Salt',               'quantity' => 1,    'unit' => 'tsp'],
                    ['name' => 'Butter',             'quantity' => 1,    'unit' => 'cup',  'preparation' => 'softened'],
                    ['name' => 'Brown sugar',        'quantity' => 0.75, 'unit' => 'cup',  'preparation' => 'packed'],
                    ['name' => 'Granulated sugar',   'quantity' => 0.75, 'unit' => 'cup'],
                    ['name' => 'Eggs',               'quantity' => 2,    'unit' => null,   'preparation' => 'large'],
                    ['name' => 'Vanilla extract',    'quantity' => 1,    'unit' => 'tsp'],
                    ['name' => 'Chocolate chips',    'quantity' => 2,    'unit' => 'cups'],
                ],
            ],
        ];

        foreach ($recipes as $def) {
            $recipe = Recipe::create([
                'family_id' => $family->id,
                'created_by' => $def['created_by'],
                'title' => $def['title'],
                'description' => $def['description'],
                'servings' => $def['servings'],
                'prep_time_minutes' => $def['prep_time_minutes'],
                'cook_time_minutes' => $def['cook_time_minutes'],
                'total_time_minutes' => $def['total_time_minutes'],
                'instructions' => $def['instructions'],
                'image_path' => $def['image_path'] ?? null,
                'is_favorite' => $def['is_favorite'] ?? false,
                'sort_order' => $def['sort_order'],
                'source_type' => RecipeSourceType::Manual,
            ]);

            foreach ($def['ingredients'] as $idx => $ing) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'name' => $ing['name'],
                    'quantity' => $ing['quantity'] ?? null,
                    'unit' => $ing['unit'] ?? null,
                    'preparation' => $ing['preparation'] ?? null,
                    'group_name' => $ing['group_name'] ?? null,
                    'is_optional' => $ing['is_optional'] ?? false,
                    'sort_order' => $idx,
                ]);
            }

            $tagIds = collect($def['tags'] ?? [])
                ->map(fn ($t) => $tagId($t))
                ->filter()
                ->all();

            if (! empty($tagIds)) {
                $recipe->tags()->syncWithoutDetaching($tagIds);
            }
        }
    }
}
