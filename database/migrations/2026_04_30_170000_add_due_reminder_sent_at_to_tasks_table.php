<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->timestamp('due_reminder_sent_at')->nullable()->after('completed_at');
            $table->index(['due_date', 'due_reminder_sent_at'], 'tasks_due_reminder_idx');
        });

        // Mark every already-overdue / due-today incomplete task as already-reminded
        // so the first cron tick after deploy doesn't firehose stale items.
        DB::table('tasks')
            ->whereNull('completed_at')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', now())
            ->update(['due_reminder_sent_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_due_reminder_idx');
            $table->dropColumn('due_reminder_sent_at');
        });
    }
};
