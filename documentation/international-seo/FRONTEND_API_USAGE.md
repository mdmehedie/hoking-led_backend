# Frontend API Usage - International SEO

This guide explains how to use the international SEO features in your frontend application when consuming the Laravel API.

## API Endpoints with International SEO

All content endpoints now include `alternates` data for hreflang implementation:

### Products API
```bash
GET /api/v1/products
GET /api/v1/products/{slug}
```

### Blogs API  
```bash
GET /api/v1/blogs
GET /api/v1/blogs/{slug}
```

### Case Studies API
```bash
GET /api/v1/case-studies
GET /api/v1/case-studies/{slug}
```

### News API
```bash
GET /api/v1/news
GET /api/v1/news/{slug}
```

### Pages API
```bash
GET /api/v1/pages
GET /api/v1/pages/{slug}
```

## Response Format with Alternates

### Single Item Response
```json
{
  "status": true,
  "message": "Product retrieved successfully",
  "data": {
    "product": {
      "id": 1,
      "title": "Sample Product",
      "slug": "sample-product",
      "short_description": "Product description",
      "detailed_description": "Full product details",
      "category_id": 1,
      "status": "published",
      "published_at": "2024-01-01T00:00:00Z",
      "is_featured": false,
      "image_path": "https://example.com/storage/product.jpg",
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z",
      "alternates": [
        {
          "locale": "en",
          "url": "https://example.com/products/sample-product"
        },
        {
          "locale": "es", 
          "url": "https://example.com/es/products/sample-product"
        },
        {
          "locale": "fr",
          "url": "https://example.com/fr/products/sample-product"
        }
      ]
    }
  }
}
```

### List Response
```json
{
  "status": true,
  "message": "Products retrieved successfully",
  "data": {
    "products": [
      {
        "id": 1,
        "title": "Sample Product",
        "slug": "sample-product",
        "alternates": [
          {
            "locale": "en",
            "url": "https://example.com/products/sample-product"
          },
          {
            "locale": "es",
            "url": "https://example.com/es/products/sample-product"
          }
        ]
      }
    ]
  }
}
```

## Frontend Implementation Examples

### 1. React/Next.js Implementation

#### Hreflang Component
```jsx
// components/HreflangTags.jsx
import Head from 'next/head';

const HreflangTags = ({ alternates }) => {
  if (!alternates || alternates.length === 0) return null;

  return (
    <Head>
      {alternates.map((alternate, index) => (
        <link
          key={index}
          rel="alternate"
          hrefLang={alternate.locale}
          href={alternate.url}
        />
      ))}
      <link rel="alternate" hrefLang="x-default" href={alternates[0]?.url} />
    </Head>
  );
};

export default HreflangTags;
```

#### Product Page with Hreflang
```jsx
// pages/products/[slug].jsx
import { useState, useEffect } from 'react';
import HreflangTags from '../../components/HreflangTags';

const ProductPage = ({ slug }) => {
  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchProduct();
  }, [slug]);

  const fetchProduct = async () => {
    try {
      const response = await fetch(`/api/v1/products/${slug}`);
      const data = await response.json();
      
      if (data.status) {
        setProduct(data.data.product);
      }
    } catch (error) {
      console.error('Error fetching product:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <div>Loading...</div>;
  if (!product) return <div>Product not found</div>;

  return (
    <>
      <HreflangTags alternates={product.alternates} />
      <main>
        <h1>{product.title}</h1>
        <p>{product.short_description}</p>
        <div>{product.detailed_description}</div>
      </main>
    </>
  );
};

export default ProductPage;
```

### 2. Vue.js Implementation

