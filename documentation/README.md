# Documentation

Welcome to the documentation for the Laravel application with international SEO features.

## 📁 Documentation Structure

```
documentation/
├── README.md                           # This file - Main documentation index
└── international-seo/                  # International SEO documentation
    ├── README.md                       # International SEO overview and quick links
    ├── INTERNATIONAL_SEO.md            # Complete implementation overview
    ├── FRONTEND_API_USAGE.md          # Frontend developer guide
    └── BACKEND_USAGE.md               # Backend developer guide
```

## 🌍 International SEO Implementation

Our application includes comprehensive international SEO features:

### Key Features
- ✅ **Automatic hreflang tags** in API responses
- ✅ **Region-specific landing pages** 
- ✅ **Enhanced sitemap generation** with hreflang support
- ✅ **Admin interface** for region management
- ✅ **Content region targeting** for all models
- ✅ **Default region configuration**

### Quick Start
1. **Frontend Developers**: See [international-seo/FRONTEND_API_USAGE.md](./international-seo/FRONTEND_API_USAGE.md)
2. **Backend Developers**: See [international-seo/BACKEND_USAGE.md](./international-seo/BACKEND_USAGE.md)
3. **Complete Overview**: See [international-seo/INTERNATIONAL_SEO.md](./international-seo/INTERNATIONAL_SEO.md)

## 🚀 Quick Links

### API Documentation
- **Frontend API Usage**: [Frontend Guide](./international-seo/FRONTEND_API_USAGE.md)
- **Backend API Development**: [Backend Guide](./international-seo/BACKEND_USAGE.md)

### Admin Panel
- **Regions Management**: Available in Filament admin panel
- **App Settings**: International SEO configuration section

### Development Commands
```bash
# Generate sitemaps
php artisan app:generate-sitemap

# Region management (recommended for CRUD operations)
php artisan regions:list
php artisan regions:list create
php artisan regions:list delete

# Seed regions
php artisan db:seed --class=RegionSeeder

# Run migrations
php artisan migrate
```

## 📋 Other Documentation

Additional documentation may be added here as the application grows:

- API documentation
- Deployment guides
- Development setup
- Contributing guidelines

---

**Last Updated**: March 9, 2026  
**Application Version**: Laravel 12.x
