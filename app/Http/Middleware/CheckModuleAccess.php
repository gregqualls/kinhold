<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:  ->middleware('module:tasks')
     *
     * Parents always have access unless the module is globally disabled ('off').
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $family = $user->family;

        if (! $family) {
            return response()->json(['message' => 'No family found.'], 403);
        }

        if (! $family->userHasModuleAccess($module, $user)) {
            return response()->json([
                'message' => 'You do not have access to this feature.',
                'module' => $module,
            ], 403);
        }

        return $next($request);
    }
}
