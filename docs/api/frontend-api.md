# Frontend API (v1)

Base URL:

- Local: `http://localhost:8000/api/v1`

Response shape:

- `status`: boolean
- `message`: string
- `data`: object

## Locale selection

The backend sets the locale using the following priority:

1. `lang` query parameter (e.g. `?lang=bd`)
2. `Accept-Language` header (e.g. `Accept-Language: bd, en;q=0.9`)
3. Session locale (admin / web)
4. Default locale from DB (`locales.is_default = 1`), falling back to `config('app.locale')`

Supported locale codes are configured in `config/app.php` under `supported_locales`.

## Endpoints

### Get active locales

`GET /locales`

Query params:

- `lang` (optional): `en`, `bd`, etc.

Example:

`GET /api/v1/locales`

Response `data.locales` fields:

- `code`
- `name`
- `direction` (`ltr` / `rtl`)
- `is_default`
- `flag_path`

### Products

`GET /products`

Query params:

- `category_id` (optional)
- `per_page` (optional)
- `lang` (optional)

`GET /products/{slug}`

`lang` behavior:

- For translatable attributes (e.g. `title`, `short_description`, `detailed_description`), the API returns the value for the resolved locale.

## Notes for frontend (Next.js / Nuxt)

- Build your language switcher by calling `GET /api/v1/locales`.
- For any content request, pass `?lang=bd` (or set `Accept-Language`).
- If you do not pass a locale, the backend will return the default locale.
