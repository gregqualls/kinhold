<?php

namespace App\Mcp\Tools\Concerns;

use Laravel\Mcp\Request;

/**
 * Gates a consolidated MCP tool behind a family module setting.
 *
 * Implementing classes must define a public MODULE constant matching one of
 * Family::MODULES (e.g. 'calendar', 'tasks', 'food'). The tool will only be
 * registered for users whose family has access to that module — keeping the
 * tool's schema out of the wire format (and the LLM's context window) when
 * the module is disabled.
 */
trait RequiresModule
{
    public function shouldRegister(Request $request): bool
    {
        $user = $request?->user();
        if (! $user || ! $user->family) {
            return false;
        }

        return $user->family->userHasModuleAccess(static::MODULE, $user);
    }
}
