# Backend usage: Localization & Translations

This project supports multilingual behavior in two areas:

1. **UI string translations** via `__()` using the database table `ui_translations`.
2. **Dynamic content translations** (model attributes) via the polymorphic table `translations` and the trait `App\\Traits\\HasTranslations`.

## Locale resolution (how the active language is selected)

Locale is resolved by `App\\Http\\Middleware\\SetLocale` in this priority order:

1. `lang` query parameter (API + web), e.g. `?lang=bd`
2. Route parameter `{locale}` (web routes only, when present)
3. Session locale (admin/web): `session('locale')`
4. `Accept-Language` header (API + web)
5. Default locale from DB (`locales.is_default = 1`), fallback to `config('app.locale')`

Supported locale codes are defined in:

- `config/app.php` => `supported_locales`

Locales are managed in DB:

- table: `locales`
- model: `App\\Models\\Locale`
- Filament: **Settings → Languages** (`LanguageResource`)

## Admin language switcher

Admin language switcher is rendered in the Filament top bar and stores the selection in the session:

- UI: `resources/views/filament/components/locale-switcher.blade.php`
- POST endpoint: `POST /admin/locale` (route name: `admin.locale.update`)
- Controller: `App\\Http\\Controllers\\Admin\\AdminLocaleController`

Notes:

- The switcher is registered via a Filament render hook in `app/Providers/Filament/AdminPanelProvider.php`.
- The switcher UI is styled with Tailwind `dark:*` classes so it matches the currently selected Filament light/dark theme.

For the admin panel to respect the choice, Filament panel middleware includes:

- `StartSession`
- `SetLocale`

Configured in:

- `app/Providers/Filament/AdminPanelProvider.php`

## UI String translations (static text with __())

### Storage

UI string translations are stored in:

- table: `ui_translations`
- model: `App\\Models\\UiTranslation`

Each translated message is identified by:

- `key` (string)
- `locale` (string)
- `value` (text)

Example:

- key: `products.retrieved_successfully`
  - locale `en` => `Products retrieved successfully`
  - locale `bd` => Bangla version

### How __() resolves values

Laravel’s translation loader is extended to merge database translations on top of file-based translations:

- loader: `App\\Translations\\DatabaseTranslationLoader`
- registration: `App\\Providers\\AppServiceProvider` (extends `translation.loader`)

This means:

- If a `ui_translations` row exists for the current locale, `__()` will return it.
- Otherwise Laravel falls back to normal file translations (`lang/` files, if any).

### Editing UI string translations

Filament resource:

- **Settings → Translations** (`TranslationResource`)

Create one row per locale per key.

### Sync missing keys from code

You can scan the codebase for translation keys used by `__()` and `@lang()` and insert missing rows into `ui_translations`:

- Command: `php artisan translations:sync`
- To auto-fill English values for sentence-style keys: `php artisan translations:sync --fill-en`

### Best practice for keys

Prefer **key-based** translations:

- Good: `__('products.retrieved_successfully')`
- Avoid: `__('Products retrieved successfully')` (works as a JSON-style key but becomes hard to manage)

## Dynamic content translations (models)

### Storage

Dynamic content translations are stored in:

- table: `translations`
- model: `App\\Models\\Translation`

Schema:

- `translatable_id`, `translatable_type`
- `locale`
- `attribute`
- `value`

### Enabling on a model

1. Add the trait:

- `use App\\Traits\\HasTranslations;`

2. Define translatable attributes:

- `protected $translatable = ['title', 'content', ...];`

3. Access as normal:

- `$model->title` returns the current locale value (fallback to default locale)

4. Set translations:

- `$model->setTranslation('title', 'bd', '...')`

Notes:

- The trait stores default-locale values into the base column as well (for backward compatibility and querying).

### Content models behavior (Blogs / News / Pages / Case Studies / Products)

The following content types now store **separate content per language** using `translations`:

- `Blog`
- `News`
- `Page`
- `CaseStudy`
- `Product` ✅ **NEWLY ADDED**

Rules:

- `slug` remains a **shared** (non-translated) field.
- These fields are **language-specific** (translated):
  - `title`
  - `excerpt` (for Blog/News/CaseStudy)
  - `content` (for Blog/News/CaseStudy)
  - `short_description` (for Product)
  - `detailed_description` (for Product)
  - `image_path` (for Blog/News/CaseStudy)
  - `meta_title`, `meta_description`, `meta_keywords` (SEO fields for all)

