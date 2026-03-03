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

- Only `Product` has been switched to the new dynamic translation trait so far.
- Some older admin strings may still be hardcoded and need to be wrapped in `__()` to become translatable.
