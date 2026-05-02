<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_storage_usages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('family_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('total_bytes')->default(0);
            // Last absolute byte total we pushed to Stripe. Stripe meter events
            // are additive (sum aggregation) — we only push the *delta* since
            // the last report, otherwise we double-count on every nightly run.
            $table->unsignedBigInteger('reported_bytes')->default(0);
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamp('last_reported_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_storage_usages');
    }
};
