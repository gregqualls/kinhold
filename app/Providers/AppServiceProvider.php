<?php

namespace App\Providers;

use App\Models\MealPlanEntry;
use App\Models\ShoppingItem;
use App\Policies\MealPlanPolicy;
use App\Policies\ShoppingListPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Passport OAuth configuration for MCP server
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::authorizationView('mcp.authorize');

        // Recipe import: 20 requests per hour per family
        RateLimiter::for('recipe-import', function ($request) {
            return Limit::perHour(20)->by($request->user()?->family_id ?? $request->ip());
        });

        // ShoppingItem uses ShoppingListPolicy (non-standard naming)
        Gate::policy(ShoppingItem::class, ShoppingListPolicy::class);

        // MealPlanEntry uses MealPlanPolicy (entry-level checks live in the plan policy)
        Gate::policy(MealPlanEntry::class, MealPlanPolicy::class);
    }
}
