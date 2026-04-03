<?php

use Illuminate\Support\Facades\Schedule;

// Send weekly digest emails every Monday at 8am
Schedule::command('app:send-weekly-digest')->weeklyOn(1, '8:00');

// Generate recurring task instances daily at 00:05
Schedule::command('app:generate-recurring-tasks')->dailyAt('00:05');

// Re-seed demo family daily so data stays fresh
Schedule::command('app:refresh-demo')->dailyAt('03:05');

// Resolve timed auctions every minute
Schedule::command('rewards:resolve-auctions')->everyMinute();
