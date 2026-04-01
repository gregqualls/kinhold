<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GoogleLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_link_with_correct_password_links_google_and_returns_token(): void
    {
        $user = User::factory()->create(['password' => Hash::make('Password1')]);

        $pendingCode = str_repeat('a', 64);
        Cache::put("google_link_pending:{$pendingCode}", [
            'user_id' => $user->id,
            'google_id' => 'google_123',
            'google_avatar' => 'https://google.com/avatar.jpg',
        ], now()->addMinutes(10));

        $response = $this->postJson('/api/v1/auth/google/confirm-link', [
            'pending_code' => $pendingCode,
            'password' => 'Password1',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user', 'message']);

        $user->refresh();
        $this->assertEquals('google_123', $user->google_id);
    }

    public function test_confirm_link_with_wrong_password_fails(): void
    {
        $user = User::factory()->create(['password' => Hash::make('Password1')]);

        $pendingCode = str_repeat('b', 64);
        Cache::put("google_link_pending:{$pendingCode}", [
            'user_id' => $user->id,
            'google_id' => 'google_456',
            'google_avatar' => 'https://google.com/avatar.jpg',
        ], now()->addMinutes(10));

        $response = $this->postJson('/api/v1/auth/google/confirm-link', [
            'pending_code' => $pendingCode,
            'password' => 'WrongPassword1',
        ]);

        $response->assertStatus(401);

        $user->refresh();
        $this->assertNull($user->google_id);
    }

    public function test_confirm_link_with_expired_code_fails(): void
    {
        $response = $this->postJson('/api/v1/auth/google/confirm-link', [
            'pending_code' => str_repeat('c', 64),
            'password' => 'Password1',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_unlink_google(): void
    {
        $user = User::factory()->create([
            'google_id' => 'google_789',
            'password' => Hash::make('Password1'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/v1/auth/google/unlink');

        $response->assertStatus(200);
        $user->refresh();
        $this->assertNull($user->google_id);
    }

    public function test_cannot_unlink_google_without_password(): void
    {
        $user = User::factory()->create([
            'google_id' => 'google_101',
            'password' => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/v1/auth/google/unlink');

        $response->assertStatus(422);
        $user->refresh();
        $this->assertEquals('google_101', $user->google_id);
    }
}
