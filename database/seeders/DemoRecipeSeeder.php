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

        $recipes = [
            // ── Dinners ──────────────────────────────────────────────────────

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
                'title' => 'Nigerian Jollof Rice',
                'description' => "Adaeze's family recipe — smoky, tomato-rich one-pot rice that everyone fights over. The secret is the tomato base and letting it catch just a little on the bottom.",
                'created_by' => $mike->id,
                'servings' => 6,
                'prep_time_minutes' => 20,
                'cook_time_minutes' => 50,
                'total_time_minutes' => 70,
                'is_favorite' => true,
                'sort_order' => 3,
                'image_path' => 'https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=800&q=80',
                'instructions' => [
                    'Blend tomatoes, red bell pepper, and habanero (if using) into a smooth purée. Set aside.',
                    'Heat vegetable oil in a large heavy-bottomed pot over medium-high heat. Add sliced onions; fry until golden, about 8 minutes.',
                    'Pour in the tomato purée. Add tomato paste, curry powder, thyme, bay leaves, and bouillon. Stir well.',
                    'Cook the tomato base, stirring often, for 15–20 minutes until it darkens and the oil floats to the top.',
                    'Wash rice until water runs clear. Add rice to the pot; stir to coat every grain.',
                    'Pour in chicken stock — it should just cover the rice. Season with salt. Bring to a boil, then reduce heat to low.',
                    'Cover tightly and cook 25–30 minutes. Lift the lid only once halfway through to stir from the bottom.',
                    'The rice is done when liquid is absorbed. Let the bottom develop a slight crust (the party rice!) before serving.',
                ],
                'tags' => ['Dinner'],
                'ingredients' => [
                    ['name' => 'Long-grain parboiled rice', 'quantity' => 3,   'unit' => 'cups'],
                    ['name' => 'Plum tomatoes',             'quantity' => 6,   'unit' => null],
                    ['name' => 'Red bell pepper',           'quantity' => 2,   'unit' => null],
                    ['name' => 'Habanero pepper',           'quantity' => 1,   'unit' => null,    'is_optional' => true],
                    ['name' => 'Vegetable oil',             'quantity' => 0.25, 'unit' => 'cup'],
                    ['name' => 'Yellow onion',              'quantity' => 2,   'unit' => null,    'preparation' => 'sliced'],
                    ['name' => 'Tomato paste',              'quantity' => 3,   'unit' => 'tbsp'],
                    ['name' => 'Curry powder',              'quantity' => 1,   'unit' => 'tsp'],
                    ['name' => 'Dried thyme',               'quantity' => 1,   'unit' => 'tsp'],
                    ['name' => 'Bay leaves',                'quantity' => 2,   'unit' => null],
                    ['name' => 'Chicken bouillon cube',     'quantity' => 2,   'unit' => null],
                    ['name' => 'Chicken stock',             'quantity' => 3,   'unit' => 'cups'],
                    ['name' => 'Salt',                      'quantity' => 1,   'unit' => 'tsp'],
                ],
            ],

            [
                'title' => 'Beef Tacos',
                'description' => 'Tuesday taco tradition — spiced ground beef, all the toppings, and a stack of warm tortillas. Everyone builds their own.',
                'created_by' => $sarah->id,
                'servings' => 4,
                'prep_time_minutes' => 10,
                'cook_time_minutes' => 15,
                'total_time_minutes' => 25,
                'is_favorite' => true,
                'sort_order' => 4,
                'instructions' => [
                    'Brown ground beef in a skillet over medium-high heat, breaking it up as it cooks. Drain excess fat.',
                    'Add taco seasoning and 1/3 cup water; stir to combine. Simmer 3–4 minutes until the sauce thickens.',
                    'Warm tortillas directly over a gas flame or in a dry skillet, 30 seconds per side.',
                    'Set out all toppings in small bowls: shredded lettuce, diced tomato, shredded cheese, sour cream, salsa, and avocado.',
                    'Let everyone assemble their own tacos.',
                ],
                'tags' => ['Dinner'],
                'ingredients' => [
                    ['name' => 'Ground beef (80/20)',   'quantity' => 1.5, 'unit' => 'lb'],
                    ['name' => 'Taco seasoning',        'quantity' => 1,   'unit' => 'packet'],
                    ['name' => 'Flour or corn tortillas', 'quantity' => 12, 'unit' => null],
                    ['name' => 'Shredded lettuce',      'quantity' => 2,   'unit' => 'cups'],
                    ['name' => 'Roma tomatoes',         'quantity' => 2,   'unit' => null,    'preparation' => 'diced'],
                    ['name' => 'Shredded cheddar',      'quantity' => 1,   'unit' => 'cup'],
                    ['name' => 'Sour cream',            'quantity' => 0.5, 'unit' => 'cup'],
                    ['name' => 'Salsa',                 'quantity' => 0.5, 'unit' => 'cup'],
                    ['name' => 'Avocado',               'quantity' => 2,   'unit' => null,    'preparation' => 'sliced', 'is_optional' => true],
                ],
            ],

            [
                'title' => 'Turkey & White Bean Chili',
                'description' => 'Lighter than beef chili but just as satisfying. Great for batch cooking — freezes beautifully and gets better the next day.',
                'created_by' => $sarah->id,
                'servings' => 6,
                'prep_time_minutes' => 15,
                'cook_time_minutes' => 35,
                'total_time_minutes' => 50,
                'sort_order' => 5,
                'instructions' => [
                    'Heat olive oil in a large pot over medium heat. Add onion and bell pepper; cook 5 minutes until soft.',
                    'Add garlic, chili powder, cumin, smoked paprika, and oregano. Stir 1 minute until fragrant.',
                    'Add ground turkey; cook 6–8 minutes, breaking it apart, until no longer pink.',
                    'Stir in drained beans, diced tomatoes, and chicken broth. Bring to a boil.',
                    'Reduce heat and simmer 20 minutes, stirring occasionally, until slightly thickened.',
                    'Season with salt and pepper. Serve topped with shredded cheese, sour cream, and cilantro.',
                ],
                'tags' => ['Dinner'],
                'ingredients' => [
                    ['name' => 'Ground turkey',         'quantity' => 1.5, 'unit' => 'lb'],
                    ['name' => 'Olive oil',             'quantity' => 2,   'unit' => 'tbsp'],
                    ['name' => 'Yellow onion',          'quantity' => 1,   'unit' => null,    'preparation' => 'diced'],
                    ['name' => 'Green bell pepper',     'quantity' => 1,   'unit' => null,    'preparation' => 'diced'],
                    ['name' => 'Garlic',                'quantity' => 4,   'unit' => 'cloves', 'preparation' => 'minced'],
                    ['name' => 'Chili powder',          'quantity' => 2,   'unit' => 'tbsp'],
                    ['name' => 'Ground cumin',          'quantity' => 1,   'unit' => 'tsp'],
                    ['name' => 'Smoked paprika',        'quantity' => 1,   'unit' => 'tsp'],
                    ['name' => 'Dried oregano',         'quantity' => 0.5, 'unit' => 'tsp'],
                    ['name' => 'White beans (canned)',  'quantity' => 2,   'unit' => 'cans',  'preparation' => 'drained and rinsed'],
                    ['name' => 'Diced tomatoes (canned)', 'quantity' => 14, 'unit' => 'oz'],
                    ['name' => 'Chicken broth',         'quantity' => 2,   'unit' => 'cups'],
                    ['name' => 'Salt and pepper',       'quantity' => null, 'unit' => null,   'preparation' => 'to taste'],
                ],
            ],

            [
                'title' => 'Fried Rice',
                'description' => 'The best use of leftover rice — better the next day once the rice has dried out a bit. Ready in 15 minutes.',
                'created_by' => $mike->id,
                'servings' => 4,
                'prep_time_minutes' => 10,
                'cook_time_minutes' => 12,
                'total_time_minutes' => 22,
                'sort_order' => 6,
                'instructions' => [
                    'Make sure the rice is cold — leftover rice from the fridge works best.',
                    'Heat 1 tbsp oil in a wok or large skillet over high heat. Add garlic and ginger; stir 20 seconds.',
                    'Push to one side. Add remaining oil and scramble the eggs, breaking them into small pieces as they set.',
                    'Add the cold rice; toss everything together. Spread flat and let it sit 1–2 minutes to get crispy spots.',
                    'Add peas, carrots, and green onion; toss and cook 2 minutes.',
                    'Drizzle soy sauce and sesame oil over the top; toss to coat. Taste and adjust seasoning.',
                ],
                'tags' => ['Dinner', 'Lunch'],
                'ingredients' => [
                    ['name' => 'Cooked jasmine rice',  'quantity' => 4,   'unit' => 'cups',  'preparation' => 'cold, day-old'],
                    ['name' => 'Eggs',                 'quantity' => 3,   'unit' => null,    'preparation' => 'beaten'],
                    ['name' => 'Vegetable oil',        'quantity' => 3,   'unit' => 'tbsp'],
                    ['name' => 'Garlic',               'quantity' => 3,   'unit' => 'cloves', 'preparation' => 'minced'],
                    ['name' => 'Fresh ginger',         'quantity' => 1,   'unit' => 'tsp',   'preparation' => 'grated'],
                    ['name' => 'Frozen peas',          'quantity' => 0.5, 'unit' => 'cup'],
                    ['name' => 'Carrots',              'quantity' => 0.5, 'unit' => 'cup',   'preparation' => 'diced small'],
                    ['name' => 'Green onions',         'quantity' => 3,   'unit' => null,    'preparation' => 'sliced'],
                    ['name' => 'Soy sauce',            'quantity' => 3,   'unit' => 'tbsp'],
                    ['name' => 'Sesame oil',           'quantity' => 1,   'unit' => 'tsp'],
                ],
            ],

            [
                'title' => 'Teriyaki Salmon Bowl',
                'description' => 'Glazed salmon over rice with quick-pickled cucumber and steamed edamame. Looks fancy, done in 30 minutes.',
                'created_by' => $sarah->id,
                'servings' => 4,
                'prep_time_minutes' => 10,
                'cook_time_minutes' => 20,
                'total_time_minutes' => 30,
                'is_favorite' => true,
                'sort_order' => 7,
                'instructions' => [
                    'Make teriyaki glaze: combine soy sauce, mirin, sugar, and cornstarch in a small saucepan. Simmer 3–4 minutes until thickened. Set aside.',
                    'Season salmon fillets with salt. Heat oil in a skillet over medium-high heat.',
                    'Cook salmon skin-side up for 3 minutes. Flip; brush with glaze. Cook 3–4 minutes more until cooked through.',
                    'For the pickled cucumber: toss sliced cucumber with rice vinegar, sugar, and a pinch of salt. Let sit 10 minutes.',
                    'Divide cooked rice into bowls. Top with salmon, pickled cucumber, edamame, and avocado.',
                    'Drizzle remaining glaze over the top. Garnish with sesame seeds and scallions.',
                ],
                'tags' => ['Dinner'],
                'ingredients' => [
                    ['name' => 'Salmon fillets',       'quantity' => 4,   'unit' => null,    'preparation' => '5–6 oz each'],
                    ['name' => 'Soy sauce',            'quantity' => 0.25, 'unit' => 'cup'],
                    ['name' => 'Mirin',                'quantity' => 2,   'unit' => 'tbsp'],
                    ['name' => 'Sugar',                'quantity' => 1,   'unit' => 'tbsp'],
                    ['name' => 'Cornstarch',           'quantity' => 1,   'unit' => 'tsp'],
                    ['name' => 'Japanese cucumber',    'quantity' => 1,   'unit' => null,    'preparation' => 'thinly sliced'],
                    ['name' => 'Rice vinegar',         'quantity' => 2,   'unit' => 'tbsp'],
                    ['name' => 'Cooked short-grain rice', 'quantity' => 3, 'unit' => 'cups'],
                    ['name' => 'Shelled edamame',      'quantity' => 1,   'unit' => 'cup',   'preparation' => 'steamed'],
                    ['name' => 'Avocado',              'quantity' => 1,   'unit' => null,    'preparation' => 'sliced', 'is_optional' => true],
                    ['name' => 'Sesame seeds',         'quantity' => 1,   'unit' => 'tsp'],
                    ['name' => 'Scallions',            'quantity' => 2,   'unit' => null,    'preparation' => 'sliced'],
                ],
            ],

            [
                'title' => 'Homemade Pizza Night',
                'description' => 'Friday tradition — everyone gets their own dough ball and picks their own toppings. Naia does cheese only; Kenji stacks everything.',
                'created_by' => $mike->id,
                'servings' => 4,
                'prep_time_minutes' => 20,
                'cook_time_minutes' => 15,
                'total_time_minutes' => 95,
                'sort_order' => 8,
                'instructions' => [
                    'Mix flour, yeast, salt, and olive oil in a bowl. Add warm water gradually; knead 8–10 minutes until smooth and elastic.',
                    'Place in an oiled bowl, cover, and let rise 1 hour until doubled.',
                    'Preheat oven to 500°F (or as hot as it goes) with a pizza stone or baking sheet inside.',
                    'Divide dough into 4 balls. On a floured surface, stretch each into a thin round.',
                    'Spread sauce on each dough round, then add mozzarella and toppings.',
                    'Bake 10–14 minutes until the crust is golden and cheese is bubbling.',
                    'Rest 2 minutes before slicing.',
                ],
                'tags' => ['Dinner'],
                'ingredients' => [
                    ['name' => 'All-purpose flour',     'quantity' => 3,   'unit' => 'cups'],
                    ['name' => 'Instant yeast',         'quantity' => 1,   'unit' => 'packet'],
                    ['name' => 'Salt',                  'quantity' => 1,   'unit' => 'tsp'],
                    ['name' => 'Olive oil',             'quantity' => 2,   'unit' => 'tbsp'],
                    ['name' => 'Warm water',            'quantity' => 1,   'unit' => 'cup'],
                    ['name' => 'Pizza sauce',           'quantity' => 1,   'unit' => 'cup'],
                    ['name' => 'Shredded mozzarella',   'quantity' => 2,   'unit' => 'cups'],
                    ['name' => 'Toppings of choice',    'quantity' => null, 'unit' => null,   'preparation' => 'pepperoni, mushrooms, bell pepper, etc.'],
                ],
            ],

            // ── Breakfasts ────────────────────────────────────────────────────

            [
                'title' => 'Saturday Pancakes',
                'description' => "Marcus's weekend pancake tradition. Fluffy, golden, and perfect with maple syrup and a pile of berries.",
                'created_by' => $mike->id,
                'servings' => 4,
                'prep_time_minutes' => 5,
                'cook_time_minutes' => 15,
                'total_time_minutes' => 20,
                'is_favorite' => true,
                'sort_order' => 9,
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
                'title' => 'Avocado Toast with Poached Eggs',
                'description' => "Zara's go-to weekend breakfast. Creamy avocado, runny poached eggs, chilli flakes. Takes 10 minutes once you get the hang of poaching.",
                'created_by' => $sarah->id,
                'servings' => 2,
                'prep_time_minutes' => 5,
                'cook_time_minutes' => 8,
                'total_time_minutes' => 13,
                'sort_order' => 10,
                'instructions' => [
                    'Bring a wide saucepan of water to a gentle simmer. Add a splash of white vinegar.',
                    'Toast the sourdough slices.',
                    'Mash avocado with lemon juice, salt, and red pepper flakes.',
                    'Create a gentle whirlpool in the water. Crack each egg into a small cup first, then slide it into the water.',
                    'Poach eggs 3 minutes for a runny yolk. Remove with a slotted spoon and blot dry.',
                    'Spread avocado on toast. Top with poached eggs. Finish with sea salt, black pepper, and extra red pepper flakes.',
                ],
                'tags' => ['Breakfast'],
                'ingredients' => [
                    ['name' => 'Sourdough bread',    'quantity' => 2,   'unit' => 'slices', 'preparation' => 'thick-cut'],
                    ['name' => 'Ripe avocado',       'quantity' => 1,   'unit' => null],
                    ['name' => 'Lemon juice',        'quantity' => 1,   'unit' => 'tsp'],
                    ['name' => 'Red pepper flakes',  'quantity' => 0.25, 'unit' => 'tsp'],
                    ['name' => 'Eggs',               'quantity' => 2,   'unit' => null],
                    ['name' => 'White vinegar',      'quantity' => 1,   'unit' => 'tbsp'],
                    ['name' => 'Sea salt',           'quantity' => null, 'unit' => null,   'preparation' => 'to taste'],
                    ['name' => 'Black pepper',       'quantity' => null, 'unit' => null,   'preparation' => 'to taste'],
                ],
            ],

            [
                'title' => 'Blueberry Muffins',
                'description' => 'Naia calls these "bakery muffins" because the tops crack open and get golden. Simple pantry recipe, 30 minutes start to finish.',
                'created_by' => $sarah->id,
                'servings' => 12,
                'prep_time_minutes' => 10,
                'cook_time_minutes' => 22,
                'total_time_minutes' => 32,
                'is_favorite' => true,
                'sort_order' => 11,
                'instructions' => [
                    'Preheat oven to 400°F. Line a 12-cup muffin tin with liners or grease well.',
                    'Whisk flour, sugar, baking powder, and salt in a large bowl.',
                    'In a separate bowl, whisk melted butter, milk, eggs, and vanilla.',
                    'Pour wet into dry; fold until just combined — do not overmix.',
                    'Fold in blueberries. Divide batter evenly into muffin cups (fill 3/4 full).',
                    'Sprinkle tops with a pinch of sugar.',
                    'Bake 20–22 minutes until a toothpick comes out clean and tops are golden.',
                    'Cool in pan 5 minutes, then transfer to a rack.',
                ],
                'tags' => ['Breakfast', 'Snack'],
                'ingredients' => [
                    ['name' => 'All-purpose flour',  'quantity' => 2,   'unit' => 'cups'],
                    ['name' => 'Sugar',              'quantity' => 0.75, 'unit' => 'cup'],
                    ['name' => 'Baking powder',      'quantity' => 2,   'unit' => 'tsp'],
                    ['name' => 'Salt',               'quantity' => 0.5, 'unit' => 'tsp'],
                    ['name' => 'Butter',             'quantity' => 0.5, 'unit' => 'cup',  'preparation' => 'melted'],
                    ['name' => 'Milk',               'quantity' => 0.75, 'unit' => 'cup'],
                    ['name' => 'Eggs',               'quantity' => 2,   'unit' => null,   'preparation' => 'large'],
                    ['name' => 'Vanilla extract',    'quantity' => 1,   'unit' => 'tsp'],
                    ['name' => 'Fresh blueberries',  'quantity' => 1.5, 'unit' => 'cups'],
                ],
            ],

            // ── Desserts & Snacks ─────────────────────────────────────────────

            [
                'title' => 'Chocolate Chip Cookies',
                'description' => "Crisp edges, chewy centers. The Ellis family's go-to weekend baking project — Naia measures, Kenji stirs, Zara judges.",
                'created_by' => $sarah->id,
                'servings' => 24,
                'prep_time_minutes' => 15,
                'cook_time_minutes' => 12,
                'total_time_minutes' => 27,
                'is_favorite' => true,
                'sort_order' => 12,
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

            [
                'title' => 'Banana Bread',
                'description' => 'The overripe banana rescue. Moist, golden, and keeps for days — if it lasts that long. Naia calls it "banana cake."',
                'created_by' => $mike->id,
                'servings' => 10,
                'prep_time_minutes' => 10,
                'cook_time_minutes' => 60,
                'total_time_minutes' => 70,
                'sort_order' => 13,
                'instructions' => [
                    'Preheat oven to 350°F. Grease a 9x5 loaf pan.',
                    'Mash bananas in a large bowl until smooth.',
                    'Whisk in melted butter, brown sugar, egg, and vanilla.',
                    'Fold in flour, baking soda, and salt until just combined.',
                    'Optional: fold in walnuts or chocolate chips.',
                    'Pour into the prepared pan. Bake 55–65 minutes until a toothpick comes out clean.',
                    'Cool in pan 10 minutes, then turn out onto a rack.',
                ],
                'tags' => ['Breakfast', 'Snack'],
                'ingredients' => [
                    ['name' => 'Overripe bananas',   'quantity' => 3,   'unit' => null,   'preparation' => 'very ripe'],
                    ['name' => 'Butter',             'quantity' => 0.33, 'unit' => 'cup',  'preparation' => 'melted'],
                    ['name' => 'Brown sugar',        'quantity' => 0.75, 'unit' => 'cup',  'preparation' => 'packed'],
                    ['name' => 'Egg',                'quantity' => 1,   'unit' => null,   'preparation' => 'beaten'],
                    ['name' => 'Vanilla extract',    'quantity' => 1,   'unit' => 'tsp'],
                    ['name' => 'All-purpose flour',  'quantity' => 1.5, 'unit' => 'cups'],
                    ['name' => 'Baking soda',        'quantity' => 1,   'unit' => 'tsp'],
                    ['name' => 'Salt',               'quantity' => 0.25, 'unit' => 'tsp'],
                    ['name' => 'Walnuts or chocolate chips', 'quantity' => 0.5, 'unit' => 'cup', 'is_optional' => true],
                ],
            ],

            // ── Lunches ───────────────────────────────────────────────────────

            [
                'title' => 'Caesar Salad',
                'description' => 'The real deal — homemade dressing, crispy croutons, Parmesan. Add grilled chicken for a full meal.',
                'created_by' => $sarah->id,
                'servings' => 4,
                'prep_time_minutes' => 20,
                'cook_time_minutes' => 10,
                'total_time_minutes' => 30,
                'sort_order' => 14,
                'instructions' => [
                    'Make croutons: toss bread cubes with olive oil, garlic powder, and salt. Bake at 375°F for 10–12 minutes until golden.',
                    'Make dressing: whisk together mayonnaise, lemon juice, Worcestershire, Dijon, and anchovy paste until smooth.',
                    'Add grated Parmesan, garlic, salt, and pepper; whisk again.',
                    'Tear romaine into large pieces and place in a wide bowl.',
                    'Toss romaine with just enough dressing to lightly coat every leaf.',
                    'Add croutons and a generous shower of Parmesan. Serve immediately.',
                ],
                'tags' => ['Lunch'],
                'ingredients' => [
                    ['name' => 'Romaine lettuce',    'quantity' => 2,   'unit' => 'heads',  'preparation' => 'torn into pieces'],
                    ['name' => 'Sourdough bread',    'quantity' => 3,   'unit' => 'cups',   'preparation' => 'cubed'],
                    ['name' => 'Olive oil',          'quantity' => 3,   'unit' => 'tbsp'],
                    ['name' => 'Mayonnaise',         'quantity' => 0.33, 'unit' => 'cup'],
                    ['name' => 'Lemon juice',        'quantity' => 2,   'unit' => 'tbsp'],
                    ['name' => 'Worcestershire sauce', 'quantity' => 1, 'unit' => 'tsp'],
                    ['name' => 'Dijon mustard',      'quantity' => 1,   'unit' => 'tsp'],
                    ['name' => 'Anchovy paste',      'quantity' => 1,   'unit' => 'tsp',    'is_optional' => true],
                    ['name' => 'Garlic',             'quantity' => 2,   'unit' => 'cloves', 'preparation' => 'minced'],
                    ['name' => 'Parmesan',           'quantity' => 0.5, 'unit' => 'cup',    'preparation' => 'freshly grated'],
                    ['name' => 'Black pepper',       'quantity' => null, 'unit' => null,    'preparation' => 'to taste'],
                ],
            ],

            [
                'title' => 'Miso Soup',
                'description' => "Simple, warming, and done in 10 minutes. Kenji learned to make this on his own — it's become his post-practice go-to.",
                'created_by' => $sarah->id,
                'servings' => 4,
                'prep_time_minutes' => 5,
                'cook_time_minutes' => 8,
                'total_time_minutes' => 13,
                'sort_order' => 15,
                'instructions' => [
                    'Bring water to just below a boil. Add dashi granules and stir to dissolve.',
                    'Add cubed tofu and sliced mushrooms; simmer 3 minutes.',
                    'Remove a ladle of broth and whisk in the miso paste until smooth. Pour back into the pot.',
                    'Do not boil after adding miso — it destroys the flavor.',
                    'Add sliced scallions. Divide into bowls and serve immediately with steamed rice.',
                ],
                'tags' => ['Lunch'],
                'ingredients' => [
                    ['name' => 'Water',              'quantity' => 4,   'unit' => 'cups'],
                    ['name' => 'Dashi granules',     'quantity' => 1,   'unit' => 'tsp'],
                    ['name' => 'White or red miso paste', 'quantity' => 3, 'unit' => 'tbsp'],
                    ['name' => 'Firm tofu',          'quantity' => 8,   'unit' => 'oz',   'preparation' => 'cubed'],
                    ['name' => 'Shiitake mushrooms',  'quantity' => 1,   'unit' => 'cup',  'preparation' => 'thinly sliced'],
                    ['name' => 'Scallions',          'quantity' => 3,   'unit' => null,   'preparation' => 'thinly sliced'],
                    ['name' => 'Dried wakame seaweed', 'quantity' => 1,  'unit' => 'tbsp', 'is_optional' => true],
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
