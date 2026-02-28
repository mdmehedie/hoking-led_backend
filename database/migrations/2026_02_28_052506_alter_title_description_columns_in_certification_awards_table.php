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
        // Convert JSON columns to TEXT columns
        // First, add temporary columns
        Schema::table('certification_awards', function (Blueprint $table) {
            $table->text('title_text')->nullable();
            $table->text('description_text')->nullable();
        });

        // Migrate data from JSON to TEXT
        \DB::statement('UPDATE certification_awards SET title_text = JSON_UNQUOTE(JSON_EXTRACT(title, "$.en")) WHERE JSON_VALID(title)');
        \DB::statement('UPDATE certification_awards SET description_text = JSON_UNQUOTE(JSON_EXTRACT(description, "$.en")) WHERE JSON_VALID(description)');

        // Drop old JSON columns
        Schema::table('certification_awards', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });

        // Rename new columns
        Schema::table('certification_awards', function (Blueprint $table) {
            $table->renameColumn('title_text', 'title');
            $table->renameColumn('description_text', 'description');
        });

        // Make title required and description nullable
        Schema::table('certification_awards', function (Blueprint $table) {
            $table->text('title')->nullable(false)->change();
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certification_awards', function (Blueprint $table) {
            //
        });
    }
};
