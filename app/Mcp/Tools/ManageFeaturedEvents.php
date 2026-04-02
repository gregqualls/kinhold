<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\FamilyEvent;
use Carbon\Carbon;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('manage-featured-events')]
#[Description('List, create, update, or delete featured/countdown events. Actions: list, create, update, delete. Write actions are parent-only.')]
class ManageFeaturedEvents extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum(['list', 'create', 'update', 'delete'])->description('Action to perform'),
            'event_id' => $schema->string()->description('Featured event UUID (required for update/delete)'),
            'title' => $schema->string()->description('Event title (required for create)'),
            'description' => $schema->string()->description('Event description'),
            'event_date' => $schema->string()->description('Event date in YYYY-MM-DD format (required for create)'),
            'event_time' => $schema->string()->description('Event time in HH:MM format'),
            'icon' => $schema->string()->description('Emoji icon for the event'),
            'color' => $schema->string()->description('Hex color (e.g. #8B5CF6)'),
            'recurrence' => $schema->string()->enum(['none', 'yearly', 'monthly', 'weekly'])->description('Recurrence pattern'),
            'is_countdown' => $schema->boolean()->description('Whether this is the countdown event on the dashboard'),
            'is_active' => $schema->boolean()->description('Whether the event is active'),
            'featured_scope' => $schema->string()->enum(['personal', 'family'])->description('Feature scope: personal (just you) or family (everyone)'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'list' => $this->listEvents(),
            'create' => $this->createEvent($request),
            'update' => $this->updateEvent($request),
            'delete' => $this->deleteEvent($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function listEvents(): Response
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

    private function createEvent(Request $request): Response
    {
        if (! $this->user()->isParent()) {
            return Response::error('Only parents can create featured events.');
        }

        $title = $request->get('title');
        if (! $title) {
            return Response::error('title is required to create a featured event.');
        }

        $eventDate = $request->get('event_date');
        if (! $eventDate) {
            return Response::error('event_date is required to create a featured event.');
        }

        $isCountdown = $request->get('is_countdown', false);

        if ($isCountdown) {
            FamilyEvent::where('family_id', $this->familyId())
                ->where('is_countdown', true)
                ->update(['is_countdown' => false]);
        }

        // Convert event_date + event_time to start_time
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

    private function updateEvent(Request $request): Response
    {
        if (! $this->user()->isParent()) {
            return Response::error('Only parents can update featured events.');
        }

        $eventId = $request->get('event_id');
        if (! $eventId) {
            return Response::error('event_id is required for update.');
        }

        $event = FamilyEvent::where('family_id', $this->familyId())
            ->whereNotNull('featured_scope')
            ->find($eventId);

        if (! $event) {
            return Response::error("Featured event not found: {$eventId}");
        }

        $updates = [];
        foreach (['title', 'description', 'icon', 'color', 'recurrence', 'is_active', 'featured_scope'] as $field) {
            if ($request->get($field) !== null) {
                $updates[$field] = $request->get($field);
            }
        }

        // Convert event_date + event_time to start_time if provided
        if ($request->get('event_date') !== null) {
            $startTime = Carbon::parse($request->get('event_date'));
            if ($request->get('event_time')) {
                $parts = explode(':', $request->get('event_time'));
                $startTime->setTime((int) $parts[0], (int) $parts[1]);
            }
            $updates['start_time'] = $startTime;
        }

        // Handle countdown toggle
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

    private function deleteEvent(Request $request): Response
    {
        if (! $this->user()->isParent()) {
            return Response::error('Only parents can delete featured events.');
        }

        $eventId = $request->get('event_id');
        if (! $eventId) {
            return Response::error('event_id is required for delete.');
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
