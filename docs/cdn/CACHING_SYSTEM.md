# Caching System Guide

## 📋 Overview

This guide covers the comprehensive caching system implementation for optimal performance, including HTTP caching headers, response caching, and cache management.

## 🚀 Features Overview

### **HTTP Caching Headers**
- Automatic asset caching with proper headers
- ETag support for conditional requests
- Different cache durations for different asset types
- Browser cache optimization

### **Response Caching**
- Intelligent page caching based on content type
- Redis-backed caching for performance
- Automatic cache invalidation
- Cache bypass support

### **Cache Management**
- Filament interface for manual cache operations
- Cache statistics and monitoring
- Bulk cache clearing operations
- Performance metrics

## 🔧 HTTP Caching Implementation

### CacheAssets Middleware

The `CacheAssets` middleware automatically adds caching headers to static assets:

```php
// app/Http/Middleware/CacheAssets.php
class CacheAssets
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->isAsset($request)) {
            $maxAge = $this->getCacheDuration($request);
            
            $response->headers->set('Cache-Control', "public, max-age={$maxAge}, immutable");
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + $maxAge));
            
            // Add ETag for better caching
            $etag = md5($response->getContent());
            $response->headers->set('ETag', "\"{$etag}\"");
            
            // Check if client has cached version
            if ($request->headers->get('If-None-Match') === "\"{$etag}\"") {
                return response('', 304);
            }
        }

        return $response;
    }
}
```

### Cache Duration Rules

Different asset types get different cache durations:

```php
private function getCacheDuration(Request $request): int
{
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    $durations = [
        'css' => 31536000,  // 1 year
        'js' => 31536000,   // 1 year
        'png' => 2592000,   // 30 days
        'jpg' => 2592000,   // 30 days
        'jpeg' => 2592000,  // 30 days
        'gif' => 2592000,   // 30 days
        'svg' => 2592000,   // 30 days
        'ico' => 2592000,   // 30 days
        'woff' => 31536000, // 1 year
        'woff2' => 31536000, // 1 year
        'webp' => 2592000,  // 30 days
        'avif' => 2592000,  // 30 days
    ];

    return $durations[$extension] ?? 86400; // Default 1 day
}
```

## 🗄️ Response Caching System

### Cache Profile Configuration

The `CachePublicPages` profile determines which requests to cache:

```php
// app/Http/CacheProfiles/CachePublicPages.php
class CachePublicPages extends BaseCacheProfile
{
    public function shouldCacheRequest(Request $request): bool
    {
        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return false;
        }

        // Don't cache authenticated users (except public pages)
        if (auth()->check() && !$this->isPublicPageForAuthenticatedUsers($request)) {
            return false;
        }

        // Don't cache if there are flash messages
        if (session()->has('flash')) {
            return false;
        }

        // Don't cache admin panel
        if ($request->is('admin/*') || $request->is('filament/*')) {
            return false;
        }

        // Don't cache API routes
        if ($request->is('api/*')) {
            return false;
        }

        return true;
    }
}
```

### Cache Duration Strategy

Different pages get different cache durations:

```php
public function cacheTimeInSeconds(Request $request): int
{
    // Longer cache for static pages
    if ($this->isStaticPage($request)) {
        return 60 * 60 * 24; // 24 hours
    }

    // Medium cache for dynamic pages
    if ($this->isDynamicPage($request)) {
        return 60 * 60 * 6; // 6 hours
    }

    // Short cache for other pages
    return 60 * 60; // 1 hour
}
```

### Cache Key Generation

Cache keys include locale and user type:

```php
public function cacheName(Request $request): string
{
    $key = 'response_cache_' . $request->fullUrl();
    
    // Include locale in cache key
    $key .= '_locale_' . app()->getLocale();
    
    // Include user type (guest/authenticated)
    $key .= '_user_' . (auth()->check() ? 'auth' : 'guest');
    
    return $key;
}
```

## 🎛️ Cache Management Interface

### Filament Cache Management Page

The Cache Management page provides:

- **Cache Statistics**: Redis status, memory usage, hit rates
- **Cache Operations**: Clear different types of cache
- **Performance Monitoring**: Real-time metrics
- **Configuration Display**: Current cache settings

### Available Operations

