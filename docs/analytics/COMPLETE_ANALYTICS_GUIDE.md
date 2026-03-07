# Analytics System - Complete Implementation Guide

## 📋 Table of Contents

1. [Backend Documentation](./BACKEND_DOCUMENTATION.md)
2. [Frontend Documentation](./FRONTEND_DOCUMENTATION.md)
3. [Quick Start Examples](#quick-start-examples)
4. [Use Cases](#real-world-use-cases)
5. [Troubleshooting](#troubleshooting)

## 🚀 Quick Start Examples

### Backend Integration
```php
// 1. Track custom events
use App\Models\AnalyticsEvent;

// Track a purchase
AnalyticsEvent::create([
    'event_name' => 'purchase',
    'page' => '/checkout/success',
    'parameters' => [
        'order_id' => $order->id,
        'total' => $order->total,
        'items' => $order->items->count()
    ],
    'user_id' => auth()->id()
]);

// 2. Get analytics data
$events = AnalyticsEvent::byName('purchase')
    ->betweenDates(now()->subDays(30), now())
    ->get();

// 3. Use GA4 service
use App\Services\GA4Service;

$ga4 = app(GA4Service::class);
$metrics = $ga4->getDashboardMetrics(7);
```

### Frontend Integration
```html
<!-- 1. Include tracker -->
<script src="/js/analytics-tracker.js"></script>

<!-- 2. Configure -->
<script>
window.analyticsConfig = {
    trackPageViews: true,
    trackClicks: true,
    trackForms: true,
    debug: true
};
</script>

<!-- 3. Track events -->
<script>
// Track button clicks
document.getElementById('buy-button').addEventListener('click', function() {
    AnalyticsTracker.track('purchase_attempt', {
        product_id: '123',
        price: 99.99,
        currency: 'USD'
    });
});

// Track form submissions
document.getElementById('contact-form').addEventListener('submit', function() {
    AnalyticsTracker.track('contact_form_submit', {
        form_name: 'contact',
        fields_count: 4
    });
});
</script>
```

## 🎯 Real-World Use Cases

### Use Case 1: E-commerce Platform

#### Backend Implementation
```php
// ProductController.php
public function show($id)
{
    $product = Product::find($id);
    
    // Track product view
    AnalyticsEvent::create([
        'event_name' => 'product_view',
        'page' => "/products/{$id}",
        'parameters' => [
            'product_id' => $product->id,
            'category' => $product->category,
            'price' => $product->price,
            'in_stock' => $product->stock > 0
        ],
        'user_id' => auth()->id()
    ]);
    
    return view('products.show', compact('product'));
}

// OrderController.php
public function store(Request $request)
{
    $order = Order::create($request->validated());
    
    // Track purchase
    AnalyticsEvent::create([
        'event_name' => 'purchase_completed',
        'page' => '/checkout',
        'parameters' => [
            'order_id' => $order->id,
            'total_amount' => $order->total,
            'items_count' => $order->items->count(),
            'payment_method' => $order->payment_method,
            'shipping_method' => $order->shipping_method
        ],
        'user_id' => auth()->id()
    ]);
    
    return redirect()->route('orders.show', $order);
}
```

#### Frontend Implementation
```javascript
// Add to cart tracking
function trackAddToCart(productId, quantity) {
    AnalyticsTracker.track('add_to_cart', {
        product_id: productId,
        quantity: quantity,
        timestamp: new Date().toISOString()
    });
}

// Checkout funnel tracking
function trackCheckoutStep(step, data) {
    AnalyticsTracker.track('checkout_step', {
        step_number: step,
        step_name: getStepName(step),
        total_steps: 4,
        completion_rate: (step / 4) * 100,
        ...data
    });
}

// Cart abandonment detection
let cartTimer = null;
function startCartTimer() {
    cartTimer = Date.now();
}

function detectCartAbandonment() {
    if (cartTimer && (Date.now() - cartTimer) > 300000) { // 5 minutes
        AnalyticsTracker.track('cart_abandoned', {
            time_in_cart: Math.round((Date.now() - cartTimer) / 1000),
            cart_value: getCartValue(),
            items_count: getCartItemCount()
        });
    }
}
```

### Use Case 2: SaaS Application

#### Backend Implementation
```php
// UserController.php
public function store(Request $request)
{
    $user = User::create($request->validated());
    
    // Track registration
    AnalyticsEvent::create([
        'event_name' => 'user_registered',
        'page' => '/register',
        'parameters' => [
            'registration_method' => 'email',
            'plan_selected' => $request->plan,
            'referral_source' => $request->get('ref'),
            'completion_time' => $request->get('completion_time')
        ],
        'user_id' => $user->id
    ]);
    
    return response()->json(['user' => $user]);
}

// FeatureUsageController.php
public function trackUsage(Request $request)
{
    AnalyticsEvent::create([
        'event_name' => 'feature_used',
        'page' => $request->get('page'),
        'parameters' => [
            'feature_name' => $request->get('feature'),
            'action' => $request->get('action'),
            'user_plan' => auth()->user()->plan,
            'usage_count' => $request->get('count', 1)
        ],
        'user_id' => auth()->id()
    ]);
    
    return response()->json(['success' => true]);
}
```

#### Frontend Implementation
```javascript
// Feature usage tracking
function trackFeatureUsage(feature, action, metadata = {}) {
    AnalyticsTracker.track('feature_interaction', {
        feature_name: feature,
        action: action,
        user_plan: getUserPlan(),
        session_duration: getSessionDuration(),
        ...metadata
    });
}

// API performance tracking
function trackApiCall(endpoint, responseTime, success) {
    AnalyticsTracker.track('api_call', {
        endpoint: endpoint,
        response_time: responseTime,
        success: success,
        user_authenticated: isAuthenticated(),
        rate_limit_remaining: getRateLimit()
    });
}

// Error tracking
function trackError(error, context) {
    AnalyticsTracker.track('application_error', {
        error_type: error.type,
        error_message: error.message,
        error_stack: error.stack,
        context: context,
        user_agent: navigator.userAgent,
        timestamp: new Date().toISOString()
    });
}
```

### Use Case 3: Content Platform

#### Backend Implementation
```php
// ArticleController.php
public function show($id)
{
    $article = Article::find($id);
    
    // Track article view
    AnalyticsEvent::create([
        'event_name' => 'article_view',
        'page' => "/articles/{$id}",
        'parameters' => [
            'article_id' => $article->id,
            'category' => $article->category,
            'author_id' => $article->author_id,
            'word_count' => $article->word_count,
            'reading_time_estimate' => $article->reading_time
        ],
        'user_id' => auth()->id()
    ]);
    
    return view('articles.show', compact('article'));
}

// CommentController.php
public function store(Request $request, $articleId)
{
    $comment = Comment::create([
        'article_id' => $articleId,
        'user_id' => auth()->id(),
        'content' => $request->content
    ]);
    
    // Track comment
    AnalyticsEvent::create([
        'event_name' => 'comment_posted',
        'page' => "/articles/{$articleId}",
        'parameters' => [
            'article_id' => $articleId,
            'comment_length' => strlen($request->content),
            'has_mentions' => str_contains($request->content, '@'),
            'reply_to' => $request->get('reply_to')
        ],
        'user_id' => auth()->id()
    ]);
    
    return back();
}
```

#### Frontend Implementation
```javascript
// Reading time tracking
function trackReadingTime(articleId) {
    const startTime = Date.now();
    let lastScrollPosition = 0;
    
    window.addEventListener('scroll', function() {
        const scrollPercent = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
        lastScrollPosition = Math.max(lastScrollPosition, scrollPercent);
    });
    
    window.addEventListener('beforeunload', function() {
        const readingTime = Math.round((Date.now() - startTime) / 1000);
        AnalyticsTracker.track('article_read', {
            article_id: articleId,
            reading_time: readingTime,
            scroll_depth: Math.round(lastScrollPosition),
            completion_rate: Math.round(lastScrollPosition)
        });
    });
}

// Social sharing tracking
function trackSocialShare(platform, contentId) {
    AnalyticsTracker.track('social_share', {
        platform: platform,
        content_id: contentId,
        content_type: 'article',
        share_method: 'button',
        user_followers: getUserFollowers()
    });
}

// Video engagement tracking
function trackVideoEngagement(video) {
    let milestones = [25, 50, 75, 90];
    let trackedMilestones = [];
    
    video.addEventListener('timeupdate', function() {
        const percent = (video.currentTime / video.duration) * 100;
        
        milestones.forEach(milestone => {
            if (percent >= milestone && !trackedMilestones.includes(milestone)) {
                AnalyticsTracker.track('video_milestone', {
                    video_id: video.dataset.id,
                    milestone_percent: milestone,
                    current_time: video.currentTime,
                    total_duration: video.duration
                });
                trackedMilestones.push(milestone);
            }
        });
    });
}
```

## 🔧 Configuration Examples

### Environment Setup
```env
# .env file
GA4_PROPERTY_ID=properties/123456789
GA4_CREDENTIALS_PATH=storage/app/ga4-credentials.json

ANALYTICS_GA4_ENABLED=true
ANALYTICS_HEATMAP_PROVIDER=hotjar
ANALYTICS_HOTJAR_ID=123456
ANALYTICS_TRACK_PAGE_VIEWS=true
ANALYTICS_TRACK_CLICKS=true
ANALYTICS_TRACK_FORMS=true
ANALYTICS_TRACK_SCROLLING=true
ANALYTICS_DEBUG_MODE=false
```

### Filament Configuration
```php
// app/Providers/Filament/AdminPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->navigationGroups([
            NavigationGroup::make('Analytics')
                ->items([
                    AnalyticsEventResource::class,
                    AnalyticsDashboard::class,
                    ComprehensiveAnalytics::class,
                    AnalyticsSettings::class,
                ]),
        ]);
}
```

## 📊 Dashboard Examples

### Custom Dashboard Widgets
```php
// Custom widget for conversion rates
class ConversionRateWidget extends Widget
{
    protected static string $view = 'filament.widgets.conversion-rate';
    
    protected function getData(): array
    {
        $conversions = AnalyticsEvent::byName('purchase')
            ->where('event_time', '>=', now()->subDays(30))
            ->count();
            
        $visitors = AnalyticsEvent::byName('page_view')
            ->where('event_time', '>=', now()->subDays(30))
            ->distinct('user_id')
            ->count();
            
        return [
            'conversion_rate' => $visitors > 0 ? round(($conversions / $visitors) * 100, 2) : 0,
            'conversions' => $conversions,
            'visitors' => $visitors
        ];
    }
}
```

### Custom Reports
```php
// Generate monthly report
public function generateMonthlyReport()
{
    $data = AnalyticsEvent::selectRaw('
            DATE(event_time) as date,
            COUNT(*) as total_events,
            COUNT(DISTINCT user_id) as unique_users,
            COUNT(DISTINCT page) as unique_pages
        ')
        ->where('event_time', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->get();
    
    return response()->json([
        'report' => $data,
        'period' => 'last_30_days',
        'generated_at' => now()->toISOString()
    ]);
}
```

## 🔍 Troubleshooting

### Common Issues

#### 1. Events Not Tracking
```javascript
// Check if tracker is loaded
if (typeof AnalyticsTracker === 'undefined') {
    console.error('Analytics tracker not loaded');
    // Load manually
    const script = document.createElement('script');
    script.src = '/js/analytics-tracker.js';
    document.head.appendChild(script);
}

// Check configuration
console.log('Analytics config:', window.analyticsConfig);
```

#### 2. Backend Errors
```php
// Check database connection
try {
    AnalyticsEvent::create([
        'event_name' => 'test',
        'parameters' => ['test' => true]
    ]);
    echo "Database connection working";
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage();
}

// Check API endpoint
Route::get('/test-analytics', function () {
    return response()->json([
        'database_connection' => DB::connection()->getPdo() ? 'OK' : 'Failed',
        'analytics_table' => Schema::hasTable('analytics_events') ? 'Exists' : 'Missing'
    ]);
});
```

#### 3. Performance Issues
```javascript
// Batch events for better performance
const eventBatch = [];
const BATCH_SIZE = 10;

function flushBatch() {
    if (eventBatch.length > 0) {
        AnalyticsTracker.track('batch_events', {
            events: eventBatch,
            batch_size: eventBatch.length
        });
        eventBatch.length = 0;
    }
}

// Flush every 5 seconds or when batch is full
setInterval(flushBatch, 5000);
```

### Debug Tools
```javascript
// Debug console
function debugAnalytics() {
    console.log('Analytics Tracker:', AnalyticsTracker);
    console.log('Configuration:', window.analyticsConfig);
    console.log('Recent Events:', localStorage.getItem('analytics_debug'));
}

// Test event
function testAnalytics() {
    AnalyticsTracker.track('test_event', {
        timestamp: new Date().toISOString(),
        random_id: Math.random(),
        test_data: 'debugging'
    });
}
```

## 📈 Performance Optimization

### Database Optimization
```sql
-- Add compound indexes for common queries
CREATE INDEX idx_events_name_time_user ON analytics_events(event_name, event_time, user_id);
CREATE INDEX idx_events_page_time ON analytics_events(page, event_time);

-- Partition large tables (optional)
ALTER TABLE analytics_events PARTITION BY RANGE (YEAR(event_time)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026)
);
```

### Caching Strategy
```php
// Cache expensive queries
$topPages = Cache::tags(['analytics', 'top_pages'])->remember('top_pages_24h', 86400, function () {
    return AnalyticsEvent::selectRaw('page, COUNT(*) as views')
        ->where('event_name', 'page_view')
        ->where('event_time', '>=', now()->subDays(1))
        ->groupBy('page')
        ->orderBy('views', 'desc')
        ->limit(10)
        ->get();
});
```

## 🛡️ Security Best Practices

### Data Sanitization
```php
// Sanitize input data
$parameters = $request->only(['event_name', 'page', 'parameters']);
$parameters['parameters'] = array_filter($parameters['parameters'], function ($value) {
    return !is_string($value) || strlen($value) <= 1000;
});

// Rate limiting
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/api/analytics/track-event', [AnalyticsController::class, 'trackEvent']);
});
```

### Privacy Protection
```javascript
// Anonymize sensitive data
function anonymizeUserData(userData) {
    return {
        user_segment: userData.segment,
        user_type: userData.type,
        // Remove PII
        // email: userData.email,
        // name: userData.name,
        // phone: userData.phone
    };
}
```

---

## 📚 Additional Resources

- [Backend Documentation](./BACKEND_DOCUMENTATION.md) - Detailed API and service documentation
- [Frontend Documentation](./FRONTEND_DOCUMENTATION.md) - Complete JavaScript tracking guide
- [Analytics Summary](./ANALYTICS_SUMMARY.md) - Implementation overview and status
- [Analytics Documentation](./ANALYTICS_DOCUMENTATION.md) - Comprehensive system documentation

*Last updated: March 7, 2025*
