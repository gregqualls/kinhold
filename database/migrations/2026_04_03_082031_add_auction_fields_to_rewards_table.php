<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->string('reward_type')->default('standard')->after('max_age');
            $table->integer('min_bid')->nullable()->after('reward_type');
            $table->timestamp('bid_start_at')->nullable()->after('min_bid');
            $table->timestamp('bid_end_at')->nullable()->after('bid_start_at');
        });
    }

    public function down(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn(['reward_type', 'min_bid', 'bid_start_at', 'bid_end_at']);
        });
    }
};
