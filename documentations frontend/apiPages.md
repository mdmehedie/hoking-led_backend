# Pages API Documentation

## Overview

This API provides endpoints to retrieve page data for the frontend application.

## Base URL

http://localhost:8000/api

## Endpoints

### Get Pages

Retrieves a list of all published pages.

#### Request

- **Method**: GET
- **URL**: /v1/pages
- **Headers**: None required

#### Response

##### Success (200 OK)

```json
{
  "status": true,
  "message": "Pages retrieved successfully",
  "data": {
    "pages": [
      {
        "id": 1,
        "title": "Sample Page Title",
        "slug": "sample-page-title",
        "description": "Brief description of the page.",
        "content": "Full HTML content of the page.",
        "image_path": "http://localhost:8000/storage/pages/image.jpg",
        "published_at": "2023-01-01T00:00:00.000000Z",
        "author_id": 1,
        "status": "published",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
      }
    ]
  }
}
```

##### Fields Description

- `id`: Unique identifier for the page.
- `title`: Page title (HTML content).
- `slug`: URL-friendly slug.
- `description`: Short description.
- `content`: Full page content in HTML.
- `image_path`: Full URL to the page image.
- `published_at`: Publication date.
- `author_id`: ID of the author.
- `status`: Publication status.
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

### Get Page by Slug

Retrieves a single published page by its slug.

#### Request

- **Method**: GET
- **URL**: /v1/pages/{slug}
- **Parameters**:
  - `slug`: The slug of the page
- **Headers**: None required

#### Response

##### Success (200 OK)

```json
{
  "status": true,
  "message": "Page retrieved successfully",
  "data": {
    "page": {
      "id": 1,
      "title": "Sample Page Title",
      "slug": "sample-page-title",
      "description": "Brief description of the page.",
      "content": "Full HTML content of the page.",
      "image_path": "http://localhost:8000/storage/pages/image.jpg",
      "published_at": "2023-01-01T00:00:00.000000Z",
      "author_id": 1,
      "status": "published",
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

- **404 Not Found**: Page not found or not published.

```json
{
  "status": false,
  "message": "Model not found",
  "data": {}
}
```

- **500 Internal Server Error**: Server error.

## Usage Example (React)

```javascript
// Get all pages
fetch('/api/v1/pages')
  .then(response => response.json())
  .then(data => {
    if (data.status) {
      console.log('Pages:', data.data.pages);
    } else {
      console.error('Error:', data.message);
    }
  });

// Get single page
fetch('/api/v1/pages/sample-page-title')
  .then(response => response.json())
  .then(data => {
    if (data.status) {
      console.log('Page:', data.data.page);
      // Use data.data.page.meta_title for <title>, etc.
    }
  });
```

## Notes

- Only pages with `status: "published"` are returned.
- Pages are sorted by `published_at` in descending order (newest first).
- Title and content fields contain HTML for rich text formatting.
