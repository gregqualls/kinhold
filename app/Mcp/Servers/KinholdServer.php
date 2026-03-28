<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('kinhold')]
#[Version('1.0.0')]
#[Instructions('Kinhold is a family hub app. Use these tools to manage tasks, points, rewards, badges, calendar, vault, and more for the authenticated family. All data is scoped to the user\'s family. Some write actions are restricted to parent users.')]
class KinholdServer extends Server
{
    protected array $tools = [
        // Family & Settings (read-only)
        \App\Mcp\Tools\ViewFamily::class,
        \App\Mcp\Tools\GetSettings::class,
        \App\Mcp\Tools\SearchFamily::class,

        // Tasks
        \App\Mcp\Tools\ManageTaskLists::class,
        \App\Mcp\Tools\ManageTasks::class,
        \App\Mcp\Tools\CompleteTask::class,
        \App\Mcp\Tools\ManageTags::class,

        // Points & Rewards
        \App\Mcp\Tools\ViewPoints::class,
        \App\Mcp\Tools\ManagePoints::class,
        \App\Mcp\Tools\ManagePointRequests::class,
        \App\Mcp\Tools\ManageRewards::class,
        \App\Mcp\Tools\PurchaseReward::class,

        // Badges & Featured Events
        \App\Mcp\Tools\ManageBadges::class,
        \App\Mcp\Tools\ViewEarnedBadges::class,
        \App\Mcp\Tools\ManageFeaturedEvents::class,

        // Calendar & Vault
        \App\Mcp\Tools\ViewCalendar::class,
        \App\Mcp\Tools\ManageVault::class,
        \App\Mcp\Tools\ManageVaultAccess::class,
    ];
}
