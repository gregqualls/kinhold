<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\PointTransaction;
use App\Services\PointsService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('view-points')]
#[Description('View point balances, leaderboard, and activity feed. Actions: bank, leaderboard, feed.')]
#[IsReadOnly]
class ViewPoints extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum(['bank', 'leaderboard', 'feed'])->description('Action to perform'),
            'user_id' => $schema->string()->description('User UUID (for bank action, defaults to current user)'),
            'period' => $schema->string()->enum(['daily', 'weekly', 'monthly'])->description('Leaderboard period (defaults to family setting)'),
            'limit' => $schema->integer()->description('Number of feed items to return (default 20)'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'bank' => $this->bank($request),
            'leaderboard' => $this->leaderboard($request),
            'feed' => $this->feed($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function bank(Request $request): Response
    {
        $userId = $request->get('user_id');
        if ($userId) {
            $user = $this->family()->members()->findOrFail($userId);
        } else {
            $user = $this->user();
        }

        return Response::json([
            'user' => $user->name,
            'balance' => $user->pointBank(),
        ]);
    }

    private function leaderboard(Request $request): Response
    {
        $pointsService = app(PointsService::class);
        $period = $request->get('period');
        $leaderboard = $pointsService->getLeaderboard($this->family(), $period);

        return Response::json([
            'period' => $period ?? $this->family()->getLeaderboardPeriod(),
            'leaderboard' => $leaderboard->toArray(),
        ]);
    }

    private function feed(Request $request): Response
    {
        $limit = $request->get('limit', 20);

        $transactions = PointTransaction::where('family_id', $this->familyId())
            ->with(['user:id,name', 'awardedBy:id,name'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return Response::json([
            'feed' => $transactions->map(fn ($t) => [
                'id' => $t->id,
                'user' => $t->user?->name,
                'type' => $t->type->value,
                'points' => $t->points,
                'description' => $t->description,
                'awarded_by' => $t->awardedBy?->name,
                'created_at' => $t->created_at->toIso8601String(),
            ])->toArray(),
        ]);
    }
}
