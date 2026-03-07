class AnalyticsTracker {
    constructor() {
        this.apiEndpoint = '/api/analytics/track-event';
        this.config = {
            enableAutoTracking: true,
            trackPageViews: true,
            trackClicks: true,
            trackForms: true,
            trackScrolling: true,
            debug: false
        };
        this.init();
    }

    init() {
        // Load configuration from window if available
        if (window.analyticsConfig) {
            this.config = { ...this.config, ...window.analyticsConfig };
        }

        // Track page view on load
        if (this.config.trackPageViews) {
            this.trackPageView();
        }

        // Set up event listeners
        if (this.config.trackClicks) {
            this.setupClickTracking();
        }

        if (this.config.trackForms) {
            this.setupFormTracking();
        }

        if (this.config.trackScrolling) {
            this.setupScrollTracking();
        }

        // Track Core Web Vitals
        this.trackCoreWebVitals();

        this.log('Analytics tracker initialized');
    }

    // Event tracking methods
    trackEvent(eventName, parameters = {}) {
        const eventData = {
            event_name: eventName,
            page: window.location.pathname,
            url: window.location.href,
            parameters: parameters,
            timestamp: new Date().toISOString()
        };

        this.sendEvent(eventData);
    }

    trackPageView() {
        this.trackEvent('page_view', {
            title: document.title,
            path: window.location.pathname,
            referrer: document.referrer,
            timestamp: new Date().toISOString()
        });
    }

    trackClick(element, additionalData = {}) {
        const eventData = {
            element: element.tagName.toLowerCase(),
            text: element.textContent?.trim().substring(0, 100),
            href: element.href,
            class: element.className,
            id: element.id,
            ...additionalData
        };

        this.trackEvent('click', eventData);
    }

    trackFormSubmit(form, additionalData = {}) {
        const formData = new FormData(form);
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }

        const eventData = {
            form_id: form.id || 'unnamed',
            form_action: form.action,
            form_method: form.method,
            fields: Object.keys(data).length,
            ...additionalData
        };

        this.trackEvent('form_submit', eventData);
    }

    trackScrollDepth() {
        const maxDepth = Math.max(
            document.body.scrollHeight - window.innerHeight,
            0
        );
        const scrollPercent = Math.round(
            (window.scrollY / maxDepth) * 100
        );

        this.trackEvent('scroll_depth', {
            depth_percent: scrollPercent,
            max_depth: maxDepth,
            current_position: window.scrollY
        });
    }

    // Setup methods
    setupClickTracking() {
        document.addEventListener('click', (event) => {
            const element = event.target.closest('a, button, input[type="button"], input[type="submit"], [role="button"]');
            if (element) {
                this.trackClick(element, {
                    coordinates: `${event.clientX},${event.clientY}`,
                    ctrl_key: event.ctrlKey,
                    shift_key: event.shiftKey
                });
            }
        }, true);
    }

    setupFormTracking() {
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (form.tagName === 'FORM') {
                this.trackFormSubmit(form);
            }
        }, true);
    }

    setupScrollTracking() {
        let trackedDepths = [25, 50, 75, 90];
        let hasTracked = new Set();

        window.addEventListener('scroll', () => {
            const maxDepth = Math.max(
                document.body.scrollHeight - window.innerHeight,
                0
            );
            const scrollPercent = Math.round(
                (window.scrollY / maxDepth) * 100
            );

            trackedDepths.forEach(depth => {
                if (scrollPercent >= depth && !hasTracked.has(depth)) {
                    this.trackEvent('scroll_milestone', {
                        milestone_percent: depth,
                        current_percent: scrollPercent
                    });
                    hasTracked.add(depth);
                }
            });
        });
    }

    // Core Web Vitals tracking
    trackCoreWebVitals() {
        // Largest Contentful Paint (LCP)
        this.observeLCP();
        
        // Cumulative Layout Shift (CLS)
        this.observeCLS();
        
        // Interaction to Next Paint (INP)
        this.observeINP();
    }

    observeLCP() {
        new PerformanceObserver((entryList) => {
            const entries = entryList.getEntries();
            const lastEntry = entries[entries.length - 1];
            
            this.trackEvent('core_web_vital', {
                metric: 'LCP',
                value: Math.round(lastEntry.startTime),
                url: window.location.href,
                timestamp: new Date().toISOString()
            });
        }).observe({ entryTypes: ['largest-contentful-paint'] });
    }

    observeCLS() {
        let clsValue = 0;
        
        new PerformanceObserver((entryList) => {
            for (const entry of entryList.getEntries()) {
                if (!entry.hadRecentInput) {
                    clsValue += entry.value;
                }
            }
            
            this.trackEvent('core_web_vital', {
                metric: 'CLS',
                value: Math.round(clsValue * 1000) / 1000,
                url: window.location.href,
                timestamp: new Date().toISOString()
            });
        }).observe({ entryTypes: ['layout-shift'] });
    }

    observeINP() {
        new PerformanceObserver((entryList) => {
            for (const entry of entryList.getEntries()) {
                this.trackEvent('core_web_vital', {
                    metric: 'INP',
                    value: Math.round(entry.duration),
                    url: window.location.href,
                    timestamp: new Date().toISOString()
                });
            }
        }).observe({ entryTypes: ['interaction-to-next-paint'] });
    }

    // API communication
    async sendEvent(eventData) {
        try {
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(eventData)
            });

            if (!response.ok) {
                this.log('Failed to send analytics event', eventData);
            }
        } catch (error) {
            this.log('Error sending analytics event', error);
        }
    }

    // Utility methods
    log(message, data = null) {
        if (this.config.debug) {
            console.log('[Analytics Tracker]', message, data);
        }
    }

    // Public API
    static create(config = {}) {
        if (window.analyticsTracker) {
            window.analyticsTracker.log('Analytics tracker already initialized');
            return;
        }

        window.analyticsTracker = new AnalyticsTracker();
        
        // Update config with provided options
        if (config) {
            window.analyticsTracker.config = { ...window.analyticsTracker.config, ...config };
        }
    }

    static track(eventName, parameters = {}) {
        if (window.analyticsTracker) {
            window.analyticsTracker.trackEvent(eventName, parameters);
        }
    }

    static page() {
        if (window.analyticsTracker) {
            window.analyticsTracker.trackPageView();
        }
    }
}

// Auto-initialize if enabled
if (typeof window !== 'undefined') {
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            AnalyticsTracker.create();
        });
    } else {
        AnalyticsTracker.create();
    }
}
