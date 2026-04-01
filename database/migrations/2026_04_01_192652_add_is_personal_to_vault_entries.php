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
        Schema::table('vault_entries', function (Blueprint $table) {
            $table->boolean('is_personal')->default(false)->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('vault_entries', function (Blueprint $table) {
            $table->dropColumn('is_personal');
        });
    }
};
