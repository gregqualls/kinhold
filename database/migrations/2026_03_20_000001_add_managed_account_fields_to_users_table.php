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
        Schema::table('users', function (Blueprint $table) {
            // Make email nullable for managed child accounts (kids without email)
            $table->string('email')->nullable()->change();

            // Make password nullable for managed child accounts
            $table->string('password')->nullable()->change();

            // Managed account fields
            $table->boolean('is_managed')->default(false)->after('family_role');
            $table->uuid('managed_by')->nullable()->after('is_managed');

            $table->foreign('managed_by')->references('id')->on('users')->onDelete('set null');
        });

        // Drop the existing unique index and replace with one that allows multiple nulls
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['managed_by']);
            $table->dropColumn(['is_managed', 'managed_by']);

            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
