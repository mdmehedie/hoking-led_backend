# Frontend Documentation - Analytics Tracking

## 🎯 Overview

The frontend analytics tracking system provides comprehensive event collection, Core Web Vitals monitoring, and seamless integration with any web application through a simple JavaScript API.

## 🚀 Quick Start

### 1. Include the Tracker
```html
<!-- In your main layout file -->
<script src="/js/analytics-tracker.js"></script>

<!-- Or with configuration -->
<script>
window.analyticsConfig = {
    trackPageViews: true,
    trackClicks: true,
    trackForms: true,
    trackScrolling: true,
    trackCoreWebVitals: true,
    debug: false
};
</script>
<script src="/js/analytics-tracker.js"></script>
```

### 2. Basic Usage
```javascript
// Track a custom event
AnalyticsTracker.track('button_click', {
    button_text: 'Buy Now',
    product_id: '123',
    category: 'ecommerce'
});

// Track page view manually
AnalyticsTracker.page();
```

## 📊 Event Types & Examples

### Page Views
```javascript
// Automatic tracking (enabled by default)
// No code needed - automatically tracked on page load

// Manual page tracking
AnalyticsTracker.track('page_view', {
    title: 'Product Details',
    path: '/products/123',
    category: 'product',
    referrer: document.referrer
});
```

### Click Events
```javascript
// Automatic tracking (enabled by default)
// Links, buttons, and clickable elements are tracked automatically

// Manual click tracking
AnalyticsTracker.track('button_click', {
    element: 'button',
    text: 'Add to Cart',
    class: 'btn btn-primary',
    id: 'add-to-cart-btn',
    coordinates: '150,200',
    ctrl_key: false,
    shift_key: true
});
```

### Form Events
```javascript
// Automatic form tracking
AnalyticsTracker.track('form_submit', {
    form_id: 'contact-form',
    form_action: '/contact',
    form_method: 'POST',
    fields: {
        name: 'John Doe',
        email: 'john@example.com',
        message: 'Hello!'
    },
    field_count: 3
});

// Custom form tracking
document.getElementById('my-form').addEventListener('submit', function(e) {
    AnalyticsTracker.track('custom_form_submit', {
        form_name: 'newsletter',
        conversion_value: 25.00,
        source: 'homepage_banner'
    });
});
```

### Scroll Events
```javascript
// Automatic scroll milestone tracking
// Tracks at 25%, 50%, 75%, 90% scroll depth

// Manual scroll tracking
window.addEventListener('scroll', function() {
    const scrollPercent = Math.round(
        (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100
    );
    
    if (scrollPercent >= 50 && !window.tracked50) {
        AnalyticsTracker.track('scroll_milestone', {
            milestone: 50,
            current_percent: scrollPercent,
            section: 'product_details'
        });
        window.tracked50 = true;
    }
});
```

### Custom Events
```javascript
// Video engagement
AnalyticsTracker.track('video_engagement', {
    video_id: 'abc123',
    video_title: 'Product Demo',
    duration: 120,
    watched_time: 45,
    completion_rate: 37.5,
    quality: '720p'
});

// File downloads
AnalyticsTracker.track('file_download', {
    file_name: 'product-catalog.pdf',
    file_size: 2048576,
    file_type: 'pdf',
    download_location: 'product_page'
});

// Error tracking
AnalyticsTracker.track('error_occurred', {
    error_type: 'javascript',
    error_message: 'Cannot read property of undefined',
    error_url: window.location.href,
    user_agent: navigator.userAgent,
    timestamp: new Date().toISOString()
});
```

## 🎛️ Configuration Options

### Global Configuration
```javascript
window.analyticsConfig = {
    // Enable/disable automatic tracking
    enableAutoTracking: true,
    trackPageViews: true,
    trackClicks: true,
    trackForms: true,
    trackScrolling: true,
    trackCoreWebVitals: true,
    
    // Debug mode for development
    debug: false,
    
    // Custom API endpoint
    apiEndpoint: '/api/analytics/track-event'
};
```

### Advanced Configuration
```javascript
window.analyticsConfig = {
    // Custom event mapping
    eventMapping: {
        'video-play': 'video_engagement',
        'download': 'file_download'
    },
    
    // Exclude elements from tracking
    excludeSelectors: [
        '.no-track',
        '[data-no-track]',
        '.admin-panel *'
    ],
    
    // Include only specific elements
    includeSelectors: [
        '.track-me',
        '[data-track]'
    ],
    
    // Custom parameters to include with all events
    globalParameters: {
        app_version: '2.1.0',
        user_segment: 'premium',
        ab_test: 'variant_a'
    }
};
```

## 🔧 Advanced Usage

