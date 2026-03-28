<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CalendarEventResource;
use App\Models\CalendarConnection;
use App\Models\FamilyEvent;
use App\Services\GoogleCalendarService;
use App\Services\IcsCalendarService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Get aggregated events from all sources (Google, ICS, manual).
     */
    public function events(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'start' => 'nullable|date_format:Y-m-d',
            'end' => 'nullable|date_format:Y-m-d',
        ]);

        $start = $validated['start'] ? Carbon::parse($validated['start'])->startOfDay() : now()->startOfDay();
        $end = $validated['end'] ? Carbon::parse($validated['end'])->endOfDay() : now()->addMonths(3)->endOfDay();

        $familyId = $user->family_id;
        $allEvents = [];

        // ── External calendar connections (Google, ICS) ──
        $connections = CalendarConnection::whereHas('user', function ($q) use ($familyId) {
                $q->where('family_id', $familyId);
            })
            ->where('is_active', true)
            ->with('user:id,name')
            ->get();

        foreach ($connections as $connection) {
            try {
                if ($connection->provider === 'ics') {
                    $service = new IcsCalendarService($connection);
                } else {
                    $service = new GoogleCalendarService($connection);
                }

                $events = $service->getEvents($start, $end);

                foreach ($events as $event) {
                    $event['user'] = [
                        'name' => $connection->user->name ?? 'Unknown',
                        'color' => $connection->color ?? '#1f2937',
                    ];
                    $event['source'] = $connection->provider;
                    $allEvents[] = $event;
                }
            } catch (\Throwable $e) {
                \Log::error("Failed to fetch calendar events for connection {$connection->id}: {$e->getMessage()}");
            }
        }

        // ── Manual family events ──
        $localEvents = FamilyEvent::where('family_id', $familyId)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      // Include all-day events that span the range
                      $q2->where('all_day', true)
                          ->where('start_time', '<=', $end)
                          ->where(function ($q3) use ($start) {
                              $q3->where('end_time', '>=', $start)
                                  ->orWhereNull('end_time');
                          });
                  });
            })
            ->with('creator:id,name')
            ->get();

        foreach ($localEvents as $event) {
            $allEvents[] = [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'start' => $event->all_day
                    ? $event->start_time->toDateString()
                    : $event->start_time->toIso8601String(),
                'end' => $event->end_time
                    ? ($event->all_day ? $event->end_time->toDateString() : $event->end_time->toIso8601String())
                    : null,
                'all_day' => $event->all_day,
                'location' => $event->location,
                'calendar_id' => null,
                'user' => [
                    'name' => $event->creator->name ?? 'Unknown',
                    'color' => $event->color ?? '#6366f1',
                ],
                'source' => 'manual',
            ];
        }

        // Sort by start time
        usort($allEvents, function ($a, $b) {
            return strtotime($a['start']) <=> strtotime($b['start']);
        });

        return response()->json([
            'events' => collect($allEvents)->map(fn ($e) => [
                'id' => $e['id'] ?? null,
                'title' => $e['title'] ?? $e['summary'] ?? null,
                'description' => $e['description'] ?? null,
                'start' => $e['start'] ?? null,
                'end' => $e['end'] ?? null,
                'all_day' => $e['all_day'] ?? false,
                'location' => $e['location'] ?? null,
                'calendar_id' => $e['calendar_id'] ?? null,
                'user' => $e['user'] ?? null,
                'source' => $e['source'] ?? 'google',
            ])->values(),
            'count' => count($allEvents),
        ], 200);
    }

    // ── Manual Event CRUD ──

    /**
     * Create a manual family event.
     */
    public function storeEvent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'all_day' => 'boolean',
            'location' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $event = FamilyEvent::create([
            'family_id' => $request->user()->family_id,
            'created_by' => $request->user()->id,
            ...$validated,
        ]);

        return response()->json(['event' => $event->load('creator:id,name')], 201);
    }

    /**
     * Update a manual family event.
     */
    public function updateEvent(Request $request, FamilyEvent $familyEvent): JsonResponse
    {
        if ($familyEvent->family_id !== $request->user()->family_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'all_day' => 'boolean',
            'location' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $familyEvent->update($validated);

        return response()->json(['event' => $familyEvent->fresh()->load('creator:id,name')]);
    }

    /**
     * Delete a manual family event.
     */
    public function destroyEvent(Request $request, FamilyEvent $familyEvent): JsonResponse
    {
        if ($familyEvent->family_id !== $request->user()->family_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $familyEvent->delete();

        return response()->json(null, 204);
    }

    // ── Calendar Connections ──

    /**
     * Get all calendar connections for the current user.
     */
    public function connections(Request $request): JsonResponse
    {
        $user = $request->user();

        $familyId = $user->family_id;
        $connections = CalendarConnection::whereHas('user', function ($q) use ($familyId) {
                $q->where('family_id', $familyId);
            })
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($conn) => [
                'id' => $conn->id,
                'user_id' => $conn->user_id,
                'user' => $conn->user ? ['id' => $conn->user->id, 'name' => $conn->user->name] : null,
                'provider' => $conn->provider,
                'calendar_name' => $conn->calendar_name,
                'is_active' => $conn->is_active,
                'color' => $conn->color,
                'last_synced_at' => $conn->last_synced_at,
                'created_at' => $conn->created_at,
            ]);

        return response()->json([
            'connections' => $connections,
        ], 200);
    }

    /**
     * Initiate Google Calendar OAuth flow.
     */
    public function connect(Request $request): JsonResponse
    {
        try {
            $origin = $request->input('origin', 'settings');
            $state = $request->user()->id . ':' . $origin;
            $authUrl = GoogleCalendarService::getAuthUrl($state);

            return response()->json([
                'auth_url' => $authUrl,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to initiate calendar connection: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle OAuth callback from Google.
     * Google redirects here with ?code=...&state=userId
     * We connect the calendar and redirect back to the settings page.
     */
    public function handleCallback(Request $request)
    {
        $code = $request->query('code');
        $rawState = $request->query('state');

        if (!$code || !$rawState) {
            return redirect('/settings?calendar_error=' . urlencode('Missing authorization code or user ID.'));
        }

        // Parse origin from state (format: "userId:origin" or just "userId")
        $parts = explode(':', $rawState, 2);
        $userId = $parts[0];
        $origin = $parts[1] ?? 'settings';

        try {
            $connections = GoogleCalendarService::handleCallback($code, $userId);
            $count = count($connections);

            if ($origin === 'onboarding') {
                return redirect('/onboarding?step=2&calendar_connected=' . $count);
            }

            return redirect('/settings?calendar_connected=' . $count);
        } catch (\Exception $e) {
            \Log::error('Calendar OAuth callback failed: ' . $e->getMessage());

            $errorRedirect = $origin === 'onboarding'
                ? '/onboarding?step=2&calendar_error=' . urlencode('Failed to connect calendar: ' . $e->getMessage())
                : '/settings?calendar_error=' . urlencode('Failed to connect calendar: ' . $e->getMessage());

            return redirect($errorRedirect);
        }
    }

    /**
     * Subscribe to an ICS calendar feed via URL.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'name' => 'nullable|string|max:255',
        ]);

        try {
            $connection = IcsCalendarService::subscribe(
                $validated['url'],
                $request->user()->id,
                $validated['name'] ?? null
            );

            return response()->json([
                'message' => "Subscribed to calendar: {$connection->calendar_name}",
                'connection' => [
                    'id' => $connection->id,
                    'calendar_name' => $connection->calendar_name,
                    'calendar_id' => $connection->calendar_id,
                    'provider' => $connection->provider,
                    'is_active' => $connection->is_active,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Disconnect a calendar connection.
     */
    public function disconnect(Request $request, CalendarConnection $connection): JsonResponse
    {
        if ($connection->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $connection->delete();

        return response()->json(null, 204);
    }

    /**
     * Force sync all calendar connections.
     */
    public function sync(Request $request): JsonResponse
    {
        $user = $request->user();
        $connections = CalendarConnection::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        $syncedCount = 0;

        foreach ($connections as $connection) {
            try {
                if ($connection->provider === 'ics') {
                    $connection->update(['last_synced_at' => now()]);
                } else {
                    $service = new GoogleCalendarService($connection);
                    $service->refreshToken();
                    $connection->update(['last_synced_at' => now()]);
                }
                $syncedCount++;
            } catch (\Exception $e) {
                \Log::error("Failed to sync calendar connection {$connection->id}: {$e->getMessage()}");
            }
        }

        return response()->json([
            'message' => "Synced {$syncedCount} calendar(s)",
            'synced_count' => $syncedCount,
        ], 200);
    }
}
