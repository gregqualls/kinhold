<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CalendarEventResource;
use App\Models\CalendarConnection;
use App\Services\GoogleCalendarService;
use App\Services\IcsCalendarService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Get aggregated events from all connected calendars.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function events(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'start' => 'nullable|date_format:Y-m-d',
            'end' => 'nullable|date_format:Y-m-d',
        ]);

        $start = $validated['start'] ? Carbon::parse($validated['start']) : now();
        $end = $validated['end'] ? Carbon::parse($validated['end']) : now()->addMonths(3);

        // Get all active connections for the user's family
        $familyId = $user->family_id;
        $connections = CalendarConnection::whereHas('user', function ($q) use ($familyId) {
                $q->where('family_id', $familyId);
            })
            ->where('is_active', true)
            ->with('user:id,name')
            ->get();

        // Return empty gracefully if no connections
        if ($connections->isEmpty()) {
            return response()->json([
                'events' => [],
                'count' => 0,
            ], 200);
        }

        $allEvents = [];

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
                // Log error, but continue with other calendars
                \Log::error("Failed to fetch calendar events for connection {$connection->id}: {$e->getMessage()}");
            }
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

    /**
     * Get all calendar connections for the current user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function connections(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get connections for all family members so we can show status in settings
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
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function connect(Request $request): JsonResponse
    {
        try {
            $authUrl = GoogleCalendarService::getAuthUrl($request->user()->id);

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
     * Handle OAuth callback and store calendar connection.
     *
     * @param Request $request
     * @return JsonResponse
     */
    /**
     * Handle OAuth callback from Google.
     * Google redirects here with ?code=...&state=userId
     * We connect the calendar and redirect back to the settings page.
     */
    public function handleCallback(Request $request)
    {
        $code = $request->query('code');
        $userId = $request->query('state');

        if (!$code || !$userId) {
            return redirect('/settings?calendar_error=' . urlencode('Missing authorization code or user ID.'));
        }

        try {
            $connections = GoogleCalendarService::handleCallback($code, $userId);
            $count = count($connections);

            return redirect('/settings?calendar_connected=' . $count);
        } catch (\Exception $e) {
            \Log::error('Calendar OAuth callback failed: ' . $e->getMessage());
            return redirect('/settings?calendar_error=' . urlencode('Failed to connect calendar: ' . $e->getMessage()));
        }
    }

    /**
     * Subscribe to an ICS calendar feed via URL.
     *
     * @param Request $request
     * @return JsonResponse
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
     *
     * @param Request $request
     * @param CalendarConnection $connection
     * @return JsonResponse
     */
    public function disconnect(Request $request, CalendarConnection $connection): JsonResponse
    {
        // Ensure user can only disconnect their own calendars
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
     *
     * @param Request $request
     * @return JsonResponse
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
                    // ICS calendars just need a timestamp update; events are fetched live
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
