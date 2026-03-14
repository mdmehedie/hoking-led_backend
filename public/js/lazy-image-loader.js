/**
 * Lazy Loading Image Component
 * Automatically loads images when they enter the viewport
 */

class LazyImageLoader {
    constructor(options = {}) {
        this.options = {
            root: null,
            rootMargin: '50px',
            threshold: 0.1,
            placeholder: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2Y0ZjRmNCIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5Mb2FkaW5nLi4uPC90ZXh0Pjwvc3ZnPg==',
            ...options
        };
        
        this.init();
    }

    init() {
        // Use Intersection Observer if available
        if ('IntersectionObserver' in window) {
            this.setupIntersectionObserver();
        } else {
            // Fallback for older browsers
            this.setupFallback();
        }
    }

    setupIntersectionObserver() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadImage(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, this.options);

        // Observe all lazy images
        document.querySelectorAll('img[data-src]').forEach(img => {
            // Set placeholder
            if (img.src && !img.complete) {
                img.src = this.options.placeholder;
                img.classList.add('lazy-loading');
            }
            observer.observe(img);
        });

        // Observe picture elements
        document.querySelectorAll('picture[data-src]').forEach(picture => {
            const img = picture.querySelector('img');
            if (img && !img.complete) {
                img.src = this.options.placeholder;
                img.classList.add('lazy-loading');
            }
            observer.observe(picture);
        });
    }

    setupFallback() {
        // Simple scroll-based lazy loading for older browsers
        const lazyImages = document.querySelectorAll('img[data-src]');
        
        const lazyLoad = () => {
            const lazyImagesInView = Array.from(lazyImages).filter(img => {
                const rect = img.getBoundingClientRect();
                return (
                    rect.top <= (window.innerHeight + this.options.rootMargin) &&
                    rect.bottom >= (0 - this.options.rootMargin) &&
                    rect.left <= (window.innerWidth + this.options.rootMargin) &&
                    rect.right >= (0 - this.options.rootMargin)
                );
            });

            lazyImagesInView.forEach(img => this.loadImage(img));
        };

        // Initial check
        lazyLoad();
        
        // Check on scroll
        window.addEventListener('scroll', lazyLoad);
        window.addEventListener('resize', lazyLoad);
    }

    loadImage(element) {
        const src = element.dataset.src;
        
        if (!src) return;

        // Handle regular img elements
        if (element.tagName === 'IMG') {
            this.loadImgElement(element, src);
        }
        
        // Handle picture elements
        else if (element.tagName === 'PICTURE') {
            this.loadPictureElement(element, src);
        }
    }

    loadImgElement(img, src) {
        // Create new image to preload
        const newImg = new Image();
        
        newImg.onload = () => {
            img.src = src;
            img.classList.remove('lazy-loading');
            img.classList.add('lazy-loaded');
            img.removeAttribute('data-src');
            
            // Add fade-in effect
            img.style.opacity = '0';
            setTimeout(() => {
                img.style.transition = 'opacity 0.3s ease-in-out';
                img.style.opacity = '1';
            }, 10);
        };
        
        newImg.onerror = () => {
            img.classList.remove('lazy-loading');
            img.classList.add('lazy-error');
        };
        
        newImg.src = src;
    }

    loadPictureElement(picture, src) {
        const img = picture.querySelector('img');
        const sources = picture.querySelectorAll('source');
        
        // Update sources
        sources.forEach(source => {
            if (source.dataset.srcset) {
                source.srcset = source.dataset.srcset;
                source.removeAttribute('data-srcset');
            }
        });
        
        // Update image
        if (img) {
            this.loadImgElement(img, src);
        }
    }

    // Method to add lazy loading to new images dynamically
    observe(element) {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.loadImage(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, this.options);
            
            observer.observe(element);
        } else {
            this.loadImage(element);
        }
    }

    // Method to preload critical images
    preload(urls) {
        urls.forEach(url => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = url;
            document.head.appendChild(link);
        });
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.lazyImageLoader = new LazyImageLoader({
        rootMargin: '100px 0px',
        threshold: 0.1
    });
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LazyImageLoader;
}
