<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->uuid('stacked_from_transaction_id')->nullable()->after('awarded_by');
            $table->foreign('stacked_from_transaction_id')
                ->references('id')->on('point_transactions')
                ->onDelete('set null');
            $table->index('stacked_from_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropForeign(['stacked_from_transaction_id']);
            $table->dropIndex(['stacked_from_transaction_id']);
            $table->dropColumn('stacked_from_transaction_id');
        });
    }
};
