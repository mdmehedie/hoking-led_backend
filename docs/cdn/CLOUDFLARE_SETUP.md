# Cloudflare CDN Setup Guide

## 📋 Overview

This guide covers the complete setup of Cloudflare CDN with the Laravel application for optimal performance and security.

## 🚀 Prerequisites

- Cloudflare account (Free plan works)
- Domain name pointing to your server
- Laravel application deployed and working
- SSL certificate (Cloudflare provides free SSL)

## 🔧 Step 1: Cloudflare Account Setup

### 1.1 Create Cloudflare Account

1. **Sign up** at [Cloudflare Dashboard](https://dash.cloudflare.com)
2. **Verify email** and complete account setup
3. **Choose plan**: Free plan is sufficient for most needs

### 1.2 Add Your Website

1. **Add site**: Enter your domain name
2. **DNS scan**: Cloudflare will automatically detect existing records
3. **Select plan**: Choose Free plan
4. **Continue** to DNS configuration

## 🔧 Step 2: DNS Configuration

### 2.1 Update DNS Records

Configure these records in Cloudflare DNS:

```
Type    Name            Content                 Proxy Status
A       @               YOUR_SERVER_IP         Proxied (Orange Cloud)
A       www             YOUR_SERVER_IP         Proxied (Orange Cloud)
CNAME   app             your-domain.com        Proxied (Orange Cloud)
CNAME   api             your-domain.com        Proxied (Orange Cloud)
CNAME   cdn             your-domain.com        Proxied (Orange Cloud)
MX      @               mail.your-domain.com   DNS Only
TXT     @               "v=spf1 include:_spf.google.com ~all"  DNS Only
```

### 2.2 Update Nameservers

After adding your site, Cloudflare will provide nameservers:

```
ns1.cloudflare.com
ns2.cloudflare.com
```

**Update these at your domain registrar:**
1. Log into your domain registrar
2. Go to DNS settings
3. Replace existing nameservers with Cloudflare ones
4. **Wait 24-48 hours** for propagation

## ⚙️ Step 3: SSL/TLS Configuration

### 3.1 SSL/TLS Settings

Navigate to **SSL/TLS** > **Overview**:

1. **Encryption Mode**: Select **Full (strict)**
   - Ensures end-to-end encryption
   - Requires valid SSL certificate on your server

2. **HTTPS Redirect**: Enable **Always Use HTTPS**
   - Redirects all HTTP traffic to HTTPS

3. **HSTS**: Enable HTTP Strict Transport Security
   - Add your domain to HSTS preload list

### 3.2 Edge Certificates

Navigate to **SSL/TLS** > **Edge Certificates**:

1. **Always Use HTTPS**: Enabled
2. **HTTP Strict Transport Security (HSTS)**: Enabled
3. **Minimum TLS Version**: TLS 1.2
4. **Opportunistic Encryption**: Enabled
5. **TLS 1.3**: Enabled
6. **Automatic HTTPS Rewrites**: Enabled

### 3.3 Origin Certificates

Navigate to **SSL/TLS** > **Origin Server** > **Create Certificate**:

1. **Generate Certificate**: Create for your domain
2. **Download Certificate**: Get PEM format
3. **Install on Server**: Configure your web server

## ⚡ Step 4: Performance Optimization

### 4.1 Caching Configuration

Navigate to **Caching** > **Configuration**:

#### **Basic Settings**
- **Caching Level**: Standard
- **Browser Cache TTL**: 4 hours
- **Edge Cache TTL**: 2 hours (for dynamic content)
- **Development Mode**: Disable in production

#### **Advanced Settings**
- **Cache By Device Type**: Enabled
- **Cache Key**: Default (URL + Headers)
- **Minimum File Size**: 1KB
- **Maximum File Size**: 500MB

### 4.2 Page Rules

Navigate to **Rules** > **Page Rules**:

#### **Rule 1: Static Assets (High Priority)**
```
*yourdomain.com/assets/*
Cache Level: Cache Everything
Edge Cache TTL: 1 month
Browser Cache TTL: 1 year
```

#### **Rule 2: Media Files (High Priority)**
```
*yourdomain.com/storage/*
Cache Level: Cache Everything
Edge Cache TTL: 1 month
Browser Cache TTL: 1 year
```

#### **Rule 3: Admin Panel (High Priority)**
```
*yourdomain.com/admin/*
Cache Level: Bypass
```

#### **Rule 4: API Endpoints (Medium Priority)**
```
*yourdomain.com/api/*
Cache Level: Bypass
```

#### **Rule 5: HTML Pages (Medium Priority)**
```
*yourdomain.com/*
Cache Level: Cache Everything
Edge Cache TTL: 2 hours
Browser Cache TTL: 4 hours
```

### 4.3 Speed Optimization

Navigate to **Speed** > **Optimization**:

#### **Enable All Features**
- ✅ **Auto Minify**: HTML, CSS, JavaScript
- ✅ **Brotli**: Better compression than gzip
- ✅ **Early Hints**: Faster resource loading
- ✅ **HTTP/2**: Multiplexed connections
- ✅ **HTTP/3 (with QUIC)**: Latest protocol

#### **Image Optimization**
- ✅ **Auto WebP**: Convert images to WebP
- ✅ **Mirrored**: Serve from multiple locations
- ✅ **Polish**: Automatic image optimization

## 🔒 Step 5: Security Configuration

### 5.1 Security Settings

Navigate to **Security** > **Settings**:

#### **Basic Security**
- **Security Level**: Medium
- **Bot Fight Mode**: Enabled
- **WAF**: Enable Web Application Firewall
- **DDoS Protection**: Enabled (default)

#### **Advanced Security**
- **Maximum Upload Size**: 100MB
- **Server Name Indication**: Enabled
- **TLS Client Auth**: Disabled (unless needed)

### 5.2 Firewall Rules

Navigate to **Security** > **WAF** > **Firewall Rules**:

#### **Rule 1: Block Malicious Bots**
```
Expression: (cf.bot_management.score lt 30) and (http.request.uri.path contains "/api")
Action: Block
```

#### **Rule 2: Admin IP Whitelist**
```
Expression: (http.request.uri.path contains "/admin") and (ip.src ne 192.168.1.100)
Action: Block
```

#### **Rule 3: API Rate Limiting**
```
Expression: (http.request.uri.path contains "/api") and (cf.threat_score gt 10)
Action: Rate Limit (100 requests per minute)
```

#### **Rule 4: Country Blocking (Optional)**
```
Expression: (ip.geo.country ne "US") and (ip.geo.country ne "CA")
Action: Block
```

### 5.3 Bot Management

Navigate to **Security** > **Bots**:

#### **Bot Fight Mode**
- **Enabled**: Automatically challenges suspicious bots
- **Protect Login Forms**: Enabled
- **Protect Registration Forms**: Enabled

#### **Bot Classification**
- **Verified Bots**: Allow (Google, Bing, etc.)
- **Likely Automated**: Challenge
- **Definitely Automated**: Block

## 🔧 Step 6: Laravel Configuration

### 6.1 Environment Variables

Update your `.env` file:

```env
# App URL (use HTTPS)
APP_URL=https://yourdomain.com

# Force HTTPS in production
APP_FORCE_HTTPS=true

# Cloudflare trusted proxies
TRUSTED_PROXIES=*
TRUSTED_PROXIES_CLOUDFLARE=true

# Asset URL (if using Cloudflare CDN)
ASSET_URL=https://yourdomain.com

# Cache configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Response caching
RESPONSE_CACHE_ENABLED=true
RESPONSE_CACHE_DRIVER=redis
RESPONSE_CACHE_LIFETIME=86400
```

### 6.2 TrustProxies Middleware

The TrustProxies middleware is configured in `app/Http/Middleware/TrustProxies.php`:

```php
protected $proxies = '*';
protected $headers = Request::HEADER_X_FORWARDED_ALL;
```

### 6.3 Force HTTPS in Production

Add to `AppServiceProvider.php`:

```php
public function boot()
{
    if (app()->environment('production')) {
        URL::forceScheme('https');
    }
}
```

### 6.4 Register Middleware

Ensure TrustProxies is registered in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        TrustProxies::class,
        CacheAssets::class,
        SetLocale::class,
    ]);
})
```

## 🚀 Step 7: Testing and Verification

### 7.1 SSL Certificate Test

```bash
# Test SSL certificate
openssl s_client -connect yourdomain.com:443

