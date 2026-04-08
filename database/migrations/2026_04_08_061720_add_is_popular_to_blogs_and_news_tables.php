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
            $table->boolean('is_popular')->default(false)->after('status');
            $table->index('is_popular');
        });

        Schema::table('news', function (Blueprint $table) {
            $table->boolean('is_popular')->default(false)->after('status');
            $table->index('is_popular');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropIndex(['is_popular']);
            $table->dropColumn('is_popular');
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex(['is_popular']);
            $table->dropColumn('is_popular');
        });
    }
};
