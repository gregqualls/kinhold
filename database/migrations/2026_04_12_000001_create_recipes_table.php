<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('family_id');
            $table->uuid('created_by');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->integer('servings')->default(4);
            $table->integer('prep_time_minutes')->nullable();
            $table->integer('cook_time_minutes')->nullable();
            $table->integer('total_time_minutes')->nullable();
            $table->string('source_url', 2048)->nullable();
            $table->string('source_type')->default('manual');
            $table->string('image_path')->nullable();
            $table->json('instructions')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('family_id')->references('id')->on('families')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->index(['family_id', 'is_favorite']);
            $table->index(['family_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
