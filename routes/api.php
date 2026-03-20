<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FamilyController;
use App\Http\Controllers\Api\V1\TaskListController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\VaultController;
use App\Http\Controllers\Api\V1\CalendarController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\SettingsController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\PointsController;
use App\Http\Controllers\Api\V1\RewardsController;
use App\Http\Controllers\Api\V1\BadgesController;

Route::prefix('v1')->group(function () {
    // Auth routes (no authentication required)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/auth/switch-profile', [AuthController::class, 'switchProfile']);
        Route::post('/auth/switch-back', [AuthController::class, 'switchBack']);

        // Family
        Route::prefix('/family')->group(function () {
            Route::get('/', [FamilyController::class, 'show']);
            Route::put('/', [FamilyController::class, 'update']);
            Route::post('/invite', [FamilyController::class, 'invite']);
            Route::post('/members', [FamilyController::class, 'addMember']);
            Route::put('/members/{member}', [FamilyController::class, 'updateMember']);
            Route::delete('/members/{member}', [FamilyController::class, 'removeMember']);
        });

        // Task Lists
        Route::prefix('/task-lists')->group(function () {
            Route::get('/', [TaskListController::class, 'index']);
            Route::post('/', [TaskListController::class, 'store']);
            Route::get('/{taskList}', [TaskListController::class, 'show']);
            Route::put('/{taskList}', [TaskListController::class, 'update']);
            Route::delete('/{taskList}', [TaskListController::class, 'destroy']);

            // Tasks under task list
            Route::prefix('/{taskList}/tasks')->group(function () {
                Route::get('/', [TaskController::class, 'index']);
                Route::post('/', [TaskController::class, 'store']);
            });
        });

        // Tags
        Route::prefix('/tags')->group(function () {
            Route::get('/', [TagController::class, 'index']);
            Route::post('/', [TagController::class, 'store']);
            Route::put('/{tag}', [TagController::class, 'update']);
            Route::delete('/{tag}', [TagController::class, 'destroy']);
        });

        // Tasks
        Route::prefix('/tasks')->group(function () {
            Route::get('/', [TaskController::class, 'index']);
            Route::post('/', [TaskController::class, 'store']);
            Route::get('/{task}', [TaskController::class, 'show']);
            Route::put('/{task}', [TaskController::class, 'update']);
            Route::delete('/{task}', [TaskController::class, 'destroy']);
            Route::patch('/{task}/complete', [TaskController::class, 'complete']);
            Route::patch('/{task}/uncomplete', [TaskController::class, 'uncomplete']);
        });

        // Vault
        Route::prefix('/vault')->group(function () {
            Route::get('/categories', [VaultController::class, 'categories']);
            Route::get('/entries', [VaultController::class, 'index']);
            Route::post('/entries', [VaultController::class, 'store']);
            Route::get('/entries/{entry}', [VaultController::class, 'show']);
            Route::put('/entries/{entry}', [VaultController::class, 'update']);
            Route::delete('/entries/{entry}', [VaultController::class, 'destroy']);
            Route::post('/entries/{entry}/permissions', [VaultController::class, 'grantPermission']);
            Route::delete('/entries/{entry}/permissions/{user}', [VaultController::class, 'revokePermission']);
            Route::post('/entries/{entry}/documents', [VaultController::class, 'uploadDocument']);
            Route::delete('/documents/{document}', [VaultController::class, 'deleteDocument']);
        });

        // Calendar
        Route::prefix('/calendar')->group(function () {
            Route::get('/events', [CalendarController::class, 'events']);
            Route::get('/connections', [CalendarController::class, 'connections']);
            Route::post('/connect', [CalendarController::class, 'connect']);
            Route::delete('/connections/{connection}', [CalendarController::class, 'disconnect']);
            Route::post('/subscribe', [CalendarController::class, 'subscribe']);
            Route::post('/sync', [CalendarController::class, 'sync']);
        });

        // Chat
        Route::prefix('/chat')->group(function () {
            Route::post('/', [ChatController::class, 'send']);
            Route::get('/history', [ChatController::class, 'history']);
        });

        // Points
        Route::prefix('/points')->group(function () {
            Route::get('/bank', [PointsController::class, 'bank']);
            Route::get('/leaderboard', [PointsController::class, 'leaderboard']);
            Route::get('/feed', [PointsController::class, 'feed']);
            Route::post('/kudos', [PointsController::class, 'kudos']);
            Route::post('/deduct', [PointsController::class, 'deduct']);
        });

        // Rewards
        Route::prefix('/rewards')->group(function () {
            Route::get('/', [RewardsController::class, 'index']);
            Route::post('/', [RewardsController::class, 'store']);
            Route::put('/{reward}', [RewardsController::class, 'update']);
            Route::delete('/{reward}', [RewardsController::class, 'destroy']);
            Route::post('/{reward}/purchase', [RewardsController::class, 'purchase']);
            Route::get('/purchases', [RewardsController::class, 'purchases']);
        });

        // Badges
        Route::prefix('/badges')->group(function () {
            Route::get('/', [BadgesController::class, 'index']);
            Route::post('/', [BadgesController::class, 'store']);
            Route::put('/{badge}', [BadgesController::class, 'update']);
            Route::delete('/{badge}', [BadgesController::class, 'destroy']);
            Route::post('/{badge}/award', [BadgesController::class, 'award']);
            Route::delete('/{badge}/revoke/{user}', [BadgesController::class, 'revoke']);
            Route::get('/earned', [BadgesController::class, 'earned']);
        });

        // Settings
        Route::prefix('/settings')->group(function () {
            Route::get('/', [SettingsController::class, 'index']);
            Route::put('/', [SettingsController::class, 'update']);
        });
    });

    // Calendar OAuth callback (public, stateless — Google redirects via GET)
    Route::get('/calendar/callback', [CalendarController::class, 'handleCallback'])->name('api.calendar.callback');
});
