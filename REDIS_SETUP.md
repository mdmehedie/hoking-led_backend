# Redis Setup Instructions

This document provides instructions for setting up Redis for the Honking LED Backend application.

## 🚀 Quick Start (Docker)

### Prerequisites
- Docker and Docker Compose installed
- Git

### 1. Clone and Setup
```bash
git clone <repository-url>
cd honking_led_backend
```

### 2. Start Redis Only
```bash
# Start just Redis (minimal setup)
docker-compose up redis -d

# Start Redis with Redis Commander (GUI tool)
docker-compose --profile tools up redis redis-commander -d
```

### 3. Start Full Stack
```bash
# Start Redis, MySQL, and Laravel application
docker-compose --profile full up -d
```

### 4. Access Redis
- **Redis Server**: `localhost:6379`
- **Redis Commander GUI**: http://localhost:8081 (if using tools profile)
- **Laravel App**: http://localhost:8000 (if using full profile)

## 🖥️ Local Development Setup

### Option 1: Native Installation

#### Windows
1. **Download Redis for Windows**
   ```bash
   # Run the provided setup script as Administrator
   scripts\setup-redis-production.bat
   ```

2. **Manual Installation**
   - Download Redis from: https://github.com/microsoftarchive/redis/releases
   - Extract to `C:\Program Files\Redis`
   - Install as service: `redis-server --service-install redis.conf --service-name Redis`
   - Start service: `redis-server --service-start --service-name Redis`

#### macOS
1. **Install with Homebrew**
   ```bash
   brew install redis
   brew services start redis
   ```

2. **Verify Installation**
   ```bash
   redis-cli ping
   # Should return: PONG
   ```

#### Linux (Ubuntu/Debian)
1. **Install Redis**
   ```bash
   sudo apt update
   sudo apt install redis-server
   sudo systemctl start redis-server
   sudo systemctl enable redis-server
   ```

2. **Verify Installation**
   ```bash
   redis-cli ping
   # Should return: PONG
   ```

### Option 2: Using the Production Setup Script

#### Linux Production Setup
```bash
# Run as root or with sudo
sudo bash scripts/setup-redis-production.sh
```

#### Windows Production Setup
```bash
# Run as Administrator
scripts\setup-redis-production.bat
```

## ⚙️ Laravel Configuration

### 1. Update Environment Variables
Update your `.env` file with Redis configuration:

```env
# Redis Configuration
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Redis Database Configuration
REDIS_DB=0
REDIS_CACHE_DB=1
REDIS_SESSION_DB=2
REDIS_QUEUE_DB=3

# Laravel Drivers
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 2. Install PHP Redis Extension (if not already installed)

#### Windows
- Download `php_redis.dll` from: https://windows.php.net/downloads/pecl/releases/redis/
- Place in your PHP extensions directory
- Add to `php.ini`: `extension=redis`

#### macOS
```bash
brew install php-redis
```

#### Linux (Ubuntu/Debian)
```bash
sudo apt install php-redis
```

### 3. Clear Laravel Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

## 🔍 Testing Redis Connection

### 1. Test Redis CLI
```bash
redis-cli ping
# Should return: PONG
```

### 2. Test Laravel Connection
```bash
php artisan tinker
>>> Cache::store('redis')->put('test', 'Redis is working!', 60);
>>> Cache::store('redis')->get('test');
# Should return: "Redis is working!"
```

### 3. Test Queue Connection
```bash
php artisan queue:failed-table
php artisan tinker
>>> dispatch(function () {
    logger('Redis queue test successful!');
});
```

## 📊 Monitoring Redis

### 1. Built-in Laravel Filament Page
- Access: Admin Panel → Redis Stats
- View: Memory usage, hit rates, connections, and more

### 2. Redis Commander (Docker)
- URL: http://localhost:8081
- Features: Web-based Redis GUI

### 3. Command Line Tools
```bash
# Redis info
redis-cli info

# Monitor Redis commands
redis-cli monitor

# Check memory usage
redis-cli info memory

