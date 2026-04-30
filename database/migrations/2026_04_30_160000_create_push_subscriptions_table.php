<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('webpush.table_name'), function (Blueprint $table) {
            // bigIncrements matches the upstream package's PushSubscription model.
            // Only the subscribable_id needs UUID width — that's the FK we cross-reference.
            $table->bigIncrements('id');
            $table->uuidMorphs('subscribable', 'push_subscriptions_subscribable_morph_idx');
            $table->string('endpoint', 500)->unique();
            $table->string('public_key')->nullable();
            $table->string('auth_token')->nullable();
            $table->string('content_encoding')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('webpush.table_name'));
    }
};
