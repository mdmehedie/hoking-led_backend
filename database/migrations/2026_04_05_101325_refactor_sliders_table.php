<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('sliders');

        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('primary_button_text')->nullable();
            $table->string('primary_button_link')->nullable();
            $table->string('background_image')->nullable();
            $table->string('foreground_image')->nullable();
            $table->string('label')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['status', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sliders');

        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('link')->nullable();
            $table->string('alt_text')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->json('custom_styles')->nullable();
            $table->enum('media_type', ['image', 'gif', 'video_url', 'video_file'])->default('image');
            $table->string('video_url')->nullable();
            $table->string('video_file')->nullable();
            $table->timestamps();
        });
    }
};
