<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeaturedEventResource;
use App\Models\FamilyEvent;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeaturedEventController extends Controller
{
    /**
     * List upcoming featured events for the family (next 60 days).
     * Now reads from the unified family_events table.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $family = $user->currentFamily()->firstOrFail();

        $events = FamilyEvent::where('family_id', $family->id)
            ->where('is_active', true)
            ->where(function ($q) use ($user) {
                // Family-featured events visible to everyone
                $q->where('featured_scope', 'family')
                    // Personal-featured events visible only to creator
                    ->orWhere(function ($q2) use ($user) {
                        $q2->where('featured_scope', 'personal')
                            ->where('created_by', $user->id);
                    });
            })
            ->with('creator:id,name,avatar')
            ->get()
            ->map(function ($event) {
                $event->computed_next_date = $event->next_occurrence;

                return $event;
            })
            ->filter(function ($event) {
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
     * Creates a FamilyEvent with featured_scope set.
     */
    public function store(Request $request): JsonResponse
    {
        // Featured events are parent-only
        if (! $request->user()->isParent()) {
            abort(403, 'Only parents can create featured events.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'recurrence' => 'nullable|string|in:none,yearly,monthly,weekly',
            'featured_scope' => 'nullable|string|in:personal,family',
        ]);

        $family = $request->user()->currentFamily()->firstOrFail();

        // Convert event_date + event_time to start_time datetime
        $startTime = Carbon::parse($validated['event_date']);
        if (! empty($validated['event_time'])) {
            $parts = explode(':', $validated['event_time']);
            $startTime->setTime((int) $parts[0], (int) $parts[1]);
        }

        $event = FamilyEvent::create([
            'family_id' => $family->id,
            'created_by' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_time' => $startTime,
            'end_time' => null,
            'all_day' => true,
            'icon' => $validated['icon'] ?? "\u{1F389}",
            'color' => $validated['color'] ?? '#8B5CF6',
            'recurrence' => $validated['recurrence'] ?? 'none',
            'featured_scope' => $validated['featured_scope'] ?? 'family',
            'visibility' => 'visible',
        ]);

        $event->load('creator:id,name,avatar');

        return response()->json([
            'featured_event' => new FeaturedEventResource($event),
        ], 201);
    }

    /**
     * Update a featured event (parent only).
     */
    public function update(Request $request, FamilyEvent $familyEvent): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();
        if ($familyEvent->family_id !== $family->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if (! $request->user()->isParent()) {
            abort(403, 'Only parents can manage featured events.');
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'event_date' => 'sometimes|date',
            'event_time' => 'nullable|date_format:H:i',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'sometimes|boolean',
            'recurrence' => 'nullable|string|in:none,yearly,monthly,weekly',
            'featured_scope' => 'nullable|string|in:personal,family',
        ]);

        // Convert event_date + event_time to start_time if provided
        $updateData = collect($validated)->except(['event_date', 'event_time'])->toArray();

        if (isset($validated['event_date'])) {
            $startTime = Carbon::parse($validated['event_date']);
            if (! empty($validated['event_time'])) {
                $parts = explode(':', $validated['event_time']);
                $startTime->setTime((int) $parts[0], (int) $parts[1]);
            }
            $updateData['start_time'] = $startTime;
        }

        $familyEvent->update($updateData);

        return response()->json([
            'featured_event' => new FeaturedEventResource($familyEvent),
        ]);
    }

    /**
     * Get the current countdown event for the family.
     */
    public function countdown(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();

        $event = FamilyEvent::where('family_id', $family->id)
            ->where('is_active', true)
            ->where('is_countdown', true)
            ->with('creator:id,name,avatar')
            ->first();

        if (! $event) {
            return response()->json(['countdown_event' => null]);
        }

        // Auto-expire past non-recurring countdown events
        if ($event->recurrence === 'none' && $event->start_time->startOfDay()->lt(now()->startOfDay())) {
            $event->update(['is_countdown' => false]);

            return response()->json(['countdown_event' => null]);
        }

        $event->computed_next_date = $event->next_occurrence;

        return response()->json([
            'countdown_event' => new FeaturedEventResource($event),
        ]);
    }

    /**
     * Set an event as the countdown event (parent only).
     */
    public function setCountdown(Request $request, FamilyEvent $familyEvent): JsonResponse
    {
        if (! $request->user()->isParent()) {
            abort(403, 'Only parents can manage countdowns.');
        }

        $family = $request->user()->currentFamily()->firstOrFail();
        if ($familyEvent->family_id !== $family->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // Capture current state before blanket unset
        $wasCountdown = $familyEvent->is_countdown;

        // Unset any existing countdown for this family
        FamilyEvent::where('family_id', $family->id)
            ->where('is_countdown', true)
            ->update(['is_countdown' => false]);

        // Toggle: if this event was already the countdown, just unset it
        if ($wasCountdown) {
            $familyEvent->refresh();

            return response()->json([
                'featured_event' => new FeaturedEventResource($familyEvent),
            ]);
        }

        // Set this event as the countdown
        $familyEvent->update(['is_countdown' => true]);

        return response()->json([
            'featured_event' => new FeaturedEventResource($familyEvent),
        ]);
    }

    /**
     * Delete a featured event (parent only).
     */
    public function destroy(Request $request, FamilyEvent $familyEvent): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();
        if ($familyEvent->family_id !== $family->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if (! $request->user()->isParent()) {
            abort(403, 'Only parents can delete featured events.');
        }

        $familyEvent->delete();

        return response()->json(null, 204);
    }
}
