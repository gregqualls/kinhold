<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MealPlan\StoreRestaurantRequest;
use App\Models\FamilyRestaurant;
use App\Models\MealPlan;
use App\Models\Rating;
use App\Models\Restaurant;
use App\Services\RestaurantImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function __construct(
        private RestaurantImportService $importService,
    ) {}

    /**
     * List all restaurants linked to the family (via family_restaurants pivot).
     */
    public function index(Request $request): JsonResponse
    {
        $family = $request->user()->family;

        $restaurants = $family->restaurants()
            ->with(['ratings' => function ($query) use ($family) {
                $query->whereHas('user', fn ($q) => $q->where('family_id', $family->id));
            }])
            ->get()
            ->map(function (Restaurant $restaurant) use ($family) {
                $restaurant->pivot_notes = $restaurant->pivot->notes;
                $restaurant->is_favorite = $restaurant->pivot->is_favorite ?? false;
                $restaurant->family_average_rating = $restaurant->familyAverageRating($family->id);

                return $restaurant;
            });

        return response()->json(['restaurants' => $restaurants]);
    }

    /**
     * Create a restaurant and link it to the family.
     */
    public function store(StoreRestaurantRequest $request): JsonResponse
    {
        $this->authorize('manageRestaurants', MealPlan::class);

        $family = $request->user()->family;

        $restaurant = Restaurant::create($request->validated());

        FamilyRestaurant::create([
            'family_id' => $family->id,
            'restaurant_id' => $restaurant->id,
        ]);

        return response()->json(['restaurant' => $restaurant], 201);
    }

    /**
     * Import a restaurant from a URL and link it to the family.
     */
    public function import(Request $request): JsonResponse
    {
        $this->authorize('manageRestaurants', MealPlan::class);

        $data = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
        ]);

        $family = $request->user()->family;

        $restaurant = $this->importService->importFromUrl($data['url'], $family);

        return response()->json(['restaurant' => $restaurant], 201);
    }

    /**
     * Show a restaurant's details (must be linked to the family).
     */
    public function show(Request $request, string $restaurant): JsonResponse
    {
        $family = $request->user()->family;

        $rest = $family->restaurants()
            ->with(['ratings' => function ($query) use ($family) {
                $query->with('user')
                    ->whereHas('user', fn ($q) => $q->where('family_id', $family->id));
            }])
            ->findOrFail($restaurant);

        $rest->pivot_notes = $rest->pivot->notes;
        $rest->is_favorite = $rest->pivot->is_favorite ?? false;
        $rest->family_average_rating = $rest->familyAverageRating($family->id);

        return response()->json(['restaurant' => $rest]);
    }

    /**
     * Update the family-specific notes and favorite status for a restaurant.
     */
    public function update(Request $request, string $restaurant): JsonResponse
    {
        $this->authorize('manageRestaurants', MealPlan::class);

        $family = $request->user()->family;

        $rest = $family->restaurants()->findOrFail($restaurant);

        $data = $request->validate([
            'notes' => ['sometimes', 'nullable', 'string'],
            'is_favorite' => ['sometimes', 'boolean'],
        ]);

        FamilyRestaurant::where('family_id', $family->id)
            ->where('restaurant_id', $rest->id)
            ->update($data);

        // Reload to get fresh pivot data
        $rest = $family->restaurants()->findOrFail($restaurant);
        $rest->pivot_notes = $rest->pivot->notes;
        $rest->is_favorite = $rest->pivot->is_favorite ?? false;

        return response()->json(['restaurant' => $rest]);
    }

    /**
     * Remove the restaurant from the family (deletes pivot record only).
     */
    public function destroy(Request $request, string $restaurant): JsonResponse
    {
        $this->authorize('manageRestaurants', MealPlan::class);

        $family = $request->user()->family;

        FamilyRestaurant::where('family_id', $family->id)
            ->where('restaurant_id', $restaurant)
            ->firstOrFail()
            ->delete();

        return response()->json(null, 204);
    }

    /**
     * Add or update the authenticated user's rating for a restaurant.
     */
    public function rate(Request $request, string $restaurant): JsonResponse
    {
        $this->authorize('manageRestaurants', MealPlan::class);

        $family = $request->user()->family;

        $rest = $family->restaurants()->findOrFail($restaurant);

        $data = $request->validate([
            'score' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $rating = Rating::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'rateable_type' => Restaurant::class,
                'rateable_id' => $rest->id,
            ],
            [
                'family_id' => $family->id,
                'score' => $data['score'],
            ]
        );

        $rating->load('user');

        return response()->json(['rating' => $rating]);
    }
}
