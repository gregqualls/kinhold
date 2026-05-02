<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marks the start of the 7-day grace period after a Stripe `invoice.payment_failed`
 * event. Cleared by `invoice.paid`. Used by `kinhold:enforce-grace-period` to
 * fire day-3 retry emails and the day-7 downgrade. See 70-H (#221).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->timestamp('payment_failed_at')->nullable()->after('settings');
        });
    }

    public function down(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->dropColumn('payment_failed_at');
        });
    }
};
