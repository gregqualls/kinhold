<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DashboardConfigService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get the authenticated user's dashboard config.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $config = $user->dashboard_config;

        if (! $config) {
            $config = DashboardConfigService::defaultFor($user);
        } elseif (($config['version'] ?? 1) < DashboardConfigService::CONFIG_VERSION) {
            // Auto-migrate old config versions
            $config = DashboardConfigService::migrateV1ToV2($config);
            $user->dashboard_config = $config;
            $user->save();
        }

        return response()->json(['config' => $config]);
    }

    /**
     * Update the authenticated user's dashboard config.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'config' => 'required|array',
            'config.version' => 'required|integer',
            'config.widgets' => 'required|array|max:20',
            'config.widgets.*.id' => 'required|string',
            'config.widgets.*.type' => 'required|string',
            'config.widgets.*.size' => 'required|string|in:sm,md,lg',
            'config.widgets.*.title' => 'sometimes|string|max:255',
            'config.widgets.*.filters' => 'sometimes|array',
            'config.widgets.*.filters.tags' => 'sometimes|array',
            'config.widgets.*.filters.tags.*' => 'string',
            'config.widgets.*.filters.due_within' => 'sometimes|string|in:today,week,month',
            'config.widgets.*.filters.assigned_to' => 'sometimes|string',
        ]);

        $config = $request->input('config');

        $errors = DashboardConfigService::validate($config);
        if (! empty($errors)) {
            return response()->json(['message' => 'Invalid dashboard config.', 'errors' => $errors], 422);
        }

        $user = $request->user();
        $user->dashboard_config = $config;
        $user->save();

        return response()->json(['config' => $config]);
    }
}
