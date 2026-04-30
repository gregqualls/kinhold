<?php

use App\Models\Family;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * Normalize family settings rows where `ai_provider` is set to a slug that
 * `AgentService::availableProviders()` no longer exposes (currently `openai`
 * and `google`, removed in #201 because the agent loop has no tool_use adapter
 * for them).
 *
 * Why this matters: prior to #201 these slugs were valid in the picker, so
 * families could persist them with a paired BYOK key. After #201 the API
 * validation rejects new writes — but already-persisted bad rows still cause
 * `AgentService::resolveApiKey()` to return null (slug guard at line 413),
 * which falls through to the platform Anthropic key. The family thinks
 * they're using their own OpenAI/Google key; Kinhold quietly pays Anthropic.
 *
 * Safe normalization for affected families:
 *   - `ai_provider` → `anthropic` (the only adapter that exists)
 *   - `ai_api_key` cleared (the stored key was for the wrong vendor)
 *   - `ai_mode` → `kinhold` (back to platform AI; explicit default rather
 *     than half-broken BYOK with no key)
 *
 * Logs each affected family so support can reach out if a real customer was
 * downgraded silently. Down() is intentionally a no-op — re-creating the
 * stale state on rollback would re-introduce the bug, and we have no record
 * of the original keys (they were encrypted and we just cleared them).
 */
return new class extends Migration
{
    public function up(): void
    {
        $stale = ['openai', 'google'];

        Family::query()
            ->whereNotNull('settings')
            ->get()
            ->each(function (Family $family) use ($stale) {
                $settings = $family->settings ?? [];
                $provider = $settings['ai_provider'] ?? null;

                if (! in_array($provider, $stale, true)) {
                    return;
                }

                Log::info('Normalizing stale ai_provider on family settings', [
                    'family_id' => $family->id,
                    'previous_provider' => $provider,
                    'had_byok_key' => ! empty($settings['ai_api_key']),
                    'previous_mode' => $settings['ai_mode'] ?? null,
                ]);

                $settings['ai_provider'] = 'anthropic';
                $settings['ai_api_key'] = '';
                $settings['ai_mode'] = 'kinhold';

                $family->update(['settings' => $settings]);
            });
    }

    public function down(): void
    {
        // Intentionally empty. Restoring `openai`/`google` would re-introduce
        // the silent-downgrade bug #201 closes, and the cleared API keys are
        // unrecoverable. Affected families need to re-enter a key + pick a
        // (now-valid) provider through Settings.
    }
};
