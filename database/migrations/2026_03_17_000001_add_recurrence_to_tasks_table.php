<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->integer('points')->nullable()->after('is_family_task');
            $table->string('recurrence_rule')->nullable()->after('points');
            $table->date('recurrence_end')->nullable()->after('recurrence_rule');
            $table->uuid('parent_task_id')->nullable()->after('recurrence_end');

            $table->foreign('parent_task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->index('parent_task_id');
            $table->index('recurrence_rule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['parent_task_id']);
            $table->dropIndex(['parent_task_id']);
            $table->dropIndex(['recurrence_rule']);
            $table->dropColumn(['points', 'recurrence_rule', 'recurrence_end', 'parent_task_id']);
        });
    }
};
