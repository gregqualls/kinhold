<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_bids', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('family_id');
            $table->uuid('reward_id');
            $table->uuid('user_id');
            $table->integer('bid_amount');
            $table->integer('held_points')->default(0);
            $table->boolean('is_winning')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('family_id')->references('id')->on('families')->cascadeOnDelete();
            $table->foreign('reward_id')->references('id')->on('rewards')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->index(['reward_id', 'bid_amount']);
            $table->index('family_id');
            $table->unique(['reward_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_bids');
    }
};