In Filament:

- When you switch the admin language using the language switcher, the form will read/write the translated values for the active locale.
- If a translation does not exist for the selected locale, the system falls back to the default locale.
- **Products now feature multilingual tabs** for title, short description, detailed description, and SEO fields, matching the behavior of blogs and other content types.

### Migrating old JSON translations

A migration exists to move `products.detailed_description` JSON into `translations` rows:

- `2026_03_03_000003_migrate_product_json_translations_to_table.php`

## Common backend workflows

### Seed demo locales

- `php artisan db:seed --class=LocaleSeeder`

### Clear caches if you change translations

- `php artisan optimize:clear`

### Update UI translations after changing labels/messages

After you change admin labels/messages (including menu items) to use `__()`, run:

- `php artisan translations:sync --fill-en`

## Known limitations (current state)

- ✅ **RESOLVED**: All Filament Admin resources now use dynamic translations via `__()`
- ✅ **RESOLVED**: Field labels, sections, and action messages are fully translatable
- ✅ **RESOLVED**: Complete multilingual support across all admin resources

## Recently Implemented Improvements (March 2026)

### Complete Filament Admin Translation System

All Filament Admin resources have been updated to use dynamic translations for **all field labels, sections, and messages**:

#### Updated Resources:
- **BlogResource.php** - Title, Excerpt, Content, Image, SEO fields
- **CaseStudyResource.php** - All form fields and table columns
- **CategoryResource.php** - Name, Description, Parent, SEO fields
- **NewsResource.php** - Complete multilingual support
- **PageResource.php** - All content fields and metadata
- **ProductResource.php** - Comprehensive product management fields
- **TestimonialResource.php** - Client information and testimonial content
- **FeaturedProductResource.php** - Featured products management
- **CertificationAwardResource.php** - Awards and certifications
- **SliderResource.php** - Media management and custom styling

#### Translation Coverage:
- ✅ **Form Field Labels** - All input fields, selects, textareas, file uploads
- ✅ **Section Headers** - General, SEO, Media, Technical Specs, etc.
- ✅ **Table Columns** - All list views and data tables
- ✅ **Status Options** - Draft, Review, Published, Active, Inactive, Archived
- ✅ **Action Labels** - Delete Selected, Change Status, Remove from Featured
- ✅ **Success Messages** - All notification messages and confirmations
- ✅ **Helper Text** - Field descriptions and validation messages

#### Added Translation Keys:
80+ new translation keys added for both English (`en`) and Bangla (`bd`) including:

**Basic Fields:**
- Title, Excerpt, Content, Image, Name, Description, Slug, Status
- Category, Parent, Visible, Order, Media, Type, URL

**Advanced Fields:**
- Meta Title, Meta Description, Meta Keywords, Canonical URL
- Client Information, Testimonial Content, Rating (1-5 stars)
- Technical Specifications, Related Products, Tags
- Slider Details, Custom Styling, Video Embeds

**Actions & Messages:**
- Delete Selected, Change Status, Remove from Featured
- Status Updated, Product removed from featured
- Items deleted successfully, Selected items have been updated

### Usage Example

When administrators navigate to any form:

```php
// Before (hardcoded)
TextInput::make('title')->required()

// After (dynamic translation)
TextInput::make('title')
    ->label(__('Title'))
    ->required()
```

**Result:**
- **EN locale**: Shows "Title"
- **BD locale**: Shows "শিরোনাম"

### Technical Implementation

All resources now use the `__('Translation Key')` pattern:

```php
// Section headers
Section::make(__('General'))
Section::make(__('SEO'))
Section::make(__('Media'))

// Field labels
->label(__('Title'))
->label(__('Description'))
->label(__('Status'))

// Status options
'options([
    'draft' => __('Draft'),
    'published' => __('Published'),
    'archived' => __('Archived'),
])'

// Action labels
->label(__('Delete Selected'))
->label(__('Change Status'))

// Success messages
->title(__('Status Updated'))
->body(__('Selected items have been updated to') . ' ' . $data['status'])
```

### Cache Management

After translation updates, clear caches:

```bash
php artisan cache:clear
php artisan config:clear
```

This ensures all translation changes take effect immediately across the admin panel.
