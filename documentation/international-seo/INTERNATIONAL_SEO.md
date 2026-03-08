# International SEO Implementation

This document outlines the comprehensive international SEO implementation that enhances the Laravel application with multilingual and regional targeting capabilities.

## Overview

The implementation provides:

1. **Automatic hreflang tags** in API responses
2. **Region-specific landing pages** 
3. **Enhanced sitemap generation** with hreflang and regional sitemaps
4. **Admin controls** for managing regions and default settings
5. **Content region targeting** for all content models

## Features Implemented

### 1. Regions System

#### Database Structure
- `regions` table with country/region information
- Pivot tables for content-region relationships
- Region-specific fields in pages table

#### Region Model Features
- **Code**: Region identifier (e.g., 'us', 'uk', 'eu')
- **Name**: Display name (e.g., 'United States')
- **Currency**: Local currency code (e.g., 'USD', 'GBP')
- **Timezone**: Default timezone (e.g., 'America/New_York')
- **Language**: Primary language (e.g., 'en-US')
- **Active/Default status**: Enable/disable and set default region
- **Sort order**: Control display order

#### Admin Interface
- Filament resource for managing regions
- Form validation and helper text
- Bulk operations and reordering

### 2. Hreflang Implementation

#### API Enhancement
All content API responses now include an `alternates` array:

```json
{
  "id": 1,
  "title": "Sample Product",
  "slug": "sample-product",
  "alternates": [
    {
      "locale": "en",
      "url": "https://example.com/products/sample-product"
    },
    {
      "locale": "es",
      "url": "https://example.com/es/products/sample-product"
    }
  ]
}
```

#### Supported Content Types
- Products
- Blogs
- Case Studies
- News
- Pages

### 3. Enhanced Sitemap Generation

#### Main Sitemap Features
- **Hreflang tags**: Automatically includes alternates for each URL
- **Last modification dates**: Tracks content updates
- **Published content filtering**: Only includes published content

#### Region-Specific Sitemaps
- Separate sitemaps for each active region
- Region-targeted content only
- `sitemap-{region}.xml` format

#### Sitemap Index
- `sitemap-index.xml` references all sitemaps
- Includes main sitemap and all regional sitemaps

#### Usage
```bash
# Generate all sitemaps
php artisan app:generate-sitemap

# Generate specific region sitemap
php artisan app:generate-sitemap --region=us
```

### 4. Region-Specific Pages

#### Page Model Enhancement
- `region` field for targeting specific regions
- Updated `getUrl()` method for region-specific URLs
- URL format: `/{region}/pages/{slug}`

#### Examples
- Global: `/pages/about-us`
- US-specific: `/us/pages/about-us`
- UK-specific: `/uk/pages/about-us`

### 5. Content Region Targeting

#### Many-to-Many Relationships
All content models can be associated with multiple regions:
- Products ↔ Regions
- Blogs ↔ Regions  
- Case Studies ↔ Regions
- News ↔ Regions
- Pages ↔ Regions

#### Implementation
```php
// Get regions for a product
$regions = $product->regions;

// Get products for a region
$products = $region->products;

// Check if content is available in region
$available = $product->regions()->where('code', 'us')->exists();
```

### 6. Default Region Settings

#### Global Configuration
- **App Settings**: Default region selector in Filament
- **Fallback logic**: Uses configured default when no region specified
- **Database field**: `default_region` in `app_settings` table

## Frontend Integration Guide

### 1. Using Alternates for hreflang

```javascript
// API response includes alternates
const response = await fetch('/api/v1/products/sample-product');
const data = await response.json();

// Generate hreflang tags
data.alternates.forEach(alternate => {
  const link = document.createElement('link');
  link.rel = 'alternate';
  link.hreflang = alternate.locale;
  link.href = alternate.url;
  document.head.appendChild(link);
});
```

### 2. Region-Specific Routing

