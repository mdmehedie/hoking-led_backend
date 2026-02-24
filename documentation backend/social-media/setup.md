# Social Media Setup Guide

This guide walks you through setting up social media integration for automatic content sharing.

## Prerequisites

- Laravel application with queue system configured
- Admin access to social media developer portals
- API credentials for desired platforms

## Installation Steps

### 1. Database Migration

The migration has been created automatically. Run it to create the `social_accounts` table:

```bash
php artisan migrate
```

**Migration Details:**
- Stores platform credentials securely
- Supports Facebook, Twitter, LinkedIn
- Includes account status management for all content types

### 2. Queue Configuration

Ensure your queue system is properly configured:

```bash
# Check queue configuration
php artisan config:cache
php artisan queue:table
php artisan migrate
```

**Recommended Queue Settings:**
```env
QUEUE_CONNECTION=database
QUEUE_FAILED_JOBS_TABLE=failed_jobs
```

### 3. Start Queue Worker

For social media posting to work, you need a queue worker running:

```bash
# Basic worker
php artisan queue:work

# Production worker with retry settings
php artisan queue:work --tries=3 --backoff=60,300,900 --sleep=3 --timeout=90
```

**Supervisor Configuration (Production):**
```ini
[program:laravel-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --tries=3 --backoff=60,300,900
directory=/path/to/project
autostart=true
autorestart=true
numprocs=2
```

## Platform Setup

### Facebook Setup

1. **Create Facebook App**
   - Go to [Facebook Developers](https://developers.facebook.com/)
   - Create a new app or use existing one
   - Add "Facebook Login" and "Pages" products

2. **Get Required Credentials**
   - **App ID**: Found in app settings
   - **App Secret**: Found in app settings
   - **Access Token**: Generate in Graph API Explorer
   - **Page ID**: Get from your Facebook Page settings

3. **Configure Permissions**
   - `pages_manage_posts` - Required for posting
   - `pages_show_list` - Required for page access

### Twitter (X) Setup

1. **Create Twitter App**
   - Go to [Twitter Developer Portal](https://developer.twitter.com/)
   - Create a new project/app
   - Enable "Read and Write" permissions

2. **Get Required Credentials**
   - **API Key**: Found in app keys section
   - **API Secret**: Found in app keys section
   - **Access Token**: Generate in app keys section
   - **Access Token Secret**: Generate in app keys section

3. **App Permissions**
   - Set app permissions to "Read and Write"
   - Enable OAuth 1.0a for API access

### LinkedIn Setup

1. **Create LinkedIn App**
   - Go to [LinkedIn Developers](https://developer.linkedin.com/)
   - Create a new app
   - Add "Share on LinkedIn" product

2. **Get Required Credentials**
   - **Client ID**: Found in app credentials
   - **Client Secret**: Found in app credentials
   - **Access Token**: Generate using OAuth flow
   - **Organization ID**: Optional, for organization posting

3. **Configure Permissions**
   - `w_member_social` - Required for posting
   - `w_organization_social` - For organization posting

## Admin Configuration

### 1. Access Social Media Settings

1. Log into your admin panel
2. Navigate to **Settings → Social Media**
3. The page should display with account management interface

### 2. Add Social Accounts

1. Click **"Add Account"** button
2. Select platform from dropdown
3. Enter account name (for your reference)
4. Fill in API credentials based on platform requirements
5. Toggle "Active" to enable the account
6. Save the account

### 4. Configure URL Prefixes (Optional)

**Location**: Admin Panel → App Settings → URL Prefixes section

Configure URL prefixes for different content types to match your website's URL structure:

1. Navigate to **App Settings**
2. Find the **"URL Prefixes"** section
3. Set custom prefixes for each content type:
   - **Blog URL Prefix**: `/blog/` (default)
   - **News URL Prefix**: `/news/` (default)
   - **Page URL Prefix**: `/pages/` (default)
   - **Case Study URL Prefix**: `/case-studies/` (default)
   - **Product URL Prefix**: `/products/` (default)

4. Save the settings

**Benefits:**
- Match your website's existing URL patterns
- Maintain SEO-friendly URL structures
- Change anytime without code modifications
- Fallback to defaults if not configured

**Example:**
```
With custom prefixes:
- Blog: https://yoursite.com/articles/my-blog-post
- Product: https://yoursite.com/shop/awesome-product
```

## Testing the Integration

### Manual Testing

```bash
# Test with existing content
php artisan tinker

$blog = App\Models\Blog::where('status', 'published')->first();
App\Jobs\PublishToSocialMedia::dispatch($blog, 'blog', ['facebook']);
```

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

# Search for specific content
grep "blog.*published" storage/logs/laravel.log
```

## Security Considerations

### Credential Storage
- API credentials are stored encrypted in the database
- Use environment variables for sensitive production credentials
- Regularly rotate access tokens to maintain security

### Access Control
- Limit social media settings access to admin users only
- Implement role-based permissions for social account management
- Audit social media posting activities

### Rate Limiting
- Monitor API usage to avoid hitting platform limits
- Implement queuing delays between posts if needed
- Set up alerts for excessive failed posting attempts

## Troubleshooting Setup Issues

### Migration Issues
```bash
# Reset and re-run migrations
php artisan migrate:reset
php artisan migrate

# Check migration status
php artisan migrate:status
```

### Queue Issues
```bash
# Clear queue and restart
php artisan queue:clear
php artisan queue:restart

# Check queue configuration
php artisan config:show queue
```

### Permission Issues
- Verify API credentials are correct
- Check token expiration dates
- Confirm app permissions match requirements
- Test API access manually using tools like Postman

## Next Steps

After setup is complete:

1. **Monitor Performance**: Track posting success rates
2. **Set Up Alerts**: Configure notifications for failed posts
3. **Schedule Maintenance**: Regular token refresh and credential updates
4. **Scale Workers**: Add more queue workers as posting volume increases

---

[← Back to Main Documentation](../README.md) | [Platform Guides →](platforms/)
