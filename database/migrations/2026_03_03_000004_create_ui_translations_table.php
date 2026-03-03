<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ui_translations', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('locale', 10);
            $table->longText('value')->nullable();
            $table->timestamps();

            $table->unique(['key', 'locale'], 'ui_translations_key_locale_unique');
            $table->index(['locale', 'key'], 'ui_translations_locale_key_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ui_translations');
    }
};
