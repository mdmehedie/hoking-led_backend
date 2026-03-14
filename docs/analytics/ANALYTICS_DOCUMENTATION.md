# Advanced Analytics and Behavior Tracking System

This document describes the comprehensive analytics and behavior tracking system implemented with traffic analytics, heatmaps, custom event tracking, and Core Web Vitals monitoring.

## 🎯 Overview

The system provides:
- **Google Analytics 4 Integration**: Real-time API data fetching with caching
- **Custom Event Tracking**: JavaScript-based event collection with backend storage
- **Heatmap & Session Recording**: Integration with Hotjar, Microsoft Clarity, and FullStory
- **Core Web Vitals Monitoring**: LCP, CLS, and INP tracking with Google PageSpeed API
- **Performance Reporting Dashboard**: Comprehensive Filament interface with tabbed analytics views

## 📁 Files Structure

### **Core Services**
```
app/
├── Services/
│   ├── GA4Service.php              # Google Analytics API integration
│   └── CoreWebVitalsService.php  # Performance monitoring service
├── Http/Controllers/
│   └── AnalyticsController.php      # API endpoints for event tracking
└── Models/
    └── AnalyticsEvent.php           # Custom event tracking model
```

### **Filament Resources**
```
app/Filament/
├── Admin/Resources/
│   └── AnalyticsEventResource.php  # Event management interface
└── Pages/
    ├── AnalyticsDashboard.php         # Main dashboard with widgets
    ├── ComprehensiveAnalytics.php    # Advanced analytics interface
    └── AnalyticsSettings.php         # Configuration management
```

### **Frontend Components**
```
public/
├── js/
│   └── analytics-tracker.js       # JavaScript tracking system
└── views/filament/pages/
    ├── analytics-dashboard.blade.php
    ├── comprehensive-analytics.blade.php
    └── analytics-settings.blade.php
```

### **Database**
```
database/migrations/
└── 2026_03_07_060053_create_analytics_events_table.php
```

## 🚀 Features

### **1. Traffic Analytics**

#### GA4 Integration
- **API Methods**:
  - `getDashboardMetrics()`: Key metrics (sessions, pageviews, users, bounce rate)
  - `getPageViews()`: Daily page view data with date breakdown
  - `getSessions()`: Session data with engagement metrics
  - `getTopPages()`: Most visited pages with view counts
  - `getDeviceBreakdown()`: Mobile vs desktop analytics
  - `getTrafficSources()`: Traffic source analysis
  - `getRealTimeData()`: Real-time user activity

#### Caching Strategy
- **Redis Tags**: `ga4_analytics`, `page_views`, `sessions`, `devices`
- **TTL**: 1 hour for dashboard metrics
- **Cache Invalidation**: Automatic flush on settings changes

### **2. Custom Event Tracking**

#### JavaScript Tracker
- **Auto-initialization**: Automatic setup on page load
- **Event Types**:
  - Page views with referrer tracking
  - Click events with coordinates and element data
  - Form submissions with field analysis
  - Scroll depth milestones (25%, 50%, 75%, 90%)
- **Configuration**: Runtime configuration via `window.analyticsConfig`

#### Backend API
- **Endpoint**: `POST /api/analytics/track-event`
- **Data Structure**:
  ```json
  {
    "event_name": "button_click",
    "page": "/products",
    "url": "https://example.com/products",
    "parameters": {
      "button_text": "Buy Now",
      "product_id": "123"
    },
    "user_agent": "Mozilla/5.0...",
    "ip_address": "192.168.1.1",
    "user_id": 456
  }
  ```

### **3. Heatmap & Session Recording**

#### Supported Providers
- **Hotjar**: Session recording and heatmaps
- **Microsoft Clarity**: Free Microsoft analytics with heatmaps
- **FullStory**: Advanced session recording and conversion funnels

#### Implementation
- **Settings Management**: Filament configuration interface
- **Production Injection**: Middleware automatically injects scripts in production
- **Environment Detection**: Scripts only loaded in production environment

### **4. Core Web Vitals Monitoring**

#### Metrics Tracked
- **LCP (Largest Contentful Paint)**: Loading performance
- **CLS (Cumulative Layout Shift)**: Visual stability
- **INP (Interaction to Next Paint)**: Interactivity responsiveness

