<?php

namespace Tests\Feature\Settings;

use App\Models\Family;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AiKeyTestEndpointTest extends TestCase
{
    use RefreshDatabase;

    private Family $family;

    private User $parent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->family = Family::factory()->create();
        $this->parent = User::factory()->parent()->create(['family_id' => $this->family->id]);
    }

    public function test_requires_authentication(): void
    {
        $this->postJson('/api/v1/settings/ai/test', ['api_key' => 'sk-ant-x'])
            ->assertStatus(401);
    }

    public function test_requires_parent_role(): void
    {
        $child = User::factory()->child()->create(['family_id' => $this->family->id]);
        Sanctum::actingAs($child);

        $this->postJson('/api/v1/settings/ai/test', ['api_key' => 'sk-ant-x'])
            ->assertStatus(403);
    }

    public function test_validates_api_key_required(): void
    {
        Sanctum::actingAs($this->parent);

        $this->postJson('/api/v1/settings/ai/test', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('api_key');
    }

    public function test_returns_valid_true_for_good_key(): void
    {
        Sanctum::actingAs($this->parent);

        Http::fake([
            'api.anthropic.com/*' => Http::response([
                'id' => 'msg_test',
                'content' => [['type' => 'text', 'text' => 'ok']],
            ], 200),
        ]);

        $this->postJson('/api/v1/settings/ai/test', ['api_key' => 'sk-ant-valid'])
            ->assertStatus(200)
            ->assertJson(['valid' => true]);
    }

    public function test_returns_invalid_for_401(): void
    {
        Sanctum::actingAs($this->parent);

        Http::fake([
            'api.anthropic.com/*' => Http::response([
                'type' => 'error',
                'error' => ['type' => 'authentication_error', 'message' => 'invalid x-api-key'],
            ], 401),
        ]);

        $this->postJson('/api/v1/settings/ai/test', ['api_key' => 'sk-ant-bogus'])
            ->assertStatus(200)
            ->assertJson(['valid' => false, 'error' => 'Invalid API key.']);
    }

    public function test_does_not_persist_key(): void
    {
        Sanctum::actingAs($this->parent);

        Http::fake([
            'api.anthropic.com/*' => Http::response(['content' => []], 200),
        ]);

        $this->postJson('/api/v1/settings/ai/test', ['api_key' => 'sk-ant-shouldnotsave'])
            ->assertStatus(200);

        $this->family->refresh();
        $this->assertArrayNotHasKey('ai_api_key', $this->family->settings ?? []);
    }
}
