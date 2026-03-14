@echo off
REM Redis Setup Script for Windows Production
REM This script installs Redis as a Windows service

echo 🚀 Starting Redis setup for Windows production...

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo Please run this script as Administrator
    pause
    exit /b 1
)

REM Check if Redis is already installed
if exist "C:\Program Files\Redis" (
    echo Redis is already installed at C:\Program Files\Redis
    echo Updating configuration...
    goto configure
)

REM Download Redis for Windows
echo 📦 Downloading Redis for Windows...
if not exist "redis.zip" (
    powershell -Command "Invoke-WebRequest -Uri 'https://github.com/microsoftarchive/redis/releases/download/win-3.0.504/Redis-x64-3.0.504.zip' -OutFile 'redis.zip'"
)

REM Create Redis directory
echo 📁 Creating Redis directory...
if not exist "C:\Program Files\Redis" mkdir "C:\Program Files\Redis"

REM Extract Redis
echo 📂 Extracting Redis...
powershell -Command "Expand-Archive -Path 'redis.zip' -DestinationPath 'C:\Program Files\Redis' -Force"

REM Clean up zip file
del redis.zip

:configure
echo ⚙️ Configuring Redis for production...

REM Create Redis configuration file
echo # Redis configuration for Windows production > "C:\Program Files\Redis\redis.conf"
echo # Network >> "C:\Program Files\Redis\redis.conf"
echo bind 127.0.0.1 >> "C:\Program Files\Redis\redis.conf"
echo port 6379 >> "C:\Program Files\Redis\redis.conf"
echo timeout 0 >> "C:\Program Files\Redis\redis.conf"
echo tcp-keepalive 300 >> "C:\Program Files\Redis\redis.conf"
echo. >> "C:\Program Files\Redis\redis.conf"
echo # Memory management >> "C:\Program Files\Redis\redis.conf"
echo maxmemory 256mb >> "C:\Program Files\Redis\redis.conf"
echo maxmemory-policy allkeys-lru >> "C:\Program Files\Redis\redis.conf"
echo. >> "C:\Program Files\Redis\redis.conf"
echo # Persistence >> "C:\Program Files\Redis\redis.conf"
echo save 900 1 >> "C:\Program Files\Redis\redis.conf"
echo save 300 10 >> "C:\Program Files\Redis\redis.conf"
echo save 60 10000 >> "C:\Program Files\Redis\redis.conf"
echo stop-writes-on-bgsave-error yes >> "C:\Program Files\Redis\redis.conf"
echo rdbcompression yes >> "C:\Program Files\Redis\redis.conf"
echo rdbchecksum yes >> "C:\Program Files\Redis\redis.conf"
echo dbfilename dump.rdb >> "C:\Program Files\Redis\redis.conf"
echo dir "C:\Program Files\Redis\data" >> "C:\Program Files\Redis\redis.conf"
echo. >> "C:\Program Files\Redis\redis.conf"
echo # Logging >> "C:\Program Files\Redis\redis.conf"
echo loglevel notice >> "C:\Program Files\Redis\redis.conf"
echo logfile "C:\Program Files\Redis\redis.log" >> "C:\Program Files\Redis\redis.conf"
echo. >> "C:\Program Files\Redis\redis.conf"
echo # Performance >> "C:\Program Files\Redis\redis.conf"
echo tcp-backlog 511 >> "C:\Program Files\Redis\redis.conf"
echo databases 16 >> "C:\Program Files\Redis\redis.conf"

REM Create data directory
if not exist "C:\Program Files\Redis\data" mkdir "C:\Program Files\Redis\data"

REM Install Redis as Windows service
echo ⚡ Installing Redis as Windows service...
cd /d "C:\Program Files\Redis"
redis-server --service-install redis.conf --service-name Redis

REM Start Redis service
echo 🔄 Starting Redis service...
redis-server --service-start --service-name Redis

REM Test Redis connection
echo 🔗 Testing Redis connection...
timeout /t 3 >nul
redis-cli ping >nul 2>&1
if %errorLevel% equ 0 (
    echo ✅ Redis connection test passed!
) else (
    echo ❌ Redis connection test failed.
    pause
    exit /b 1
)

REM Display Redis info
echo 📈 Redis Information:
redis-cli info server | findstr "redis_version os arch process_id uptime_in_seconds"

echo.
echo 🎉 Redis setup completed successfully!
echo.
echo 📝 Next steps:
echo 1. Update your Laravel .env file with Redis configuration:
echo    CACHE_DRIVER=redis
echo    SESSION_DRIVER=redis
echo    QUEUE_CONNECTION=redis
echo.
echo 2. Configure Redis monitoring and backups as needed
echo 3. Check Redis status anytime with: redis-server --service-status --service-name Redis
echo.
echo 📚 Useful commands:
echo    redis-cli ping                    # Test connection
echo    redis-cli info                    # Get Redis info
echo    redis-cli monitor                 # Monitor Redis commands
echo    redis-server --service-stop --service-name Redis     # Stop Redis
echo    redis-server --service-start --service-name Redis    # Start Redis
echo    redis-server --service-restart --service-name Redis  # Restart Redis

pause
