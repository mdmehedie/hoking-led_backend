# Backend Usage - International SEO

This guide explains how to use and manage the international SEO features in the Laravel backend application.

## Table of Contents

1. [Admin Panel Management](#admin-panel-management)
2. [Database Management](#database-management)
3. [API Development](#api-development)
4. [Sitemap Generation](#sitemap-generation)
5. [Configuration](#configuration)
6. [Custom Development](#custom-development)
7. [Troubleshooting](#troubleshooting)

## Admin Panel Management

### 1. Managing Regions

#### Access Region Management
1. Login to your Laravel admin panel
2. Navigate to "Regions" in the sidebar
3. **Full CRUD operations**: Create, Edit, Delete, and View regions
4. **Bulk operations**: Select multiple regions for batch actions
5. **Interactive features**: Drag-and-drop reordering, filtering, sorting

#### Region Management Options

**Option 1: Filament Admin Panel (Recommended for all operations)**
- Navigate to "Regions" in the admin sidebar
- **Full CRUD operations**: Create, Read, Update, Delete
- **Bulk operations**: Toggle active status, change sort order
- **Interactive features**: Drag-and-drop reordering, filtering, sorting
- **Form validation**: Built-in validation for all fields

#### Filament Admin Features

**Create New Region**
1. Click "New Region" button in the top right
2. Fill in the form fields:
   - **Region Code**: Unique identifier (e.g., `us`, `uk`, `eu`)
   - **Region Name**: Display name (e.g., `United States`)
   - **Currency**: ISO 4217 code (e.g., `USD`, `GBP`, `EUR`)
   - **Timezone**: PHP timezone (e.g., `America/New_York`)
   - **Language**: Language code (e.g., `en`, `en-US`)
   - **Active**: Enable/disable region for users
   - **Default Region**: Set as default for new users
   - **Sort Order**: Display order in selectors
3. Click "Save" to create the region

**Edit Existing Region**
1. Click the "Edit" button (pencil icon) in the table row
2. Modify any fields in the form
3. Click "Save" to update the region

**Delete Region**
1. Click the "Delete" button (trash icon) in the table row
2. Confirm the deletion in the modal
3. Click "Delete" to permanently remove the region

**Bulk Operations**
1. Select multiple regions using checkboxes
2. Choose from bulk actions:
   - **Delete Selected**: Remove multiple regions at once
   - **Toggle Active Status**: Enable/disable multiple regions
   - **Change Sort Order**: Set the same sort order for multiple regions

**Interactive Features**
- **Drag-and-Drop**: Reorder regions by dragging rows
- **Filtering**: Filter by active/default status
- **Sorting**: Click column headers to sort
- **Search**: Search by code, name, currency, or language
- **Toggle Columns**: Show/hide columns like created/updated dates

**Option 2: Command Line Management (Alternative/Advanced)**
```bash
# List all regions
php artisan regions:list

# Create a new region (interactive wizard)
php artisan regions:list create

# Delete a region (interactive selection)
php artisan regions:list delete

# Edit regions via tinker
php artisan tinker
```

**Option 3: Database Operations (Advanced)**
```php
// Via tinker
php artisan tinker
>>> Region::create([
...     'code' => 'jp',
...     'name' => 'Japan',
...     'currency' => 'JPY',
...     'timezone' => 'Asia/Tokyo',
...     'language' => 'ja',
...     'is_active' => true,
...     'is_default' => false,
...     'sort_order' => 10
... ]);

// Use existing seeder
php artisan db:seed --class=RegionSeeder
```

#### Adding a New Region (Step-by-Step)

**Method 1: Interactive Command Line (Recommended)**
```bash
php artisan regions:list create
```
The command will guide you through:
1. **Region code** (e.g., `jp`, `br`, `in`)
2. **Region name** (e.g., `Japan`, `Brazil`, `India`)
3. **Currency code** (e.g., `JPY`, `BRL`, `INR`)
4. **Timezone** (e.g., `Asia/Tokyo`, `America/Sao_Paulo`, `Asia/Kolkata`)
5. **Language code** (e.g., `ja`, `pt-BR`, `en-IN`)
6. **Active status** (Enable region for users?)
7. **Default region** (Set as default for new users?)
8. **Sort order** (Display order in selectors)

**Example Session:**
```bash
$ php artisan regions:list create

Create a new region:
> Region code (e.g., us, uk, eu): jp
> Region name (e.g., United States): Japan
> Currency code (e.g., USD, GBP, EUR): JPY
> Timezone (e.g., America/New_York): Asia/Tokyo
> Language code (e.g., en-US, en-GB): ja
> Make this region active? (yes/no) [yes]: yes
> Make this the default region? (yes/no) [no]: no
> Sort order [0]: 10

✓ Region 'jp' created successfully!
```

**Method 2: Direct Database Creation**
```php
// Via tinker
php artisan tinker
>>> Region::create([
...     'code' => 'jp',
...     'name' => 'Japan',
...     'currency' => 'JPY',
...     'timezone' => 'Asia/Tokyo',
...     'language' => 'ja',
...     'is_active' => true,
...     'is_default' => false,
...     'sort_order' => 10
... ]);
```

#### Editing Existing Regions

**Method 1: Update via Tinker (Recommended)**
```bash
php artisan tinker
```

**Common Edit Operations:**
```php
// Update basic information
>>> Region::where('code', 'jp')->update([
...     'name' => 'Japan (Updated)',
...     'currency' => 'JPY',
...     'timezone' => 'Asia/Tokyo'
... ]);

// Activate/deactivate a region
>>> Region::where('code', 'jp')->update(['is_active' => false]);

// Set as default region
>>> Region::where('code', 'jp')->update(['is_default' => true]);

// Update sort order
>>> Region::where('code', 'jp')->update(['sort_order' => 5]);

// Multiple field updates
>>> Region::where('code', 'jp')->update([
...     'name' => 'Japan',
...     'currency' => 'JPY',
...     'language' => 'ja',
...     'is_active' => true,
...     'sort_order' => 3
... ]);
```

**Method 2: Find and Edit Pattern**
```php
// Find the region first
>>> $region = Region::where('code', 'jp')->first();

// Edit individual properties
>>> $region->name = 'Japan (Updated)';
>>> $region->currency = 'JPY';
>>> $region->save();

// Or update multiple properties
>>> $region->update([
...     'name' => 'Japan',
...     'timezone' => 'Asia/Tokyo',
...     'is_active' => true
... ]);
```

**Method 3: Batch Updates**
```php
// Activate multiple regions
>>> Region::whereIn('code', ['jp', 'kr', 'cn'])->update(['is_active' => true]);

// Update sort order for multiple regions
>>> Region::whereIn('code', ['jp', 'kr', 'cn'])->update([
...     'sort_order' => \DB::raw('sort_order + 10')
... ]);

// Change currency for all Asian regions
>>> Region::whereIn('code', ['jp', 'kr', 'cn', 'sg'])->update(['currency' => 'USD']);
```

#### Advanced Region Management

**Changing Default Region**
```php
// Remove default from current default
>>> Region::where('is_default', true)->update(['is_default' => false]);

// Set new default
>>> Region::where('code', 'jp')->update(['is_default' => true]);
```

**Reordering Regions**
```php
// Reset all sort orders
>>> Region::query()->update(['sort_order' => 0]);

// Set specific order
>>> $regions = ['us' => 1, 'uk' => 2, 'eu' => 3, 'jp' => 4];
>>> foreach ($regions as $code => $order) {
...     Region::where('code', $code)->update(['sort_order' => $order]);
... }
```

**Bulk Region Creation**
```php
// Create multiple regions at once
>>> $newRegions = [
...     ['code' => 'sg', 'name' => 'Singapore', 'currency' => 'SGD', 'timezone' => 'Asia/Singapore'],
...     ['code' => 'hk', 'name' => 'Hong Kong', 'currency' => 'HKD', 'timezone' => 'Asia/Hong_Kong'],
...     ['code' => 'tw', 'name' => 'Taiwan', 'currency' => 'TWD', 'timezone' => 'Asia/Taipei']
... ];
>>> foreach ($newRegions as $region) {
...     Region::create(array_merge($region, [
...         'is_active' => true,
...         'is_default' => false,
...         'sort_order' => 20
...     ]));
... }
```

#### Creating a New Region (Command Line)
```bash
php artisan regions:list create
```
The command will prompt you for:
- Region code (e.g., us, uk, eu)
- Region name (e.g., United States)
- Currency code (e.g., USD, GBP, EUR)
- Timezone (e.g., America/New_York)
- Language code (e.g., en-US, en-GB)
- Active status (yes/no)
- Default region (yes/no)
- Sort order (numeric)

#### Region Fields Explanation
| Field | Description | Example |
|-------|-------------|---------|
| Code | Unique region identifier (2-10 chars) | `us`, `uk`, `eu` |
| Name | Display name for the region | `United States` |
| Currency | ISO 4217 currency code | `USD`, `GBP`, `EUR` |
| Timezone | PHP timezone identifier | `America/New_York` |
| Language | ISO language code | `en-US`, `en-GB` |
| Active | Enable/disable region for users | `true`/`false` |
| Default Region | Set as default for new users | `true`/`false` |
| Sort Order | Display order in selectors | `1`, `2`, `3` |

### 2. Managing Default Region

#### Via App Settings
1. Navigate to "Settings" → "App Settings"
2. Find "International SEO" section
3. Select "Default Region" from dropdown
4. Click "Save"

#### Programmatically
```php
use App\Models\AppSetting;

// Get current default region
$defaultRegion = AppSetting::first()->default_region;

// Update default region
AppSetting::first()->update(['default_region' => 'uk']);
```

#### Via Command Line
```bash
# View current regions to identify default
php artisan regions:list

# Create new default region (will ask if you want it as default)
php artisan regions:list create

# Or update via tinker
php artisan tinker
>>> Region::where('code', 'us')->update(['is_default' => true]);
```

## 🚀 Quick Reference - Common Region Operations

### Add New Region
```bash
# Interactive wizard (recommended)
php artisan regions:list create

# Direct creation
php artisan tinker
>>> Region::create(['code' => 'br', 'name' => 'Brazil', 'currency' => 'BRL', 'timezone' => 'America/Sao_Paulo', 'language' => 'pt-BR', 'is_active' => true, 'sort_order' => 10]);
```

### Edit Region
```bash
php artisan tinker
>>> Region::where('code', 'br')->update(['name' => 'Brazil (Updated)']);
>>> Region::where('code', 'br')->update(['is_active' => false]);
>>> Region::where('code', 'br')->update(['sort_order' => 5]);
```

### Delete Region
```bash
# Interactive selection
php artisan regions:list delete

# Direct deletion
php artisan tinker
>>> Region::where('code', 'br')->delete();
```

### Change Default Region
```bash
php artisan tinker
>>> Region::where('is_default', true)->update(['is_default' => false]);
>>> Region::where('code', 'br')->update(['is_default' => true]);
```

### List All Regions
```bash
php artisan regions:list
```

### 3. Content Region Association

#### Via Filament (View Only)
1. Navigate to content type (Products, Blogs, etc.)
2. Edit existing content
3. Look for "Regions" field (multi-select) - **View current associations**
4. For modifications, use programmatic approach below

#### Via Command Line
```bash
# Associate product with regions via tinker
php artisan tinker
>>> $product = Product::find(1);
>>> $usRegion = Region::where('code', 'us')->first();
>>> $product->regions()->attach($usRegion->id);

# Associate with multiple regions
>>> $regions = Region::whereIn('code', ['us', 'uk', 'eu'])->pluck('id');
>>> $product->regions()->sync($regions);

# Remove all region associations
>>> $product->regions()->detach();
```

#### Programmatically
```php
use App\Models\Product;
use App\Models\Region;

// Get region
$usRegion = Region::where('code', 'us')->first();

// Associate product with region
$product = Product::find(1);
$product->regions()->attach($usRegion->id);

// Associate with multiple regions
$regions = Region::whereIn('code', ['us', 'uk', 'eu'])->pluck('id');
$product->regions()->sync($regions);

// Remove all region associations
$product->regions()->detach();
```

## Database Management

### 1. Migration Commands

#### Run All International SEO Migrations
```bash
php artisan migrate
```

#### Run Specific Migration
```bash
php artisan migrate --path=database/migrations/2026_03_08_193939_create_regions_table.php
```

#### Rollback Migrations
```bash
# Rollback last migration
php artisan migrate:rollback

# Rollback specific steps
php artisan migrate:rollback --step=5
```

### 2. Seeding Data

#### Seed Default Regions
```bash
php artisan db:seed --class=RegionSeeder
```

#### Custom Seeder
```php
// database/seeders/CustomRegionSeeder.php
<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class CustomRegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            [
                'code' => 'jp',
                'name' => 'Japan',
                'currency' => 'JPY',
                'timezone' => 'Asia/Tokyo',
                'language' => 'ja',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 10,
            ],
            [
                'code' => 'br',
                'name' => 'Brazil',
                'currency' => 'BRL',
                'timezone' => 'America/Sao_Paulo',
                'language' => 'pt-BR',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 11,
            ],
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}
```

### 3. Database Queries

#### Get Active Regions
```php
use App\Models\Region;

$activeRegions = Region::where('is_active', true)
    ->orderBy('sort_order')
    ->get();

// Get as array
$regionOptions = Region::where('is_active', true)
    ->orderBy('sort_order')
    ->pluck('name', 'code')
    ->toArray();
```

#### Get Default Region
```php
$defaultRegion = Region::where('is_default', true)->first();
// Or use helper
$defaultRegionCode = Region::defaultCode();
```

#### Content by Region
```php
// Get products available in US region
$usProducts = Product::whereHas('regions', function($query) {
    $query->where('code', 'us');
})->get();

// Get regions for a product
$productRegions = Product::find(1)->regions;
```

## API Development

### 1. Extending API Resources

#### Add Alternates to Custom Resource
```php
// app/Http/Resources/CustomResource.php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            // Add alternates for SEO
            'alternates' => $this->getAlternates(),
        ];
    }
}
```

#### Custom Alternates Method
```php
// app/Models/CustomModel.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CustomModel extends Model
{
    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'custom_model_regions');
    }

    public function getUrl(): string
    {
        return url('/custom/' . $this->slug);
    }

    public function getAlternates(): array
    {
        $alternates = [];
        $supportedLocales = \App\Models\Locale::activeCodes();
        
        foreach ($supportedLocales as $locale) {
            $url = $this->getUrl();
            $alternates[] = [
                'locale' => $locale,
                'url' => $locale === \App\Models\Locale::defaultCode() 
                    ? $url 
                    : str_replace(url('/'), url('/' . $locale), $url)
            ];
        }
        
        return $alternates;
    }
}
```

### 2. API Controllers

#### Region-Aware Controller
```php
// app/Http/Controllers/Api/V1/RegionAwareController.php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Product;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionAwareController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::where('status', 'published');
        
        // Filter by region if specified
        if ($request->has('region') && $request->region) {
            $query->whereHas('regions', function($q) use ($request) {
                $q->where('code', $request->region);
            });
        }
        
        $products = $query->paginate($request->get('per_page', 10));
        
        return $this->okResponse([
            'products' => ProductResource::collection($products)
        ]);
    }
}
```

#### Region Validation
```php
// app/Http/Requests/RegionRequest.php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'region' => [
                'nullable',
                'string',
                Rule::exists('regions', 'code')->where('is_active', true)
            ],
        ];
    }
}
```

### 3. Middleware

#### Region Detection Middleware
```php
// app/Http/Middleware/SetRegion.php
<?php

namespace App\Http\Middleware;

use App\Models\Region;
use Closure;
use Illuminate\Http\Request;

class SetRegion
{
    public function handle(Request $request, Closure $next)
    {
        $region = $this->detectRegion($request);
        
        if ($region) {
            // Store region in session or request
            $request->merge(['detected_region' => $region]);
        }
        
        return $next($request);
    }
    
    private function detectRegion(Request $request): ?string
    {
        // 1. Check URL parameter
        if ($request->has('region')) {
            return $request->get('region');
        }
        
        // 2. Check session
        if ($request->session()->has('user_region')) {
            return $request->session()->get('user_region');
        }
        
        // 3. Check subdomain (us.example.com, uk.example.com)
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0] ?? null;
        
        if ($subdomain && Region::where('code', $subdomain)->exists()) {
            return $subdomain;
        }
        
        // 4. Check default region
        return Region::defaultCode();
    }
}
```

## Sitemap Generation

### 1. Basic Sitemap Generation

#### Generate All Sitemaps
```bash
php artisan app:generate-sitemap
```

This creates:
- `public/sitemap.xml` - Main sitemap with hreflang
- `public/sitemap-{region}.xml` - Region-specific sitemaps
- `public/sitemap-index.xml` - Index of all sitemaps

#### Generate Specific Region Sitemap
```bash
php artisan app:generate-sitemap --region=us
```

### 2. Automated Sitemap Generation

#### Schedule in Console Kernel
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule): void
{
    // Generate sitemaps daily at 2 AM
    $schedule->command('app:generate-sitemap')
        ->dailyAt('02:00')
        ->withoutOverlapping();
        
    // Generate region sitemaps every 6 hours
    $schedule->command('app:generate-sitemap --region=us')
        ->cron('0 */6 * * *')
        ->withoutOverlapping();
}
```

#### Custom Sitemap Command
```php
// app/Console/Commands/GenerateCustomSitemap.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;

class GenerateCustomSitemap extends Command
{
    protected $signature = 'app:generate-custom-sitemap {type}';
    
    protected $description = 'Generate custom sitemap for specific content type';
    
    public function handle()
    {
        $type = $this->argument('type');
        $sitemap = Sitemap::create();
        
        switch ($type) {
            case 'products':
                $this->addProducts($sitemap);
                break;
            case 'blogs':
                $this->addBlogs($sitemap);
                break;
            default:
                $this->error('Invalid type. Use: products, blogs');
                return 1;
        }
        
        $filename = "sitemap-{$type}.xml";
        $sitemap->writeToFile(public_path($filename));
        
        $this->info("Generated {$filename}");
        return 0;
    }
    
    private function addProducts(Sitemap $sitemap): void
    {
        Product::where('status', 'published')
            ->chunk(100, function ($products) use ($sitemap) {
                foreach ($products as $product) {
                    $url = Url::create($product->getUrl())
                        ->setLastModificationDate($product->updated_at);
                    
                    foreach ($product->getAlternates() as $alternate) {
                        $url->addAlternate($alternate['url'], $alternate['locale']);
                    }
                    
                    $sitemap->add($url);
                }
            });
    }
}
```

### 3. Sitemap Customization

#### Custom URL Priority and Frequency
```php
// In your sitemap generator
$url = Url::create($product->getUrl())
    ->setLastModificationDate($product->updated_at)
    ->setChangeFrequency('weekly')
    ->setPriority(0.8);
```

#### Exclude Content from Sitemap
```php
// In your model
public function shouldBeIncludedInSitemap(): bool
{
    return $this->status === 'published' && !$this->is_archived;
}

// In sitemap generator
Product::where('status', 'published')
    ->where('is_archived', false)
    ->get();
```

## Configuration

### 1. Environment Variables

```env
# International SEO Settings
DEFAULT_REGION=us
SUPPORTED_LOCALES=en,es,fr,de,it,pt

# Sitemap Settings
SITEMAP_CACHE_TTL=3600
ENABLE_REGIONAL_SITEMAPS=true
```

### 2. Configuration Files

#### Create SEO Config
```php
// config/seo.php
<?php

return [
    'default_region' => env('DEFAULT_REGION', 'us'),
    'supported_locales' => explode(',', env('SUPPORTED_LOCALES', 'en')),
    
    'sitemap' => [
        'cache_ttl' => env('SITEMAP_CACHE_TTL', 3600),
        'enable_regional' => env('ENABLE_REGIONAL_SITEMAPS', true),
        'include_unpublished' => false,
    ],
    
    'hreflang' => [
        'add_x_default' => true,
        'self_referencing' => true,
    ],
];
```

### 3. Service Provider

#### SEO Service Provider
```php
// app/Providers/SEOServiceProvider.php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SEOService;

class SEOServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SEOService::class, function ($app) {
            return new SEOService();
        });
    }
    
    public function boot(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/seo.php', 'seo'
        );
    }
}
```

## Custom Development

### 1. SEO Service Class

```php
// app/Services/SEOService.php
<?php

namespace App\Services;

use App\Models\Locale;
use App\Models\Region;

class SEOService
{
    public function getAlternatesForModel($model): array
    {
        if (!method_exists($model, 'getAlternates')) {
            return [];
        }
        
        return $model->getAlternates();
    }
    
    public function getRegionUrl($url, ?string $region = null): string
    {
        if (!$region || $region === Region::defaultCode()) {
            return $url;
        }
        
        return str_replace(url('/'), url('/' . $region), $url);
    }
    
    public function detectUserRegion($request): ?string
    {
        // Implementation for region detection
        // IP geolocation, browser language, etc.
        return null;
    }
    
    public function isRegionActive(string $regionCode): bool
    {
        return Region::where('code', $regionCode)
            ->where('is_active', true)
            ->exists();
    }
}
```

### 2. Custom Traits

#### SEO Trait for Models
```php
// app/Traits/HasSEO.php
<?php

namespace App\Traits;

use App\Models\Locale;

trait HasSEO
{
    public function getAlternates(): array
    {
        $alternates = [];
        $supportedLocales = Locale::activeCodes();
        $defaultLocale = Locale::defaultCode();
        
        foreach ($supportedLocales as $locale) {
            $url = $this->getUrl();
            $alternates[] = [
                'locale' => $locale,
                'url' => $locale === $defaultLocale 
                    ? $url 
                    : str_replace(url('/'), url('/' . $locale), $url)
            ];
        }
        
        return $alternates;
    }
    
    public function getHreflangTags(): string
    {
        $tags = '';
        foreach ($this->getAlternates() as $alternate) {
            $tags .= sprintf(
                '<link rel="alternate" hreflang="%s" href="%s">' . "\n",
                $alternate['locale'],
                $alternate['url']
            );
        }
        return $tags;
    }
}
```

#### Region Trait for Models
```php
// app/Traits/HasRegions.php
<?php

namespace App\Traits;

use App\Models\Region;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRegions
{
    public function regions(): BelongsToMany
    {
        $tableName = $this->getTable();
        return $this->belongsToMany(Region::class, "{$tableName}_regions");
    }
    
    public function isAvailableInRegion(string $regionCode): bool
    {
        return $this->regions()->where('code', $regionCode)->exists();
    }
    
    public function getAvailableRegions(): array
    {
        return $this->regions()->where('is_active', true)->pluck('code')->toArray();
    }
}
```

### 3. Custom Validation Rules

#### Region Exists Rule
```php
// app/Rules/ValidRegion.php
<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Region;

class ValidRegion implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Region::where('code', $value)->where('is_active', true)->exists()) {
            $fail('The :attribute must be a valid active region.');
        }
    }
}
```

#### Usage in Form Request
```php
// app/Http/Requests/ProductRequest.php
use App\Rules\ValidRegion;

