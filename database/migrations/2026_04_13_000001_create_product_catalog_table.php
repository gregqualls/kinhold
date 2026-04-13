<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_catalog', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->string('category', 100)->nullable();
            $table->timestamps();

            $table->unique('name');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_catalog');
    }
};
