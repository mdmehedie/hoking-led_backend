# App Settings API

## Overview

This API provides endpoints for retrieving app settings, including organization details, contact information, addresses, and social media links.

## Base URL

http://localhost:8000/api/v1

  ```

## Endpoints

### Get App Settings

- **Method**: GET
- **Endpoint**: /app-settings
- **Description**: Retrieve a list of app settings with pagination.
- **Query Parameters**:
  - `per_page` (optional): Number of app settings per page. Default is 10.
- **Example Request**:
  ```
  GET http://localhost:8000/api/v1/app-settings?per_page=5
  ```
- **Example Response**:
  ```json
  {
    "status": true,
    "message": "App settings retrieved successfully",
    "data": {
      "app_settings": {
        "current_page": 1,
        "data": [
          {
            "id": 1,
            "logo_light": "http://localhost:8000/storage/settings/logo_light.png",
            "logo_dark": "http://localhost:8000/storage/settings/logo_dark.png",
            "favicon": "http://localhost:8000/storage/settings/favicon.ico",
            "primary_color": "#3b82f6",
            "secondary_color": "#10b981",
            "accent_color": "#f59e0b",
            "font_family": "Arial",
            "base_font_size": "16px",
            "organization": {
              "company_name": "Example Company",
              "about": "<p>About the company</p>",
              "contact_emails": [
                {
                  "email": "contact@example.com"
                }
              ],
              "contact_phones": [
                {
                  "phone": "+1234567890"
                }
              ],
              "office_addresses": [
                {
                  "label": "Head Office",
                  "street": "123 Main St",
                  "city": "City",
                  "country": "Country",
                  "map_link": "https://maps.google.com/..."
                }
              ],
              "social_links": [
                {
                  "platform": "facebook",
                  "url": "https://facebook.com/example"
                }
              ]
            },
            "company_name": "Example Company",
            "about": "<p>About the company</p>",
            "contact_emails": [
              {
                "email": "contact@example.com"
              }
            ],
            "contact_phones": [
              {
                "phone": "+1234567890"
              }
            ],
            "office_addresses": [
              {
                "label": "Head Office",
                "street": "123 Main St",
                "city": "City",
                "country": "Country",
                "map_link": "https://maps.google.com/..."
              }
            ],
            "social_links": [
              {
                "platform": "facebook",
                "url": "https://facebook.com/example"
              }
            ],
            "toastr_enabled": true,
            "toastr_position": "top-right",
            "toastr_duration": 5000,
            "toastr_show_method": "slideDown",
            "toastr_hide_method": "slideUp",
            "app_name": "Example App"
          }
        ],
        "per_page": 5,
        "total": 1
      }
    }
  }
  ```

### Get App Setting by Column

- **Method**: GET
- **Endpoint**: /app-settings/{column}
- **Description**: Retrieve a specific field value from app settings by column name.
- **Path Parameters**:
  - `column`: The column name (e.g., logo_light, base_font_size, organization)
- **Example Request**:
  ```
  GET http://localhost:8000/api/v1/app-settings/logo_light
  ```
- **Example Response**:
  ```json
  {
    "status": true,
    "message": "App setting field retrieved successfully",
    "data": {
      "value": "http://localhost:8000/storage/settings/logo_light.png"
    }
  }
  ```

  For organization:
  ```json
  {
    "status": true,
    "message": "App setting field retrieved successfully",
    "data": {
      "value": {
        "company_name": "Example Company",
        "about": "<p>About the company</p>",
        "contact_emails": [
          {
            "email": "contact@example.com"
          }
        ],
        "contact_phones": [
          {
            "phone": "+1234567890"
          }
        ],
        "office_addresses": [
          {
            "label": "Head Office",
            "street": "123 Main St",
            "city": "City",
            "country": "Country",
            "map_link": "https://maps.google.com/..."
          }
        ],
        "social_links": [
          {
            "platform": "facebook",
            "url": "https://facebook.com/example"
          }
        ]
      }
    }
  }
  ```

## Notes

- Images (logos and favicon) are returned with full URLs.
- Organization details are provided both as a nested array and as separate top-level fields for easier access.
- Social media platforms include: facebook, twitter, linkedin, instagram, youtube, tiktok, github, website.
