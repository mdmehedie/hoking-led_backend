<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->uuid('session_id')->unique();
            $table->string('visitor_name');
            $table->string('visitor_email');
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->string('company_name')->nullable();
            $table->string('status')->default('new');
            $table->string('priority')->default('medium');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('last_visitor_message_at')->nullable();
            $table->timestamp('last_admin_message_at')->nullable();
            $table->timestamp('admin_read_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['status', 'last_visitor_message_at']);
            $table->index(['session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
