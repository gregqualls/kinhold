<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\CalendarConnection;
use App\Services\GoogleCalendarService;
use App\Services\IcsCalendarService;
use Carbon\Carbon;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('view-calendar')]
#[Description('View family calendar events, connections, or trigger a sync. Actions: events (by date range), connections (list connected calendars), sync (refresh calendar data).')]
#[IsReadOnly]
class ViewCalendar extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'action' => [
                    'type' => 'string',
                    'enum' => ['events', 'connections', 'sync'],
                    'description' => 'Action to perform',
                ],
                'start' => [
                    'type' => 'string',
                    'description' => 'Start date in YYYY-MM-DD format (defaults to today)',
                ],
                'end' => [
                    'type' => 'string',
                    'description' => 'End date in YYYY-MM-DD format (defaults to 3 months from now)',
                ],
            ],
            'required' => ['action'],
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'events' => $this->getEvents($request),
            'connections' => $this->getConnections(),
            'sync' => $this->syncCalendars(),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function getEvents(Request $request): Response
    {
        $start = $request->get('start') ? Carbon::parse($request->get('start')) : now();
        $end = $request->get('end') ? Carbon::parse($request->get('end')) : now()->addMonths(3);

        $connections = CalendarConnection::whereHas('user', function ($q) {
                $q->where('family_id', $this->familyId());
            })
            ->where('is_active', true)
            ->with('user:id,name')
            ->get();

        if ($connections->isEmpty()) {
            return Response::json(['events' => [], 'count' => 0]);
        }

        $allEvents = [];

        foreach ($connections as $connection) {
            try {
                $service = $connection->provider === 'ics'
                    ? new IcsCalendarService($connection)
                    : new GoogleCalendarService($connection);

                $events = $service->getEvents($start, $end);

                foreach ($events as $event) {
                    $allEvents[] = [
                        'title' => $event['title'] ?? $event['summary'] ?? null,
                        'start' => $event['start'] ?? null,
                        'end' => $event['end'] ?? null,
                        'all_day' => $event['all_day'] ?? false,
                        'location' => $event['location'] ?? null,
                        'member' => $connection->user->name ?? 'Unknown',
                        'source' => $connection->provider,
                    ];
                }
            } catch (\Throwable $e) {
                // Skip failed calendars silently
            }
        }

        usort($allEvents, fn ($a, $b) => strtotime($a['start']) <=> strtotime($b['start']));

        return Response::json([
            'events' => $allEvents,
            'count' => count($allEvents),
        ]);
    }

    private function getConnections(): Response
    {
        $connections = CalendarConnection::whereHas('user', function ($q) {
                $q->where('family_id', $this->familyId());
            })
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->get();

        return Response::json([
            'connections' => $connections->map(fn ($c) => [
                'id' => $c->id,
                'user' => $c->user?->name,
                'provider' => $c->provider,
                'calendar_name' => $c->calendar_name,
                'is_active' => $c->is_active,
                'color' => $c->color,
                'last_synced_at' => $c->last_synced_at?->toIso8601String(),
            ])->toArray(),
        ]);
    }

    private function syncCalendars(): Response
    {
        $connections = CalendarConnection::where('user_id', $this->user()->id)
            ->where('is_active', true)
            ->get();

        $synced = 0;

        foreach ($connections as $connection) {
            try {
                if ($connection->provider === 'ics') {
                    $connection->update(['last_synced_at' => now()]);
                } else {
                    $service = new GoogleCalendarService($connection);
                    $service->refreshToken();
                    $connection->update(['last_synced_at' => now()]);
                }
                $synced++;
            } catch (\Throwable $e) {
                // Skip failed syncs
            }
        }

        return Response::text("Synced {$synced} calendar(s).");
    }
}
