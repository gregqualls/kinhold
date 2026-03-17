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
        Schema::create('vault_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vault_entry_id');
            $table->uuid('user_id');
            $table->string('permission_level')->default('view');
            $table->timestamps();

            $table->foreign('vault_entry_id')->references('id')->on('vault_entries')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['vault_entry_id', 'user_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vault_permissions');
    }
};
