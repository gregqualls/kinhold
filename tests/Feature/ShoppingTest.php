<?php

namespace Tests\Feature;

use App\Enums\FamilyRole;
use App\Enums\ShoppingItemSource;
use App\Models\Family;
use App\Models\ProductCatalog;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\ShoppingItem;
use App\Models\ShoppingList;
use App\Models\Staple;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShoppingTest extends TestCase
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

    // -------------------------------------------------------------------------
    // Shopping List CRUD
    // -------------------------------------------------------------------------

    public function test_parent_can_create_shopping_list(): void
    {
        Sanctum::actingAs($this->parent);

        $response = $this->postJson('/api/v1/shopping/lists', [
            'name' => 'Weekly Groceries',
            'store_name' => 'Trader Joe\'s',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('list.name', 'Weekly Groceries')
            ->assertJsonPath('list.store_name', 'Trader Joe\'s');

        $this->assertDatabaseHas('shopping_lists', [
            'family_id' => $this->family->id,
            'name' => 'Weekly Groceries',
        ]);
    }

    public function test_parent_can_update_shopping_list(): void
    {
        Sanctum::actingAs($this->parent);

        $list = ShoppingList::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'name' => 'Old Name',
        ]);

        $response = $this->putJson("/api/v1/shopping/lists/{$list->id}", ['name' => 'New Name']);

        $response->assertOk()->assertJsonPath('list.name', 'New Name');
        $this->assertDatabaseHas('shopping_lists', ['id' => $list->id, 'name' => 'New Name']);
    }

    public function test_parent_can_delete_shopping_list(): void
    {
        Sanctum::actingAs($this->parent);

        $list = ShoppingList::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'name' => 'Costco Run',
        ]);

        ShoppingItem::create([
            'shopping_list_id' => $list->id,
            'family_id' => $this->family->id,
            'added_by' => $this->parent->id,
            'name' => 'Eggs',
            'source' => ShoppingItemSource::Manual,
        ]);

        $response = $this->deleteJson("/api/v1/shopping/lists/{$list->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('shopping_lists', ['id' => $list->id]);
        $this->assertDatabaseMissing('shopping_items', ['shopping_list_id' => $list->id]);
    }

    public function test_child_cannot_create_shopping_list(): void
    {
        Sanctum::actingAs($this->child);

        $response = $this->postJson('/api/v1/shopping/lists', ['name' => 'My List']);

        $response->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // Auto-populate staples
    // -------------------------------------------------------------------------

    public function test_create_list_auto_populates_active_staples(): void
    {
        Sanctum::actingAs($this->parent);

        Staple::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'name' => 'Milk', 'default_quantity' => '1 gallon', 'is_active' => true]);
        Staple::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'name' => 'Eggs', 'default_quantity' => '1 dozen', 'is_active' => true]);
        Staple::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'name' => 'Bread', 'default_quantity' => null, 'is_active' => false]);

        $response = $this->postJson('/api/v1/shopping/lists', ['name' => 'Weekly']);

        $response->assertStatus(201);

        $listId = $response->json('list.id');
        $items = ShoppingItem::where('shopping_list_id', $listId)->get();

        $this->assertCount(2, $items);
        $this->assertEqualsCanonicalizing(['Milk', 'Eggs'], $items->pluck('name')->toArray());
        $this->assertTrue($items->every(fn ($i) => $i->source === ShoppingItemSource::Staple));
    }

    // -------------------------------------------------------------------------
    // Manual item + auto-categorization
    // -------------------------------------------------------------------------

    public function test_add_manual_item_auto_categorizes_from_catalog(): void
    {
        Sanctum::actingAs($this->parent);

        ProductCatalog::create(['id' => (string) Str::uuid(), 'name' => 'Eggs', 'category' => 'Dairy']);

        $list = ShoppingList::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'name' => 'Test List',
        ]);

        $response = $this->postJson("/api/v1/shopping/lists/{$list->id}/items", [
            'name' => 'Eggs',
        ]);

        $response->assertStatus(201)->assertJsonPath('item.category', 'Dairy');
    }

    public function test_add_manual_item_without_catalog_match_has_null_category(): void
    {
        Sanctum::actingAs($this->parent);

        $list = ShoppingList::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'name' => 'Test List',
        ]);

        $response = $this->postJson("/api/v1/shopping/lists/{$list->id}/items", [
            'name' => 'Weird Thing XYZ',
        ]);

        $response->assertStatus(201)->assertJsonPath('item.category', null);
    }

    // -------------------------------------------------------------------------
    // Catalog search / autocomplete
    // -------------------------------------------------------------------------

    public function test_catalog_search_returns_matching_items(): void
    {
        Sanctum::actingAs($this->parent);

        ProductCatalog::create(['id' => (string) Str::uuid(), 'name' => 'Eggs', 'category' => 'Dairy']);
        ProductCatalog::create(['id' => (string) Str::uuid(), 'name' => 'Eggplant', 'category' => 'Produce']);
        ProductCatalog::create(['id' => (string) Str::uuid(), 'name' => 'Milk', 'category' => 'Dairy']);

        $response = $this->getJson('/api/v1/shopping/catalog/search?q=egg');

        $response->assertOk();
        $names = collect($response->json('results'))->pluck('name');
        $this->assertTrue($names->contains('Eggs'));
        $this->assertTrue($names->contains('Eggplant'));
        $this->assertFalse($names->contains('Milk'));
    }

    // -------------------------------------------------------------------------
    // Check / uncheck items
    // -------------------------------------------------------------------------

    public function test_check_item_records_user_and_timestamp(): void
    {
        Sanctum::actingAs($this->parent);

        $list = ShoppingList::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'name' => 'List']);
        $item = ShoppingItem::create([
            'shopping_list_id' => $list->id,
            'family_id' => $this->family->id,
            'added_by' => $this->parent->id,
            'name' => 'Butter',
            'source' => ShoppingItemSource::Manual,
        ]);

        $response = $this->patchJson("/api/v1/shopping/items/{$item->id}/check");

        $response->assertOk()->assertJsonPath('item.is_checked', true);
        $this->assertDatabaseHas('shopping_items', [
            'id' => $item->id,
            'is_checked' => true,
            'checked_by' => $this->parent->id,
        ]);
        $this->assertNotNull($item->fresh()->checked_at);
    }

    public function test_uncheck_item_clears_check_state(): void
    {
        Sanctum::actingAs($this->parent);

        $list = ShoppingList::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'name' => 'List']);
        $item = ShoppingItem::create([
            'shopping_list_id' => $list->id,
            'family_id' => $this->family->id,
            'added_by' => $this->parent->id,
            'name' => 'Butter',
            'source' => ShoppingItemSource::Manual,
            'is_checked' => true,
            'checked_by' => $this->parent->id,
            'checked_at' => now(),
        ]);

        $response = $this->patchJson("/api/v1/shopping/items/{$item->id}/uncheck");

        $response->assertOk()->assertJsonPath('item.is_checked', false);
        $fresh = $item->fresh();
        $this->assertFalse($fresh->is_checked);
        $this->assertNull($fresh->checked_by);
        $this->assertNull($fresh->checked_at);
    }

    // -------------------------------------------------------------------------
    // Pre-shop checklist (on-hand)
    // -------------------------------------------------------------------------

    public function test_mark_on_hand_and_clear_on_hand(): void
    {
        Sanctum::actingAs($this->parent);

        $list = ShoppingList::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'name' => 'List']);
        $item = ShoppingItem::create([
            'shopping_list_id' => $list->id,
            'family_id' => $this->family->id,
            'added_by' => $this->parent->id,
            'name' => 'Olive Oil',
            'source' => ShoppingItemSource::Manual,
        ]);

        $this->patchJson("/api/v1/shopping/items/{$item->id}/on-hand")->assertOk();
        $this->assertTrue($item->fresh()->has_on_hand);

        $this->patchJson("/api/v1/shopping/items/{$item->id}/need")->assertOk();
        $this->assertFalse($item->fresh()->has_on_hand);
    }

    // -------------------------------------------------------------------------
    // Complete trip
    // -------------------------------------------------------------------------

    public function test_complete_trip_deactivates_list(): void
    {
        Sanctum::actingAs($this->parent);

        $list = ShoppingList::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'name' => 'Active List',
            'is_active' => true,
        ]);

        $response = $this->postJson("/api/v1/shopping/lists/{$list->id}/complete");

        $response->assertOk()->assertJsonPath('list.is_active', false);
        $fresh = $list->fresh();
        $this->assertFalse($fresh->is_active);
        $this->assertNotNull($fresh->completed_at);
        $this->assertDatabaseHas('shopping_lists', ['id' => $list->id]);
    }

    // -------------------------------------------------------------------------
    // Family scoping
    // -------------------------------------------------------------------------

    public function test_family_cannot_see_other_familys_lists(): void
    {
        ShoppingList::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'name' => 'Family A List',
        ]);

        Sanctum::actingAs($this->otherParent);

        $response = $this->getJson('/api/v1/shopping/lists');

        $response->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    // -------------------------------------------------------------------------
    // Staple CRUD + toggle
    // -------------------------------------------------------------------------

    public function test_staple_crud(): void
    {
        Sanctum::actingAs($this->parent);

        $create = $this->postJson('/api/v1/shopping/staples', [
            'name' => 'Milk',
            'default_quantity' => '1 gallon',
            'category' => 'Dairy',
        ]);
        $create->assertStatus(201)->assertJsonPath('staple.name', 'Milk');
        $stapleId = $create->json('staple.id');

        $this->putJson("/api/v1/shopping/staples/{$stapleId}", ['name' => 'Whole Milk'])
            ->assertOk()
            ->assertJsonPath('staple.name', 'Whole Milk');

        $this->deleteJson("/api/v1/shopping/staples/{$stapleId}")->assertOk();
        $this->assertDatabaseMissing('staples', ['id' => $stapleId]);
    }

    public function test_toggle_staple_active_inactive(): void
    {
        Sanctum::actingAs($this->parent);

        $staple = Staple::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'name' => 'Butter',
            'is_active' => true,
        ]);

        $this->patchJson("/api/v1/shopping/staples/{$staple->id}/toggle")
            ->assertOk()
            ->assertJsonPath('staple.is_active', false);

        $this->patchJson("/api/v1/shopping/staples/{$staple->id}/toggle")
            ->assertOk()
            ->assertJsonPath('staple.is_active', true);
    }

    // -------------------------------------------------------------------------
    // Module gating
    // -------------------------------------------------------------------------

    public function test_shopping_routes_return_403_when_food_module_disabled(): void
    {
        $settings = $this->family->settings ?? [];
        $settings['modules'] = ['food' => false];
        $this->family->update(['settings' => $settings]);

        Sanctum::actingAs($this->parent);

        $this->getJson('/api/v1/shopping/lists')->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // Child permissions
    // -------------------------------------------------------------------------

    public function test_child_can_check_but_cannot_add_items(): void
    {
        Sanctum::actingAs($this->child);

        $list = ShoppingList::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'name' => 'Family List',
        ]);

        // Cannot add items
        $this->postJson("/api/v1/shopping/lists/{$list->id}/items", ['name' => 'Chips'])
            ->assertStatus(403);

        // Can check items
        $item = ShoppingItem::create([
            'shopping_list_id' => $list->id,
            'family_id' => $this->family->id,
            'added_by' => $this->parent->id,
            'name' => 'Chips',
            'source' => ShoppingItemSource::Manual,
        ]);

        $this->patchJson("/api/v1/shopping/items/{$item->id}/check")->assertOk();
        $this->patchJson("/api/v1/shopping/items/{$item->id}/on-hand")->assertOk();
    }

    // -------------------------------------------------------------------------
    // Add recipe ingredients to shopping list
    // -------------------------------------------------------------------------

    public function test_add_recipe_to_list_creates_items_from_ingredients(): void
    {
        Sanctum::actingAs($this->parent);

        $recipe = Recipe::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Tacos',
            'source_type' => 'manual',
        ]);

        RecipeIngredient::create(['recipe_id' => $recipe->id, 'name' => 'Ground Beef', 'quantity' => '1', 'unit' => 'lb', 'sort_order' => 0]);
        RecipeIngredient::create(['recipe_id' => $recipe->id, 'name' => 'Shredded Cheese', 'quantity' => '2', 'unit' => 'cups', 'sort_order' => 1]);
        RecipeIngredient::create(['recipe_id' => $recipe->id, 'name' => 'Tortillas', 'quantity' => '8', 'unit' => null, 'sort_order' => 2]);

        $list = ShoppingList::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'name' => 'Taco Night',
        ]);

        $response = $this->postJson("/api/v1/shopping/lists/{$list->id}/add-recipe", [
            'recipe_id' => $recipe->id,
        ]);

        $response->assertStatus(201);
        $items = ShoppingItem::where('shopping_list_id', $list->id)->get();

        $this->assertCount(3, $items);
        $this->assertEqualsCanonicalizing(['Ground Beef', 'Shredded Cheese', 'Tortillas'], $items->pluck('name')->toArray());
        $this->assertTrue($items->every(fn ($i) => $i->source === ShoppingItemSource::Recipe));
        $this->assertTrue($items->every(fn ($i) => $i->source_recipe_id === $recipe->id));
        $this->assertTrue($items->every(fn ($i) => $i->source_recipe_name === 'Tacos'));

        // Check quantity formatting
        $beef = $items->firstWhere('name', 'Ground Beef');
        $this->assertEquals('1.000 lb', $beef->quantity);
    }

    public function test_add_recipe_rejects_cross_family_recipe(): void
    {
        Sanctum::actingAs($this->parent);

        $otherRecipe = Recipe::create([
            'family_id' => $this->otherFamily->id,
            'created_by' => $this->otherParent->id,
            'title' => 'Secret Recipe',
            'source_type' => 'manual',
        ]);

        $list = ShoppingList::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'name' => 'My List',
        ]);

        $response = $this->postJson("/api/v1/shopping/lists/{$list->id}/add-recipe", [
            'recipe_id' => $otherRecipe->id,
        ]);

        $response->assertStatus(404);
    }
}
