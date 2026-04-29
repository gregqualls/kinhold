<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\MergesUpdates;
use App\Mcp\Tools\Concerns\RequiresModule;
use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\Badge;
use App\Services\BadgeService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('kinhold-achievements')]
#[Description(<<<'DESC'
Manage badges and view earned achievements.

Actions:
  badge_list — List all family badges (children see hidden badges masked unless earned).
  badge_create (name*, description*, trigger_type*, trigger_threshold?, icon?, color?, is_hidden?) — Parent only.
  badge_update (badge_id*, [any field]) — Parent only.
  badge_delete (badge_id*) — Parent only.
  badge_award (badge_id*, user_id*) — Manually award. Parent only.
  badge_revoke (badge_id*, user_id*) — Manually revoke. Parent only.
  badge_earned (user_id?) — List badges earned by a user (defaults to current user).

Trigger types: points_earned, tasks_completed, task_streak, kudos_received, kudos_given, rewards_purchased, login_streak, custom.
DESC)]
class KinholdAchievements extends Tool
{
    use MergesUpdates, RequiresModule, ScopesToFamily;

    public const MODULE = 'badges';

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum([
                'badge_list', 'badge_create', 'badge_update', 'badge_delete',
                'badge_award', 'badge_revoke', 'badge_earned',
            ])->description('Action to perform'),
            'badge_id' => $schema->string()->description('Badge UUID (required for update/delete/award/revoke)'),
            'user_id' => $schema->string()->description('User UUID (required for award/revoke; optional for badge_earned)'),
            'name' => $schema->string()->description('Badge name (required for create)'),
            'description' => $schema->string()->description('Badge description (required for create)'),
            'icon' => $schema->string()->description('Icon name from preset list'),
            'color' => $schema->string()->description('Hex color (e.g. #7d57a8)'),
            'trigger_type' => $schema->string()->enum([
                'points_earned', 'tasks_completed', 'task_streak', 'kudos_received',
                'kudos_given', 'rewards_purchased', 'login_streak', 'custom',
            ])->description('What triggers this badge (required for create)'),
            'trigger_threshold' => $schema->integer()->description('Threshold value for auto-trigger (null for custom badges)'),
            'is_hidden' => $schema->boolean()->description('Whether badge is hidden until earned'),
            'is_active' => $schema->boolean()->description('Whether badge is active'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'badge_list' => $this->badgeList(),
            'badge_create' => $this->badgeCreate($request),
            'badge_update' => $this->badgeUpdate($request),
            'badge_delete' => $this->badgeDelete($request),
            'badge_award' => $this->badgeAward($request),
            'badge_revoke' => $this->badgeRevoke($request),
            'badge_earned' => $this->badgeEarned($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function badgeList(): Response
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

                if (! $this->isParent()) {
                    $badgeData = Badge::maskHidden($badgeData, in_array($b->id, $earnedBadgeIds));
                }

                return $badgeData;
            })->toArray(),
        ]);
    }

    private function badgeCreate(Request $request): Response
    {
        if ($denied = $this->authorize('create', Badge::class)) {
            return $denied;
        }

        $name = $request->get('name');
        if (! $name) {
            return Response::error('name is required to create a badge.');
        }

        $description = $request->get('description');
        if (! $description) {
            return Response::error('description is required to create a badge.');
        }

        $triggerType = $request->get('trigger_type');
        if (! $triggerType) {
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

    private function badgeUpdate(Request $request): Response
    {
        $badgeId = $request->get('badge_id');
        if (! $badgeId) {
            return Response::error('badge_id is required for update.');
        }

        $badge = Badge::where('family_id', $this->familyId())->findOrFail($badgeId);

        if ($denied = $this->authorize('update', $badge)) {
            return $denied;
        }

        // mergeUpdates allows clearing nullable fields (e.g. trigger_threshold
        // can be cleared by passing null/"") — switching a custom badge to a
        // triggered badge or vice versa.
        $updates = $this->mergeUpdates(
            $request,
            simpleFields: [
                'name', 'description', 'icon', 'color', 'trigger_type',
                'trigger_threshold', 'is_hidden', 'is_active',
            ],
        );

        $badge->update($updates);

        return Response::json([
            'message' => "Badge \"{$badge->name}\" updated.",
            'badge' => ['id' => $badge->id, 'name' => $badge->name],
        ]);
    }

    private function badgeDelete(Request $request): Response
    {
        $badgeId = $request->get('badge_id');
        if (! $badgeId) {
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

    private function badgeAward(Request $request): Response
    {
        if ($denied = $this->authorize('award', Badge::class)) {
            return $denied;
        }

        $badgeId = $request->get('badge_id');
        $userId = $request->get('user_id');
        if (! $badgeId || ! $userId) {
            return Response::error('badge_id and user_id are required for award.');
        }

        $badge = Badge::where('family_id', $this->familyId())->findOrFail($badgeId);
        $target = $this->family()->members()->findOrFail($userId);

        $badgeService = app(BadgeService::class);
        $badgeService->manuallyAward($badge, $target, $this->user());

        return Response::text("Awarded \"{$badge->name}\" to {$target->name}.");
    }

    private function badgeRevoke(Request $request): Response
    {
        if ($denied = $this->authorize('revoke', Badge::class)) {
            return $denied;
        }

        $badgeId = $request->get('badge_id');
        $userId = $request->get('user_id');
        if (! $badgeId || ! $userId) {
            return Response::error('badge_id and user_id are required for revoke.');
        }

        $badge = Badge::where('family_id', $this->familyId())->findOrFail($badgeId);
        $target = $this->family()->members()->findOrFail($userId);

        $badgeService = app(BadgeService::class);
        $badgeService->revokeBadge($badge, $target);

        return Response::text("Revoked \"{$badge->name}\" from {$target->name}.");
    }

    private function badgeEarned(Request $request): Response
    {
        $userId = $request->get('user_id');
        $user = $userId
            ? $this->family()->members()->findOrFail($userId)
            : $this->user();

        $badges = $user->badges()
            ->orderByPivot('earned_at', 'desc')
            ->get();

        return Response::json([
            'user' => $user->name,
            'badges' => $badges->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'description' => $b->description,
                'icon' => $b->icon,
                'color' => $b->color,
                'earned_at' => $b->pivot->earned_at,
            ])->toArray(),
        ]);
    }
}
