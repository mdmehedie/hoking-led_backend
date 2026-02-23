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
        Schema::table('sliders', function (Blueprint $table) {
            $table->enum('media_type', ['image', 'gif', 'video_url', 'video_file'])->default('image');
            $table->string('video_url')->nullable();
            $table->string('video_file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'video_url', 'video_file']);
        });
    }
};
