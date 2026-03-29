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
            $table->boolean('allow_pileup')->default(false)->after('recurrence_end');
        });

        // Clean up stale recurring task pileups: for each parent template,
        // keep only the most recent incomplete instance, delete the rest.
        $templates = DB::table('tasks')
            ->whereNotNull('recurrence_rule')
            ->whereNull('parent_task_id')
            ->pluck('id');

        foreach ($templates as $templateId) {
            $pendingInstances = DB::table('tasks')
                ->where('parent_task_id', $templateId)
                ->whereNull('completed_at')
                ->orderByDesc('due_date')
                ->pluck('id');

            if ($pendingInstances->count() > 1) {
                // Keep the first (most recent), delete the rest
                $toDelete = $pendingInstances->slice(1);
                DB::table('tasks')->whereIn('id', $toDelete)->delete();
            }
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('allow_pileup');
        });
    }
};
