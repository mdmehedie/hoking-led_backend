# Redis Backend Documentation

## 📋 Table of Contents
1. [Overview](#overview)
2. [Configuration](#configuration)
3. [Usage Examples](#usage-examples)
4. [Cache Management](#cache-management)
5. [Queue Management](#queue-management)
6. [Session Management](#session-management)
7. [API Endpoints](#api-endpoints)
8. [Monitoring](#monitoring)
9. [Troubleshooting](#troubleshooting)
10. [Best Practices](#best-practices)

## 🎯 Overview

This application uses Redis for caching, session storage, and queue management. Redis configuration is managed through the admin panel and automatically applied to the Laravel application.

### **Key Components**
- **RedisConfigService**: Manages Redis configuration from database
- **Redis Stats Page**: Monitoring dashboard in Filament admin
- **Admin Settings**: Redis configuration through App Settings
- **API Endpoints**: RESTful endpoints for Redis management

## ⚙️ Configuration

### **Admin Panel Configuration**
1. Navigate to **Settings → App Settings → Redis Configuration**
2. Configure the following settings:

| Setting | Description | Default |
|---------|-------------|---------|
| Redis Host | Redis server hostname/IP | `127.0.0.1` |
| Redis Port | Redis server port | `6379` |
| Redis Password | Authentication password | `null` |
| Redis Client | Client implementation | `phpredis` |
| Default DB | Default Redis database | `0` |
| Cache DB | Cache database | `1` |
| Session DB | Session database | `2` |
| Queue DB | Queue database | `3` |
| Key Prefix | Redis key prefix | `laravel_` |
| Cache TTL | Cache expiration (seconds) | `3600` |
| Session TTL | Session expiration (minutes) | `120` |

### **Environment Variables (Fallback)**
```env
# Redis Configuration
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
REDIS_SESSION_DB=2
REDIS_QUEUE_DB=3
REDIS_PREFIX=laravel_

# Laravel Drivers
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## 💻 Usage Examples

### **Basic Cache Operations**

```php
use Illuminate\Support\Facades\Cache;

// Store data with tags
Cache::tags(['users', 'profile'])->remember('user_1_profile', 3600, function () {
    return User::find(1);
});

// Store simple value
Cache::put('site_visits', 100, 3600);

// Get value
$visits = Cache::get('site_visits');

// Check existence
if (Cache::has('site_visits')) {
    // Value exists
}

// Delete specific key
Cache::forget('site_visits');

// Clear by tags
Cache::tags(['users'])->flush();
```

### **Session Operations**

```php
// Session data is automatically stored in Redis
session(['key' => 'value']);

// Get session data
$value = session('key');

// Flash data (available for next request)
session()->flash('message', 'Operation successful');
```

### **Queue Operations**

```php
// Dispatch job to Redis queue
dispatch(new ProcessDataJob($data));

// Dispatch with delay
dispatch(new ProcessDataJob($data))->delay(now()->addMinutes(5));

// Chain jobs
dispatch(new FirstJob())->chain([
    new SecondJob(),
    new ThirdJob(),
]);
```

### **Direct Redis Operations**

```php
use Illuminate\Support\Facades\Redis;

// Direct Redis connection
$redis = Redis::connection();

// Basic operations
$redis->set('key', 'value');
$value = $redis->get('key');
$redis->del('key');

// List operations
$redis->lpush('mylist', 'item1', 'item2');
$items = $redis->lrange('mylist', 0, -1);

// Hash operations
$redis->hset('user:1', 'name', 'John');
$name = $redis->hget('user:1', 'name');
$userData = $redis->hgetall('user:1');
```

## 🗂️ Cache Management

### **Cache Tags Strategy**
```php
// User-related cache
Cache::tags(['users', 'user_' . $userId])->remember('user_profile_' . $userId, 3600, function () {
    return User::find($userId);
});

// Product-related cache
Cache::tags(['products', 'category_' . $categoryId])->remember('products_list_' . $categoryId, 7200, function () {
    return Product::where('category_id', $categoryId)->get();
});

// Analytics cache
Cache::tags(['analytics', 'daily'])->remember('page_views_' . now()->format('Y-m-d'), 86400, function () {
    return Analytics::getPageViews();
});
```

### **Cache Invalidation**
```php
// Clear all user cache
Cache::tags(['users'])->flush();

// Clear specific user cache
Cache::tags(['users', 'user_' . $userId])->flush();

// Clear analytics cache
Cache::tags(['analytics'])->flush();
```

### **Helper Functions**
```php
// In app/Helpers/cache.php
if (!function_exists('cached')) {
    function cached($key, $ttl, $callback, $tags = []) {
        if (!empty($tags)) {
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        }
        return Cache::remember($key, $ttl, $callback);
    }
}

// Usage
$user = cached('user_1', 3600, function() {
    return User::find(1);
}, ['users']);
```

## 📋 Queue Management

### **Job Classes**
```php
<?php
// app/Jobs/ProcessDataJob.php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 60, 120];
    public $timeout = 300;

    public function __construct(
        private array $data
    ) {}

    public function handle(): void
    {
        // Process the data
        Log::info('Processing data', ['data' => $this->data]);
        
        // Your business logic here
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Job failed', ['error' => $exception->getMessage()]);
    }
}
```

### **Queue Commands**
```bash
# Start queue worker
php artisan queue:work --redis

# Start with specific connection
php artisan queue:work --redis --queue=default,high,low

# Start with delay
php artisan queue:work --redis --sleep=3 --tries=3

# Stop all workers
php artisan queue:restart

# List failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush-failed
```

### **Queue Configuration**
```php
// config/queue.php
'redis' => [
    'driver' => 'redis',
    'connection' => 'default',
    'queue' => env('REDIS_QUEUE', 'default'),
    'retry_after' => 90,
    'block_for' => null,
    'after_commit' => false,
],
```

## 🔐 Session Management

### **Session Configuration**
```php
// config/session.php
'driver' => env('SESSION_DRIVER', 'redis'),
'connection' => 'default',
'table' => 'sessions',
'lifetime' => env('SESSION_LIFETIME', 120),
'expire_on_close' => false,
'encrypt' => false,
'files' => storage_path('framework/sessions'),
'store' => null,
'lottery' => [2, 100],
'cookie' => env('SESSION_COOKIE', Str::slug(env('APP_NAME', 'laravel'), '_').'_session'),
'path' => '/',
'domain' => env('SESSION_DOMAIN'),
'secure' => env('SESSION_SECURE_COOKIE'),
'http_only' => true,
'same_site' => 'lax',
```

### **Session Operations**
```php
// Store session data
session(['user_id' => $user->id, 'role' => $user->role]);

// Flash data
session()->flash('success', 'Operation completed successfully');

// Keep flash data for additional request
session()->reflash();

// Get session data
$userId = session('user_id');

// Check session existence
if (session()->has('user_id')) {
    // User is logged in
}

// Forget session data
session()->forget('user_id');

// Clear all session data
session()->flush();
```

## 🌐 API Endpoints

### **Redis Configuration API**
```php
// GET /admin/redis/config
// Get current Redis configuration
Response: {
    "success": true,
    "config": {
        "host": "127.0.0.1",
        "port": 6379,
        "password": "***",
        "database": 0,
        "cache_enabled": true,
        "session_enabled": true,
        "queue_enabled": true
    }
}

// POST /admin/redis/test-connection
// Test Redis connection
Response: {
    "success": true,
    "message": "Redis connection successful",
    "ping_result": "PONG",
    "cache_test": true
}

// GET /admin/redis/server-info
// Get Redis server information
Response: {
    "success": true,
    "info": {...},
    "version": "7.0.0",
    "uptime": 86400,
    "connected_clients": 5,
    "used_memory": "2.5M"
}

// POST /admin/redis/clear-cache
// Clear Redis configuration cache
Response: {
    "success": true,
    "message": "Redis configuration cache cleared successfully"
}
```

### **JavaScript Usage**
```javascript
// Test Redis connection
async function testRedisConnection() {
    try {
        const response = await fetch('/admin/redis/test-connection', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();
        
        if (data.success) {
            console.log('Redis connection successful');
        } else {
            console.error('Redis connection failed:', data.message);
        }
    } catch (error) {
        console.error('Error testing Redis connection:', error);
    }
}
```

## 📊 Monitoring

### **Redis Stats Dashboard**
Access via: **Admin Panel → Redis Stats**

**Metrics Available:**
- Memory usage and percentage
- Operations per second
- Total keys and connections
- Cache hit rate
- Database information per DB
- Network input/output statistics

### **Custom Monitoring**
```php
// app/Services/RedisMonitoringService.php
class RedisMonitoringService
{
    public static function getMemoryUsage(): array
    {
        $info = Redis::info('memory');
        return [
            'used' => $info['used_memory_human'],
            'peak' => $info['used_memory_peak_human'],
            'percentage' => self::calculateMemoryPercentage($info)
        ];
    }
    
    public static function getPerformanceMetrics(): array
    {
        $info = Redis::info('stats');
        return [
            'ops_per_sec' => $info['instantaneous_ops_per_sec'],
            'hit_rate' => self::calculateHitRate($info),
            'connected_clients' => $info['connected_clients']
        ];
    }
}
```

### **Health Checks**
```php
// app/Http/Controllers/HealthController.php
public function redisHealth(): JsonResponse
{
    try {
        $redis = Redis::connection();
        $ping = $redis->ping();
        
        if ($ping === 'PONG') {
            return response()->json([
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'memory_usage' => Redis::info('memory')['used_memory_human']
            ]);
        }
        
        return response()->json([
            'status' => 'unhealthy',
            'error' => 'Redis ping failed'
        ], 503);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'error' => $e->getMessage()
        ], 503);
    }
}
```

## 🔧 Troubleshooting

### **Common Issues**

#### **1. Connection Refused**
```bash
# Check Redis status
sudo systemctl status redis-server

# Start Redis
sudo systemctl start redis-server

# Check port
netstat -tlnp | grep 6379
```

#### **2. Authentication Failed**
```bash
# Test with password
redis-cli -a yourpassword ping

# Check Redis config
redis-cli config get requirepass
```

#### **3. Memory Issues**
```bash
# Check memory usage
redis-cli info memory | grep used_memory

# Clear cache
redis-cli flushdb

# Check maxmemory setting
redis-cli config get maxmemory
```

#### **4. Queue Jobs Not Processing**
```bash
# Check queue status
php artisan queue:failed

# Restart workers
php artisan queue:restart

# Monitor queue
php artisan queue:work --redis --timeout=60
```

### **Debugging Tools**
```php
// Debug Redis connection
try {
    $redis = Redis::connection();
    $ping = $redis->ping();
    Log::info('Redis ping result: ' . $ping);
} catch (\Exception $e) {
    Log::error('Redis connection failed: ' . $e->getMessage());
}

// Debug cache operations
Cache::put('test_key', 'test_value', 60);
$retrieved = Cache::get('test_key');
Log::info('Cache test: ' . ($retrieved === 'test_value' ? 'PASS' : 'FAIL'));
```

### **Performance Optimization**
```php
// Use pipeline for multiple operations
$redis = Redis::connection();
$redis->pipeline(function ($pipe) {
    for ($i = 0; $i < 100; $i++) {
        $pipe->set("key:$i", "value:$i");
    }
});

// Use transactions
$redis->multi();
$redis->set('key1', 'value1');
$redis->set('key2', 'value2');
$redis->exec();
```

## 🎯 Best Practices

### **1. Cache Strategy**
- Use descriptive key names with prefixes
- Implement proper cache invalidation with tags
- Set appropriate TTL values
- Cache expensive database queries and API calls

### **2. Queue Management**
- Implement proper error handling in jobs
- Use job chaining for complex workflows
- Monitor failed jobs regularly
- Implement job retry logic

### **3. Session Security**
- Use secure session cookies in production
- Set appropriate session lifetime
- Implement session fixation protection
- Use HTTPS for session cookies

### **4. Performance**
- Use Redis pipelining for bulk operations
- Monitor memory usage and set limits
- Use appropriate data structures (lists, sets, hashes)
- Implement proper key expiration policies

### **5. Monitoring**
- Set up alerts for Redis downtime
- Monitor memory usage and hit rates
- Track queue processing times
- Implement health checks

### **6. Security**
- Use Redis authentication in production
- Bind Redis to localhost only
- Use firewall rules to restrict access
- Regularly update Redis version

## 📚 Additional Resources

- [Redis Documentation](https://redis.io/documentation)
- [Laravel Redis Documentation](https://laravel.com/docs/redis)
- [Laravel Cache Documentation](https://laravel.com/docs/cache)
- [Laravel Queue Documentation](https://laravel.com/docs/queues)

## 🚀 Quick Start Checklist

- [ ] Configure Redis in admin panel
- [ ] Test Redis connection
- [ ] Set up cache tags strategy
- [ ] Configure queue workers
- [ ] Set up monitoring
- [ ] Implement health checks
- [ ] Document your cache keys
- [ ] Set up backup strategy

---

**Last Updated**: March 7, 2026  
**Version**: 1.0.0  
**Maintainer**: Development Team
