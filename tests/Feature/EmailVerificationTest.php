<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_sends_verification_email(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'verify@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'family_name' => 'Test Family',
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'verify@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        // Verification notification should have been sent
        Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
    }

    public function test_resend_verification_works(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email_verified_at' => null]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/email/resend');

        $response->assertStatus(200);
        Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
    }

    public function test_resend_verification_skips_if_already_verified(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email_verified_at' => now()]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/email/resend');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Email already verified']);
        Notification::assertNotSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
    }

    public function test_user_resource_includes_email_verified_at(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/user');

        $response->assertStatus(200);
        $response->assertJsonPath('user.email_verified_at', fn ($v) => $v !== null);
    }

    public function test_user_resource_includes_google_id_boolean(): void
    {
        $user = User::factory()->create(['google_id' => 'some_google_id']);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/user');

        $response->assertStatus(200);
        // Should be boolean true, not the actual google ID
        $response->assertJsonPath('user.google_id', true);
    }
}
