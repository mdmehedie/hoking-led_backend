# Case Studies API Documentation

## Overview

This API provides endpoints to retrieve case study data for the frontend application.

## Base URL

http://localhost:8000/api

## Endpoints

### Get Case Studies

Retrieves a list of all published case studies.

#### Request

- **Method**: GET
- **URL**: /v1/case-studies
- **Query Parameters**:
  - `per_page` (optional): Number of items per page (default 10)
- **Headers**: None required

#### Response

##### Success (200 OK)

```json
{
  "status": true,
  "message": "Case studies retrieved successfully",
  "data": {
    "case_studies": {
      "data": [
        {
          "id": 1,
          "title": "Sample Case Study Title",
          "slug": "sample-case-study-title",
          "description": "Brief description of the case study.",
          "content": "Full HTML content of the case study.",
          "image_path": "http://localhost:8000/storage/case-studies/image.jpg",
          "published_at": "2023-01-01T00:00:00.000000Z",
          "author_id": 1,
          "status": "published",
          "created_at": "2023-01-01T00:00:00.000000Z",
          "updated_at": "2023-01-01T00:00:00.000000Z"
        }
      ],
      "links": {
        "first": "http://localhost:8000/api/v1/case-studies?page=1",
        "last": "http://localhost:8000/api/v1/case-studies?page=1",
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

- `id`: Unique identifier for the case study.
- `title`: Case study title (HTML content).
- `slug`: URL-friendly slug.
- `description`: Short description.
- `content`: Full case study content in HTML.
- `image_path`: Full URL to the case study image.
- `published_at`: Publication date.
- `author_id`: ID of the author.
- `status`: Publication status.
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

### Get Case Study by Slug

Retrieves a single published case study by its slug.

#### Request

- **Method**: GET
- **URL**: /v1/case-studies/{slug}
- **Parameters**:
  - `slug`: The slug of the case study
- **Headers**: None required

#### Response

##### Success (200 OK)

```json
{
  "status": true,
  "message": "Case study retrieved successfully",
  "data": {
    "case_study": {
      "id": 1,
      "title": "Sample Case Study Title",
      "slug": "sample-case-study-title",
      "description": "Brief description of the case study.",
      "content": "Full HTML content of the case study.",
      "image_path": "http://localhost:8000/storage/case-studies/image.jpg",
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

- **404 Not Found**: Case study not found or not published.

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
// Get all case studies (paginated)
fetch('/api/v1/case-studies')
  .then(response => response.json())
  .then(data => {
    if (data.status) {
      console.log('Case Studies:', data.data.case_studies.data); // Array of case studies
      console.log('Pagination:', data.data.case_studies.meta); // Meta info
    } else {
      console.error('Error:', data.message);
    }
  });

// Get single case study
fetch('/api/v1/case-studies/sample-case-study-title')
  .then(response => response.json())
  .then(data => {
    if (data.status) {
      console.log('Case Study:', data.data.case_study);
      // Use data.data.case_study.meta_title for <title>, etc.
    }
  });
```

## Notes

- Only case studies with `status: "published"` are returned.
- Case studies are sorted by `published_at` in descending order (newest first).
- Title and content fields contain HTML for rich text formatting.
- The index endpoint supports pagination with `per_page` (default 10) and `page` parameters.
