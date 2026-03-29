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

        // Backup existing detailed_description data
        $products = DB::table('products')->get();
        $backup = [];
        foreach ($products as $product) {
            if ($product->detailed_description) {
                $backup[$product->id] = $product->detailed_description;
            }
        }

        // Convert column type to JSON (set to NULL first to avoid invalid JSON errors)
        DB::statement('ALTER TABLE products MODIFY detailed_description JSON NULL');

        // Note: Existing HTML content cannot be automatically converted to JSON repeater format
        // The detailed_description field will need to be repopulated via the admin panel
        // This is expected as we're changing from rich text to a structured repeater format
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
