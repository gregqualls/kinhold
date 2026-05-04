<?php

namespace App\Services;

use App\Models\Family;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

class AccountDeletionService
{
    /**
     * Delete a user account and all associated data.
     *
     * Handles pre-delete cleanup (files, tokens, sessions) before
     * letting DB cascades handle the rest. If the user is a parent,
     * their managed children are recursively deleted first.
     */
    public function deleteUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            // 1. Recursively delete managed children first
            foreach ($user->managedChildren as $child) {
                $this->deleteUser($child);
            }

            // 2. Pre-delete cleanup (before cascade removes records)
            $this->revokeCalendarConnections($user);
            $this->deletePhysicalFiles($user);
            $this->clearTokensAndSessions($user);

            // 3. Delete the user (DB cascades handle related data)
            $user->delete();

            // 4. Clean up orphaned family
            $this->cleanupOrphanedFamily($user->family_id);
        });

        Log::info('Account deleted', ['user_id' => $user->id, 'email' => $user->email]);
    }

    /**
     * Check if a user can delete their own account.
     *
     * Returns null if allowed, or an error message if blocked.
     */
    public function canDeleteSelf(User $user): ?string
    {
        if ($this->isDemoFamily($user)) {
            return 'Account deletion is disabled for the demo family. Create your own family to try all features!';
        }

        // Last parent guard: if you're the last parent and there are non-managed members
        if ($user->isParent() && $user->family_id) {
            $family = $user->family;
            $otherParents = $family->members()
                ->where('id', '!=', $user->id)
                ->where('family_role', 'parent')
                ->count();

            if ($otherParents === 0) {
                $nonManagedMembers = $family->members()
                    ->where('id', '!=', $user->id)
                    ->where('is_managed', false)
                    ->count();

                if ($nonManagedMembers > 0) {
                    return 'You are the last parent in this family. Transfer the parent role to another member first, or delete the entire family.';
                }
            }

            // Billing-owner force-transfer (#269). If this user owns the
            // family's Stripe subscription and the family will *survive*
            // their deletion (other parents remain), require them to hand
            // off billing first so we don't silently abandon a paid sub.
            if ((string) $family->billing_owner_id === (string) $user->id
                && $otherParents > 0
                && $family->subscription('default')?->valid()) {
                return 'You are the billing owner for this family. Transfer billing ownership to another parent before deleting your account, or delete the entire family.';
            }
        }

        return null;
    }

    /**
     * Check if the user belongs to the demo family.
     */
    public function isDemoFamily(User $user): bool
    {
        return $user->family && $user->family->slug === 'q32-demo-family';
    }

    public function revokeCalendarConnections(User $user): void
    {
        foreach ($user->calendarConnections as $connection) {
            $connection->revoke();
        }
    }

    public function deletePhysicalFiles(User $user): void
    {
        foreach ($user->documents as $document) {
            $document->deleteFile();
        }

        // Also delete uploaded avatar if it's a file path
        if ($user->avatar && str_starts_with($user->avatar, 'avatars/')) {
            \Storage::disk('local')->delete($user->avatar);
        }
    }

    public function clearTokensAndSessions(User $user): void
    {
        $user->tokens()->delete();
        $user->pushSubscriptions()->delete();
        DB::table('sessions')->where('user_id', $user->id)->delete();
    }

    private function cleanupOrphanedFamily(?string $familyId): void
    {
        if (! $familyId) {
            return;
        }

        $remainingMembers = User::where('family_id', $familyId)->count();

        if ($remainingMembers === 0) {
            $family = Family::where('id', $familyId)->first();
            if ($family) {
                // Cancel any active Stripe subscription before the family
                // record is destroyed (#269). Otherwise the customer keeps
                // being billed for a family that no longer exists.
                $this->cancelStripeSubscription($family);
                // Eloquent delete fires model events and cascades properly
                $family->delete();
            }
        }
    }

    /**
     * Best-effort cancellation of the family's Stripe subscription before its
     * record is deleted. Called from both account-deletion (orphan cleanup)
     * and family-deletion paths so users who exercise their GDPR right to
     * erasure don't keep accruing Stripe charges.
     *
     * Failures are logged but do NOT block deletion — a Stripe outage must
     * not prevent a user from deleting their account, or we'd be holding
     * GDPR-protected data hostage to a third-party API. Operators can
     * reconcile orphaned Stripe customers manually via the Dashboard.
     */
    public function cancelStripeSubscription(Family $family): void
    {
        if (! $family->stripe_id) {
            return;
        }

        $subscription = $family->subscription('default');
        if (! $subscription) {
            return;
        }

        try {
            $subscription->cancelNow();
            Log::info('Stripe subscription cancelled before family deletion', [
                'family_id' => $family->id,
                'stripe_id' => $family->stripe_id,
                'subscription_id' => $subscription->stripe_id,
            ]);
        } catch (ApiErrorException $e) {
            Log::warning('Failed to cancel Stripe subscription before family deletion', [
                'family_id' => $family->id,
                'stripe_id' => $family->stripe_id,
                'error' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Unexpected error cancelling Stripe subscription before family deletion', [
                'family_id' => $family->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
