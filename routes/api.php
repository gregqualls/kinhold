<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FamilyController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\VaultController;
use App\Http\Controllers\Api\V1\CalendarController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\SettingsController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\PointsController;
use App\Http\Controllers\Api\V1\PointRequestController;
use App\Http\Controllers\Api\V1\RewardsController;
use App\Http\Controllers\Api\V1\BadgesController;
use App\Http\Controllers\Api\V1\FeaturedEventController;
use App\Http\Controllers\Api\V1\McpTokenController;
use App\Http\Controllers\Api\V1\GoogleAuthController;
use App\Http\Controllers\Api\V1\OnboardingController;

Route::prefix('v1')->group(function () {
    // Auth routes (no authentication required)
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/auth/exchange', [GoogleAuthController::class, 'exchange'])->middleware('throttle:10,1');
    Route::post('/auth/google/confirm-link', [GoogleAuthController::class, 'confirmLink'])->middleware('throttle:5,1');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/auth/switch-profile', [AuthController::class, 'switchProfile']);
        Route::patch('/user', [AuthController::class, 'updateProfile']);
        Route::post('/user/avatar', [AuthController::class, 'uploadAvatar']);
        Route::delete('/user/avatar', [AuthController::class, 'deleteAvatar']);
        Route::put('/user/avatar/preset', [AuthController::class, 'setPresetAvatar']);
        Route::post('/user/avatar/google', [AuthController::class, 'restoreGoogleAvatar']);

        // Google account linking
        Route::get('/auth/google/link', [GoogleAuthController::class, 'linkRedirect']);
        Route::delete('/auth/google/unlink', [GoogleAuthController::class, 'unlink']);

        // Email verification
        Route::post('/email/resend', [AuthController::class, 'resendVerification'])->middleware('throttle:3,1');

        // Onboarding
        Route::get('/onboarding/status', [OnboardingController::class, 'status']);
        Route::post('/onboarding/complete', [OnboardingController::class, 'complete']);

        // Family
        Route::prefix('/family')->group(function () {
            Route::get('/', [FamilyController::class, 'show']);
            Route::put('/', [FamilyController::class, 'update']);
            Route::post('/invite', [FamilyController::class, 'invite']);
            Route::post('/members', [FamilyController::class, 'addMember']);
            Route::put('/members/{member}', [FamilyController::class, 'updateMember']);
            Route::delete('/members/{member}', [FamilyController::class, 'removeMember']);
        });

        // Tags (module: tasks)
        Route::prefix('/tags')->middleware('module:tasks')->group(function () {
            Route::get('/', [TagController::class, 'index']);
            Route::post('/', [TagController::class, 'store']);
            Route::put('/{tag}', [TagController::class, 'update']);
            Route::delete('/{tag}', [TagController::class, 'destroy']);
        });

        // Tasks (module: tasks)
        Route::prefix('/tasks')->middleware('module:tasks')->group(function () {
            Route::get('/', [TaskController::class, 'index']);
            Route::post('/', [TaskController::class, 'store']);
            Route::get('/{task}', [TaskController::class, 'show']);
            Route::put('/{task}', [TaskController::class, 'update']);
            Route::delete('/{task}', [TaskController::class, 'destroy']);
            Route::patch('/{task}/complete', [TaskController::class, 'complete']);
            Route::patch('/{task}/uncomplete', [TaskController::class, 'uncomplete']);
        });

        // Vault (module: vault)
        Route::prefix('/vault')->middleware('module:vault')->group(function () {
            Route::get('/categories', [VaultController::class, 'categories']);
            Route::get('/entries', [VaultController::class, 'index']);
            Route::post('/entries', [VaultController::class, 'store']);
            Route::get('/entries/{entry}', [VaultController::class, 'show']);
            Route::put('/entries/{entry}', [VaultController::class, 'update']);
            Route::delete('/entries/{entry}', [VaultController::class, 'destroy']);
            Route::post('/entries/{entry}/permissions', [VaultController::class, 'grantPermission']);
            Route::delete('/entries/{entry}/permissions/{user}', [VaultController::class, 'revokePermission']);
            Route::post('/entries/{entry}/documents', [VaultController::class, 'uploadDocument']);
            Route::get('/documents/{document}/download', [VaultController::class, 'downloadDocument']);
            Route::delete('/documents/{document}', [VaultController::class, 'deleteDocument']);
        });

        // Calendar (module: calendar)
        Route::prefix('/calendar')->middleware('module:calendar')->group(function () {
            Route::get('/events', [CalendarController::class, 'events']);
            Route::post('/events', [CalendarController::class, 'storeEvent']);
            Route::put('/events/{familyEvent}', [CalendarController::class, 'updateEvent']);
            Route::delete('/events/{familyEvent}', [CalendarController::class, 'destroyEvent']);
            Route::get('/connections', [CalendarController::class, 'connections']);
            Route::post('/connect', [CalendarController::class, 'connect']);
            Route::delete('/connections/{connection}', [CalendarController::class, 'disconnect']);
            Route::post('/subscribe', [CalendarController::class, 'subscribe']);
            Route::post('/sync', [CalendarController::class, 'sync']);
        });

        // Chat (module: chat)
        Route::prefix('/chat')->middleware('module:chat')->group(function () {
            Route::post('/', [ChatController::class, 'send']);
            Route::get('/history', [ChatController::class, 'history']);
        });

        // Points (module: points)
        Route::prefix('/points')->middleware('module:points')->group(function () {
            Route::get('/bank', [PointsController::class, 'bank']);
            Route::get('/leaderboard', [PointsController::class, 'leaderboard']);
            Route::get('/feed', [PointsController::class, 'feed']);
            Route::post('/kudos', [PointsController::class, 'kudos']);
            Route::post('/deduct', [PointsController::class, 'deduct']);
            Route::get('/requests', [PointRequestController::class, 'index']);
            Route::post('/request', [PointRequestController::class, 'store']);
            Route::post('/requests/{pointRequest}/approve', [PointRequestController::class, 'approve']);
            Route::post('/requests/{pointRequest}/deny', [PointRequestController::class, 'deny']);
        });

        // Rewards (module: points)
        Route::prefix('/rewards')->middleware('module:points')->group(function () {
            Route::get('/', [RewardsController::class, 'index']);
            Route::post('/', [RewardsController::class, 'store']);
            Route::put('/{reward}', [RewardsController::class, 'update']);
            Route::delete('/{reward}', [RewardsController::class, 'destroy']);
            Route::post('/{reward}/purchase', [RewardsController::class, 'purchase']);
            Route::get('/purchases', [RewardsController::class, 'purchases']);
        });

        // Badges (module: badges)
        Route::prefix('/badges')->middleware('module:badges')->group(function () {
            Route::get('/', [BadgesController::class, 'index']);
            Route::post('/', [BadgesController::class, 'store']);
            Route::put('/{badge}', [BadgesController::class, 'update']);
            Route::delete('/{badge}', [BadgesController::class, 'destroy']);
            Route::post('/{badge}/award', [BadgesController::class, 'award']);
            Route::delete('/{badge}/revoke/{user}', [BadgesController::class, 'revoke']);
            Route::get('/earned', [BadgesController::class, 'earned']);
            Route::post('/easter-egg', [BadgesController::class, 'easterEgg']);
        });

        // Featured Events
        Route::prefix('/featured-events')->group(function () {
            Route::get('/', [FeaturedEventController::class, 'index']);
            Route::get('/countdown', [FeaturedEventController::class, 'countdown']);
            Route::post('/', [FeaturedEventController::class, 'store']);
            Route::put('/{featuredEvent}', [FeaturedEventController::class, 'update']);
            Route::put('/{featuredEvent}/countdown', [FeaturedEventController::class, 'setCountdown']);
            Route::delete('/{featuredEvent}', [FeaturedEventController::class, 'destroy']);
        });

        // Settings
        Route::prefix('/settings')->group(function () {
            Route::get('/', [SettingsController::class, 'index']);
            Route::put('/', [SettingsController::class, 'update']);
            Route::get('/email-preferences', [SettingsController::class, 'emailPreferences']);
            Route::put('/email-preferences', [SettingsController::class, 'updateEmailPreferences']);
        });

        // MCP Token
        Route::prefix('/mcp')->group(function () {
            Route::get('/token', [McpTokenController::class, 'show']);
            Route::post('/token', [McpTokenController::class, 'store']);
            Route::delete('/token', [McpTokenController::class, 'destroy']);
        });
    });

    // Avatar serving (public — avatars are visible to all family members)
    Route::get('/user/avatar/{userId}', [AuthController::class, 'serveAvatar']);

    // Calendar OAuth callback (public, stateless — Google redirects via GET)
    Route::get('/calendar/callback', [CalendarController::class, 'handleCallback'])->name('api.calendar.callback');
});
