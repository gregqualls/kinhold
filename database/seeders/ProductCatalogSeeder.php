<?php

namespace Database\Seeders;

use App\Models\ProductCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Produce' => [
                'Apples', 'Bananas', 'Avocados', 'Lettuce', 'Tomatoes', 'Onions', 'Potatoes',
                'Carrots', 'Broccoli', 'Bell Peppers', 'Garlic', 'Lemons', 'Limes', 'Celery',
                'Cucumbers', 'Mushrooms', 'Spinach', 'Kale', 'Strawberries', 'Blueberries',
                'Grapes', 'Oranges', 'Ginger', 'Cilantro', 'Parsley', 'Green Onions', 'Zucchini',
                'Yellow Squash', 'Sweet Potatoes', 'Cabbage', 'Brussels Sprouts', 'Asparagus',
                'Green Beans', 'Peas', 'Corn', 'Cauliflower', 'Eggplant', 'Artichokes',
                'Beets', 'Radishes', 'Turnips', 'Butternut Squash', 'Pumpkin', 'Watermelon',
                'Cantaloupe', 'Honeydew', 'Pineapple', 'Mango', 'Peaches', 'Plums',
                'Pears', 'Cherries', 'Raspberries', 'Blackberries', 'Kiwi', 'Papaya',
                'Pomegranate', 'Grapefruit', 'Tangerines', 'Clementines', 'Jalapeños',
                'Serrano Peppers', 'Habanero Peppers', 'Roma Tomatoes', 'Cherry Tomatoes',
                'Baby Spinach', 'Arugula', 'Mixed Greens', 'Romaine Lettuce', 'Iceberg Lettuce',
                'Red Onions', 'Shallots', 'Leeks', 'Fennel', 'Bok Choy', 'Watercress',
            ],
            'Dairy' => [
                'Milk', 'Butter', 'Eggs', 'Shredded Cheese', 'Cream Cheese', 'Sour Cream',
                'Yogurt', 'Heavy Cream', 'Half and Half', 'Parmesan Cheese', 'Cottage Cheese',
                'Whipped Cream', 'Mozzarella Cheese', 'Cheddar Cheese', 'Swiss Cheese',
                'Provolone Cheese', 'Gouda Cheese', 'Brie', 'Feta Cheese', 'Ricotta Cheese',
                'Buttermilk', 'Evaporated Milk', 'Condensed Milk', 'Whipping Cream',
                'Cream Fraiche', 'Greek Yogurt', 'Kefir', 'String Cheese', 'Colby Jack Cheese',
                'Pepper Jack Cheese', 'American Cheese', 'Velveeta',
            ],
            'Meat & Seafood' => [
                'Chicken Breast', 'Ground Beef', 'Bacon', 'Sausage', 'Salmon', 'Shrimp',
                'Pork Chops', 'Ground Turkey', 'Steak', 'Tilapia', 'Deli Turkey', 'Deli Ham',
                'Chicken Thighs', 'Chicken Wings', 'Whole Chicken', 'Pork Tenderloin',
                'Pork Ribs', 'Lamb Chops', 'Ground Lamb', 'Bison', 'Venison', 'Turkey Breast',
                'Hot Dogs', 'Pepperoni', 'Salami', 'Prosciutto', 'Pancetta', 'Chorizo',
                'Italian Sausage', 'Bratwurst', 'Andouille Sausage', 'Cod', 'Halibut',
                'Tuna', 'Canned Tuna', 'Canned Salmon', 'Sardines', 'Anchovies',
                'Lobster', 'Crab', 'Scallops', 'Clams', 'Mussels', 'Oysters', 'Calamari',
                'Beef Roast', 'Pot Roast', 'Beef Brisket', 'Lamb Leg', 'Pork Belly',
            ],
            'Bakery' => [
                'Bread', 'Tortillas', 'Hamburger Buns', 'Hot Dog Buns', 'English Muffins',
                'Bagels', 'Croissants', 'Dinner Rolls', 'Pita Bread', 'Naan', 'Sourdough Bread',
                'Whole Wheat Bread', 'Rye Bread', 'Ciabatta', 'Baguette', 'Brioche',
                'Flour Tortillas', 'Corn Tortillas', 'Flatbread', 'Breadcrumbs', 'Croutons',
                'Muffins', 'Donuts', 'Cinnamon Rolls', 'Danish', 'Scones', 'Biscuits',
            ],
            'Pantry' => [
                'Olive Oil', 'Vegetable Oil', 'Coconut Oil', 'Canola Oil', 'Sesame Oil',
                'Rice', 'Pasta', 'Flour', 'Sugar', 'Salt', 'Black Pepper', 'Chicken Broth',
                'Beef Broth', 'Vegetable Broth', 'Canned Tomatoes', 'Tomato Sauce', 'Tomato Paste',
                'Soy Sauce', 'Apple Cider Vinegar', 'White Vinegar', 'Balsamic Vinegar',
                'Red Wine Vinegar', 'Honey', 'Maple Syrup', 'Agave', 'Peanut Butter',
                'Almond Butter', 'Jelly', 'Jam', 'Preserves', 'Cereal', 'Oats', 'Granola',
                'Canned Beans', 'Black Beans', 'Kidney Beans', 'Chickpeas', 'Lentils',
                'Canned Corn', 'Canned Peas', 'Canned Green Beans', 'Canned Pumpkin',
                'Diced Tomatoes', 'Crushed Tomatoes', 'Sun-Dried Tomatoes', 'Tomato Soup',
                'Cream of Mushroom Soup', 'Chicken Noodle Soup', 'Beef Stew', 'Chili',
                'Refried Beans', 'Coconut Milk', 'Almond Milk', 'Oat Milk', 'Soy Milk',
                'Worcestershire Sauce', 'Fish Sauce', 'Oyster Sauce', 'Hoisin Sauce',
                'Teriyaki Sauce', 'Sriracha', 'Tabasco', 'Frank\'s Red Hot', 'Anchovy Paste',
                'Miso Paste', 'Tahini', 'Harissa', 'Dijon Mustard', 'Whole Grain Mustard',
                'White Rice', 'Brown Rice', 'Jasmine Rice', 'Basmati Rice', 'Quinoa',
                'Couscous', 'Bulgur', 'Farro', 'Barley', 'Arborio Rice',
                'Spaghetti', 'Penne', 'Rigatoni', 'Fettuccine', 'Linguine', 'Angel Hair',
                'Lasagna Noodles', 'Egg Noodles', 'Ramen Noodles', 'Rice Noodles',
                'Panko Breadcrumbs', 'Cornmeal', 'Bread Flour', 'All-Purpose Flour',
                'Whole Wheat Flour', 'Almond Flour', 'Coconut Flour', 'Tapioca Starch',
                'Arrowroot Powder', 'Nutritional Yeast',
            ],
            'Frozen' => [
                'Frozen Vegetables', 'Frozen Fruit', 'Ice Cream', 'Frozen Pizza', 'Frozen Waffles',
                'Frozen Chicken Nuggets', 'Frozen Fish Sticks', 'Frozen Burritos', 'Frozen Edamame',
                'Frozen Peas', 'Frozen Corn', 'Frozen Broccoli', 'Frozen Spinach', 'Frozen Berries',
                'Frozen Mango', 'Frozen Shrimp', 'Frozen Salmon', 'Frozen Pot Pies',
                'Frozen Breakfast Sandwiches', 'Frozen Hash Browns', 'Tater Tots',
                'Frozen Fries', 'Frozen Onion Rings', 'Frozen Meatballs', 'Frozen Lasagna',
                'Frozen Macaroni and Cheese', 'Frozen Soup', 'Frozen Tamales', 'Popsicles',
                'Frozen Yogurt', 'Gelato', 'Ice Cream Bars', 'Sorbet',
            ],
            'Beverages' => [
                'Water', 'Sparkling Water', 'Juice', 'Orange Juice', 'Apple Juice',
                'Cranberry Juice', 'Grape Juice', 'Lemonade', 'Coffee', 'Ground Coffee',
                'Whole Bean Coffee', 'Instant Coffee', 'Tea', 'Green Tea', 'Black Tea',
                'Herbal Tea', 'Soda', 'Diet Soda', 'Sports Drinks', 'Energy Drinks',
                'Coconut Water', 'Kombucha', 'Cold Brew Coffee', 'Iced Tea', 'Hot Chocolate Mix',
                'Protein Shake', 'Smoothie Mix', 'Wine', 'Beer', 'Hard Cider',
            ],
            'Snacks' => [
                'Chips', 'Tortilla Chips', 'Potato Chips', 'Pretzels', 'Crackers',
                'Graham Crackers', 'Rice Cakes', 'Popcorn', 'Granola Bars', 'Protein Bars',
                'Nuts', 'Almonds', 'Cashews', 'Walnuts', 'Peanuts', 'Pistachios',
                'Trail Mix', 'Sunflower Seeds', 'Pumpkin Seeds', 'Cookies', 'Oreos',
                'Fruit Snacks', 'Gummy Bears', 'Fruit Roll-Ups', 'Cheese Crackers',
                'Peanut Butter Crackers', 'Hummus', 'Guacamole', 'Salsa', 'Bean Dip',
                'Spinach Dip', 'French Onion Dip', 'Veggie Chips', 'Seaweed Snacks',
                'Beef Jerky', 'Pepperoni Sticks', 'Cheese Puffs', 'Popcorn Cakes',
            ],
            'Condiments' => [
                'Ketchup', 'Mustard', 'Yellow Mustard', 'Mayonnaise', 'Hot Sauce', 'BBQ Sauce',
                'Ranch Dressing', 'Italian Dressing', 'Caesar Dressing', 'Balsamic Dressing',
                'Thousand Island Dressing', 'Blue Cheese Dressing', 'Honey Mustard',
                'Relish', 'Pickle Relish', 'Pickles', 'Olives', 'Capers', 'Salsa',
                'Pico de Gallo', 'Guacamole', 'Hummus', 'Tzatziki', 'Aioli',
                'Tartar Sauce', 'Cocktail Sauce', 'Horseradish', 'Wasabi', 'Gochujang',
                'Chimichurri', 'Pesto', 'Alfredo Sauce', 'Marinara Sauce', 'Spaghetti Sauce',
                'Pizza Sauce', 'Nacho Cheese Sauce', 'Hollandaise Sauce', 'Gravy',
            ],
            'Baking' => [
                'Baking Soda', 'Baking Powder', 'Vanilla Extract', 'Almond Extract',
                'Cocoa Powder', 'Chocolate Chips', 'Dark Chocolate Chips', 'White Chocolate Chips',
                'Brown Sugar', 'Powdered Sugar', 'Turbinado Sugar', 'Stevia', 'Splenda',
                'Cornstarch', 'Yeast', 'Active Dry Yeast', 'Instant Yeast', 'Cream of Tartar',
                'Gelatin', 'Pectin', 'Food Coloring', 'Sprinkles', 'Cake Mix', 'Brownie Mix',
                'Muffin Mix', 'Pancake Mix', 'Waffle Mix', 'Pie Crust', 'Phyllo Dough',
                'Puff Pastry', 'Shortening', 'Lard', 'Molasses', 'Corn Syrup', 'Light Corn Syrup',
                'Dark Corn Syrup', 'Sweetened Condensed Milk',
            ],
            'Spices' => [
                'Garlic Powder', 'Onion Powder', 'Cumin', 'Paprika', 'Smoked Paprika',
                'Chili Powder', 'Cinnamon', 'Ground Cinnamon', 'Italian Seasoning', 'Oregano',
                'Dried Basil', 'Thyme', 'Rosemary', 'Sage', 'Dill', 'Tarragon', 'Bay Leaves',
                'Red Pepper Flakes', 'Cayenne Pepper', 'Turmeric', 'Curry Powder',
                'Garam Masala', 'Coriander', 'Cardamom', 'Nutmeg', 'Cloves', 'Allspice',
                'Star Anise', 'Fennel Seeds', 'Mustard Seeds', 'Poppy Seeds', 'Sesame Seeds',
                'Everything Bagel Seasoning', 'Lemon Pepper', 'Garlic Salt', 'Seasoned Salt',
                'Steak Seasoning', 'Taco Seasoning', 'Ranch Seasoning', 'Onion Flakes',
                'Dried Minced Garlic', 'Dried Parsley', 'Dried Chives',
            ],
            'Household' => [
                'Paper Towels', 'Toilet Paper', 'Dish Soap', 'Laundry Detergent', 'Fabric Softener',
                'Dryer Sheets', 'Trash Bags', 'Gallon Zip Bags', 'Quart Zip Bags',
                'Sandwich Bags', 'Aluminum Foil', 'Parchment Paper', 'Plastic Wrap',
                'Wax Paper', 'Sponges', 'Scrub Brushes', 'All-Purpose Cleaner',
                'Bathroom Cleaner', 'Glass Cleaner', 'Floor Cleaner', 'Bleach',
                'Dishwasher Detergent', 'Dishwasher Pods', 'Hand Sanitizer', 'Disinfecting Wipes',
                'Paper Plates', 'Paper Cups', 'Plastic Utensils', 'Napkins', 'Coffee Filters',
                'Matches', 'Candles', 'Air Freshener', 'Batteries', 'Light Bulbs',
                'Shelf Liner', 'Rubber Gloves', 'Mop Refills', 'Vacuum Bags',
            ],
            'Personal Care' => [
                'Shampoo', 'Conditioner', 'Body Wash', 'Bar Soap', 'Toothpaste', 'Toothbrush',
                'Dental Floss', 'Mouthwash', 'Deodorant', 'Antiperspirant', 'Hand Soap',
                'Lotion', 'Body Lotion', 'Face Wash', 'Facial Moisturizer', 'Sunscreen',
                'Tissues', 'Cotton Balls', 'Cotton Swabs', 'Razors', 'Shaving Cream',
                'Aftershave', 'Hair Gel', 'Hair Spray', 'Dry Shampoo', 'Hair Ties',
                'Bobby Pins', 'Nail Clippers', 'Nail File', 'Tweezers', 'Lip Balm',
                'Band-Aids', 'Hydrogen Peroxide', 'Rubbing Alcohol', 'Neosporin',
                'Ibuprofen', 'Acetaminophen', 'Antacids', 'Cold Medicine', 'Allergy Medicine',
                'Vitamins', 'Prenatal Vitamins', 'Fish Oil', 'Melatonin',
            ],
            'Baby' => [
                'Diapers', 'Wipes', 'Baby Food', 'Baby Formula', 'Baby Cereal', 'Baby Lotion',
                'Baby Shampoo', 'Baby Wash', 'Diaper Rash Cream', 'Baby Powder',
                'Baby Snacks', 'Puffs', 'Teething Biscuits', 'Baby Oatmeal',
            ],
            'Pet' => [
                'Dog Food', 'Cat Food', 'Cat Litter', 'Dog Treats', 'Cat Treats',
                'Dog Shampoo', 'Flea Treatment', 'Pet Vitamins', 'Pet Wipes',
                'Bird Seed', 'Fish Food', 'Hamster Food',
            ],
            'Deli' => [
                'Sliced Turkey', 'Sliced Ham', 'Sliced Roast Beef', 'Sliced Chicken',
                'Sliced Salami', 'Sliced Pepperoni', 'Sliced Provolone', 'Sliced Swiss',
                'Sliced Cheddar', 'Sliced American', 'Potato Salad', 'Coleslaw',
                'Macaroni Salad', 'Pasta Salad', 'Rotisserie Chicken', 'Soup',
            ],
            'International' => [
                'Soy Sauce', 'Tamari', 'Rice Vinegar', 'Mirin', 'Sake', 'Ponzu',
                'Kimchi', 'Gochujang', 'Doenjang', 'Tofu', 'Tempeh', 'Edamame',
                'Wonton Wrappers', 'Spring Roll Wrappers', 'Dumpling Wrappers',
                'Rice Paper', 'Nori', 'Miso Paste', 'Dashi', 'Panko Breadcrumbs',
                'Coconut Aminos', 'Sambal Oelek', 'Green Curry Paste', 'Red Curry Paste',
                'Yellow Curry Paste', 'Massaman Curry Paste', 'Lemongrass', 'Galangal',
                'Kaffir Lime Leaves', 'Tamarind Paste',
            ],
        ];

        $records = [];

        foreach ($categories as $category => $items) {
            foreach ($items as $item) {
                $records[] = [
                    'id' => (string) Str::uuid(),
                    'name' => $item,
                    'category' => $category,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert in chunks for performance, skip duplicates
        foreach (array_chunk($records, 100) as $chunk) {
            ProductCatalog::upsert(
                $chunk,
                ['name'],
                ['category', 'updated_at'],
            );
        }
    }
}
