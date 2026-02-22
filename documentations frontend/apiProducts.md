# Products API Documentation

## Overview

This API provides endpoints to retrieve product data for the frontend application.

## Base URL

http://localhost:8000/api

## Endpoints

### Get Products

Retrieves a list of all published products, optionally filtered by category.

#### Request

- **Method**: GET
- **URL**: /v1/products
- **Query Parameters**:
  - `category_id` (optional): Filter products by category ID
  - `per_page` (optional): Number of items per page (default 10)
- **Headers**: None required

#### Response

##### Success (200 OK)

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
