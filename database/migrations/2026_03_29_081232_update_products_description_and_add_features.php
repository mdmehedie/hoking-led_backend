<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new features column
        Schema::table('products', function (Blueprint $table) {
            $table->json('features')->nullable()->after('technical_specs');
        });

        // Clear old HTML content (can't be auto-converted to JSON repeater format)
        DB::table('products')->whereNotNull('detailed_description')->update(['detailed_description' => null]);

        // Convert column type to JSON
        DB::statement('ALTER TABLE products MODIFY detailed_description JSON NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to LONGTEXT
        DB::statement('ALTER TABLE products MODIFY detailed_description LONGTEXT NULL');

        // Drop features column
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('features');
        });
    }
};
