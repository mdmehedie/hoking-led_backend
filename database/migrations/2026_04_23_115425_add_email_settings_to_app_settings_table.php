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
            // Internal Notification Configuration (Staff)
            $table->boolean('contact_internal_enabled')->default(true);
            $table->json('contact_internal_recipients')->nullable();
            $table->text('contact_internal_subject')->nullable();
            $table->longText('contact_internal_template')->nullable();

            // External Acknowledgement Configuration (Visitor)
            $table->boolean('contact_external_enabled')->default(true);
            $table->text('contact_external_subject')->nullable();
            $table->longText('contact_external_template')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn([
                'contact_internal_enabled',
                'contact_internal_recipients',
                'contact_internal_subject',
                'contact_internal_template',
                'contact_external_enabled',
                'contact_external_subject',
                'contact_external_template'
            ]);
        });
    }
};