# Check connected clients
redis-cli info clients
```

## 🔧 Redis Configuration Files

### Production Configuration
- **Linux**: `/etc/redis/redis.conf`
- **Windows**: `C:\Program Files\Redis\redis.conf`
- **Docker**: `docker/redis/redis.conf`

### Key Settings
- `maxmemory`: Memory limit (256mb for production, 128mb for development)
- `maxmemory-policy`: `allkeys-lru` for automatic eviction
- `save`: Persistence settings
- `databases`: Number of databases (16)

## 🚨 Troubleshooting

### Common Issues

#### 1. Connection Refused
```bash
# Check if Redis is running
sudo systemctl status redis-server  # Linux
redis-server --service-status --service-name Redis  # Windows

# Start Redis if not running
sudo systemctl start redis-server  # Linux
redis-server --service-start --service-name Redis  # Windows
```

#### 2. PHP Redis Extension Not Found
```bash
# Check if extension is loaded
php -m | grep redis

# Install extension if missing
sudo apt install php-redis  # Linux
brew install php-redis       # macOS
```

#### 3. Laravel Cache Not Working
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan session:clear
php artisan view:clear

# Test cache manually
php artisan tinker
>>> Cache::store('redis')->put('test', 'value', 60);
>>> Cache::store('redis')->get('test');
```

#### 4. Queue Jobs Not Processing
```bash
# Check queue connection
php artisan queue:failed

# Restart queue worker
php artisan queue:restart

# Run queue worker manually
php artisan queue:work --tries=3
```

### Performance Optimization

#### 1. Redis Memory Optimization
```bash
# Monitor memory usage
redis-cli info memory | grep used_memory

# Check memory fragmentation
redis-cli info memory | grep mem_fragmentation_ratio
```

#### 2. Laravel Cache Optimization
```bash
# Use Redis tags for better cache management
Cache::tags(['users', 'profile'])->remember('user_1_profile', 3600, function () {
    return User::find(1);
});

# Clear specific tag groups
Cache::tags(['users'])->flush();
```

## 📚 Useful Commands

### Redis Commands
```bash
redis-cli ping                    # Test connection
redis-cli info                    # Get Redis info
redis-cli monitor                 # Monitor Redis commands
redis-cli info memory            # Memory usage
redis-cli info clients           # Client connections
redis-cli info stats             # General statistics
redis-cli info keyspace          # Database statistics
```

### Laravel Commands
```bash
php artisan cache:clear           # Clear cache
php artisan config:cache          # Cache configuration
php artisan queue:work           # Start queue worker
php artisan queue:restart         # Restart queue workers
php artisan schedule:run         # Run scheduled tasks
```

### Docker Commands
```bash
docker-compose up redis -d                    # Start Redis
docker-compose logs redis                     # View Redis logs
docker-compose exec redis redis-cli           # Access Redis CLI
docker-compose down                          # Stop all services
docker-compose down -v                       # Stop and remove volumes
```

## 🔐 Security Considerations

1. **Production Security**
   - Set a strong Redis password: `requirepass your-strong-password`
   - Bind to localhost only: `bind 127.0.0.1`
   - Use firewall rules to restrict access
   - Disable dangerous commands: `rename-command FLUSHDB ""`

2. **Environment Variables**
   - Never commit Redis passwords to version control
   - Use environment variables for sensitive configuration
   - Rotate passwords regularly

3. **Network Security**
   - Use TLS/SSL for Redis connections in production
   - Consider Redis Sentinel or Cluster for high availability
   - Implement proper network segmentation

## 📈 Monitoring and Alerts

1. **Key Metrics to Monitor**
   - Memory usage percentage
   - Cache hit rate
   - Connected clients
   - Commands per second
   - Evicted keys count

2. **Alert Thresholds**
   - Memory usage > 80%
   - Cache hit rate < 90%
   - Connected clients > 1000
   - Evicted keys > 0

3. **Monitoring Tools**
   - Laravel Filament Redis Stats page
   - Redis Commander GUI
   - Prometheus + Grafana
   - DataDog/New Relic APM

For additional support or questions, refer to the [Redis documentation](https://redis.io/documentation) and [Laravel Redis documentation](https://laravel.com/docs/redis).
