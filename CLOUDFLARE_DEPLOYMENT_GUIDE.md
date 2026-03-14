# Cloudflare CDN Integration & Deployment Guide

## 📋 Overview

This guide covers the complete setup of Cloudflare CDN with the Laravel application for optimal performance and security.

## 🚀 Prerequisites

- Cloudflare account (Free plan works)
- Domain name pointing to your server
- Laravel application deployed and working
- SSL certificate (Cloudflare provides free SSL)

## 🔧 Step 1: Cloudflare Setup

### 1.1 Add Your Website to Cloudflare

1. **Sign up/login** to [Cloudflare Dashboard](https://dash.cloudflare.com)
2. **Add site**: Enter your domain name
3. **Choose plan**: Select Free plan (sufficient for most needs)
4. **DNS Records**: Cloudflare will automatically detect existing records

### 1.2 Update DNS Records

```
Type    Name            Content                 Proxy Status
A       @               YOUR_SERVER_IP         Proxied (Orange Cloud)
A       www             YOUR_SERVER_IP         Proxied (Orange Cloud)
CNAME   app             your-domain.com        Proxied (Orange Cloud)
CNAME   api             your-domain.com        Proxied (Orange Cloud)
```

### 1.3 Update Nameservers

After adding your site, Cloudflare will provide nameservers. Update them at your domain registrar:

```
ns1.cloudflare.com
ns2.cloudflare.com
```

## ⚙️ Step 2: Cloudflare Configuration

### 2.1 SSL/TLS Settings

Navigate to **SSL/TLS** > **Overview**:

1. **Encryption Mode**: Select **Full (strict)**
   - Ensures end-to-end encryption
   - Requires valid SSL certificate on your server

2. **HTTPS Redirect**: Enable **Always Use HTTPS**
   - Redirects all HTTP traffic to HTTPS

3. **HSTS**: Enable HTTP Strict Transport Security
   - Add your domain to HSTS preload list

### 2.2 Performance Settings

#### **Caching** (Caching > Configuration)

1. **Caching Level**: Standard
2. **Browser Cache TTL**: 4 hours
3. **Edge Cache TTL**: 2 hours (for dynamic content)
4. **Development Mode**: Disable in production

#### **Page Rules** (Rules > Page Rules)

Create these page rules:

1. **Static Assets** (High Priority)
   ```
   *yourdomain.com/assets/*
   Cache Level: Cache Everything
   Edge Cache TTL: 1 month
   Browser Cache TTL: 1 year
   ```

2. **Media Files** (High Priority)
   ```
   *yourdomain.com/storage/*
   Cache Level: Cache Everything
   Edge Cache TTL: 1 month
   Browser Cache TTL: 1 year
   ```

3. **Admin Panel** (High Priority)
   ```
   *yourdomain.com/admin/*
   Cache Level: Bypass
   ```

4. **API Endpoints** (Medium Priority)
   ```
   *yourdomain.com/api/*
   Cache Level: Bypass
   ```

#### **Speed** (Speed > Optimization)

Enable these features:
- ✅ Auto Minify (HTML, CSS, JavaScript)
- ✅ Brotli compression
- ✅ Early Hints
- ✅ HTTP/2 (with ALPN)

### 2.3 Security Settings

#### **Security** (Security > Settings)

1. **Security Level**: Medium
2. **Bot Fight Mode**: Enable
3. **WAF**: Enable Web Application Firewall
4. **DDoS Protection**: Enable (default)

#### **Firewall Rules** (Security > WAF > Firewall Rules)

```
# Block malicious bots
(cf.bot_management.score lt 30) and (http.request.uri.path contains "/api")

# Allow admin access only from specific IPs
(http.request.uri.path contains "/admin") and (ip.src ne 192.168.1.100)

# Rate limit API endpoints
(http.request.uri.path contains "/api") and (cf.threat_score gt 10)
```

## 🔧 Step 3: Laravel Configuration

### 3.1 Environment Variables

Update your `.env` file:

```env
# App URL (use HTTPS)
APP_URL=https://yourdomain.com

# Force HTTPS in production
APP_FORCE_HTTPS=true

# Cloudflare trusted proxies
TRUSTED_PROXIES=*
TRUSTED_PROXIES_CLOUDFLARE=true

# Cache configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Response caching
RESPONSE_CACHE_ENABLED=true
RESPONSE_CACHE_DRIVER=redis
RESPONSE_CACHE_LIFETIME=86400

# Asset URL (if using Cloudflare CDN)
ASSET_URL=https://yourdomain.com
```

### 3.2 TrustProxies Middleware

The TrustProxies middleware is already configured in `app/Http/Middleware/TrustProxies.php`:

```php
protected $proxies = '*';
protected $headers = Request::HEADER_X_FORWARDED_ALL;
```

### 3.3 Force HTTPS in Production

Add to `AppServiceProvider.php`:

```php
public function boot()
{
    if (app()->environment('production')) {
        URL::forceScheme('https');
    }
}
```

### 3.4 Asset Optimization

Your `vite.config.js` is already configured for production optimization.

## 🚀 Step 4: Deployment Process

### 4.1 Pre-Deployment Checklist

```bash
# 1. Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 2. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Optimize images
php artisan media:optimize-images --disk=public --force

# 4. Build assets
npm run build
```

### 4.2 Deploy Commands

```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci --production

# Run migrations
php artisan migrate --force

# Clear and optimize caches
php artisan optimize:clear
php artisan optimize

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 4.3 Post-Deployment Verification

1. **Check SSL**: Visit `https://yourdomain.com`
2. **Test Cloudflare**: Check headers in browser dev tools
3. **Verify caching**: Test static assets load from Cloudflare
4. **Test API**: Ensure API endpoints work correctly

## 📊 Step 5: Monitoring & Analytics

### 5.1 Cloudflare Analytics

Access Cloudflare dashboard:
- **Analytics & Logs**: Monitor traffic and performance
- **Security Events**: Review blocked threats
- **Load Balancing**: Monitor server health

### 5.2 Laravel Performance

Use the Cache Management page in Filament:
- **Cache Statistics**: Monitor Redis performance
- **Clear Cache**: Manual cache clearing when needed
- **Response Cache**: Monitor cached responses

### 5.3 Performance Monitoring

```bash
# Check Cloudflare status
curl -I https://yourdomain.com

# Test response times
curl -w "@curl-format.txt" -o /dev/null -s https://yourdomain.com

# Monitor Redis
redis-cli info stats
```

## 🛠️ Step 6: Advanced Configuration

### 6.1 Cloudflare Workers (Optional)

Create a worker for custom logic:

```javascript
// Cloudflare Worker for API rate limiting
addEventListener('fetch', event => {
  event.respondWith(handleRequest(event.request))
})

async function handleRequest(request) {
  if (request.url.includes('/api/')) {
    // Add rate limiting logic
    return rateLimit(request)
  }
  return fetch(request)
}
```

### 6.2 Argo Smart Routing

Enable Argo for faster content delivery:
- **Speed > Argo Smart Routing**: Enable for paid plans
- **Performance improvement**: 30%+ faster load times

### 6.3 Image Optimization

Use Cloudflare Image Resizing:

```html
<!-- Automatic image optimization -->
<img src="https://yourdomain.com/cdn-cgi/image/width=800,height=600,fit=cover,quality=80/images/photo.jpg" alt="Photo">
```

## 🔧 Step 7: Troubleshooting

### 7.1 Common Issues

#### **SSL Certificate Errors**
```bash
# Check certificate
openssl s_client -connect yourdomain.com:443

# Verify Cloudflare SSL mode
curl -I https://yourdomain.com
```

#### **Cache Issues**
```bash
# Clear Cloudflare cache
# In Cloudflare dashboard: Caching > Configuration > Purge

# Clear Laravel cache
php artisan cache:clear
php artisan responsecache:clear
```

#### **Mixed Content Errors**
```bash
# Check for HTTP resources
grep -r "http://" resources/

# Use asset() helper for HTTPS
{{ asset('css/style.css') }}
```

### 7.2 Performance Testing

```bash
# Test page load time
curl -w "%{time_total}\n" -o /dev/null -s https://yourdomain.com

# Test with WebPageTest
# https://www.webpagetest.org/

# Test with GTmetrix
# https://gtmetrix.com/
```

## 📈 Step 8: Performance Optimization Tips

### 8.1 Database Optimization

```sql
-- Add indexes for common queries
CREATE INDEX idx_media_conversions ON media(conversion_status, created_at);
CREATE INDEX idx_analytics_events_time ON analytics_events(event_time);
```

### 8.2 Redis Configuration

```redis
# redis.conf
maxmemory 256mb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

### 8.3 PHP Optimization

```ini
# php.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=10000
opcache.revalidate_freq=0
```

## 🎯 Step 9: Security Best Practices

### 9.1 Cloudflare Security

- Enable **Under Attack Mode** during attacks
- Use **Page Rules** to restrict admin access
- Configure **Firewall Rules** for API protection
- Enable **Bot Fight Mode**

### 9.2 Laravel Security

```bash
# Generate app key
php artisan key:generate

# Set secure cookie settings
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

## 📋 Step 10: Maintenance

### 10.1 Regular Tasks

```bash
# Weekly maintenance
php artisan cache:clear
php artisan responsecache:clear
php artisan media:optimize-images --force

# Monthly maintenance
php artisan queue:restart
php artisan schedule:run
```

### 10.2 Monitoring Setup

```bash
# Add to crontab
0 2 * * * /usr/bin/php /path/to/artisan schedule:run
0 3 * * 0 /usr/bin/php /path/to/artisan cache:clear
```

## 🎉 Conclusion

Your Laravel application is now fully optimized with Cloudflare CDN! You should see:

- ✅ **50%+ faster page load times**
- ✅ **99.9% uptime** with Cloudflare's network
- ✅ **Free SSL** certificate
- ✅ **DDoS protection**
- ✅ **Global CDN** distribution
- ✅ **Automatic image optimization**
- ✅ **Enhanced security**

## 📞 Support

- **Cloudflare Support**: https://support.cloudflare.com
- **Laravel Documentation**: https://laravel.com/docs
- **Application Support**: Check Filament admin panel for cache management

---

*Last updated: March 7, 2025*
