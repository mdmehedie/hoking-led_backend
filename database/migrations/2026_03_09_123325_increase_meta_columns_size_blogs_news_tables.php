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
        Schema::table('blogs', function (Blueprint $table) {
            $table->json('title')->change();
            $table->json('excerpt')->change();
            $table->json('content')->change();
            $table->json('image_path')->change();
            $table->json('meta_title')->change();
            $table->json('meta_description')->change();
            $table->json('meta_keywords')->change();
        });

        Schema::table('news', function (Blueprint $table) {
            $table->json('title')->change();
            $table->json('excerpt')->change();
            $table->json('content')->change();
            $table->json('image_path')->change();
            $table->json('meta_title')->change();
            $table->json('meta_description')->change();
            $table->json('meta_keywords')->change();
        });

        Schema::table('case_studies', function (Blueprint $table) {
            $table->json('title')->change();
            $table->json('excerpt')->change();
            $table->json('content')->change();
            $table->json('image_path')->change();
            $table->json('meta_title')->change();
            $table->json('meta_description')->change();
            $table->json('meta_keywords')->change();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->json('title')->change();
            $table->json('excerpt')->change();
            $table->json('content')->change();
            $table->json('image_path')->change();
            $table->json('meta_title')->change();
            $table->json('meta_description')->change();
            $table->json('meta_keywords')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('title')->change();
            $table->text('excerpt')->change();
            $table->longText('content')->change();
            $table->string('image_path')->change();
            $table->string('meta_title')->change();
            $table->text('meta_description')->change();
            $table->text('meta_keywords')->change();
        });

        Schema::table('news', function (Blueprint $table) {
            $table->string('title')->change();
            $table->text('excerpt')->change();
            $table->longText('content')->change();
            $table->string('image_path')->change();
            $table->string('meta_title')->change();
            $table->text('meta_description')->change();
            $table->text('meta_keywords')->change();
        });

        Schema::table('case_studies', function (Blueprint $table) {
            $table->string('title')->change();
            $table->text('excerpt')->change();
            $table->longText('content')->change();
            $table->string('image_path')->change();
            $table->string('meta_title')->change();
            $table->text('meta_description')->change();
            $table->text('meta_keywords')->change();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->string('title')->change();
            $table->text('excerpt')->change();
            $table->longText('content')->change();
            $table->string('image_path')->change();
            $table->string('meta_title')->change();
            $table->text('meta_description')->change();
            $table->text('meta_keywords')->change();
        });
    }
};
