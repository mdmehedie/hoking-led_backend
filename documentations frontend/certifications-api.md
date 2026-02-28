# Certifications & Awards API Documentation

## Overview
The Certifications & Awards API provides endpoints to retrieve certification and award information for the frontend application.

## Base URL
```
https://your-domain.com/api/v1
```

## Authentication
No authentication required for frontend API endpoints.

## Endpoints

### 1. List Certifications
Get a paginated list of visible certifications.

**Endpoint:** `GET /api/v1/certifications`

**Query Parameters:**
- `per_page` (optional): Number of items per page (default: 10)
- `year` (optional): Filter by award year (e.g., 2024)

**Example Requests:**
```bash
# Get all certifications (default pagination)
GET /api/v1/certifications

# Get certifications with custom pagination
GET /api/v1/certifications?per_page=5

# Get certifications from specific year
GET /api/v1/certifications?year=2024

# Combine parameters
GET /api/v1/certifications?year=2024&per_page=10
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "certifications": {
      "current_page": 1,
      "data": [
        {
          "id": 1,
          "title": "AWS Certified Solutions Architect",
          "slug": "aws-certified-solutions-architect",
          "issuing_organization": "Amazon Web Services",
          "date_awarded": "2024-03-15",
          "description": "Professional certification demonstrating expertise in AWS solutions architecture.",
          "image": "/storage/certifications/aws-cert.png",
          "is_visible": true,
          "sort_order": 1,
          "created_at": "2024-03-15T10:00:00.000000Z",
          "updated_at": "2024-03-15T10:00:00.000000Z"
        }
      ],
      "first_page_url": "https://your-domain.com/api/v1/certifications?page=1",
      "from": 1,
      "last_page": 1,
      "last_page_url": "https://your-domain.com/api/v1/certifications?page=1",
      "links": [
        {
          "url": null,
          "label": "&laquo; Previous",
          "active": false
        },
        {
          "url": "https://your-domain.com/api/v1/certifications?page=1",
          "label": "1",
          "active": true
        },
        {
          "url": null,
          "label": "Next &raquo;",
          "active": false
        }
      ],
      "next_page_url": null,
      "path": "https://your-domain.com/api/v1/certifications",
      "per_page": 10,
      "prev_page_url": null,
      "to": 1,
      "total": 1
    }
  },
  "message": "Certifications retrieved successfully"
}
```

### 2. Get Certification by Slug
Get a specific certification by its slug.

**Endpoint:** `GET /api/v1/certifications/{slug}`

**Path Parameters:**
- `slug`: The URL-friendly identifier of the certification

**Example Request:**
```bash
GET /api/v1/certifications/aws-certified-solutions-architect
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "certification": {
      "id": 1,
      "title": "AWS Certified Solutions Architect",
      "slug": "aws-certified-solutions-architect",
      "issuing_organization": "Amazon Web Services",
      "date_awarded": "2024-03-15",
      "description": "Professional certification demonstrating expertise in AWS solutions architecture.",
      "image": "/storage/certifications/aws-cert.png",
      "is_visible": true,
      "sort_order": 1,
      "created_at": "2024-03-15T10:00:00.000000Z",
      "updated_at": "2024-03-15T10:00:00.000000Z"
    }
  },
  "message": "Certification retrieved successfully"
}
```

**Error Response (404):**
```json
{
  "success": false,
  "data": [],
  "message": "Certification not found"
}
```

## Data Fields

### Certification Object
| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Unique identifier |
| `title` | string | Certification title |
| `slug` | string | URL-friendly identifier |
| `issuing_organization` | string | Organization that issued the certification |
| `date_awarded` | date | Date the certification was awarded |
| `description` | text | Detailed description |
| `image` | string | Path to certification image |
| `is_visible` | boolean | Whether the certification is publicly visible |
| `sort_order` | integer | Display order (lower numbers appear first) |
| `created_at` | datetime | Creation timestamp |
| `updated_at` | datetime | Last update timestamp |

## Filtering & Sorting

### Year Filtering
Filter certifications by the year they were awarded:
```bash
GET /api/v1/certifications?year=2024
```

### Sorting
Certifications are sorted by `sort_order` (ascending) by default.

## Pagination

The API uses Laravel's pagination. Response includes:
- `current_page`: Current page number
- `data`: Array of certification objects
- `first_page_url`: URL to first page
- `last_page_url`: URL to last page
- `next_page_url`: URL to next page (null if no next page)
- `prev_page_url`: URL to previous page (null if no previous page)
- `per_page`: Items per page
- `total`: Total number of items

## Error Handling

All endpoints return consistent error responses:

**404 Not Found:**
```json
{
  "success": false,
  "data": [],
  "message": "Certification not found"
}
```

**500 Internal Server Error:**
```json
{
  "success": false,
  "data": [],
  "message": "Internal server error"
}
```

## Rate Limiting
No rate limiting is currently implemented for these endpoints.

## Notes
- Only certifications with `is_visible = true` are returned
- Images are stored in the `/storage/certifications/` directory
- Slugs are auto-generated and must be unique
- The API follows RESTful conventions
