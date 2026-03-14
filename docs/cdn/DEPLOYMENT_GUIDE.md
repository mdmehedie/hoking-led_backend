# Deployment Guide

## 📋 Overview

This guide covers the complete deployment process for the Laravel application with Cloudflare CDN integration and performance optimizations.

## 🚀 Prerequisites

### **Server Requirements**
- **PHP**: 8.2 or higher
- **Web Server**: Nginx or Apache
- **Database**: MySQL 8.0+ or PostgreSQL 12+
- **Redis**: 6.0+ for caching
- **Node.js**: 18+ for asset building
- **SSL Certificate**: Valid certificate (Cloudflare provides free)

### **Domain Requirements**
- **Domain name**: Registered and pointing to server
- **DNS access**: Ability to change nameservers
- **Cloudflare account**: Free plan sufficient

### **Development Tools**
- **Git**: For version control
- **Composer**: For PHP dependencies
- **NPM**: For Node.js dependencies
- **SSH access**: For server management

## 📦 Pre-Deployment Checklist

### **1. Code Preparation**

```bash
# Ensure latest code is committed
git status
git add .
git commit -m "Ready for deployment"
git push origin main

# Tag release (optional)
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

### **2. Environment Configuration**

```bash
# Copy environment file
cp .env.example .env

# Update production values
php artisan key:generate
php artisan jwt:secret  # If using JWT
```

### **3. Dependency Installation**

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
npm ci --production
```

### **4. Asset Building**

```bash
# Build optimized assets
npm run build

# Verify build output
ls -la public/build/
```

## 🔧 Server Setup

### **1. Web Server Configuration**

#### Nginx Configuration

```nginx
# /etc/nginx/sites-available/yourdomain.com
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/yourdomain.com/public;
    index index.php index.html;

    # SSL Configuration
    ssl_certificate /etc/ssl/certs/yourdomain.com.crt;
    ssl_certificate_key /etc/ssl/private/yourdomain.com.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/json
        application/javascript
        application/xml+rss
        application/atom+xml
        image/svg+xml;

    # Brotli Compression (if available)
    brotli on;
    brotli_comp_level 6;
    brotli_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    # Laravel Configuration
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    # PHP Processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # PHP-FPM Settings
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_connect_timeout 60;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 256 16k;
        fastcgi_busy_buffers_size 256k;
    }

    # Static Assets with Caching
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Vary Accept-Encoding;
        access_log off;
        log_not_found off;
    }

    # Media Files
    location ~* \.(mp4|webm|ogg|mp3|wav|flac|aac)$ {
        expires 1y;
        add_header Cache-Control "public";
        add_header Vary Accept-Encoding;
        access_log off;
    }

    # Security
    location ~ /\.ht {
        deny all;
    }

    location ~ /\.env {
        deny all;
    }

    # PHP Information (disable in production)
    location ~ ^/(phpinfo|phpmyadmin) {
        deny all;
    }
}
```

#### Apache Configuration

```apache
# /etc/apache2/sites-available/yourdomain.com.conf
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    Redirect permanent / https://yourdomain.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/yourdomain.com/public

    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/yourdomain.com.crt
    SSLCertificateKeyFile /etc/ssl/private/yourdomain.com.key
    SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256
    SSLHonorCipherOrder off
    SSLCompression off

    # Security Headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set X-Content-Type-Options "nosniff"
    Header always set Referrer-Policy "no-referrer-when-downgrade"
    Header always set Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'"

    # Enable Compression
    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/plain
        AddOutputFilterByType DEFLATE text/html
        AddOutputFilterByType DEFLATE text/xml
        AddOutputFilterByType DEFLATE text/css
        AddOutputFilterByType DEFLATE application/xml
        AddOutputFilterByType DEFLATE application/xhtml+xml
        AddOutputFilterByType DEFLATE application/rss+xml
        AddOutputFilterByType DEFLATE application/javascript
        AddOutputFilterByType DEFLATE application/x-javascript
    </IfModule>

    # Laravel Configuration
    <Directory /var/www/yourdomain.com/public>
        AllowOverride All
        Require all granted
    </Directory>

    # Static Assets Caching
    <LocationMatch "\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$">
        ExpiresActive On
        ExpiresDefault "access plus 1 year"
        Header append Cache-Control "public, immutable"
    </LocationMatch>

    # Error and Access Logs
    ErrorLog ${APACHE_LOG_DIR}/yourdomain.com_error.log
    CustomLog ${APACHE_LOG_DIR}/yourdomain.com_access.log combined
</VirtualHost>
```

