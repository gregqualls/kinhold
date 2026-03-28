# Kinhold Food Features — Implementation Spec

> **Status:** Reviewed & Approved — ready to implement
> **Author:** Claude + Greg Qualls (spec session, 2026-03-28)
> **Inputs:** Meal Planning Competitive Analysis (March 2026), Kinhold codebase audit, Greg's direction
> **Build order:** PR 0 (Global Quick-Add) → Recipes → Shopping → Errands → Meal Planning → Integration
> **AI scope:** AI-powered import (photo, URL+LLM fallback) ships in Phase 1
> **Offline:** Architecture designed for offline-first; service worker implementation deferred to Phase B (PWA)
> **Target:** Family-first but extensible for open-source community

---

## Architecture Overview

### The Pipeline

The central insight from the competitive analysis is that **no app gets the full pipeline right**:

```
Recipe → Meal Plan → Shopping List
```

Paprika breaks at meal plan → shopping list. Mealime resets the list on any plan edit. Most apps treat these as disconnected features. Kinhold's architecture treats the shopping list as a **live derivative** of the meal plan from day one — not a one-time export.

### Data Flow

```
┌─────────────┐     ┌─────────────┐     ┌──────────────────┐
│   Recipes    │────▶│  Meal Plan  │────▶│  Grocery List     │
│  (library)   │     │  (calendar) │     │  (live derived +  │
│              │     │             │     │   manual items)   │
└─────────────┘     └─────────────┘     └──────────────────┘
       ▲                  │                      ▲
       │                  │                      │
  AI Import          Presets &              Staples List
  (photo, URL,       Restaurants           (persistent,
   social media)     (eating out,           auto-populates)
                      fend for
                      yourself)
                                    ┌──────────────────┐
                                    │  Errands List     │
                                    │  (standalone,     │
                                    │   store-tagged)   │
                                    └──────────────────┘
```

**Critical design decision:** Shopping list items know their source. An item can be:
1. **Recipe-derived** — came from a meal plan entry, linked to a specific recipe ingredient. Displays recipe attribution (e.g., "Shredded cheese — 2 cups for Tacos, 1 cup for Egg Bake")
2. **Manual** — added by hand, standalone
3. **Staple** — from the family's standing staples list, auto-populates each trip

When a meal plan changes, only recipe-derived items update. Manual and staple items are untouched. This is how we avoid Mealime's "reset on edit" problem.

### Two Shopping Experiences

**Grocery List** (inside Food module) — The full pipeline. Connected to meal plans, staples, auto-categorization, pre-shop checklist, freshness dates. Your oldest can pull this up and know exactly what to get.

**Errands List** (outside Food module — always available) — A persistent family notepad for "write it down so we don't forget next time we're out." Items tagged by store (Home Depot, Target, Amazon). Filter by store when heading out. Check off → gone. Dead simple.

### Module Gating

Food features are gated behind a `food` module toggle (consistent with existing `tasks`, `vault`, `calendar`, `chat`, `points`, `badges` toggles). **Enabled by default** for all families (existing and new). Parents can disable in Settings. When disabled, all food routes return 403 and nav items are hidden.

The Errands List lives **outside** the food module — it's always available regardless of food toggle state.

### Navigation

Food features live under a single **"Food"** nav entry with tabbed sub-navigation inside:
- **Recipes** — recipe library
- **Meals** — weekly meal planner
- **Shopping** — grocery list

Errands List gets its own separate nav entry (always visible).

### New Module Structure

Following existing Kinhold patterns, the food features add:

```
app/
├── Models/
│   ├── Recipe.php
│   ├── RecipeIngredient.php
│   ├── RecipeCookLog.php
│   ├── Rating.php                    # Polymorphic: Recipe or Restaurant
│   ├── ShoppingList.php
│   ├── ShoppingItem.php
│   ├── Staple.php
│   ├── ProductCatalog.php            # Global seeded item database
│   ├── ErrandList.php                # Simple family errands
│   ├── ErrandItem.php
│   ├── MealPlan.php
│   ├── MealPlanEntry.php
│   ├── MealPreset.php                # "Fend for Yourself", "Eating Out", etc.
│   ├── Restaurant.php                # Global shared restaurant database
│   └── FamilyRestaurant.php          # Family's saved restaurants with notes
├── Services/
│   ├── RecipeService.php
│   ├── RecipeImportService.php
│   ├── ShoppingListService.php
│   ├── MealPlanService.php
│   └── RestaurantImportService.php
├── Http/
│   ├── Controllers/Api/V1/
│   │   ├── RecipeController.php
│   │   ├── ShoppingListController.php
│   │   ├── ErrandController.php
│   │   ├── MealPlanController.php
│   │   └── RestaurantController.php
│   ├── Requests/
│   │   ├── Recipe/
│   │   ├── ShoppingList/
│   │   ├── Errand/
│   │   └── MealPlan/
│   └── Resources/
│       ├── RecipeResource.php
│       ├── RecipeIngredientResource.php
│       ├── RatingResource.php
│       ├── ShoppingListResource.php
│       ├── ShoppingItemResource.php
│       ├── ErrandItemResource.php
│       ├── MealPlanResource.php
│       ├── MealPlanEntryResource.php
│       └── RestaurantResource.php
├── Mcp/Tools/
│   ├── ManageRecipes.php
│   ├── ManageShoppingLists.php
│   ├── ManageErrands.php
│   └── ManageMealPlans.php
└── Enums/
    ├── MealSlot.php
    ├── ShoppingItemSource.php
    └── RecipeImportType.php

resources/js/
├── stores/
│   ├── recipes.js
│   ├── shopping.js
│   ├── errands.js
│   └── mealPlan.js
├── views/
│   ├── food/
│   │   ├── FoodView.vue              # Tab container for Recipes/Meals/Shopping
│   │   ├── RecipesTab.vue
│   │   ├── RecipeDetailView.vue
│   │   ├── MealPlanTab.vue
│   │   └── ShoppingTab.vue
│   └── errands/
│       └── ErrandsView.vue
├── components/
│   ├── common/
│   │   └── GlobalQuickAdd.vue        # Bottom nav center button (mobile)
│   ├── recipes/
│   │   ├── RecipeCard.vue
│   │   ├── RecipeForm.vue
│   │   ├── RecipeImportModal.vue
│   │   ├── IngredientList.vue
│   │   ├── StepList.vue
│   │   ├── CookLogEntry.vue
│   │   └── FamilyRating.vue
│   ├── shopping/
│   │   ├── ShoppingListItem.vue
│   │   ├── AddItemInput.vue
│   │   ├── AisleGroup.vue
│   │   ├── StaplesManager.vue
│   │   ├── PreShopChecklist.vue
│   │   └── TripControls.vue
│   ├── errands/
│   │   ├── ErrandItem.vue
│   │   ├── StoreFilter.vue
│   │   └── AddErrandInput.vue
│   └── meals/
│       ├── MealCalendar.vue
│       ├── MealSlotCard.vue
│       ├── MealPlanSidebar.vue
│       ├── RecipePicker.vue
│       ├── PresetPicker.vue
│       └── RestaurantPicker.vue
```

---

## Permissions Model (All Food Features)

**This is a cross-cutting concern.** Every food feature must enforce role-based permissions. The existing `parent` / `child` role system applies, but food features need an explicit matrix because kids WILL try to add "butt poop soup" to the recipe library and "1 million dollars for Deacon" to the grocery list.

**Principle:** Children can view and participate (check items, rate, log cooks). Children cannot create, edit, or delete anything that affects the family's shared data without parent oversight. Every write endpoint must check `family_role`.

### Permissions Matrix

| Action | Parent | Child | Notes |
|--------|--------|-------|-------|
| **Recipes** | | | |
| View recipes | ✅ | ✅ | |
| Create recipe (manual) | ✅ | ❌ | |
| Import recipe (URL/photo) | ✅ | ❌ | |
| Edit recipe | ✅ | ❌ | |
| Delete recipe | ✅ | ❌ | |
| Rate recipe | ✅ | ✅ | Everyone's opinion counts |
| Log a cook | ✅ | ✅ | Kids can log that they cooked |
| **Shopping List** | | | |
| View shopping list | ✅ | ✅ | |
| Create shopping list | ✅ | ❌ | |
| Add items to list | ✅ | ❌ | Prevents "1 million dollars" items |
| Edit items | ✅ | ❌ | |
| Delete items | ✅ | ❌ | |
| Check/uncheck items | ✅ | ✅ | Kids can check items off while shopping |
| Mark on-hand (pre-shop) | ✅ | ✅ | Kids can help with the pre-shop check |
| Complete trip | ✅ | ❌ | |
| Manage staples | ✅ | ❌ | |
| **Errands** | | | |
| View errands | ✅ | ✅ | |
| Add errand | ✅ | ❌ | |
| Edit errand | ✅ | ❌ | |
| Delete errand | ✅ | ❌ | |
| Check/uncheck errand | ✅ | ✅ | Kids can check items off |
| **Meal Planning** | | | |
| View meal plan | ✅ | ✅ | |
| Add/edit/remove entries | ✅ | ❌ | |
| Assign cook | ✅ | ❌ | Only parents assign who's cooking |
| Manage presets | ✅ | ❌ | |
| Add/manage restaurants | ✅ | ❌ | |
| Rate restaurant | ✅ | ✅ | Everyone's opinion counts |
| **Module Settings** | | | |
| Enable/disable food module | ✅ | ❌ | |

