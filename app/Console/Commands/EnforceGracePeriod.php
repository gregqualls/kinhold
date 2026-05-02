<?php

namespace App\Console\Commands;

use App\Models\Family;
use App\Notifications\Billing\PaymentRetryNotification;
use App\Notifications\Billing\SubscriptionDowngradedNotification;
use App\Services\BillingService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Daily sweep of families with `payment_failed_at` set. Drives the day-3
 * reminder and day-7 downgrade. Day-0 notification is sent inline by the
 * webhook handler — keeping it out of here means the user hears about the
 * failure within seconds, not at the next 03:00 UTC tick.
 *
 * Idempotent: each step writes a marker into `families.settings` so a missed
 * run (or two) on a given day doesn't fire duplicate emails.
 */
class EnforceGracePeriod extends Command
{
    protected $signature = 'kinhold:enforce-grace-period';

    protected $description = 'Sweep families in a payment-failed grace period; fire day-3 reminders and day-7 downgrades (#221 / 70-H)';

    public function handle(BillingService $billing): int
    {
        $remindersSent = 0;
        $downgrades = 0;

        Family::query()
            ->whereNotNull('payment_failed_at')
            ->each(function (Family $family) use ($billing, &$remindersSent, &$downgrades): void {
                $days = (int) $family->payment_failed_at?->diffInDays(Carbon::now());
                $settings = $family->settings ?? [];

                // Day 3 — retry reminder.
                if ($days >= 3 && empty($settings['grace_day_3_sent_at']) && empty($settings['downgraded_at'])) {
                    if ($owner = $family->billingOwner()->first()) {
                        $owner->notify(new PaymentRetryNotification($family, max(0, 7 - $days)));
                    }
                    $settings['grace_day_3_sent_at'] = Carbon::now()->toIso8601String();
                    $family->forceFill(['settings' => $settings])->save();
                    $remindersSent++;
                }

                // Day 7 — downgrade.
                if ($days >= 7 && empty($settings['downgraded_at'])) {
                    $previousTier = $settings['chatbot']['plan'] ?? null;

                    // Try to drop the AI item from Stripe. If it fails, leave
                    // the local downgrade flag unset so we retry tomorrow —
                    // never assume we downgraded if Stripe didn't confirm.
                    try {
                        if ($previousTier !== null) {
                            $billing->selectAiTier($family, 'off');
                            $family->refresh();
                            $settings = $family->settings ?? [];
                            $settings['ai_plan_before_downgrade'] = $previousTier;
                        }
                    } catch (\Throwable $e) {
                        Log::error('Grace period downgrade — Stripe call failed', [
                            'family_id' => $family->id,
                            'error' => $e->getMessage(),
                        ]);

                        return; // skip the rest of this iteration; retry tomorrow
                    }

                    $settings['storage_soft_capped'] = true;
                    $settings['downgraded_at'] = Carbon::now()->toIso8601String();
                    $family->forceFill(['settings' => $settings])->save();

                    if ($owner = $family->billingOwner()->first()) {
                        $owner->notify(new SubscriptionDowngradedNotification($family));
                    }
                    $downgrades++;
                }
            });

        $this->info("Grace period sweep: {$remindersSent} day-3 reminders sent, {$downgrades} downgrades applied.");

        return self::SUCCESS;
    }
}
