<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\MergesUpdates;
use App\Mcp\Tools\Concerns\RequiresModule;
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

#[Name('kinhold-calendar')]
#[Description(<<<'DESC'
Family calendar: events (manual + external + tasks), connections, and featured/countdown events.

Actions:
  event_list (start?, end?) — Aggregated events from external calendars (Google/ICS), manual family events, and task due dates. Defaults: today → +3mo.
  event_create (title*, start_time*, end_time?, all_day?, description?, location?, color?, recurrence?, visibility?, featured_scope?, icon?, is_countdown?) — Create a manual family event.
  event_update (event_id*, [any field]) — Creator or parent only.
  event_delete (event_id*) — Creator or parent only.
  connection_list — List connected external calendars.
  connection_sync — Refresh sync timestamps + Google tokens for the current user's connections.
  featured_list — Upcoming featured/countdown events for the dashboard.
  featured_create (title*, event_date*, event_time?, icon?, color?, recurrence?, is_countdown?, featured_scope?) — Parent only.
  featured_update (event_id*, [any field]) — Parent only.
  featured_delete (event_id*) — Parent only.

Recurrence: none, yearly, monthly, weekly. Visibility: visible, busy, private.
Only one countdown event per family — setting is_countdown=true clears any existing countdown.
DESC)]
class KinholdCalendar extends Tool
{
    use MergesUpdates, RequiresModule, ScopesToFamily;