```php
public function clearCache(array $data): void
{
    $operations = $data['operations'];
    $results = [];

    foreach ($operations as $operation) {
        switch ($operation) {
            case 'application':
                Artisan::call('cache:clear');
                $results[] = 'Application cache cleared successfully';
                break;

            case 'response':
                ResponseCache::clear();
                $results[] = 'Response cache cleared successfully';
                break;

            case 'config':
                Artisan::call('config:clear');
                $results[] = 'Configuration cache cleared successfully';
                break;

            case 'routes':
                Artisan::call('route:clear');
                $results[] = 'Route cache cleared successfully';
                break;

            case 'views':
                Artisan::call('view:clear');
                $results[] = 'View cache cleared successfully';
                break;

            case 'redis':
                if (config('cache.default') === 'redis') {
                    Cache::store('redis')->flush();
                    $results[] = 'Redis cache cleared successfully';
                }
                break;
        }
    }
}
```

### Cache Statistics

Real-time cache statistics:

```php
public function getCacheStatistics(): array
{
    $stats = [];

    if (config('cache.default') === 'redis') {
        $redis = Cache::store('redis');
        $info = $redis->connection()->info();
        
        $stats = [
            'redis_version' => $info['redis_version'] ?? 'Unknown',
            'connected_clients' => $info['connected_clients'] ?? 0,
            'total_commands_processed' => $info['total_commands_processed'] ?? 0,
            'keyspace_hits' => $info['keyspace_hits'] ?? 0,
            'keyspace_misses' => $info['keyspace_misses'] ?? 0,
            'hit_rate' => $this->calculateHitRate($info),
        ];
    }

    return $stats;
}
```

## 🚀 Usage Examples

### Basic Cache Clearing

```bash
# Clear all cache
php artisan cache:clear

# Clear response cache
php artisan responsecache:clear

# Clear specific cache store
php artisan cache:clear --store=redis
```

### Programmatic Cache Operations

```php
// Clear response cache
ResponseCache::clear();

// Clear specific cache tags
Cache::tags(['response_cache', 'public_pages'])->flush();

// Clear cache by pattern
Cache::forget('cache_key_here');

// Store data with tags
Cache::tags(['analytics', 'ga4'])->put('ga4_metrics', $data, 3600);
```

### Cache Bypass

```php
// Add bypass header to skip cache
$request->headers->set('X-Cache-Bypass', '1');

// Or use query parameter
$url = 'https://example.com/page?no-cache=1';
```

## 📊 Cache Configuration

### Response Cache Configuration

```php
// config/responsecache.php
return [
    'enabled' => env('RESPONSE_CACHE_ENABLED', true),
    'cache_profile' => \App\Http\CacheProfiles\CachePublicPages::class,
    'cache_lifetime_in_seconds' => (int) env('RESPONSE_CACHE_LIFETIME', 86400),
    'cache_store' => env('RESPONSE_CACHE_DRIVER', 'redis'),
    'cache_tag' => ['response_cache', 'public_pages'],
    'cache_bypass_header' => [
        'name' => env('CACHE_BYPASS_HEADER_NAME', 'X-Cache-Bypass'),
        'value' => env('CACHE_BYPASS_HEADER_VALUE', '1'),
    ],
];
```

### Environment Variables

```env
# Response Caching
RESPONSE_CACHE_ENABLED=true
RESPONSE_CACHE_DRIVER=redis
RESPONSE_CACHE_LIFETIME=86400
CACHE_BYPASS_HEADER_NAME=X-Cache-Bypass
CACHE_BYPASS_HEADER_VALUE=1
RESPONSE_CACHE_HEADER_NAME=laravel-responsecache
RESPONSE_CACHE_AGE_HEADER=false
```

## 🔧 Advanced Configuration

### Custom Cache Replacers

Add custom content replacers for dynamic content:

```php
// config/responsecache.php
'replacers' => [
    \Spatie\ResponseCache\Replacers\CsrfTokenReplacer::class,
    \App\Http\Replacers\UserDataReplacer::class,
    \App\Http\Replacers\DateTimeReplacer::class,
],
```

### Custom Replacer Example

