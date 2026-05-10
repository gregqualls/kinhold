<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterHydratesFullUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_then_user_endpoint_returns_full_payload(): void
    {
        config()->set('kinhold.billing_enabled', true);

        $register = $this->postJson('/api/v1/register', [
            'name' => 'Reg Test',
            'email' => 'reg@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'family_name' => 'Reg Family',
        ]);

        $register->assertStatus(201);
        $token = $register->json('token');
        $this->assertNotEmpty($token);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/user');

        $response->assertStatus(200);

        $userId = $response->json('user.id');

        $response
            ->assertJsonPath('user.role', 'parent')
            ->assertJsonPath('family.billing_owner_id', $userId)
            ->assertJsonPath('family.is_demo', false)
            ->assertJsonStructure([
                'user' => ['id', 'role', 'email_verified_at'],
                'family' => [
                    'id',
                    'billing_owner_id',
                    'is_demo',
                    'module_access',
                    'members' => [['id', 'name', 'family_role']],
                    'billing',
                ],
            ]);

        $this->assertNotNull($response->json('family.billing'), 'family.billing must be populated when BILLING_ENABLED=true');

        $moduleAccess = $response->json('family.module_access');
        $this->assertIsArray($moduleAccess);
        $this->assertArrayHasKey('calendar', $moduleAccess);

        $members = $response->json('family.members');
        $this->assertIsArray($members);
        $this->assertCount(1, $members);
        $this->assertSame($userId, $members[0]['id']);
    }

    public function test_billing_payload_is_null_when_billing_disabled(): void
    {
        config()->set('kinhold.billing_enabled', false);

        $register = $this->postJson('/api/v1/register', [
            'name' => 'No Billing',
            'email' => 'nobilling@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'family_name' => 'No Billing Family',
        ])->assertStatus(201);

        $token = $register->json('token');

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/user')
            ->assertStatus(200);

        $this->assertNull($response->json('family.billing'));
    }
}
