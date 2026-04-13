<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopping_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('shopping_list_id');
            $table->uuid('family_id');
            $table->uuid('added_by');
            $table->string('name', 255);
            $table->string('quantity', 100)->nullable();
            $table->string('category', 100)->nullable();
            $table->boolean('is_checked')->default(false);
            $table->uuid('checked_by')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->boolean('has_on_hand')->default(false);
            $table->string('source');
            $table->uuid('source_recipe_id')->nullable();
            $table->string('source_recipe_name', 255)->nullable();
            $table->uuid('source_ingredient_id')->nullable();
            $table->uuid('meal_plan_entry_id')->nullable();
            $table->date('needed_date')->nullable();
            $table->string('notes', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('shopping_list_id')->references('id')->on('shopping_lists')->cascadeOnDelete();
            $table->foreign('family_id')->references('id')->on('families')->cascadeOnDelete();
            $table->foreign('added_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('checked_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('source_recipe_id')->references('id')->on('recipes')->nullOnDelete();
            $table->foreign('source_ingredient_id')->references('id')->on('recipe_ingredients')->nullOnDelete();

            $table->index(['shopping_list_id', 'is_checked']);
            $table->index(['shopping_list_id', 'category']);
            $table->index(['family_id', 'name']);
            $table->index('meal_plan_entry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopping_items');
    }
};
