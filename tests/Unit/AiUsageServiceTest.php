<?php

namespace Tests\Unit;

use App\Models\AiUsageDaily;
use App\Models\Family;
use App\Services\AiUsageService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiUsageServiceTest extends TestCase
{
    use RefreshDatabase;

    private AiUsageService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AiUsageService;

        config()->set('kinhold.chatbot.plans', [
            'free' => ['name' => 'Free', 'daily_messages' => 25],
            'lite' => ['name' => 'AI Lite', 'daily_messages' => 50],
            'standard' => ['name' => 'AI Standard', 'daily_messages' => 150],
            'pro' => ['name' => 'AI Pro', 'daily_messages' => 300],
        ]);
        config()->set('kinhold.chatbot.default_plan', 'free');
        config()->set('kinhold.chatbot.demo_plan', 'lite');
    }

    public function test_today_for_creates_row_with_zero_counters(): void
    {
        $family = Family::factory()->create();

        $row = $this->service->todayFor($family);

        $this->assertInstanceOf(AiUsageDaily::class, $row);
        $this->assertSame(0, $row->message_count);
        $this->assertSame(0, $row->input_tokens);
        $this->assertSame(0, $row->output_tokens);
        $this->assertSame(CarbonImmutable::now('UTC')->toDateString(), $row->date->toDateString());
    }

    public function test_today_for_is_idempotent_on_same_day(): void
    {
        $family = Family::factory()->create();

        $first = $this->service->todayFor($family);
        $second = $this->service->todayFor($family);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, AiUsageDaily::where('family_id', $family->id)->count());
    }

    public function test_record_message_increments_counters(): void
    {
        $family = Family::factory()->create();

        $this->service->recordMessage($family, 100, 50);
        $this->service->recordMessage($family, 200, 80);

        $row = $this->service->todayFor($family);
        $this->assertSame(2, $row->message_count);
        $this->assertSame(300, $row->input_tokens);
        $this->assertSame(130, $row->output_tokens);
    }

    public function test_plan_for_returns_default_plan_when_nothing_set(): void
    {
        $family = Family::factory()->create(['settings' => []]);

        $plan = $this->service->planFor($family);

        $this->assertSame('free', $plan['slug']);
        $this->assertSame(25, $plan['daily_messages']);
    }

    public function test_plan_for_resolves_family_assigned_slug(): void
    {
        $family = Family::factory()->create([
            'settings' => ['chatbot' => ['plan' => 'standard']],
        ]);

        $plan = $this->service->planFor($family);

        $this->assertSame('standard', $plan['slug']);
        $this->assertSame(150, $plan['daily_messages']);
    }

    public function test_plan_for_falls_through_unknown_slugs_to_default(): void
    {
        $family = Family::factory()->create([
            'settings' => ['chatbot' => ['plan' => 'enterprise-deluxe']],
        ]);

        $plan = $this->service->planFor($family);

        $this->assertSame('free', $plan['slug']);
    }

    public function test_plan_for_numeric_override_beats_plan_slug(): void
    {
        $family = Family::factory()->create([
            'settings' => ['chatbot' => ['plan' => 'pro', 'daily_message_limit' => 7]],
        ]);

        $plan = $this->service->planFor($family);

        $this->assertSame('custom', $plan['slug']);
        $this->assertSame('Custom', $plan['name']);
        $this->assertSame(7, $plan['daily_messages']);
    }

    public function test_plan_for_demo_family_uses_demo_plan(): void
    {
        $family = Family::factory()->create([
            'slug' => 'q32-demo-family',
            'settings' => [],
        ]);

        $plan = $this->service->planFor($family);

        $this->assertSame('lite', $plan['slug']);
        $this->assertSame(50, $plan['daily_messages']);
    }

    public function test_is_exhausted_true_when_count_at_limit(): void
    {
        $family = Family::factory()->create([
            'settings' => ['chatbot' => ['daily_message_limit' => 3]],
        ]);

        $this->service->recordMessage($family, 0, 0);
        $this->service->recordMessage($family, 0, 0);
        $this->assertFalse($this->service->isExhausted($family));

        $this->service->recordMessage($family, 0, 0);
        $this->assertTrue($this->service->isExhausted($family));
    }

    public function test_should_enforce_false_for_byok_family(): void
    {
        $family = Family::factory()->create([
            'settings' => [
                'ai_mode' => 'byok',
                'ai_provider' => 'anthropic',
                'ai_api_key' => encrypt('sk-ant-test-key'),
            ],
        ]);

        $this->assertFalse($this->service->shouldEnforce($family));
    }

    public function test_should_enforce_true_for_platform_key_family(): void
    {
        config()->set('kinhold.self_hosted', false);

        $family = Family::factory()->create(['settings' => []]);

        $this->assertTrue($this->service->shouldEnforce($family));
    }

    public function test_should_enforce_false_when_self_hosted(): void
    {
        config()->set('kinhold.self_hosted', true);

        $family = Family::factory()->create(['settings' => []]);
        $this->assertFalse($this->service->shouldEnforce($family));
    }

    public function test_payload_for_includes_plan_and_reset_at(): void
    {
        config()->set('kinhold.self_hosted', false);

        $family = Family::factory()->create([
            'settings' => ['chatbot' => ['plan' => 'lite']],
        ]);

        $this->service->recordMessage($family, 100, 50);

        $payload = $this->service->payloadFor($family);

        $this->assertSame(1, $payload['count']);
        $this->assertSame(50, $payload['limit']);
        $this->assertSame(49, $payload['remaining']);
        $this->assertTrue($payload['enforced']);
        $this->assertSame('lite', $payload['plan']['slug']);
        $this->assertSame('AI Lite', $payload['plan']['name']);
        $this->assertNotEmpty($payload['reset_at']);
    }

    public function test_payload_for_byok_reports_zero_count_and_unenforced(): void
    {
        $family = Family::factory()->create([
            'settings' => [
                'ai_mode' => 'byok',
                'ai_provider' => 'anthropic',
                'ai_api_key' => encrypt('sk-ant-test-key'),
                'chatbot' => ['plan' => 'pro'],
            ],
        ]);

        $payload = $this->service->payloadFor($family);

        $this->assertFalse($payload['enforced']);
        $this->assertSame(0, $payload['count']);
        $this->assertSame(300, $payload['limit']); // plan still resolves; chip just hides on FE
    }
}
