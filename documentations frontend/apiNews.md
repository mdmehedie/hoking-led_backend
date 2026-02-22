# News API Documentation

## Overview

This API provides endpoints to retrieve news data for the frontend application.

## Base URL

http://localhost:8000/api

## Endpoints

### Get News

Retrieves a list of all published news.

#### Request

- **Method**: GET
- **URL**: /v1/news
- **Headers**: None required

#### Response

##### Success (200 OK)

```json
{
  "status": true,
  "message": "News retrieved successfully",
  "data": {
    "news": [
      {
        "id": 1,
        "title": "Sample News Title",
        "slug": "sample-news-title",
        "description": "Brief description of the news.",
        "content": "Full HTML content of the news.",
        "image_path": "http://localhost:8000/storage/news/image.jpg",
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

- `id`: Unique identifier for the news.
- `title`: News title (HTML content).
- `slug`: URL-friendly slug.
- `description`: Short description.
- `content`: Full news content in HTML.
- `image_path`: Full URL to the news image.
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

### Get News by Slug

Retrieves a single published news by its slug.

#### Request

- **Method**: GET
- **URL**: /v1/news/{slug}
- **Parameters**:
  - `slug`: The slug of the news
- **Headers**: None required

#### Response

##### Success (200 OK)

```json
{
  "status": true,
  "message": "News retrieved successfully",
  "data": {
    "news": {
      "id": 1,
      "title": "Sample News Title",
      "slug": "sample-news-title",
      "description": "Brief description of the news.",
      "content": "Full HTML content of the news.",
      "image_path": "http://localhost:8000/storage/news/image.jpg",
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

- **404 Not Found**: News not found or not published.

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
// Get all news
fetch('/api/v1/news')
  .then(response => response.json())
  .then(data => {
    if (data.status) {
      console.log('News:', data.data.news);
    } else {
      console.error('Error:', data.message);
    }
  });

// Get single news
fetch('/api/v1/news/sample-news-title')
  .then(response => response.json())
  .then(data => {
    if (data.status) {
      console.log('News:', data.data.news);
      // Use data.data.news.meta_title for <title>, etc.
    }
  });
```

## Notes

- Only news with `status: "published"` are returned.
- News are sorted by `published_at` in descending order (newest first).
- Title and content fields contain HTML for rich text formatting.
