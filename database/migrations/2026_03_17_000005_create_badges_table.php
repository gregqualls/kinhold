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
        Schema::create('badges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('family_id')->nullable(); // null for system presets
            $table->uuid('created_by')->nullable();
            $table->string('name');
            $table->string('description');
            $table->string('icon')->nullable();
            $table->string('custom_icon_path')->nullable();
            $table->string('color')->default('#7d57a8');
            $table->string('trigger_type'); // BadgeTriggerType enum
            $table->integer('trigger_threshold')->nullable();
            $table->boolean('is_hidden')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('family_id')->references('id')->on('families')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->index('family_id');
            $table->index('trigger_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
