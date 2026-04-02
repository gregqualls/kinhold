<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\CalendarConnection;
use App\Models\FamilyEvent;
use App\Models\Task;
use App\Services\GoogleCalendarService;
use App\Services\IcsCalendarService;
use Carbon\Carbon;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('view-calendar')]
#[Description('View family calendar events, connections, or trigger a sync. Create, update, and delete manual family events. Actions: events (by date range), connections (list connected calendars), sync (refresh calendar data), create_event, update_event, delete_event.')]
class ViewCalendar extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum(['events', 'connections', 'sync', 'create_event', 'update_event', 'delete_event'])->description('Action to perform'),
            'start' => $schema->string()->description('Start date in YYYY-MM-DD format (defaults to today)'),
            'end' => $schema->string()->description('End date in YYYY-MM-DD format (defaults to 3 months from now)'),
            'title' => $schema->string()->description('Event title (required for create_event)'),
            'description' => $schema->string()->description('Event description'),
            'start_time' => $schema->string()->description('Start datetime in ISO format or YYYY-MM-DD (required for create_event)'),
            'end_time' => $schema->string()->description('End datetime in ISO format or YYYY-MM-DD'),
            'all_day' => $schema->boolean()->description('Whether this is an all-day event (default: true)'),
            'location' => $schema->string()->description('Event location'),
            'color' => $schema->string()->description('Event color as hex (e.g. #8B5CF6)'),
            'recurrence' => $schema->string()->enum(['none', 'yearly', 'monthly', 'weekly'])->description('Recurrence pattern'),
            'visibility' => $schema->string()->enum(['visible', 'busy', 'private'])->description('Who can see this event'),
            'featured_scope' => $schema->string()->enum(['personal', 'family'])->description('Feature on dashboard (null for not featured)'),
            'icon' => $schema->string()->description('Icon name for featured display'),
            'event_id' => $schema->string()->description('Event UUID (required for update_event and delete_event)'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'events' => $this->getEvents($request),
            'connections' => $this->getConnections(),
            'sync' => $this->syncCalendars(),
            'create_event' => $this->createEvent($request),
            'update_event' => $this->updateEvent($request),
            'delete_event' => $this->deleteEvent($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function getEvents(Request $request): Response
    {
        $start = $request->get('start') ? Carbon::parse($request->get('start')) : now();
        $end = $request->get('end') ? Carbon::parse($request->get('end')) : now()->addMonths(3);

        $allEvents = [];

        // ── External calendar connections (Google, ICS) ──
        $connections = CalendarConnection::whereHas('user', function ($q) {
            $q->where('family_id', $this->familyId());
        })
            ->where('is_active', true)
            ->with('user:id,name')
            ->get();

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

        // ── Manual family events ──
        $localEvents = FamilyEvent::where('family_id', $this->familyId())
            ->where('is_active', true)
            ->where(function ($q) use ($start, $end) {
                $q->where(function ($q2) use ($start, $end) {
                    $q2->where('recurrence', 'none')
                        ->whereBetween('start_time', [$start, $end]);
                })
                    ->orWhere('recurrence', '!=', 'none');
            })
            ->with('creator:id,name')
            ->get();

        foreach ($localEvents as $event) {
            $occurrences = $event->occurrencesInRange($start, $end);

            foreach ($occurrences as $occurrenceDate) {
                $allEvents[] = [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->all_day ? $occurrenceDate->toDateString() : $occurrenceDate->copy()->setTimeFrom($event->start_time)->toIso8601String(),
                    'end' => $event->end_time ? $occurrenceDate->copy()->setTimeFrom($event->end_time)->toIso8601String() : null,
                    'all_day' => $event->all_day,
                    'location' => $event->location,
                    'member' => $event->creator->name ?? 'Unknown',
                    'source' => 'manual',
                    'recurrence' => $event->recurrence,
                    'visibility' => $event->visibility,
                    'featured' => $event->featured_scope,
                ];
            }
        }

        // ── Tasks with due dates ──
        $tasks = Task::where('family_id', $this->familyId())
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$start, $end])
            ->with('assignee:id,name')
            ->get();

        foreach ($tasks as $task) {
            $allEvents[] = [
                'title' => ($task->completed_at ? "\u{2705} " : '').$task->title,
                'start' => $task->due_date->toDateString(),
                'end' => $task->due_date->toDateString(),
                'all_day' => true,
                'member' => $task->assignee?->name ?? 'Unassigned',
                'source' => 'task',
                'completed' => (bool) $task->completed_at,
            ];
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

    private function createEvent(Request $request): Response
    {
        $title = $request->get('title');
        if (! $title) {
            return Response::error('title is required for create_event');
        }

        $startTime = $request->get('start_time') ?? $request->get('start');
        if (! $startTime) {
            return Response::error('start_time is required for create_event');
        }

        $allDay = $request->get('all_day') ?? true;

        $data = [
            'family_id' => $this->familyId(),
            'created_by' => $this->user()->id,
            'title' => $title,
            'description' => $request->get('description'),
            'start_time' => Carbon::parse($startTime),
            'end_time' => $request->get('end_time') ? Carbon::parse($request->get('end_time')) : null,
            'all_day' => $allDay,
            'location' => $request->get('location'),
            'color' => $request->get('color') ?? '#6366f1',
            'recurrence' => $request->get('recurrence') ?? 'none',
            'visibility' => $request->get('visibility') ?? 'visible',
        ];

        // Featured scope, countdown, and icon are parent-only
        if ($this->user()->isParent()) {
            $data['featured_scope'] = $request->get('featured_scope');
            $data['icon'] = $request->get('icon');
        }

        // Enforce single countdown per family (parent-only)
        if ($request->get('is_countdown') && $this->user()->isParent()) {
            FamilyEvent::where('family_id', $this->familyId())
                ->where('is_countdown', true)
                ->update(['is_countdown' => false]);
            $data['is_countdown'] = true;
        }

        $event = FamilyEvent::create($data);

        return Response::text("Created event: {$event->title} on {$event->start_time->toDateString()}");
    }

    private function updateEvent(Request $request): Response
    {
        $eventId = $request->get('event_id');
        if (! $eventId) {
            return Response::error('event_id is required for update_event');
        }

        $event = FamilyEvent::where('family_id', $this->familyId())->find($eventId);
        if (! $event) {
            return Response::error("Event not found: {$eventId}");
        }

        // Only creator or parent can update
        if (! $this->user()->isParent() && $event->created_by !== $this->user()->id) {
            return Response::error('You can only edit your own events.');
        }

        $updateData = [];
        foreach (['title', 'description', 'location', 'color', 'recurrence', 'visibility', 'featured_scope', 'icon'] as $field) {
            if ($request->get($field) !== null) {
                $updateData[$field] = $request->get($field);
            }
        }

        if ($request->get('start_time') || $request->get('start')) {
            $updateData['start_time'] = Carbon::parse($request->get('start_time') ?? $request->get('start'));
        }
        if ($request->get('end_time')) {
            $updateData['end_time'] = Carbon::parse($request->get('end_time'));
        }
        if ($request->get('all_day') !== null) {
            $updateData['all_day'] = $request->get('all_day');
        }

        $event->update($updateData);

        return Response::text("Updated event: {$event->title}");
    }

    private function deleteEvent(Request $request): Response
    {
        $eventId = $request->get('event_id');
        if (! $eventId) {
            return Response::error('event_id is required for delete_event');
        }

        $event = FamilyEvent::where('family_id', $this->familyId())->find($eventId);
        if (! $event) {
            return Response::error("Event not found: {$eventId}");
        }

        // Only creator or parent can delete
        if (! $this->user()->isParent() && $event->created_by !== $this->user()->id) {
            return Response::error('You can only delete your own events.');
        }

        $title = $event->title;
        $event->delete();

        return Response::text("Deleted event: {$title}");
    }
}
