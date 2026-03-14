<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class RedisConfigService
{
    /**
     * Get Redis configuration from database settings
     */
    public static function getConfig(): array
    {
        $settings = Cache::remember('redis_config', 3600, function () {
            return AppSetting::first();
        });

        if (!$settings) {
            return self::getDefaultConfig();
        }

        return [
            'client' => $settings->redis_client ?? 'phpredis',
            'host' => $settings->redis_host ?? '127.0.0.1',
            'port' => (int) ($settings->redis_port ?? 6379),
            'password' => $settings->redis_password ?? null,
            'database' => (int) ($settings->redis_db ?? 0),
            'cache_db' => (int) ($settings->redis_cache_db ?? 1),
            'session_db' => (int) ($settings->redis_session_db ?? 2),
            'queue_db' => (int) ($settings->redis_queue_db ?? 3),
            'prefix' => $settings->redis_prefix ?? 'laravel_',
            'cache_ttl' => (int) ($settings->redis_cache_ttl ?? 3600),
            'session_ttl' => (int) ($settings->redis_session_ttl ?? 120),
            'cache_enabled' => (bool) ($settings->redis_cache_enabled ?? true),
            'session_enabled' => (bool) ($settings->redis_session_enabled ?? true),
            'queue_enabled' => (bool) ($settings->redis_queue_enabled ?? true),
        ];
    }

    /**
     * Get default Redis configuration
     */
    public static function getDefaultConfig(): array
    {
        return [
            'client' => 'phpredis',
            'host' => '127.0.0.1',
            'port' => 6379,
            'password' => null,
            'database' => 0,
            'cache_db' => 1,
            'session_db' => 2,
            'queue_db' => 3,
            'prefix' => 'laravel_',
            'cache_ttl' => 3600,
            'session_ttl' => 120,
            'cache_enabled' => true,
            'session_enabled' => true,
            'queue_enabled' => true,
        ];
    }

    /**
     * Update Laravel configuration based on database settings
     */
    public static function updateLaravelConfig(): void
    {
        $config = self::getConfig();

        // Update Redis configuration
        config([
            'database.redis.client' => $config['client'],
            'database.redis.default.host' => $config['host'],
            'database.redis.default.port' => $config['port'],
            'database.redis.default.password' => $config['password'],
            'database.redis.default.database' => $config['database'],
            'database.redis.cache.host' => $config['host'],
            'database.redis.cache.port' => $config['port'],
            'database.redis.cache.password' => $config['password'],
            'database.redis.cache.database' => $config['cache_db'],
            'database.redis.prefix' => $config['prefix'],
        ]);

        // Update cache configuration
        if ($config['cache_enabled']) {
            config([
                'cache.default' => 'redis',
                'cache.stores.redis.connection' => 'cache',
                'cache.prefix' => $config['prefix'] . 'cache-',
            ]);
        }

        // Update session configuration
        if ($config['session_enabled']) {
            config([
                'session.driver' => 'redis',
                'session.connection' => 'default',
                'session.lifetime' => $config['session_ttl'],
            ]);
        }

        // Update queue configuration
        if ($config['queue_enabled']) {
            config([
                'queue.default' => 'redis',
                'queue.connections.redis.connection' => 'default',
            ]);
        }
    }

    /**
     * Test Redis connection with current configuration
     */
    public static function testConnection(): array
    {
        try {
            $config = self::getConfig();
            
            // Test basic connection
            $redis = Redis::connection('default');
            $ping = $redis->ping();
            
            // Test cache functionality
            $testKey = 'redis_test_' . time();
            $testValue = 'test_value_' . time();
            
            Cache::store('redis')->put($testKey, $testValue, 60);
            $retrievedValue = Cache::store('redis')->get($testKey);
            Cache::store('redis')->forget($testKey);
            
            $success = $ping === 'PONG' && $retrievedValue === $testValue;
            
            return [
                'success' => $success,
                'message' => $success ? 'Redis connection successful' : 'Redis connection failed',
                'config' => $config,
                'ping_result' => $ping,
                'cache_test' => $retrievedValue === $testValue,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Redis connection error: ' . $e->getMessage(),
                'config' => self::getConfig(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Clear Redis configuration cache
     */
    public static function clearConfigCache(): void
    {
        Cache::forget('redis_config');
        Cache::forget('app_settings');
    }

    /**
     * Get Redis server information
     */
    public static function getServerInfo(): array
    {
        try {
            $info = Redis::info();
            return [
                'success' => true,
                'info' => $info,
                'version' => $info['redis_version'] ?? 'unknown',
                'uptime' => $info['uptime_in_seconds'] ?? 0,
                'connected_clients' => $info['connected_clients'] ?? 0,
                'used_memory' => $info['used_memory_human'] ?? 'unknown',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
