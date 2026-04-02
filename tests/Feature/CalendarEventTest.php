<?php

namespace Tests\Feature;

use App\Enums\FamilyRole;
use App\Models\Family;
use App\Models\FamilyEvent;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CalendarEventTest extends TestCase
{
    use RefreshDatabase;

    private Family $family;

    private Family $otherFamily;

    private User $parent;

    private User $child;

    private User $otherParent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->family = Family::create(['name' => 'Test Family', 'slug' => 'test-family', 'invite_code' => 'TEST01']);
        $this->otherFamily = Family::create(['name' => 'Other Family', 'slug' => 'other-family', 'invite_code' => 'OTHER1']);

        $this->parent = User::create([
            'name' => 'Parent',
            'email' => 'parent@test.com',
            'password' => bcrypt('password'),
            'family_id' => $this->family->id,
            'family_role' => FamilyRole::Parent,
        ]);

        $this->child = User::create([
            'name' => 'Child',
            'email' => 'child@test.com',
            'password' => bcrypt('password'),
            'family_id' => $this->family->id,
            'family_role' => FamilyRole::Child,
        ]);

        $this->otherParent = User::create([
            'name' => 'Other Parent',
            'email' => 'other@test.com',
            'password' => bcrypt('password'),
            'family_id' => $this->otherFamily->id,
            'family_role' => FamilyRole::Parent,
        ]);
    }

    // ── CRUD Tests ──

    public function test_parent_can_create_calendar_event(): void
    {
        Sanctum::actingAs($this->parent);

        $response = $this->postJson('/api/v1/calendar/events', [
            'title' => 'Family Dinner',
            'start_time' => '2026-05-01T18:00:00',
            'end_time' => '2026-05-01T20:00:00',
            'all_day' => false,
            'location' => 'Home',
            'color' => '#8B5CF6',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('event.title', 'Family Dinner');
        $this->assertDatabaseHas('family_events', ['title' => 'Family Dinner', 'family_id' => $this->family->id]);
    }

    public function test_child_can_create_calendar_event(): void
    {
        Sanctum::actingAs($this->child);

        $response = $this->postJson('/api/v1/calendar/events', [
            'title' => 'Study Group',
            'start_time' => '2026-05-02T15:00:00',
            'all_day' => false,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('family_events', ['title' => 'Study Group', 'created_by' => $this->child->id]);
    }

    public function test_can_create_event_with_visibility(): void
    {
        Sanctum::actingAs($this->parent);

        $response = $this->postJson('/api/v1/calendar/events', [
            'title' => 'Private Meeting',
            'start_time' => '2026-05-03T10:00:00',
            'visibility' => 'private',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('family_events', ['title' => 'Private Meeting', 'visibility' => 'private']);
    }

    public function test_can_create_event_with_recurrence(): void
    {
        Sanctum::actingAs($this->parent);

        $response = $this->postJson('/api/v1/calendar/events', [
            'title' => 'Weekly Team Sync',
            'start_time' => '2026-05-05T09:00:00',
            'recurrence' => 'weekly',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('family_events', ['title' => 'Weekly Team Sync', 'recurrence' => 'weekly']);
    }

    public function test_can_update_calendar_event(): void
    {
        Sanctum::actingAs($this->parent);

        $event = FamilyEvent::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Old Title',
            'start_time' => '2026-05-01T18:00:00',
            'all_day' => false,
        ]);

        $response = $this->putJson("/api/v1/calendar/events/{$event->id}", [
            'title' => 'New Title',
            'location' => 'New Location',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('family_events', ['id' => $event->id, 'title' => 'New Title']);
    }

    public function test_can_delete_calendar_event(): void
    {
        Sanctum::actingAs($this->parent);

        $event = FamilyEvent::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'To Delete',
            'start_time' => '2026-05-01T18:00:00',
        ]);

        $response = $this->deleteJson("/api/v1/calendar/events/{$event->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('family_events', ['id' => $event->id]);
    }

    // ── Cross-Family Isolation ──

    public function test_cannot_update_other_familys_event(): void
    {
        Sanctum::actingAs($this->parent);

        $event = FamilyEvent::create([
            'family_id' => $this->otherFamily->id,
            'created_by' => $this->otherParent->id,
            'title' => 'Their Event',
            'start_time' => '2026-05-01T18:00:00',
        ]);

        $response = $this->putJson("/api/v1/calendar/events/{$event->id}", [
            'title' => 'Hacked',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('family_events', ['id' => $event->id, 'title' => 'Their Event']);
    }

    public function test_cannot_delete_other_familys_event(): void
    {
        Sanctum::actingAs($this->parent);

        $event = FamilyEvent::create([
            'family_id' => $this->otherFamily->id,
            'created_by' => $this->otherParent->id,
            'title' => 'Their Event',
            'start_time' => '2026-05-01T18:00:00',
        ]);

        $response = $this->deleteJson("/api/v1/calendar/events/{$event->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('family_events', ['id' => $event->id]);
    }

    // ── Event Aggregation ──

    public function test_events_endpoint_includes_manual_events(): void
    {
        Sanctum::actingAs($this->parent);

        FamilyEvent::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Manual Event',
            'start_time' => now()->addDays(1),
            'all_day' => true,
        ]);

        $response = $this->getJson('/api/v1/calendar/events?start='.now()->format('Y-m-d').'&end='.now()->addDays(7)->format('Y-m-d'));

        $response->assertStatus(200);
        $titles = collect($response->json('events'))->pluck('title')->toArray();
        $this->assertContains('Manual Event', $titles);
    }

    public function test_events_endpoint_includes_tasks(): void
    {
        Sanctum::actingAs($this->parent);

        Task::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Due Task',
            'due_date' => now()->addDays(2),
        ]);

        $response = $this->getJson('/api/v1/calendar/events?start='.now()->format('Y-m-d').'&end='.now()->addDays(7)->format('Y-m-d'));

        $response->assertStatus(200);
        $titles = collect($response->json('events'))->pluck('title')->toArray();
        $this->assertTrue(
            collect($titles)->contains(fn ($t) => str_contains($t, 'Due Task')),
            'Task should appear in calendar events'
        );
    }

    public function test_private_events_hidden_from_non_creator(): void
    {
        // Parent creates a private event
        $event = FamilyEvent::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Secret Meeting',
            'start_time' => now()->addDays(1),
            'all_day' => true,
            'visibility' => 'private',
        ]);

        // Child should not see it
        Sanctum::actingAs($this->child);
        $response = $this->getJson('/api/v1/calendar/events?start='.now()->format('Y-m-d').'&end='.now()->addDays(7)->format('Y-m-d'));

        $titles = collect($response->json('events'))->pluck('title')->toArray();
        $this->assertNotContains('Secret Meeting', $titles);
    }

    public function test_busy_events_masked_for_non_creator(): void
    {
        $event = FamilyEvent::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Important Interview',
            'start_time' => now()->addDays(1),
            'all_day' => true,
            'visibility' => 'busy',
        ]);

        // Child sees "Busy" instead of the real title
        Sanctum::actingAs($this->child);
        $response = $this->getJson('/api/v1/calendar/events?start='.now()->format('Y-m-d').'&end='.now()->addDays(7)->format('Y-m-d'));

        $events = collect($response->json('events'));
        $busyEvent = $events->first(fn ($e) => $e['id'] === $event->id);
        $this->assertEquals('Busy', $busyEvent['title']);
    }

    // ── Featured Events (via unified model) ──

    public function test_featured_endpoint_returns_featured_events(): void
    {
        Sanctum::actingAs($this->parent);

        FamilyEvent::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Birthday Party',
            'start_time' => now()->addDays(5),
            'all_day' => true,
            'featured_scope' => 'family',
            'icon' => 'cake',
            'color' => '#EC4899',
        ]);

        $response = $this->getJson('/api/v1/featured-events');

        $response->assertStatus(200);
        $titles = collect($response->json('featured_events'))->pluck('title')->toArray();
        $this->assertContains('Birthday Party', $titles);
    }

    public function test_countdown_auto_expires_past_non_recurring(): void
    {
        Sanctum::actingAs($this->parent);

        FamilyEvent::create([
            'family_id' => $this->family->id,
            'created_by' => $this->parent->id,
            'title' => 'Past Event',
            'start_time' => now()->subDays(2),
            'all_day' => true,
            'is_countdown' => true,
            'featured_scope' => 'family',
            'recurrence' => 'none',
        ]);

        $response = $this->getJson('/api/v1/featured-events/countdown');

        $response->assertStatus(200);
        $response->assertJsonPath('countdown_event', null);
    }

    public function test_only_parent_can_create_featured_event(): void
    {
        Sanctum::actingAs($this->child);

        $response = $this->postJson('/api/v1/featured-events', [
            'title' => 'Kid Event',
            'event_date' => now()->addDays(5)->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
    }
}
