# Social Media API Reference

This document provides technical details about the social media integration APIs.

## Core Classes

### SocialAccount Model

**Location**: `app/Models/SocialAccount.php`

**Properties:**
```php
protected $fillable = [
    'platform',     // 'facebook', 'twitter', 'linkedin'
    'account_name', // Display name for the account
    'credentials',  // JSON array of API credentials
    'is_active',    // boolean: account active status
];
```

**Methods:**

#### `active(): Collection`
Get all active social accounts.
```php
$accounts = SocialAccount::active();
```

#### `activeForPlatform(string $platform): Collection`
Get active accounts for specific platform.
```php
$facebookAccounts = SocialAccount::activeForPlatform('facebook');
```

#### `getPlatformIcon(): string`
Get icon name for UI display.
```php
$icon = $account->getPlatformIcon(); // 'heroicon-o-facebook'
```

#### `getPlatformColor(): string`
Get color name for UI display.
```php
$color = $account->getPlatformColor(); // 'blue'
```

### PublishToSocialMedia Job

**Location**: `app/Jobs/PublishToSocialMedia.php`

**Constructor:**
```php
public function __construct($content, string $contentType, ?array $platforms = null)
```

**Parameters:**
- `$content`: Blog or Product model instance
- `$contentType`: 'blog' or 'product'
- `$platforms`: Array of platforms or null for all active

**Job Configuration:**
```php
public int $tries = 3;
public int $backoff = 60; // 1 minute, 5 minutes, 15 minutes
```

## Observers

### BlogObserver

**Location**: `app/Observers/BlogObserver.php`

**Events Handled:**
- `updated`: Triggers when blog status changes to 'published'
- `created`: Triggers when blog is created as 'published'

### ProductObserver

**Location**: `app/Observers/ProductObserver.php`

**Events Handled:**
- `updated`: Triggers when product status changes to 'published'
- `created`: Triggers when product is created as 'published'

## API Endpoints

### Platform APIs Used

#### Facebook Graph API
- **Endpoint**: `POST /{page-id}/feed`
- **Authentication**: Page Access Token
- **Parameters**:
  - `message`: Post content
  - `link`: URL to share
  - `access_token`: Page access token

#### Twitter API v2
- **Endpoint**: `POST /2/tweets`
- **Authentication**: OAuth 1.0a
- **Parameters**:
  - `text`: Tweet content (max 280 characters)

#### LinkedIn UGC API
- **Endpoint**: `POST /v2/ugcPosts`
- **Authentication**: Bearer Token
- **Content-Type**: UGC (User Generated Content)

## Queue Configuration

### Recommended Settings

```bash
# Start queue worker with retry settings
php artisan queue:work --tries=3 --backoff=60,300,900 --timeout=90
```

### Supervisor Configuration (Production)

```ini
[program:laravel-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --tries=3 --backoff=60,300,900
directory=/path/to/project
autostart=true
autorestart=true
numprocs=2
```

## Database Schema

### social_accounts Table

```sql
CREATE TABLE social_accounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(255) NOT NULL,
    account_name VARCHAR(255) NOT NULL,
    credentials JSON NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY platform_account (platform, account_name)
);
```

### Migration File
**Location**: `database/migrations/*_create_social_accounts_table.php`

## Content Processing

### Post Data Structure

```php
[
    'title' => 'Blog Post Title',
    'excerpt' => 'Post excerpt text...',
    'url' => 'https://example.com/blog/post-slug',
    'image' => 'https://example.com/storage/image.jpg', // or null
    'type' => 'blog' // or 'product'
]
```

### Platform-Specific Formatting

#### Facebook
- **Max Length**: No strict limit
- **Format**: Title + excerpt + hashtags + URL

#### Twitter
- **Max Length**: 280 characters
- **Format**: Shortened version + URL

#### LinkedIn
- **Max Length**: No strict limit
- **Format**: Professional format with hashtags

## Error Handling

### Job Failure Handling

```php
public function failed(Exception $exception): void
{
    // Log failure details
    Log::error('Social media publishing job failed', [
        'content_id' => $this->content->id ?? null,
        'content_type' => $this->contentType,
        'platforms' => $this->platforms,
        'error' => $exception->getMessage(),
    ]);
}
```

### Retry Logic

- **Max Attempts**: 3
- **Backoff**: 1min, 5min, 15min
- **Failure**: Job marked as failed, logged

## Security Considerations

### Credential Storage
- Credentials stored as JSON in database
- Laravel encryption applied automatically
- No plain text storage of API keys

### Access Control
- Admin-only access to social media settings
- Role-based permissions for account management
- Audit logging of social media activities

### Rate Limiting
- Platform-specific rate limits monitored
- Queue delays can be added if needed
- Failed job monitoring for abuse detection

## Extending the System

### Adding New Platforms

1. **Update SocialAccount Model**
   - Add platform to validation
   - Add icon/color methods

2. **Add Platform Method to Job**
   ```php
   protected function publishToNewPlatform(SocialAccount $account, array $postData): void
   {
       // Platform-specific API calls
   }
   ```

3. **Update Admin Forms**
   - Add platform to select options
   - Add credential fields

4. **Add Documentation**
   - Platform setup guide
   - API requirements
   - Troubleshooting

### Custom Post Templates

Override `generatePostMessage()` in the job:

```php
protected function generatePostMessage(array $postData, string $platform): string
{
    return match($platform) {
        'custom' => $this->generateCustomMessage($postData),
        default => parent::generatePostMessage($postData, $platform),
    };
}
```

### Scheduled Posting

Modify job to support delayed posting:

```php
// Dispatch with delay
PublishToSocialMedia::dispatch($content, $type)->delay(now()->addHours(2));
```

## Monitoring & Analytics

### Queue Monitoring

```bash
# Check queue status
php artisan queue:status

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Log Monitoring

```bash
# Monitor social media logs
tail -f storage/logs/laravel.log | grep -i "social\|publish"

# Search specific content
grep "content_id.*123" storage/logs/laravel.log
```

### Performance Metrics

- Posting success rate
- Average posting time
- Platform-specific error rates
- Queue processing throughput

---

[← Back to Main Documentation](../README.md) | [Troubleshooting →](troubleshooting.md)
