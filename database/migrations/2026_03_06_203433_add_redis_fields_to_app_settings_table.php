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
            $table->string('redis_host')->default('127.0.0.1')->after('use_default_robots_txt');
            $table->integer('redis_port')->default(6379)->after('redis_host');
            $table->string('redis_password')->nullable()->after('redis_port');
            $table->integer('redis_db')->default(0)->after('redis_password');
            $table->integer('redis_cache_db')->default(1)->after('redis_db');
            $table->integer('redis_session_db')->default(2)->after('redis_cache_db');
            $table->integer('redis_queue_db')->default(3)->after('redis_session_db');
            $table->string('redis_prefix')->default('laravel_')->after('redis_queue_db');
            $table->boolean('redis_cache_enabled')->default(true)->after('redis_prefix');
            $table->boolean('redis_session_enabled')->default(true)->after('redis_cache_enabled');
            $table->boolean('redis_queue_enabled')->default(true)->after('redis_session_enabled');
            $table->integer('redis_cache_ttl')->default(3600)->after('redis_queue_enabled');
            $table->integer('redis_session_ttl')->default(120)->after('redis_cache_ttl');
            $table->string('redis_client')->default('phpredis')->after('redis_session_ttl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn([
                'redis_host',
                'redis_port',
                'redis_password',
                'redis_db',
                'redis_cache_db',
                'redis_session_db',
                'redis_queue_db',
                'redis_prefix',
                'redis_cache_enabled',
                'redis_session_enabled',
                'redis_queue_enabled',
                'redis_cache_ttl',
                'redis_session_ttl',
                'redis_client'
            ]);
        });
    }
};