#### Hreflang Plugin
```javascript
// plugins/hreflang.js
export default {
  install(app) {
    app.config.globalProperties.$addHreflangTags = (alternates) => {
      if (!alternates || !alternates.length) return;

      // Remove existing hreflang tags
      document.querySelectorAll('link[rel="alternate"]').forEach(tag => tag.remove());

      // Add new hreflang tags
      alternates.forEach(alternate => {
        const link = document.createElement('link');
        link.rel = 'alternate';
        link.hreflang = alternate.locale;
        link.href = alternate.url;
        document.head.appendChild(link);
      });

      // Add x-default
      const defaultLink = document.createElement('link');
      defaultLink.rel = 'alternate';
      defaultLink.hreflang = 'x-default';
      defaultLink.href = alternates[0]?.url;
      document.head.appendChild(defaultLink);
    };
  }
};
```

#### Product Component
```vue
<!-- components/ProductDetail.vue -->
<template>
  <div v-if="product">
    <h1>{{ product.title }}</h1>
    <p>{{ product.short_description }}</p>
    <div v-html="product.detailed_description"></div>
  </div>
  <div v-else-if="loading">
    Loading...
  </div>
  <div v-else>
    Product not found
  </div>
</template>

<script>
export default {
  name: 'ProductDetail',
  props: ['slug'],
  data() {
    return {
      product: null,
      loading: true
    };
  },
  async mounted() {
    await this.fetchProduct();
  },
  methods: {
    async fetchProduct() {
      try {
        const response = await fetch(`/api/v1/products/${this.slug}`);
        const data = await response.json();
        
        if (data.status) {
          this.product = data.data.product;
          // Add hreflang tags
          this.$addHreflangTags(this.product.alternates);
        }
      } catch (error) {
        console.error('Error fetching product:', error);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>
```

### 3. Vanilla JavaScript Implementation

#### Hreflang Manager
```javascript
// utils/hreflang.js
class HreflangManager {
  static addTags(alternates) {
    if (!alternates || !alternates.length) return;

    // Remove existing hreflang tags
    document.querySelectorAll('link[rel="alternate"]').forEach(tag => tag.remove());

    // Add new hreflang tags
    alternates.forEach(alternate => {
      const link = document.createElement('link');
      link.rel = 'alternate';
      link.hreflang = alternate.locale;
      link.href = alternate.url;
      document.head.appendChild(link);
    });

    // Add x-default
    const defaultLink = document.createElement('link');
    defaultLink.rel = 'alternate';
    defaultLink.hreflang = 'x-default';
    defaultLink.href = alternates[0]?.url;
    document.head.appendChild(defaultLink);
  }

  static removeTags() {
    document.querySelectorAll('link[rel="alternate"]').forEach(tag => tag.remove());
  }
}

export default HreflangManager;
```

#### Product Page
```javascript
// pages/product.js
import HreflangManager from './utils/hreflang.js';

class ProductPage {
  constructor(slug) {
    this.slug = slug;
    this.product = null;
    this.init();
  }

  async init() {
    await this.fetchProduct();
    this.render();
  }

  async fetchProduct() {
    try {
      const response = await fetch(`/api/v1/products/${this.slug}`);
      const data = await response.json();
      
      if (data.status) {
        this.product = data.data.product;
        // Add hreflang tags
        HreflangManager.addTags(this.product.alternates);
      }
    } catch (error) {
      console.error('Error fetching product:', error);
    }
  }

  render() {
    const main = document.querySelector('main');
    
    if (!this.product) {
      main.innerHTML = '<h1>Product not found</h1>';
      return;
    }

    main.innerHTML = `
      <article>
        <h1>${this.product.title}</h1>
        <p>${this.product.short_description}</p>
        <div>${this.product.detailed_description}</div>
      </article>
    `;
  }
}

// Initialize page
const slug = window.location.pathname.split('/').pop();
new ProductPage(slug);
```

## Region-Specific Routing

