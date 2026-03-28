<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\Reward;
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
            'rewards' => $rewards->map(fn ($r) => [
                'id' => $r->id,
                'title' => $r->title,
                'description' => $r->description,
                'point_cost' => $r->point_cost,
                'icon' => $r->icon,
                'is_active' => $r->is_active,
                'times_purchased' => $r->purchases_count,
            ])->toArray(),
        ]);
    }

    private function createReward(Request $request): Response
    {
        if ($denied = $this->requireParent()) {
            return $denied;
        }

        $title = $request->get('title');
        if (!$title) {
            return Response::error('title is required to create a reward.');
        }

        $pointCost = $request->get('point_cost');
        if (!$pointCost || $pointCost < 1) {
            return Response::error('point_cost must be a positive integer.');
        }

        $reward = Reward::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->user()->id,
            'title' => $title,
            'description' => $request->get('description'),
            'point_cost' => $pointCost,
            'icon' => $request->get('icon'),
            'is_active' => $request->get('is_active', true),
            'sort_order' => Reward::where('family_id', $this->familyId())->max('sort_order') + 1,
        ]);

        return Response::json([
            'message' => "Reward \"{$reward->title}\" created ({$reward->point_cost} pts).",
            'reward' => [
                'id' => $reward->id,
                'title' => $reward->title,
                'point_cost' => $reward->point_cost,
            ],
        ]);
    }

    private function updateReward(Request $request): Response
    {
        if ($denied = $this->requireParent()) {
            return $denied;
        }

        $rewardId = $request->get('reward_id');
        if (!$rewardId) {
            return Response::error('reward_id is required for update.');
        }

        $reward = Reward::where('family_id', $this->familyId())->findOrFail($rewardId);

        $updates = [];
        foreach (['title', 'description', 'point_cost', 'icon', 'is_active'] as $field) {
            if ($request->get($field) !== null) {
                $updates[$field] = $request->get($field);
            }
        }

        $reward->update($updates);

        return Response::json([
            'message' => "Reward \"{$reward->title}\" updated.",
            'reward' => [
                'id' => $reward->id,
                'title' => $reward->title,
                'point_cost' => $reward->point_cost,
                'is_active' => $reward->is_active,
            ],
        ]);
    }

    private function deleteReward(Request $request): Response
    {
        if ($denied = $this->requireParent()) {
            return $denied;
        }

        $rewardId = $request->get('reward_id');
        if (!$rewardId) {
            return Response::error('reward_id is required for delete.');
        }

        $reward = Reward::where('family_id', $this->familyId())->findOrFail($rewardId);
        $title = $reward->title;
        $reward->delete();

        return Response::text("Reward \"{$title}\" deleted.");
    }
}