### E-commerce Tracking
```javascript
// Product view tracking
function trackProductView(product) {
    AnalyticsTracker.track('product_view', {
        product_id: product.id,
        product_name: product.name,
        category: product.category,
        price: product.price,
        currency: 'USD',
        in_stock: product.inStock,
        brand: product.brand
    });
}

// Add to cart tracking
function trackAddToCart(product, quantity) {
    AnalyticsTracker.track('add_to_cart', {
        product_id: product.id,
        product_name: product.name,
        quantity: quantity,
        price: product.price,
        total_value: product.price * quantity,
        cart_total: getCartTotal()
    });
}

// Purchase tracking
function trackPurchase(order) {
    AnalyticsTracker.track('purchase', {
        order_id: order.id,
        order_value: order.total,
        currency: 'USD',
        items: order.items.map(item => ({
            product_id: item.id,
            product_name: item.name,
            quantity: item.quantity,
            price: item.price
        })),
        payment_method: order.paymentMethod,
        shipping_method: order.shippingMethod
    });
}
```

### Content Engagement
```javascript
// Article reading time
function trackArticleReading() {
    let startTime = Date.now();
    let maxScroll = 0;
    
    window.addEventListener('scroll', function() {
        const scrollPercent = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
        maxScroll = Math.max(maxScroll, scrollPercent);
    });
    
    window.addEventListener('beforeunload', function() {
        const readingTime = Math.round((Date.now() - startTime) / 1000);
        AnalyticsTracker.track('article_read', {
            article_id: articleId,
            reading_time: readingTime,
            scroll_depth: Math.round(maxScroll),
            completion_rate: Math.round(maxScroll)
        });
    });
}

// Video engagement
function trackVideoEngagement(video) {
    let startTime = Date.now();
    let watchedTime = 0;
    let isPlaying = false;
    
    video.addEventListener('play', function() {
        isPlaying = true;
        startTime = Date.now();
    });
    
    video.addEventListener('pause', function() {
        if (isPlaying) {
            watchedTime += Date.now() - startTime;
            isPlaying = false;
        }
    });
    
    video.addEventListener('ended', function() {
        AnalyticsTracker.track('video_complete', {
            video_id: video.dataset.id,
            total_duration: video.duration,
            watched_time: watchedTime / 1000,
            completion_rate: 100
        });
    });
}
```

### User Journey Tracking
```javascript
// Track user journey steps
function trackUserJourney(step, data) {
    AnalyticsTracker.track('user_journey', {
        step_number: step,
        step_name: data.name,
        step_type: data.type,
        time_spent: data.timeSpent,
        user_segment: getUserSegment(),
        device_type: getDeviceType()
    });
}

// Funnel analysis
function trackFunnelStep(step, additionalData = {}) {
    AnalyticsTracker.track('funnel_step', {
        funnel_name: 'purchase_funnel',
        step_number: step,
        step_name: getStepName(step),
        total_steps: 4,
        completion_rate: (step / 4) * 100,
        ...additionalData
    });
}
```

## 🎯 Real-World Examples

### Example 1: SaaS Application
```javascript
// User registration tracking
function trackRegistration(user) {
    AnalyticsTracker.track('user_registered', {
        user_id: user.id,
        registration_method: 'email',
        plan_selected: user.plan,
        referral_source: getReferralSource(),
        completion_time: getRegistrationTime()
    });
}

// Feature usage tracking
function trackFeatureUsage(feature, action) {
    AnalyticsTracker.track('feature_used', {
        feature_name: feature,
        action: action,
        user_plan: getUserPlan(),
        timestamp: new Date().toISOString()
    });
}

// API usage tracking
function trackApiUsage(endpoint, responseTime, statusCode) {
    AnalyticsTracker.track('api_call', {
        endpoint: endpoint,
        response_time: responseTime,
        status_code: statusCode,
        user_authenticated: isAuthenticated(),
        rate_limit_remaining: getRateLimit()
    });
}
```

### Example 2: E-commerce Store
```javascript
// Product filtering
function trackProductFilter(filters) {
    AnalyticsTracker.track('product_filter', {
        filter_type: filters.type,
        filter_value: filters.value,
        results_count: filters.resultsCount,
        sort_by: filters.sortBy,
        price_range: filters.priceRange
    });
}

// Search tracking
function trackSearch(query, results) {
    AnalyticsTracker.track('search', {
        search_query: query,
        results_count: results.length,
        search_time: results.searchTime,
        search_type: results.type,
        clicked_result: results.clickedResult
    });
}

// Cart abandonment
function trackCartAbandonment(cartItems, timeOnPage) {
    AnalyticsTracker.track('cart_abandoned', {
        cart_value: calculateCartValue(cartItems),
        items_count: cartItems.length,
        time_on_page: timeOnPage,
        abandonment_reason: detectAbandonmentReason()
    });
}
```

### Example 3: Content Platform
```javascript
// Content interaction
function trackContentInteraction(content, interactionType) {
    AnalyticsTracker.track('content_interaction', {
        content_id: content.id,
        content_type: content.type,
        interaction_type: interactionType,
        content_length: content.length,
        author_id: content.authorId,
        tags: content.tags
    });
}

// Social sharing
function trackSocialShare(content, platform) {
    AnalyticsTracker.track('social_share', {
        content_id: content.id,
        platform: platform,
        content_type: content.type,
        share_method: 'button',
        user_followers: getUserFollowers()
    });
}

// Comment engagement
function trackCommentEngagement(comment) {
    AnalyticsTracker.track('comment_engagement', {
        content_id: comment.contentId,
        comment_length: comment.text.length,
        has_mentions: comment.hasMentions,
        reply_to: comment.replyToId,
        sentiment: analyzeSentiment(comment.text)
    });
}
```