### Implementation Notes

- Every controller method that mutates data must check `$user->family_role === 'parent'` (or use a policy/middleware)
- Consider a reusable `EnsureParent` middleware or a `$this->authorizeParent()` method on the base controller
- MCP tools must enforce the same permissions (authenticated user's role matters)
- **Future consideration:** Some families may want to grant specific children more access (e.g., the 17-year-old can add to the grocery list). This would require per-feature permission overrides on the user model. Not in v1.0, but don't design in a way that prevents it.

### Retroactive Audit (Existing Modules)

This permissions discipline should be audited across **all existing modules** too. Flag as a separate task:
- [ ] Tasks: Can children create/edit/delete tasks they shouldn't?
- [ ] Vault: Permissions model already exists — verify it's enforced consistently
- [ ] Calendar: Read-only for children? Or can they create events?
- [ ] Chat: Should children be able to use the AI chatbot?
- [ ] Points/Rewards: Can children give themselves kudos or create rewards?

This audit is outside the food spec scope but should be a tracked follow-up item.

---

## PR 0: Global Quick-Add

**Goal:** Promote the existing task FAB (mobile) to an app-wide quick-add center button in the bottom nav bar. Foundation for food features to hook into.

**Scope:**
- Extract existing task FAB into `GlobalQuickAdd.vue` component
- Position as center button in mobile bottom nav bar
- Tap → action sheet with options: **New Task**, **New Event** (stub)
- Extensible: later PRs add **Shopping Item**, **Errand** actions
- Desktop: less prominent (planning-focused layout), could be a header action or omitted

**Docs update:** CLAUDE.md (new component pattern), CHANGELOG

---

## Phase 1: Recipe Management

**Goal:** Build the recipe library with AI-powered import and a unified family rating system. This is the foundation everything else depends on.

### 1.1 Database Schema

#### `recipes` table

| Column | Type | Notes |
|--------|------|-------|
| id | uuid (PK) | HasUuids trait |
| family_id | uuid (FK → families) | Scoped to family |
| created_by | uuid (FK → users) | Who added it |
| title | string(255) | Required |
| description | text, nullable | Short summary |
| servings | integer | Default serving count (e.g., 4) |
| prep_time_minutes | integer, nullable | |
| cook_time_minutes | integer, nullable | |
| total_time_minutes | integer, nullable | Computed or manual override |
| source_url | string(2048), nullable | Original URL if imported |
| source_type | enum | `manual`, `url`, `photo`, `social_media` |
| image_path | string, nullable | Stored in vault attachments |
| instructions | json | Array of step objects: `[{step: 1, text: "..."}, ...]` |
| notes | text, nullable | Personal notes about the recipe |
| is_favorite | boolean, default false | Quick filter |
| sort_order | integer, default 0 | For manual ordering |
| deleted_at | timestamp, nullable | **Soft deletes** — meal plan entries keep working |
| created_at | timestamp | |
| updated_at | timestamp | |

**Design notes:**
- `instructions` as JSON array of step objects (not plain text) enables future cook mode step-by-step display
- `source_type` enum tracks how the recipe entered the system for analytics and re-import
- **Soft deletes:** Recipes use `SoftDeletes` trait. Deleted recipes are hidden from the library but meal plan entries referencing them continue to work. Can be restored.
- **Tags:** Recipes use the **existing polymorphic tag system** (same infrastructure as tasks). Tags are scoped by `taggable_type` so recipe tags ("italian", "quick", "kid-friendly") never bleed into task tags and vice versa.

#### `recipe_ingredients` table

| Column | Type | Notes |
|--------|------|-------|
| id | uuid (PK) | |
| recipe_id | uuid (FK → recipes) | |
| name | string(255) | Ingredient name (normalized: "olive oil" not "extra virgin olive oil from Italy") |
| quantity | decimal(8,3), nullable | Numeric amount |
| unit | string(50), nullable | Free-text unit (cups, tbsp, lbs, etc.) |
| preparation | string(255), nullable | "diced", "minced", "room temperature" |
| group_name | string(100), nullable | For grouping: "For the sauce", "For the crust" |
| is_optional | boolean, default false | |
| sort_order | integer | Preserves recipe order |

**Design notes:**
- `unit` is free-text, not an enum. Recipe units are wildly inconsistent ("a pinch", "1 handful", "2 14oz cans"). Enforcing an enum creates friction during import. Normalization happens at the shopping list layer.
- `group_name` enables sectioned ingredient lists ("For the dough" / "For the filling")
- `quantity` is nullable for cases like "salt to taste"

#### `recipe_cook_logs` table

| Column | Type | Notes |
|--------|------|-------|
| id | uuid (PK) | |
| recipe_id | uuid (FK → recipes) | |
| user_id | uuid (FK → users) | Who cooked |
| cooked_at | date | When they made it |
| servings_made | integer, nullable | How many servings they actually made |
| notes | text, nullable | "Added more garlic, reduced sugar by half" |
| created_at | timestamp | |

**Design notes:**
- Cook log is the "cook log" differentiator from the competitive analysis. No mainstream app does this.
- Simple and lightweight — date, freeform notes per cook session.
- **No rating field** — ratings are handled by the unified rating system (see below).

#### `ratings` table (polymorphic — recipes AND restaurants)

| Column | Type | Notes |
|--------|------|-------|
| id | uuid (PK) | |
| user_id | uuid (FK → users) | Who rated |
| family_id | uuid (FK → families) | Scoped to family |
| rateable_type | string | `Recipe` or `Restaurant` |
| rateable_id | uuid | |
| score | tinyint | 1–5 stars |
| created_at | timestamp | |
| updated_at | timestamp | |

**Unique constraint:** `(user_id, rateable_type, rateable_id)` — one rating per user per item, updatable.

**Design notes:**
- Every family member can rate any recipe or restaurant 1–5 stars
- Family average is displayed on cards (computed, not stored)
- Helps with "What should we have for dinner?" — sort by family average
- Replaces the per-cook-session rating that was on cook logs

### 1.2 API Endpoints

All under `/api/v1/recipes`, middleware: `auth:sanctum`, `module:food`

| Method | Path | Action | Notes |
|--------|------|--------|-------|
| GET | `/recipes` | index | List family recipes, filterable by tag, favorite, search |
| POST | `/recipes` | store | Manual create |
| GET | `/recipes/{recipe}` | show | Full recipe with ingredients, cook logs, ratings |
| PUT | `/recipes/{recipe}` | update | Update recipe |
| DELETE | `/recipes/{recipe}` | destroy | Soft delete |
| POST | `/recipes/{recipe}/restore` | restore | Restore soft-deleted recipe |
| POST | `/recipes/{recipe}/favorite` | toggleFavorite | Toggle is_favorite |
| POST | `/recipes/import/url` | importFromUrl | URL scrape + LLM fallback |
| POST | `/recipes/import/photo` | importFromPhoto | Photo/OCR → structured recipe |
| GET | `/recipes/{recipe}/cook-logs` | cookLogs | List cook log entries |
| POST | `/recipes/{recipe}/cook-logs` | addCookLog | Add a cook log entry |
| POST | `/recipes/{recipe}/rate` | rate | Add/update user's rating (1–5) |
| GET | `/recipes/{recipe}/ratings` | ratings | Get all family member ratings |

**Query params for index:**
- `?search=` — full-text search on title, description, ingredient names
- `?tag=` — filter by tag (exact match)
- `?favorite=1` — only favorites
- `?sort=recent|alpha|rating` — sort order (rating = family average)
- `?per_page=20` — pagination

### 1.3 Recipe Import Service (`RecipeImportService.php`)

This is the differentiator. Three import paths, one service:

#### URL Import (with LLM fallback)
```
1. Fetch the URL content
2. Check for JSON-LD structured data (schema.org/Recipe)
3. If found → parse structured data → return Recipe
4. If not found → send raw HTML to Claude API with extraction prompt
5. Claude returns structured JSON (title, ingredients, steps, times, servings)
6. Validate and normalize the response
7. Create Recipe + RecipeIngredients
```

**LLM prompt pattern:** Send the raw HTML (stripped of nav/footer/ads) with a system prompt that returns a strict JSON schema. Use Claude's tool use / structured output mode for reliable extraction. This is the "resilient import" the competitive analysis identifies as a gap.

**Rate limiting:** URL imports are expensive (LLM call). Rate limit to 20 imports/hour per family.

#### Photo Import
```
1. Accept image upload (JPEG, PNG, HEIC)
2. Send to Claude Vision API with extraction prompt
3. Claude returns structured JSON from the photo
4. Validate and normalize
5. Create Recipe + RecipeIngredients
6. Store original photo as recipe image
```

**Prompt pattern:** "Extract a structured recipe from this image. Return JSON with: title, servings, prep_time, cook_time, ingredients (array of {name, quantity, unit, preparation}), instructions (array of step strings)."

#### Social Media Import (v1.1 — stub the endpoint, implement later)
The endpoint exists but returns a 501 with a message. The data model supports it via `source_type: social_media`. Implementation requires platform-specific extraction logic that's better as a fast-follow.

### 1.4 Frontend Views

#### RecipesTab.vue (library)
- Grid of RecipeCard components (image, title, time, tags, favorite heart, family average rating)
- Search bar + tag filter chips
- "Add Recipe" button → opens RecipeForm or RecipeImportModal
- Empty state: "No recipes yet — add your first one"
- Mobile: single-column card list. Desktop: 2–3 column grid

#### RecipeDetailView.vue
- Hero image (or placeholder)
- Title, description, times, servings (with +/- adjuster that scales ingredients)
- Family rating display + tap-to-rate for current user
- Tabbed sections: Ingredients | Steps | Cook Log
- Ingredient scaling: changing servings recalculates all quantities in real-time (client-side math)
- "Log a Cook" button → opens CookLogEntry modal
- Edit/Delete in context menu

#### RecipeImportModal.vue
- Tab toggle: "From URL" | "From Photo"
- URL tab: paste URL, loading spinner, **full editable preview before saving**
- Photo tab: camera/file upload, loading spinner, **full editable preview before saving**
- **Preview is a full edit form** — not a read-only confirmation. AI extraction will get things wrong (wrong quantities, missed ingredients, garbled steps). The user must be able to fix everything before hitting "Save."
- Preview shows: title, description, servings, times, ingredients (add/remove/edit each), steps (add/remove/edit each), tags
- "Save" only fires after the user explicitly confirms. No auto-save on import.
- Error states: "Couldn't extract a recipe from this URL/photo — try manual entry?"
- **Post-save editing:** Recipes are fully editable at any time after import via the standard edit flow (RecipeForm). Import is just the starting point — the recipe is yours to modify forever.

### 1.5 MCP Tool: ManageRecipes.php

Actions: `list`, `view`, `create`, `update`, `delete`, `restore`, `import_url`, `import_photo`, `search`, `add_cook_log`, `rate`

This lets Greg manage recipes entirely from Claude Desktop/Code.

### 1.6 Testing Plan — Phase 1

**Backend (Feature tests):**
- [ ] Recipe CRUD: create, read, update, soft-delete, restore
- [ ] Recipe scoping: family A cannot see family B's recipes
- [ ] Child role access: children can view recipes, cannot delete
- [ ] Ingredient scaling: verify quantity math at different serving counts
- [ ] URL import: mock HTTP + mock Claude API → verify recipe creation
- [ ] Photo import: mock Claude Vision API → verify recipe creation
- [ ] Cook log: add entry, list entries, verify user association
- [ ] Ratings: add rating, update rating, family average calculation
- [ ] Tags: add tags to recipe using polymorphic tag system, verify no task tag bleed
- [ ] Search: verify full-text search matches title, description, ingredients
- [ ] Tag filtering: single tag, multiple tags
- [ ] Module gating: food module disabled → 403 on all recipe routes

**Frontend (Manual testing checklist):**
- [ ] Recipe list renders, search works, tag filter works
- [ ] Create recipe manually — all fields save correctly
- [ ] Import from URL — preview shows, editable, saves correctly
- [ ] Import from photo — upload works, preview shows, saves correctly
- [ ] Ingredient scaling: change servings, verify quantities update
- [ ] Family ratings: rate a recipe, see average update, see other family member ratings
- [ ] Cook log: add entry, see it in the log list
- [ ] Mobile responsiveness: card grid → single column, touch targets adequate
- [ ] Dark mode: all new components render correctly in dark mode
- [ ] Empty states: no recipes, no cook logs

**Run after Phase 1:**
```bash
php artisan test --filter=Recipe
php artisan test --filter=CookLog
php artisan test --filter=Rating
npx vite build  # verify 0 errors
```

---

## Phase 2: Shopping Lists

**Goal:** Build the grocery shopping list with staples, aisle organization, pre-shop checklist, item catalog for autocomplete, and the data model that connects to meal plans in Phase 3.

**Depends on:** Phase 1 (recipe_ingredients structure informs shopping item normalization)

### 2.1 Database Schema

#### `product_catalog` table (global — shared across all families)

| Column | Type | Notes |
|--------|------|-------|
| id | uuid (PK) | |
| name | string(255) | Canonical item name ("Eggs", "Olive Oil", "Toilet Paper") |
| category | string(100), nullable | Default category ("Dairy", "Pantry", "Household") |
| created_at | timestamp | |

**Design notes:**
- Pre-seeded with ~500 common grocery/household items and their categories
- Powers autocomplete when adding items to any shopping list
- Family-scoped purchase history is tracked separately (via shopping_items) — families don't contribute back to the global catalog (avoids moderation issues)
- Enables future analytics: "You bought 156 eggs this year" (query family's shopping_items history)

#### `shopping_lists` table

| Column | Type | Notes |
|--------|------|-------|
| id | uuid (PK) | |
| family_id | uuid (FK → families) | |
| created_by | uuid (FK → users) | |
| name | string(255) | "Weekly Groceries", "Costco Run", "Party Supplies" |
| store_name | string(255), nullable | Associated store |
| is_active | boolean, default true | Active = current trip. Completed trips set to false |
| completed_at | timestamp, nullable | When the trip was finished |
| created_at | timestamp | |
| updated_at | timestamp | |

#### `shopping_items` table

| Column | Type | Notes |
|--------|------|-------|
| id | uuid (PK) | |
| shopping_list_id | uuid (FK → shopping_lists) | |
| family_id | uuid (FK → families) | Denormalized for efficient queries |
| added_by | uuid (FK → users) | Who added this item |
| name | string(255) | Display name |
| quantity | string(100), nullable | Free-text: "2", "1 lb", "a bunch" |
| category | string(100), nullable | Aisle/category: "Produce", "Dairy", "Frozen" |
| is_checked | boolean, default false | Checked off during shopping |
| checked_by | uuid (FK → users), nullable | Who checked it |
| checked_at | timestamp, nullable | When checked |
| has_on_hand | boolean, default false | Pre-shop checklist: "already have this" |
| source | enum | `manual`, `recipe`, `staple` |
| source_recipe_id | uuid (FK → recipes), nullable | If source=recipe |
| source_recipe_name | string(255), nullable | Denormalized recipe title for display attribution |
| source_ingredient_id | uuid (FK → recipe_ingredients), nullable | Specific ingredient link |
| meal_plan_entry_id | uuid (FK → meal_plan_entries), nullable | Which meal plan entry generated this (Phase 3) |
| needed_date | date, nullable | Date the ingredient is needed (from meal plan entry) |
| notes | string(255), nullable | "Get the organic one", "Brand X only" |
| sort_order | integer, default 0 | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Design notes:**
- `source` enum is the key to the pipeline. Recipe-derived items link back to their origin. When a meal plan changes, only `source=recipe` items with the relevant `meal_plan_entry_id` are affected. **Cascade delete:** when a meal plan entry is deleted, all its shopping items are deleted too (including checked ones).
- `source_recipe_name` is denormalized so the display can show "Shredded cheese — 2 cups for Tacos, 1 cup for Egg Bake" without joining to a soft-deleted recipe.
- `quantity` is free-text because shopping quantities are messy ("2 cans", "1 bunch", "about 3 lbs"). Recipe-derived items include recipe attribution in the display.
- `category` is free-text with smart defaults from the product catalog. Users can override.
- `has_on_hand` powers the pre-shop checklist: before a trip, review items and mark what you already have. Those items are grayed out / hidden during shopping.
- `needed_date` populated from the meal plan entry's date. Enables "needed soonest" sorting and freshness-aware shopping (important in the UK where things go bad quickly).

#### `staples` table

| Column | Type | Notes |
|--------|------|-------|
| id | uuid (PK) | |
| family_id | uuid (FK → families) | |
| created_by | uuid (FK → users) | |
| name | string(255) | "Milk", "Eggs", "Olive Oil" |
| default_quantity | string(100), nullable | "1 gallon", "1 dozen" |
| category | string(100), nullable | Auto-categorized from product catalog |
| is_active | boolean, default true | Inactive staples don't auto-populate |
| sort_order | integer, default 0 | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Design notes:**
- Staples are a standalone concept, not a tag on shopping items. They persist across trips.
- When a new shopping list is created, all active staples are copied into it as `source=staple` items.
- Staples can be individually toggled active/inactive without deleting them.
- Grocery-specific feature — errands list does not use staples.

### 2.2 Shopping List Service (`ShoppingListService.php`)

Key methods:

**`createList(Family $family, User $user, string $name, ?string $store): ShoppingList`**
- Creates the list
- Copies all active staples into it as `source=staple` items
- Returns the list with items loaded

**`addItem(ShoppingList $list, array $data): ShoppingItem`**
- Creates a manual item
- Auto-categorizes by product catalog lookup if no category provided
- Autocomplete matches against product catalog + family's previous items
- Returns the item

**`addRecipeIngredients(ShoppingList $list, Recipe $recipe, ?MealPlanEntry $entry): Collection`**
- Converts recipe ingredients into shopping items
- Sets `source_recipe_name` for display attribution
- Sets `needed_date` from the meal plan entry date
- Groups by recipe: display shows "Shredded cheese — 2 cups for Tacos, 1 cup for Egg Bake"
- Sets `source=recipe`, links `source_recipe_id` and `source_ingredient_id`
- Returns created items

**`removeRecipeItems(ShoppingList $list, MealPlanEntry $entry): void`**
- Removes ALL shopping items linked to a specific meal plan entry (including checked items)
- Used when a meal is removed from the plan

**`checkItem(ShoppingItem $item, User $user): void`**
- Sets `is_checked`, `checked_by`, `checked_at`

**`uncheckItem(ShoppingItem $item): void`**
- Clears check state

**`markOnHand(ShoppingItem $item): void`**
- Sets `has_on_hand = true` (pre-shop checklist)

**`clearOnHand(ShoppingItem $item): void`**
- Sets `has_on_hand = false`

**`completeTrip(ShoppingList $list): void`**
- Sets `is_active = false`, `completed_at = now()`
- Keeps the list for history (enables future analytics)

### 2.3 API Endpoints

All under `/api/v1/shopping`, middleware: `auth:sanctum`, `module:food`

| Method | Path | Action | Notes |
|--------|------|--------|-------|
| GET | `/shopping/lists` | index | List family's shopping lists (active first) |
| POST | `/shopping/lists` | store | Create new list (auto-populates staples) |
| GET | `/shopping/lists/{list}` | show | Full list with items, grouped by category |
| PUT | `/shopping/lists/{list}` | update | Rename, change store |
| DELETE | `/shopping/lists/{list}` | destroy | Delete list |
| POST | `/shopping/lists/{list}/complete` | complete | Mark trip done |
| POST | `/shopping/lists/{list}/items` | addItem | Add manual item |
| PUT | `/shopping/items/{item}` | updateItem | Edit item |
| DELETE | `/shopping/items/{item}` | removeItem | Remove item |
| PATCH | `/shopping/items/{item}/check` | checkItem | Check off |
| PATCH | `/shopping/items/{item}/uncheck` | uncheckItem | Uncheck |
| PATCH | `/shopping/items/{item}/on-hand` | markOnHand | Pre-shop: already have |
| PATCH | `/shopping/items/{item}/need` | clearOnHand | Pre-shop: still need |
| POST | `/shopping/lists/{list}/add-recipe` | addRecipe | Add all ingredients from a recipe |
| GET | `/shopping/staples` | listStaples | Family staples |
| POST | `/shopping/staples` | addStaple | Add a staple |
| PUT | `/shopping/staples/{staple}` | updateStaple | Edit staple |
| DELETE | `/shopping/staples/{staple}` | removeStaple | Delete staple |
| PATCH | `/shopping/staples/{staple}/toggle` | toggleStaple | Toggle active/inactive |
| GET | `/shopping/catalog/search` | searchCatalog | Autocomplete from product catalog + family history |

### 2.4 Frontend Views

#### ShoppingTab.vue
- **Active list** prominently displayed (most families have 1 active list at a time)
- **Pre-shop mode:** toggle to review list before heading out. Mark items you already have (grayed out during shopping)
- Items grouped by category (Produce, Dairy, Meat, Pantry, etc.) with collapsible sections
- Recipe-derived items show attribution: "Shredded cheese — 2 cups for Tacos, 1 cup for Egg Bake"
- **Freshness sorting:** option to sort by "needed soonest" using `needed_date`
- Each item: checkbox, name, quantity, notes — tap to check, long-press for edit/delete
- Checked items: gray, struck-through, sorted to bottom of their category (not hidden)
- Quick-add input at top with **autocomplete** from product catalog + family history
- "Add from Recipe" button → recipe picker modal
- Trip controls: "New List" | "Complete Trip" | "Pre-Shop Check" | active list selector
- **Staples tab** (or section): manage standing items, toggle on/off
- Global quick-add integration: "Shopping Item" action adds to active grocery list

#### ShoppingListItem.vue
- Checkbox + name + quantity on one line
- Recipe attribution line below (if recipe-derived): "for Tacos, for Egg Bake"
- Source indicator: small icon for recipe (link icon), staple (pin icon), or manual
- `needed_date` badge showing days until needed (if from meal plan)
- Swipe-to-delete on mobile
- Tap item name to edit inline

#### PreShopChecklist.vue
- Same list but with "Have it" / "Need it" toggles instead of shopping checkboxes
- Items marked "have it" are grayed/collapsed during actual shopping
- Quick way to review before leaving the house

#### StaplesManager.vue
- List of staples with toggle switches
- Add new staple input (with autocomplete from product catalog)
- Drag to reorder
- Edit name/quantity/category inline

### 2.5 MCP Tool: ManageShoppingLists.php

Actions: `list_lists`, `view_list`, `create_list`, `add_item`, `check_item`, `uncheck_item`, `mark_on_hand`, `remove_item`, `complete_trip`, `add_recipe_to_list`, `list_staples`, `add_staple`, `toggle_staple`

### 2.6 Testing Plan — Phase 2

**Backend:**
- [ ] Shopping list CRUD
- [ ] Auto-populate staples on list creation
- [ ] Add manual item with auto-categorization from product catalog
- [ ] Autocomplete search against catalog + family history
- [ ] Add recipe ingredients to list — verify recipe attribution display
- [ ] Pre-shop checklist: mark on-hand, verify grayed during shopping
- [ ] Freshness: needed_date populated from meal plan entry
- [ ] Check/uncheck items — verify user tracking
- [ ] Complete trip — list becomes inactive
- [ ] Family scoping — family A can't see family B's lists
- [ ] Staple CRUD and toggle
- [ ] Module gating

**Frontend:**
- [ ] Create new list — staples appear automatically
- [ ] Add items manually — autocomplete works, category auto-assigned
- [ ] Pre-shop checklist mode — toggle items, grayed during shopping
- [ ] Check items — visual feedback, moves to bottom
- [ ] Recipe attribution displays correctly on recipe-derived items
- [ ] Needed-date badge shows days until needed
- [ ] "Add from Recipe" — recipe picker works, items added grouped correctly
- [ ] Staples manager — add, edit, toggle, delete
- [ ] Optimistic updates (check immediately, sync in background)
- [ ] Mobile: touch targets, swipe gestures
- [ ] Dark mode

**Run after Phase 2:**
```bash
php artisan test --filter=Shopping
php artisan test --filter=Staple
php artisan test --filter=ProductCatalog
npx vite build
```

---

## Phase 2.5: Errands List

**Goal:** A dead-simple family notepad for non-grocery shopping. "Write it down so we don't forget next time we're at Home Depot."

**Lives outside the food module — always available.**

### 2.5.1 Database Schema

#### `errand_items` table

| Column | Type | Notes |
|--------|------|-------|
| id | uuid (PK) | |
| family_id | uuid (FK → families) | |
| added_by | uuid (FK → users) | |
| name | string(255) | "WD-40", "Picture hooks", "Dog food" |
| store | string(100), nullable | "Home Depot", "Target", "Amazon" |
| notes | string(255), nullable | "Aisle 7", "The blue kind" |
| is_checked | boolean, default false | |
| checked_by | uuid (FK → users), nullable | |
| checked_at | timestamp, nullable | |
| sort_order | integer, default 0 | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Design notes:**
- No separate "list" model. The family has one persistent errands list. Checked items can be cleared in bulk.
- `store` is free-text. Common stores auto-suggest from family's previous entries.
- No categories, no staples, no meal plan connection. Just items + store tag + checkbox.

### 2.5.2 API Endpoints

All under `/api/v1/errands`, middleware: `auth:sanctum` (no module gating — always available)

| Method | Path | Action | Notes |
|--------|------|--------|-------|
| GET | `/errands` | index | List family's errand items, filterable by store |
| POST | `/errands` | store | Add errand item |
| PUT | `/errands/{item}` | update | Edit item |
| DELETE | `/errands/{item}` | destroy | Remove item |
| PATCH | `/errands/{item}/check` | check | Check off |
| PATCH | `/errands/{item}/uncheck` | uncheck | Uncheck |
| POST | `/errands/clear-checked` | clearChecked | Bulk remove all checked items |
| GET | `/errands/stores` | stores | List unique store names for filter/autocomplete |

### 2.5.3 Frontend

#### ErrandsView.vue
- Simple list with store filter tabs/chips at the top ("All", "Home Depot", "Target", "Amazon", etc.)
- Quick-add input: type item name, optional store tag, press enter
- Store autocomplete from previous entries
- Each item: checkbox + name + store badge + notes
- Checked items: strike-through, "Clear checked" button at bottom
- Global quick-add integration: "Errand" action opens a quick-add sheet with name + store
- Separate nav entry (not inside Food tab)

### 2.5.4 MCP Tool: ManageErrands.php

Actions: `list`, `add`, `check`, `uncheck`, `remove`, `clear_checked`

### 2.5.5 Testing Plan

- [ ] Errand item CRUD
- [ ] Store filter
- [ ] Check/uncheck
- [ ] Clear checked (bulk)
- [ ] Family scoping
- [ ] Store autocomplete from previous entries
- [ ] Always accessible (no module gating)
- [ ] Dark mode

---

## Phase 3: Meal Planning

**Goal:** Weekly meal calendar that ties recipes, restaurants, and presets to days/meals and generates a live shopping list.

**Depends on:** Phase 1 (recipes) and Phase 2 (shopping lists, `addRecipeIngredients` service method)

### 3.1 Database Schema

#### `meal_presets` table

| Column | Type | Notes |
|--------|------|-------|
| id | uuid (PK) | |
| family_id | uuid (FK → families) | |
| label | string(255) | "Fend for Yourself", "Eating Out", "Leftovers", "Takeaway" |
| icon | string(50), nullable | Emoji or Lucide icon name |
| is_system | boolean, default false | Seeded defaults vs family-created |
| sort_order | integer, default 0 | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Seeded system presets (4 per family):**
1. "Fend for Yourself" 🍳
2. "Eating Out" 🍽️
3. "Leftovers" 📦
4. "Takeaway" 🥡

Families can add their own: "Grandma's Night", "School Dinner", etc.

#### `restaurants` table (global — shared across all families)

| Column | Type | Notes |
|--------|------|-------|
| id | uuid (PK) | |
| name | string(255) | Restaurant name |
| google_maps_url | string(2048), nullable | Original Google Maps share link |
| menu_url | string(2048), nullable | Link to menu |
| cuisine | string(100), nullable | "Italian", "Chinese", "Indian" |
| address | text, nullable | Full address |
| phone | string(50), nullable | |
| notes | text, nullable | General notes |
| created_at | timestamp | |
| updated_at | timestamp | |

**Design notes:**
- Global table — one entry per restaurant, shared across all families
- Populated via 3-layer import: (1) parse Google Maps URL structure, (2) LLM extraction fallback, (3) manual entry
- Once saved, future families get autocomplete from this database

#### `family_restaurants` table (pivot)

| Column | Type | Notes |
|--------|------|-------|
| id | uuid (PK) | |
| family_id | uuid (FK → families) | |
| restaurant_id | uuid (FK → restaurants) | |
| notes | text, nullable | Family-specific notes ("Kids love the mac and cheese") |
| is_favorite | boolean, default false | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Unique constraint:** `(family_id, restaurant_id)`

#### `meal_plans` table

| Column | Type | Notes |
|--------|------|-------|
| id | uuid (PK) | |
| family_id | uuid (FK → families) | |
| created_by | uuid (FK → users) | |
| week_start | date | Monday of the plan week |
| notes | text, nullable | "Emily is at camp Mon–Wed" |
| shopping_list_id | uuid (FK → shopping_lists), nullable | Auto-linked shopping list |
| created_at | timestamp | |
| updated_at | timestamp | |

**Unique constraint:** `(family_id, week_start)` — one plan per family per week.

#### `meal_plan_entries` table

| Column | Type | Notes |
|--------|------|-------|
| id | uuid (PK) | |
| meal_plan_id | uuid (FK → meal_plans) | |
| recipe_id | uuid (FK → recipes), nullable | For recipe entries |
| restaurant_id | uuid (FK → restaurants), nullable | For eating out entries |
| meal_preset_id | uuid (FK → meal_presets), nullable | For preset entries (fend for yourself, leftovers, etc.) |
| date | date | The specific day |
| meal_slot | enum | `breakfast`, `lunch`, `dinner`, `snack` |
| custom_title | string(255), nullable | Freeform fallback if none of the above fit |
| servings | integer, nullable | Override recipe default servings for this meal |
| assigned_cooks | json, nullable | Array of user UUIDs: who's cooking this meal (supports multiple) |
| notes | string(255), nullable | "Make extra for tomorrow's lunch" |
| sort_order | integer, default 0 | Multiple items per slot |
| created_at | timestamp | |
| updated_at | timestamp | |

**Design notes:**
- A meal plan entry has exactly ONE of: `recipe_id`, `restaurant_id`, `meal_preset_id`, or `custom_title`. This handles all scenarios:
  - **Recipe night:** `recipe_id` set → generates shopping items
  - **Friday eating out:** `restaurant_id` set → shows restaurant info, no shopping items
  - **Fend for yourself:** `meal_preset_id` set → shows preset label, no shopping items
  - **Anything else:** `custom_title` set → freeform text
- `servings` override: if null, uses the recipe's default. If set, the shopping list uses this number for ingredient scaling.
- `meal_slot` enum covers all meals, not just dinner. The competitive analysis flagged "breakfast and lunch parity" as a user wish. (The Qualls family mainly plans dinners, but the system supports everything.)
- `assigned_cooks` is a JSON array of user UUIDs. Supports multiple cooks per meal (mom + dad, parent + kid, etc.). See Cook Assignment section below.

### 3.2 Restaurant Import Service (`RestaurantImportService.php`)

**3-layer import from Google Maps links:**

```
1. Parse the Google Maps share URL
   - Extract place name, coordinates, place ID from URL structure
   - Google Maps URLs often contain the place name directly
2. If parsing insufficient → LLM extraction
   - Fetch the URL content
   - Send to Claude: "Extract restaurant name, address, cuisine, phone, menu URL"
   - Same pattern as recipe URL import
3. Before saving, check global restaurants table for match (by name + address or Google Maps URL)
   - If match found → reuse existing record (autocomplete for the family)
   - If new → create global restaurant record
4. Create family_restaurants pivot entry with family-specific notes
```

**No extra API key required.** Reuses the Anthropic API key already configured for the chatbot.

### 3.3 Cook Assignment (Meal Plan → Tasks + Calendar)

When `assigned_cooks` is set on a meal plan entry, side effects flow automatically. **Multiple family members can be assigned to the same meal** (mom + dad, parent + kid, etc.).

**When cooks are assigned to an entry:**
1. Auto-create a linked task **per assigned cook**: **"Cook: {recipe title}"** (or "Cook: {custom_title}" / "Order: {restaurant name}")
2. Each task is assigned to its respective user, due on the entry's `date`
3. Each task gets configurable points (family setting: `cook_task_points`, default 5)
4. Tasks link back to the meal plan entry via polymorphic `source_type` / `source_id`
5. If family has calendar integration enabled, optionally create a calendar event

**When the entry changes:**
- **Recipe swapped:** all linked task titles update ("Cook: Tacos" → "Cook: Pasta")
- **Entry deleted:** all linked tasks are deleted
- **Cooks changed:** tasks for removed cooks are deleted, tasks for new cooks are created, existing cook tasks are untouched
- **Entry has no recipe** (preset/restaurant): task title uses the preset label or restaurant name ("Order: Nando's", "Dinner: Fend for Yourself")

**When a task is completed:**
- Completing the "Cook: Tacos" task awards points to that user as usual
- Each cook earns points independently
- **Future enhancement (v1.1):** completing the cook task could auto-prompt a cook log entry and trigger the post-meal rating prompt

**Permissions:** Only parents can assign cooks (consistent with the permissions matrix — parents control meal plan entries). Assigned users see the task in their task list like any other task.

**UI:** On the meal plan calendar, each entry shows stacked avatars of the assigned cooks. In the entry picker / edit flow, a "Who's cooking?" multi-select lets parents assign one or more family members.

### 3.4 The Live Pipeline: Meal Plan → Shopping List

This is the most critical piece of the entire food feature set. Here's how it works:

**When a meal plan entry is created (recipe assigned to a day):**
1. `MealPlanService::addEntry()` creates the entry
2. If no shopping list is linked to the plan yet, create one: "{week_start} Groceries"
3. Call `ShoppingListService::addRecipeIngredients()` with the recipe, scaled to `entry.servings`
4. Each shopping item gets `meal_plan_entry_id` and `needed_date` set
5. Non-recipe entries (restaurants, presets, custom) do NOT generate shopping items

**When a meal plan entry is updated (recipe swapped or servings changed):**
1. `MealPlanService::updateEntry()` updates the entry
2. Call `ShoppingListService::removeRecipeItems()` for the old entry (deletes all items for that entry, including checked)
3. Call `ShoppingListService::addRecipeIngredients()` for the new recipe/servings
4. New items get fresh `needed_date` from the entry

**When a meal plan entry is deleted:**
1. Call `ShoppingListService::removeRecipeItems()` for the entry
2. All items with `meal_plan_entry_id` matching this entry are removed (cascade)
3. Manual items and staples are untouched

**Key rule:** Never regenerate the entire list. Always diff. This is how we avoid Mealime's problem.

### 3.5 API Endpoints

All under `/api/v1/meal-plans`, middleware: `auth:sanctum`, `module:food`

| Method | Path | Action | Notes |
|--------|------|--------|-------|
| GET | `/meal-plans` | index | List plans (paginated, most recent first) |
| GET | `/meal-plans/current` | current | Get or create this week's plan |
| GET | `/meal-plans/{plan}` | show | Full plan with entries and linked recipes/restaurants |
| POST | `/meal-plans` | store | Create plan for a specific week |
| PUT | `/meal-plans/{plan}` | update | Update plan notes |
| DELETE | `/meal-plans/{plan}` | destroy | Delete plan + clean up shopping items |
| POST | `/meal-plans/{plan}/entries` | addEntry | Add a recipe/restaurant/preset/custom to a day+slot |
| PUT | `/meal-plan-entries/{entry}` | updateEntry | Move to different day, swap recipe, change servings |
| DELETE | `/meal-plan-entries/{entry}` | removeEntry | Remove from plan + clean shopping list |
| POST | `/meal-plans/{plan}/generate-list` | generateList | Force-regenerate the linked shopping list |
| GET | `/meal-plans/{plan}/shopping-list` | shoppingList | Get the linked shopping list |
| GET | `/meal-plans/presets` | listPresets | Family meal presets |
| POST | `/meal-plans/presets` | createPreset | Add custom preset |
| PUT | `/meal-plans/presets/{preset}` | updatePreset | Edit preset |
| DELETE | `/meal-plans/presets/{preset}` | deletePreset | Delete custom preset (not system presets) |

Restaurant endpoints under `/api/v1/restaurants`:

| Method | Path | Action | Notes |
|--------|------|--------|-------|
| GET | `/restaurants` | index | Family's saved restaurants |
| POST | `/restaurants` | store | Add restaurant (manual or from Google Maps URL) |
| POST | `/restaurants/import` | import | Import from Google Maps URL (3-layer) |
| PUT | `/restaurants/{restaurant}` | update | Update family notes/favorite |
| DELETE | `/restaurants/{restaurant}` | destroy | Remove from family's list (global record stays) |
| POST | `/restaurants/{restaurant}/rate` | rate | Rate the restaurant (1–5, unified rating system) |

### 3.6 Frontend Views

#### MealPlanTab.vue
- **Weekly calendar grid (desktop):** 7 columns (Mon–Sun) × 4 rows (breakfast, lunch, dinner, snack)
- Each cell shows assigned entries as compact cards:
  - Recipe: thumbnail + title + family rating
  - Restaurant: name + cuisine badge
  - Preset: icon + label (e.g., 🍳 Fend for Yourself)
  - Custom: text
- Empty cells have a "+" button → opens entry picker
- **Desktop:** drag-and-drop to move entries between cells
- **Mobile:** day-list view. Tap a day to see its meals. "Add" button per day opens entry picker
- Servings badge on recipe entries — tap to adjust
- Week navigation: prev/next week arrows, "This Week" button
- **Sidebar panel** (desktop) or **bottom sheet** (mobile): shows linked shopping list summary — how many items, how many checked
- "View Shopping List" button → navigates to ShoppingTab with this plan's list

#### Entry Picker (modal/sheet)
Unified picker with sections:
1. **Recipes** — search family recipe library, tap to add
2. **Restaurants** — search saved restaurants or add new (paste Google Maps link)
3. **Quick Options** — preset buttons: Fend for Yourself, Eating Out, Leftovers, Takeaway, + family custom presets
4. **Custom** — freeform text entry

#### MealSlotCard.vue
- Compact card: thumbnail/icon, title, type indicator
- Recipe cards: family average rating, servings count, prep time
- Restaurant cards: cuisine badge, tap to see address/menu link
- Long-press/right-click → context menu: edit servings, swap, remove, move to different day

#### RestaurantPicker.vue
- Search saved family restaurants
- "Add New" → paste Google Maps link or enter manually
- Shows restaurant name, cuisine, family average rating
- Quick-rate from the picker

### 3.7 MCP Tool: ManageMealPlans.php

Actions: `view_current_week`, `view_week`, `add_meal`, `update_meal`, `remove_meal`, `move_meal`, `get_shopping_list`, `list_presets`, `add_restaurant`, `rate_restaurant`, `suggest_meal` (calls Claude to suggest a recipe from the library based on what's been planned recently)

### 3.8 Testing Plan — Phase 3

**Backend:**
- [ ] Create meal plan for a week
- [ ] Add recipe entry → verify shopping items created with recipe attribution and needed_date
- [ ] Add restaurant entry → verify NO shopping items created
- [ ] Add preset entry (fend for yourself) → verify NO shopping items created
- [ ] Update entry (swap recipe) → verify old items removed, new items added
- [ ] Delete entry → verify all shopping items for that entry removed (including checked)
- [ ] Custom entries (no recipe) don't create shopping items
- [ ] Servings override → verify ingredient quantities scaled correctly
- [ ] One plan per family per week constraint
- [ ] Shopping list auto-creation on first recipe entry
- [ ] Family scoping
- [ ] Module gating
- [ ] Restaurant import: URL parsing, LLM fallback, global DB upsert
- [ ] Restaurant ratings (unified system)
- [ ] Cook assignment: assign 1 cook → task auto-created with points
- [ ] Cook assignment: assign 2 cooks → separate task per cook, both get points
- [ ] Cook assignment: swap recipe → all linked task titles update
- [ ] Cook assignment: remove entry → all linked tasks deleted
- [ ] Cook assignment: change cooks (remove one, add another) → correct tasks created/deleted
- [ ] Cook assignment: only parents can assign (child role blocked)
- [ ] Meal presets: seeded defaults, family custom, CRUD

**Frontend:**
- [ ] Weekly calendar renders correctly with all entry types (recipe, restaurant, preset, custom)
- [ ] Add meal via unified entry picker
- [ ] Desktop drag-and-drop between days/slots
- [ ] Mobile day-list view with add button per day
- [ ] Servings adjustment reflects in shopping list
- [ ] Remove meal — shopping list updates
- [ ] Week navigation (prev/next/this week)
- [ ] Restaurant picker: search saved, add new from Google Maps link
- [ ] Preset quick-options: one-tap to add
- [ ] Cook assignment: "Who's cooking?" multi-select on entry, stacked avatars show on calendar
- [ ] Shopping list link from meal plan
- [ ] Dark mode

**Integration test (the full pipeline):**
- [ ] Add 5 recipes to a week → verify shopping list has all ingredients with recipe attribution
- [ ] Add Friday eating out (restaurant) → verify no shopping items
- [ ] Add "Fend for Yourself" to Wednesday → verify no shopping items
- [ ] Swap one recipe → verify list updated (old removed, new added)
- [ ] Needed dates correct on all items
- [ ] Complete the shopping trip → start next week → staples appear, no stale items

```bash
php artisan test --filter=MealPlan
php artisan test --filter=Restaurant
php artisan test --filter=MealPreset
php artisan test --filter=Shopping  # re-run, now with recipe integration
npx vite build
```

---

## Phase 4: Integration & Polish

**Goal:** Wire food features into the existing Kinhold ecosystem and polish the experience.

**Depends on:** Phases 1–3 complete

### 4.1 MCP Tools

Ship all MCP tools:
- `ManageRecipes.php` — list, view, create, update, delete, restore, import, search, rate, add_cook_log
- `ManageShoppingLists.php` — full shopping list management including pre-shop checklist
- `ManageErrands.php` — simple errand CRUD
- `ManageMealPlans.php` — meal planning including restaurants, presets, suggestions

Greg manages Kinhold entirely through Claude Desktop/Code — MCP parity is a first-class priority.

### 4.2 Chatbot Integration

Update `ChatbotService.php` to include food context:

- "What's for dinner tonight?" → query today's meal plan entries
- "Add milk to the shopping list" → create shopping item on active grocery list
- "Add WD-40 to the errands for Home Depot" → create errand item
- "Show me the shopping list" → return current active list
- "What recipes do we have for chicken?" → search recipe library
- "Plan tacos for Tuesday dinner" → create meal plan entry (if recipe exists)
- "What did we have last week?" → query last week's meal plan
- "Rate the tacos 4 stars" → add/update rating

### 4.3 Dashboard Integration

Add a food section to the DashboardView:

- **Tonight's Dinner** card: shows today's dinner entry (recipe, restaurant, or preset). "Nothing planned — add a meal?" if empty.
- **Shopping List** summary: "12 items, 4 checked" with tap to open
- **Quick Actions**: "Plan this week's meals" | "Start a shopping trip"

### 4.4 Gamification Integration

Optional food-related point events and badges:

**Point events:**
- Log a cook → earn points (configurable per family)
- Complete a shopping trip → earn points

**Badges (seeded):**
- "First Recipe" — add your first recipe
- "Cookbook" — add 10 recipes
- "Master Chef" — add 50 recipes
- "Meal Planner" — plan a full week (7 dinners)
- "Shop 'Til You Drop" — complete 10 shopping trips
- "Kitchen Critic" — log 20 cooks

### 4.5 Onboarding Extension

Add an optional food onboarding step (after the existing 5 steps, for families that enable the food module):

- "Add your first recipe" — URL import or manual
- "Set up your staples" — preset common items, toggle on/off
- "Save your favorite restaurants" — paste Google Maps links

### 4.6 Navigation Updates

- Add **"Food"** to the sidebar/bottom nav (icon: utensils from Lucide) → opens FoodView with tab sub-navigation (Recipes | Meals | Shopping)
- Add **"Errands"** to the sidebar/bottom nav (icon: clipboard-list from Lucide) → opens ErrandsView
- **Mobile bottom nav:** center button is the Global Quick-Add (from PR 0) with actions for Task, Event, Shopping Item, Errand

### 4.7 Documentation & Marketing

- **README.md** — update feature list with food features
- **Landing page** — add food/meal planning to the feature showcase
- **CLAUDE.md** — update module list, schema, file structure, API routes
- **ROADMAP.md** — mark food features complete, update phases
- **CHANGELOG.md** — comprehensive entry for the food release

### 4.8 Seeder Updates

Add to `DatabaseSeeder.php`:

- 5–10 sample recipes with ingredients (varied: Italian, Mexican, quick dinners, breakfast)
- A sample meal plan for the current week with mix of recipes, a restaurant, and a "Fend for Yourself"
- A shopping list generated from the meal plan + some manual items
- 5 staples (Milk, Eggs, Bread, Butter, Coffee)
- A couple of cook log entries
- Family ratings on some recipes and restaurants
- 4 system meal presets per family
- 2–3 sample restaurants
- A few errand items tagged to different stores
- Food-related badges (seeded like existing badges)
- Product catalog seeded with ~500 common items

### 4.9 Testing Plan — Phase 4

- [ ] Chatbot: ask food-related questions, verify context returned
- [ ] Dashboard: dinner card shows correct meal, shopping summary accurate
- [ ] Badges: trigger each food badge, verify it appears
- [ ] Points: log a cook with points enabled, verify transaction
- [ ] Onboarding: new family sees food setup step
- [ ] Navigation: Food tab accessible with sub-tabs, Errands accessible separately
- [ ] Global quick-add: all actions work (task, event, shopping item, errand)
- [ ] Dark mode across all new integration points
- [ ] MCP tools: test all food-related MCP operations from Claude Desktop
- [ ] Landing page: food features showcased
- [ ] README: accurate and up to date

---

## Data Model Relationships (Complete)

```
Family
├── has many Recipes (soft deletes)
│   ├── has many RecipeIngredients
│   ├── has many RecipeCookLogs
│   ├── has many Ratings (polymorphic)
│   └── uses polymorphic Tags (scoped by taggable_type)
├── has many ShoppingLists
│   └── has many ShoppingItems
│       ├── belongs to Recipe (optional, via source_recipe_id)
│       ├── belongs to RecipeIngredient (optional)
│       └── belongs to MealPlanEntry (optional, cascade delete)
├── has many Staples
├── has many ErrandItems
├── has many MealPlans
│   ├── belongs to ShoppingList (optional)
│   └── has many MealPlanEntries
│       ├── belongs to Recipe (optional)
│       ├── belongs to Restaurant (optional)
│       └── belongs to MealPreset (optional)
├── has many MealPresets
├── has many FamilyRestaurants → Restaurant (global)
│   └── has many Ratings (polymorphic)
└── references ProductCatalog (global, read-only)
```

---

## Migration Sequence

Run in this order (Phase 1 → 2 → 2.5 → 3 → 4):

```
2026_03_29_000001_create_recipes_table.php              (with soft deletes)
2026_03_29_000002_create_recipe_ingredients_table.php
2026_03_29_000003_create_recipe_cook_logs_table.php
2026_03_29_000004_create_ratings_table.php               (polymorphic: recipes + restaurants)
2026_03_29_000005_create_product_catalog_table.php        (global item database)
2026_03_29_000006_create_shopping_lists_table.php
2026_03_29_000007_create_shopping_items_table.php         (with has_on_hand, needed_date, source_recipe_name)
2026_03_29_000008_create_staples_table.php
2026_03_29_000009_create_errand_items_table.php
2026_03_29_000010_create_meal_presets_table.php
2026_03_29_000011_create_restaurants_table.php            (global)
2026_03_29_000012_create_family_restaurants_table.php     (pivot)
2026_03_29_000013_create_meal_plans_table.php
2026_03_29_000014_create_meal_plan_entries_table.php      (FKs to recipe, restaurant, preset)
2026_03_29_000015_add_food_module_to_families.php         (adds 'food' toggle, enabled by default)
```

All migrations include `family_id` FK with cascade delete (where applicable).

---

## Configuration

### New .env Variables

```env
# Recipe import rate limit (per family per hour)
RECIPE_IMPORT_RATE_LIMIT=20

# Anthropic API key is already in .env for the chatbot
# Recipe + restaurant import reuses the same key — no additional config needed
```

### config/shopping_categories.php

Auto-categorization lookup table. Ships as part of the product_catalog seeder with ~500 common items mapped to categories. Families can override per-item.

---

## PR Implementation Sequence

Each PR is self-contained, independently mergeable and testable. **Every PR updates relevant docs** (README, CLAUDE.md, CHANGELOG, ROADMAP, onboarding, landing page — whichever apply).

| PR | Name | Key Deliverables |
|----|------|-----------------|
| **0** | **Global Quick-Add** | Extract task FAB → app-wide bottom-nav center button. Actions: Task, Event (stubs for Shopping Item + Errand). |
| **1** | **Recipe Foundation** | Migrations (recipes, ingredients, cook_logs, ratings). Models, RecipeService, controller, form requests, resources, routes, tags integration. Feature tests. Module gating setup. |
| **2** | **Recipe Import** | RecipeImportService (URL + photo). LLM extraction prompts. Rate limiting. Feature tests with mocked API. |
| **3a** | **Recipe Frontend: Core** | Pinia store. RecipesTab, RecipeCard, RecipeForm. Router + Food tab navigation shell. |
| **3b** | **Recipe Frontend: Detail** | RecipeDetailView, IngredientList, StepList, CookLogEntry, FamilyRating. Ingredient scaling. |
| **3c** | **Recipe Frontend: Import** | RecipeImportModal (URL + photo tabs). Preview + edit before save. Error states. |
| **4** | **Shopping Backend** | Migrations (lists, items, staples, product_catalog). Models, ShoppingListService, controller, routes. Auto-categorization, catalog autocomplete, pre-shop checklist. Feature tests. Product catalog seeder. |
| **5** | **Shopping Frontend** | Pinia store. ShoppingTab with category grouping, recipe attribution, freshness dates. PreShopChecklist, StaplesManager, AddItemInput with autocomplete. Quick-add integration. |
| **6** | **Errands List** | Migration (errand_items). Model, controller, routes. ErrandsView with store filter. Quick-add integration. Nav entry. Always available (no module gating). |
| **7** | **Meal Plan Backend** | Migrations (plans, entries, presets, restaurants, family_restaurants). Models, MealPlanService, RestaurantImportService. **The live pipeline.** Preset seeder. Feature tests including full pipeline integration. |
| **8** | **Meal Plan Frontend** | Pinia store. MealPlanTab (weekly grid desktop, day-list mobile). Entry picker (recipes, restaurants, presets, custom). Desktop drag-and-drop. Restaurant import from Google Maps. |
| **9** | **Integration** | All MCP tools. Chatbot context. Dashboard food cards. Badges + points. Onboarding food step. Full seeder updates. Landing page + README + final docs sweep. |

---

## Decisions Log

| # | Decision | Rationale |
|---|----------|-----------|
| 1 | Recipe tags use existing polymorphic tag system | One tag infrastructure. Scoped by `taggable_type` — no bleed between recipe and task tags. |
| 2 | Shopping items show recipe attribution | "Shredded cheese — 2 cups for Tacos, 1 cup for Egg Bake". Denormalized `source_recipe_name` for display. |
| 3 | Cascade delete shopping items with meal plan entries | Simplest approach. If the meal is gone, its shopping items go too. Pre-shop checklist covers "already have" separately. |
| 4 | Defer real-time sync to v1.1 | Optimistic local updates + pull-to-refresh. No WebSocket infrastructure needed for v1.0. |
| 5 | Drag-and-drop desktop only | Mobile gets "Add Recipe" button per day with search. Avoids complex touch handling. |
| 6 | Single "Food" nav entry with tabs | Recipes, Meals, Shopping as sub-tabs. Errands is a separate nav item. Reduces nav clutter. |
| 7 | Soft-delete recipes | `deleted_at` column. Meal plan entries keep working with soft-deleted recipes. Can be restored. |
| 8 | Pre-shop checklist on shopping list | `has_on_hand` boolean. Review before a trip, mark what you already have. Grayed during shopping. |
| 9 | Freshness filter in v1.0 | `needed_date` from meal plan entry. Sort by "needed soonest". Lightweight — data already available. |
| 10 | Defer cook mode to v1.1 | Recipe detail shows all steps inline. Full-screen cook mode with wake lock and timers is a v1.1 enhancement. |
| 11 | Unified polymorphic rating system | One `ratings` table for recipes AND restaurants. Each family member rates 1–5. Family average displayed. |
| 12 | Restaurants: lean model, 3-layer import, shared globally | Parse Google Maps URL → LLM fallback → save to global DB. All families benefit from the growing database. |
| 13 | Meal presets: seeded quick options + family custom | 4 system presets (Fend for Yourself, Eating Out, Leftovers, Takeaway). Families add their own. |
| 14 | Errands list: separate, simple, always available | Outside food module. Persistent family notepad, store-tagged, filter by store. No complexity. |
| 15 | Product catalog: seeded global, family history private | ~500 common items pre-seeded. Powers autocomplete. Family purchase history is private. Enables future analytics. |
| 16 | Global quick-add: bottom nav center button (mobile) | Extends existing task FAB. Tap → choose: Task, Event, Shopping Item, Errand. Desktop less prominent. |
| 17 | Every PR updates relevant docs | README, CLAUDE.md, CHANGELOG, ROADMAP, onboarding, landing page — checked per PR. |
| 18 | Food module enabled by default | All families get food features when deployed. Parents can disable in Settings. |
| 19 | Granular parent/child permissions on every feature | Children can view and participate (check items, rate, log cooks) but cannot create/edit/delete shared data. Every write endpoint enforces role. Retroactive audit of existing modules flagged as follow-up. |
| 20 | Import preview is a full edit form | AI extraction will get things wrong. Preview before save is fully editable. Recipes remain editable forever after import. |
| 21 | Post-meal rating prompts deferred to v1.1/v1.2 | Uber-style "Rate Tuesday's tacos?" notification. v1.1: bell notification on next login. v1.2: push notification via PWA. |
| 22 | Cook assignment on meal plan entries | `assigned_cooks` JSON array (supports multiple). Auto-creates a linked task per cook with points. Tasks update/delete when entry changes. Parents only. |

---

## Scope Boundaries — What's NOT in This Spec

| Feature | Reason | When |
|---------|--------|------|
| Cook mode (full-screen step-by-step) | Good feature, not essential for pipeline. Recipe detail shows steps inline. | v1.1 |
| Real-time cross-device sync | Requires WebSocket infrastructure. Optimistic updates cover 90%. | v1.1 |
| Offline-first service worker | Architecture designed for it, implementation belongs in Phase B (PWA) | Phase B |
| Social media import (TikTok/Instagram) | Endpoint stubbed, requires platform-specific work | v1.1 |
| Nutritional information | Requires data licensing or unreliable estimation | Future |
| Leftover/batch-cook tracking | Adds schema complexity — better as v1.1 enhancement | v1.1 |
| Auto-generate meal plans from preferences | Complex AI feature (Mealime/Eat This Much territory) | Future |
| Per-store aisle customization | Auto-categorization covers 80% of the value | v1.1 |
| Grocery delivery integration (Instacart) | Third-party dependency | Phase D |
| Recipe sharing outside the family | Outside the family-hub model | Future |
| Template weeks (save/reuse meal plans) | Low-cost addition once meal planning works | v1.1 |
| Price tracking / running total | Outside core pipeline | Future |
| Barcode scanning | Niche, adds UI complexity | Skip |
| Post-meal rating prompts | Uber-style "Rate Tuesday's tacos?" notification after a meal is cooked. Could be a bell notification on next login (v1.1) or push notification via PWA (v1.2). Drives rating adoption without nagging during dinner. | v1.1 / v1.2 |
| Pantry inventory management | Pre-shop checklist covers the core need. Full pantry is a separate feature. | Future |
| Year-in-review analytics | Product catalog + shopping history enables this. UI is a separate feature. | Future |

---

## Success Criteria

When all phases are complete, these workflows work end-to-end:

1. Greg imports a recipe from a URL — Claude extracts it cleanly, ingredients structured
2. Shannon takes a photo of her mom's recipe card — it's digitized and in the library
3. Everyone rates their favorite recipes — family averages help decide what to cook
4. Greg opens the meal planner and adds recipes to each dinner this week
5. Friday night is "Eating Out" — he adds their favorite Indian restaurant from a Google Maps link
6. Wednesday is "Fend for Yourself" — one tap, it's on the calendar
7. A grocery list auto-generates with all ingredients, showing recipe attribution ("2 cups for Tacos, 1 cup for Egg Bake")
8. Items show when they're needed ("needed in 3 days") for freshness-aware shopping
9. Before heading to the store, the oldest does a pre-shop check — marks eggs and milk as "already have"
10. Shannon adds "Paper towels" manually — it's categorized and won't be touched when Greg swaps Wednesday's recipe
11. At the store, items are checked off. Optimistic updates feel instant.
12. Greg adds "WD-40" and "picture hooks" to the errands list tagged "Home Depot" — next time they're there, they check the list
13. Greg swaps Thursday's dinner — the shopping list updates cleanly
14. From Claude Desktop, Greg asks "What's for dinner tonight?" and gets the answer
15. After cooking, Mason logs a cook with a note about adding more cheese
16. At the end of the week, Greg completes the trip and starts fresh — staples auto-populate the new list

That's the full pipeline. No other consumer app does all of this today.
