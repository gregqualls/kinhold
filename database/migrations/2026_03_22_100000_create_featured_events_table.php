<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('featured_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('family_id');
            $table->uuid('created_by');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->time('event_time')->nullable();
            $table->string('icon', 50)->default("\u{1F389}"); // party popper emoji
            $table->string('color', 7)->default('#8B5CF6'); // wisteria purple
            $table->string('recurrence', 20)->default('none'); // none, yearly, monthly, weekly
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('family_id')->references('id')->on('families')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->index(['family_id', 'event_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('featured_events');
    }
};