```javascript
// Detect user's region (from IP, preferences, etc.)
const userRegion = detectUserRegion(); // 'us', 'uk', etc.

// Navigate to region-specific page
if (userRegion && userRegion !== defaultRegion) {
  window.location.href = `/${userRegion}/products/sample-product`;
}
```

### 3. Sitemap URLs

- Main sitemap: `https://example.com/sitemap.xml`
- Region sitemaps: `https://example.com/sitemap-{region}.xml`
- Sitemap index: `https://example.com/sitemap-index.xml`

## SEO Best Practices Implemented

### 1. Hreflang Tags
- **Complete coverage**: All content has alternates for all active locales
- **Self-referencing**: Each page includes itself in alternates
- **Proper format**: Uses ISO language codes (en, es, fr, etc.)

### 2. Regional Targeting
- **ccTLD-like structure**: `/us/`, `/uk/` paths simulate country-specific domains
- **Content filtering**: Region-specific content only appears in regional sitemaps
- **Default fallback**: Graceful handling when no region specified

### 3. Sitemap Optimization
- **Multiple sitemaps**: Separate sitemaps for better organization
- **Sitemap index**: Helps search engines discover all sitemaps
- **Fresh content**: Includes last modification dates

## Configuration

### 1. Environment Variables
```env
# Default region (fallback)
DEFAULT_REGION=us

# Supported locales
SUPPORTED_LOCALES=en,es,fr,de
```

### 2. App Settings
Configure through Filament admin:
- Navigate to Settings → App Settings
- Set "Default Region" in International SEO section

### 3. Region Management
- Navigate to Regions in admin panel
- Add/edit regions with proper codes and settings
- Activate regions and set default

## Migration Guide

### 1. Database Migrations
```bash
# Run all international SEO migrations
php artisan migrate
```

### 2. Seed Default Regions
```bash
# Seed sample regions
php artisan db:seed --class=RegionSeeder
```

### 3. Update Existing Content
```php
// Associate existing content with default region
$defaultRegion = Region::where('is_default', true)->first();
Product::chunk(100, function ($products) use ($defaultRegion) {
    foreach ($products as $product) {
        $product->regions()->attach($defaultRegion->id);
    }
});
```

## Testing

### 1. API Testing
```bash
# Test alternates in API response
curl -X GET "http://localhost:8000/api/v1/products/1"

# Expected: alternates array in response
```

### 2. Sitemap Testing
```bash
# Generate sitemaps
php artisan app:generate-sitemap

# Check sitemap content
cat public/sitemap.xml
cat public/sitemap-us.xml
cat public/sitemap-index.xml
```

### 3. Region Pages
```bash
# Test region-specific URLs
curl -X GET "http://localhost:8000/us/pages/about-us"
curl -X GET "http://localhost:8000/uk/pages/about-us"
```

## Performance Considerations

### 1. Caching
- Region data cached using Redis
- Alternates generation optimized
- Sitemap generation can be scheduled

### 2. Database Optimization
- Proper indexing on region relationships
- Efficient queries for region-specific content
- Lazy loading for region relationships

### 3. CDN Integration
- Region-specific content can be served from edge locations
- Sitemaps cached at CDN level
- API responses with appropriate cache headers

## Future Enhancements

### 1. Automatic Region Detection
- IP-based geolocation
- Browser language preferences
- User selection with cookies

### 2. Advanced Targeting
- City-level targeting
- Language combinations (en-US, en-GB)
- Custom region groups

### 3. Analytics Integration
- Track regional performance
- hreflang tag validation
- Regional conversion tracking

## Troubleshooting

### 1. Common Issues
- **Missing alternates**: Check locale configuration and active status
- **Region pages 404**: Ensure region is active and page has region association
- **Sitemap errors**: Verify content is published and has proper relationships

### 2. Debug Commands
```bash
# Check active regions
php artisan tinker
>>> Region::activeCodes();

# Check content regions
>>> Product::first()->regions()->pluck('code');

# Test alternates generation
>>> Product::first()->getAlternates();
```

This implementation provides a robust foundation for international SEO while maintaining flexibility for future enhancements.
