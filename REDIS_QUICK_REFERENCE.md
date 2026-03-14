# Redis Quick Reference Guide

## 🚀 Quick Setup

### **1. Admin Panel Configuration**
- Go to **Settings → App Settings → Redis Configuration**
- Set host: `127.0.0.1`, port: `6379`
- Enable cache, sessions, queue as needed
- Save settings

### **2. Test Connection**
```bash
php artisan tinker
>>> \App\Services\RedisConfigService::testConnection();
```

## 💻 Common Code Patterns

### **Cache with Tags**
```php
// Store with tags
Cache::tags(['users', 'profile'])->remember('user_1', 3600, function () {
    return User::find(1);
});

// Clear by tag
Cache::tags(['users'])->flush();
```

### **Queue Jobs**
```php
// Dispatch job
dispatch(new ProcessDataJob($data));

// With delay
dispatch(new ProcessDataJob($data))->delay(5);
```

### **Direct Redis**
```php
use Illuminate\Support\Facades\Redis;

// Basic operations
Redis::set('key', 'value');
$value = Redis::get('key');

// List operations
Redis::lpush('list', 'item1', 'item2');
$items = Redis::lrange('list', 0, -1);
```

## 🔧 Essential Commands

### **Queue Management**
```bash
php artisan queue:work --redis
php artisan queue:restart
php artisan queue:failed
php artisan queue:retry all
```

### **Cache Management**
```bash
php artisan cache:clear
php artisan config:clear
php artisan cache:table  # If using database cache
```

### **Redis CLI**
```bash
redis-cli ping
redis-cli info memory
redis-cli monitor
redis-cli flushdb
```

## 📊 Monitoring

### **Admin Panel**
- **Redis Stats Page**: View memory, hit rates, connections
- **App Settings**: Configure Redis parameters

### **API Endpoints**
- `GET /admin/redis/config` - Current configuration
- `POST /admin/redis/test-connection` - Test connection
- `GET /admin/redis/server-info` - Server information

## 🚨 Troubleshooting

### **Connection Issues**
```bash
# Check Redis status
sudo systemctl status redis-server

# Start Redis
sudo systemctl start redis-server

# Test connection
redis-cli ping
```

### **Clear Cache**
```bash
# Clear all cache
php artisan cache:clear

# Clear Redis cache
redis-cli flushdb

# Clear configuration cache
\App\Services\RedisConfigService::clearConfigCache();
```

### **Queue Issues**
```bash
# Restart workers
php artisan queue:restart

# Check failed jobs
php artisan queue:failed

# Monitor queue
php artisan queue:work --redis --timeout=60
```

## 📋 Configuration Examples

### **Environment Variables**
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
```

### **Job Example**
```php
class ProcessDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $tries = 3;
    public $backoff = [30, 60, 120];
    
    public function handle(): void
    {
        // Your logic here
    }
}
```

### **Service Provider Setup**
```php
// In AppServiceProvider
public function boot(): void
{
    // Update Redis config from database
    \App\Services\RedisConfigService::updateLaravelConfig();
}
```

## 🎯 Best Practices

1. **Use descriptive cache keys with prefixes**
2. **Implement cache tags for easy invalidation**
3. **Set appropriate TTL values**
4. **Monitor Redis memory usage**
5. **Handle queue job failures gracefully**
6. **Use Redis authentication in production**
7. **Set up health checks and monitoring**
8. **Document your cache strategy**

## 📞 Support

- **Documentation**: `REDIS_BACKEND_DOCUMENTATION.md`
- **Admin Panel**: Settings → App Settings → Redis Configuration
- **Monitoring**: Admin Panel → Redis Stats
- **Logs**: `storage/logs/laravel.log`
