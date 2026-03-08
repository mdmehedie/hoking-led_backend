# Products API Documentation - Multilingual Support

## Overview

This API provides endpoints to retrieve product data for the frontend application with comprehensive multilingual support. All responses include localized messages and translations.

## Base URL

http://localhost:8000/api

## Language Selection

The API supports multiple methods for language selection:

### Method 1: Query Parameter (Recommended)
```
GET /v1/products?lang=bd
```

### Method 2: Accept-Language Header
```
GET /v1/products
Accept-Language: bd, en;q=0.9
```

### Method 3: Session Locale
For authenticated users, the API respects the session locale.

## Response Structure

All API responses now include localization support:

```json
{
  "status": true,
  "message": "Products retrieved successfully",
  "data": { ... },
  "locale": "en",
  "translations": {
    "loading": "Loading...",
    "error": "Error",
    "success": "Success",
    "no_data": "No data available",
    "not_found": "Not found",
    "server_error": "Server error",
    "try_again": "Please try again",
    "close": "Close",
    "cancel": "Cancel",
    "confirm": "Confirm",
    "yes": "Yes",
    "no": "No",
    "ok": "OK",
    "save": "Save",
    "edit": "Edit",
    "delete": "Delete",
    "view": "View",
    "search": "Search",
    "filter": "Filter",
    "sort": "Sort",
    "page": "Page",
    "of": "of",
    "items_per_page": "Items per page",
    "showing": "Showing",
    "to": "to",
    "of_total": "of total",
    "previous": "Previous",
    "next": "Next",
    "first": "First",
    "last": "Last"
  }
}
```

## Endpoints

### Get Products

Retrieves a list of all published products, optionally filtered by category.

#### Request

- **Method**: GET
- **URL**: /v1/products
- **Query Parameters**:
  - `category_id` (optional): Filter products by category ID
  - `per_page` (optional): Number of items per page (default 10)
  - `lang` (optional): Language code (en, bd)
- **Headers**:
  - `Accept`: application/json
  - `Accept-Language` (optional): Language preference

#### Response

##### Success (200 OK) - English

```json
{
  "status": true,
  "message": "Products retrieved successfully",
  "data": {
    "products": {
      "data": [
        {
          "id": 1,
          "title": "Sample Product Title",
          "slug": "sample-product-title",
          "short_description": "Brief description.",
          "detailed_description": "Full detailed description.",
          "category_id": 1,
          "status": "published",
          "published_at": "2023-01-01T00:00:00.000000Z",
          "author_id": 1,
          "is_featured": false,
          "image_path": "http://localhost:8000/storage/products/image.jpg",
          "created_at": "2023-01-01T00:00:00.000000Z",
          "updated_at": "2023-01-01T00:00:00.000000Z"
        }
      ],
      "links": { ... },
      "meta": { ... }
    }
  },
  "locale": "en",
  "translations": {
    "loading": "Loading...",
    "error": "Error",
    "success": "Success",
    // ... all common translations
  }
}
```

##### Success (200 OK) - Bangla

```json
{
  "status": true,
  "message": "পণ্যসমূহ সফলতাভারভে",
  "data": {
    "products": {
      "data": [
        {
          "id": 1,
          "title": "নমুনা পণ্যের শিরোনাম",
          "slug": "sample-product-title",
          "short_description": "সংক্ষিপ্ত বর্ণনা।",
          "detailed_description": "সম্পূর্ণ বিস্তারিত বর্ণনা।",
          "category_id": 1,
          "status": "published",
          "published_at": "2023-01-01T00:00:00.000000Z",
          "author_id": 1,
          "is_featured": false,
          "image_path": "http://localhost:8000/storage/products/image.jpg",
          "created_at": "2023-01-01T00:00:00.000000Z",
          "updated_at": "2023-01-01T00:00:00.000000Z"
        }
      ],
      "links": { ... },
      "meta": { ... }
    }
  },
  "locale": "bd",
  "translations": {
    "loading": "লোড হচ্ছে...",
    "error": "ত্রুটি",
    "success": "সফলতা",
    // ... all common translations in Bangla
  }
}
```

##### Error (404 Not Found)

```json
{
  "status": false,
  "message": "Product not found",
  "data": {},
  "locale": "en",
  "translations": {
    "error": "Error",
    "not_found": "Not found",
    "try_again": "Please try again"
  }
}
```

### Get Single Product

Retrieves a single product by slug.

#### Request

- **Method**: GET
- **URL**: /v1/products/{slug}
- **Path Parameters**:
  - `slug`: Product slug
- **Query Parameters**:
  - `lang` (optional): Language code (en, bd)
- **Headers**:
  - `Accept`: application/json
  - `Accept-Language` (optional): Language preference

#### Response

##### Success (200 OK)

```json
{
  "status": true,
  "message": "Product retrieved successfully",
  "data": {
    "product": {
      "id": 1,
      "title": "Sample Product Title",
      "slug": "sample-product-title",
      "short_description": "Brief description.",
      "detailed_description": "Full detailed description.",
      "category_id": 1,
      "status": "published",
      "published_at": "2023-01-01T00:00:00.000000Z",
      "author_id": 1,
      "is_featured": false,
      "image_path": "http://localhost:8000/storage/products/image.jpg",
      "created_at": "2023-01-01T00:00:00.000000Z",
      "updated_at": "2023-01-01T00:00:00.000000Z"
    }
  },
  "locale": "en",
  "translations": { ... }
}
```

