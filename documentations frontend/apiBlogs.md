# Blogs API Documentation

## Overview

This API provides endpoints to retrieve blog data for the frontend application.

## Base URL

http://localhost:8000/api

## Endpoints

### Get Blogs

Retrieves a list of all published blogs.

#### Request

- **Method**: GET
- **URL**: /v1/blogs
- **Headers**: None required

#### Response

##### Success (200 OK)

```json
{
  "status": true,
  "message": "Blogs retrieved successfully",
  "data": {
    "blogs": [
      {
        "id": 1,
        "title": "Sample Blog Title",
        "slug": "sample-blog-title",
        "description": "Brief description of the blog.",
        "content": "Full HTML content of the blog.",
        "image_path": "http://localhost:8000/storage/blogs/image.jpg",
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

- `id`: Unique identifier for the blog.
- `title`: Blog title (HTML content).
- `slug`: URL-friendly slug.
- `description`: Short description.
- `content`: Full blog content in HTML.
- `image_path`: Full URL to the blog image.
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

## Usage Example (React)

```javascript
fetch('/api/v1/blogs')
  .then(response => response.json())
  .then(data => {
    if (data.status) {
      console.log('Blogs:', data.data.blogs);
    } else {
      console.error('Error:', data.message);
    }
  })
  .catch(error => console.error('Fetch error:', error));
```

## Notes

- Only blogs with `status: "published"` are returned.
- Blogs are sorted by `published_at` in descending order (newest first).
- Title and content fields contain HTML for rich text formatting.
