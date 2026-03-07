# Backend Documentation - Analytics System

## 📋 Overview

The analytics backend provides comprehensive tracking and reporting capabilities through RESTful APIs, services, and Filament interfaces.

## 🔧 API Endpoints

### Track Custom Events
```http
POST /api/analytics/track-event
Content-Type: application/json

{
  "event_name": "button_click",
  "page": "/products",
  "url": "https://example.com/products",
  "parameters": {
    "button_text": "Buy Now",
    "product_id": "123",
    "price": 99.99
  }
}
```

**Response:**
```json
{
  "success": true,
  "event_id": 12345,
  "message": "Event tracked successfully"
}
```

### Get Analytics Statistics
```http
GET /api/analytics/stats?start_date=2024-01-01&end_date=2024-01-31
```

**Response:**
```json
{
  "total_events": 15420,
  "unique_pages": 45,
  "unique_users": 892,
  "top_events": [
    {"event_name": "page_view", "count": 12000},
    {"event_name": "click", "count": 2500},
    {"event_name": "form_submit", "count": 920}
  ],
  "top_pages": [
    {"page": "/", "count": 5000},
    {"page": "/products", "count": 3000}
  ],
  "device_stats": {
    "mobile": 8000,
    "desktop": 7420
  }
}
```

### Get Funnel Analysis
```http
POST /api/analytics/funnel
Content-Type: application/json

{
  "steps": [
    {"event_name": "page_view", "page": "/products"},
    {"event_name": "click", "parameters": {"element": "add_to_cart"}},
    {"event_name": "page_view", "page": "/checkout"},
    {"event_name": "form_submit", "page": "/checkout"}
  ],
  "start_date": "2024-01-01",
  "end_date": "2024-01-31"
}
```

**Response:**
```json
{
  "success": true,
  "funnel_data": [
    {
      "step": 1,
      "name": "page_view",
      "page": "/products",
      "unique_users": 1000,
      "conversion_rate": 100,
      "drop_off_rate": 0
    },
    {
      "step": 2,
      "name": "click",
      "unique_users": 450,
      "conversion_rate": 45,
      "drop_off_rate": 55
    }
  ]
}
```

## 🏗️ Services

### GA4Service
```php
use App\Services\GA4Service;

$ga4 = app(GA4Service::class);

// Get dashboard metrics
$metrics = $ga4->getDashboardMetrics(7);
// Returns: ['sessions' => 1500, 'page_views' => 5000, ...]

// Get top pages
$pages = $ga4->getTopPages(7, 10);
// Returns: [['page' => '/', 'page_views' => 1000], ...]

// Get device breakdown
$devices = $ga4->getDeviceBreakdown(7);
// Returns: [['device' => 'desktop', 'sessions' => 800], ...]
```

### CoreWebVitalsService
```php
use App\Services\CoreWebVitalsService;

$vitals = app(CoreWebVitalsService::class);

// Get local vitals data
$data = $vitals->getLocalVitalsData(7);
// Returns: ['lcp' => [...], 'cls' => [...], 'inp' => [...]]

// Get PageSpeed data
$pagespeed = $vitals->getPageSpeedData('https://example.com');
// Returns: ['overall_score' => 85, 'core_web_vitals' => [...]]
```

## 📊 Database Models

### AnalyticsEvent
```php
use App\Models\AnalyticsEvent;

// Create event
$event = AnalyticsEvent::create([
    'event_name' => 'button_click',
    'page' => '/products',
    'parameters' => ['button_text' => 'Buy Now'],
    'user_id' => auth()->id()
]);

// Query events
$events = AnalyticsEvent::byName('click')
    ->betweenDates(now()->subDays(7), now())
    ->get();

// Get user events
$userEvents = AnalyticsEvent::byUser(auth()->id())
    ->get();
```

## 🔧 Configuration

### Environment Variables
```env
# GA4 Configuration
GA4_PROPERTY_ID=properties/123456789
GA4_CREDENTIALS_PATH=storage/app/ga4-credentials.json

# Analytics Settings
ANALYTICS_GA4_ENABLED=true
ANALYTICS_HEATMAP_PROVIDER=hotjar
ANALYTICS_HOTJAR_ID=123456
ANALYTICS_TRACK_PAGE_VIEWS=true
ANALYTICS_TRACK_CLICKS=true
ANALYTICS_TRACK_FORMS=true
```

