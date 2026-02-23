# Sitemap Documentation

This document explains how to use the XML sitemap feature implemented in the Honking LED Backend application.

## Overview

The application generates an XML sitemap that includes URLs for all products, categories, blogs, case studies, news articles, and pages. This helps search engines like Google discover and index your content more effectively.

The sitemap is generated using the `spatie/laravel-sitemap` package and includes the following features:
- Automatic daily generation (if enabled)
- Manual generation via Artisan command
- Last modification dates for each URL
- SEO-optimized URLs based on model slugs

## Automatic Generation

### Enabling/Disabling Sitemap Generation

1. Go to the Admin Panel (`/admin`)
2. Navigate to "App Settings"
3. Find the "SEO Settings" section
4. Toggle the "Enable Sitemap Generation" option
5. Save the settings

When enabled, the sitemap will be automatically generated daily at midnight via Laravel's scheduler.

### Scheduler Setup

Ensure your server has Laravel's scheduler set up to run the daily command:

```bash
# Add this to your crontab (on Linux/Unix servers)
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

On Windows servers, you may need to use Task Scheduler or similar.

## Manual Generation

You can manually generate the sitemap at any time using the Artisan command:

```bash
php artisan app:generate-sitemap
```

This will:
1. Collect all published/active records from products, categories, blogs, case studies, news, and pages
2. Generate URLs using each model's `getUrl()` method
3. Include the last updated timestamp for each URL
4. Save the XML file to `public/sitemap.xml`

## Accessing the Sitemap

Once generated, the sitemap is available at:
```
https://yourdomain.com/sitemap.xml
```

You can submit this URL to search engines like Google Search Console, Bing Webmaster Tools, etc.

## Sitemap Structure

The generated sitemap follows the standard XML sitemap protocol:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://yourdomain.com/products/product-slug</loc>
        <lastmod>2026-02-23</lastmod>
    </url>
    <url>
        <loc>https://yourdomain.com/categories/category-slug</loc>
        <lastmod>2026-02-23</lastmod>
    </url>
    <!-- More URLs for blogs, case studies, news, pages -->
</urlset>
```

## Included Content

The sitemap includes URLs for:
- **Products**: `/products/{slug}`
- **Categories**: `/categories/{slug}`
- **Blogs**: `/blog/{slug}`
- **Case Studies**: `/case-studies/{slug}`
- **News**: `/news/{slug}`
- **Pages**: `/pages/{slug}`

Only models with valid slugs and appropriate status (published/active) are included.

## URL Generation

Each model implements a `getUrl()` method that constructs the full URL:

```php
public function getUrl(): string
{
    return url('/products/' . $this->slug); // Example for Product
}
```

The URLs are generated using Laravel's `url()` helper to ensure correct domain and protocol.

## Troubleshooting

### Sitemap Not Generating Automatically
- Check that "Enable Sitemap Generation" is toggled on in App Settings
- Verify that Laravel's scheduler is running on your server
- Check application logs for any errors during scheduled command execution

### Manual Generation Errors
- Ensure all models have valid slugs
- Check file permissions for `public/` directory
- Verify database connectivity

### Sitemap Not Found
- Run the manual generation command: `php artisan app:generate-sitemap`
- Check that `public/sitemap.xml` exists and is readable
- Clear any caching mechanisms if using CDN or reverse proxy

## Customization

### Adding More Models
To include additional models in the sitemap:

1. Ensure the model has a `slug` field and `getUrl()` method
2. Update the `GenerateSitemap` command in `app/Console/Commands/GenerateSitemap.php` to include the new model

### Modifying URL Structure
Update the `getUrl()` method in the respective model to change URL patterns.

### Changing Generation Frequency
Modify the schedule in `app/Console/Kernel.php`:

```php
// Change from daily() to hourly(), weekly(), etc.
$schedule->command('app:generate-sitemap')->hourly();
```

## SEO Benefits

- Helps search engines discover all your content
- Provides last modification dates for better crawling prioritization
- Improves overall site indexation
- Can help with rich snippets and search result features

## Support

If you encounter issues with the sitemap generation, check the Laravel logs and ensure all dependencies are properly installed.
