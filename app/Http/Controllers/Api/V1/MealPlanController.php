<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MealSlot;
use App\Http\Controllers\Controller;
use App\Http\Requests\MealPlan\StoreMealPlanEntryRequest;
use App\Http\Requests\MealPlan\StoreMealPlanRequest;
use App\Http\Requests\MealPlan\StoreMealPresetRequest;
use App\Http\Requests\MealPlan\UpdateMealPlanEntryRequest;
use App\Http\Resources\MealPlanEntryResource;
use App\Http\Resources\MealPlanResource;
use App\Http\Resources\MealPresetResource;
use App\Models\MealPlan;
use App\Models\MealPlanEntry;
use App\Models\MealPreset;
use App\Services\MealPlanService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MealPlanController extends Controller
{
    public function __construct(
        private MealPlanService $mealPlanService,
    ) {}

    /**
     * List all meal plans for the family, optionally filtered by week_start.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', MealPlan::class);

        $family = $request->user()->family;

        $query = MealPlan::where('family_id', $family->id)
            ->with(['entries.recipe', 'entries.restaurant', 'entries.preset', 'shoppingList.items'])
            ->orderByDesc('week_start');

        if ($weekStart = $request->query('week_start')) {
            $query->where('week_start', $weekStart);
        }

        $plans = $query->get();

        return response()->json(['meal_plans' => MealPlanResource::collection($plans)]);
    }

    /**
     * Get or create the current week's meal plan.
     */
    public function current(Request $request): JsonResponse
    {
        $this->authorize('create', MealPlan::class);

        $family = $request->user()->family;
        $plan = $this->mealPlanService->getCurrentWeekPlan($family, $request->user());

        return response()->json(['meal_plan' => new MealPlanResource($plan)]);
    }

    /**
     * Create a meal plan for a specific week.
     */
    public function store(StoreMealPlanRequest $request): JsonResponse
    {
        $this->authorize('create', MealPlan::class);

        $family = $request->user()->family;
        $plan = $this->mealPlanService->getOrCreatePlan(
            $family,
            $request->user(),
            $request->validated('week_start')
        );

        return response()->json(['meal_plan' => new MealPlanResource($plan)], 201);
    }

    /**
     * Show a specific meal plan with all entries.
     */
    public function show(Request $request, string $plan): JsonResponse
    {
        $mealPlan = MealPlan::where('family_id', $request->user()->family_id)
            ->with(['entries.recipe', 'entries.restaurant', 'entries.preset', 'shoppingList.items'])
            ->findOrFail($plan);

        $this->authorize('view', $mealPlan);

        return response()->json(['meal_plan' => new MealPlanResource($mealPlan)]);
    }

    /**
     * Add an entry to a meal plan.
     */
    public function addEntry(StoreMealPlanEntryRequest $request, string $plan): JsonResponse
    {
        $mealPlan = MealPlan::where('family_id', $request->user()->family_id)->findOrFail($plan);

        $this->authorize('addEntry', $mealPlan);

        $entry = $this->mealPlanService->addEntry($mealPlan, $request->validated(), $request->user());

        return response()->json(['entry' => new MealPlanEntryResource($entry)], 201);
    }

    /**
     * Update a meal plan entry.
     */
    public function updateEntry(UpdateMealPlanEntryRequest $request, string $entry): JsonResponse
    {
        $mealPlanEntry = MealPlanEntry::with('mealPlan')->findOrFail($entry);

        $this->authorize('updateEntry', $mealPlanEntry);

        $updated = $this->mealPlanService->updateEntry($mealPlanEntry, $request->validated(), $request->user());

        return response()->json(['entry' => new MealPlanEntryResource($updated)]);
    }

    /**
     * Remove a meal plan entry.
     */
    public function removeEntry(Request $request, string $entry): JsonResponse
    {
        $mealPlanEntry = MealPlanEntry::with('mealPlan')->findOrFail($entry);

        $this->authorize('deleteEntry', $mealPlanEntry);

        $this->mealPlanService->removeEntry($mealPlanEntry);

        return response()->json(null, 204);
    }

    /**
     * Move a meal plan entry to a different day/slot.
     */
    public function moveEntry(Request $request, string $entry): JsonResponse
    {
        $mealPlanEntry = MealPlanEntry::with('mealPlan')->findOrFail($entry);

        $this->authorize('updateEntry', $mealPlanEntry);

        $data = $request->validate([
            'date' => ['required', 'date'],
            'meal_slot' => ['required', 'string', Rule::enum(MealSlot::class)],
        ]);

        $updated = $this->mealPlanService->moveEntry($mealPlanEntry, $data['date'], $data['meal_slot']);

        return response()->json(['entry' => new MealPlanEntryResource($updated)]);
    }

    /**
     * Preview recipe entries (with ingredients) scheduled within the next N days.
     * Used by the meal-plan "Add to shopping list" flow so the user can pick which
     * ingredients to add before committing.
     */
    public function previewShoppingList(Request $request, string $plan): JsonResponse
    {
        $mealPlan = MealPlan::where('family_id', $request->user()->family_id)->findOrFail($plan);

        $this->authorize('view', $mealPlan);

        $data = $request->validate([
            'days' => ['nullable', 'integer', 'min:1', 'max:60'],
            'start' => ['nullable', 'date'],
            'shopping_list_id' => ['nullable', 'uuid'],
        ]);

        $days = $data['days'] ?? 7;
        $start = isset($data['start']) ? Carbon::parse($data['start']) : Carbon::today();
        $end = (clone $start)->addDays($days - 1);

        $entries = $this->mealPlanService->entriesWithIngredientsInRange(
            $mealPlan,
            $start->toDateString(),
            $end->toDateString(),
        );

        // Pre-build a case-insensitive name set for the chosen list (or the
        // plan's auto-list) so the frontend can warn about already-listed items.
        $existingNames = $this->mealPlanService->existingShoppingItemNames(
            $mealPlan,
            $data['shopping_list_id'] ?? null,
        );

        return response()->json([
            'range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
                'days' => $days,
            ],
            'entries' => $entries->map(fn (MealPlanEntry $entry) => [
                'entry_id' => $entry->id,
                'date' => $entry->date->toDateString(),
                'meal_slot' => $entry->meal_slot?->value,
                'recipe' => [
                    'id' => $entry->recipe->id,
                    'title' => $entry->recipe->title,
                ],
                'ingredients' => $entry->recipe->ingredients->map(fn ($i) => [
                    'id' => $i->id,
                    'name' => $i->name,
                    'quantity' => $i->quantity,
                    'unit' => $i->unit,
                    'preparation' => $i->preparation,
                    'is_optional' => (bool) $i->is_optional,
                    'already_on_list' => in_array(strtolower(trim($i->name)), $existingNames, true),
                ])->values(),
            ])->values(),
        ]);
    }

    /**
     * Add a curated selection of recipe ingredients (across multiple entries) to
     * the plan's shopping list. Body: { selections: [{ entry_id, ingredient_ids? }] }
     */
    public function addToShoppingList(Request $request, string $plan): JsonResponse
    {
        $mealPlan = MealPlan::where('family_id', $request->user()->family_id)->findOrFail($plan);

        $this->authorize('update', $mealPlan);

        $data = $request->validate([
            'selections' => ['required', 'array', 'min:1'],
            'selections.*.entry_id' => ['required', 'uuid'],
            'selections.*.ingredient_ids' => ['nullable', 'array'],
            'selections.*.ingredient_ids.*' => ['uuid'],
            'shopping_list_id' => ['nullable', 'uuid'],
        ]);

        $added = $this->mealPlanService->addSelectionsToShoppingList(
            $mealPlan,
            $request->user(),
            $data['selections'],
            $data['shopping_list_id'] ?? null,
        );

        $mealPlan->load(['shoppingList.items']);

        return response()->json([
            'added_count' => $added,
            'shopping_list' => $mealPlan->shoppingList,
        ]);
    }

    /**
     * Force full regeneration of the shopping list for a plan.
     */
    public function generateShoppingList(Request $request, string $plan): JsonResponse
    {
        $mealPlan = MealPlan::where('family_id', $request->user()->family_id)->findOrFail($plan);

        $this->authorize('update', $mealPlan);

        $this->mealPlanService->generateShoppingList($mealPlan, $request->user());

        $mealPlan->load(['shoppingList.items']);

        return response()->json([
            'message' => 'Shopping list generated.',
            'shopping_list' => $mealPlan->shoppingList,
        ]);
    }

    /**
     * List all meal presets for the family.
     */
    public function presets(Request $request): JsonResponse
    {
        $family = $request->user()->family;

        $presets = MealPreset::where('family_id', $family->id)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();

        return response()->json(['presets' => MealPresetResource::collection($presets)]);
    }

    /**
     * Create a new meal preset (parent only).
     */
    public function storePreset(StoreMealPresetRequest $request): JsonResponse
    {
        $this->authorize('managePresets', MealPlan::class);

        $family = $request->user()->family;

        $preset = MealPreset::create(array_merge(
            $request->validated(),
            ['family_id' => $family->id, 'is_system' => false]
        ));

        return response()->json(['preset' => new MealPresetResource($preset)], 201);
    }

    /**
     * Update a meal preset.
     */
    public function updatePreset(Request $request, string $preset): JsonResponse
    {
        $this->authorize('managePresets', MealPlan::class);

        $mealPreset = MealPreset::where('family_id', $request->user()->family_id)->findOrFail($preset);

        $data = $request->validate([
            'label' => ['sometimes', 'string', 'max:255'],
            'icon' => ['sometimes', 'nullable', 'string', 'max:50'],
            'sort_order' => ['sometimes', 'nullable', 'integer'],
        ]);

        $mealPreset->update($data);

        return response()->json(['preset' => new MealPresetResource($mealPreset)]);
    }

    /**
     * Delete a meal preset (non-system only).
     */
    public function deletePreset(Request $request, string $preset): JsonResponse
    {
        $this->authorize('managePresets', MealPlan::class);

        $mealPreset = MealPreset::where('family_id', $request->user()->family_id)->findOrFail($preset);

        if ($mealPreset->is_system) {
            return response()->json(['message' => 'System presets cannot be deleted.'], 422);
        }

        $mealPreset->delete();

        return response()->json(null, 204);
    }
}