### 1. Detect User Region
```javascript
// utils/regionDetection.js
export const detectUserRegion = async () => {
  // Try to get from localStorage first
  const savedRegion = localStorage.getItem('userRegion');
  if (savedRegion) return savedRegion;

  // Detect from browser locale
  const browserLocale = navigator.language || navigator.userLanguage;
  const localeToRegion = {
    'en-US': 'us',
    'en-GB': 'uk', 
    'en-CA': 'ca',
    'en-AU': 'au'
  };

  const detectedRegion = localeToRegion[browserLocale] || 'us';
  
  // Save to localStorage
  localStorage.setItem('userRegion', detectedRegion);
  
  return detectedRegion;
};
```

### 2. Region-Aware Navigation
```javascript
// utils/regionNavigation.js
export const navigateToRegion = (path, region) => {
  if (region && region !== 'us') {
    window.location.href = `/${region}${path}`;
  } else {
    window.location.href = path;
  }
};

export const getRegionUrl = (path, region) => {
  if (region && region !== 'us') {
    return `/${region}${path}`;
  }
  return path;
};
```

### 3. Region Selector Component
```jsx
// components/RegionSelector.jsx
import { useState, useEffect } from 'react';

const RegionSelector = ({ currentRegion, onRegionChange }) => {
  const regions = [
    { code: 'us', name: 'United States', flag: '🇺🇸' },
    { code: 'uk', name: 'United Kingdom', flag: '🇬🇧' },
    { code: 'eu', name: 'European Union', flag: '🇪🇺' },
    { code: 'ca', name: 'Canada', flag: '🇨🇦' },
    { code: 'au', name: 'Australia', flag: '🇦🇺' }
  ];

  const handleRegionChange = (newRegion) => {
    localStorage.setItem('userRegion', newRegion);
    
    // Get current path without region prefix
    const currentPath = window.location.pathname.replace(/^\/[a-z]{2}/, '');
    
    // Navigate to new region
    if (newRegion === 'us') {
      window.location.href = currentPath;
    } else {
      window.location.href = `/${newRegion}${currentPath}`;
    }
  };

  return (
    <div className="region-selector">
      <select 
        value={currentRegion} 
        onChange={(e) => handleRegionChange(e.target.value)}
        className="region-dropdown"
      >
        {regions.map(region => (
          <option key={region.code} value={region.code}>
            {region.flag} {region.name}
          </option>
        ))}
      </select>
    </div>
  );
};

export default RegionSelector;
```

## API Integration Examples

### 1. Axios Service
```javascript
// services/api.js
import axios from 'axios';

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Request interceptor for locale
api.interceptors.request.use((config) => {
  const locale = localStorage.getItem('userLocale') || 'en';
  config.params = { ...config.params, lang: locale };
  return config;
});

// Response interceptor for error handling
api.interceptors.response.use(
  (response) => response,
  (error) => {
    console.error('API Error:', error);
    return Promise.reject(error);
  }
);

export default api;
```

### 2. Product Service
```javascript
// services/productService.js
import api from './api';

export const productService = {
  async getProducts(params = {}) {
    const response = await api.get('/products', { params });
    return response.data;
  },

  async getProduct(slug) {
    const response = await api.get(`/products/${slug}`);
    return response.data;
  },

  async getProductsByRegion(region, params = {}) {
    const response = await api.get('/products', { 
      params: { ...params, region } 
    });
    return response.data;
  }
};
```

### 3. React Hook for Products
```javascript
// hooks/useProducts.js
import { useState, useEffect } from 'react';
import { productService } from '../services/productService';

export const useProducts = (params = {}) => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchProducts();
  }, [JSON.stringify(params)]);

  const fetchProducts = async () => {
    try {
      setLoading(true);
      const response = await productService.getProducts(params);
      setProducts(response.data.products);
      setError(null);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  return { products, loading, error, refetch: fetchProducts };
};

export const useProduct = (slug) => {
  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (slug) fetchProduct();
  }, [slug]);

  const fetchProduct = async () => {
    try {
      setLoading(true);
      const response = await productService.getProduct(slug);
      setProduct(response.data.product);
      setError(null);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  return { product, loading, error, refetch: fetchProduct };
};
```

