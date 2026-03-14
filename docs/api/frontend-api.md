# Frontend API (v1) - Multilingual Support

Base URL:

- Local: `http://localhost:8000/api/v1`

## Response Structure

All API responses now include localization support:

```json
{
    "status": true,
    "message": "Products retrieved successfully",
    "data": { ... },
    "locale": "en",
    "translations": {
        "loading": "Loading...",
        "error": "Error",
        "success": "Success",
        "no_data": "No data available",
        "not_found": "Not found",
        "server_error": "Server error",
        "try_again": "Please try again",
        "close": "Close",
        "cancel": "Cancel",
        "confirm": "Confirm",
        "yes": "Yes",
        "no": "No",
        "ok": "OK",
        "save": "Save",
        "edit": "Edit",
        "delete": "Delete",
        "view": "View",
        "search": "Search",
        "filter": "Filter",
        "sort": "Sort",
        "page": "Page",
        "of": "of",
        "items_per_page": "Items per page",
        "showing": "Showing",
        "to": "to",
        "of_total": "of total",
        "previous": "Previous",
        "next": "Next",
        "first": "First",
        "last": "Last"
    }
}
```

## Locale Selection

The backend sets the locale using the following priority:

1. `lang` query parameter (e.g. `?lang=bd`)
2. `Accept-Language` header (e.g. `Accept-Language: bd, en;q=0.9`)
3. Session locale (admin / web)
4. Default locale from DB (`locales.is_default = 1`), falling back to `config('app.locale')`

Supported locale codes are configured in `config/app.php` under `supported_locales`.

## Request Headers for Localization

To get localized responses, include one of the following:

### Method 1: Query Parameter (Recommended)
```
GET /api/v1/products?lang=bd
```

### Method 2: Accept-Language Header
```
GET /api/v1/products
Accept-Language: bd, en;q=0.9
```

### Method 3: Session Locale (for authenticated users)
```
POST /api/v1/forms/1/submit
Cookie: laravel_session=...
```

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

**Localized Response Example (Bangla):**

```json
{
    "status": true,
    "message": "পণ্যসমূহ সফলতাভারভে",
    "data": {
        "products": [
            {
                "id": 1,
                "title": "পণ্যটির শিরোনাম",
                "slug": "product-1",
                "short_description": "এটি পণ্যটির সংক্ষিপ্ত বর্ণনা",
                "detailed_description": "এই পণ্যটির বিস্তারিত বর্ণনা...",
                "category_id": 1,
                "status": "published",
                "published_at": "2024-01-01T10:00:00Z",
                "author_id": 1,
                "is_featured": true,
                "image_path": "http://example.com/storage/products/main/product-1.jpg",
                "created_at": "2024-01-01T10:00:00Z",
                "updated_at": "2024-01-01T10:00:00Z"
            }
        ]
    },
    "locale": "bd",
    "translations": {
        "loading": "লোড হচ্ছে...",
        "error": "ত্রুটি",
        "success": "সফলতা",
        "no_data": "কোনো তথ্য নেই",
        "not_found": "পাওয়া যায়",
        "server_error": "সার্ভার ত্রুটি",
        "try_again": "অনুগ্রহণ করুন",
        "close": "বন্ধ করুন",
        "cancel": "াতিল করুন",
        "confirm": "নিশ্চিত করুন",
        "yes": "হ্যাঁ",
        "no": "না",
        "ok": "ঠিক আছে",
        "save": "সংরক্ষণ করুন",
        "edit": "সম্পাদনা",
        "delete": "মুছে ফেলুন",
        "view": "দেখুন",
        "search": "অনুসন্ধান",
        "filter": "ফিল্টার",
        "sort": "সাজান",
        "page": "পৃষ্ঠা",
        "of": "এর",
        "items_per_page": "প্রতি পৃষ্ঠায়",
        "showing": "দেখাচ্ছে",
        "to": "থেকে",
        "of_total": "োট",
        "previous": "আগের",
        "next": "পরবর্তী",
        "first": "প্রথম",
        "last": "শেষ"
    }
}
```

### Blogs

`GET /blogs`

`GET /blogs/{slug}`

### Case Studies

`GET /case-studies`

`GET /case-studies/{slug}`

### News

`GET /news`

`GET /news/{slug}`

### Pages

`GET /pages`

`GET /pages/{slug}`

### Categories

`GET /categories`

### Featured Products

`GET /featured-products`

### Certifications & Awards

`GET /certifications`

`GET /certifications/{slug}`

### Testimonials

`GET /testimonials`

### Sliders

