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
        "media_type": "image",
        "image_path": "http://localhost:8000/storage/sliders/slider-image.jpg",
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
      },
      {
        "id": 2,
        "title": "<p>Video Slider</p>",
        "description": "<p>Embedded video slider</p>",
        "media_type": "video_url",
        "video_url": "https://youtube.com/watch?v=example",
        "link": "https://example.com",
        "alt_text": "Video Alt Text",
        "order": 2,
        "status": true,
        "custom_styles": {
          "link_class": "btn btn-secondary",
          "alt_text_class": "text-light"
        },
        "created_at": "2023-01-02T00:00:00.000000Z",
        "updated_at": "2023-01-02T00:00:00.000000Z"
      }
    ]
  }
}
```

##### Fields Description

- `id`: Unique identifier for the slider.
- `title`: HTML content for the slider title (can include inline styles).
- `description`: HTML content for the slider description (can include inline styles).
- `media_type`: Type of media ('image', 'gif', 'video_url', 'video_file').
- `image_path`: Full URL to the slider image/GIF file (only included when media_type is 'image' or 'gif').
- `video_url`: URL for embedded video (only included when media_type is 'video_url').
- `video_file`: Full URL to the uploaded video file (only included when media_type is 'video_file').
- `link`: URL for the slider link.
- `alt_text`: Alt text for the media.
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

## Notes

- Only sliders with `status: true` are returned.
- Sliders are sorted by the `order` field in ascending order.
- Title and description fields contain HTML from the RichEditor, allowing inline styling for different colors and formats as needed.
- Custom CSS classes in `custom_styles` can be applied to enhance styling for link and alt_text elements.

## Media Type Handling

The `media_type` field determines how to display the slider media. Handle each type as follows:

### Image/GIF (`media_type: 'image'` or `'gif'`)
- Display the image using `image_path` as the `src` attribute.
- Use `alt_text` for the `alt` attribute.
- For GIFs, the image will animate automatically.

### Video URL (`media_type: 'video_url'`)
- Embed the video using `video_url`.
- For YouTube/Vimeo, use iframe embeds or video players.
- Example: `<iframe src="https://www.youtube.com/embed/VIDEO_ID" ...></iframe>`

### Uploaded Video (`media_type: 'video_file'`)
- Use `video_file` as the `src` for an HTML5 `<video>` element.
- Include controls and appropriate attributes.
- Example: `<video src="{video_file}" controls></video>`

## Updated Usage Example (React)

```javascript
fetch('/api/v1/sliders')
  .then(response => response.json())
  .then(data => {
    if (data.status) {
      data.data.sliders.forEach(slider => {
        console.log('Title:', slider.title);
        console.log('Description:', slider.description);
        
        switch(slider.media_type) {
          case 'image':
          case 'gif':
            // Render image/GIF
            console.log('Image URL:', slider.image_path);
            break;
          case 'video_url':
            // Render embedded video
            console.log('Video URL:', slider.video_url);
            break;
          case 'video_file':
            // Render uploaded video
            console.log('Video File:', slider.video_file);
            break;
        }
        
        console.log('Link:', slider.link);
        console.log('Alt Text:', slider.alt_text);
        console.log('Custom Styles:', slider.custom_styles);
      });
    } else {
      console.error('Error:', data.message);
    }
  })
  .catch(error => console.error('Fetch error:', error));
```
