<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MealPlan\StoreRestaurantRequest;
use App\Models\FamilyRestaurant;
use App\Models\MealPlan;
use App\Models\Rating;
use App\Models\Restaurant;
use App\Models\Tag;
use App\Services\RestaurantImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RestaurantController extends Controller
{
    public function __construct(
        private RestaurantImportService $importService,
    ) {}

    /**
     * List all restaurants linked to the family (via family_restaurants pivot).
     * Optional ?tag=<uuid> filter restricts to restaurants carrying that tag.
     */
    public function index(Request $request): JsonResponse
    {
        $family = $request->user()->family;
        $tagId = $request->query('tag');

        $query = $family->restaurants()
            ->with([
                'tags' => fn ($q) => $q->where('tags.family_id', $family->id),
                'ratings' => fn ($q) => $q->whereHas('user', fn ($u) => $u->where('family_id', $family->id)),
            ]);

        if ($tagId) {
            $query->whereHas('tags', fn ($q) => $q->where('tags.id', $tagId));
        }

        $restaurants = $query->get()
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
        $validated = $request->validated();
        $tagIds = $validated['tag_ids'] ?? [];
        unset($validated['tag_ids']);

        $restaurant = Restaurant::create($validated);

        FamilyRestaurant::create([
            'family_id' => $family->id,
            'restaurant_id' => $restaurant->id,
        ]);

        if ($tagIds) {
            $restaurant->tags()->sync($this->scopedTagIds($family->id, $tagIds));
        }

        $restaurant->load(['tags' => fn ($q) => $q->where('tags.family_id', $family->id)]);

        return response()->json(['restaurant' => $restaurant], 201);
    }

    /**
     * Import a restaurant from a URL and link it to the family.
     *
     * With ?preview=1, returns extracted data without saving (for edit-before-save flow).
     * Without preview, saves directly (backwards compatible).
     */
    public function import(Request $request): JsonResponse
    {
        $this->authorize('manageRestaurants', MealPlan::class);

        $data = $request->validate([
            'url' => ['required', 'url:http,https', 'max:2048'],
        ]);

        $family = $request->user()->family;

        // Preview mode: extract data but don't save
        if ($request->boolean('preview')) {
            $extracted = $this->importService->extractFromUrl($data['url']);

            return response()->json(['preview' => $extracted]);
        }

        // Direct save mode
        $restaurant = $this->importService->importFromUrl($data['url'], $family);

        return response()->json(['restaurant' => $restaurant], 201);
    }

    /**
     * Upload a restaurant photo. Returns the public URL.
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $this->authorize('manageRestaurants', MealPlan::class);

        $request->validate([
            'photo' => ['required', 'file', 'mimes:jpeg,png,webp,heic,heif', 'max:10240'],
        ]);

        $path = $this->importService->storeUploadedImage($request->file('photo'));

        return response()->json([
            'path' => $path,
            'url' => '/storage/'.$path,
        ]);
    }

    /**
     * Show a restaurant's details (must be linked to the family).
     */
    public function show(Request $request, string $restaurant): JsonResponse
    {
        $family = $request->user()->family;

        $rest = $family->restaurants()
            ->with([
                'tags' => fn ($q) => $q->where('tags.family_id', $family->id),
                'ratings' => fn ($q) => $q->with('user')->whereHas('user', fn ($u) => $u->where('family_id', $family->id)),
            ])
            ->findOrFail($restaurant);

        $rest->pivot_notes = $rest->pivot->notes;
        $rest->is_favorite = $rest->pivot->is_favorite ?? false;
        $rest->family_average_rating = $rest->familyAverageRating($family->id);

        return response()->json(['restaurant' => $rest]);
    }

    /**
     * Update a restaurant's details and/or family-specific notes.
     */
    public function update(Request $request, string $restaurant): JsonResponse
    {
        $this->authorize('manageRestaurants', MealPlan::class);

        $family = $request->user()->family;

        $rest = $family->restaurants()->findOrFail($restaurant);

        $data = $request->validate([
            // Core restaurant fields
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'cuisine' => ['sometimes', 'nullable', 'string', 'max:100'],
            'address' => ['sometimes', 'nullable', 'string'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'google_maps_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'menu_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'image_url' => ['sometimes', 'nullable', 'string', 'max:2048'],
            // Family-specific pivot fields
            'notes' => ['sometimes', 'nullable', 'string'],
            'is_favorite' => ['sometimes', 'boolean'],
            // Tags (family-scoped)
            'tag_ids' => ['sometimes', 'array'],
            'tag_ids.*' => ['uuid', Rule::exists('tags', 'id')->where('family_id', $family->id)],
        ]);

        // Split into core vs pivot fields
        $coreFields = ['name', 'cuisine', 'address', 'phone', 'google_maps_url', 'menu_url', 'image_url'];
        $pivotFields = ['notes', 'is_favorite'];

        $coreData = array_intersect_key($data, array_flip($coreFields));
        $pivotData = array_intersect_key($data, array_flip($pivotFields));

        if ($coreData) {
            $rest->update($coreData);
        }

        if ($pivotData) {
            FamilyRestaurant::where('family_id', $family->id)
                ->where('restaurant_id', $rest->id)
                ->update($pivotData);
        }

        if (array_key_exists('tag_ids', $data)) {
            $rest->tags()->sync($this->scopedTagIds($family->id, $data['tag_ids'] ?? []));
        }

        // Reload to get fresh data
        $rest = $family->restaurants()
            ->with(['tags' => fn ($q) => $q->where('tags.family_id', $family->id)])
            ->findOrFail($restaurant);
        $rest->pivot_notes = $rest->pivot->notes;
        $rest->is_favorite = $rest->pivot->is_favorite ?? false;
        $rest->family_average_rating = $rest->familyAverageRating($family->id);

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

    /**
     * Filter the given tag IDs down to ones that belong to the given family.
     * Prevents cross-family tag attachment via spoofed IDs.
     */
    private function scopedTagIds(int|string $familyId, array $tagIds): array
    {
        if (empty($tagIds)) {
            return [];
        }

        return Tag::whereIn('id', $tagIds)
            ->where('family_id', $familyId)
            ->pluck('id')
            ->all();
    }
}
