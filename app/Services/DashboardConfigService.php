<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class DashboardConfigService
{
    /**
     * Valid widget types.
     */
    public const WIDGET_TYPES = [
        'welcome',
        'countdown',
        'stat',
        'list',
        'leaderboard',
        'feed',
        'quick-actions',
        'calendar-mini',
        'progress',
        'badges',
        'rewards',
    ];

    /**
     * Available API endpoints that widgets can bind to.
     */
    public const AVAILABLE_ENDPOINTS = [
        '/api/v1/tasks',
        '/api/v1/calendar/events',
        '/api/v1/points/bank',
        '/api/v1/points/leaderboard',
        '/api/v1/points/feed',
        '/api/v1/rewards',
        '/api/v1/rewards/purchases',
        '/api/v1/badges',
        '/api/v1/badges/earned',
        '/api/v1/featured-events',
        '/api/v1/featured-events/countdown',
        '/api/v1/vault/categories',
        '/api/v1/vault/entries',
        '/api/v1/family',
    ];

    /**
     * Generate a default dashboard config based on the user's enabled modules.
     */
    public static function defaultFor(User $user): array
    {
        $family = $user->family;
        $enabledModules = $family?->settings['modules'] ?? [
            'tasks' => true,
            'vault' => true,
            'calendar' => true,
            'chat' => true,
            'points' => true,
            'badges' => true,
        ];

        $widgets = [];

        // Welcome widget — always present
        $widgets[] = self::widget('welcome', 'Welcome', null, [], 'lg');

        // Countdown — always available (shows nothing if no countdown event)
        $widgets[] = self::widget('countdown', 'Countdown', '/api/v1/featured-events/countdown', [], 'lg');

        // Calendar — today's events
        if ($enabledModules['calendar'] ?? true) {
            $widgets[] = self::widget('calendar-mini', "Today's Events", '/api/v1/calendar/events', [
                'range' => 'today',
            ], 'md', ['limit' => 5, 'viewAllPath' => '/calendar']);
        }

        // Tasks — my tasks
        if ($enabledModules['tasks'] ?? true) {
            $widgets[] = self::widget('list', 'My Tasks', '/api/v1/tasks', [
                'assigned_to' => 'me',
                'status' => 'pending',
            ], 'sm', [
                'limit' => 5,
                'showDueDate' => true,
                'showPoints' => true,
                'completable' => true,
                'viewAllPath' => '/tasks',
                'emptyMessage' => 'No tasks assigned to you.',
            ]);

            // Open family tasks
            $widgets[] = self::widget('list', 'Open Tasks', '/api/v1/tasks', [
                'is_family_task' => true,
                'status' => 'pending',
            ], 'md', [
                'limit' => 10,
                'showDueDate' => true,
                'showPoints' => true,
                'completable' => true,
                'viewAllPath' => '/tasks',
                'emptyMessage' => 'No open family tasks.',
            ]);
        }

        // Points
        if ($enabledModules['points'] ?? true) {
            $widgets[] = self::widget('stat', 'Points Balance', '/api/v1/points/bank', [], 'sm', [
                'valueKey' => 'bank',
                'icon' => 'trophy',
                'suffix' => 'pts',
            ]);

            $widgets[] = self::widget('leaderboard', 'Leaderboard', '/api/v1/points/leaderboard', [], 'sm', [
                'limit' => 5,
            ]);

            $widgets[] = self::widget('rewards', 'Rewards Shop', '/api/v1/rewards', [], 'sm');
        }

        // Badges
        if ($enabledModules['badges'] ?? true) {
            $widgets[] = self::widget('badges', 'Badges', '/api/v1/badges', [], 'sm');
        }

        // Quick actions — always present
        $widgets[] = self::widget('quick-actions', 'Quick Actions', null, [], 'sm', [
            'actions' => self::defaultQuickActions($enabledModules),
        ]);

        return [
            'version' => 1,
            'widgets' => $widgets,
        ];
    }

    /**
     * Build a single widget config entry.
     */
    private static function widget(
        string $type,
        string $title,
        ?string $endpoint,
        array $params = [],
        string $size = 'sm',
        array $settings = [],
    ): array {
        return [
            'id' => (string) Str::uuid(),
            'type' => $type,
            'title' => $title,
            'endpoint' => $endpoint,
            'params' => $params,
            'size' => $size,
            'settings' => $settings,
        ];
    }

    /**
     * Default quick action buttons based on enabled modules.
     */
    private static function defaultQuickActions(array $modules): array
    {
        $actions = [];

        if ($modules['tasks'] ?? true) {
            $actions[] = ['label' => 'Add Task', 'icon' => 'plus-circle', 'path' => '/tasks'];
        }
        if ($modules['vault'] ?? true) {
            $actions[] = ['label' => 'Vault', 'icon' => 'lock-closed', 'path' => '/vault'];
        }
        if ($modules['chat'] ?? true) {
            $actions[] = ['label' => 'Assistant', 'icon' => 'cpu-chip', 'path' => '/chat'];
        }
        if ($modules['calendar'] ?? true) {
            $actions[] = ['label' => 'Calendar', 'icon' => 'calendar', 'path' => '/calendar'];
        }

        return $actions;
    }

    /**
     * Validate a dashboard config structure. Returns array of errors or empty array.
     */
    public static function validate(array $config): array
    {
        $errors = [];

        if (! isset($config['version']) || $config['version'] !== 1) {
            $errors[] = 'Config version must be 1.';
        }

        if (! isset($config['widgets']) || ! is_array($config['widgets'])) {
            $errors[] = 'Config must contain a widgets array.';

            return $errors;
        }

        if (count($config['widgets']) > 20) {
            $errors[] = 'Maximum 20 widgets allowed.';
        }

        foreach ($config['widgets'] as $i => $widget) {
            if (! isset($widget['id']) || ! is_string($widget['id'])) {
                $errors[] = "Widget {$i}: missing or invalid id.";
            }
            if (! isset($widget['type']) || ! in_array($widget['type'], self::WIDGET_TYPES, true)) {
                $type = $widget['type'] ?? 'null';
                $errors[] = "Widget {$i}: invalid type '{$type}'.";
            }
            if (! isset($widget['title']) || ! is_string($widget['title'])) {
                $errors[] = "Widget {$i}: missing title.";
            }
            if (! in_array($widget['size'] ?? '', ['sm', 'md', 'lg'], true)) {
                $errors[] = "Widget {$i}: invalid size. Must be sm, md, or lg.";
            }
            if (isset($widget['endpoint']) && $widget['endpoint'] !== null) {
                if (! str_starts_with($widget['endpoint'], '/api/v1/')) {
                    $errors[] = "Widget {$i}: endpoint must start with /api/v1/.";
                }
            }
        }

        return $errors;
    }
}
