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
            if (!Schema::hasColumn('app_settings', 'ga4_property_id')) {
                $table->string('ga4_property_id')->nullable();
            }
            if (!Schema::hasColumn('app_settings', 'ga4_credentials_file')) {
                $table->string('ga4_credentials_file')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['ga4_property_id', 'ga4_credentials_file']);
        });
    }
};
