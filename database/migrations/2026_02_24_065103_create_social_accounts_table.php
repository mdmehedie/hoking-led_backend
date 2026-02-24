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
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('platform'); // facebook, twitter, linkedin
            $table->string('account_name'); // Display name for the account
            $table->json('credentials'); // API keys, tokens, secrets
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['platform', 'account_name']); // Prevent duplicate accounts per platform
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
