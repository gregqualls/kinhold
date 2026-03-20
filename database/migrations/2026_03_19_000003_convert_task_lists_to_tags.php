<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Convert existing task lists into tags
        $taskLists = DB::table('task_lists')->get();
        $listToTag = [];

        foreach ($taskLists as $list) {
            $tagId = Str::uuid()->toString();
            DB::table('tags')->insert([
                'id' => $tagId,
                'family_id' => $list->family_id,
                'name' => $list->name,
                'color' => $list->color,
                'sort_order' => $list->sort_order,
                'created_at' => $list->created_at,
                'updated_at' => $list->updated_at,
            ]);
            $listToTag[$list->id] = $tagId;
        }

        // Link tasks to their corresponding tags
        $tasks = DB::table('tasks')->whereNotNull('task_list_id')->get();
        foreach ($tasks as $task) {
            if (isset($listToTag[$task->task_list_id])) {
                DB::table('task_tag')->insert([
                    'id' => Str::uuid()->toString(),
                    'task_id' => $task->id,
                    'tag_id' => $listToTag[$task->task_list_id],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Make task_list_id nullable
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignUuid('task_list_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignUuid('task_list_id')->nullable(false)->change();
        });
    }
};
