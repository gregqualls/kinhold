<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shopping\AddShoppingItemRequest;
use App\Http\Requests\Shopping\StoreShoppingListRequest;
use App\Http\Requests\Shopping\StoreStapleRequest;
use App\Http\Requests\Shopping\UpdateShoppingItemRequest;
use App\Http\Requests\Shopping\UpdateShoppingListRequest;
use App\Http\Resources\ShoppingItemResource;
use App\Http\Resources\ShoppingListResource;
use App\Http\Resources\StapleResource;
use App\Models\ProductCatalog;
use App\Models\Recipe;
use App\Models\ShoppingItem;
use App\Models\ShoppingList;
use App\Models\Staple;
use App\Services\ShoppingListService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShoppingListController extends Controller
{
    public function __construct(private ShoppingListService $shoppingListService) {}

    // -------------------------------------------------------------------------
    // Shopping Lists
    // -------------------------------------------------------------------------

    public function index(Request $request): AnonymousResourceCollection
    {
        $lists = ShoppingList::where('family_id', $request->user()->family_id)
            ->orderByDesc('updated_at')
            ->withCount('items')
            ->get();

        return ShoppingListResource::collection($lists);
    }

    public function store(StoreShoppingListRequest $request): JsonResponse
    {
        $this->authorize('create', ShoppingList::class);

        $list = $this->shoppingListService->createList(
            $request->user()->family,
            $request->user(),
            $request->validated('name'),
            $request->validated('store_name'),
        );

        return response()->json(['list' => new ShoppingListResource($list)], 201);
    }

    public function show(Request $request, ShoppingList $shoppingList): JsonResponse
    {
        $this->authorize('view', $shoppingList);

        $shoppingList->load(['items' => fn ($q) => $q->orderBy('category')->orderBy('sort_order')->orderBy('name'), 'creator']);

        return response()->json(['list' => new ShoppingListResource($shoppingList)]);
    }

    public function update(UpdateShoppingListRequest $request, ShoppingList $shoppingList): JsonResponse
    {
        $this->authorize('update', $shoppingList);

        $shoppingList->update($request->validated());

        return response()->json(['list' => new ShoppingListResource($shoppingList)]);
    }

    public function destroy(Request $request, ShoppingList $shoppingList): JsonResponse
    {
        $this->authorize('delete', $shoppingList);

        $shoppingList->delete();

        return response()->json(['message' => 'Shopping list deleted.']);
    }

    public function completeTrip(Request $request, ShoppingList $shoppingList): JsonResponse
    {
        $this->authorize('completeTrip', $shoppingList);

        $this->shoppingListService->completeTrip($shoppingList);

        return response()->json(['list' => new ShoppingListResource($shoppingList->fresh())]);
    }

    // -------------------------------------------------------------------------
    // Shopping Items
    // -------------------------------------------------------------------------

    public function addItem(AddShoppingItemRequest $request, ShoppingList $shoppingList): JsonResponse
    {
        $this->authorize('addItem', $shoppingList);

        $item = $this->shoppingListService->addItem(
            $shoppingList,
            $request->validated(),
            $request->user(),
        );

        return response()->json(['item' => new ShoppingItemResource($item)], 201);
    }

    public function updateItem(UpdateShoppingItemRequest $request, ShoppingItem $shoppingItem): JsonResponse
    {
        $this->authorize('updateItem', $shoppingItem);

        $shoppingItem->update($request->validated());

        return response()->json(['item' => new ShoppingItemResource($shoppingItem)]);
    }

    public function removeItem(Request $request, ShoppingItem $shoppingItem): JsonResponse
    {
        $this->authorize('removeItem', $shoppingItem);

        $shoppingItem->delete();

        return response()->json(['message' => 'Item removed.']);
    }

    public function checkItem(Request $request, ShoppingItem $shoppingItem): JsonResponse
    {
        $this->authorize('checkItem', $shoppingItem);

        $this->shoppingListService->checkItem($shoppingItem, $request->user());

        return response()->json(['item' => new ShoppingItemResource($shoppingItem->fresh())]);
    }

    public function uncheckItem(Request $request, ShoppingItem $shoppingItem): JsonResponse
    {
        $this->authorize('checkItem', $shoppingItem);

        $this->shoppingListService->uncheckItem($shoppingItem);

        return response()->json(['item' => new ShoppingItemResource($shoppingItem->fresh())]);
    }

    public function markOnHand(Request $request, ShoppingItem $shoppingItem): JsonResponse
    {
        $this->authorize('markOnHand', $shoppingItem);

        $this->shoppingListService->markOnHand($shoppingItem);

        return response()->json(['item' => new ShoppingItemResource($shoppingItem->fresh())]);
    }

    public function clearOnHand(Request $request, ShoppingItem $shoppingItem): JsonResponse
    {
        $this->authorize('markOnHand', $shoppingItem);

        $this->shoppingListService->clearOnHand($shoppingItem);

        return response()->json(['item' => new ShoppingItemResource($shoppingItem->fresh())]);
    }

    public function addRecipeToList(Request $request, ShoppingList $shoppingList): JsonResponse
    {
        $this->authorize('addRecipeToList', $shoppingList);

        $validated = $request->validate([
            'recipe_id' => ['required', 'uuid', 'exists:recipes,id'],
            'ingredient_ids' => ['nullable', 'array'],
            'ingredient_ids.*' => ['uuid'],
        ]);

        $recipe = Recipe::where('id', $validated['recipe_id'])
            ->where('family_id', $request->user()->family_id)
            ->firstOrFail();

        $items = $this->shoppingListService->addRecipeIngredients(
            $shoppingList,
            $recipe,
            $request->user(),
            ingredientIds: $validated['ingredient_ids'] ?? null,
        );

        return response()->json(['items' => ShoppingItemResource::collection($items)], 201);
    }

    // -------------------------------------------------------------------------
    // Clear Checked / Move / Recurring
    // -------------------------------------------------------------------------

    public function clearChecked(Request $request, ShoppingList $shoppingList): JsonResponse
    {
        $this->authorize('clearChecked', $shoppingList);

        $result = $this->shoppingListService->clearChecked($shoppingList);

        return response()->json([
            'cleared' => $result['cleared'],
            'recurring_reset' => $result['recurring_reset'],
            'list' => new ShoppingListResource($shoppingList->fresh()->load(['items' => fn ($q) => $q->orderBy('category')->orderBy('sort_order')->orderBy('name')])),
        ]);
    }

    public function moveItem(Request $request, ShoppingItem $shoppingItem): JsonResponse
    {
        $this->authorize('moveItem', $shoppingItem);

        $validated = $request->validate([
            'target_list_id' => ['required', 'uuid', 'exists:shopping_lists,id'],
        ]);

        $targetList = ShoppingList::where('id', $validated['target_list_id'])
            ->where('family_id', $request->user()->family_id)
            ->firstOrFail();

        $item = $this->shoppingListService->moveItem($shoppingItem, $targetList);

        return response()->json(['item' => new ShoppingItemResource($item)]);
    }

    public function toggleRecurring(Request $request, ShoppingItem $shoppingItem): JsonResponse
    {
        $this->authorize('toggleRecurring', $shoppingItem);

        $item = $this->shoppingListService->toggleRecurring($shoppingItem);

        return response()->json(['item' => new ShoppingItemResource($item)]);
    }

    // -------------------------------------------------------------------------
    // Staples (deprecated — use recurring items instead)
    // -------------------------------------------------------------------------

    public function listStaples(Request $request): AnonymousResourceCollection
    {
        $staples = Staple::where('family_id', $request->user()->family_id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return StapleResource::collection($staples);
    }

    public function addStaple(StoreStapleRequest $request): JsonResponse
    {
        $this->authorize('manageStaples', ShoppingList::class);

        $staple = Staple::create([
            'family_id' => $request->user()->family_id,
            'created_by' => $request->user()->id,
            ...$request->validated(),
        ]);

        return response()->json(['staple' => new StapleResource($staple)], 201);
    }

    public function updateStaple(Request $request, Staple $staple): JsonResponse
    {
        $this->authorize('manageStaples', ShoppingList::class);

        abort_if($staple->family_id !== $request->user()->family_id, 403);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'default_quantity' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $staple->update($validated);

        return response()->json(['staple' => new StapleResource($staple)]);
    }

    public function removeStaple(Request $request, Staple $staple): JsonResponse
    {
        $this->authorize('manageStaples', ShoppingList::class);

        abort_if($staple->family_id !== $request->user()->family_id, 403);

        $staple->delete();

        return response()->json(['message' => 'Staple removed.']);
    }

    public function toggleStaple(Request $request, Staple $staple): JsonResponse
    {
        $this->authorize('manageStaples', ShoppingList::class);

        abort_if($staple->family_id !== $request->user()->family_id, 403);

        $staple->update(['is_active' => ! $staple->is_active]);

        return response()->json(['staple' => new StapleResource($staple)]);
    }

    // -------------------------------------------------------------------------
    // Product Catalog
    // -------------------------------------------------------------------------

    public function searchCatalog(Request $request): JsonResponse
    {
        $request->validate(['q' => ['required', 'string', 'max:100']]);

        $q = $request->input('q');

        // Match catalog items
        $catalogResults = ProductCatalog::whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($q).'%'])
            ->orderByRaw('LOWER(name) ASC')
            ->limit(10)
            ->get(['name', 'category']);

        // Also search family's shopping history for custom items
        $historyResults = ShoppingItem::where('family_id', $request->user()->family_id)
            ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($q).'%'])
            ->select('name', 'category')
            ->distinct()
            ->limit(5)
            ->get();

        $combined = $catalogResults->concat($historyResults)
            ->unique('name')
            ->take(15)
            ->values();

        return response()->json(['results' => $combined]);
    }
}
