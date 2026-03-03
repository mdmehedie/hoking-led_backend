# Frontend usage: Localization & API

This project exposes a v1 REST API for frontend apps (Next.js / Nuxt).

## Base URL

Local development:

- `http://localhost:8000/api/v1`

## How to choose the language

You can select the response language in two ways:

1. **Query parameter** (recommended for simplicity)

- `?lang=en`
- `?lang=bd`

2. **Header**

- `Accept-Language: bd`

If neither is sent, the backend uses the default locale from DB (`locales.is_default = 1`).

## Recommended flow for a frontend language switcher

1. Call `GET /locales` once on app load.
2. Render a language dropdown using the returned locales (`code`, `name`, `direction`).
3. When user switches language, store `code` in your app state (cookie/localStorage).
4. For every API call, send:

- either `?lang=<code>`
- or `Accept-Language: <code>`

## Endpoints

### GET /locales

Returns active locales.

- URL: `/api/v1/locales`
- Response: `data.locales[]`

Each locale object includes:

- `code`
- `name`
- `direction` (`ltr` / `rtl`)
- `is_default`
- `flag_path` (optional)

### Products

- `GET /api/v1/products?lang=bd`
- `GET /api/v1/products/{slug}?lang=bd`

For translatable attributes (like `title`, `short_description`, `detailed_description`), the API returns the value for the requested locale (with fallback).

## Postman

Import the Postman collection:

- `docs/postman/Frontend-API.postman_collection.json`

Set variable:

- `baseUrl` = `http://localhost:8000`

## Notes

- Text direction can be applied in UI based on locale `direction`.
- If you add new locales in admin, call `GET /locales` again to refresh available languages.
