<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            // Phase 1: Quantity & Expiration
            $table->integer('quantity')->nullable()->after('icon');
            $table->integer('quantity_purchased')->default(0)->after('quantity');
            $table->timestamp('expires_at')->nullable()->after('quantity_purchased');

            // Phase 2: Visibility
            $table->string('visibility')->default('everyone')->after('expires_at');
            $table->json('visible_to')->nullable()->after('visibility');
            $table->integer('min_age')->nullable()->after('visible_to');
            $table->integer('max_age')->nullable()->after('min_age');
        });
    }

    public function down(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn([
                'quantity',
                'quantity_purchased',
                'expires_at',
                'visibility',
                'visible_to',
                'min_age',
                'max_age',
            ]);
        });
    }
};
