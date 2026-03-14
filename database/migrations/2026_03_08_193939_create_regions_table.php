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
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // e.g., 'us', 'uk', 'eu'
            $table->string('name', 100); // e.g., 'United States', 'United Kingdom'
            $table->string('currency', 3)->nullable(); // e.g., 'USD', 'GBP', 'EUR'
            $table->string('timezone')->nullable(); // e.g., 'America/New_York'
            $table->string('language', 10)->nullable(); // e.g., 'en', 'en-US'
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
