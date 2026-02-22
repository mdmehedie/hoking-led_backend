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
- **Headers**: None required

#### Response

##### Success (200 OK)

```json
{
  "status": true,
  "message": "Case studies retrieved successfully",
  "data": {
    "case_studies": [
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
    ]
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
fetch('/api/v1/case-studies')
  .then(response => response.json())
  .then(data => {
    if (data.status) {
      console.log('Case Studies:', data.data.case_studies);
    } else {
      console.error('Error:', data.message);
    }
  })
  .catch(error => console.error('Fetch error:', error));
```

## Notes

- Only case studies with `status: "published"` are returned.
- Case studies are sorted by `published_at` in descending order (newest first).
- Title and content fields contain HTML for rich text formatting.
