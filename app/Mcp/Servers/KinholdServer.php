<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\KinholdAchievements;
use App\Mcp\Tools\KinholdCalendar;
use App\Mcp\Tools\KinholdFamily;
use App\Mcp\Tools\KinholdFood;
use App\Mcp\Tools\KinholdPoints;
use App\Mcp\Tools\KinholdTasks;
use App\Mcp\Tools\KinholdVault;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('kinhold')]
#[Version('1.0.0')]
#[Instructions('Kinhold is a family hub app. Each tool consolidates one domain: kinhold-calendar, kinhold-tasks, kinhold-food, kinhold-points, kinhold-vault, kinhold-achievements, kinhold-family. Every tool takes an `action` parameter — read the tool description for the full action enum and which params each action requires. All data is family-scoped; some write actions are parent-only. Domain-specific tools (calendar/tasks/food/points/vault/achievements) only register when the family has the corresponding module enabled.')]
class KinholdServer extends Server
{
    public int $defaultPaginationLength = 50;

    protected array $tools = [
        // Always-on (family core)
        KinholdFamily::class,

        // Module-gated (auto-skip when module is disabled for the family)
        KinholdCalendar::class,
        KinholdTasks::class,
        KinholdFood::class,
        KinholdPoints::class,
        KinholdVault::class,
        KinholdAchievements::class,
    ];
}
