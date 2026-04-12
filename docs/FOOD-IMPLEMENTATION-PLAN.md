# Food Features — Implementation Plan

> **Full spec:** [docs/FOOD-FEATURES-SPEC.md](FOOD-FEATURES-SPEC.md)
> **Milestone:** [Phase F: Food & Meal Planning](https://github.com/gregqualls/kinhold/milestone/7)
> **Created:** 2026-04-12
>
> 8 steps. Each step = one PR = roughly one session. Execute in order.

---

## Overview

The food module adds four interconnected features to Kinhold:

```
Recipe Library → Meal Planner → Grocery List (live derived)
                                     ↑
                               Staples (auto-populate)

Errands List (standalone, always available)
```

The central insight: the shopping list is a **live derivative** of the meal plan — not a one-time export. When a meal plan changes, only recipe-derived items update. Manual and staple items are untouched.

---

## Step 1: Recipe Backend + Module Gating

**Issue:** [#148](https://github.com/gregqualls/kinhold/issues/148)
**Branch:** `feature/food-step-1-recipe-backend`
**Depends on:** Nothing
**Size:** Large (full session)

### Scope

Set up the `food` module toggle and build the entire recipe backend: migrations, models, service, controller, policy, form requests, API resources, routes, and tests.

### Deliverables

- [ ] Add `'food'` to `Family::MODULES` constant and frontend `enabledModules`
- [ ] Create enums: `RecipeSourceType`, `MealSlot`, `ShoppingItemSource`
- [ ] 5 migrations: `recipes` (soft deletes), `recipe_ingredients`, `recipe_cook_logs`, `ratings` (polymorphic), `recipe_tag` (pivot)
- [ ] 5 models: `Recipe`, `RecipeIngredient`, `RecipeCookLog`, `Rating`, `RecipeTag`
- [ ] Add `recipes()` relationship to `Tag` model
- [ ] `RecipePolicy` — parent-only create/update/delete, both roles rate + log cooks
- [ ] `StoreRecipeRequest` + `UpdateRecipeRequest` with nested ingredient validation
- [ ] `RecipeService` — CRUD with ingredients + tags, search, rating, cook log
- [ ] 4 API resources: `RecipeResource`, `RecipeIngredientResource`, `RecipeCookLogResource`, `RatingResource`
- [ ] `RecipeController` — 11 endpoints (index, store, show, update, destroy, restore, toggleFavorite, cookLogs, addCookLog, rate, ratings)
- [ ] Routes under `/api/v1/recipes` with `module:food` middleware
- [ ] ~20 feature tests covering CRUD, permissions, search, ratings, tags, module gating

### Key Files

| Action | Path |
|--------|------|
| Modify | `app/Models/Family.php` |
| Modify | `app/Models/Tag.php` |
| Modify | `resources/js/stores/auth.js` |
| Modify | `routes/api.php` |
| Create | `app/Enums/RecipeSourceType.php`, `MealSlot.php`, `ShoppingItemSource.php` |
| Create | `database/migrations/2026_04_12_00000{1-5}_*.php` |
| Create | `app/Models/Recipe.php`, `RecipeIngredient.php`, `RecipeCookLog.php`, `Rating.php`, `RecipeTag.php` |
| Create | `app/Policies/RecipePolicy.php` |
| Create | `app/Http/Requests/Recipe/StoreRecipeRequest.php`, `UpdateRecipeRequest.php` |
| Create | `app/Services/RecipeService.php` |
| Create | `app/Http/Resources/Recipe*.php`, `RatingResource.php` |
| Create | `app/Http/Controllers/Api/V1/RecipeController.php` |
| Create | `tests/Feature/RecipeTest.php` |

### Done Criteria

```bash
php artisan migrate                    # 5 new tables created
php artisan test --filter=Recipe       # All tests pass
./vendor/bin/pint --test               # No formatting issues
./vendor/bin/phpstan analyse           # No errors
npx vite build                         # Clean build
```

---

## Step 2: Recipe Import Service

**Issue:** [#149](https://github.com/gregqualls/kinhold/issues/149)
**Branch:** `feature/food-step-2-recipe-import`
**Depends on:** Step 1
**Size:** Medium

### Scope

AI-powered recipe extraction from URLs and photos using Claude API. Two import flows, both returning editable preview data before saving.

### Deliverables

- [ ] `RecipeImportService` with two flows:
  - **URL import:** Fetch URL → check for JSON-LD `schema.org/Recipe` → if not found, send cleaned HTML to Claude API → return structured recipe data
  - **Photo import:** Accept image → send to Claude Vision API → return structured recipe data
- [ ] Both endpoints support `?preview=1` mode (returns parsed data without persisting)
- [ ] `ImportUrlRequest` + `ImportPhotoRequest` form requests (parent-only)
- [ ] Rate limiting: 20 imports/hour/family
- [ ] 2 new routes: `POST /recipes/import/url`, `POST /recipes/import/photo`
- [ ] Feature tests with mocked HTTP + mocked Claude API responses
- [ ] Graceful error handling (unparseable URL, failed extraction, invalid photo)

### Key Files

| Action | Path |
|--------|------|
| Create | `app/Services/RecipeImportService.php` |
| Create | `app/Http/Requests/Recipe/ImportUrlRequest.php`, `ImportPhotoRequest.php` |
| Create | `tests/Feature/RecipeImportTest.php` |
| Modify | `app/Http/Controllers/Api/V1/RecipeController.php` (add 2 methods) |
| Modify | `routes/api.php` (add 2 routes) |

### Done Criteria

```bash
php artisan test --filter=RecipeImport  # All tests pass (mocked API)
./vendor/bin/pint --test
./vendor/bin/phpstan analyse
```

---

## Step 3: Recipe Frontend

**Issue:** [#150](https://github.com/gregqualls/kinhold/issues/150)
**Branch:** `feature/food-step-3-recipe-frontend`
**Depends on:** Step 1 (Step 2 optional — import modal can stub if import service not yet merged)
**Size:** Large (full session)

### Scope

Complete recipe UI: Pinia store, Food tab container with sub-navigation, recipe list with search/filter, recipe cards, manual create/edit form, detail view with ingredient scaling, cook log, family ratings, import modal, and navigation entries.

### Deliverables

- [ ] `resources/js/stores/recipes.js` — Pinia store (Composition API) with full CRUD, search/filter state, import actions
- [ ] `FoodView.vue` — Tab container with Recipes / Meals / Shopping sub-tabs (Meals + Shopping are placeholder stubs)
- [ ] `RecipesTab.vue` — Grid of recipe cards, search bar, tag filter chips, "Add Recipe" button, empty state
- [ ] `RecipeCard.vue` — Image, title, time badge, tags, favorite heart, family average rating
- [ ] `RecipeForm.vue` — Full form for manual create/edit with dynamic ingredient rows (add/remove/reorder), tag picker
- [ ] `RecipeDetailView.vue` — Hero image, metadata, tabbed sections (Ingredients / Steps / Cook Log)
- [ ] `IngredientList.vue` — Grouped by group_name, servings adjuster with client-side quantity scaling
- [ ] `StepList.vue` — Numbered instructions
- [ ] `CookLogEntry.vue` — Modal to add a cook log entry
- [ ] `FamilyRating.vue` — Star display + tap-to-rate for current user
- [ ] `RecipeImportModal.vue` — Tabs for URL and Photo, loading states, editable preview (pre-populated RecipeForm), error states
- [ ] Router: `/food` with children (`/food/recipes`, `/food/recipes/:id`), lazy-loaded, `meta: { module: 'food' }`
- [ ] Nav entries: Add "Food" to Sidebar + BottomNav (gated by food module)
- [ ] Mobile-first responsive design, dark mode support

### Key Files

| Action | Path |
|--------|------|
| Create | `resources/js/stores/recipes.js` |
| Create | `resources/js/views/food/FoodView.vue`, `RecipesTab.vue`, `RecipeDetailView.vue` |
| Create | `resources/js/components/recipes/RecipeCard.vue`, `RecipeForm.vue`, `RecipeImportModal.vue`, `IngredientList.vue`, `StepList.vue`, `CookLogEntry.vue`, `FamilyRating.vue` |
| Modify | `resources/js/router/index.js` |
| Modify | `resources/js/components/layout/Sidebar.vue`, `BottomNav.vue` |

### Done Criteria

```bash
npx vite build                         # Clean build, 0 errors
# Manual: recipe list renders, CRUD works, search/filter, ratings, cook log, import, dark mode, mobile
```

---

## Step 4: Shopping Backend + Product Catalog

**Issue:** [#151](https://github.com/gregqualls/kinhold/issues/151)
**Branch:** `feature/food-step-4-shopping-backend`
**Depends on:** Step 1
**Size:** Large (full session)

### Scope

Full shopping list backend: product catalog with ~500 seeded items, shopping lists, items with source tracking (manual/recipe/staple), staples, auto-categorization, pre-shop checklist, and trip completion.

### Deliverables

- [ ] 4 migrations: `product_catalog` (global), `shopping_lists`, `shopping_items` (with source enum, recipe attribution, has_on_hand, needed_date), `staples`
- [ ] 4 models: `ProductCatalog`, `ShoppingList`, `ShoppingItem`, `Staple`
- [ ] `ShoppingListService` — createList (auto-populate staples), addItem (auto-categorize from catalog), addRecipeIngredients, removeRecipeItems, checkItem, uncheckItem, markOnHand, clearOnHand, completeTrip
- [ ] `ShoppingListController` — 17 endpoints (list CRUD, item CRUD, check/uncheck, on-hand/need, add-recipe, staples CRUD, catalog search)
- [ ] `ShoppingListPolicy` — parent-only for create/add/edit/delete, both roles for check/uncheck/on-hand
- [ ] Form requests for list and item operations
- [ ] API resources: `ShoppingListResource`, `ShoppingItemResource`
- [ ] `ProductCatalogSeeder` — ~500 common grocery/household items with categories
- [ ] Routes under `/api/v1/shopping` with `module:food` middleware
- [ ] Feature tests: CRUD, staple auto-populate, auto-categorize, pre-shop, recipe attribution, family scoping, module gating

### Key Files

| Action | Path |
|--------|------|
| Create | `database/migrations/2026_04_XX_00000{1-4}_*.php` |
| Create | `app/Models/ProductCatalog.php`, `ShoppingList.php`, `ShoppingItem.php`, `Staple.php` |
| Create | `app/Services/ShoppingListService.php` |
| Create | `app/Policies/ShoppingListPolicy.php` |
| Create | `app/Http/Controllers/Api/V1/ShoppingListController.php` |
| Create | `app/Http/Resources/ShoppingListResource.php`, `ShoppingItemResource.php` |
| Create | `database/seeders/ProductCatalogSeeder.php` |
| Create | `tests/Feature/ShoppingListTest.php` |
| Modify | `routes/api.php` |

### Done Criteria

```bash
php artisan migrate
php artisan db:seed --class=ProductCatalogSeeder
php artisan test --filter=Shopping
./vendor/bin/pint --test
./vendor/bin/phpstan analyse
```

---

## Step 5: Shopping Frontend + Errands List

**Issue:** [#152](https://github.com/gregqualls/kinhold/issues/152)
**Branch:** `feature/food-step-5-shopping-errands`
**Depends on:** Step 3 (FoodView shell), Step 4 (shopping backend)
**Size:** Large (full session)

### Scope

Shopping list UI with category grouping, recipe attribution, pre-shop checklist, staples manager, and autocomplete. Plus the complete errands list (full stack — backend + frontend) as a standalone always-available feature.

### Deliverables

**Shopping Frontend:**
- [ ] `resources/js/stores/shopping.js` — Pinia store
- [ ] `ShoppingTab.vue` — Active list display, category-grouped items, trip controls
- [ ] `ShoppingListItem.vue` — Checkbox, name, quantity, recipe attribution, needed-date badge, source icon
- [ ] `AddItemInput.vue` — Quick-add with autocomplete from product catalog + family history
- [ ] `PreShopChecklist.vue` — "Have it" / "Need it" toggles before a trip
- [ ] `StaplesManager.vue` — Manage standing items, toggle on/off
- [ ] `TripControls.vue` — New List, Complete Trip, Pre-Shop Check, list selector

**Errands List (full stack):**
- [ ] Migration: `errand_items` table (family_id, name, store, notes, is_checked, checked_by/at, sort_order)
- [ ] `ErrandItem` model, `ErrandController`, `ErrandPolicy` (parent-only add/edit/delete, both roles check)
- [ ] Routes under `/api/v1/errands` — NO module gating (always available)
- [ ] `resources/js/stores/errands.js` — Pinia store
- [ ] `ErrandsView.vue` — Store filter chips, quick-add with store autocomplete, clear-checked button
- [ ] `ErrandItem.vue` — Checkbox, name, store badge, notes
- [ ] Nav entry for Errands (separate from Food, always visible)
- [ ] Feature tests for errands

### Done Criteria

```bash
php artisan migrate                     # errand_items table
php artisan test --filter=Errand
npx vite build
# Manual: shopping list works, errands list works, both in dark mode
```

---

## Step 6: Meal Plan Backend

**Issue:** [#153](https://github.com/gregqualls/kinhold/issues/153)
**Branch:** `feature/food-step-6-mealplan-backend`
**Depends on:** Step 1 (recipes), Step 4 (shopping lists — `addRecipeIngredients` method)
**Size:** XL (biggest single step)

### Scope

The meal plan backend including the **live pipeline** (meal plan → shopping list), restaurants, presets, and cook assignment. This is the most architecturally critical step.

### Deliverables

- [ ] 5 migrations: `meal_presets`, `restaurants` (global), `family_restaurants` (pivot), `meal_plans`, `meal_plan_entries`
- [ ] 6 models: `MealPreset`, `Restaurant`, `FamilyRestaurant`, `MealPlan`, `MealPlanEntry`
- [ ] `MealPlanService` — **the live pipeline:**
  - addEntry: creates entry → if recipe, calls `ShoppingListService::addRecipeIngredients()` → creates linked shopping list if needed
  - updateEntry: removes old recipe items → adds new ones (diff, never regenerate)
  - removeEntry: cascades shopping item removal
  - Cook assignment: auto-creates linked tasks per assigned cook with points
- [ ] `RestaurantImportService` — 3-layer Google Maps import (URL parse → LLM fallback → global DB upsert)
- [ ] `MealPlanController` — plan CRUD, entry CRUD, generate-list, presets CRUD
- [ ] `RestaurantController` — CRUD, import, rate (unified rating system)
- [ ] `MealPresetSeeder` — 4 system presets per family
- [ ] Routes under `/api/v1/meal-plans` + `/api/v1/restaurants` with `module:food`
- [ ] Feature tests including **full pipeline integration test:**
  - Add 5 recipes to a week → verify shopping list has all ingredients with attribution
  - Swap one recipe → verify list updated (old removed, new added)
  - Delete entry → verify cascade
  - Add restaurant/preset → verify NO shopping items created
  - Cook assignment → verify tasks created, updated, deleted

### Key Files

| Action | Path |
|--------|------|
| Create | `database/migrations/2026_04_XX_00000{1-5}_*.php` |
| Create | `app/Models/MealPreset.php`, `Restaurant.php`, `FamilyRestaurant.php`, `MealPlan.php`, `MealPlanEntry.php` |
| Create | `app/Services/MealPlanService.php`, `RestaurantImportService.php` |
| Create | `app/Http/Controllers/Api/V1/MealPlanController.php`, `RestaurantController.php` |
| Create | `database/seeders/MealPresetSeeder.php` |
| Create | `tests/Feature/MealPlanTest.php`, `tests/Feature/MealPlanPipelineTest.php` |
| Modify | `routes/api.php` |

### Done Criteria

```bash
php artisan migrate
php artisan db:seed --class=MealPresetSeeder
php artisan test --filter=MealPlan
php artisan test --filter=Restaurant
./vendor/bin/pint --test
./vendor/bin/phpstan analyse
```

---

## Step 7: Meal Plan Frontend

**Issue:** [#154](https://github.com/gregqualls/kinhold/issues/154)
**Branch:** `feature/food-step-7-mealplan-frontend`
**Depends on:** Step 3 (FoodView shell), Step 6 (meal plan backend)
**Size:** Large (full session)

### Scope

Weekly meal calendar UI with entry picker, desktop drag-and-drop, restaurant picker, and the shopping list connection.

### Deliverables

- [ ] `resources/js/stores/mealPlan.js` — Pinia store
- [ ] `MealPlanTab.vue` — Weekly grid (desktop: 7 cols × 4 rows), day-list (mobile), week navigation
- [ ] `MealSlotCard.vue` — Compact card for each entry type (recipe/restaurant/preset/custom)
- [ ] `MealPlanSidebar.vue` (desktop) — Linked shopping list summary
- [ ] Entry picker modal with sections: Recipes (search), Restaurants (search + add), Quick Options (presets), Custom (freeform)
- [ ] `RecipePicker.vue` — Search family recipe library
- [ ] `RestaurantPicker.vue` — Search saved restaurants, add new from Google Maps link
- [ ] `PresetPicker.vue` — One-tap preset buttons
- [ ] Desktop drag-and-drop between day/slot cells (vuedraggable or native)
- [ ] Servings adjustment on recipe entries
- [ ] Cook assignment UI: "Who's cooking?" multi-select, stacked avatars on calendar
- [ ] "View Shopping List" navigation to ShoppingTab
- [ ] Mobile-first responsive, dark mode

### Done Criteria

```bash
npx vite build
# Manual: weekly view renders, add meals via picker, drag-drop (desktop), week nav, shopping list link, dark mode
```

---

## Step 8: Integration & Polish

**Issue:** [#155](https://github.com/gregqualls/kinhold/issues/155)
**Branch:** `feature/food-step-8-integration`
**Depends on:** All previous steps
**Size:** Large (full session)

### Scope

Wire food into every Kinhold integration point: MCP tools, AI chatbot, dashboard, gamification, Global Quick-Add, onboarding, seeder, and documentation.

### Deliverables

**MCP Tools (4 new tools):**
- [ ] `ManageRecipes.php` — list, view, create, update, delete, restore, import_url, search, rate, add_cook_log
- [ ] `ManageShoppingLists.php` — full shopping list management including pre-shop
- [ ] `ManageErrands.php` — simple CRUD + check/clear
- [ ] `ManageMealPlans.php` — meal planning including restaurants, presets, suggestions

**AI Chatbot:**
- [ ] Update `AgentService` / tool registry to include food MCP tools
- [ ] "What's for dinner?" → query today's meal plan
- [ ] "Add milk to the shopping list" → create item
- [ ] "Show me chicken recipes" → search library

**Dashboard:**
- [ ] "Tonight's Dinner" card (today's dinner entry or empty state)
- [ ] Shopping list summary ("12 items, 4 checked")

**Global Quick-Add (PR 0 from spec):**
- [ ] `GlobalQuickAdd.vue` — center "+" button in mobile bottom nav
- [ ] Action sheet: New Task, New Event, Shopping Item, Errand

**Gamification:**
- [ ] Food-related badges (First Recipe, Cookbook, Master Chef, Meal Planner, etc.)
- [ ] Cook log + trip completion earn points (configurable)

**Seeder:**
- [ ] 5-10 sample recipes with ingredients
- [ ] Sample meal plan for current week
- [ ] Shopping list generated from meal plan + manual items
- [ ] 5 staples, 2-3 restaurants, a few errands
- [ ] Food badges, cook logs, ratings

**Documentation:**
- [ ] Update `CLAUDE.md` — new module, schema, routes, file structure
- [ ] Update `docs/ROADMAP.md` — mark Phase F complete
- [ ] Update `CHANGELOG.md`
- [ ] Update `README.md` feature list

### Done Criteria

```bash
php artisan test                        # Full test suite passes
./vendor/bin/pint --test
./vendor/bin/phpstan analyse
npx vite build
# Manual: MCP tools work from Claude Desktop, chatbot answers food questions, dashboard shows dinner card
```

---

## Dependencies Graph

```
Step 1 (Recipe Backend) ──────┬──→ Step 2 (Recipe Import)
                              ├──→ Step 3 (Recipe Frontend) ──→ Step 5 (Shopping FE + Errands)
                              ├──→ Step 4 (Shopping Backend) ─┤
                              │                               └──→ Step 6 (Meal Plan Backend)
                              └──→ Step 6 (Meal Plan Backend) ──→ Step 7 (Meal Plan Frontend)
                                                                          │
Step 8 (Integration) ←── All steps complete ──────────────────────────────┘
```

**Parallel-safe pairs** (can run in separate worktrees):
- Step 2 + Step 4 (recipe import + shopping backend — no file overlap)
- Step 3 + Step 4 (recipe frontend + shopping backend — FE vs BE)
- Step 5 + Step 6 (shopping FE + meal plan BE — if Step 4 merged first)

---

## Reference

- **Full spec:** [docs/FOOD-FEATURES-SPEC.md](FOOD-FEATURES-SPEC.md)
- **Permissions matrix:** See spec § "Permissions Model"
- **Decisions log:** See spec § "Decisions Log" (22 decisions)
- **Out of scope:** See spec § "Scope Boundaries" (cook mode, real-time sync, nutritional info, etc.)
