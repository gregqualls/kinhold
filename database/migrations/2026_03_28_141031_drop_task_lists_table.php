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
            $table->dropForeign(['task_list_id']);
            $table->dropIndex(['task_list_id']);
            $table->dropColumn('task_list_id');
        });

        Schema::dropIfExists('task_lists');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('task_lists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('family_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->uuid('task_list_id')->nullable()->after('family_id');
            $table->foreign('task_list_id')->references('id')->on('task_lists')->onDelete('cascade');
            $table->index('task_list_id');
        });
    }
};
