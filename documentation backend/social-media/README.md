# Social Media Integration

The Social Media Integration system allows you to automatically share content (blogs, products, pages, news, case studies) to connected social media platforms (Facebook, Twitter, LinkedIn) when they are published. You can also manually share content through admin interfaces.

## Overview

### Key Features

- ✅ **Automatic Publishing**: Content is automatically shared when published
- ✅ **Manual Sharing**: Share buttons in admin interfaces for on-demand posting
- ✅ **Multi-Platform Support**: Facebook, Twitter (X), and LinkedIn
- ✅ **Queue-Based Processing**: Asynchronous posting for performance
- ✅ **Admin Management**: Easy social account configuration
- ✅ **Retry Logic**: Failed posts retry with exponential backoff

### How It Works

```
Content Published → Observer Triggers → Job Dispatched → Queue Worker → Social APIs → Posted
Manual Share Click → Job Dispatched → Queue Worker → Social APIs → Posted
```

## Quick Start

### 1. Configure Social Accounts

1. Go to **Admin Panel → Settings → Social Media**
2. Click **"Add Account"**
3. Select platform (Facebook/Twitter/LinkedIn)
4. Enter account name and API credentials
5. Save the account

### 2. Start Queue Worker

```bash
php artisan queue:work --tries=3 --backoff=60,300,900
```

### 3. Publish Content

- **Automatic**: Simply publish a blog post or product - it will auto-share
- **Manual**: Use the "Share" button in blog/product tables to share existing content

### 4. Monitor Logs

```bash
# Check social media posting logs
tail -f storage/logs/laravel.log | grep "social\|PublishToSocialMedia"
```

## Supported Platforms

### Facebook
- **Required Credentials**: `app_id`, `app_secret`, `access_token`, `page_id`
- **Setup**: [Facebook Developers](https://developers.facebook.com/)
- **Posts to**: Connected Facebook Page

### Twitter (X)
- **Required Credentials**: `api_key`, `api_secret`, `access_token`, `access_token_secret`
- **Setup**: [Twitter Developer Portal](https://developer.twitter.com/)
- **Posts as**: Connected Twitter account

### LinkedIn
- **Required Credentials**: `client_id`, `client_secret`, `access_token`, `organization_id` (optional)
- **Setup**: [LinkedIn Developers](https://developer.linkedin.com/)
- **Posts as**: Personal profile or Organization page

## Admin Interface

### Social Media Settings Page
**Location**: Admin Panel → Settings → Social Media

**Features:**
- ✅ Add/edit/delete social accounts
- ✅ Platform-specific credential fields
- ✅ Account status management (active/inactive)
- ✅ Setup instructions for each platform

### URL Configuration

**Location**: Admin Panel → App Settings → URL Prefixes section

The system generates shareable URLs for social media posts using configurable prefixes. You can customize URL structures for different content types:

**Configurable Prefixes:**
- **Blog URL Prefix**: `/blog/` (default)
- **News URL Prefix**: `/news/` (default)  
- **Page URL Prefix**: `/pages/` (default)
- **Case Study URL Prefix**: `/case-studies/` (default)
- **Product URL Prefix**: `/products/` (default)

**URL Generation:**
```
Frontend URL + Prefix + Content Slug

Example: https://yoursite.com/blog/my-awesome-post
```

**Benefits:**
- ✅ **Flexible Structure**: Match your website's URL patterns
- ✅ **SEO Friendly**: Maintain consistent URL structures
- ✅ **Dynamic**: Change anytime without code modifications
- ✅ **Fallback Safe**: Uses defaults if not configured

## API Reference

### Job: PublishToSocialMedia

**Parameters:**
- `$content`: Blog or Product model instance
- `$contentType`: 'blog' or 'product'
- `$platforms`: Array of platforms or null for all active

**Usage:**
```php
// Auto-publish to all active accounts
PublishToSocialMedia::dispatch($blog, 'blog');

// Manual share to specific platforms
PublishToSocialMedia::dispatch($product, 'product', ['facebook', 'twitter']);
```

### Model: SocialAccount

**Methods:**
- `SocialAccount::active()` - Get all active accounts
- `SocialAccount::activeForPlatform('facebook')` - Get active accounts for platform
- `$account->getPlatformIcon()` - Get icon name for UI
- `$account->getPlatformColor()` - Get color for UI

## Troubleshooting

### Common Issues

#### "No active social media accounts found"
- **Cause**: No social accounts configured or all are inactive
- **Solution**: Add and activate social accounts in Settings → Social Media

#### "Failed to publish to [platform]"
- **Cause**: Invalid API credentials or expired tokens
- **Solution**: Check and update credentials in social account settings

#### "Queue worker not running"
- **Cause**: Laravel queue worker process not started
- **Solution**: Run `php artisan queue:work` command

#### "Content not auto-sharing"
- **Cause**: Observer not registered or content not properly published
- **Solution**: Check that content status is 'published' and observers are registered

### Logs and Monitoring

```bash
# View social media logs
grep "PublishToSocialMedia\|social" storage/logs/laravel.log

# Check queue status
php artisan queue:status

# Clear failed jobs
php artisan queue:failed
php artisan queue:flush
```

## Security Notes

- **API Credentials**: Store securely in database (encrypted by Laravel)
- **Token Rotation**: Regularly refresh access tokens to prevent expiration
- **Permissions**: Limit social media access to trusted admin users
- **Rate Limits**: Monitor API usage to avoid hitting platform limits

## Development Notes

### Adding New Platforms

1. Update `SocialAccount` model platform options
2. Add platform method in `PublishToSocialMedia` job
3. Update admin forms and validation
4. Add platform documentation

### Testing

```bash
# Test job dispatching (won't actually post)
php artisan tinker
$blog = App\Models\Blog::first();
App\Jobs\PublishToSocialMedia::dispatch($blog, 'blog', ['facebook']);
```

---

## Table of Contents

- [Setup Guide](setup.md) - Installation and configuration
- [Platform Guides](platforms/) - Facebook, Twitter, LinkedIn setup
- [API Reference](api-reference.md) - Technical API details
- [Troubleshooting](troubleshooting.md) - Common issues and solutions

---

**Need Help?** Check the troubleshooting guide or contact your development team!
