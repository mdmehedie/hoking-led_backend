#!/bin/bash

# Redis Setup Script for Production
# This script installs and configures Redis on Ubuntu/Debian systems

set -e

echo "🚀 Starting Redis setup for production..."

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo "Please run as root (use sudo)"
    exit 1
fi

# Update package list
echo "📦 Updating package list..."
apt update

# Install Redis server
echo "🔧 Installing Redis server..."
apt install -y redis-server

# Enable Redis service
echo "⚡ Enabling Redis service..."
systemctl enable redis-server

# Configure Redis for production
echo "⚙️ Configuring Redis for production..."

# Backup original config
cp /etc/redis/redis.conf /etc/redis/redis.conf.backup

# Redis configuration optimizations for production
cat > /etc/redis/redis.conf << 'EOF'
# Redis configuration for production

# Network
bind 127.0.0.1
port 6379
timeout 0
tcp-keepalive 300

# Memory management
maxmemory 256mb
maxmemory-policy allkeys-lru
maxmemory-samples 5

# Persistence
save 900 1
save 300 10
save 60 10000
stop-writes-on-bgsave-error yes
rdbcompression yes
rdbchecksum yes
dbfilename dump.rdb
dir /var/lib/redis

# Logging
loglevel notice
logfile /var/log/redis/redis-server.log

# Security
# requirepass your-secure-password-here

# Performance
tcp-backlog 511
databases 16
always-show-logo yes

# Client connections
maxclients 10000

# Slow log
slowlog-log-slower-than 10000
slowlog-max-len 128

# Latency monitoring
latency-monitor-threshold 100

# Memory optimization
hash-max-ziplist-entries 512
hash-max-ziplist-value 64
list-max-ziplist-size -2
list-compress-depth 0
set-max-intset-entries 512
zset-max-ziplist-entries 128
zset-max-ziplist-value 64
hll-sparse-max-bytes 3000
EOF

# Set proper permissions
chown redis:redis /etc/redis/redis.conf
chmod 640 /etc/redis/redis.conf

# Create Redis log directory if it doesn't exist
mkdir -p /var/log/redis
chown redis:redis /var/log/redis

# Restart Redis service
echo "🔄 Restarting Redis service..."
systemctl restart redis-server

# Check Redis status
echo "📊 Checking Redis status..."
if systemctl is-active --quiet redis-server; then
    echo "✅ Redis is running successfully!"
else
    echo "❌ Redis failed to start. Check logs with: journalctl -u redis-server"
    exit 1
fi

# Test Redis connection
echo "🔗 Testing Redis connection..."
if redis-cli ping > /dev/null 2>&1; then
    echo "✅ Redis connection test passed!"
else
    echo "❌ Redis connection test failed."
    exit 1
fi

# Display Redis info
echo "📈 Redis Information:"
redis-cli info server | grep -E "redis_version|os|arch|process_id|uptime_in_seconds"

echo ""
echo "🎉 Redis setup completed successfully!"
echo ""
echo "📝 Next steps:"
echo "1. Update your Laravel .env file with Redis configuration:"
echo "   CACHE_DRIVER=redis"
echo "   SESSION_DRIVER=redis"
echo "   QUEUE_CONNECTION=redis"
echo ""
echo "2. Consider setting a Redis password by uncommenting 'requirepass' in /etc/redis/redis.conf"
echo "3. Configure Redis monitoring and backups as needed"
echo "4. Check Redis status anytime with: systemctl status redis-server"
echo ""
echo "📚 Useful commands:"
echo "   redis-cli ping                    # Test connection"
echo "   redis-cli info                    # Get Redis info"
echo "   redis-cli monitor                 # Monitor Redis commands"
echo "   systemctl restart redis-server    # Restart Redis"
echo "   journalctl -u redis-server        # View Redis logs"
