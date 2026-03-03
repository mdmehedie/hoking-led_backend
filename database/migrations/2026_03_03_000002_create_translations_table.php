<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->morphs('translatable');
            $table->string('locale', 10);
            $table->string('attribute', 100);
            $table->longText('value')->nullable();
            $table->timestamps();

            $table->unique(['translatable_id', 'translatable_type', 'locale', 'attribute'], 'translations_unique');
            $table->index(['translatable_type', 'locale', 'attribute'], 'translations_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
