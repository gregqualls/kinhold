<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('get-settings')]
#[Description('View current family settings including enabled modules, leaderboard period, default points, and AI configuration status. Read-only.')]
#[IsReadOnly]
class GetSettings extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [];
    }

    public function handle(Request $request): Response
    {
        $family = $this->family();
        $settings = $family->settings ?? [];

        return Response::json([
            'settings' => [
                'modules' => $family->getEnabledModules(),
                'leaderboard_period' => $family->getLeaderboardPeriod(),
                'default_points' => [
                    'low' => $family->getDefaultPoints('low'),
                    'medium' => $family->getDefaultPoints('medium'),
                    'high' => $family->getDefaultPoints('high'),
                ],
                'kudos_cost_enabled' => $settings['kudos_cost_enabled'] ?? false,
                'task_assignment' => $family->getTaskAssignment(),
                'ai_provider' => $settings['ai_provider'] ?? 'anthropic',
                'ai_has_key' => ! empty($settings['ai_api_key']),
            ],
        ]);
    }
}
