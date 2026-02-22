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
- **Headers**: None required

#### Response

##### Success (200 OK)

```json
{
  "status": true,
  "message": "Products retrieved successfully",
  "data": {
    "products": [
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
    ]
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

#### Error Responses

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
// Get all products
fetch('/api/v1/products')
  .then(response => response.json())
  .then(data => {
    if (data.status) {
      console.log('Products:', data.data.products);
    }
  });

// Get products by category
fetch('/api/v1/products?category_id=1')
  .then(response => response.json())
  .then(data => {
    if (data.status) {
      console.log('Products in category:', data.data.products);
    }
  });
```

## Notes

- Only products with `status: "published"` are returned.
- Products are sorted by the `order` field.
- Use `category_id` query parameter to filter products by category (supports category-wise filtering).