`GET /sliders`

### App Settings

`GET /app-settings`

`GET /app-settings/{column}`

### Forms

`GET /forms`

`POST /forms/{form}/submit`

## Content Localization

For translatable content (title, descriptions, etc.), the API automatically returns the value for the resolved locale:

- **English request**: `?lang=en` → Returns English content
- **Bangla request**: `?lang=bd` → Returns Bangla content
- **No locale specified**: Returns default locale content

## Error Handling

All error responses are also localized:

```json
{
    "status": false,
    "message": "Product not found",
    "data": {},
    "locale": "en",
    "translations": {
        "error": "Error",
        "not_found": "Not found",
        "try_again": "Please try again"
    }
}
```

## Pagination

Paginated responses include localized pagination text:

```json
{
    "data": {
        "products": [...],
        "links": {...},
        "meta": {
            "current_page": 1,
            "from": 1,
            "last_page": 5,
            "per_page": 10,
            "to": 10,
            "total": 50
        }
    },
    "translations": {
        "showing": "Showing",
        "to": "to",
        "of_total": "of total",
        "previous": "Previous",
        "next": "Next",
        "first": "First",
        "last": "Last"
    }
}
```

## Implementation Notes for Frontend

### 1. Language Switcher Implementation

Build your language switcher by calling `GET /api/v1/locales`:

```javascript
// Example React component
const [locales, setLocales] = useState([]);
const [currentLocale, setCurrentLocale] = useState('en');

useEffect(() => {
    fetch('/api/v1/locales')
        .then(res => res.json())
        .then(data => {
            setLocales(data.data.locales);
            // Set current locale from browser/localStorage or default
            setCurrentLocale(localStorage.getItem('locale') || data.data.locales.find(l => l.is_default)?.code || 'en');
        });
}, []);

const changeLanguage = (locale) => {
    setCurrentLocale(locale);
    localStorage.setItem('locale', locale);
    // Reload page or update API calls to use new locale
};
```

### 2. API Request with Localization

Always pass the current locale to API calls:

```javascript
const api = axios.create({
    baseURL: 'http://localhost:8000/api/v1',
    headers: {
        'Accept-Language': `${currentLocale}, en;q=0.9`,
        'Content-Type': 'application/json',
    },
});

// Or use query parameter
const response = await api.get(`/products?lang=${currentLocale}`);
```

### 3. Using Translations in UI

The `translations` object in responses provides common UI text in the current locale:

```javascript
// Success message
const successMessage = response.data.message; // Already localized
const uiText = response.data.translations; // Common UI text

// Example usage
toast.success(successMessage);
alert(uiText.close);
```

### 4. Form Submissions

Form submissions automatically use the current locale for success messages:

```javascript
const submitForm = async (formData) => {
    const response = await api.post(`/forms/${formId}/submit`, formData, {
        headers: {
            'Accept-Language': `${currentLocale}, en;q=0.9`,
        },
    });
    
    // Response will include localized success message
    alert(response.data.message);
};
```

## Testing Localization

### Test with Different Locales

```bash
# English (default)
curl "http://localhost:8000/api/v1/products"

# Bangla
curl "http://localhost:8000/api/v1/products?lang=bd"

# With Accept-Language header
curl -H "Accept-Language: bd, en;q=0.9" "http://localhost:8000/api/v1/products"
```

### Expected Response Differences

**English Response:**
- `message`: "Products retrieved successfully"
- `locale`: "en"
- `translations.loading`: "Loading..."

**Bangla Response:**
- `message`: "পণ্যসমূহ সফলতাভারভে"
- `locale`: "bd"
- `translations.loading`: "লোড হচ্ছে..."

## Supported Locales

Current supported locales:
- **en** - English (default)
- **bd** - Bangla

Add new locales by:
1. Adding entries to the `locales` table
2. Adding translation keys to `ui_translations` table
3. Updating `config/app.php` `supported_locales` array

## Best Practices

1. **Always include locale in API requests** for consistent user experience
2. **Use the translations object** for common UI text to avoid hardcoding
3. **Handle fallback gracefully** when translations are missing
4. **Cache locale preferences** in browser storage for better UX
5. **Test with all supported locales** to ensure consistency

## Rate Limiting & Caching

- API responses are cached based on locale to improve performance
- Rate limiting is applied per IP address
- Cache invalidation occurs when translations are updated

## Error Codes

Standard HTTP status codes are used with localized messages:
- `200` - Success with localized success message
- `404` - Not found with localized error message  
- `500` - Server error with localized error message
- `422` - Validation error with localized error message