## SEO Best Practices for Frontend

### 1. Meta Tags Implementation
```jsx
// components/SEOMetaTags.jsx
import Head from 'next/head';

const SEOMetaTags = ({ product, alternates }) => {
  return (
    <Head>
      <title>{product.title} | Your Store</title>
      <meta name="description" content={product.short_description} />
      <meta property="og:title" content={product.title} />
      <meta property="og:description" content={product.short_description} />
      <meta property="og:url" content={window.location.href} />
      
      {/* Hreflang tags */}
      {alternates?.map((alternate, index) => (
        <link
          key={index}
          rel="alternate"
          hrefLang={alternate.locale}
          href={alternate.url}
        />
      ))}
      <link rel="alternate" hrefLang="x-default" href={alternates?.[0]?.url} />
    </Head>
  );
};

export default SEOMetaTags;
```

### 2. Structured Data
```jsx
// components/StructuredData.jsx
const StructuredData = ({ product }) => {
  const structuredData = {
    "@context": "https://schema.org",
    "@type": "Product",
    "name": product.title,
    "description": product.short_description,
    "url": window.location.href
  };

  return (
    <script
      type="application/ld+json"
      dangerouslySetInnerHTML={{ __html: JSON.stringify(structuredData) }}
    />
  );
};

export default StructuredData;
```

## Error Handling

### 1. API Error Handling
```javascript
// utils/errorHandler.js
export const handleApiError = (error) => {
  if (error.response) {
    // Server responded with error status
    console.error('API Error:', error.response.data);
    return error.response.data.message || 'An error occurred';
  } else if (error.request) {
    // Request was made but no response received
    console.error('Network Error:', error.request);
    return 'Network error. Please check your connection.';
  } else {
    // Something else happened
    console.error('Error:', error.message);
    return 'An unexpected error occurred.';
  }
};
```

### 2. Fallback for Missing Alternates
```javascript
// utils/hreflangFallback.js
export const getAlternatesWithFallback = (item, defaultLocale = 'en') => {
  if (!item.alternates || item.alternates.length === 0) {
    // Create fallback alternates
    const baseUrl = window.location.origin;
    return [
      {
        locale: defaultLocale,
        url: `${baseUrl}${item.getUrl?.() || ''}`
      }
    ];
  }
  return item.alternates;
};
```

## Testing

### 1. Unit Test Example (Jest)
```javascript
// __tests__/hreflang.test.js
import HreflangManager from '../utils/hreflang';

describe('HreflangManager', () => {
  beforeEach(() => {
    document.head.innerHTML = '';
  });

  test('adds hreflang tags correctly', () => {
    const alternates = [
      { locale: 'en', url: 'https://example.com/en/page' },
      { locale: 'es', url: 'https://example.com/es/page' }
    ];

    HreflangManager.addTags(alternates);

    const links = document.querySelectorAll('link[rel="alternate"]');
    expect(links).toHaveLength(2);
    expect(links[0].hreflang).toBe('en');
    expect(links[1].hreflang).toBe('es');
  });

  test('removes existing tags before adding new ones', () => {
    // Add initial tags
    const link1 = document.createElement('link');
    link1.rel = 'alternate';
    link1.hreflang = 'en';
    document.head.appendChild(link1);

    expect(document.querySelectorAll('link[rel="alternate"]')).toHaveLength(1);

    // Add new tags
    HreflangManager.addTags([
      { locale: 'es', url: 'https://example.com/es/page' }
    ]);

    const links = document.querySelectorAll('link[rel="alternate"]');
    expect(links).toHaveLength(1);
    expect(links[0].hreflang).toBe('es');
  });
});
```

This frontend guide provides comprehensive examples for implementing international SEO features in various frontend frameworks and scenarios.
