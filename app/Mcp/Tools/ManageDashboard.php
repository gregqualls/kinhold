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
#[Description('Get or set the user\'s dashboard layout. Actions: get (view current config), set (replace entire config), add_widget (add one widget), remove_widget (remove by ID), reorder (reorder widget IDs). The dashboard is a JSON config of widgets bound to API endpoints.')]
class ManageDashboard extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()
                ->required()
                ->description('Action to perform')
                ->enum(['get', 'set', 'add_widget', 'remove_widget', 'reorder']),
            'config' => $schema->object()
                ->description('Full dashboard config JSON (for "set" action). Must have version:1 and widgets array.'),
            'widget' => $schema->object()
                ->description('Widget to add (for "add_widget"). Fields: type (required), title, endpoint, params, size (sm/md/lg), settings.'),
            'widget_id' => $schema->string()
                ->description('Widget UUID to remove (for "remove_widget" action).'),
            'widget_ids' => $schema->array()
                ->description('Ordered array of widget UUIDs (for "reorder" action).'),
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
        }

        return Response::json([
            'config' => $config,
            'available_widget_types' => DashboardConfigService::WIDGET_TYPES,
            'available_endpoints' => DashboardConfigService::AVAILABLE_ENDPOINTS,
        ]);
    }

    private function set(Request $request): Response
    {
        $config = $request->get('config');

        if (! $config || ! is_array($config)) {
            return Response::error('Config is required and must be an object with version and widgets.');
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
        $widget = $request->get('widget');

        if (! $widget || ! is_array($widget)) {
            return Response::error('Widget object is required with at least a type field.');
        }

        if (! isset($widget['type']) || ! in_array($widget['type'], DashboardConfigService::WIDGET_TYPES, true)) {
            return Response::error('Invalid widget type. Valid types: '.implode(', ', DashboardConfigService::WIDGET_TYPES));
        }

        $user = $this->user();
        $config = $user->dashboard_config ?? DashboardConfigService::defaultFor($user);

        if (count($config['widgets']) >= 20) {
            return Response::error('Maximum 20 widgets allowed.');
        }

        $newWidget = [
            'id' => (string) Str::uuid(),
            'type' => $widget['type'],
            'title' => $widget['title'] ?? ucfirst($widget['type']),
            'endpoint' => $widget['endpoint'] ?? null,
            'params' => $widget['params'] ?? [],
            'size' => $widget['size'] ?? 'sm',
            'settings' => $widget['settings'] ?? [],
        ];

        $config['widgets'][] = $newWidget;

        $user->dashboard_config = $config;
        $user->save();

        return Response::json([
            'message' => "Widget '{$newWidget['title']}' added.",
            'widget_id' => $newWidget['id'],
        ]);
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
            return Response::error("Widget with ID '{$widgetId}' not found.");
        }

        $user->dashboard_config = $config;
        $user->save();

        return Response::json(['message' => 'Widget removed.', 'remaining' => count($config['widgets'])]);
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

        // Append any widgets not in the reorder list at the end
        foreach ($config['widgets'] as $widget) {
            if (! in_array($widget['id'], $widgetIds, true)) {
                $reordered[] = $widget;
            }
        }

        $config['widgets'] = $reordered;

        $user->dashboard_config = $config;
        $user->save();

        return Response::json(['message' => 'Widgets reordered.', 'order' => array_column($reordered, 'id')]);
    }
}