```php
<?php

namespace App\Http\Replacers;

use Spatie\ResponseCache\Replacers\Replacer;
use Illuminate\Http\Request;

class UserDataReplacer implements Replacer
{
    public function prepareResponseToCache(Request $request, $response)
    {
        if (auth()->check()) {
            $content = $response->getContent();
            $content = str_replace(
                auth()->user()->name,
                '{{user_name}}',
                $content
            );
            $response->setContent($content);
        }

        return $response;
    }

    public function replaceInCachedResponse(Request $request, $response)
    {
        if (auth()->check()) {
            $content = $response->getContent();
            $content = str_replace(
                '{{user_name}}',
                auth()->user()->name,
                $content
            );
            $response->setContent($content);
        }

        return $response;
    }
}
```

## 📈 Performance Monitoring

### Cache Hit Rate Monitoring

```php
// Monitor cache performance
$hitRate = $this->calculateHitRate($redisInfo);

if ($hitRate < 80) {
    logger()->warning('Low cache hit rate', [
        'hit_rate' => $hitRate,
        'hits' => $hits,
        'misses' => $misses
    ]);
}
```

### Memory Usage Monitoring

```php
// Monitor Redis memory usage
$memoryUsage = $redis->connection()->info('memory');
$usedMemory = $memoryUsage['used_memory'] ?? 0;
$maxMemory = $memoryUsage['maxmemory'] ?? 0;

$memoryUsagePercent = ($usedMemory / $maxMemory) * 100;

if ($memoryUsagePercent > 80) {
    logger()->warning('High Redis memory usage', [
        'used' => $usedMemory,
        'max' => $maxMemory,
        'percent' => $memoryUsagePercent
    ]);
}
```

## 🛠️ Troubleshooting

### Common Issues

#### **Cache Not Working**
```bash
# Check cache configuration
php artisan config:cache

# Verify Redis connection
php artisan tinker
>>> Cache::store('redis')->ping()

# Check response cache status
php artisan tinker
>>> config('responsecache.enabled')
```

#### **High Memory Usage**
```bash
# Check Redis memory
redis-cli info memory

# Clear specific keys
redis-cli --scan --pattern "response_cache_*" | xargs redis-cli del

# Monitor memory usage
redis-cli monitor
```

#### **Cache Invalidation Issues**
```php
// Clear cache manually
ResponseCache::clear();

// Clear by tags
Cache::tags(['response_cache'])->flush();

// Check cache keys
php artisan tinker
>>> Cache::getStore()->getRedis()->connection()->keys('*')
```

### Debug Tools

```php
// Enable cache debugging
// config/responsecache.php
'add_cache_time_header' => true,
'add_cache_age_header' => true,

// Check cache headers
curl -I https://example.com/page

// Look for:
// laravel-responsecache: cached_at_timestamp
// laravel-responsecache-age: cache_age_in_seconds
```

## 📚 Best Practices

### **Cache Strategy**
1. **Static Content**: Long cache times (1 year)
2. **Dynamic Content**: Short cache times (1-6 hours)
3. **User-specific Content**: No caching
4. **Admin Content**: No caching

### **Cache Keys**
1. **Include locale** for multilingual sites
2. **Include user type** for guest/authenticated
3. **Include query parameters** for filtered content
4. **Use versioning** for cache busting

### **Cache Invalidation**
1. **Automatic**: Use model events
2. **Manual**: Use cache management interface
3. **Scheduled**: Use Laravel scheduler
4. **Conditional**: Use cache tags

### **Performance**
1. **Use Redis** for best performance
2. **Monitor hit rates** (>80% is good)
3. **Set appropriate TTLs** based on content
4. **Use compression** for large responses

## 🎯 Optimization Tips

### **Database Query Caching**
```php
// Cache expensive queries
$results = Cache::remember('expensive_query', 3600, function () {
    return DB::table('large_table')->complexQuery()->get();
});
```

### **API Response Caching**
```php
// Cache API responses
$cacheKey = 'api_response_' . md5($request->fullUrl());
$response = Cache::remember($cacheKey, 300, function () use ($request) {
    return $this->processApiRequest($request);
});
```

### **Partial Caching**
```php
// Cache view fragments
$cachedFragment = Cache::remember('fragment_' . $id, 3600, function () use ($id) {
    return view('partials.expensive', ['id' => $id])->render();
});
```

## 📞 Support

For cache-related issues:
1. Check the **Cache Management** page in Filament
2. Review the **Performance Monitoring** guide
3. Check Laravel documentation on caching
4. Monitor Redis performance metrics

---

*Last updated: March 7, 2025*