# Check certificate details
curl -I https://yourdomain.com
```

### 7.2 Cloudflare Headers Test

```bash
# Check Cloudflare headers
curl -I https://yourdomain.com

# Look for these headers:
# CF-RAY: Cloudflare ray ID
# CF-Cache-Status: Cache status
# Server: cloudflare
```

### 7.3 Performance Test

```bash
# Test page load time
curl -w "%{time_total}\n" -o /dev/null -s https://yourdomain.com

# Test with WebPageTest
# https://www.webpagetest.org/

# Test with GTmetrix
# https://gtmetrix.com/
```

### 7.4 Cache Test

```bash
# Test static asset caching
curl -I https://yourdomain.com/assets/css/app.css

# Look for Cache-Control header
# Cache-Control: public, max-age=31536000, immutable
```

## 📊 Step 8: Monitoring

### 8.1 Cloudflare Analytics

Access these in Cloudflare dashboard:

#### **Analytics & Logs**
- **Overview**: Traffic trends and patterns
- **Security Events**: Blocked threats and attacks
- **Firewall Events**: Detailed firewall logs
- **Load Balancing**: Server health and performance

#### **Caching**
- **Cache Analytics**: Cache hit rates and performance
- **Purge Cache**: Manual cache clearing
- **Cache Rules**: Advanced caching configuration

### 8.2 Laravel Performance

Use the Cache Management page in Filament:

- **Cache Statistics**: Monitor Redis performance
- **Clear Cache**: Manual cache clearing when needed
- **Response Cache**: Monitor cached responses
- **Image Optimization**: Track optimization progress

## 🔧 Step 9: Advanced Configuration

### 9.1 Argo Smart Routing (Paid Plans)

Navigate to **Speed** > **Argo Smart Routing**:

- **Enable**: Activate for 30%+ faster performance
- **Smart Routing**: Intelligent traffic routing
- **Tiered Cache**: Additional caching layers

### 9.2 Cloudflare Workers (Optional)

Create custom edge logic:

```javascript
// Worker for API rate limiting
addEventListener('fetch', event => {
  event.respondWith(handleRequest(event.request))
})

