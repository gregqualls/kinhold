<?php

namespace Tests\Feature;

use App\Enums\FamilyRole;
use App\Models\Family;
use App\Models\Rating;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    private Family $family;

    private Family $otherFamily;

    private User $parent;

    private User $child;

    private User $otherParent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->family = Family::create(['name' => 'Test Family', 'slug' => 'test-family', 'invite_code' => 'TEST01']);
        $this->otherFamily = Family::create(['name' => 'Other Family', 'slug' => 'other-family', 'invite_code' => 'OTHER1']);

        $this->parent = User::create([
            'name' => 'Parent',
            'email' => 'parent@test.com',
            'password' => bcrypt('password'),
            'family_id' => $this->family->id,
            'family_role' => FamilyRole::Parent,
        ]);

        $this->child = User::create([
            'name' => 'Child',
            'email' => 'child@test.com',
            'password' => bcrypt('password'),
            'family_id' => $this->family->id,
            'family_role' => FamilyRole::Child,
        ]);

        $this->otherParent = User::create([
            'name' => 'Other Parent',
            'email' => 'other@test.com',
            'password' => bcrypt('password'),
            'family_id' => $this->otherFamily->id,
            'family_role' => FamilyRole::Parent,
        ]);
    }

    // ── CRUD Tests ──

    public function test_parent_can_create_recipe_with_ingredients_and_tags(): void
    {
        Sanctum::actingAs($this->parent);

        $tag = Tag::create(['family_id' => $this->family->id, 'name' => 'Dinner', 'color' => '#000000']);

        $response = $this->postJson('/api/v1/recipes', [
            'title' => 'Spaghetti Bolognese',
            'description' => 'Classic Italian pasta',
            'servings' => 4,
            'prep_time_minutes' => 15,
            'cook_time_minutes' => 30,
            'instructions' => [
                ['step' => 1, 'text' => 'Boil water'],
                ['step' => 2, 'text' => 'Cook pasta'],
            ],
            'ingredients' => [
                ['name' => 'Spaghetti', 'quantity' => 400, 'unit' => 'g'],
                ['name' => 'Ground beef', 'quantity' => 500, 'unit' => 'g'],
            ],
            'tag_ids' => [$tag->id],
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('recipe.title', 'Spaghetti Bolognese');

        $this->assertDatabaseHas('recipes', [
            'title' => 'Spaghetti Bolognese',
            'family_id' => $this->family->id,
        ]);
        $this->assertDatabaseHas('recipe_ingredients', ['name' => 'Spaghetti']);
        $this->assertDatabaseHas('recipe_ingredients', ['name' => 'Ground beef']);
        $this->assertDatabaseHas('recipe_tag', ['tag_id' => $tag->id]);
    }

    public function test_parent_can_view_recipe_with_all_relationships(): void
    {
        Sanctum::actingAs($this->parent);

        $recipe = Recipe::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Tacos',
        ]);
        $recipe->ingredients()->create(['name' => 'Tortilla', 'sort_order' => 0]);
        $tag = Tag::create(['family_id' => $this->family->id, 'name' => 'Mexican', 'color' => '#ff0000']);
        $recipe->tags()->attach($tag->id);

        $response = $this->getJson("/api/v1/recipes/{$recipe->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('recipe.title', 'Tacos');
        $response->assertJsonStructure(['recipe' => ['ingredients', 'tags', 'creator']]);
    }

    public function test_parent_can_list_recipes(): void
    {
        Sanctum::actingAs($this->parent);

        Recipe::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'title' => 'Recipe A']);
        Recipe::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'title' => 'Recipe B']);

        $response = $this->getJson('/api/v1/recipes');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    public function test_parent_can_update_recipe_and_replace_ingredients(): void
    {
        Sanctum::actingAs($this->parent);

        $recipe = Recipe::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Old Title',
        ]);
        $recipe->ingredients()->create(['name' => 'Old Ingredient', 'sort_order' => 0]);

        $response = $this->putJson("/api/v1/recipes/{$recipe->id}", [
            'title' => 'New Title',
            'ingredients' => [
                ['name' => 'New Ingredient', 'quantity' => 1, 'unit' => 'cup'],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('recipe.title', 'New Title');

        $this->assertDatabaseMissing('recipe_ingredients', ['name' => 'Old Ingredient']);
        $this->assertDatabaseHas('recipe_ingredients', ['name' => 'New Ingredient']);
    }

    public function test_parent_can_soft_delete_recipe(): void
    {
        Sanctum::actingAs($this->parent);

        $recipe = Recipe::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'To Delete',
        ]);

        $response = $this->deleteJson("/api/v1/recipes/{$recipe->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('recipes', ['id' => $recipe->id]);
    }

    public function test_parent_can_restore_soft_deleted_recipe(): void
    {
        Sanctum::actingAs($this->parent);

        $recipe = Recipe::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Deleted Recipe',
        ]);
        $recipe->delete();

        $response = $this->postJson("/api/v1/recipes/{$recipe->id}/restore");

        $response->assertStatus(200);
        $this->assertDatabaseHas('recipes', ['id' => $recipe->id, 'deleted_at' => null]);
    }

    public function test_parent_can_toggle_favorite(): void
    {
        Sanctum::actingAs($this->parent);

        $recipe = Recipe::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Favorite Recipe',
            'is_favorite' => false,
        ]);

        $response = $this->postJson("/api/v1/recipes/{$recipe->id}/favorite");

        $response->assertStatus(200);
        $response->assertJsonPath('recipe.is_favorite', true);
        $this->assertDatabaseHas('recipes', ['id' => $recipe->id, 'is_favorite' => true]);
    }

    // ── Permission Tests ──

    public function test_child_cannot_create_recipe_when_parents_only(): void
    {
        Sanctum::actingAs($this->child);

        $settings = $this->family->settings ?? [];
        $settings['recipe_creation'] = ['mode' => 'parents_only'];
        $this->family->settings = $settings;
        $this->family->save();

        $response = $this->postJson('/api/v1/recipes', [
            'title' => 'Child Recipe',
        ]);

        $response->assertStatus(403);
    }

    public function test_child_can_view_recipe(): void
    {
        Sanctum::actingAs($this->child);

        $recipe = Recipe::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Family Recipe',
        ]);

        $response = $this->getJson("/api/v1/recipes/{$recipe->id}");

        $response->assertStatus(200);
    }

    public function test_child_cannot_update_recipe(): void
    {
        Sanctum::actingAs($this->child);

        $recipe = Recipe::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Parent Recipe',
        ]);

        $response = $this->putJson("/api/v1/recipes/{$recipe->id}", ['title' => 'Hacked']);

        $response->assertStatus(403);
    }

    public function test_child_cannot_delete_recipe(): void
    {
        Sanctum::actingAs($this->child);

        $recipe = Recipe::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Parent Recipe',
        ]);

        $response = $this->deleteJson("/api/v1/recipes/{$recipe->id}");

        $response->assertStatus(403);
    }

    public function test_child_can_rate_recipe(): void
    {
        Sanctum::actingAs($this->child);

        $recipe = Recipe::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Ratable Recipe',
        ]);

        $response = $this->postJson("/api/v1/recipes/{$recipe->id}/rate", ['score' => 4]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('ratings', [
            'rateable_id' => $recipe->id,
            'rateable_type' => Recipe::class,
            'user_id' => $this->child->id,
            'score' => 4,
        ]);
    }

    public function test_child_can_add_cook_log(): void
    {
        Sanctum::actingAs($this->child);

        $recipe = Recipe::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Cooked Recipe',
        ]);

        $response = $this->postJson("/api/v1/recipes/{$recipe->id}/cook-logs", [
            'cooked_at' => '2026-04-10',
            'servings_made' => 4,
            'notes' => 'Turned out great!',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('recipe_cook_logs', [
            'recipe_id' => $recipe->id,
            'user_id' => $this->child->id,
        ]);
    }

    // ── Cross-Family Isolation ──

    public function test_family_scoping_prevents_cross_family_access(): void
    {
        Sanctum::actingAs($this->otherParent);

        $recipe = Recipe::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Family A Recipe',
        ]);

        $response = $this->getJson("/api/v1/recipes/{$recipe->id}");

        $response->assertStatus(403);
    }

    // ── Rating Tests ──

    public function test_rating_is_upserted_not_duplicated(): void
    {
        Sanctum::actingAs($this->parent);

        $recipe = Recipe::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Rated Recipe',
        ]);

        $this->postJson("/api/v1/recipes/{$recipe->id}/rate", ['score' => 3]);
        $this->postJson("/api/v1/recipes/{$recipe->id}/rate", ['score' => 5]);

        $this->assertDatabaseCount('ratings', 1);
        $this->assertDatabaseHas('ratings', ['rateable_id' => $recipe->id, 'score' => 5]);
    }

    public function test_family_average_rating_calculated(): void
    {
        $recipe = Recipe::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Averaged Recipe',
        ]);

        $user2 = User::create([
            'name' => 'Parent 2',
            'email' => 'parent2@test.com',
            'password' => bcrypt('password'),
            'family_id' => $this->family->id,
            'family_role' => FamilyRole::Parent,
        ]);
        $user3 = User::create([
            'name' => 'Parent 3',
            'email' => 'parent3@test.com',
            'password' => bcrypt('password'),
            'family_id' => $this->family->id,
            'family_role' => FamilyRole::Parent,
        ]);

        Rating::create(['user_id' => $this->parent->id, 'family_id' => $this->family->id, 'rateable_type' => Recipe::class, 'rateable_id' => $recipe->id, 'score' => 3]);
        Rating::create(['user_id' => $user2->id, 'family_id' => $this->family->id, 'rateable_type' => Recipe::class, 'rateable_id' => $recipe->id, 'score' => 4]);
        Rating::create(['user_id' => $user3->id, 'family_id' => $this->family->id, 'rateable_type' => Recipe::class, 'rateable_id' => $recipe->id, 'score' => 5]);

        Sanctum::actingAs($this->parent);
        $response = $this->getJson("/api/v1/recipes/{$recipe->id}");

        $response->assertStatus(200);
        $this->assertEquals(4.0, $response->json('recipe.family_average_rating'));
    }

    // ── Search & Filter Tests ──

    public function test_search_by_title(): void
    {
        Sanctum::actingAs($this->parent);

        Recipe::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'title' => 'Chicken Tacos']);
        Recipe::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'title' => 'Pasta']);
        Recipe::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'title' => 'Fish Tacos']);

        $response = $this->getJson('/api/v1/recipes?search=tacos');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    public function test_search_by_ingredient_name(): void
    {
        Sanctum::actingAs($this->parent);

        $recipe1 = Recipe::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'title' => 'Pasta with Garlic']);
        $recipe1->ingredients()->create(['name' => 'Garlic', 'sort_order' => 0]);

        Recipe::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'title' => 'Plain Pasta']);

        $response = $this->getJson('/api/v1/recipes?search=garlic');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', 'Pasta with Garlic');
    }

    public function test_filter_by_tag(): void
    {
        Sanctum::actingAs($this->parent);

        $tag = Tag::create(['family_id' => $this->family->id, 'name' => 'Vegetarian', 'color' => '#00ff00']);

        $recipe1 = Recipe::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'title' => 'Veggie Stir Fry']);
        $recipe1->tags()->attach($tag->id);

        Recipe::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'title' => 'Beef Burger']);

        $response = $this->getJson("/api/v1/recipes?tag={$tag->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', 'Veggie Stir Fry');
    }

    public function test_filter_by_favorite(): void
    {
        Sanctum::actingAs($this->parent);

        Recipe::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'title' => 'Fave Recipe', 'is_favorite' => true]);
        Recipe::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'title' => 'Normal Recipe', 'is_favorite' => false]);
        Recipe::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'title' => 'Also Normal', 'is_favorite' => false]);

        $response = $this->getJson('/api/v1/recipes?favorite=1');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', 'Fave Recipe');
    }

    public function test_recipe_tags_do_not_bleed_into_task_tags(): void
    {
        Sanctum::actingAs($this->parent);

        $tag = Tag::create(['family_id' => $this->family->id, 'name' => 'Shared Tag', 'color' => '#000000']);

        $recipe = Recipe::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'title' => 'Tagged Recipe']);
        $recipe->tags()->attach($tag->id);

        $this->assertDatabaseCount('task_tag', 0);
        $this->assertDatabaseCount('recipe_tag', 1);
    }

    public function test_food_module_disabled_returns_403(): void
    {
        Sanctum::actingAs($this->parent);

        $settings = $this->family->settings ?? [];
        $settings['module_access']['food'] = ['mode' => 'off'];
        $this->family->settings = $settings;
        $this->family->save();

        $response = $this->getJson('/api/v1/recipes');

        $response->assertStatus(403);
    }

    // ── Fractional quantity validation (#161) ──

    public function test_fractional_quantity_slash_fraction_is_accepted(): void
    {
        Sanctum::actingAs($this->parent);

        $response = $this->postJson('/api/v1/recipes', [
            'title' => 'Fraction Test',
            'instructions' => [['step' => 1, 'text' => 'Mix.']],
            'ingredients' => [
                ['name' => 'butter', 'quantity' => '1/2', 'unit' => 'cup'],
                ['name' => 'sugar', 'quantity' => '3/4', 'unit' => 'cup'],
                ['name' => 'flour', 'quantity' => '1 1/2', 'unit' => 'cups'],
            ],
        ]);

        $response->assertStatus(201);
        // Backend normalises fractions to floats before storing
        $this->assertDatabaseHas('recipe_ingredients', ['name' => 'butter', 'quantity' => 0.5]);
        $this->assertDatabaseHas('recipe_ingredients', ['name' => 'sugar', 'quantity' => 0.75]);
        $this->assertDatabaseHas('recipe_ingredients', ['name' => 'flour', 'quantity' => 1.5]);
    }

    public function test_unicode_fractions_are_accepted(): void
    {
        Sanctum::actingAs($this->parent);

        $response = $this->postJson('/api/v1/recipes', [
            'title' => 'Unicode Fraction Test',
            'instructions' => [['step' => 1, 'text' => 'Mix.']],
            'ingredients' => [
                ['name' => 'milk', 'quantity' => '½', 'unit' => 'cup'],
                ['name' => 'salt', 'quantity' => '¼', 'unit' => 'tsp'],
                ['name' => 'cream', 'quantity' => '1½', 'unit' => 'cups'],
            ],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('recipe_ingredients', ['name' => 'milk', 'quantity' => 0.5]);
        $this->assertDatabaseHas('recipe_ingredients', ['name' => 'salt', 'quantity' => 0.25]);
        $this->assertDatabaseHas('recipe_ingredients', ['name' => 'cream', 'quantity' => 1.5]);
    }

    public function test_invalid_quantity_string_returns_422(): void
    {
        Sanctum::actingAs($this->parent);

        $response = $this->postJson('/api/v1/recipes', [
            'title' => 'Bad Quantity',
            'instructions' => [['step' => 1, 'text' => 'Mix.']],
            'ingredients' => [
                ['name' => 'flour', 'quantity' => 'a lot', 'unit' => 'cups'],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['ingredients.0.quantity']);
    }
}