### **2. Database Setup**

```bash
# Create database
mysql -u root -p
CREATE DATABASE laravel_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON laravel_app.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Test connection
mysql -u laravel_user -p laravel_app
```

### **3. Redis Setup**

```bash
# Install Redis
sudo apt-get update
sudo apt-get install redis-server

# Configure Redis
sudo nano /etc/redis/redis.conf

# Key settings:
# bind 127.0.0.1
# port 6379
# requirepass your_redis_password
# maxmemory 256mb
# maxmemory-policy allkeys-lru

# Start Redis
sudo systemctl start redis-server
sudo systemctl enable redis-server

# Test Redis
redis-cli ping
```

### **4. PHP-FPM Configuration**

```bash
# Edit PHP-FPM configuration
sudo nano /etc/php/8.2/fpm/pool.d/www.conf

# Key settings:
# pm = dynamic
# pm.max_children = 50
# pm.start_servers = 5
# pm.min_spare_servers = 5
# pm.max_spare_servers = 35
# pm.max_requests = 500

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

## 🚀 Deployment Process

### **1. Deploy Code**

```bash
# Clone repository
cd /var/www/
git clone https://github.com/your-repo/yourdomain.com.git
cd yourdomain.com

# Checkout specific tag/branch
git checkout v1.0.0

# Set permissions
sudo chown -R www-data:www-data /var/www/yourdomain.com
sudo chmod -R 755 /var/www/yourdomain.com
sudo chmod -R 777 /var/www/yourdomain.com/storage
sudo chmod -R 777 /var/www/yourdomain.com/bootstrap/cache
```

### **2. Install Dependencies**

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
npm ci --production

# Build assets
npm run build
```

### **3. Environment Configuration**

```bash
# Edit environment file
nano .env

# Key production settings:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_app
DB_USERNAME=laravel_user
DB_PASSWORD=strong_password

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379

# Cloudflare
TRUSTED_PROXIES=*
TRUSTED_PROXIES_CLOUDFLARE=true
ASSET_URL=https://yourdomain.com

# Response Cache
RESPONSE_CACHE_ENABLED=true
RESPONSE_CACHE_DRIVER=redis
RESPONSE_CACHE_LIFETIME=86400
```

### **4. Run Migrations**

```bash
# Run database migrations
php artisan migrate --force

# Seed database (if needed)
php artisan db:seed --force

# Clear and optimize caches
php artisan optimize:clear
php artisan optimize
```

### **5. Optimize Images**

```bash
# Optimize existing images
php artisan media:optimize-images --disk=public --force

# Check optimization results
php artisan media:optimize-images --dry-run
```

### **6. Set Up Scheduled Tasks**

```bash
# Edit crontab
crontab -e

# Add Laravel scheduler
* * * * * cd /var/www/yourdomain.com && php artisan schedule:run >> /dev/null 2>&1

# Add custom tasks
0 2 * * * cd /var/www/yourdomain.com && php artisan cache:clear
0 3 * * 0 cd /var/www/yourdomain.com && php artisan media:optimize-images --force
0 4 * * * cd /var/www/yourdomain.com && php artisan responsecache:clear
```

### **7. Set Up Queue Worker**

