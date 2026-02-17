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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo_light')->nullable();
            $table->string('logo_dark')->nullable();
            $table->string('favicon')->nullable();
            $table->string('primary_color')->default('#3b82f6');
            $table->string('secondary_color');
            $table->string('accent_color');
            $table->string('font_family')->default('Arial');
            $table->string('base_font_size')->default('16px');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
