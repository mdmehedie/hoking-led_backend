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
        // Use raw SQL to change JSON columns to TEXT
        \DB::statement('ALTER TABLE certification_awards MODIFY COLUMN title TEXT');
        \DB::statement('ALTER TABLE certification_awards MODIFY COLUMN description TEXT');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Change back to JSON columns
        \DB::statement('ALTER TABLE certification_awards MODIFY COLUMN title JSON');
        \DB::statement('ALTER TABLE certification_awards MODIFY COLUMN description JSON');
    }
};
