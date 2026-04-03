<?php

namespace App\Services;

use App\Enums\PointTransactionType;
use App\Models\PointTransaction;
use App\Models\Reward;
use App\Models\RewardBid;
use App\Models\RewardPurchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuctionService
{
    /**
     * Place or update a bid on an auction reward.
     */
    public function placeBid(Reward $reward, User $user, int $amount): RewardBid
    {
        return DB::transaction(function () use ($reward, $user, $amount) {
            $reward = Reward::lockForUpdate()->findOrFail($reward->id);

            if (! $reward->isAuction()) {
                throw new \Exception('This reward is not an auction.');
            }

            if (! $reward->isBiddingOpen()) {
                throw new \Exception('Bidding is not open for this reward.');
            }

            if ($reward->min_bid !== null && $amount < $reward->min_bid) {
                throw new \Exception("Minimum bid is {$reward->min_bid} points.");
            }

            // Check if bid exceeds current highest
            $highestBid = $reward->highestBid();
            if ($highestBid && $amount <= $highestBid->bid_amount) {
                throw new \Exception("Bid must exceed the current highest bid of {$highestBid->bid_amount} points.");
            }

            // Calculate available points (considering existing hold if updating)
            $existingBid = RewardBid::where('reward_id', $reward->id)
                ->where('user_id', $user->id)
                ->whereNull('resolved_at')
                ->lockForUpdate()
                ->first();

            $currentHold = $existingBid ? $existingBid->held_points : 0;
            $availableWithRelease = $user->availablePoints() + $currentHold;

            if ($amount > $availableWithRelease) {
                throw new \Exception('Insufficient available points. You have '.$availableWithRelease.' available.');
            }

            // Create or update the bid
            if ($existingBid) {
                $existingBid->update([
                    'bid_amount' => $amount,
                    'held_points' => $amount,
                ]);

                return $existingBid;
            }

            return RewardBid::create([
                'family_id' => $user->family_id,
                'reward_id' => $reward->id,
                'user_id' => $user->id,
                'bid_amount' => $amount,
                'held_points' => $amount,
            ]);
        });
    }

    /**
     * Resolve an auction — award to highest bidder, release all other holds.
     */
    public function resolveAuction(Reward $reward): ?RewardBid
    {
        return DB::transaction(function () use ($reward) {
            $reward = Reward::lockForUpdate()->findOrFail($reward->id);

            if (! $reward->isAuction()) {
                throw new \Exception('This reward is not an auction.');
            }

            if ($reward->isResolved()) {
                throw new \Exception('This auction has already been resolved.');
            }

            $activeBids = RewardBid::where('reward_id', $reward->id)
                ->whereNull('resolved_at')
                ->lockForUpdate()
                ->orderByDesc('bid_amount')
                ->orderBy('created_at')
                ->get();

            if ($activeBids->isEmpty()) {
                return null;
            }

            $winner = $activeBids->first();

            // Deduct winner's points (convert hold to real transaction)
            PointTransaction::create([
                'family_id' => $winner->family_id,
                'user_id' => $winner->user_id,
                'type' => PointTransactionType::Redemption,
                'points' => -$winner->bid_amount,
                'description' => "Won auction: {$reward->title}",
                'source_type' => Reward::class,
                'source_id' => $reward->id,
            ]);

            // Create purchase record
            RewardPurchase::create([
                'family_id' => $winner->family_id,
                'reward_id' => $reward->id,
                'user_id' => $winner->user_id,
                'points_spent' => $winner->bid_amount,
                'purchased_at' => now(),
            ]);

            // Increment stock counter if applicable
            if ($reward->quantity !== null) {
                $reward->increment('quantity_purchased');
            }

            // Mark winner
            $winner->update([
                'is_winning' => true,
                'held_points' => 0,
                'resolved_at' => now(),
            ]);

            // Release all other holds
            $activeBids->slice(1)->each(function (RewardBid $bid) {
                $bid->update([
                    'held_points' => 0,
                    'resolved_at' => now(),
                ]);
            });

            return $winner;
        });
    }

    /**
     * Close a parent-called auction and resolve it.
     */
    public function closeAuction(Reward $reward): ?RewardBid
    {
        if ($reward->bid_end_at !== null) {
            throw new \Exception('This is a timed auction — it will close automatically.');
        }

        return $this->resolveAuction($reward);
    }

    /**
     * Cancel an auction — release all holds, no winner.
     */
    public function cancelAuction(Reward $reward): void
    {
        DB::transaction(function () use ($reward) {
            $reward = Reward::lockForUpdate()->findOrFail($reward->id);

            if (! $reward->isAuction()) {
                throw new \Exception('This reward is not an auction.');
            }

            // Release all active holds
            RewardBid::where('reward_id', $reward->id)
                ->whereNull('resolved_at')
                ->update([
                    'held_points' => 0,
                    'resolved_at' => now(),
                ]);

            // Deactivate the reward
            $reward->update(['is_active' => false]);
        });
    }
}
