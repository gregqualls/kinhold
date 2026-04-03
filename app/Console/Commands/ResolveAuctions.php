<?php

namespace App\Console\Commands;

use App\Enums\RewardType;
use App\Models\Reward;
use App\Services\AuctionService;
use Illuminate\Console\Command;

class ResolveAuctions extends Command
{
    protected $signature = 'rewards:resolve-auctions';

    protected $description = 'Resolve timed auctions that have ended';

    public function handle(AuctionService $auctionService): int
    {
        $auctions = Reward::where('reward_type', RewardType::Auction->value)
            ->where('is_active', true)
            ->whereNotNull('bid_end_at')
            ->where('bid_end_at', '<=', now())
            ->whereDoesntHave('bids', fn ($q) => $q->where('is_winning', true))
            ->get();

        if ($auctions->isEmpty()) {
            $this->info('No auctions to resolve.');

            return self::SUCCESS;
        }

        foreach ($auctions as $auction) {
            try {
                $winner = $auctionService->resolveAuction($auction);
                if ($winner) {
                    $this->info("Resolved: {$auction->title} — won by user {$winner->user_id} for {$winner->bid_amount} pts");
                } else {
                    $this->info("Resolved: {$auction->title} — no bids");
                }
            } catch (\Exception $e) {
                $this->error("Failed to resolve {$auction->title}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
