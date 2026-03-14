/**
 * Lazy Image Loader
 * 
 * This file handles lazy loading of images to improve page performance.
 * It automatically loads images when they come into viewport.
 */

/**
 * LazyImageLoader class
 */
class LazyImageLoader {
    constructor(options = {}) {
        this.options = {
            rootMargin: '0px',
            threshold: 0.1,
            enableNativeLazyLoad: true,
            ...options
        };
        
        this.observer = null;
        this.init();
    }
    
    /**
     * Initialize the lazy loader
     */
    init() {
        // Check if native lazy loading is supported
        if ('loading' in HTMLImageElement.prototype && this.options.enableNativeLazyLoad) {
            console.log('Native lazy loading supported');
            return;
        }
        
        // Use Intersection Observer for modern browsers
        if ('IntersectionObserver' in window) {
            this.observer = new IntersectionObserver(this.handleIntersection.bind(this), {
                rootMargin: this.options.rootMargin,
                threshold: this.options.threshold
            });
            
            this.observeImages();
            console.log('Intersection Observer lazy loading initialized');
        } else {
            // Fallback for older browsers
            this.initScrollListener();
            console.log('Scroll-based lazy loading initialized');
        }
    }
    
    /**
     * Observe all images with lazy loading
     */
    observeImages() {
        const images = document.querySelectorAll('img[data-src], img[data-lazy]');
        
        images.forEach(img => {
            // Set initial state
            img.classList.add('lazy-loading');
            
            // Start observing
            this.observer.observe(img);
        });
    }
    
    /**
     * Handle intersection observer callback
     */
    handleIntersection(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                
                // Load the image
                this.loadImage(img);
                
                // Stop observing this image
                observer.unobserve(img);
            }
        });
    }
    
    /**
     * Load the actual image
     */
    loadImage(img) {
        if (img.dataset.src) {
            img.src = img.dataset.src;
            delete img.dataset.src;
        }
        
        if (img.dataset.lazy) {
            img.src = img.dataset.lazy;
            delete img.dataset.lazy;
        }
        
        // Remove loading class and add loaded class
        img.classList.remove('lazy-loading');
        img.classList.add('lazy-loaded');
        
        // Add fade-in effect
        img.style.opacity = '0';
        img.style.transition = 'opacity 0.3s ease-in-out';
        
        // Trigger reflow and fade in
        img.offsetHeight;
        setTimeout(() => {
            img.style.opacity = '1';
        }, 10);
    }
    
    /**
     * Initialize scroll-based lazy loading (fallback)
     */
    initScrollListener() {
        let ticking = false;
        
        const scrollHandler = () => {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(() => {
                    this.checkImagesInViewport();
                    ticking = false;
                });
            }
        };
        
        window.addEventListener('scroll', scrollHandler);
        window.addEventListener('resize', scrollHandler);
        window.addEventListener('orientationchange', scrollHandler);
        
        // Initial check
        this.checkImagesInViewport();
    }
    
    /**
     * Check if images are in viewport
     */
    checkImagesInViewport() {
        const images = document.querySelectorAll('img[data-src]:not(.lazy-loaded), img[data-lazy]:not(.lazy-loaded)');
        
        images.forEach(img => {
            const rect = img.getBoundingClientRect();
            const isInViewport = (
                rect.top <= window.innerHeight + this.options.rootMargin &&
                rect.left <= window.innerWidth + this.options.rootMargin &&
                rect.bottom >= -this.options.rootMargin &&
                rect.right >= -this.options.rootMargin
            );
            
            if (isInViewport) {
                this.loadImage(img);
            }
        });
    }
    
    /**
     * Update lazy loading for dynamically added images
     */
    update() {
        if (this.observer) {
            this.observeImages();
        } else {
            this.checkImagesInViewport();
        }
    }
    
    /**
     * Destroy the lazy loader
     */
    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
        
        // Remove event listeners
        window.removeEventListener('scroll', this.scrollHandler);
        window.removeEventListener('resize', this.scrollHandler);
        window.removeEventListener('orientationchange', this.scrollHandler);
    }
}

/**
 * Initialize lazy loading when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    // Auto-initialize with default options
    window.LazyImageLoader = new LazyImageLoader();
    
    // Allow manual initialization with custom options
    window.initLazyImages = function(options) {
        return new LazyImageLoader(options);
    };
});

/**
 * Utility function to add lazy loading to images
 */
window.addLazyLoading = function(images, options = {}) {
    const loader = new LazyImageLoader(options);
    
    if (typeof images === 'string') {
        // CSS selector
        document.querySelectorAll(images).forEach(img => {
            img.setAttribute('data-lazy', img.src);
            img.src = '';
            img.classList.add('lazy-loading');
        });
    } else if (images instanceof NodeList) {
        // NodeList
        images.forEach(img => {
            img.setAttribute('data-lazy', img.src);
            img.src = '';
            img.classList.add('lazy-loading');
        });
    }
    
    // Update the loader to handle new images
    setTimeout(() => loader.update(), 100);
};
