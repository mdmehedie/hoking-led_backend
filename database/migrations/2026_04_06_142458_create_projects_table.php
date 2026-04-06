<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('secondary_title')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->text('description')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('client_name')->nullable();
            $table->string('location')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->date('project_date')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->integer('sort_order')->default(0);
            $table->string('video_url')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