public function rules(): array
{
    return [
        'title' => 'required|string|max:255',
        'regions' => 'array',
        'regions.*' => ['string', new ValidRegion()],
    ];
}
```

## Troubleshooting

### 1. Common Issues

#### Filament Admin Issues
**Problem**: Regions page shows error or missing actions
**Solution**: 
- Use command line for CRUD operations: `php artisan regions:list`
- View-only access works in Filament admin panel
- Actions disabled due to Filament version compatibility

#### Regions Not Showing in API
```php
// Check if regions are active
Region::where('is_active', true)->get();

// Check if content has regions associated
Product::with('regions')->find(1);

// Debug alternates generation
$product = Product::find(1);
dd($product->getAlternates());
```

#### Sitemap Generation Issues
```bash
# Check file permissions
ls -la public/sitemap*.xml

# Regenerate sitemap
php artisan app:generate-sitemap --force

# Clear cache
php artisan cache:clear
php artisan config:clear
```

#### Database Relationship Issues
```php
// Check pivot table exists
Schema::hasTable('product_regions');

// Check foreign keys
Product::with('regions')->get();
```

### 2. Debug Commands

#### Region Debug
```bash
php artisan tinker
>>> Region::activeCodes();
>>> Region::defaultCode();
>>> Product::first()->regions()->pluck('code');
>>> Product::first()->getAlternates();
```

#### Command Line Management
```bash
# List all regions with status
php artisan regions:list

