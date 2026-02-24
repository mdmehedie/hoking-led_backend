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
            $table->string('frontend_url')->nullable();
            $table->string('blog_prefix')->default('/blog/');
            $table->string('news_prefix')->default('/news/');
            $table->string('page_prefix')->default('/pages/');
            $table->string('case_study_prefix')->default('/case-studies/');
            $table->string('product_prefix')->default('/products/');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['frontend_url', 'blog_prefix', 'news_prefix', 'page_prefix', 'case_study_prefix', 'product_prefix']);
        });
    }
};
