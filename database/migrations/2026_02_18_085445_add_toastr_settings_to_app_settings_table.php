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
            $table->boolean('toastr_enabled')->default(true);
            $table->string('toastr_position')->default('top-right');
            $table->integer('toastr_duration')->default(5000);
            $table->string('toastr_show_method')->default('fadeIn');
            $table->string('toastr_hide_method')->default('fadeOut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['toastr_enabled', 'toastr_position', 'toastr_duration', 'toastr_show_method', 'toastr_hide_method']);
        });
    }
};