##### Error (404 Not Found)

```json
{
  "status": false,
  "message": "Product not found",
  "data": {},
  "locale": "en",
  "translations": { ... }
}
```

## Implementation Examples

### JavaScript/React Example

```javascript
// Get products with language preference
const getProducts = async (lang = 'en') => {
  try {
    const response = await fetch(`/api/v1/products?lang=${lang}`, {
      headers: {
        'Accept': 'application/json',
        'Accept-Language': `${lang}, en;q=0.9`
      }
    });
    
    const data = await response.json();
    
    // Use localized message
    console.log(data.message); // "Products retrieved successfully" or "পণ্যসমূহ সফলতাভারভে"
    
    // Use common translations
    const translations = data.translations;
    console.log(translations.loading); // "Loading..." or "লোড হচ্ছে..."
    
    return data;
  } catch (error) {
    console.error('Error:', error);
  }
};
```

### cURL Examples

```bash
# English (default)
curl "http://localhost:8000/api/v1/products"

# Bangla via query parameter
curl "http://localhost:8000/api/v1/products?lang=bd"

# Bangla via Accept-Language header
curl -H "Accept-Language: bd, en;q=0.9" \
     "http://localhost:8000/api/v1/products"

# Single product in Bangla
curl -H "Accept-Language: bd" \
     "http://localhost:8000/api/v1/products/sample-product"
```

## Supported Languages

- **en** - English (default)
- **bd** - Bangla

## Error Handling

All error responses include:
- Localized error messages
- Current locale
- Common translations for UI elements
- Appropriate HTTP status codes

## Best Practices

1. **Always include locale** in API requests for consistent user experience
2. **Use the translations object** for common UI text to avoid hardcoding
3. **Handle fallback gracefully** when translations are missing
4. **Cache locale preferences** in browser storage for better UX
5. **Test with all supported locales** to ensure consistency

## Rate Limiting & Caching

- API responses are cached based on locale to improve performance
- Rate limiting is applied per IP address
- Cache invalidation occurs when translations are updated
        }
      ],
      "links": {
        "first": "http://localhost:8000/api/v1/products?page=1",
        "last": "http://localhost:8000/api/v1/products?page=1",
        "prev": null,
        "next": null
      },
      "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "per_page": 10,
        "to": 1,
        "total": 1
      }
    }
  }
}
```

##### Fields Description

- `id`: Unique identifier for the product.
- `title`: Product title.
- `slug`: URL-friendly slug.
- `short_description`: Brief description.
- `detailed_description`: Full product description.
- `category_id`: ID of the associated category.
- `status`: Publication status.
- `published_at`: Publication date.
- `author_id`: ID of the author.
- `is_featured`: Boolean indicating if featured.
- `image_path`: Full URL to the product image.
- `created_at` / `updated_at`: Timestamps.
- `links`: Pagination links.
- `meta`: Pagination metadata (current_page, per_page, total, etc.).

#### Error Responses

- **500 Internal Server Error**: Server error.

```json
{
  "status": false,
  "message": "Internal Server Error",
  "data": {}
}
```

### Get Product by Slug

Retrieves a single published product by its slug.

#### Request

- **Method**: GET
- **URL**: /v1/products/{slug}
- **Parameters**:
  - `slug`: The slug of the product
- **Headers**: None required

#### Response

##### Success (200 OK)

```json
{
  "status": true,
  "message": "Product retrieved successfully",
  "data": {
    "product": {
      "id": 1,
      "title": "Sample Product Title",
      "slug": "sample-product-title",
      "short_description": "Brief description.",
      "detailed_description": "Full detailed description.",
      "category_id": 1,
      "status": "published",
      "published_at": "2023-01-01T00:00:00.000000Z",
      "author_id": 1,
      "is_featured": false,
      "image_path": "http://localhost:8000/storage/products/image.jpg",
      "created_at": "2023-01-01T00:00:00.000000Z",
      "updated_at": "2023-01-01T00:00:00.000000Z",
      "meta_title": "SEO Title",
      "meta_description": "SEO Description",
      "meta_keywords": "keyword1, keyword2"
    }
  }
}
```

##### Error Responses

- **404 Not Found**: Product not found or not published.

```json
{
  "status": false,
  "message": "Model not found",
  "data": {}
}
```

- **500 Internal Server Error**: Server error.

```json
{
  "status": false,
  "message": "Internal Server Error",
  "data": {}
}
```

## Usage Example (React)

```javascript
// Get all products (paginated)
fetch('/api/v1/products')
  .then(response => response.json())
  .then(data => {
    if (data.status) {
      console.log('Products:', data.data.products.data); // Array of products
      console.log('Pagination:', data.data.products.meta); // Meta info
    }
  });

// Get products by category
fetch('/api/v1/products?category_id=1&per_page=5')
  .then(response => response.json())
  .then(data => {
    if (data.status) {
      console.log('Products in category:', data.data.products.data);
    }
  });

// Get single product
fetch('/api/v1/products/sample-product-title')
  .then(response => response.json())
  .then(data => {
    if (data.status) {
      console.log('Product:', data.data.product);
      // Use data.data.product.meta_title for <title>, etc.
    }
  });
```

## Notes

- Only products with `status: "published"` are returned.
- Products are sorted by the `title` field.
- Use `category_id` query parameter to filter products by category (supports category-wise filtering).
- The index endpoint supports pagination with `per_page` (default 10) and `page` parameters.
- The show endpoint includes SEO fields for frontend meta tags.