#### Data Sources
- **Google PageSpeed API**: Official performance metrics
- **Local Tracking**: Browser-based real-time monitoring
- **Historical Analysis**: Trend analysis and performance tracking

#### Performance Thresholds
```php
$LCP_GOOD = 2.5s;      // 2500ms
$LCP_NEEDS_IMPROVEMENT = 4.0s; // 4000ms

$CLS_GOOD = 0.1;        // 10% shift
$CLS_NEEDS_IMPROVEMENT = 0.25;   // 25% shift

$INP_GOOD = 200ms;       // 200ms
$INP_NEEDS_IMPROVEMENT = 500ms;  // 500ms
```

### **5. Dashboard Interfaces**

#### Analytics Dashboard
- **Overview Widgets**: Sessions, pageviews, users, bounce rate
- **Device Breakdown**: Mobile vs desktop usage
- **Top Pages**: Most visited content
- **Traffic Sources**: Referral and direct traffic analysis

#### Comprehensive Analytics
- **Tabbed Interface**: Traffic, Behavior, Performance sections
- **Funnel Analysis**: Conversion rate tracking with configurable steps
- **User Path Analysis**: Common user journey patterns
- **Performance Trends**: Historical Core Web Vitals data

## 🔧 Configuration

### Environment Variables
```env
# Google Analytics 4
GA4_PROPERTY_ID=properties/123456789
GA4_CREDENTIALS_PATH=storage/app/ga4-credentials.json

# Analytics Settings
ANALYTICS_GA4_ENABLED=false
ANALYTICS_HEATMAP_PROVIDER=none
ANALYTICS_HOTJAR_ID=
ANALYTICS_CLARITY_ID=
ANALYTICS_TRACK_PAGE_VIEWS=true
ANALYTICS_TRACK_CLICKS=true
ANALYTICS_TRACK_FORMS=true
ANALYTICS_TRACK_SCROLLING=true
ANALYTICS_TRACK_CORE_WEB_VITALS=true
ANALYTICS_DEBUG_MODE=false

# Core Web Vitals
ANALYTICS_VITALS_PROVIDER=custom
ANALYTICS_PAGESPEED_API_KEY=
ANALYTICS_MONITORING_URLS=/
```

### Service Configuration
```php
// config/services.php
'ga4' => [
    'property_id' => env('GA4_PROPERTY_ID'),
    'credentials_path' => env('GA4_CREDENTIALS_PATH'),
],
```

## 📊 API Endpoints

### Analytics Events
```php
// Track custom events
POST /api/analytics/track-event
Content-Type: application/json

{
  "event_name": "button_click",
  "page": "/products",
  "url": "https://example.com/products",
  "parameters": {
    "button_text": "Buy Now"
  }
}

// Get analytics statistics
GET /api/analytics/stats?start_date=2024-01-01&end_date=2024-01-31

// Get funnel data
POST /api/analytics/funnel
{
  "steps": [
    {"event_name": "page_view", "page": "/products"},
    {"event_name": "click", "parameters": {"element": "add_to_cart"}},
    {"event_name": "form_submit", "page": "/checkout"}
  ]
}
```

## 🎨 Frontend Integration

### Include JavaScript Tracker
```html
<!-- In your main layout -->
<script src="/js/analytics-tracker.js"></script>

<!-- Or with configuration -->
<script>
window.analyticsConfig = {
    trackPageViews: true,
    trackClicks: true,
    trackForms: true,
    trackScrolling: true,
    debug: false
};
</script>
<script src="/js/analytics-tracker.js"></script>
```

### Manual Event Tracking
```javascript
// Track button clicks
AnalyticsTracker.track('button_click', {
    button_text: 'Buy Now',
    product_id: '123',
    price: 99.99
});

// Track form submissions
AnalyticsTracker.track('form_submit', {
    form_name: 'contact',
    fields_count: 3
});

// Track custom events
AnalyticsTracker.track('video_play', {
    video_id: 'abc123',
    duration: 120,
    autoplay: true
});
```

## 🔍 Monitoring & Debugging

### Debug Mode
Enable debug mode to see console logs:
```javascript
window.analyticsConfig = {
    debug: true
};
```

