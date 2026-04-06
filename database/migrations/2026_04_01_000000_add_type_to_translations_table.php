<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('translations')) {
            return;
        }

        if (!Schema::hasColumn('translations', 'type')) {
            Schema::table('translations', function (Blueprint $table): void {
                $table->string('type', 20)->default('string')->after('attribute');
            });
        }

        // Update existing records to have proper type based on value content
        DB::table('translations')
            ->whereNull('type')
            ->orWhere('type', '')
            ->update(['type' => 'string']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('translations')) {
            Schema::table('translations', function (Blueprint $table): void {
                $table->dropColumn('type');
            });
        }
    }
};
