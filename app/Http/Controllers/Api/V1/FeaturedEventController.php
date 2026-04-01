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

        // Fetch all active events — recurring ones may have old dates but still be relevant
        $events = FeaturedEvent::where('family_id', $family->id)
            ->where('is_active', true)
            ->with('creator:id,name,avatar')
            ->get()
            ->map(function ($event) {
                // Compute next occurrence for recurring events
                $event->computed_next_date = $event->next_occurrence;

                return $event;
            })
            ->filter(function ($event) {
                // Only show events whose next occurrence is within the next 60 days
                return $event->computed_next_date->lte(Carbon::today()->addDays(60))
                    && $event->computed_next_date->gte(Carbon::today());
            })
            ->sortBy('computed_next_date')
            ->values();

        return response()->json([
            'featured_events' => FeaturedEventResource::collection($events),
        ]);
    }

    /**
     * Create a featured event (parent only).
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', FeaturedEvent::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'recurrence' => 'nullable|string|in:none,yearly,monthly,weekly',
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
            'recurrence' => $validated['recurrence'] ?? 'none',
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
        // Ensure event belongs to user's family
        $family = $request->user()->currentFamily()->firstOrFail();
        if ($featuredEvent->family_id !== $family->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $this->authorize('update', $featuredEvent);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'event_date' => 'sometimes|date',
            'event_time' => 'nullable|date_format:H:i',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'sometimes|boolean',
            'recurrence' => 'nullable|string|in:none,yearly,monthly,weekly',
        ]);

        $featuredEvent->update($validated);

        return response()->json([
            'featured_event' => new FeaturedEventResource($featuredEvent),
        ]);
    }

    /**
     * Get the current countdown event for the family.
     */
    public function countdown(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();

        $event = FeaturedEvent::where('family_id', $family->id)
            ->where('is_active', true)
            ->where('is_countdown', true)
            ->with('creator:id,name,avatar')
            ->first();

        if (! $event) {
            return response()->json(['countdown_event' => null]);
        }

        // Compute next occurrence for recurring events
        $event->computed_next_date = $event->next_occurrence;

        return response()->json([
            'countdown_event' => new FeaturedEventResource($event),
        ]);
    }

    /**
     * Set an event as the countdown event (parent only). Unsets any other countdown.
     */
    public function setCountdown(Request $request, FeaturedEvent $featuredEvent): JsonResponse
    {
        $this->authorize('update', $featuredEvent);

        $family = $request->user()->currentFamily()->firstOrFail();
        if ($featuredEvent->family_id !== $family->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // Unset any existing countdown for this family
        FeaturedEvent::where('family_id', $family->id)
            ->where('is_countdown', true)
            ->update(['is_countdown' => false]);

        // Toggle: if this event was already the countdown, just unset it
        if ($featuredEvent->is_countdown) {
            $featuredEvent->refresh();

            return response()->json([
                'featured_event' => new FeaturedEventResource($featuredEvent),
            ]);
        }

        // Set this event as the countdown
        $featuredEvent->update(['is_countdown' => true]);

        return response()->json([
            'featured_event' => new FeaturedEventResource($featuredEvent),
        ]);
    }

    /**
     * Delete a featured event (parent only).
     */
    public function destroy(Request $request, FeaturedEvent $featuredEvent): JsonResponse
    {
        // Ensure event belongs to user's family
        $family = $request->user()->currentFamily()->firstOrFail();
        if ($featuredEvent->family_id !== $family->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $this->authorize('delete', $featuredEvent);

        $featuredEvent->delete();

        return response()->json(null, 204);
    }
}
