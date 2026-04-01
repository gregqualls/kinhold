<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\Reward;
use App\Models\RewardPurchase;
use App\Services\BadgeService;
use App\Services\PointsService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('purchase-reward')]
#[Description('Purchase a reward with points or view purchase history. Actions: purchase, history.')]
class PurchaseReward extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum(['purchase', 'history'])->description('Action to perform'),
            'reward_id' => $schema->string()->description('Reward UUID (required for purchase)'),
            'user_id' => $schema->string()->description('Filter history by user UUID'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'purchase' => $this->purchase($request),
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
        ];

        if (! empty($newBadges)) {
            $response['badges_earned'] = collect($newBadges)->map(fn ($b) => $b->name)->toArray();
        }

        return Response::json($response);
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
