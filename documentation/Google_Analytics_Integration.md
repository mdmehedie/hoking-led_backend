# Google Analytics 4 Integration Documentation

## Overview

This application integrates Google Analytics 4 (GA4) to provide comprehensive analytics data within the Filament admin panel. The integration includes real-time metrics, historical data analysis, and traffic insights through dedicated dashboard widgets.

## Features

### 📊 Dashboard Widgets

- **Page Views Widget**: Displays total page views for the last 7 days
- **Top Visited Pages Widget**: Shows the most visited pages/products (last 30 days)
- **Traffic Sources Widget**: Displays traffic sources and user sessions (last 30 days)
- **SEO Dashboard Widget**: Provides SEO metrics and keyword ranking placeholders
- **Realtime Users Widget**: Shows currently active users (updated every minute)

### 🔄 Data Caching

- All GA4 data is cached for 1 hour to optimize performance and respect API rate limits
- Realtime data is cached for 1 minute for near-live updates
- Automatic cache invalidation ensures fresh data

### 🛡️ Error Handling

- Graceful fallbacks when GA4 is not configured
- Comprehensive error logging for debugging
- User-friendly error messages in the admin interface

## Setup and Configuration

### Prerequisites

1. **Google Cloud Project**: Create a project at [Google Cloud Console](https://console.cloud.google.com/)
2. **GA4 Property**: Set up Google Analytics 4 property for your website
3. **Google Analytics Data API**: Enable the API in your Google Cloud project

### 1. Create Google Cloud Service Account

```bash
# Navigate to Google Cloud Console
# Go to IAM & Admin > Service Accounts
# Click "Create Service Account"
# Give it a name like "ga4-analytics-service"
# Grant "Viewer" role for Google Analytics
```

### 2. Generate Service Account Key

```bash
# In Service Accounts, click on your new service account
# Go to "Keys" tab
# Click "Add Key" > "Create new key" > JSON
# Download the JSON key file
```

### 3. Grant GA4 Property Access

```bash
# Go to Google Analytics
# Admin > Property > Property Access Management
# Add your service account email as "Viewer"
```

### 4. Configure Environment Variables

Add these variables to your `.env` file:

```env
# Google Analytics 4 Configuration
GA4_PROPERTY_ID=your_ga4_property_id_here
GA4_CREDENTIALS_PATH=/absolute/path/to/your/service-account-key.json
```

### 5. Upload Credentials in Admin Panel

1. Go to **Admin Panel > Settings**
2. Find the **App Settings** resource
3. Upload your GA4 credentials JSON file
4. Enter your GA4 Property ID
5. Save the settings

## API Reference

### GA4Service Class

Located at: `app/Services/GA4Service.php`

#### Methods

##### `getPageViewsLast7Days()`
Returns total page views for the last 7 days.

```php
$ga4Service = new GA4Service();
$pageViews = $ga4Service->getPageViewsLast7Days();
// Returns: int
```

##### `getTopVisitedPages($limit = 10)`
Returns top visited pages with view counts.

```php
$ga4Service = new GA4Service();
$topPages = $ga4Service->getTopVisitedPages(10);
// Returns: array of ['page_path' => string, 'page_views' => int]
```

##### `getTrafficSources($limit = 10)`
Returns traffic sources with session and user data.

```php
$ga4Service = new GA4Service();
$sources = $ga4Service->getTrafficSources(10);
// Returns: array of ['source' => string, 'sessions' => int, 'users' => int]
```

##### `getRealtimeUsers()`
Returns currently active users count.

```php
$ga4Service = new GA4Service();
$activeUsers = $ga4Service->getRealtimeUsers();
// Returns: int
```

##### `testConnection()`
Tests GA4 API connectivity.

```php
$ga4Service = new GA4Service();
$isConnected = $ga4Service->testConnection();
// Returns: bool
```

## Widget Configuration

### PageViewsWidget

**Location**: `app/Filament/Admin/Widgets/PageViewsWidget.php`

Displays page views for the last 7 days in a stats overview format.

```php
// Automatic configuration - no additional setup needed
// Widget is registered in AdminPanelProvider
```

### TopVisitedPagesWidget

**Location**: `app/Filament/Admin/Widgets/TopVisitedPagesWidget.php`

Shows a table of top visited pages with rankings.

**Features:**
- Displays page paths and view counts
- Limited to top 10 pages by default
- Shows "No data" message when GA4 not configured

### TrafficSourcesWidget

**Location**: `app/Filament/Admin/Widgets/TrafficSourcesWidget.php`

Displays traffic source breakdown with sessions and users.

**Features:**
- Shows channel grouping (Organic Search, Direct, Social, etc.)
- Displays session and user counts
- Ordered by session count descending

### SEODashboardWidget

**Location**: `app/Filament/Admin/Widgets/SEODashboardWidget.php`

Provides SEO metrics overview with placeholders for future integration.

**Features:**
- Organic keywords count
- Top keyword rankings
- Average position tracking
- SEO score placeholder

### KeywordRankingWidget

**Location**: `app/Filament/Admin/Widgets/KeywordRankingWidget.php`

Table widget for keyword ranking data (currently shows placeholder).

## Database Schema

### App Settings Table

GA4 configuration is stored in the `app_settings` table:

```sql
-- Migration: 2026_02_24_094146_add_ga4_fields_to_app_settings.php
ALTER TABLE app_settings ADD COLUMN ga4_property_id VARCHAR(255) NULL;
ALTER TABLE app_settings ADD COLUMN ga4_credentials_file VARCHAR(255) NULL;
```

### Cache Keys

GA4 data is cached with these keys:
- `ga4_page_views_7d` - Page views data (1 hour)
- `ga4_top_pages_30d` - Top pages data (1 hour)
- `ga4_traffic_sources_30d` - Traffic sources data (1 hour)
- `ga4_realtime_users` - Realtime users (1 minute)

## Configuration Files

### Services Configuration

**Location**: `config/services.php`

```php
'ga4' => [
    'property_id' => env('GA4_PROPERTY_ID'),
    'credentials_path' => env('GA4_CREDENTIALS_PATH'),
],
```

### Environment Variables

**File**: `.env`

```env
# Required GA4 Configuration
GA4_PROPERTY_ID=123456789
GA4_CREDENTIALS_PATH=/var/www/storage/app/ga4-credentials.json
```

## Troubleshooting

### Common Issues

#### 1. "GA4 credentials not configured" Error

**Cause**: Missing or incorrect GA4 configuration in settings.

**Solution**:
1. Verify `.env` file has correct `GA4_PROPERTY_ID` and `GA4_CREDENTIALS_PATH`
2. Upload GA4 credentials JSON file in Admin Settings
3. Ensure file path is absolute and accessible

#### 2. "GA4 API error" in Logs

**Cause**: API permission issues or invalid credentials.

**Solution**:
1. Check Google Cloud service account has proper permissions
2. Verify GA4 property access is granted
3. Ensure credentials file is valid JSON
4. Check property ID is correct

#### 3. No Data in Widgets

**Cause**: GA4 property has no data or date range issues.

**Solution**:
1. Verify GA4 property has collected data
2. Check date ranges in GA4 service methods
3. Ensure property ID matches your GA4 property

#### 4. Permission Denied Errors

**Cause**: Service account lacks required permissions.

**Solution**:
1. Grant "Viewer" role in Google Analytics property
2. Ensure service account email is added to property access
3. Regenerate service account key if compromised

### Debug Commands

Test GA4 connection via Tinker:

```bash
php artisan tinker
```

```php
$service = new App\Services\GA4Service();
dd($service->testConnection());
```

Check cached data:

```php
Cache::get('ga4_page_views_7d')
```

Clear GA4 cache:

```php
Cache::forget('ga4_page_views_7d');
Cache::forget('ga4_top_pages_30d');
Cache::forget('ga4_traffic_sources_30d');
Cache::forget('ga4_realtime_users');
```

### Log Files

GA4 errors are logged to Laravel's log files:

```bash
# Check recent GA4 errors
tail -f storage/logs/laravel.log | grep GA4
```

## Security Considerations

### Credential Storage

- GA4 credentials JSON file is stored securely in the application
- File path is configured via environment variables
- Credentials are not exposed in version control

### API Permissions

- Service account has minimal required permissions ("Viewer" role)
- Access limited to GA4 Data API only
- No write permissions to GA4 property

### Data Privacy

- Analytics data is cached temporarily for performance
- No user-identifiable information is stored
- Compliance with Google Analytics data retention policies

## Performance Optimization

### Caching Strategy

- **Page Views**: 1 hour cache (relatively static data)
- **Top Pages/Sources**: 1 hour cache (daily summary data)
- **Realtime Users**: 1 minute cache (near-live data)

### API Rate Limiting

- Google Analytics Data API has rate limits
- Caching reduces API calls significantly
- Failed requests are logged but don't crash the application

### Widget Loading

- Widgets load asynchronously in Filament dashboard
- Error states don't block other widgets
- Graceful degradation when services are unavailable

## Future Enhancements

### Planned Features

1. **Custom Date Ranges**: Allow admin to select custom date ranges
2. **Advanced Filtering**: Filter analytics by page types, user segments
3. **Goal Tracking**: Display conversion goals and funnel data
4. **Real-time Alerts**: Notifications for traffic anomalies
5. **SEO Integration**: Connect with SEO tools for keyword data
6. **Export Functionality**: Export analytics data to CSV/PDF

### SEO Service Integration

The application includes placeholders for SEO service integration:

- **Keyword Ranking Widget**: Ready for external SEO API integration
- **SEO Dashboard Widget**: Structured for multiple SEO metrics
- **Configurable API Keys**: Framework for adding SEO service credentials

## Support

### Getting Help

1. Check this documentation first
2. Review Laravel logs for error details
3. Verify Google Cloud and GA4 configurations
4. Test API connectivity using debug commands

### Common Support Queries

- **Setup Issues**: Follow the setup guide step-by-step
- **Permission Errors**: Double-check service account configuration
- **No Data Display**: Verify GA4 property has collected data
- **Performance Issues**: Check caching and API rate limits

---

**Last Updated**: February 24, 2026
**Version**: 1.0.0
**Laravel Version**: 12.x
**Filament Version**: 3.x
