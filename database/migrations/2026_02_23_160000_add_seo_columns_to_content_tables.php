<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['products', 'categories', 'blogs', 'case_studies', 'news', 'pages'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'meta_title')) {
                    $table->string('meta_title')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'meta_description')) {
                    $table->text('meta_description')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'meta_keywords')) {
                    $table->text('meta_keywords')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'canonical_url')) {
                    $table->string('canonical_url')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        $tables = ['products', 'categories', 'blogs', 'case_studies', 'news', 'pages'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $columnsToDrop = [];
                if (Schema::hasColumn($tableName, 'meta_title')) {
                    $columnsToDrop[] = 'meta_title';
                }
                if (Schema::hasColumn($tableName, 'meta_description')) {
                    $columnsToDrop[] = 'meta_description';
                }
                if (Schema::hasColumn($tableName, 'meta_keywords')) {
                    $columnsToDrop[] = 'meta_keywords';
                }
                if (Schema::hasColumn($tableName, 'canonical_url')) {
                    $columnsToDrop[] = 'canonical_url';
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};
