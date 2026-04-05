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
        'my-tasks',
        'family-tasks',
        'todays-schedule',
        'points-summary',
        'leaderboard',
        'activity-feed',
        'rewards-shop',
        'badge-collection',
        'filtered-tasks',
        'quick-actions',
    ];

    /**
     * Supported sizes per widget type.
     */
    public const WIDGET_SIZES = [
        'welcome' => ['lg'],
        'countdown' => ['lg'],
        'my-tasks' => ['sm', 'md', 'lg'],
        'family-tasks' => ['sm', 'md'],
        'todays-schedule' => ['sm', 'md'],
        'points-summary' => ['sm'],
        'leaderboard' => ['sm', 'md'],
        'activity-feed' => ['sm', 'md'],
        'rewards-shop' => ['sm', 'md'],
        'badge-collection' => ['sm', 'md'],
        'filtered-tasks' => ['sm', 'md', 'lg'],
        'quick-actions' => ['sm'],
    ];

    /**
     * Current config version.
     */
    public const CONFIG_VERSION = 2;

    /**
     * Generate a default dashboard config based on enabled modules.
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

        $widgets[] = self::widget('welcome', 'lg');
        $widgets[] = self::widget('countdown', 'lg');

        if ($enabledModules['calendar'] ?? true) {
            $widgets[] = self::widget('todays-schedule', 'md');
        }

        if ($enabledModules['tasks'] ?? true) {
            $widgets[] = self::widget('my-tasks', 'sm');
            $widgets[] = self::widget('family-tasks', 'md');
        }

        if ($enabledModules['points'] ?? true) {
            $widgets[] = self::widget('points-summary', 'sm');
            $widgets[] = self::widget('leaderboard', 'sm');
            $widgets[] = self::widget('rewards-shop', 'sm');
        }

        if ($enabledModules['badges'] ?? true) {
            $widgets[] = self::widget('badge-collection', 'sm');
        }

        $widgets[] = self::widget('quick-actions', 'sm');

        return [
            'version' => self::CONFIG_VERSION,
            'widgets' => $widgets,
        ];
    }

    /**
     * Build a widget config entry.
     */
    private static function widget(string $type, string $size): array
    {
        return [
            'id' => (string) Str::uuid(),
            'type' => $type,
            'size' => $size,
        ];
    }

    /**
     * Validate a v2 dashboard config.
     */
    public static function validate(array $config): array
    {
        $errors = [];

        if (! isset($config['version']) || $config['version'] !== self::CONFIG_VERSION) {
            $errors[] = 'Config version must be '.self::CONFIG_VERSION.'.';
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
                $errors[] = "Widget {$i}: missing id.";
            }
            if (! isset($widget['type']) || ! in_array($widget['type'], self::WIDGET_TYPES, true)) {
                $type = $widget['type'] ?? 'null';
                $errors[] = "Widget {$i}: invalid type '{$type}'.";
            }
            $type = $widget['type'] ?? null;
            $size = $widget['size'] ?? null;
            if ($type && $size) {
                $supported = self::WIDGET_SIZES[$type] ?? ['sm'];
                if (! in_array($size, $supported, true)) {
                    $errors[] = "Widget {$i}: size '{$size}' not supported for type '{$type}'. Valid: ".implode(', ', $supported).'.';
                }
            } elseif (! $size) {
                $errors[] = "Widget {$i}: missing size.";
            }

            // Validate optional title
            if (isset($widget['title']) && (! is_string($widget['title']) || strlen($widget['title']) > 255)) {
                $errors[] = "Widget {$i}: title must be a string under 255 characters.";
            }

            // Validate filters for filtered-tasks widgets
            if ($type === 'filtered-tasks' && isset($widget['filters'])) {
                $filters = $widget['filters'];
                if (! is_array($filters)) {
                    $errors[] = "Widget {$i}: filters must be an object.";
                } else {
                    if (isset($filters['tags'])) {
                        if (! is_array($filters['tags'])) {
                            $errors[] = "Widget {$i}: filters.tags must be an array.";
                        } else {
                            foreach ($filters['tags'] as $tag) {
                                if (! is_string($tag)) {
                                    $errors[] = "Widget {$i}: filters.tags must contain strings.";
                                    break;
                                }
                            }
                        }
                    }
                    if (isset($filters['due_within']) && ! in_array($filters['due_within'], ['today', 'week', 'month'], true)) {
                        $errors[] = "Widget {$i}: filters.due_within must be today, week, or month.";
                    }
                    if (isset($filters['assigned_to']) && ! is_string($filters['assigned_to'])) {
                        $errors[] = "Widget {$i}: filters.assigned_to must be a string.";
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Migrate a v1 config to v2.
     */
    public static function migrateV1ToV2(array $config): array
    {
        if (($config['version'] ?? 1) >= self::CONFIG_VERSION) {
            return $config;
        }

        $newWidgets = [];

        foreach ($config['widgets'] ?? [] as $widget) {
            $type = $widget['type'] ?? '';
            $size = $widget['size'] ?? 'sm';
            $params = $widget['params'] ?? [];
            $endpoint = $widget['endpoint'] ?? '';

            $newType = match (true) {
                $type === 'welcome' => 'welcome',
                $type === 'countdown' => 'countdown',
                $type === 'quick-actions' => 'quick-actions',
                $type === 'leaderboard' => 'leaderboard',
                $type === 'badges', $type === 'badge-collection' => 'badge-collection',
                $type === 'rewards', $type === 'rewards-shop' => 'rewards-shop',
                $type === 'calendar-mini' => 'todays-schedule',
                $type === 'feed', $type === 'activity-feed' => 'activity-feed',
                $type === 'stat' && str_contains($endpoint, 'points') => 'points-summary',
                $type === 'list' && ($params['assigned_to'] ?? '') === 'me' => 'my-tasks',
                $type === 'list' && ($params['is_family_task'] ?? false) => 'family-tasks',
                $type === 'list' && str_contains($endpoint, 'tasks') => 'my-tasks',
                $type === 'list' && str_contains($endpoint, 'rewards') => 'rewards-shop',
                $type === 'list' && str_contains($endpoint, 'badges') => 'badge-collection',
                $type === 'progress' => null, // drop progress widget
                default => null,
            };

            if ($newType === null) {
                continue;
            }

            // Clamp size to supported sizes
            $supported = self::WIDGET_SIZES[$newType] ?? ['sm'];
            if (! in_array($size, $supported, true)) {
                $size = $supported[0];
            }

            $newWidgets[] = [
                'id' => $widget['id'] ?? (string) Str::uuid(),
                'type' => $newType,
                'size' => $size,
            ];
        }

        return [
            'version' => self::CONFIG_VERSION,
            'widgets' => $newWidgets,
        ];
    }
}