### Performance Monitoring
```bash
# Fetch Core Web Vitals data
php artisan analytics:fetch-core-web-vitals --urls=/,/about,/contact

# Monitor specific URLs
php artisan analytics:fetch-core-web-vitals --urls="https://example.com,https://example.com/about"
```

### Cache Management
```bash
# Clear analytics cache
php artisan cache:clear --tags=ga4_analytics

# Clear all cache
php artisan cache:clear
```

## 📈 Usage Examples

### Custom Event Tracking
```php
// In your Blade templates
<button onclick="AnalyticsTracker.track('cta_click', {location: 'header'})">
    Buy Now
</button>

<form onsubmit="AnalyticsTracker.trackFormSubmit(this)">
    <input type="email" name="email">
    <button type="submit">Subscribe</button>
</form>
```

### Funnel Analysis
Define conversion funnels in the backend:
```php
// Example: Purchase funnel
$funnelSteps = [
    ['event_name' => 'page_view', 'page' => '/products', 'label' => 'View Products'],
    ['event_name' => 'click', 'parameters' => ['element' => 'add_to_cart'], 'label' => 'Add to Cart'],
    ['event_name' => 'page_view', 'page' => '/checkout', 'label' => 'View Checkout'],
    ['event_name' => 'form_submit', 'page' => '/checkout', 'label' => 'Complete Purchase']
];
```

### Performance Monitoring
```javascript
// Core Web Vitals are automatically tracked
// View data in Performance tab of Comprehensive Analytics

// Manual performance tracking
AnalyticsTracker.track('performance_metric', {
    metric: 'custom_load_time',
    value: 1500,
    url: window.location.href
});
```

## 🛠️ Troubleshooting

### Common Issues

#### GA4 API Not Working
1. **Check Credentials**: Ensure service account JSON file is accessible
2. **Property ID**: Verify GA4 Property ID format
3. **API Quotas**: Check Google Cloud Console for usage limits
4. **Cache Issues**: Clear cache: `php artisan cache:clear`

#### Events Not Tracking
1. **JavaScript Errors**: Check browser console for script errors
2. **CORS Issues**: Verify API endpoint accessibility
3. **CSRF Token**: Ensure token is included in requests
4. **Middleware**: Verify `InjectAnalyticsTracking` is registered

#### Performance Data Missing
1. **PageSpeed API**: Check API key configuration
2. **Monitoring URLs**: Verify URLs are correctly configured
3. **Browser Support**: Core Web Vitals require modern browsers

### Debug Commands
```bash
# Test GA4 connection
php artisan tinker
>>> app(GA4Service::class)->getDashboardMetrics();

# Test event tracking
php artisan tinker
>>> AnalyticsEvent::create([
...     'event_name' => 'test_event',
...     'parameters' => ['test' => true]
... ]);

# Check cache tags
php artisan tinker
>>> Cache::tags(['ga4_analytics'])->get('ga4_dashboard_7d');
```

## 🚀 Deployment

### Production Setup
1. **Configure Environment Variables**: Set all required `.env` variables
2. **Run Migrations**: `php artisan migrate`
3. **Clear Cache**: `php artisan cache:clear`
4. **Schedule Commands**: Add Core Web Vitals fetching to cron:
   ```bash
   # Every 6 hours
   0 */6 * * * php /path/to/artisan analytics:fetch-core-web-vitals
   ```

### Security Considerations
- **PII Protection**: Avoid collecting personally identifiable information
- **Data Retention**: Implement data cleanup policies
- **Access Control**: Secure analytics endpoints with authentication
- **Rate Limiting**: Prevent abuse of tracking endpoints

## 📚 Dependencies

### Required Packages
- `google/analytics-data`: GA4 API client
- `google/apiclient`: Google API client library
- `predis/predis`: Redis client (already installed)

### Laravel Features Used
- **Filament v5**: Admin interface framework
- **Redis**: Caching and session storage
- **Eloquent**: Database ORM and relationships
- **Queue System**: Background job processing
- **Middleware**: Request/response processing

## 🔄 Version History

### v1.0.0 (2025-03-07)
- Initial implementation
- GA4 API integration
- Custom event tracking system
- Heatmap and session recording support
- Core Web Vitals monitoring
- Comprehensive Filament dashboards
- Performance monitoring and reporting

---

*Last updated: March 7, 2025*
