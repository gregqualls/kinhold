<?php

namespace App\Services;

use App\Models\Family;
use App\Models\Rating;
use App\Models\Recipe;
use App\Models\RecipeCookLog;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class RecipeService
{
    public function createRecipe(Family $family, User $user, array $data): Recipe
    {
        $recipe = Recipe::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'servings' => $data['servings'] ?? 4,
            'prep_time_minutes' => $data['prep_time_minutes'] ?? null,
            'cook_time_minutes' => $data['cook_time_minutes'] ?? null,
            'total_time_minutes' => $data['total_time_minutes'] ?? null,
            'source_url' => $data['source_url'] ?? null,
            'source_type' => $data['source_type'] ?? 'manual',
            'image_path' => $data['image_path'] ?? null,
            'instructions' => $data['instructions'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_favorite' => $data['is_favorite'] ?? false,
        ]);

        if (! empty($data['ingredients'])) {
            $this->insertIngredients($recipe, $data['ingredients']);
        }

        if (isset($data['tag_ids'])) {
            $recipe->tags()->sync($data['tag_ids']);
        }

        return $recipe->load(['ingredients', 'tags', 'creator']);
    }

    public function updateRecipe(Recipe $recipe, array $data): Recipe
    {
        $fields = [
            'title', 'description', 'servings', 'prep_time_minutes', 'cook_time_minutes',
            'total_time_minutes', 'source_url', 'source_type', 'image_path', 'instructions',
            'notes', 'is_favorite',
        ];

        $updateData = array_intersect_key($data, array_flip($fields));

        if (! empty($updateData)) {
            $recipe->update($updateData);
        }

        if (array_key_exists('ingredients', $data)) {
            $recipe->ingredients()->delete();
            if (! empty($data['ingredients'])) {
                $this->insertIngredients($recipe, $data['ingredients']);
            }
        }

        if (array_key_exists('tag_ids', $data)) {
            $recipe->tags()->sync($data['tag_ids']);
        }

        return $recipe->load(['ingredients', 'tags', 'creator']);
    }

    public function deleteRecipe(Recipe $recipe): void
    {
        $recipe->delete();
    }

    public function restoreRecipe(Recipe $recipe): void
    {
        $recipe->restore();
    }

    public function toggleFavorite(Recipe $recipe): Recipe
    {
        $recipe->is_favorite = ! $recipe->is_favorite;
        $recipe->save();

        return $recipe;
    }

    public function addCookLog(Recipe $recipe, User $user, array $data): RecipeCookLog
    {
        return RecipeCookLog::create([
            'recipe_id' => $recipe->id,
            'user_id' => $user->id,
            'cooked_at' => $data['cooked_at'],
            'servings_made' => $data['servings_made'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function rateRecipe(Recipe $recipe, User $user, int $score): Rating
    {
        return Rating::updateOrCreate(
            [
                'user_id' => $user->id,
                'rateable_type' => Recipe::class,
                'rateable_id' => $recipe->id,
            ],
            [
                'score' => $score,
                'family_id' => $user->family_id,
            ]
        );
    }

    public function searchRecipes(Family $family, array $filters): LengthAwarePaginator
    {
        $query = Recipe::forFamily((string) $family->id);

        if (! empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (! empty($filters['tag'])) {
            $query->whereHas('tags', fn ($q) => $q->where('tags.id', $filters['tag']));
        }

        if (! empty($filters['favorite'])) {
            $query->favorites();
        }

        $sort = $filters['sort'] ?? 'recent';
        match ($sort) {
            'alpha' => $query->orderBy('title'),
            'rating' => $query->orderByDesc(
                Rating::selectRaw('AVG(score)')
                    ->whereColumn('rateable_id', 'recipes.id')
                    ->where('rateable_type', Recipe::class)
                    ->limit(1)
            ),
            default => $query->orderByDesc('created_at'),
        };

        $perPage = (int) ($filters['per_page'] ?? 20);

        return $query->with(['ingredients', 'tags', 'creator'])->paginate($perPage);
    }

    private function insertIngredients(Recipe $recipe, array $ingredients): void
    {
        $rows = [];
        foreach ($ingredients as $index => $ingredient) {
            $rows[] = [
                'id' => Str::uuid()->toString(),
                'recipe_id' => $recipe->id,
                'name' => $ingredient['name'],
                'quantity' => $ingredient['quantity'] ?? null,
                'unit' => $ingredient['unit'] ?? null,
                'preparation' => $ingredient['preparation'] ?? null,
                'group_name' => $ingredient['group_name'] ?? null,
                'is_optional' => $ingredient['is_optional'] ?? false,
                'sort_order' => $ingredient['sort_order'] ?? $index,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        $recipe->ingredients()->insert($rows);
    }
}
