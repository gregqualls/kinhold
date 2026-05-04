<?php

namespace App\Services;

use App\Models\AiUsageDaily;
use App\Models\Family;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
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
     * Resolve the plan row that applies to this family.
     *
     * Precedence:
     *   1. Numeric override (`settings.chatbot.daily_message_limit`) — admin escape hatch.
     *   2. Family-set plan slug (`settings.chatbot.plan`) — explicit pick from BillingPanel.
     *   3. Trial fallback — Cashier-trialing families with no explicit pick get Lite
     *      free (issue #230). Settings stay null so the trial-end webhook can clear
     *      the implicit grant cleanly without diffing.
     *   4. Demo family default (`kinhold.chatbot.demo_plan`).
     *   5. Global default (`kinhold.chatbot.default_plan`).
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

        // 3. Trial fallback — see precedence note above. Skip the subscription
        // lookup entirely for families that can't possibly be on a Cashier trial
        // (no Stripe customer = never went through checkout). Avoids loading the
        // subscriptions relation per chat message in the BILLING_ENABLED=false
        // and self-hosted cases (the chat hot path).
        if ($family->stripe_id && isset($plans['lite']) && $family->subscription('default')?->onTrial()) {
            return $this->planRow('lite', $plans['lite']);
        }

        // 4. Demo family default.
        if ($family->slug === 'q32-demo-family') {
            $demoSlug = config('kinhold.chatbot.demo_plan', 'lite');
            if (isset($plans[$demoSlug])) {
                return $this->planRow($demoSlug, $plans[$demoSlug]);
            }
        }

        // 5. Global default.
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

    /* ===== Demo-family caps (#266) ===========================================
     * The kinhold.app marketing demo runs on a shared family. Without these,
     * one motivated visitor can burn the whole daily cap, and a busy week can
     * easily run real Anthropic spend into 2-3 figures. Three layered caps:
     *
     *   1. Per-session — caps a single visitor's browser session at N messages
     *      so the experience is "try it, then sign up to chat more."
     *   2. Family-wide daily — already enforced via planFor() returning the
     *      `demo` plan tier. Just scoped lower than Lite.
     *   3. Monthly cost — circuit breaker on month-to-date estimated spend.
     *      When tripped, demo AI goes silent until the next month.
     * ====================================================================== */

    /**
     * Demo session quota. Returns true when a visitor's browser session has
     * hit the per-session cap and should be told to sign up. Counter ticks via
     * `incrementDemoSession()` — call that after a successful chat send.
     */
    public function isDemoSessionExhausted(string $sessionKey): bool
    {
        $limit = (int) config('kinhold.chatbot.demo_session_limit', 3);
        if ($limit <= 0) {
            return false;
        }

        return $this->demoSessionCount($sessionKey) >= $limit;
    }

    public function demoSessionCount(string $sessionKey): int
    {
        return (int) Cache::get($this->demoSessionCacheKey($sessionKey), 0);
    }

    public function incrementDemoSession(string $sessionKey): void
    {
        $key = $this->demoSessionCacheKey($sessionKey);
        // 24h TTL — long enough to outlast a single demo session, short
        // enough that the slot recycles within a day for new visitors.
        Cache::put($key, $this->demoSessionCount($sessionKey) + 1, now()->addDay());
    }

    public function demoSessionLimit(): int
    {
        return (int) config('kinhold.chatbot.demo_session_limit', 3);
    }

    private function demoSessionCacheKey(string $sessionKey): string
    {
        return 'demo-ai-session:'.sha1($sessionKey);
    }

    /**
     * Estimated month-to-date Anthropic spend for the demo family in cents.
     * Sums input/output tokens across this calendar month and applies the
     * configured per-million-token rates.
     */
    public function monthlyDemoCostCents(Family $family): int
    {
        $rates = (array) config('kinhold.chatbot.demo_cost', []);
        $inRate = (int) ($rates['input_per_million_cents'] ?? 80);
        $outRate = (int) ($rates['output_per_million_cents'] ?? 400);

        $start = CarbonImmutable::now('UTC')->startOfMonth();

        $row = DB::table('ai_usage_daily')
            ->where('family_id', $family->id)
            ->where('date', '>=', $start)
            ->selectRaw('COALESCE(SUM(input_tokens),0) AS input_tokens, COALESCE(SUM(output_tokens),0) AS output_tokens')
            ->first();

        $in = (int) ($row->input_tokens ?? 0);
        $out = (int) ($row->output_tokens ?? 0);

        // (tokens / 1_000_000) * rate_per_million → cents. Use intdiv-style
        // math to avoid float drift when the totals are small.
        return (int) (($in * $inRate + $out * $outRate) / 1_000_000);
    }

    public function isDemoMonthExhausted(Family $family): bool
    {
        $ceiling = (int) config('kinhold.chatbot.demo_monthly_cost_ceiling_cents', 0);
        if ($ceiling <= 0) {
            return false;
        }

        return $this->monthlyDemoCostCents($family) >= $ceiling;
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