### Settings Management
```php
// Get settings
$ga4Enabled = setting('analytics.ga4_enabled', false);
$hotjarId = setting('analytics.hotjar_id');

// Update settings
setting('analytics.ga4_enabled', true);
setting('analytics.hotjar_id', '123456');
```

## 🎯 Use Cases

### Case 1: E-commerce Conversion Tracking
```php
// Track product views
AnalyticsEvent::create([
    'event_name' => 'product_view',
    'parameters' => ['product_id' => $product->id, 'price' => $product->price]
]);

// Track add to cart
AnalyticsEvent::create([
    'event_name' => 'add_to_cart',
    'parameters' => ['product_id' => $product->id, 'quantity' => $quantity]
]);

// Track purchase
AnalyticsEvent::create([
    'event_name' => 'purchase',
    'parameters' => [
        'order_id' => $order->id,
        'total' => $order->total,
        'items' => $order->items->count()
    ]
]);
```

### Case 2: Content Engagement Analysis
```php
// Track video plays
AnalyticsEvent::create([
    'event_name' => 'video_play',
    'parameters' => [
        'video_id' => $video->id,
        'duration' => $video->duration,
        'autoplay' => true
    ]
]);

// Track article reading time
AnalyticsEvent::create([
    'event_name' => 'article_read',
    'parameters' => [
        'article_id' => $article->id,
        'reading_time' => $readingTime,
        'scroll_depth' => 95
    ]
]);
```

### Case 3: User Behavior Analysis
```php
// Get user journey
$userJourney = AnalyticsEvent::byUser($userId)
    ->byName('page_view')
    ->orderBy('event_time', 'asc')
    ->pluck('page');

// Get drop-off points
$dropOffs = AnalyticsEvent::selectRaw('
        page,
        COUNT(*) as views,
        COUNT(DISTINCT user_id) as unique_users,
        AVG(CASE WHEN parameters LIKE "%duration%" THEN JSON_EXTRACT(parameters, "$.duration") ELSE NULL END) as avg_time
    ')
    ->where('event_time', '>=', now()->subDays(30))
    ->groupBy('page')
    ->orderBy('views', 'desc')
    ->get();
```

## 🚀 Commands

### Fetch Core Web Vitals
```bash
# Fetch for default URLs
php artisan analytics:fetch-core-web-vitals

# Fetch for specific URLs
php artisan analytics:fetch-core-web-vitals --urls="/,/about,/contact"

# Schedule in cron
0 */6 * * * * php /path/to/artisan analytics:fetch-core-web-vitals
```

## 🔍 Monitoring & Debugging

### Cache Management
```php
// Clear analytics cache
Cache::tags(['ga4_analytics'])->flush();

// Clear specific cache
Cache::forget('ga4_dashboard_7d');
```

### Performance Monitoring
```php
// Monitor API performance
$startTime = microtime(true);
$metrics = $ga4->getDashboardMetrics(7);
$duration = microtime(true) - $startTime;

if ($duration > 5) {
    Log::warning('GA4 API slow response', ['duration' => $duration]);
}
```

## 🛡️ Security Considerations

### Data Privacy
```php
// Anonymize IP addresses
$event->ip_address = request()->ip();
$event->parameters = array_filter($event->parameters, function($value) {
    return !in_array($value, ['email', 'phone', 'ssn']);
});
```

### Rate Limiting
```php
// Implement rate limiting for tracking endpoint
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/api/analytics/track-event', [AnalyticsController::class, 'trackEvent']);
});
```

## 📈 Performance Optimization

### Database Indexes
```sql
-- Already implemented in migration
CREATE INDEX idx_events_name_time ON analytics_events(event_name, event_time);
CREATE INDEX idx_events_page_time ON analytics_events(page, event_time);
CREATE INDEX idx_events_user_time ON analytics_events(user_id, event_time);
```

### Caching Strategy
```php
// Cache expensive queries
$topPages = Cache::tags(['analytics', 'top_pages'])->remember('top_pages_7d', 3600, function () {
    return AnalyticsEvent::selectRaw('page, COUNT(*) as views')
        ->where('event_name', 'page_view')
        ->where('event_time', '>=', now()->subDays(7))
        ->groupBy('page')
        ->orderBy('views', 'desc')
        ->limit(10)
        ->get();
});
```

---

*Last updated: March 7, 2025*
