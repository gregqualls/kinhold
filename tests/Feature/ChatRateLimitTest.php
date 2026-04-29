<?php

namespace Tests\Feature;

use App\Models\AiUsageDaily;
use App\Models\ChatMessage;
use App\Models\Family;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatRateLimitTest extends TestCase
{
    use RefreshDatabase;

    private Family $family;

    private User $parent;

    protected function setUp(): void
    {
        parent::setUp();

        // Platform Anthropic key for tests; the agent loop's resolveProvider
        // requires one configured.
        config()->set('kinhold.chatbot.api_key', 'test-platform-key');
        config()->set('kinhold.chatbot.plans', [
            'free' => ['name' => 'Free', 'daily_messages' => 25],
            'lite' => ['name' => 'AI Lite', 'daily_messages' => 50],
            'standard' => ['name' => 'AI Standard', 'daily_messages' => 150],
            'pro' => ['name' => 'AI Pro', 'daily_messages' => 300],
        ]);
        config()->set('kinhold.chatbot.default_plan', 'free');
        config()->set('kinhold.chatbot.demo_plan', 'lite');

        config()->set('kinhold.self_hosted', false);

        $this->family = Family::factory()->create([
            'settings' => ['chatbot' => ['plan' => 'free']],
        ]);
        $this->parent = User::factory()->parent()->create(['family_id' => $this->family->id]);

        $this->fakeAnthropicEndTurn('All set.');
    }

    /**
     * Stub the Anthropic Messages endpoint with an end_turn response that
     * carries a usage block — the agent loop sums this into the daily counters.
     */
    private function fakeAnthropicEndTurn(string $text, int $inputTokens = 100, int $outputTokens = 30): void
    {
        Http::fake([
            'api.anthropic.com/*' => Http::response([
                'content' => [['type' => 'text', 'text' => $text]],
                'stop_reason' => 'end_turn',
                'usage' => [
                    'input_tokens' => $inputTokens,
                    'output_tokens' => $outputTokens,
                ],
            ], 200),
        ]);
    }

    public function test_request_under_limit_returns_200_with_usage_payload(): void
    {
        Sanctum::actingAs($this->parent);

        $response = $this->postJson('/api/v1/chat', ['message' => 'hello']);

        $response->assertStatus(200);
        $response->assertJsonPath('usage.enforced', true);
        $response->assertJsonPath('usage.count', 1);
        $response->assertJsonPath('usage.limit', 25);
        $response->assertJsonPath('usage.remaining', 24);
        $response->assertJsonPath('usage.plan.slug', 'free');
        $response->assertJsonStructure(['usage' => ['reset_at']]);

        $this->assertSame(1, AiUsageDaily::where('family_id', $this->family->id)->value('message_count'));
    }

    public function test_request_at_limit_returns_429_with_usage_payload(): void
    {
        Sanctum::actingAs($this->parent);

        // Drop the limit to 1 so the next call lands over the cap.
        $this->family->settings = ['chatbot' => ['daily_message_limit' => 1]];
        $this->family->save();

        // Burn the one allowed message.
        $this->postJson('/api/v1/chat', ['message' => 'first'])->assertStatus(200);

        // Second call hits the lockout.
        $response = $this->postJson('/api/v1/chat', ['message' => 'second']);

        $response->assertStatus(429);
        $response->assertJsonPath('error', 'usage_limit_reached');
        $response->assertJsonPath('usage.enforced', true);
        $response->assertJsonPath('usage.remaining', 0);
        $response->assertJsonPath('usage.plan.slug', 'custom');
        $response->assertJsonStructure(['usage' => ['reset_at']]);

        // The 429-rejected request should NOT have been persisted as a chat
        // message — only the one that succeeded.
        $this->assertSame(1, ChatMessage::where('family_id', $this->family->id)->where('role', 'user')->count());
    }

    public function test_byok_family_bypasses_limit(): void
    {
        $this->family->settings = [
            'ai_mode' => 'byok',
            'ai_provider' => 'anthropic',
            'ai_api_key' => encrypt('sk-ant-byok-key'),
            'chatbot' => ['daily_message_limit' => 1],
        ];
        $this->family->save();

        Sanctum::actingAs($this->parent);

        $this->postJson('/api/v1/chat', ['message' => 'one'])->assertStatus(200);
        $this->postJson('/api/v1/chat', ['message' => 'two'])->assertStatus(200);

        // No row should exist — BYOK doesn't accrue against the platform cap.
        $this->assertSame(0, AiUsageDaily::where('family_id', $this->family->id)->count());
    }

    public function test_self_hosted_env_bypasses_limit(): void
    {
        config()->set('kinhold.self_hosted', true);

        $this->family->settings = ['chatbot' => ['daily_message_limit' => 1]];
        $this->family->save();

        Sanctum::actingAs($this->parent);

        $this->postJson('/api/v1/chat', ['message' => 'one'])->assertStatus(200);
        $this->postJson('/api/v1/chat', ['message' => 'two'])->assertStatus(200);

        $this->assertSame(0, AiUsageDaily::where('family_id', $this->family->id)->count());
    }

    public function test_demo_family_resolves_to_demo_plan(): void
    {
        $demoFamily = Family::factory()->create([
            'slug' => 'q32-demo-family',
            'settings' => [],
        ]);
        $demoUser = User::factory()->parent()->create(['family_id' => $demoFamily->id]);

        Sanctum::actingAs($demoUser);

        $response = $this->postJson('/api/v1/chat', ['message' => 'demo hello']);

        $response->assertStatus(200);
        $response->assertJsonPath('usage.plan.slug', 'lite');
        $response->assertJsonPath('usage.limit', 50);
    }

    public function test_assistant_message_metadata_captures_token_counts(): void
    {
        Sanctum::actingAs($this->parent);

        // setUp() faked Anthropic to return input=100, output=30. We're
        // verifying those values flow through into both the chat_messages
        // metadata column and the ai_usage_daily aggregate.
        $this->postJson('/api/v1/chat', ['message' => 'count me'])->assertStatus(200);

        $assistantMessage = ChatMessage::where('family_id', $this->family->id)
            ->where('role', 'assistant')
            ->latest()
            ->first();

        $this->assertNotNull($assistantMessage);
        $this->assertSame(100, $assistantMessage->metadata['input_tokens'] ?? null);
        $this->assertSame(30, $assistantMessage->metadata['output_tokens'] ?? null);

        $row = AiUsageDaily::where('family_id', $this->family->id)->first();
        $this->assertSame(100, $row->input_tokens);
        $this->assertSame(30, $row->output_tokens);
    }
}
