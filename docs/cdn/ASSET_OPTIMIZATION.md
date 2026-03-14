# Asset Optimization Guide

## 📋 Overview

This guide covers the comprehensive asset optimization system, including CSS/JS minification, code splitting, versioning, and build process optimization using Vite.

## 🚀 Features Overview

### **Build Optimization**
- CSS and JavaScript minification
- Code splitting for better caching
- Tree shaking to remove unused code
- Source map generation for debugging

### **Asset Versioning**
- Hash-based file naming
- Cache busting for updates
- Automatic version management
- CDN-friendly asset URLs

### **Performance Optimization**
- Lazy loading for JavaScript modules
- Critical CSS inlining
- Font optimization
- Image asset optimization

## ⚙️ Vite Configuration

### Production Build Setup

The `vite.config.js` is configured for optimal performance:

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/lazy-image-loader.js',
                'resources/js/analytics-tracker.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // Production optimizations
        minify: 'terser',
        sourcemap: false,
        rollupOptions: {
            output: {
                manualChunks: {
                    // Separate vendor chunks
                    vendor: ['@headlessui/vue', '@inertiajs/vue3'],
                    // Separate analytics tracker
                    analytics: ['./resources/js/analytics-tracker.js'],
                    // Separate lazy loader
                    lazyloader: ['./resources/js/lazy-image-loader.js'],
                },
                // Asset naming for better caching
                chunkFileNames: 'assets/js/[name]-[hash].js',
                entryFileNames: 'assets/js/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    const info = assetInfo.name.split('.');
                    const ext = info[info.length - 1];
                    
                    if (/\.(mp4|webm|ogg|mp3|wav|flac|aac)(\?.*)?$/i.test(assetInfo.name)) {
                        return 'assets/media/[name]-[hash][extname]';
                    }
                    if (/\.(png|jpe?g|gif|svg|webp|avif)(\?.*)?$/i.test(assetInfo.name)) {
                        return 'assets/images/[name]-[hash][extname]';
                    }
                    if (/\.(woff2?|eot|ttf|otf)(\?.*)?$/i.test(assetInfo.name)) {
                        return 'assets/fonts/[name]-[hash][extname]';
                    }
                    if (/\.css(\?.*)?$/i.test(assetInfo.name)) {
                        return 'assets/css/[name]-[hash][extname]';
                    }
                    return 'assets/[name]-[hash][extname]';
                },
            },
        },
        // CSS optimization
        cssCodeSplit: true,
        // Terser options for better minification
        terserOptions: {
            compress: {
                drop_console: true, // Remove console.log in production
                drop_debugger: true, // Remove debugger statements
            },
            mangle: {
                safari10: true, // Support Safari 10
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    // Define global constants
    define: {
        __VUE_OPTIONS_API__: false, // Disable Vue Options API for smaller bundle
        __VUE_PROD_DEVTOOLS__: false, // Disable devtools in production
    },
});
```

### Code Splitting Strategy

The configuration creates separate chunks for different purposes:

```javascript
manualChunks: {
    // Vendor libraries (rarely change)
    vendor: ['@headlessui/vue', '@inertiajs/vue3'],
    
    // Analytics tracker (loaded separately)
    analytics: ['./resources/js/analytics-tracker.js'],
    
    // Image loader (loaded on demand)
    lazyloader: ['./resources/js/lazy-image-loader.js'],
    
    // Additional chunks can be added
    filament: ['@filament/*'],
    charts: ['apexcharts', 'chart.js'],
}
```

## 🎨 CSS Optimization

### Tailwind CSS Configuration

```javascript
// tailwind.config.js
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            // Custom optimizations
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
    // Production optimizations
    purge: {
        enabled: process.env.NODE_ENV === 'production',
        content: [
            './resources/**/*.blade.php',
            './resources/**/*.js',
            './resources/**/*.vue',
        ],
        options: {
            safelist: [
                // Add classes that should not be purged
                'lazy-loading',
                'lazy-loaded',
                'lazy-error',
            ],
        },
    },
}
```

### Critical CSS

Extract critical CSS for faster initial render:

```javascript
// vite.config.js - Add critical CSS plugin
import critical from 'rollup-plugin-critical';

plugins: [
    // ... other plugins
    critical({
        criticalUrl: 'http://localhost:8000',
        criticalBase: 'public/',
        criticalPages: [
            { uri: '/', template: 'home' },
            { uri: '/about', template: 'about' },
        ],
        criticalCss: {
            extract: true,
            inline: true,
            minify: true,
        },
    }),
]
```

### CSS Optimization Techniques

```css
/* Use CSS custom properties for better compression */
:root {
    --primary-color: #3b82f6;
    --secondary-color: #64748b;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
}

/* Optimize selectors */
.component {
    /* Use shorthand properties */
    margin: 1rem 0;
    padding: 0.5rem 1rem;
    
    /* Use efficient selectors */
    &.active {
        background-color: var(--primary-color);
    }
    
    /* Avoid universal selectors */
    * {
        box-sizing: border-box;
    }
}

