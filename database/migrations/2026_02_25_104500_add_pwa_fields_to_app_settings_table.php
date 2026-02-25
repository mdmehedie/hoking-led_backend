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
        Schema::table('app_settings', function (Blueprint $table) {
            // PWA settings
            $table->boolean('pwa_enabled')->default(false);
            $table->string('pwa_display_mode')->default('standalone');
            $table->string('pwa_orientation')->default('portrait-primary');
            $table->string('pwa_theme_color')->nullable();
            $table->string('pwa_background_color')->nullable();
            $table->string('pwa_icon_72')->nullable();
            $table->string('pwa_icon_96')->nullable();
            $table->string('pwa_icon_128')->nullable();
            $table->string('pwa_icon_144')->nullable();
            $table->string('pwa_icon_192')->nullable();
            $table->string('pwa_icon_512')->nullable();
            $table->string('pwa_short_name')->nullable();
            $table->text('pwa_description')->nullable();
            $table->json('pwa_categories')->nullable();
            $table->string('pwa_start_url')->default('/');
            $table->string('pwa_scope')->default('/');
            $table->string('pwa_lang')->default('en-US');
            $table->string('pwa_dir')->default('ltr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn([
                'pwa_enabled',
                'pwa_display_mode',
                'pwa_orientation',
                'pwa_theme_color',
                'pwa_background_color',
                'pwa_icon_72',
                'pwa_icon_96',
                'pwa_icon_128',
                'pwa_icon_144',
                'pwa_icon_192',
                'pwa_icon_512',
                'pwa_short_name',
                'pwa_description',
                'pwa_categories',
                'pwa_start_url',
                'pwa_scope',
                'pwa_lang',
                'pwa_dir',
            ]);
        });
    }
};