    public const MODULE = 'calendar';

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum([
                'event_list', 'event_create', 'event_update', 'event_delete',
                'connection_list', 'connection_sync',
                'featured_list', 'featured_create', 'featured_update', 'featured_delete',
            ])->description('Action to perform'),
            'event_id' => $schema->string()->description('Event UUID (required for *_update/*_delete)'),
            'start' => $schema->string()->description('Start date YYYY-MM-DD (event_list, defaults to today)'),
            'end' => $schema->string()->description('End date YYYY-MM-DD (event_list, defaults to today+3mo)'),
            'title' => $schema->string()->description('Event title (required for create)'),
            'description' => $schema->string()->description('Event description'),
            'start_time' => $schema->string()->description('Start datetime ISO or YYYY-MM-DD (required for event_create)'),
            'end_time' => $schema->string()->description('End datetime ISO'),
            'all_day' => $schema->boolean()->description('All-day event (default: true for events)'),
            'location' => $schema->string()->description('Event location'),
            'color' => $schema->string()->description('Hex color (e.g. #8B5CF6)'),
            'recurrence' => $schema->string()->enum(['none', 'yearly', 'monthly', 'weekly'])->description('Recurrence pattern'),
            'visibility' => $schema->string()->enum(['visible', 'busy', 'private'])->description('Who can see this event'),
            'featured_scope' => $schema->string()->enum(['personal', 'family'])->description('Feature on dashboard (null for not featured)'),
            'icon' => $schema->string()->description('Icon/emoji for featured display'),
            'event_date' => $schema->string()->description('Featured event date YYYY-MM-DD (required for featured_create)'),
            'event_time' => $schema->string()->description('Featured event time HH:MM'),
            'is_countdown' => $schema->boolean()->description('Whether this is THE countdown event on the dashboard (only one allowed per family)'),
            'is_active' => $schema->boolean()->description('Whether the featured event is active'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'event_list' => $this->eventList($request),
            'event_create' => $this->eventCreate($request),
            'event_update' => $this->eventUpdate($request),
            'event_delete' => $this->eventDelete($request),
            'connection_list' => $this->connectionList(),
            'connection_sync' => $this->connectionSync(),
            'featured_list' => $this->featuredList(),
            'featured_create' => $this->featuredCreate($request),
            'featured_update' => $this->featuredUpdate($request),
            'featured_delete' => $this->featuredDelete($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function eventList(Request $request): Response
    {
        $start = $request->get('start') ? Carbon::parse($request->get('start')) : now();
        $end = $request->get('end') ? Carbon::parse($request->get('end')) : now()->addMonths(3);

        $allEvents = [];

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

    private function eventCreate(Request $request): Response
    {
        $title = $request->get('title');
        if (! $title) {
            return Response::error('title is required for event_create');
        }

        $startTime = $request->get('start_time') ?? $request->get('start');
        if (! $startTime) {
            return Response::error('start_time is required for event_create');
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

        if ($this->user()->isParent()) {
            $data['featured_scope'] = $request->get('featured_scope');
            $data['icon'] = $request->get('icon');
        }

        if ($request->get('is_countdown') && $this->user()->isParent()) {
            FamilyEvent::where('family_id', $this->familyId())
                ->where('is_countdown', true)
                ->update(['is_countdown' => false]);
            $data['is_countdown'] = true;
        }

        $event = FamilyEvent::create($data);

        return Response::text("Created event: {$event->title} on {$event->start_time->toDateString()}");
    }

    private function eventUpdate(Request $request): Response
    {
        $eventId = $request->get('event_id');
        if (! $eventId) {
            return Response::error('event_id is required for event_update');
        }

        $event = FamilyEvent::where('family_id', $this->familyId())->find($eventId);
        if (! $event) {
            return Response::error("Event not found: {$eventId}");
        }

        if (! $this->user()->isParent() && $event->created_by !== $this->user()->id) {
            return Response::error('You can only edit your own events.');
        }

        $updateData = $this->mergeUpdates(
            $request,
            simpleFields: ['title', 'description', 'location', 'color', 'recurrence', 'visibility', 'featured_scope', 'icon', 'all_day'],
        );

        // Date fields need parsing through Carbon.
        if ($request->get('start_time') || $request->get('start')) {
            $updateData['start_time'] = Carbon::parse($request->get('start_time') ?? $request->get('start'));
        }
        if ($request->get('end_time')) {
            $updateData['end_time'] = Carbon::parse($request->get('end_time'));
        }

        $event->update($updateData);

        return Response::text("Updated event: {$event->title}");
    }

    private function eventDelete(Request $request): Response
    {
        $eventId = $request->get('event_id');
        if (! $eventId) {
            return Response::error('event_id is required for event_delete');
        }

        $event = FamilyEvent::where('family_id', $this->familyId())->find($eventId);
        if (! $event) {
            return Response::error("Event not found: {$eventId}");
        }

        if (! $this->user()->isParent() && $event->created_by !== $this->user()->id) {
            return Response::error('You can only delete your own events.');
        }

        $title = $event->title;
        $event->delete();

        return Response::text("Deleted event: {$title}");
    }

    private function connectionList(): Response
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

    private function connectionSync(): Response
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

    private function featuredList(): Response
    {
        $events = FamilyEvent::where('family_id', $this->familyId())
            ->where('is_active', true)
            ->whereNotNull('featured_scope')
            ->get()
            ->map(function ($event) {
                $event->computed_next_date = $event->next_occurrence;

                return $event;
            })
            ->filter(fn ($event) => $event->computed_next_date->gte(Carbon::today()))
            ->sortBy('computed_next_date')
            ->values();

        return Response::json([
            'events' => $events->map(fn ($e) => [
                'id' => $e->id,
                'title' => $e->title,
                'description' => $e->description,
                'event_date' => $e->start_time->format('Y-m-d'),
                'next_occurrence' => $e->computed_next_date->format('Y-m-d'),
                'days_until' => (int) Carbon::today()->diffInDays($e->computed_next_date, false),
                'event_time' => $e->start_time->format('H:i') !== '00:00' ? $e->start_time->format('H:i') : null,
                'icon' => $e->icon,
                'color' => $e->color,
                'recurrence' => $e->recurrence,
                'is_countdown' => $e->is_countdown,
                'featured_scope' => $e->featured_scope,
            ])->toArray(),
        ]);
    }

    private function featuredCreate(Request $request): Response
    {
        if (! $this->user()->isParent()) {
            return Response::error('Only parents can create featured events.');
        }

        $title = $request->get('title');
        if (! $title) {
            return Response::error('title is required for featured_create.');
        }

        $eventDate = $request->get('event_date');
        if (! $eventDate) {
            return Response::error('event_date is required for featured_create.');
        }

        $isCountdown = $request->get('is_countdown', false);
        if ($isCountdown) {
            FamilyEvent::where('family_id', $this->familyId())
                ->where('is_countdown', true)
                ->update(['is_countdown' => false]);
        }

        $startTime = Carbon::parse($eventDate);
        $eventTime = $request->get('event_time');
        if ($eventTime) {
            $parts = explode(':', $eventTime);
            $startTime->setTime((int) $parts[0], (int) $parts[1]);
        }

        $event = FamilyEvent::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->user()->id,
            'title' => $title,
            'description' => $request->get('description'),
            'start_time' => $startTime,
            'end_time' => null,
            'all_day' => true,
            'icon' => $request->get('icon', "\u{1F389}"),
            'color' => $request->get('color', '#8B5CF6'),
            'recurrence' => $request->get('recurrence', 'none'),
            'featured_scope' => $request->get('featured_scope', 'family'),
            'visibility' => 'visible',
            'is_countdown' => $isCountdown,
        ]);

        return Response::json([
            'message' => "Featured event \"{$event->title}\" created.",
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'event_date' => $event->start_time->format('Y-m-d'),
            ],
        ]);
    }

    private function featuredUpdate(Request $request): Response
    {
        if (! $this->user()->isParent()) {
            return Response::error('Only parents can update featured events.');
        }

        $eventId = $request->get('event_id');
        if (! $eventId) {
            return Response::error('event_id is required for featured_update.');
        }

        $event = FamilyEvent::where('family_id', $this->familyId())
            ->whereNotNull('featured_scope')
            ->find($eventId);

        if (! $event) {
            return Response::error("Featured event not found: {$eventId}");
        }

        $updates = $this->mergeUpdates(
            $request,
            simpleFields: ['title', 'description', 'icon', 'color', 'recurrence', 'is_active', 'featured_scope'],
        );

        if ($request->get('event_date') !== null) {
            $startTime = Carbon::parse($request->get('event_date'));
            if ($request->get('event_time')) {
                $parts = explode(':', $request->get('event_time'));
                $startTime->setTime((int) $parts[0], (int) $parts[1]);
            }
            $updates['start_time'] = $startTime;
        }

        if ($request->get('is_countdown') !== null) {
            if ($request->get('is_countdown')) {
                FamilyEvent::where('family_id', $this->familyId())
                    ->where('id', '!=', $event->id)
                    ->where('is_countdown', true)
                    ->update(['is_countdown' => false]);
            }
            $updates['is_countdown'] = $request->get('is_countdown');
        }

        $event->update($updates);

        return Response::json([
            'message' => "Featured event \"{$event->title}\" updated.",
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'event_date' => $event->start_time->format('Y-m-d'),
                'is_countdown' => $event->is_countdown,
            ],
        ]);
    }

    private function featuredDelete(Request $request): Response
    {
        if (! $this->user()->isParent()) {
            return Response::error('Only parents can delete featured events.');
        }

        $eventId = $request->get('event_id');
        if (! $eventId) {
            return Response::error('event_id is required for featured_delete.');
        }

        $event = FamilyEvent::where('family_id', $this->familyId())
            ->whereNotNull('featured_scope')
            ->find($eventId);

        if (! $event) {
            return Response::error("Featured event not found: {$eventId}");
        }

        $title = $event->title;
        $event->delete();

        return Response::text("Featured event \"{$title}\" deleted.");
    }
}
