# International SEO Documentation

This directory contains comprehensive documentation for the international SEO implementation in the Laravel application.

## 📁 Documentation Structure

```
documentation/international-seo/
├── README.md                    # This file - Overview and quick links
├── INTERNATIONAL_SEO.md         # Complete implementation overview
├── FRONTEND_API_USAGE.md       # Frontend developer guide
└── BACKEND_USAGE.md            # Backend developer guide
```

## 🚀 Quick Start

### For Frontend Developers
Start with **[FRONTEND_API_USAGE.md](./FRONTEND_API_USAGE.md)** to learn:
- How to consume API responses with alternates
- Implement hreflang tags in React, Vue, or vanilla JS
- Region detection and navigation
- SEO best practices

### For Backend Developers  
Start with **[BACKEND_USAGE.md](./BACKEND_USAGE.md)** to learn:
- Managing regions via admin panel
- Database operations and migrations
- API development and customization
- Sitemap generation

### For Complete Overview
Read **[INTERNATIONAL_SEO.md](./INTERNATIONAL_SEO.md)** for:
- Full feature list and architecture
- Implementation details
- Configuration options
- Migration guide

## 📋 Key Features Implemented

✅ **Automatic hreflang tags** in API responses  
✅ **Region-specific landing pages** with URL structure  
✅ **Enhanced sitemap generation** with hreflang and regional support  
✅ **Admin interface** for region management  
✅ **Content region targeting** for all models  
✅ **Default region configuration**  
✅ **Comprehensive documentation**  

## 🌍 Supported Regions

The system comes pre-configured with these regions:
- 🇺🇸 **United States** (us) - Default
- 🇬🇧 **United Kingdom** (uk)  
- 🇪🇺 **European Union** (eu)
- 🇨🇦 **Canada** (ca)
- 🇦🇺 **Australia** (au)

## 🔗 Quick Links

### API Endpoints
```bash
# Products with alternates
GET /api/v1/products
GET /api/v1/products/{slug}

# Other content types
GET /api/v1/blogs
GET /api/v1/case-studies
GET /api/v1/news
GET /api/v1/pages
```

### Admin Panel
- **Regions Management**: `/admin/regions` (View-only, use CLI for CRUD)
- **App Settings**: `/admin/app-settings` (International SEO section)

### Command Line Management
```bash
# Region management (recommended for CRUD)
php artisan regions:list
php artisan regions:list create
php artisan regions:list delete

# Sitemap generation
php artisan app:generate-sitemap
php artisan app:generate-sitemap --region=us
```

## 📖 Documentation Summary

| Document | Audience | Content |
|----------|----------|---------|
| [INTERNATIONAL_SEO.md](./INTERNATIONAL_SEO.md) | All stakeholders | Complete overview, architecture, features |
| [FRONTEND_API_USAGE.md](./FRONTEND_API_USAGE.md) | Frontend developers | API consumption, hreflang implementation, examples |
| [BACKEND_USAGE.md](./BACKEND_USAGE.md) | Backend developers | Admin usage, database, API development, troubleshooting |

## � Configuration

### Environment Variables
```env
DEFAULT_REGION=us
SUPPORTED_LOCALES=en,es,fr,de
ENABLE_REGIONAL_SITEMAPS=true
```

### Management Options

| Task | Method | Command/Location | Notes |
|------|--------|------------------|-------|
| **View Regions** | Filament Admin | `/admin/regions` | Table view with filtering, sorting |
| **Add New Region** | Filament Admin | `/admin/regions` | "New Region" button with form |
| **Edit Regions** | Filament Admin | `/admin/regions` | Edit button in table rows |
| **Delete Regions** | Filament Admin | `/admin/regions` | Delete button with confirmation |
| **Bulk Operations** | Filament Admin | `/admin/regions` | Select multiple regions |
| **List Regions** | Command Line | `php artisan regions:list` | Status overview |
| **Default Region** | App Settings | `/admin/app-settings` | Dropdown selection |
| **Region Associations** | Tinker | `php artisan tinker` | Content-region links |
| **Advanced Operations** | Tinker | `php artisan tinker` | Batch updates/creation |

### Database Tables
- `regions` - Region definitions
- `{model}_regions` - Content-region relationships
- `app_settings` - Default region setting

## �🛠️ Implementation Examples

### Frontend - React Hook
```jsx
const { product } = useProduct(slug);
// product.alternates contains hreflang data
```

### Backend - Region Management
```bash
# Create new region
php artisan regions:list create

# List all regions
php artisan regions:list

# Associate content with regions
php artisan tinker
>>> $product->regions()->sync(['us', 'uk', 'eu']);
```

### API Response Format
```json
{
  "id": 1,
  "title": "Product Name",
  "alternates": [
    {"locale": "en", "url": "https://example.com/products/product"},
    {"locale": "es", "url": "https://example.com/es/products/product"}
  ]
}
```

## 🔧 Configuration

### Environment Variables
```env
DEFAULT_REGION=us
SUPPORTED_LOCALES=en,es,fr,de
ENABLE_REGIONAL_SITEMAPS=true
```

### Database Tables
- `regions` - Region definitions
- `{model}_regions` - Content-region relationships
- `app_settings` - Default region setting

## 📞 Support

For questions or issues:
1. Check the relevant documentation file
2. Review troubleshooting sections
3. Test with provided debug commands
4. Contact the development team

---

**Last Updated**: March 9, 2026  
**Version**: 1.0.0  
**Laravel Version**: 12.x
