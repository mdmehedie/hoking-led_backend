# Robots.txt - Frontend Documentation

## Overview

The robots.txt file is automatically served by the application at `/robots.txt`. This file tells search engine crawlers which parts of your website they can and cannot access. The content is dynamically managed through the admin panel.

## Endpoint Details

### URL
```
GET /robots.txt
```

### Content Type
```
text/plain; charset=UTF-8
```

### HTTP Status
- **200 OK** - Content served successfully
- **500 Internal Server Error** - Server error (rare)

### Access
- **Public:** No authentication required
- **Caching:** Not cached (content can change dynamically)
- **CORS:** Not applicable (plain text response)

## Content Types

### Default Robots.txt Content

When the admin setting "Use Default Robots.txt" is enabled, the following content is served:

```
User-agent: *
Allow: /

Disallow: /admin/
Disallow: /storage/private/
Disallow: /nova/
Disallow: /horizon/
Disallow: /telescope/

Sitemap: https://your-domain.com/sitemap.xml
```

**What this means:**
- `User-agent: *` - Applies to all web crawlers
- `Allow: /` - Allows crawling of all public content
- `Disallow: /admin/` - Blocks admin panel from crawling
- `Disallow: /storage/private/` - Blocks private file storage
- `Disallow: /nova/` - Blocks Laravel Nova admin (if used)
- `Disallow: /horizon/` - Blocks Laravel Horizon (if used)
- `Disallow: /telescope/` - Blocks Laravel Telescope (if used)
- `Sitemap: ...` - Tells crawlers where to find the XML sitemap

### Custom Robots.txt Content

When the admin setting "Use Default Robots.txt" is disabled, the custom content entered in the admin panel is served. This allows for:

- Specific crawler permissions
- Blocking sensitive areas
- SEO optimization
- Integration with analytics tools

## Usage Examples

### Basic Access

**Direct URL Access:**
```
https://your-domain.com/robots.txt
```

**Browser:**
- Open your website URL and add `/robots.txt`
- Example: `https://example.com/robots.txt`

**Command Line:**
```bash
curl https://your-domain.com/robots.txt
```

### Testing the Content

**Check Content Type:**
```bash
curl -I https://your-domain.com/robots.txt
# Expected: Content-Type: text/plain; charset=UTF-8
```

**View Raw Content:**
```bash
curl -s https://your-domain.com/robots.txt | cat
```

**Validate Syntax:**
```bash
# Check for proper line endings
curl -s https://your-domain.com/robots.txt | hexdump -C
```

### Integration with SEO Tools

**Google Search Console:**
1. Submit your robots.txt URL in Google Search Console
2. Use the robots.txt tester to validate syntax
3. Monitor crawl errors and blocked resources

**Other SEO Tools:**
- Bing Webmaster Tools
- Yandex Webmaster
- Screaming Frog SEO Spider
- Various robots.txt validators online

## Content Management

### Who Controls the Content

The robots.txt content is managed by website administrators through the admin panel:

1. **Location:** Admin Panel → App Settings → Robots.txt Settings
2. **Permissions:** Requires admin access
3. **Changes:** Take effect immediately (not cached)

### Content Update Process

1. Admin logs into `/admin`
2. Navigates to App Settings
3. Modifies robots.txt settings
4. Saves changes
5. Content is immediately available at `/robots.txt`

## Common Use Cases

### E-commerce Site
```
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /checkout/
Disallow: /account/
Disallow: /api/private/

Sitemap: https://shop.example.com/sitemap.xml
```

### Blog/Content Site
```
User-agent: *
Allow: /

Disallow: /admin/
Disallow: /wp-admin/  # If using WordPress
Disallow: /wp-includes/
Disallow: /private/
Disallow: /drafts/

Sitemap: https://blog.example.com/sitemap.xml
```

### API-Heavy Application
```
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /api/admin/
Disallow: /api/private/
Disallow: /storage/private/

# Allow specific crawlers
User-agent: Googlebot
Allow: /api/public/

Sitemap: https://api.example.com/sitemap.xml
```

## Technical Details

### Response Headers
```
HTTP/1.1 200 OK
Content-Type: text/plain; charset=UTF-8
Cache-Control: no-cache, private
```

### Character Encoding
- UTF-8 encoding
- Plain text format
- No HTML or special formatting

### Performance
- Fast response (database query + string return)
- No heavy processing
- Suitable for high-traffic sites

## Troubleshooting

### Content Not Updating

**Issue:** Changes in admin panel not reflected in robots.txt

**Solutions:**
1. Clear application cache: `php artisan cache:clear`
2. Clear config cache: `php artisan config:clear`
3. Check database directly for settings
4. Verify admin panel changes were saved

### 404 Error

**Issue:** `/robots.txt` returns 404

**Solutions:**
1. Check route registration in `routes/web.php`
2. Verify `RobotsTxtController` exists
3. Ensure migration was run
4. Check web server configuration

### Wrong Content Type

**Issue:** Browser shows HTML instead of plain text

**Solutions:**
1. Check controller response headers
2. Verify `Content-Type: text/plain` is set
3. Clear browser cache
4. Test with curl command

### Syntax Errors

**Issue:** Search engines report robots.txt syntax errors

**Solutions:**
1. Validate syntax at Google Robots.txt Tester
2. Check for proper line breaks
3. Ensure no invalid characters
4. Review custom content in admin panel

## Integration Examples

### JavaScript (Frontend)
```javascript
// Check if robots.txt is accessible
fetch('/robots.txt')
  .then(response => response.text())
  .then(content => console.log('Robots.txt content:', content))
  .catch(error => console.error('Error fetching robots.txt:', error));
```

### PHP (Backend Integration)
```php
// In your application code
$robotsUrl = config('app.url') . '/robots.txt';
$content = file_get_contents($robotsUrl);
```

### SEO Monitoring Tools
```javascript
// Example: Add to your SEO monitoring script
const robotsTxtUrl = 'https://your-domain.com/robots.txt';

fetch(robotsTxtUrl)
  .then(response => response.text())
  .then(content => {
    // Analyze content for SEO compliance
    if (!content.includes('Sitemap:')) {
      console.warn('Warning: Sitemap not referenced in robots.txt');
    }
  });
```

## Security Considerations

### Information Disclosure
- Never include sensitive paths in robots.txt
- Use robots.txt for guidance, not security
- Consider using both robots.txt and server-side access control

### Best Practices
- Regularly review robots.txt content
- Test changes with multiple search engines
- Monitor crawl errors in webmaster tools
- Keep content simple and clear

## Related Documentation

- [Backend Robots.txt Management](../documentation backend/robots-txt-management.md)
- [App Settings API](../api/app-settings.md)
- [SEO Guidelines](seo-guidelines.md)
- [Admin Panel Usage](admin-panel-usage.md)