/* Use modern layout techniques */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}
```

## 📦 JavaScript Optimization

### Tree Shaking

Ensure only used code is included:

```javascript
// resources/js/app.js
// Import only what you need
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Dynamic imports for code splitting
const LazyImageLoader = () => import('./lazy-image-loader.js');
const AnalyticsTracker = () => import('./analytics-tracker.js');

// Use only needed functions
import { debounce, throttle } from 'lodash-es';

// Tree shake unused exports
export { debounce, throttle };
```

### Module Federation

For micro-frontend architecture:

```javascript
// vite.config.js
import { ModuleFederationPlugin } from '@module-federation/vite';

plugins: [
    ModuleFederationPlugin({
        name: 'shell',
        remotes: {
            admin: 'admin@http://localhost:3001/remoteEntry.js',
            blog: 'blog@http://localhost:3002/remoteEntry.js',
        },
        shared: {
            vue: { singleton: true },
            'vue-router': { singleton: true },
        },
    }),
]
```

### Performance Monitoring

```javascript
// resources/js/performance-monitor.js
class PerformanceMonitor {
    constructor() {
        this.metrics = {};
        this.init();
    }

    init() {
        // Monitor page load
        window.addEventListener('load', () => {
            this.recordPageLoad();
        });

        // Monitor Core Web Vitals
        this.monitorWebVitals();
    }

    recordPageLoad() {
        const navigation = performance.getEntriesByType('navigation')[0];
        
        this.metrics = {
            domContentLoaded: navigation.domContentLoadedEventEnd - navigation.domContentLoadedEventStart,
            loadComplete: navigation.loadEventEnd - navigation.loadEventStart,
            firstPaint: performance.getEntriesByName('first-paint')[0]?.startTime,
            firstContentfulPaint: performance.getEntriesByName('first-contentful-paint')[0]?.startTime,
        };

        this.sendMetrics();
    }

    monitorWebVitals() {
        // Largest Contentful Paint
        new PerformanceObserver((list) => {
            const entries = list.getEntries();
            const lastEntry = entries[entries.length - 1];
            this.metrics.lcp = lastEntry.startTime;
        }).observe({ entryTypes: ['largest-contentful-paint'] });

        // Cumulative Layout Shift
        let clsValue = 0;
        new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                if (!entry.hadRecentInput) {
                    clsValue += entry.value;
                }
            }
            this.metrics.cls = clsValue;
        }).observe({ entryTypes: ['layout-shift'] });

        // First Input Delay
        new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                this.metrics.fid = entry.processingStart - entry.startTime;
            }
        }).observe({ entryTypes: ['first-input'] });
    }

    sendMetrics() {
        // Send to analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'page_performance', this.metrics);
        }

        // Send to your analytics endpoint
        fetch('/api/analytics/performance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(this.metrics),
        });
    }
}

// Initialize monitor
new PerformanceMonitor();
```

## 🚀 Build Process

### Development Build

```bash
# Development with hot reload
npm run dev

# Development with HTTPS
npm run dev -- --https

# Development with specific host
npm run dev -- --host 0.0.0.0
```

### Production Build

```bash
# Production build
npm run build

# Build with analysis
npm run build -- --analyze

# Build with specific mode
npm run build -- --mode production
```

### Build Scripts

```json
// package.json
{
    "scripts": {
        "dev": "vite",
        "build": "vite build",
        "build:analyze": "vite build --mode analyze",
        "build:production": "vite build --mode production",
        "preview": "vite preview",
        "optimize": "npm run build && npm run optimize:css && npm run optimize:js",
        "optimize:css": "postcss resources/css/app.css -o public/css/app.min.css",
        "optimize:js": "terser public/js/app.js -o public/js/app.min.js"
    }
}
```

## 📊 Asset Management

### Version Control

```php
// Helper functions for asset URLs
function asset_versioned($path) {
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    
    if (isset($manifest[$path])) {
        return asset($manifest[$path]);
    }
    
    return asset($path);
}

// Usage in Blade
{{ asset_versioned('resources/css/app.css') }}
{{ asset_versioned('resources/js/app.js') }}
```

### CDN Integration

```javascript
// vite.config.js - CDN setup
export default defineConfig({
    build: {
        rollupOptions: {
            output: {
                assetFileNames: (assetInfo) => {
                    // Use CDN URL in production
                    if (process.env.NODE_ENV === 'production') {
                        return `https://cdn.yourdomain.com/assets/[name]-[hash][extname]`;
                    }
                    return 'assets/[name]-[hash][extname]';
                },
            },
        },
    },
});
```

### Asset Preloading

```php
// Preload critical assets
function preload_assets() {
    $assets = [
        'css' => ['/css/app.css'],
        'js' => ['/js/app.js'],
        'fonts' => ['/fonts/inter.woff2'],
    ];

    foreach ($assets as $type => $files) {
        foreach ($files as $file) {
            echo '<link rel="preload" href="' . asset($file) . '" as="' . $type . '">' . "\n";
        }
    }
}
```

## 🔧 Advanced Configuration

### Custom Plugins

```javascript
// vite.config.js - Custom plugins
import { defineConfig } from 'vite';
import { visualizer } from 'rollup-plugin-visualizer';