```bash
# Create supervisor configuration
sudo nano /etc/supervisor/conf.d/laravel-worker.conf

[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/yourdomain.com/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/yourdomain.com/storage/logs/worker.log
stopwaitsecs=3600

# Start supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## 🔒 Security Hardening

### **1. File Permissions**

```bash
# Set secure permissions
find /var/www/yourdomain.com -type f -exec chmod 644 {} \;
find /var/www/yourdomain.com -type d -exec chmod 755 {} \;
chmod 600 /var/www/yourdomain.com/.env
chmod 600 /var/www/yourdomain.com/storage/oauth-*.key
chmod 600 /var/www/yourdomain.com/storage/*.key

# Set ownership
chown -R www-data:www-data /var/www/yourdomain.com
```

### **2. Firewall Configuration**

```bash
# Configure UFW
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw allow 'Apache Full'
sudo ufw deny 5432  # PostgreSQL
sudo ufw deny 3306  # MySQL
```

### **3. SSL Certificate**

```bash
# Install Certbot
sudo apt-get install certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Set up auto-renewal
sudo crontab -e
0 12 * * * /usr/bin/certbot renew --quiet
```

### **4. PHP Security**

```bash
# Edit php.ini
sudo nano /etc/php/8.2/cli/php.ini

# Security settings:
expose_php = Off
display_errors = Off
log_errors = On
allow_url_fopen = Off
allow_url_include = Off
file_uploads = On
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
```

## 🌐 Cloudflare Setup

### **1. Add Site to Cloudflare**

1. **Sign up/login** to [Cloudflare Dashboard](https://dash.cloudflare.com)
2. **Add site**: Enter your domain name
3. **Choose plan**: Free plan
4. **Scan DNS**: Cloudflare will detect existing records

### **2. Update DNS Records**

```
Type    Name            Content                 Proxy Status
A       @               YOUR_SERVER_IP         Proxied (Orange Cloud)
A       www             YOUR_SERVER_IP         Proxied (Orange Cloud)
CNAME   app             your-domain.com        Proxied (Orange Cloud)
CNAME   api             your-domain.com        Proxied (Orange Cloud)
```

### **3. Update Nameservers**

```
ns1.cloudflare.com
ns2.cloudflare.com
```

Update these at your domain registrar.

### **4. SSL/TLS Configuration**

- **Encryption Mode**: Full (strict)
- **HTTPS Redirect**: Always Use HTTPS
- **HSTS**: Enabled
- **Minimum TLS Version**: TLS 1.2

### **5. Performance Settings**

- **Caching Level**: Standard
- **Browser Cache TTL**: 4 hours
- **Edge Cache TTL**: 2 hours
- **Auto Minify**: HTML, CSS, JavaScript
- **Brotli**: Enabled
- **HTTP/2**: Enabled

### **6. Page Rules**

```
*yourdomain.com/assets/*
Cache Level: Cache Everything
Edge Cache TTL: 1 month
Browser Cache TTL: 1 year

*yourdomain.com/storage/*
Cache Level: Cache Everything
Edge Cache TTL: 1 month
Browser Cache TTL: 1 year

*yourdomain.com/admin/*
Cache Level: Bypass

*yourdomain.com/api/*
Cache Level: Bypass
```

## 📊 Post-Deployment Verification

### **1. Basic Functionality**

```bash
# Test application
curl -I https://yourdomain.com

# Check SSL certificate
openssl s_client -connect yourdomain.com:443

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo()
```

### **2. Performance Testing**

```bash
# Test page load time
curl -w "%{time_total}\n" -o /dev/null -s https://yourdomain.com

# Test with WebPageTest
# https://www.webpagetest.org/

# Test with GTmetrix
# https://gtmetrix.com/
```

### **3. Cache Testing**

```bash
# Test static asset caching
curl -I https://yourdomain.com/assets/css/app.css

# Look for:
# Cache-Control: public, max-age=31536000, immutable
# CF-Cache-Status: HIT
```

### **4. Image Optimization**

```bash
# Test WebP images
curl -I https://yourdomain.com/storage/images/photo.webp

# Test lazy loading
# Check browser network tab for lazy loading behavior
```

## 🔧 Maintenance Procedures

### **1. Regular Maintenance**

```bash
# Weekly maintenance script
#!/bin/bash
# maintenance.sh

echo "Starting weekly maintenance..."

# Clear caches
php artisan cache:clear
php artisan responsecache:clear
php artisan view:clear

# Optimize images
php artisan media:optimize-images --force

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart redis-server

# Backup database
mysqldump -u laravel_user -p laravel_app > /backups/db_$(date +%Y%m%d).sql

echo "Weekly maintenance completed!"
```

### **2. Deployment Script**

```bash
#!/bin/bash
# deploy.sh

set -e

echo "Starting deployment..."

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci --production

# Build assets
npm run build

# Run migrations
php artisan migrate --force

# Clear and optimize caches
php artisan optimize:clear
php artisan optimize

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm

echo "Deployment completed!"
```

### **3. Backup Procedures**

```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u laravel_user -p laravel_app > $BACKUP_DIR/db_$DATE.sql

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/yourdomain.com

# Clean old backups (keep last 7 days)
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

## 🚨 Troubleshooting

### **Common Issues**

#### **502 Bad Gateway**
```bash
# Check Nginx status
sudo systemctl status nginx

# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check Nginx error log
sudo tail -f /var/log/nginx/error.log

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

#### **Database Connection Error**
```bash
# Check database status
sudo systemctl status mysql

# Test connection
mysql -u laravel_user -p laravel_app

# Check Laravel logs
tail -f storage/logs/laravel.log
```

#### **Cache Issues**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan responsecache:clear

# Check Redis
redis-cli ping
redis-cli info stats
```

#### **Asset Loading Issues**
```bash
# Check build assets
ls -la public/build/

# Rebuild assets
npm run build

# Check permissions
sudo chown -R www-data:www-data public/build/
```

### **Performance Issues**

```bash
# Check server load
top
htop

# Check memory usage
free -h

# Check disk usage
df -h

# Check Nginx connections
sudo nginx -T | grep worker_connections
```

## 📈 Monitoring

### **1. Application Monitoring**

```bash
# Monitor Laravel logs
tail -f storage/logs/laravel.log

# Monitor queue workers
php artisan queue:monitor

# Monitor cache performance
php artisan monitor:performance
```

### **2. Server Monitoring**

```bash
# Monitor system resources
htop
iotop
nethogs

# Monitor Nginx
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log

# Monitor PHP-FPM
sudo tail -f /var/log/php8.2-fpm.log
```

### **3. Cloudflare Monitoring**

- **Analytics**: Traffic trends and patterns
- **Security Events**: Blocked threats and attacks
- **Cache Analytics**: Cache hit rates and performance
- **Load Balancing**: Server health and performance

## 📚 Best Practices

### **Security**
1. **Keep software updated**: Regular security patches
2. **Use strong passwords**: For all services
3. **Enable firewall**: Restrict unnecessary access
4. **Monitor logs**: Watch for suspicious activity
5. **Backup regularly**: Automated backups

### **Performance**
1. **Use CDN**: Cloudflare for static assets
2. **Enable caching**: At all levels
3. **Optimize images**: WebP and responsive sizes
4. **Minimize assets**: CSS and JavaScript
5. **Monitor performance**: Regular checks

### **Reliability**
1. **Use HTTPS**: Secure all connections
2. **Implement backups**: Automated and tested
3. **Monitor uptime**: Alert on downtime
4. **Load balancing**: Multiple servers if needed
5. **Disaster recovery**: Plan for failures

## 🎯 Advanced Features

### **Load Balancing**

```nginx
# Nginx load balancing
upstream backend {
    server 127.0.0.1:8000;
    server 127.0.0.1:8001;
    server 127.0.0.1:8002;
}

server {
    location / {
        proxy_pass http://backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### **Auto-scaling**

```bash
# Auto-scaling script
#!/bin/bash
# autoscale.sh

CPU_USAGE=$(top -bn1 | grep "Cpu(s)" | sed "s/.*, *\([0-9.]*\)%* id.*/\1/" | awk '{print 100 - $1}')

if (( $(echo "$CPU_USAGE > 80" | bc -l) )); then
    echo "High CPU usage: $CPU_USAGE%"
    # Scale up logic here
fi
```

## 📞 Support

For deployment issues:
1. Check the **troubleshooting section**
2. Review **server logs** and **Laravel logs**
3. Verify **Cloudflare configuration**
4. Contact your hosting provider if needed

---

*Last updated: March 7, 2025*
