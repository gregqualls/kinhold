<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_presets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('family_id')->constrained()->cascadeOnDelete();
            $table->string('label', 255);
            $table->string('icon', 50)->nullable();
            $table->boolean('is_system')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('restaurants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->string('google_maps_url', 2048)->nullable();
            $table->string('menu_url', 2048)->nullable();
            $table->string('cuisine', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('family_restaurants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('family_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('restaurant_id')->constrained()->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->timestamps();

            $table->unique(['family_id', 'restaurant_id']);
        });

        Schema::create('meal_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('family_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->date('week_start');
            $table->text('notes')->nullable();
            $table->foreignUuid('shopping_list_id')->nullable()->constrained('shopping_lists')->nullOnDelete();
            $table->timestamps();

            $table->unique(['family_id', 'week_start']);
        });

        Schema::create('meal_plan_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('meal_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('recipe_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('restaurant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('meal_preset_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->string('meal_slot');
            $table->string('custom_title', 255)->nullable();
            $table->integer('servings')->nullable();
            $table->json('assigned_cooks')->nullable();
            $table->string('notes', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['meal_plan_id', 'date', 'meal_slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_plan_entries');
        Schema::dropIfExists('meal_plans');
        Schema::dropIfExists('family_restaurants');
        Schema::dropIfExists('restaurants');
        Schema::dropIfExists('meal_presets');
    }
};