export default defineConfig({
    plugins: [
        // Bundle analyzer
        visualizer({
            filename: 'dist/stats.html',
            open: true,
            gzipSize: true,
            brotliSize: true,
        }),
        
        // Custom optimization plugin
        {
            name: 'optimize-images',
            generateBundle(options, bundle) {
                // Optimize images in bundle
            },
        },
    ],
});
```

### Environment-Specific Config

```javascript
// vite.config.js
export default defineConfig(({ command, mode }) => {
    const isProduction = mode === 'production';
    const isDevelopment = mode === 'development';

    return {
        build: {
            minify: isProduction ? 'terser' : false,
            sourcemap: !isProduction,
            rollupOptions: {
                output: {
                    manualChunks: isProduction ? {
                        vendor: ['vue', '@inertiajs/vue3'],
                    } : {},
                },
            },
        },
        define: {
            __DEV__: isDevelopment,
            __PROD__: isProduction,
        },
    };
});
```

## 📈 Performance Optimization

### Bundle Analysis

```bash
# Analyze bundle size
npm run build:analyze

# Visualize bundle
npx vite-bundle-analyzer dist

# Check bundle size
npx bundlesize
```

### Performance Budgets

```javascript
// vite.config.js - Performance budgets
export default defineConfig({
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    // Split large chunks
                    if (id.includes('node_modules')) {
                        const groups = {
                            'vendor-ui': ['vue', '@headlessui/vue'],
                            'vendor-utils': ['lodash-es', 'date-fns'],
                            'vendor-charts': ['apexcharts', 'chart.js'],
                        };
                        
                        for (const [name, modules] of Object.entries(groups)) {
                            if (modules.some(module => id.includes(module))) {
                                return name;
                            }
                        }
                    }
                },
            },
        },
    },
});
```

### Critical Path Optimization

```php
// resources/views/layouts/app.blade.php
@section('head')
    <!-- Critical CSS inline -->
    <style>
        /* Critical CSS for above-the-fold content */
        body { margin: 0; font-family: system-ui; }
        .loading { opacity: 0; transition: opacity 0.3s; }
    </style>
    
    <!-- Preload critical resources -->
    <link rel="preload" href="{{ asset('fonts/inter.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('css/app.css') }}" as="style">
    
    <!-- Load non-critical CSS asynchronously -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" media="print" onload="this.media='all'">
@endsection
```

## 🛠️ Troubleshooting

### Common Issues

#### **Build Failures**
```bash
# Clear build cache
rm -rf node_modules/.vite
npm install

# Check for syntax errors
npm run build -- --mode development

# Check dependencies
npm audit
npm audit fix
```

#### **Asset Not Loading**
```bash
# Check manifest file
cat public/build/manifest.json

# Verify asset URLs
php artisan tinker
>>> asset('css/app.css')

# Check file permissions
ls -la public/build/
```

#### **Performance Issues**
```bash
# Analyze bundle size
npm run build:analyze

# Check for large dependencies
npx webpack-bundle-analyzer dist

# Optimize images
npm run optimize:images
```

### Debug Tools

```javascript
// Debug build process
// vite.config.js
export default defineConfig({
    logLevel: 'debug',
    clearScreen: false,
    server: {
        hmr: {
            overlay: true,
        },
    },
});
```

## 📚 Best Practices

### **Build Optimization**
1. **Use code splitting** for better caching
2. **Minify and compress** all assets
3. **Remove unused code** with tree shaking
4. **Optimize images** and fonts

### **Performance**
1. **Load critical CSS** inline
2. **Preload important resources**
3. **Lazy load non-critical assets**
4. **Use modern formats** (WebP, AVIF)

### **Development**
1. **Use source maps** for debugging
2. **Enable hot reload** for faster development
3. **Analyze bundles** regularly
4. **Monitor performance** metrics

### **Production**
1. **Minimize bundle size**
2. **Use CDN** for asset delivery
3. **Implement caching** strategies
4. **Monitor performance** continuously

## 🎯 Advanced Features

### **Service Workers**

```javascript
// public/sw.js
const CACHE_NAME = 'v1';
const urlsToCache = [
    '/',
    '/css/app.css',
    '/js/app.js',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request)
            .then((response) => response || fetch(event.request))
    );
});
```

### **Progressive Enhancement**

```php
// Progressive enhancement for assets
@if (app()->environment('production'))
    <!-- Production assets -->
    <script src="{{ asset('js/app.min.js') }}" defer></script>
@else
    <!-- Development assets -->
    <script src="{{ asset('js/app.js') }}" defer></script>
@endif

<!-- Fallback for no JavaScript -->
<noscript>
    <div class="no-js-warning">
        Please enable JavaScript for the best experience.
    </div>
</noscript>
```

## 📞 Support

For asset optimization issues:
1. Check the **build logs** for errors
2. Verify **Vite configuration**
3. Check **file permissions**
4. Monitor **bundle sizes**

---

*Last updated: March 7, 2025*
