# Testimonials API Documentation

## Overview

The Testimonials API allows you to retrieve customer testimonials for display on your website. Testimonials are filtered to show only visible testimonials and are ordered by their sort order.

## Base URL

```
https://your-domain.com/api/v1
```

## Authentication

No authentication required for public testimonial retrieval.

## Endpoints

### Get Testimonials

Retrieve a paginated list of visible testimonials.

**Endpoint:** `GET /testimonials`

**Description:** Returns all visible testimonials ordered by sort order.

#### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `per_page` | integer | Optional | Number of testimonials per page (default: 10, max: 100) |

#### Response

**Status Code:** `200 OK`

**Response Format:**
```json
{
    "success": true,
    "message": "Testimonials retrieved successfully",
    "data": {
        "testimonials": {
            "current_page": 1,
            "data": [
                {
                    "id": 1,
                    "client_name": "John Doe",
                    "client_position": "CEO",
                    "client_company": "Tech Corp",
                    "testimonial": "Great service and excellent quality!",
                    "rating": 5,
                    "image": "https://your-domain.com/storage/testimonials/john-doe-avatar.jpg",
                    "is_visible": true,
                    "sort_order": 1,
                    "created_at": "2026-02-28T10:00:00.000000Z",
                    "updated_at": "2026-02-28T10:00:00.000000Z"
                }
            ],
            "first_page_url": "https://your-domain.com/api/v1/testimonials?page=1",
            "from": 1,
            "last_page": 1,
            "last_page_url": "https://your-domain.com/api/v1/testimonials?page=1",
            "links": [
                {
                    "url": null,
                    "label": "Previous",
                    "active": false
                },
                {
                    "url": "https://your-domain.com/api/v1/testimonials?page=1",
                    "label": "1",
                    "active": true
                },
                {
                    "url": null,
                    "label": "Next",
                    "active": false
                }
            ],
            "next_page_url": null,
            "path": "https://your-domain.com/api/v1/testimonials",
            "per_page": 10,
            "prev_page_url": null,
            "to": 1,
            "total": 1
        }
    }
}
```

#### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `success` | boolean | Operation success status |
| `message` | string | Response message |
| `data.testimonials` | object | Paginated testimonials data |
| `data.testimonials.data[]` | array | Array of testimonial objects |
| `id` | integer | Testimonial unique identifier |
| `client_name` | string | Client's full name |
| `client_position` | string | Client's job position (nullable) |
| `client_company` | string | Client's company name (nullable) |
| `testimonial` | string | The testimonial content |
| `rating` | integer | Rating from 1-5 stars |
| `image` | string | Full URL to client's image (nullable) |
| `is_visible` | boolean | Whether testimonial is visible |
| `sort_order` | integer | Display order |
| `created_at` | datetime | Creation timestamp |
| `updated_at` | datetime | Last update timestamp |

#### Error Responses

**Status Code:** `500 Internal Server Error`

```json
{
    "success": false,
    "message": "Internal server error",
    "data": null
}
```

## Usage Examples

### Get All Testimonials

```bash
curl -X GET "https://your-domain.com/api/v1/testimonials" \
     -H "Accept: application/json"
```

### Get Testimonials with Custom Pagination

```bash
curl -X GET "https://your-domain.com/api/v1/testimonials?per_page=20" \
     -H "Accept: application/json"
```

### JavaScript/Axios Example

```javascript
// Get testimonials
const response = await axios.get('/api/v1/testimonials');
const testimonials = response.data.data.testimonials.data;

// Display testimonials
testimonials.forEach(testimonial => {
    console.log(testimonial.client_name, testimonial.testimonial, testimonial.rating);
});
```

### PHP/Guzzle Example

```php
use GuzzleHttp\Client;

$client = new Client();
$response = $client->get('https://your-domain.com/api/v1/testimonials');
$data = json_decode($response->getBody(), true);

$testimonials = $data['data']['testimonials']['data'];
foreach ($testimonials as $testimonial) {
    echo $testimonial['client_name'] . ': ' . $testimonial['testimonial'] . PHP_EOL;
}
```

## Notes

- Only testimonials with `is_visible = true` are returned
- Testimonials are ordered by `sort_order` (ascending)
- Images are served from the storage directory
- Pagination follows Laravel's standard pagination format
- SEO fields (`meta_title`, `meta_description`, `meta_keywords`) are not included in API responses
