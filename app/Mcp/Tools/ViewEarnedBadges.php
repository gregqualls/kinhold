<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('view-earned-badges')]
#[Description('View earned badges for a family member. Defaults to the current user.')]
#[IsReadOnly]
class ViewEarnedBadges extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'user_id' => [
                    'type' => 'string',
                    'description' => 'User UUID (defaults to current user)',
                ],
            ],
        ];
    }

    public function handle(Request $request): Response
    {
        $userId = $request->get('user_id');
        if ($userId) {
            $user = $this->family()->members()->findOrFail($userId);
        } else {
            $user = $this->user();
        }

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
