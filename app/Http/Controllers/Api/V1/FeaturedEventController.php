<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeaturedEventResource;
use App\Models\FeaturedEvent;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeaturedEventController extends Controller
{
    /**
     * List upcoming featured events for the family (next 30 days).
     */
    public function index(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();

        $events = FeaturedEvent::where('family_id', $family->id)
            ->where('is_active', true)
            ->where('event_date', '>=', Carbon::today())
            ->where('event_date', '<=', Carbon::today()->addDays(30))
            ->orderBy('event_date')
            ->orderBy('event_time')
            ->with('creator:id,name,avatar')
            ->get();

        return response()->json([
            'featured_events' => FeaturedEventResource::collection($events),
        ]);
    }

    /**
     * Create a featured event (parent only).
     */
    public function store(Request $request): JsonResponse
    {
        if (!$request->user()->isParent()) {
            return response()->json(['message' => 'Only parents can create featured events'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'event_date' => 'required|date|after_or_equal:today',
            'event_time' => 'nullable|date_format:H:i',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $family = $request->user()->currentFamily()->firstOrFail();

        $event = FeaturedEvent::create([
            'family_id' => $family->id,
            'created_by' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'event_date' => $validated['event_date'],
            'event_time' => $validated['event_time'] ?? null,
            'icon' => $validated['icon'] ?? "\u{1F389}",
            'color' => $validated['color'] ?? '#8B5CF6',
        ]);

        $event->load('creator:id,name,avatar');

        return response()->json([
            'featured_event' => new FeaturedEventResource($event),
        ], 201);
    }

    /**
     * Update a featured event (parent only).
     */
    public function update(Request $request, FeaturedEvent $featuredEvent): JsonResponse
    {
        if (!$request->user()->isParent()) {
            return response()->json(['message' => 'Only parents can update featured events'], 403);
        }

        // Ensure event belongs to user's family
        $family = $request->user()->currentFamily()->firstOrFail();
        if ($featuredEvent->family_id !== $family->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'event_date' => 'sometimes|date',
            'event_time' => 'nullable|date_format:H:i',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'sometimes|boolean',
        ]);

        $featuredEvent->update($validated);

        return response()->json([
            'featured_event' => new FeaturedEventResource($featuredEvent),
        ]);
    }

    /**
     * Delete a featured event (parent only).
     */
    public function destroy(Request $request, FeaturedEvent $featuredEvent): JsonResponse
    {
        if (!$request->user()->isParent()) {
            return response()->json(['message' => 'Only parents can delete featured events'], 403);
        }

        // Ensure event belongs to user's family
        $family = $request->user()->currentFamily()->firstOrFail();
        if ($featuredEvent->family_id !== $family->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $featuredEvent->delete();

        return response()->json(null, 204);
    }
}