## 🔍 Debugging & Testing

### Enable Debug Mode
```javascript
window.analyticsConfig = {
    debug: true
};

// Console output will show:
// [Analytics Tracker] Event tracked: button_click {button_text: "Buy Now", product_id: "123"}
// [Analytics Tracker] API request sent to: /api/analytics/track-event
// [Analytics Tracker] API response: {success: true, event_id: 12345}
```

### Manual Testing
```javascript
// Test event tracking
AnalyticsTracker.track('test_event', {
    test_data: 'debugging',
    timestamp: new Date().toISOString(),
    random_id: Math.random()
});

// Verify in browser network tab
// Look for POST request to /api/analytics/track-event
// Check request payload and response
```

### Error Handling
```javascript
// Override error handling
window.addEventListener('unhandledrejection', function(event) {
    AnalyticsTracker.track('javascript_error', {
        error_type: 'promise_rejection',
        error_message: event.reason,
        url: window.location.href,
        stack_trace: event.reason?.stack
    });
});

window.addEventListener('error', function(event) {
    AnalyticsTracker.track('javascript_error', {
        error_type: 'runtime_error',
        error_message: event.message,
        error_file: event.filename,
        error_line: event.lineno,
        error_column: event.colno,
        url: window.location.href
    });
});
```

## 🚀 Performance Optimization

### Lazy Loading
```javascript
// Load analytics tracker only when needed
function loadAnalyticsTracker() {
    if (shouldTrackUser()) {
        const script = document.createElement('script');
        script.src = '/js/analytics-tracker.js';
        script.async = true;
        document.head.appendChild(script);
    }
}

// Load after page load
window.addEventListener('load', loadAnalyticsTracker);
```

### Batch Events
```javascript
// Batch multiple events for performance
const eventQueue = [];

function batchTrack(eventName, parameters) {
    eventQueue.push({name: eventName, params: parameters, time: Date.now()});
    
    if (eventQueue.length >= 5) {
        sendBatchEvents();
    }
}

function sendBatchEvents() {
    AnalyticsTracker.track('batch_events', {
        events: eventQueue,
        batch_size: eventQueue.length,
        batch_id: generateBatchId()
    });
    eventQueue.length = 0;
}
```

## 🛡️ Privacy & Compliance

### GDPR Compliance
```javascript
// Check user consent
function hasAnalyticsConsent() {
    return localStorage.getItem('analytics_consent') === 'true';
}

// Conditional tracking
if (hasAnalyticsConsent()) {
    AnalyticsTracker.track('page_view', {
        consent_given: true,
        consent_timestamp: localStorage.getItem('consent_timestamp')
    });
}

// Anonymize data
function anonymizeUserData(userData) {
    return {
        user_segment: userData.segment,
        user_type: userData.type,
        // Remove personally identifiable information
        // user_id: userData.id, // Removed
        // email: userData.email  // Removed
    };
}
```

### Cookie Consent
```javascript
// Respect cookie preferences
function canUseCookies() {
    return localStorage.getItem('cookie_consent') === 'true';
}

if (canUseCookies()) {
    // Enable full tracking
    window.analyticsConfig = {
        trackPageViews: true,
        trackClicks: true,
        trackForms: true
    };
} else {
    // Limited tracking only
    window.analyticsConfig = {
        trackPageViews: false,
        trackClicks: false,
        trackForms: false
    };
}
```

## 📈 Best Practices

### 1. Event Naming
```javascript
// Good: Descriptive and consistent
AnalyticsTracker.track('product_added_to_cart');
AnalyticsTracker.track('user_completed_registration');
AnalyticsTracker.track('video_started_playing');

// Bad: Vague or inconsistent
AnalyticsTracker.track('click');
AnalyticsTracker.track('form');
AnalyticsTracker.track('stuff');
```

### 2. Parameter Structure
```javascript
// Good: Structured and predictable
AnalyticsTracker.track('purchase', {
    order_id: 'ORD-12345',
    total_amount: 99.99,
    currency: 'USD',
    items_count: 3,
    payment_method: 'credit_card'
});

// Bad: Unstructured
AnalyticsTracker.track('purchase', {
    data: 'ORD-12345,99.99,USD,3,credit_card'
});
```

### 3. Error Tracking
```javascript
// Good: Include context
AnalyticsTracker.track('form_validation_error', {
    form_name: 'registration',
    field_name: 'email',
    error_type: 'invalid_format',
    user_input: 'invalid-email',
    validation_rule: 'email_format'
});

// Bad: Generic error
AnalyticsTracker.track('error', {
    message: 'Something went wrong'
});
```

---

*Last updated: March 7, 2025*
