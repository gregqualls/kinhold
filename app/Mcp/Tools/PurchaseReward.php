<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\Reward;
use App\Models\RewardPurchase;
use App\Services\AuctionService;
use App\Services\BadgeService;
use App\Services\PointsService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('purchase-reward')]
#[Description('Purchase a reward with points, place a bid on an auction, or view purchase history. Actions: purchase, bid, history.')]
class PurchaseReward extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum(['purchase', 'bid', 'history'])->description('Action to perform'),
            'reward_id' => $schema->string()->description('Reward UUID (required for purchase and bid)'),
            'bid_amount' => $schema->integer()->description('Bid amount in points (required for bid action)'),
            'user_id' => $schema->string()->description('Filter history by user UUID'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'purchase' => $this->purchase($request),
            'bid' => $this->bid($request),
            'history' => $this->history($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function purchase(Request $request): Response
    {
        $rewardId = $request->get('reward_id');
        if (! $rewardId) {
            return Response::error('reward_id is required to purchase.');
        }

        $reward = Reward::where('family_id', $this->familyId())
            ->where('is_active', true)
            ->findOrFail($rewardId);

        $user = $this->user();
        $pointsService = app(PointsService::class);

        try {
            $result = $pointsService->redeemReward($reward, $user);
        } catch (\Exception $e) {
            return Response::error($e->getMessage());
        }

        $badgeService = app(BadgeService::class);
        $newBadges = $badgeService->checkAndAwardBadges($user);

        $response = [
            'message' => "Purchased \"{$reward->title}\" for {$reward->point_cost} points!",
            'remaining_balance' => $user->pointBank(),
            'remaining_stock' => $result['reward']->remainingStock(),
        ];

        if (! empty($newBadges)) {
            $response['badges_earned'] = collect($newBadges)->map(fn ($b) => $b->name)->toArray();
        }

        return Response::json($response);
    }

    private function bid(Request $request): Response
    {
        $rewardId = $request->get('reward_id');
        if (! $rewardId) {
            return Response::error('reward_id is required to bid.');
        }

        $bidAmount = $request->get('bid_amount');
        if (! $bidAmount || $bidAmount < 1) {
            return Response::error('bid_amount must be a positive integer.');
        }

        $reward = Reward::where('family_id', $this->familyId())->findOrFail($rewardId);
        $user = $this->user();
        $auctionService = app(AuctionService::class);

        try {
            $bid = $auctionService->placeBid($reward, $user, (int) $bidAmount);
        } catch (\Exception $e) {
            return Response::error($e->getMessage());
        }

        return Response::json([
            'message' => "Bid of {$bid->bid_amount} points placed on \"{$reward->title}\".",
            'bid_amount' => $bid->bid_amount,
            'held_points' => $bid->held_points,
            'available_points' => $user->availablePoints(),
        ]);
    }

    private function history(Request $request): Response
    {
        $query = RewardPurchase::where('family_id', $this->familyId())
            ->with(['reward:id,title,point_cost,icon', 'user:id,name']);

        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }

        $purchases = $query->orderByDesc('purchased_at')->limit(50)->get();

        return Response::json([
            'purchases' => $purchases->map(fn ($p) => [
                'id' => $p->id,
                'reward' => $p->reward?->title,
                'icon' => $p->reward?->icon,
                'user' => $p->user?->name,
                'points_spent' => $p->points_spent,
                'purchased_at' => $p->purchased_at->toIso8601String(),
            ])->toArray(),
        ]);
    }
}
