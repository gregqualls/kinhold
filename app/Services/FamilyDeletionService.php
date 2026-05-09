<?php

namespace App\Services;

use App\Models\Family;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FamilyDeletionService
{
    public function __construct(
        private AccountDeletionService $accountDeletionService,
    ) {}

    /**
     * Delete an entire family and all its members/data.
     */
    public function deleteFamily(Family $family): void
    {
        // Cancel any active Stripe subscription BEFORE the transaction (#269).
        // Stripe is an external API call — keeping it outside the DB
        // transaction means a Stripe hiccup can't dirty local state, and the
        // cancellation is best-effort anyway (logged on failure).
        $this->accountDeletionService->cancelStripeSubscription($family);

        DB::transaction(function () use ($family) {
            // Delete all members (handles file cleanup, token revocation, etc.)
            foreach ($family->members as $member) {
                $this->accountDeletionService->revokeCalendarConnections($member);
                $this->accountDeletionService->deletePhysicalFiles($member);
                $this->accountDeletionService->clearTokensAndSessions($member);
                $member->delete();
            }

            // Delete the family (cascade handles all family-scoped data:
            // tasks, vault entries, vault categories, chat messages,
            // point transactions, rewards, badges, family events, tags, etc.)
            $family->delete();
        });

        Log::info('Family deleted', ['family_id' => $family->id, 'family_name' => $family->name]);
    }

    /**
     * Check if a family can be deleted.
     *
     * Returns null if allowed, or an error message if blocked.
     */
    public function canDeleteFamily(Family $family): ?string
    {
        if ($family->slug === 'q32-demo-family') {
            return 'Family deletion is disabled for the demo family. Create your own family to try all features!';
        }

        return null;
    }
}
