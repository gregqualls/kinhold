<?php

namespace Database\Seeders;

use App\Models\Family;
use App\Models\VaultCategory;
use Illuminate\Database\Seeder;

class VaultCategorySeeder extends Seeder
{
    /**
     * Seed the vault categories for existing families.
     */
    public function run(): void
    {
        $families = Family::all();

        foreach ($families as $family) {
            foreach (VaultCategory::defaultCategories() as $category) {
                VaultCategory::updateOrCreate(
                    [
                        'family_id' => $family->id,
                        'slug' => $category['slug'],
                    ],
                    [
                        'name' => $category['name'],
                        'icon' => $category['icon'],
                        'description' => $category['description'],
                    ]
                );
            }
        }
    }
}
