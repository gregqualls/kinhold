<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipe_cook_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('recipe_id');
            $table->uuid('user_id');
            $table->date('cooked_at');
            $table->integer('servings_made')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['recipe_id', 'cooked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_cook_logs');
    }
};
