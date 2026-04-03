<?php

namespace App\Mcp\Tools;

use App\Enums\RewardVisibility;
use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\Reward;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('manage-rewards')]
#[Description('List, create, update, or delete rewards in the family store. Actions: list, create, update, delete. Write actions are parent-only.')]
class ManageRewards extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum(['list', 'create', 'update', 'delete'])->description('Action to perform'),
            'reward_id' => $schema->string()->description('Reward UUID (required for update/delete)'),
            'title' => $schema->string()->description('Reward title (required for create)'),
            'description' => $schema->string()->description('Reward description'),
            'point_cost' => $schema->integer()->description('Points required to purchase (required for create)'),
            'icon' => $schema->string()->description('Emoji or icon for the reward'),
            'is_active' => $schema->boolean()->description('Whether the reward is available for purchase'),
            'quantity' => $schema->integer()->description('Stock limit (null = unlimited, 0+ = limited). Leave empty for unlimited purchases.'),
            'expires_at' => $schema->string()->description('Expiration date in ISO 8601 format (e.g. 2026-12-31T23:59:59). Null = never expires.'),
            'visibility' => $schema->string()->enum(['everyone', 'parent_only', 'child_only', 'specific'])->description('Who can see this reward'),
            'visible_to' => $schema->string()->description('Comma-separated user UUIDs when visibility is "specific"'),
            'min_age' => $schema->integer()->description('Minimum age to see this reward (null = no minimum)'),
            'max_age' => $schema->integer()->description('Maximum age to see this reward (null = no maximum)'),
            'reward_type' => $schema->string()->enum(['standard', 'auction'])->description('Reward type (default: standard)'),
            'min_bid' => $schema->integer()->description('Minimum bid amount for auctions'),
            'bid_start_at' => $schema->string()->description('Auction start time (ISO 8601). Null = open immediately.'),
            'bid_end_at' => $schema->string()->description('Auction end time (ISO 8601). Null = parent-called (manual close).'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'list' => $this->listRewards(),
            'create' => $this->createReward($request),
            'update' => $this->updateReward($request),
            'delete' => $this->deleteReward($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function listRewards(): Response
    {
        $rewards = Reward::where('family_id', $this->familyId())
            ->withCount('purchases')
            ->orderBy('sort_order')
            ->get();

        return Response::json([
            'rewards' => $rewards->map(function (Reward $r) {
                /** @var Carbon|null $expiresAt */
                $expiresAt = $r->expires_at;
                /** @var RewardVisibility $visibility */
                $visibility = $r->visibility;

                return [
                    'id' => $r->id,
                    'title' => $r->title,
                    'description' => $r->description,
                    'point_cost' => $r->point_cost,
                    'icon' => $r->icon,
                    'is_active' => $r->is_active,
                    'times_purchased' => $r->purchases_count,
                    'quantity' => $r->quantity,
                    'remaining_stock' => $r->remainingStock(),
                    'is_expired' => $r->isExpired(),
                    'is_purchasable' => $r->isPurchasable(),
                    'expires_at' => $expiresAt?->toIso8601String(),
                    'visibility' => $visibility->value,
                    'visible_to' => $r->visible_to,
                    'min_age' => $r->min_age,
                    'max_age' => $r->max_age,
                    'reward_type' => $r->reward_type->value,
                    'min_bid' => $r->min_bid,
                    'bid_start_at' => $r->bid_start_at?->toIso8601String(),
                    'bid_end_at' => $r->bid_end_at?->toIso8601String(),
                    'bidding_open' => $r->isAuction() ? $r->isBiddingOpen() : null,
                    'highest_bid' => $r->isAuction() ? $r->highestBid()?->bid_amount : null,
                    'total_bids' => $r->isAuction() ? $r->activeBids()->count() : null,
                ];
            })->toArray(),
        ]);
    }

    private function createReward(Request $request): Response
    {
        if ($denied = $this->authorize('create', Reward::class)) {
            return $denied;
        }

        $title = $request->get('title');
        if (! $title) {
            return Response::error('title is required to create a reward.');
        }

        $pointCost = $request->get('point_cost');
        if (! $pointCost || $pointCost < 1) {
            return Response::error('point_cost must be a positive integer.');
        }

        $visibleTo = $this->parseVisibleTo($request->get('visible_to'));

        $reward = Reward::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->user()->id,
            'title' => $title,
            'description' => $request->get('description'),
            'point_cost' => $pointCost,
            'icon' => $request->get('icon'),
            'is_active' => $request->get('is_active', true),
            'sort_order' => Reward::where('family_id', $this->familyId())->max('sort_order') + 1,
            'quantity' => $request->get('quantity'),
            'expires_at' => $request->get('expires_at'),
            'visibility' => $request->get('visibility', 'everyone'),
            'visible_to' => $visibleTo,
            'min_age' => $request->get('min_age'),
            'max_age' => $request->get('max_age'),
            'reward_type' => $request->get('reward_type', 'standard'),
            'min_bid' => $request->get('min_bid'),
            'bid_start_at' => $request->get('bid_start_at'),
            'bid_end_at' => $request->get('bid_end_at'),
        ]);

        /** @var Carbon|null $expiresAt */
        $expiresAt = $reward->expires_at;
        /** @var RewardVisibility $visibility */
        $visibility = $reward->visibility;

        return Response::json([
            'message' => "Reward \"{$reward->title}\" created ({$reward->point_cost} pts).",
            'reward' => [
                'id' => $reward->id,
                'title' => $reward->title,
                'point_cost' => $reward->point_cost,
                'quantity' => $reward->quantity,
                'expires_at' => $expiresAt?->toIso8601String(),
                'visibility' => $visibility->value,
            ],
        ]);
    }

    private function updateReward(Request $request): Response
    {
        $rewardId = $request->get('reward_id');
        if (! $rewardId) {
            return Response::error('reward_id is required for update.');
        }

        $reward = Reward::where('family_id', $this->familyId())->findOrFail($rewardId);

        if ($denied = $this->authorize('update', $reward)) {
            return $denied;
        }

        $updates = [];
        foreach (['title', 'description', 'point_cost', 'icon', 'is_active', 'quantity', 'expires_at', 'visibility', 'min_age', 'max_age', 'reward_type', 'min_bid', 'bid_start_at', 'bid_end_at'] as $field) {
            if ($request->get($field) !== null) {
                $updates[$field] = $request->get($field);
            }
        }

        // Handle visible_to separately (comma-separated string → array)
        if ($request->get('visible_to') !== null) {
            $updates['visible_to'] = $this->parseVisibleTo($request->get('visible_to'));
        }

        $reward->update($updates);

        /** @var Carbon|null $expiresAt */
        $expiresAt = $reward->expires_at;
        /** @var RewardVisibility $visibility */
        $visibility = $reward->visibility;

        return Response::json([
            'message' => "Reward \"{$reward->title}\" updated.",
            'reward' => [
                'id' => $reward->id,
                'title' => $reward->title,
                'point_cost' => $reward->point_cost,
                'is_active' => $reward->is_active,
                'quantity' => $reward->quantity,
                'remaining_stock' => $reward->remainingStock(),
                'expires_at' => $expiresAt?->toIso8601String(),
                'visibility' => $visibility->value,
            ],
        ]);
    }

    private function deleteReward(Request $request): Response
    {
        $rewardId = $request->get('reward_id');
        if (! $rewardId) {
            return Response::error('reward_id is required for delete.');
        }

        $reward = Reward::where('family_id', $this->familyId())->findOrFail($rewardId);

        if ($denied = $this->authorize('delete', $reward)) {
            return $denied;
        }
        $title = $reward->title;
        $reward->delete();

        return Response::text("Reward \"{$title}\" deleted.");
    }

    /**
     * Parse comma-separated UUIDs into an array, filtering to family members only.
     */
    private function parseVisibleTo(?string $value): ?array
    {
        if (! $value) {
            return null;
        }

        $ids = array_filter(array_map('trim', explode(',', $value)));

        if (empty($ids)) {
            return null;
        }

        // Only keep IDs that belong to this family
        return User::where('family_id', $this->familyId())
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();
    }
}
