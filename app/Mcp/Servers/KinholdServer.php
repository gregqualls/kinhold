<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\CompleteTask;
use App\Mcp\Tools\GetSettings;
use App\Mcp\Tools\ManageBadges;
use App\Mcp\Tools\ManageFeaturedEvents;
use App\Mcp\Tools\ManagePointRequests;
use App\Mcp\Tools\ManagePoints;
use App\Mcp\Tools\ManageRewards;
use App\Mcp\Tools\ManageTags;
use App\Mcp\Tools\ManageTasks;
use App\Mcp\Tools\ManageVault;
use App\Mcp\Tools\ManageVaultAccess;
use App\Mcp\Tools\PurchaseReward;
use App\Mcp\Tools\SearchFamily;
use App\Mcp\Tools\ViewCalendar;
use App\Mcp\Tools\ViewEarnedBadges;
use App\Mcp\Tools\ViewFamily;
use App\Mcp\Tools\ViewPoints;
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
        ViewFamily::class,
        GetSettings::class,
        SearchFamily::class,

        // Tasks
        ManageTasks::class,
        CompleteTask::class,
        ManageTags::class,

        // Points & Rewards
        ViewPoints::class,
        ManagePoints::class,
        ManagePointRequests::class,
        ManageRewards::class,
        PurchaseReward::class,

        // Badges & Featured Events
        ManageBadges::class,
        ViewEarnedBadges::class,
        ManageFeaturedEvents::class,

        // Calendar & Vault
        ViewCalendar::class,
        ManageVault::class,
        ManageVaultAccess::class,
    ];
}
