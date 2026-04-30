<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_reminder_sends', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('family_event_id')->constrained()->cascadeOnDelete();
            $table->date('occurrence_date');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->unique(['family_event_id', 'occurrence_date'], 'event_reminder_dedup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_reminder_sends');
    }
};
