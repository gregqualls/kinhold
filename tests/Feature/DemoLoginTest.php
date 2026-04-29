<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\Family;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_login_wipes_that_users_chat_history(): void
    {
        $family = Family::create([
            'name' => 'Demo',
            'slug' => 'q32-demo-family',
            'invite_code' => 'DEMO',
            'settings' => [],
        ]);

        $mike = User::factory()->create([
            'name' => 'Mike',
            'family_id' => $family->id,
        ]);

        ChatMessage::create([
            'family_id' => $family->id,
            'user_id' => $mike->id,
            'role' => 'user',
            'message' => 'leftover from previous visitor',
        ]);
        ChatMessage::create([
            'family_id' => $family->id,
            'user_id' => $mike->id,
            'role' => 'assistant',
            'message' => 'leftover assistant reply',
        ]);

        $this->assertDatabaseCount('chat_messages', 2);

        $response = $this->postJson('/api/v1/demo-login', ['member' => 'mike']);

        $response->assertOk();
        $this->assertDatabaseCount('chat_messages', 0);
    }

    public function test_demo_login_does_not_wipe_other_demo_users_chat(): void
    {
        $family = Family::create([
            'name' => 'Demo',
            'slug' => 'q32-demo-family',
            'invite_code' => 'DEMO',
            'settings' => [],
        ]);

        $mike = User::factory()->create(['name' => 'Mike', 'family_id' => $family->id]);
        $sarah = User::factory()->create(['name' => 'Sarah', 'family_id' => $family->id]);

        ChatMessage::create([
            'family_id' => $family->id,
            'user_id' => $sarah->id,
            'role' => 'user',
            'message' => "Sarah's chat",
        ]);

        $this->postJson('/api/v1/demo-login', ['member' => 'mike'])->assertOk();

        $this->assertDatabaseHas('chat_messages', ['user_id' => $sarah->id]);
    }
}
