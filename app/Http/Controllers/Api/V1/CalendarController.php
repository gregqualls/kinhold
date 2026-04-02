<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CalendarConnection;
use App\Models\FamilyEvent;
use App\Models\Task;
use App\Services\GoogleCalendarService;
use App\Services\IcsCalendarService;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Get aggregated events from all sources (Google, ICS, manual, tasks).
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
            ->where('is_active', true)
            ->where(function ($q) use ($start, $end) {
                // Non-recurring events in the date range
                $q->where(function ($q2) use ($start, $end) {
                    $q2->where('recurrence', 'none')
                        ->where(function ($q3) use ($start, $end) {
                            $q3->whereBetween('start_time', [$start, $end])
                                ->orWhere(function ($q4) use ($start, $end) {
                                    $q4->where('all_day', true)
                                        ->where('start_time', '<=', $end)
                                        ->where(function ($q5) use ($start) {
                                            $q5->where('end_time', '>=', $start)
                                                ->orWhereNull('end_time');
                                        });
                                });
                        });
                })
                // Recurring events — fetch all, filter by next_occurrence later
                    ->orWhere('recurrence', '!=', 'none');
            })
            ->with('creator:id,name')
            ->get();

        foreach ($localEvents as $event) {
            // Apply visibility filtering
            $vis = $event->visibilityFor($user);
            if ($vis === 'hidden') {
                continue;
            }

            $title = $vis === 'busy' ? 'Busy' : $event->title;

            // Expand recurring events into all occurrences within the range
            $occurrences = $event->occurrencesInRange($start, $end);

            foreach ($occurrences as $occurrenceDate) {
                $eventStart = $event->all_day
                    ? $occurrenceDate->toDateString()
                    : $occurrenceDate->copy()->setTimeFrom($event->start_time)->toIso8601String();
                $eventEnd = $event->end_time
                    ? ($event->all_day ? $occurrenceDate->toDateString() : $occurrenceDate->copy()->setTimeFrom($event->end_time)->toIso8601String())
                    : null;

                $allEvents[] = [
                    'id' => $event->id,
                    'title' => $title,
                    'description' => $vis === 'busy' ? null : $event->description,
                    'start' => $eventStart,
                    'end' => $eventEnd,
                    'all_day' => $event->all_day,
                    'location' => $vis === 'busy' ? null : $event->location,
                    'calendar_id' => null,
                    'user' => [
                        'name' => $event->creator->name ?? 'Unknown',
                        'color' => $event->color ?? '#6366f1',
                    ],
                    'source' => 'manual',
                    'visibility' => $event->visibility,
                    'featured_scope' => $event->featured_scope,
                    'is_countdown' => $event->is_countdown,
                    'icon' => $event->icon,
                    'recurrence' => $event->recurrence,
                ];
            }
        }

        // ── Tasks with due dates ──
        $tasks = Task::where('family_id', $familyId)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$start, $end])
            ->with('assignee:id,name')
            ->get();

        foreach ($tasks as $task) {
            $allEvents[] = [
                'id' => 'task-'.$task->id,
                'title' => ($task->completed_at ? "\u{2705} " : '').$task->title,
                'description' => $task->description,
                'start' => $task->due_date->toDateString(),
                'end' => $task->due_date->toDateString(),
                'all_day' => true,
                'location' => null,
                'calendar_id' => null,
                'user' => [
                    'name' => $task->assignee?->name ?? 'Unassigned',
                    'color' => $task->completed_at ? '#9A9892' : '#B38A50',
                ],
                'source' => 'task',
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
                'visibility' => $e['visibility'] ?? null,
                'featured_scope' => $e['featured_scope'] ?? null,
                'is_countdown' => $e['is_countdown'] ?? false,
                'icon' => $e['icon'] ?? null,
                'recurrence' => $e['recurrence'] ?? null,
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
            'recurrence' => 'nullable|string|in:none,yearly,monthly,weekly',
            'visibility' => 'nullable|string|in:visible,busy,private',
            'featured_scope' => 'nullable|string|in:personal,family',
            'is_countdown' => 'boolean',
            'icon' => 'nullable|string|max:50',
        ]);

        $user = $request->user();

        // Featured scope and countdown are parent-only
        if (! $user->isParent()) {
            unset($validated['featured_scope'], $validated['is_countdown'], $validated['icon']);
        }

        // Only one countdown per family
        if (! empty($validated['is_countdown'])) {
            FamilyEvent::where('family_id', $user->family_id)
                ->where('is_countdown', true)
                ->update(['is_countdown' => false]);
        }

        $event = FamilyEvent::create([
            'family_id' => $user->family_id,
            'created_by' => $user->id,
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

        $this->authorize('update', $familyEvent);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'all_day' => 'boolean',
            'location' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'recurrence' => 'nullable|string|in:none,yearly,monthly,weekly',
            'visibility' => 'nullable|string|in:visible,busy,private',
            'featured_scope' => 'nullable|string|in:personal,family',
            'is_countdown' => 'boolean',
            'icon' => 'nullable|string|max:50',
        ]);

        // Featured scope and countdown are parent-only
        if (! $request->user()->isParent()) {
            unset($validated['featured_scope'], $validated['is_countdown'], $validated['icon']);
        }

        // Only one countdown per family
        if (! empty($validated['is_countdown'])) {
            FamilyEvent::where('family_id', $request->user()->family_id)
                ->where('is_countdown', true)
                ->where('id', '!=', $familyEvent->id)
                ->update(['is_countdown' => false]);
        }

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

        $this->authorize('delete', $familyEvent);

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
        if (empty(config('kinhold.google.client_id')) || empty(config('kinhold.google.client_secret'))) {
            return response()->json([
                'message' => 'Google Calendar is not configured on this server. The server administrator needs to set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET.',
                'error_type' => 'configuration',
            ], 422);
        }

        try {
            $origin = $request->input('origin', 'settings');
            // SECURITY: Encrypt the state parameter to prevent CSRF on callback
            $state = encrypt($request->user()->id.':'.$origin);
            $authUrl = GoogleCalendarService::getAuthUrl($state);

            return response()->json([
                'auth_url' => $authUrl,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to initiate calendar connection. Please try again.',
            ], 500);
        }
    }

    /**
     * Handle OAuth callback from Google.
     */
    public function handleCallback(Request $request)
    {
        $code = $request->query('code');
        $rawState = $request->query('state');

        if (! $code || ! $rawState) {
            return redirect('/settings?calendar_error='.urlencode('Missing authorization code or user ID.'));
        }

        try {
            $decrypted = decrypt($rawState);
        } catch (DecryptException $e) {
            return redirect('/settings?calendar_error='.urlencode('Invalid state parameter.'));
        }

        $parts = explode(':', $decrypted, 2);
        $userId = $parts[0];
        $origin = $parts[1] ?? 'settings';

        try {
            $connections = GoogleCalendarService::handleCallback($code, $userId);
            $count = count($connections);

            if ($origin === 'onboarding') {
                return redirect('/onboarding?step=2&calendar_connected='.$count);
            }

            return redirect('/settings?calendar_connected='.$count);
        } catch (\Exception $e) {
            \Log::error('Calendar OAuth callback failed: '.$e->getMessage());

            $errorRedirect = $origin === 'onboarding'
                ? '/onboarding?step=2&calendar_error='.urlencode('Failed to connect calendar. Please try again.')
                : '/settings?calendar_error='.urlencode('Failed to connect calendar. Please try again.');

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