async function handleRequest(request) {
  if (request.url.includes('/api/')) {
    return rateLimit(request)
  }
  return fetch(request)
}
```

### 9.3 Image Resizing (Paid Plans)

```html
<!-- Automatic image optimization -->
<img src="https://yourdomain.com/cdn-cgi/image/width=800,height=600,fit=cover,quality=80/images/photo.jpg" alt="Photo">
```

## 🛠️ Step 10: Troubleshooting

### 10.1 Common Issues

#### **SSL Certificate Errors**
```bash
# Check certificate chain
curl -I https://yourdomain.com

# Verify SSL mode is Full (strict)
# Check server has valid certificate
```

#### **Cache Issues**
```bash
# Clear Cloudflare cache
# Dashboard: Caching > Configuration > Purge

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

#### **DNS Propagation Issues**
```bash
# Check DNS propagation
nslookup yourdomain.com
dig yourdomain.com

# Check nameservers
whois yourdomain.com
```

### 10.2 Performance Issues

#### **Slow Load Times**
1. Check Cloudflare analytics
2. Verify caching rules
3. Test with different geographic locations
4. Check server performance

#### **High Bandwidth Usage**
1. Enable Brotli compression
2. Optimize images
3. Use WebP format
4. Implement lazy loading

## 📈 Step 11: Optimization Tips

### 11.1 Performance Best Practices

- **Enable all caching features**
- **Use WebP images whenever possible**
- **Implement lazy loading for images**
- **Minify CSS and JavaScript**
- **Use HTTP/2 and HTTP/3**
- **Enable Brotli compression**

### 11.2 Security Best Practices

- **Use Full (strict) SSL mode**
- **Enable HSTS**
- **Configure firewall rules**
- **Enable Bot Fight Mode**
- **Monitor security events**
- **Regular security audits**

### 11.3 Monitoring Best Practices

- **Check analytics daily**
- **Monitor cache hit rates**
- **Track performance metrics**
- **Set up alerts for issues**
- **Regular performance testing**

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
