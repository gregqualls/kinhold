<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Services\DashboardConfigService;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('manage-dashboard')]
#[Description('Get or set the user\'s dashboard layout. Actions: get, set, add_widget, remove_widget, reorder. Widgets are purpose-built per feature: my-tasks, family-tasks, todays-schedule, points-summary, leaderboard, activity-feed, rewards-shop, badge-collection, welcome, countdown, quick-actions.')]
class ManageDashboard extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()
                ->required()
                ->description('Action: get, set, add_widget, remove_widget, reorder')
                ->enum(['get', 'set', 'add_widget', 'remove_widget', 'reorder']),
            'config' => $schema->object()
                ->description('Full dashboard config (for "set"). Must have version and widgets array.'),
            'widget_type' => $schema->string()
                ->description('Widget type to add (for "add_widget"). E.g., my-tasks, leaderboard, badge-collection.')
                ->enum(DashboardConfigService::WIDGET_TYPES),
            'widget_size' => $schema->string()
                ->description('Widget size (for "add_widget"). Must be a supported size for the type.')
                ->enum(['sm', 'md', 'lg']),
            'widget_id' => $schema->string()
                ->description('Widget UUID to remove (for "remove_widget").'),
            'widget_title' => $schema->string()
                ->description('Optional custom title for the widget.'),
            'widget_filters' => $schema->object()
                ->description('Filters for "filtered-tasks" widget. Keys: tags (array of tag UUIDs), due_within (today/week/month), assigned_to (user UUID or "me").'),
            'widget_ids' => $schema->array()
                ->description('Ordered widget UUIDs (for "reorder").'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'get' => $this->get(),
            'set' => $this->set($request),
            'add_widget' => $this->addWidget($request),
            'remove_widget' => $this->removeWidget($request),
            'reorder' => $this->reorder($request),
            default => Response::error('Invalid action.'),
        };
    }

    private function get(): Response
    {
        $user = $this->user();
        $config = $user->dashboard_config;

        if (! $config) {
            $config = DashboardConfigService::defaultFor($user);
        } elseif (($config['version'] ?? 1) < DashboardConfigService::CONFIG_VERSION) {
            $config = DashboardConfigService::migrateV1ToV2($config);
            $user->dashboard_config = $config;
            $user->save();
        }

        return Response::json([
            'config' => $config,
            'available_types' => DashboardConfigService::WIDGET_TYPES,
            'widget_sizes' => DashboardConfigService::WIDGET_SIZES,
        ]);
    }

    private function set(Request $request): Response
    {
        $config = $request->get('config');

        if (! $config || ! is_array($config)) {
            return Response::error('Config is required with version and widgets array.');
        }

        $errors = DashboardConfigService::validate($config);
        if (! empty($errors)) {
            return Response::error('Invalid config: '.implode(' ', $errors));
        }

        $user = $this->user();
        $user->dashboard_config = $config;
        $user->save();

        return Response::json([
            'message' => 'Dashboard config saved.',
            'widget_count' => count($config['widgets']),
        ]);
    }

    private function addWidget(Request $request): Response
    {
        $type = $request->get('widget_type');
        $size = $request->get('widget_size');

        if (! $type || ! in_array($type, DashboardConfigService::WIDGET_TYPES, true)) {
            return Response::error('Invalid widget type. Valid: '.implode(', ', DashboardConfigService::WIDGET_TYPES));
        }

        $supported = DashboardConfigService::WIDGET_SIZES[$type] ?? ['sm'];
        if (! $size) {
            $size = $supported[0];
        }
        if (! in_array($size, $supported, true)) {
            return Response::error("Size '{$size}' not supported for '{$type}'. Valid: ".implode(', ', $supported));
        }

        $user = $this->user();
        $config = $user->dashboard_config ?? DashboardConfigService::defaultFor($user);

        if (count($config['widgets']) >= 20) {
            return Response::error('Maximum 20 widgets allowed.');
        }

        $widget = [
            'id' => (string) Str::uuid(),
            'type' => $type,
            'size' => $size,
        ];

        if ($request->get('widget_title')) {
            $title = $request->get('widget_title');
            if (! is_string($title) || strlen($title) > 255) {
                return Response::error('Widget title must be a string under 255 characters.');
            }
            $widget['title'] = $title;
        }

        if ($type === 'filtered-tasks' && $request->get('widget_filters')) {
            $filters = $request->get('widget_filters');
            if (! is_array($filters)) {
                return Response::error('Widget filters must be an object.');
            }
            if (isset($filters['tags']) && ! is_array($filters['tags'])) {
                return Response::error('filters.tags must be an array of tag UUIDs.');
            }
            if (isset($filters['due_within']) && ! in_array($filters['due_within'], ['today', 'week', 'month'], true)) {
                return Response::error('filters.due_within must be today, week, or month.');
            }
            if (isset($filters['assigned_to']) && ! is_string($filters['assigned_to'])) {
                return Response::error('filters.assigned_to must be a string.');
            }
            $widget['filters'] = $filters;
        }

        $config['widgets'][] = $widget;

        $user->dashboard_config = $config;
        $user->save();

        return Response::json(['message' => "Added '{$type}' widget."]);
    }

    private function removeWidget(Request $request): Response
    {
        $widgetId = $request->get('widget_id');

        if (! $widgetId) {
            return Response::error('widget_id is required.');
        }

        $user = $this->user();
        $config = $user->dashboard_config ?? DashboardConfigService::defaultFor($user);

        $before = count($config['widgets']);
        $config['widgets'] = array_values(array_filter(
            $config['widgets'],
            fn ($w) => $w['id'] !== $widgetId
        ));

        if (count($config['widgets']) === $before) {
            return Response::error("Widget '{$widgetId}' not found.");
        }

        $user->dashboard_config = $config;
        $user->save();

        return Response::json(['message' => 'Widget removed.']);
    }

    private function reorder(Request $request): Response
    {
        $widgetIds = $request->get('widget_ids');

        if (! $widgetIds || ! is_array($widgetIds)) {
            return Response::error('widget_ids array is required.');
        }

        $user = $this->user();
        $config = $user->dashboard_config ?? DashboardConfigService::defaultFor($user);

        $indexed = collect($config['widgets'])->keyBy('id');
        $reordered = [];

        foreach ($widgetIds as $id) {
            if ($indexed->has($id)) {
                $reordered[] = $indexed->get($id);
            }
        }

        foreach ($config['widgets'] as $widget) {
            if (! in_array($widget['id'], $widgetIds, true)) {
                $reordered[] = $widget;
            }
        }

        $config['widgets'] = $reordered;
        $user->dashboard_config = $config;
        $user->save();

        return Response::json(['message' => 'Widgets reordered.']);
    }
}
