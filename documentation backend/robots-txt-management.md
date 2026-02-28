# Robots.txt Management - Backend Documentation

## Overview

The robots.txt management system allows administrators to dynamically control the robots.txt file for the website through the admin panel. This feature provides both default and custom robots.txt content options.

## Accessing Robots.txt Settings

### Admin Panel Location
1. Log in to the admin panel (`/admin`)
2. Navigate to **App Settings** (`/admin/app-settings`)
3. Scroll down to the **"Robots.txt Settings"** section

## Configuration Options

### Use Default Robots.txt
- **Checkbox:** "Use Default Robots.txt"
- **Default:** Enabled (checked)
- **Description:** When enabled, the system uses a pre-configured default robots.txt content
- **When to use:** Recommended for most websites with standard crawling requirements

### Custom Robots.txt Content
- **Textarea:** "Custom Robots.txt Content"
- **State:** Disabled when "Use Default Robots.txt" is checked
- **Description:** Allows entering custom robots.txt directives
- **Validation:** Accepts any valid robots.txt syntax

## Default Robots.txt Content

When "Use Default Robots.txt" is enabled, the following content is served:

```
User-agent: *
Allow: /

Disallow: /admin/
Disallow: /storage/private/
Disallow: /nova/
Disallow: /horizon/
Disallow: /telescope/

Sitemap: {frontend_url}/sitemap.xml
```

### Default Behavior Explanation
- **Allows all crawlers:** `User-agent: *` followed by `Allow: /`
- **Blocks admin areas:** Prevents crawling of admin, private storage, and development tools
- **Includes sitemap:** References the website's sitemap for better indexing
- **Dynamic sitemap URL:** Uses the frontend URL from app settings

## Custom Robots.txt Configuration

### When to Use Custom Content
- Specific SEO requirements
- Blocking certain sections of the website
- Allowing specific crawlers different permissions
- Integration with third-party SEO tools

### Syntax Requirements
- Follow standard robots.txt format
- Use proper User-agent directives
- Include Allow/Disallow rules
- Add Sitemap references
- Comment lines with `#`

### Example Custom Content
```
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /private/
Disallow: /api/private/
Disallow: /storage/private/

# Block specific crawlers
User-agent: BadBot
Disallow: /

# Allow Googlebot full access
User-agent: Googlebot
Allow: /

Sitemap: https://your-domain.com/sitemap.xml
```

## Database Storage

### Settings Storage
- **Table:** `app_settings`
- **Fields:**
  - `robots_txt_content` (TEXT, nullable) - Custom robots.txt content
  - `use_default_robots_txt` (BOOLEAN, default: true) - Toggle between default/custom

### Migration Details
The feature was added via migration `2026_02_28_062304_add_robots_txt_to_app_settings_table`

## Public Endpoint

### URL
`GET /robots.txt`

### Content-Type
`text/plain; charset=UTF-8`

### Response Behavior
- Returns default content when `use_default_robots_txt = true`
- Returns custom content when `use_default_robots_txt = false`
- Always returns plain text format
- Not cached (dynamic content)

## Best Practices

### Security Considerations
- Never allow crawling of admin areas
- Block private storage directories
- Consider blocking API endpoints if they contain sensitive data

### SEO Optimization
- Include sitemap reference for better indexing
- Allow legitimate crawlers full access
- Block malicious bots appropriately

### Maintenance
- Regularly review robots.txt content
- Test changes with Google Search Console
- Monitor crawl errors and adjust accordingly

## Troubleshooting

### Content Not Updating
1. Clear application cache: `php artisan cache:clear`
2. Clear config cache: `php artisan config:clear`
3. Check database for correct settings
4. Verify route is accessible

### Syntax Errors
1. Validate robots.txt syntax at [Google Robots.txt Tester](https://www.google.com/webmasters/tools/robots-testing-tool)
2. Check for proper line breaks and spacing
3. Ensure no invalid characters

### Admin Panel Issues
1. Verify user has proper permissions
2. Check Filament admin panel configuration
3. Ensure migration has been run

## API Integration

### Programmatic Access
```php
use App\Models\AppSetting;

// Get current settings
$settings = AppSetting::first();

// Check if using default
$isDefault = $settings->use_default_robots_txt;

// Get custom content
$customContent = $settings->robots_txt_content;
```

### Testing Endpoint
```bash
# Test robots.txt endpoint
curl -H "Accept: text/plain" https://your-domain.com/robots.txt

# Check content type
curl -I https://your-domain.com/robots.txt
```

## Related Documentation

- [Frontend Robots.txt Usage](../documentations frontend/robots-txt-frontend.md)
- [App Settings Configuration](app-settings.md)
- [SEO Settings](seo-settings.md)
