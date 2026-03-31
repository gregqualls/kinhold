<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\Badge;
use App\Services\BadgeService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('manage-badges')]
#[Description('List, create, update, delete, or manually award/revoke badges. Actions: list, create, update, delete, award, revoke. Write actions are parent-only.')]
class ManageBadges extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum(['list', 'create', 'update', 'delete', 'award', 'revoke'])->description('Action to perform'),
            'badge_id' => $schema->string()->description('Badge UUID (required for update/delete/award/revoke)'),
            'user_id' => $schema->string()->description('Target user UUID (required for award/revoke)'),
            'name' => $schema->string()->description('Badge name (required for create)'),
            'description' => $schema->string()->description('Badge description (required for create)'),
            'icon' => $schema->string()->description('Icon name from preset list'),
            'color' => $schema->string()->description('Hex color (e.g. #7d57a8)'),
            'trigger_type' => $schema->string()->enum(['points_earned', 'tasks_completed', 'task_streak', 'kudos_received', 'kudos_given', 'rewards_purchased', 'login_streak', 'custom'])->description('What triggers this badge (required for create)'),
            'trigger_threshold' => $schema->integer()->description('Threshold value for auto-trigger (null for custom badges)'),
            'is_hidden' => $schema->boolean()->description('Whether badge is hidden until earned'),
            'is_active' => $schema->boolean()->description('Whether badge is active'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'list' => $this->listBadges(),
            'create' => $this->createBadge($request),
            'update' => $this->updateBadge($request),
            'delete' => $this->deleteBadge($request),
            'award' => $this->awardBadge($request),
            'revoke' => $this->revokeBadge($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function listBadges(): Response
    {
        $user = $this->user();
        $earnedBadgeIds = $user->badges()->pluck('badges.id')->toArray();

        $badges = Badge::where('family_id', $this->familyId())
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return Response::json([
            'badges' => $badges->map(function ($b) use ($earnedBadgeIds) {
                $badgeData = [
                    'id' => $b->id,
                    'name' => $b->name,
                    'description' => $b->description,
                    'icon' => $b->icon,
                    'color' => $b->color,
                    'trigger_type' => $b->trigger_type->value,
                    'trigger_threshold' => $b->trigger_threshold,
                    'is_hidden' => $b->is_hidden,
                ];

                // Parents see full details for management; children get masked hidden badges
                if (!$this->isParent()) {
                    $badgeData = Badge::maskHidden($badgeData, in_array($b->id, $earnedBadgeIds));
                }

                return $badgeData;
            })->toArray(),
        ]);
    }

    private function createBadge(Request $request): Response
    {
        if ($denied = $this->authorize('create', Badge::class)) {
            return $denied;
        }

        $name = $request->get('name');
        if (!$name) {
            return Response::error('name is required to create a badge.');
        }

        $description = $request->get('description');
        if (!$description) {
            return Response::error('description is required to create a badge.');
        }

        $triggerType = $request->get('trigger_type');
        if (!$triggerType) {
            return Response::error('trigger_type is required to create a badge.');
        }

        $badge = Badge::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->user()->id,
            'name' => $name,
            'description' => $description,
            'icon' => $request->get('icon'),
            'color' => $request->get('color', '#7d57a8'),
            'trigger_type' => $triggerType,
            'trigger_threshold' => $request->get('trigger_threshold'),
            'is_hidden' => $request->get('is_hidden', false),
        ]);

        return Response::json([
            'message' => "Badge \"{$badge->name}\" created.",
            'badge' => ['id' => $badge->id, 'name' => $badge->name],
        ]);
    }

    private function updateBadge(Request $request): Response
    {
        $badgeId = $request->get('badge_id');
        if (!$badgeId) {
            return Response::error('badge_id is required for update.');
        }

        $badge = Badge::where('family_id', $this->familyId())->findOrFail($badgeId);

        if ($denied = $this->authorize('update', $badge)) {
            return $denied;
        }

        $updates = [];
        foreach (['name', 'description', 'icon', 'color', 'trigger_type', 'trigger_threshold', 'is_hidden', 'is_active'] as $field) {
            if ($request->get($field) !== null) {
                $updates[$field] = $request->get($field);
            }
        }

        $badge->update($updates);

        return Response::json([
            'message' => "Badge \"{$badge->name}\" updated.",
            'badge' => ['id' => $badge->id, 'name' => $badge->name],
        ]);
    }

    private function deleteBadge(Request $request): Response
    {
        $badgeId = $request->get('badge_id');
        if (!$badgeId) {
            return Response::error('badge_id is required for delete.');
        }

        $badge = Badge::where('family_id', $this->familyId())->findOrFail($badgeId);

        if ($denied = $this->authorize('delete', $badge)) {
            return $denied;
        }
        $name = $badge->name;
        $badge->delete();

        return Response::text("Badge \"{$name}\" deleted.");
    }

    private function awardBadge(Request $request): Response
    {
        if ($denied = $this->authorize('award', Badge::class)) {
            return $denied;
        }

        $badgeId = $request->get('badge_id');
        $userId = $request->get('user_id');
        if (!$badgeId || !$userId) {
            return Response::error('badge_id and user_id are required for award.');
        }

        $badge = Badge::where('family_id', $this->familyId())->findOrFail($badgeId);
        $target = $this->family()->members()->findOrFail($userId);

        $badgeService = app(BadgeService::class);
        $badgeService->manuallyAward($badge, $target, $this->user());

        return Response::text("Awarded \"{$badge->name}\" to {$target->name}.");
    }

    private function revokeBadge(Request $request): Response
    {
        if ($denied = $this->authorize('revoke', Badge::class)) {
            return $denied;
        }

        $badgeId = $request->get('badge_id');
        $userId = $request->get('user_id');
        if (!$badgeId || !$userId) {
            return Response::error('badge_id and user_id are required for revoke.');
        }

        $badge = Badge::where('family_id', $this->familyId())->findOrFail($badgeId);
        $target = $this->family()->members()->findOrFail($userId);

        $badgeService = app(BadgeService::class);
        $badgeService->revokeBadge($badge, $target);

        return Response::text("Revoked \"{$badge->name}\" from {$target->name}.");
    }
}
