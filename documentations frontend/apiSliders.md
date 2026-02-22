# Sliders API Documentation

## Overview

This API provides endpoints to retrieve slider data for the frontend application.

## Base URL

All API endpoints are prefixed with `http://localhost:8000/api/v1`.

## Authentication

Not required for this endpoint (to be added later).

## Endpoints

### Get Sliders

Retrieves a list of all active sliders.

#### Request

- **Method**: GET
- **URL**: `/v1/sliders`
- **Headers**: None required

#### Response

##### Success (200 OK)

```json
{
  "status": true,
  "message": "Sliders retrieved successfully",
  "data": {
    "sliders": [
      {
        "id": 1,
        "title": "<p>Demo Slider 01</p>",
        "description": "<p>This is a sample slider description with <strong>bold text</strong>.</p>",
        "image_path": "sliders/slider-image.jpg",
        "link": "https://example.com",
        "alt_text": "Slider Alt Text",
        "order": 1,
        "status": true,
        "custom_styles": {
          "link_class": "btn btn-primary",
          "alt_text_class": "text-muted"
        },
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
      }
    ]
  }
}
```

##### Fields Description

- `id`: Unique identifier for the slider.
- `title`: HTML content for the slider title (can include inline styles).
- `description`: HTML content for the slider description (can include inline styles).
- `image_path`: Path to the slider image file.
- `link`: URL for the slider link.
- `alt_text`: Alt text for the image.
- `order`: Display order of the slider.
- `status`: Boolean indicating if the slider is active.
- `custom_styles`: Object with CSS classes for link and alt_text elements.
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
fetch('/api/v1/sliders')
  .then(response => response.json())
  .then(data => {
    if (data.status) {
      console.log('Sliders:', data.data.sliders);
    } else {
      console.error('Error:', data.message);
    }
  })
  .catch(error => console.error('Fetch error:', error));
```

## Notes

- Only sliders with `status: true` are returned.
- Sliders are sorted by the `order` field in ascending order.
- Title and description fields contain HTML from the RichEditor, allowing inline styling for different colors and formats as needed.
- Custom CSS classes in `custom_styles` can be applied to enhance styling for link and alt_text elements.
