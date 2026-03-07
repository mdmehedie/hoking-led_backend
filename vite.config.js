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
