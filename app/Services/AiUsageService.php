<?php

namespace App\Services;

use App\Models\AiUsageDaily;
use App\Models\Family;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class AiUsageService
{
    /**
     * Whether AI-usage limits should be enforced for this family.
     *
     * Self-hosted instances bypass entirely (their key, their cost). BYOK
     * families also bypass — AgentService::resolveApiKey returns the family's
     * own key, so Kinhold never pays for that request. Everyone else pays the
     * platform bill and is therefore subject to the daily cap.
     */
    public function shouldEnforce(Family $family): bool
    {
        if ((bool) config('kinhold.self_hosted', false)) {
            return false;
        }

        return AgentService::usesPlatformKey($family);
    }

    /**
     * Resolve the plan row that applies to this family. See planFor() precedence
     * comment for the rules.
     *
     * @return array{name: string, daily_messages: int, slug: string}
     */
    public function planFor(Family $family): array
    {
        $settings = $family->settings ?? [];
        $chatbot = $settings['chatbot'] ?? [];

        // 1. Numeric override — admin / support escape hatch. No UI; set via tinker.
        if (isset($chatbot['daily_message_limit']) && is_numeric($chatbot['daily_message_limit'])) {
            return [
                'slug' => 'custom',
                'name' => 'Custom',
                'daily_messages' => max(0, (int) $chatbot['daily_message_limit']),
            ];
        }

        $plans = config('kinhold.chatbot.plans', []);

        // 2. Family-set plan slug.
        $familySlug = $chatbot['plan'] ?? null;
        if (is_string($familySlug) && isset($plans[$familySlug])) {
            return $this->planRow($familySlug, $plans[$familySlug]);
        }

        // 3. Demo family default.
        if ($family->slug === 'q32-demo-family') {
            $demoSlug = config('kinhold.chatbot.demo_plan', 'lite');
            if (isset($plans[$demoSlug])) {
                return $this->planRow($demoSlug, $plans[$demoSlug]);
            }
        }

        // 4. Global default.
        $defaultSlug = config('kinhold.chatbot.default_plan', 'free');
        if (isset($plans[$defaultSlug])) {
            return $this->planRow($defaultSlug, $plans[$defaultSlug]);
        }

        // Defensive fallback — should only hit if config is malformed.
        return ['slug' => 'free', 'name' => 'Free', 'daily_messages' => 0];
    }

    public function limitFor(Family $family): int
    {
        return $this->planFor($family)['daily_messages'];
    }

    /**
     * Get-or-create today's usage row for the family. UTC throughout — copy says
     * "Resets at midnight UTC."
     *
     * Passes a Carbon instance to firstOrCreate so the model's `date` cast
     * normalizes both the INSERT value and the WHERE clause. Querying with a
     * raw 'Y-m-d' string mismatches Laravel's stored datetime form
     * ('Y-m-d 00:00:00') in sqlite and silently fails the unique-row lookup.
     */
    public function todayFor(Family $family): AiUsageDaily
    {
        $today = CarbonImmutable::now('UTC')->startOfDay();

        return AiUsageDaily::firstOrCreate(
            ['family_id' => $family->id, 'date' => $today],
            ['message_count' => 0, 'input_tokens' => 0, 'output_tokens' => 0],
        );
    }

    public function isExhausted(Family $family): bool
    {
        $limit = $this->limitFor($family);
        if ($limit <= 0) {
            return true;
        }

        return $this->todayFor($family)->message_count >= $limit;
    }

    /**
     * Increment today's counters in a single atomic UPDATE. `incrementEach`
     * validates each amount is numeric before composing the SET clause, so we
     * don't have to hand-build raw SQL with concatenated values.
     */
    public function recordMessage(Family $family, int $inputTokens, int $outputTokens): AiUsageDaily
    {
        $row = $this->todayFor($family);

        DB::table('ai_usage_daily')
            ->where('id', $row->id)
            ->incrementEach(
                [
                    'message_count' => 1,
                    'input_tokens' => max(0, $inputTokens),
                    'output_tokens' => max(0, $outputTokens),
                ],
                ['updated_at' => now()],
            );

        return $row->fresh() ?? $row;
    }

    /**
     * Next reset moment — tomorrow at 00:00 UTC.
     */
    public function resetAt(): CarbonImmutable
    {
        return CarbonImmutable::now('UTC')->addDay()->startOfDay();
    }

    /**
     * Build the JSON payload the chat endpoint returns alongside every message
     * (and as the body of a 429). Frontend uses this to render the chip and
     * the lockout state.
     *
     * @return array{count: int, limit: int, remaining: int, reset_at: string, enforced: bool, plan: array{slug: string, name: string}}
     */
    public function payloadFor(Family $family): array
    {
        $enforced = $this->shouldEnforce($family);
        $plan = $this->planFor($family);
        $count = $enforced ? $this->todayFor($family)->message_count : 0;
        $limit = $plan['daily_messages'];
        $remaining = max(0, $limit - $count);

        return [
            'count' => $count,
            'limit' => $limit,
            'remaining' => $remaining,
            'reset_at' => $this->resetAt()->toIso8601String(),
            'enforced' => $enforced,
            'plan' => [
                'slug' => $plan['slug'],
                'name' => $plan['name'],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array{slug: string, name: string, daily_messages: int}
     */
    private function planRow(string $slug, array $row): array
    {
        return [
            'slug' => $slug,
            'name' => (string) ($row['name'] ?? ucfirst($slug)),
            'daily_messages' => (int) ($row['daily_messages'] ?? 0),
        ];
    }
}
