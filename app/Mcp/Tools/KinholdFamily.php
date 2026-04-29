<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\Task;
use App\Models\VaultEntry;
use App\Services\DashboardConfigService;
use App\Services\UpdateCheckService;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('kinhold-family')]
#[Description(<<<'DESC'
Family info, settings, cross-module search, and per-user dashboard layout.

Actions:
  family_info — Family details + member roster (read-only).
  family_members — Detailed member list with bank balances (read-only).
  family_member (member_id*) — One member's full profile (read-only).
  settings — Current family settings: enabled modules, leaderboard period, default points, AI config status, app version (read-only).
  search (query*) — Cross-module search over tasks, vault entries (permission-aware), and members (read-only).
  dashboard_get — Current user's dashboard config + available widget types/sizes.
  dashboard_set (config*) — Replace dashboard config wholesale.
  dashboard_add_widget (widget_type*, widget_size?, widget_title?, widget_filters?) — Add a single widget.
  dashboard_remove_widget (widget_id*) — Remove a widget by id.
  dashboard_reorder (widget_ids*) — Reorder widgets by id.

Widget types: my-tasks, family-tasks, todays-schedule, points-summary, leaderboard, activity-feed, rewards-shop, badge-collection, welcome, countdown, quick-actions, filtered-tasks.
DESC)]
class KinholdFamily extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum([
                'family_info', 'family_members', 'family_member',
                'settings', 'search',
                'dashboard_get', 'dashboard_set', 'dashboard_add_widget', 'dashboard_remove_widget', 'dashboard_reorder',
            ])->description('Action to perform'),
            'member_id' => $schema->string()->description('User UUID (required for family_member)'),
            'query' => $schema->string()->description('Search keywords (required for search)'),
            'config' => $schema->object()->description('Full dashboard config (for dashboard_set). Must have version and widgets array.'),
            'widget_type' => $schema->string()->enum(DashboardConfigService::WIDGET_TYPES)->description('Widget type (for dashboard_add_widget)'),
            'widget_size' => $schema->string()->enum(['sm', 'md', 'lg'])->description('Widget size (for dashboard_add_widget)'),
            'widget_id' => $schema->string()->description('Widget UUID (for dashboard_remove_widget)'),
            'widget_title' => $schema->string()->description('Optional custom widget title'),
            'widget_filters' => $schema->object()->description('Filters for filtered-tasks widget. Keys: tags (UUIDs), due_within (today/week/month), assigned_to (UUID or "me").'),
            'widget_ids' => $schema->array()->description('Ordered widget UUIDs (for dashboard_reorder)'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'family_info' => $this->familyInfo(),
            'family_members' => $this->familyMembers(),
            'family_member' => $this->familyMember($request),
            'settings' => $this->settings(),
            'search' => $this->search($request),
            'dashboard_get' => $this->dashboardGet(),
            'dashboard_set' => $this->dashboardSet($request),
            'dashboard_add_widget' => $this->dashboardAddWidget($request),
            'dashboard_remove_widget' => $this->dashboardRemoveWidget($request),
            'dashboard_reorder' => $this->dashboardReorder($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function familyInfo(): Response
    {
        $family = $this->family();
        $family->load('members');

        return Response::json([
            'family' => [
                'id' => $family->id,
                'name' => $family->name,
                'slug' => $family->slug,
                'invite_code' => $this->isParent() ? $family->invite_code : null,
                'member_count' => $family->members->count(),
                'members' => $family->members->map(fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'role' => $m->family_role->value ?? $m->family_role,
                ])->toArray(),
            ],
        ]);
    }

    private function familyMembers(): Response
    {
        $members = $this->family()->members()->get();

        return Response::json([
            'members' => $members->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'email' => $m->email,
                'role' => $m->family_role->value ?? $m->family_role,
                'avatar' => $m->avatar,
                'date_of_birth' => $m->date_of_birth?->format('Y-m-d'),
                'timezone' => $m->timezone,
                'points_bank' => $m->pointBank(),
            ])->toArray(),
        ]);
    }

    private function familyMember(Request $request): Response
    {
        $memberId = $request->get('member_id');
        if (! $memberId) {
            return Response::error('member_id is required for family_member.');
        }

        $member = $this->family()->members()->find($memberId);
        if (! $member) {
            return Response::error('Family member not found.');
        }

        return Response::json([
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'role' => $member->family_role->value ?? $member->family_role,
                'avatar' => $member->avatar,
                'date_of_birth' => $member->date_of_birth?->format('Y-m-d'),
                'timezone' => $member->timezone,
                'points_bank' => $member->pointBank(),
            ],
        ]);
    }

    private function settings(): Response
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
                'app_version' => config('version.current'),
                'update_available' => app(UpdateCheckService::class)->getStatus(),
            ],
        ]);
    }

    private function search(Request $request): Response
    {
        $query = $request->get('query');
        if (! $query) {
            return Response::error('query is required for search.');
        }

        $familyId = $this->familyId();
        $user = $this->user();
        $results = [];

        $tasks = Task::where('family_id', $familyId)
            ->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                    ->orWhere('description', 'ilike', "%{$query}%");
            })
            ->limit(10)
            ->get();

        if ($tasks->isNotEmpty()) {
            $results['tasks'] = $tasks->map(fn ($t) => [
                'id' => $t->id,
                'title' => $t->title,
                'status' => $t->isComplete() ? 'completed' : 'pending',
                'due_date' => $t->due_date?->format('Y-m-d'),
                'assigned_to' => $t->assignee?->name,
            ])->toArray();
        }

        $vaultQuery = VaultEntry::where('family_id', $familyId)
            ->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                    ->orWhere('notes', 'ilike', "%{$query}%");
            });

        if (! $user->isParent()) {
            $vaultQuery->whereHas('permissions', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $vaultEntries = $vaultQuery->limit(10)->get();

        if ($vaultEntries->isNotEmpty()) {
            $results['vault_entries'] = $vaultEntries->map(fn ($v) => [
                'id' => $v->id,
                'title' => $v->title,
                'category' => $v->category?->name,
            ])->toArray();
        }

        $members = $this->family()->members()
            ->where(function ($q) use ($query) {
                $q->where('name', 'ilike', "%{$query}%")
                    ->orWhere('email', 'ilike', "%{$query}%");
            })
            ->limit(5)
            ->get();

        if ($members->isNotEmpty()) {
            $results['members'] = $members->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'role' => $m->family_role->value ?? $m->family_role,
            ])->toArray();
        }

        if (empty($results)) {
            return Response::text("No results found for \"{$query}\".");
        }

        return Response::json($results);
    }

    private function dashboardGet(): Response
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

    private function dashboardSet(Request $request): Response
    {
        $config = $request->get('config');

        if (! $config || ! is_array($config)) {
            return Response::error('config is required with version and widgets array.');
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

    private function dashboardAddWidget(Request $request): Response
    {
        $type = $request->get('widget_type');
        $size = $request->get('widget_size');

        if (! $type || ! in_array($type, DashboardConfigService::WIDGET_TYPES, true)) {
            return Response::error('Invalid widget_type. Valid: '.implode(', ', DashboardConfigService::WIDGET_TYPES));
        }

        $supported = DashboardConfigService::WIDGET_SIZES[$type] ?? ['sm']; // @phpstan-ignore-line
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
                return Response::error('widget_title must be a string under 255 characters.');
            }
            $widget['title'] = $title;
        }

        if ($type === 'filtered-tasks' && $request->get('widget_filters')) {
            $filters = $request->get('widget_filters');
            if (! is_array($filters)) {
                return Response::error('widget_filters must be an object.');
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

    private function dashboardRemoveWidget(Request $request): Response
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

    private function dashboardReorder(Request $request): Response
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