# Test region creation
php artisan regions:list create

# Check region associations
php artisan tinker
>>> Product::first()->regions()->get();
```

### 3. API Testing
```bash
# Test API endpoint
curl -X GET "http://localhost:8000/api/v1/products/1" -H "Accept: application/json"

# Test with region filter
curl -X GET "http://localhost:8000/api/v1/products?region=us" -H "Accept: application/json"

# Test alternates in response
curl -s "http://localhost:8000/api/v1/products/1" | jq '.data.product.alternates'
```

### 4. Performance Optimization

#### Cache Region Data
```php
// In your model or service
public static function getActiveRegions(): array
{
    return Cache::remember('active_regions', 3600, function () {
        return Region::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('name', 'code')
            ->toArray();
    });
}
```

#### Optimize Sitemap Generation
```php
// Use chunks for large datasets
Product::where('status', 'published')
    ->chunk(1000, function ($products) use ($sitemap) {
        foreach ($products as $product) {
            // Add to sitemap
        }
    });
```

### 5. Migration Issues

#### After Filament Compatibility Fix
If you experience issues after the Filament fix:

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart development server
php artisan serve
```

#### Verify Region Resource
```bash
# Check if Region resource is properly registered
php artisan route:list | grep regions

# Test command line access
php artisan regions:list
```

This backend usage guide provides comprehensive information for managing and extending the international SEO features in your Laravel application.
