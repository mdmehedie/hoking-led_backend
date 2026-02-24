# Facebook Integration Guide

This guide covers setting up Facebook integration for automatic content sharing.

## Overview

The Facebook integration allows you to automatically post blog posts and products to your Facebook Page when they are published.

## Prerequisites

- Facebook Developer Account
- Facebook Page to post to
- Laravel application with social media integration

## Step-by-Step Setup

### 1. Create Facebook App

1. Go to [Facebook Developers](https://developers.facebook.com/)
2. Click **"Create App"**
3. Choose **"Business"** as app type
4. Fill in app details:
   - **App Name**: Your app name (e.g., "My Website Social Sharing")
   - **App Contact Email**: Your email
   - **Business Account**: Select or create business account

### 2. Add Products to Your App

1. In your app dashboard, click **"Add Product"**
2. Add **"Facebook Login"**
3. Add **"Meta for Developers"** (for Pages API)

### 3. Configure Facebook Login

1. Go to **Facebook Login → Settings**
2. Add your website domain to **"Valid OAuth Redirect URIs"**
   - Example: `https://yourwebsite.com/admin/social-media/callback`
3. Save changes

### 4. Get Your Page Access Token

#### Option A: Graph API Explorer (Recommended)

1. Go to [Graph API Explorer](https://developers.facebook.com/tools/explorer/)
2. Select your app from the dropdown
3. Click **"Generate Access Token"**
4. Select your Facebook Page and required permissions:
   - `pages_manage_posts`
   - `pages_show_list`
5. Copy the generated access token

#### Option B: App Dashboard

1. Go to **Meta for Developers → Tools**
2. Use **Access Token Tool** to generate page access token
3. Select your page and permissions

### 5. Get Your Page ID

1. Go to your Facebook Page
2. The Page ID is in the URL: `https://www.facebook.com/YOUR_PAGE_NAME`
3. Or use Graph API Explorer:
   ```http
   GET https://graph.facebook.com/v18.0/me/accounts?access_token=YOUR_ACCESS_TOKEN
   ```
4. Find your page in the response and copy the `id` field

### 6. Configure in Laravel Admin

1. Go to **Admin Panel → Settings → Social Media**
2. Click **"Add Account"**
3. Fill in the form:
   - **Platform**: Facebook
   - **Account Name**: Your Facebook Page name
   - **Credentials**:
     - `app_id`: Your Facebook App ID
     - `app_secret`: Your Facebook App Secret
     - `access_token`: Your Page Access Token
     - `page_id`: Your Facebook Page ID
4. Toggle **Active** to ON
5. Click **Save**

## Testing the Integration

### 1. Create Test Content

1. Create a blog post or product in your admin panel
2. Set status to **"Published"**
3. The system should automatically post to Facebook

### 2. Manual Testing

1. Go to Blogs or Products table
2. Find a published item
3. Click the **"Share"** button
4. Select **Facebook** in the modal
5. Click **"Share Now"**

### 3. Check Facebook Page

- Go to your Facebook Page
- Check if the post appears in the timeline
- Verify the content and link are correct

## Troubleshooting

### "Invalid OAuth access token"

**Cause**: Access token expired or invalid
**Solution**:
1. Generate a new access token in Graph API Explorer
2. Update the credentials in Social Media settings
3. Test again

### "Permissions Error"

**Cause**: Missing page permissions
**Solution**:
1. Check that `pages_manage_posts` permission is granted
2. Re-authorize the app with correct permissions
3. Generate new access token

### "Page not found"

**Cause**: Incorrect page ID
**Solution**:
1. Verify the page ID in Facebook Page settings
2. Test the page ID with Graph API Explorer
3. Update credentials if needed

### Posts Not Appearing

**Cause**: Various issues
**Solutions**:
1. Check queue worker is running: `php artisan queue:work`
2. Check logs: `tail -f storage/logs/laravel.log | grep facebook`
3. Verify page access token is valid
4. Check Facebook Page posting restrictions

## API Reference

### Facebook Graph API Endpoints Used

- **Post to Page**: `POST /{page-id}/feed`
- **Parameters**:
  - `message`: Post text content
  - `link`: URL to share
  - `access_token`: Page access token

### Post Format

```
🚀 New blog published: [Title]

[Excerpt from content]

#blog #newcontent

[Link to blog post]
```

## Security Notes

- **Access Tokens**: Regenerate every 60 days
- **App Secrets**: Never expose in client-side code
- **Permissions**: Only grant necessary permissions
- **Rate Limits**: Facebook allows ~200 posts per hour per page

## Advanced Configuration

### Custom Post Templates

You can modify the post format in `app/Jobs/PublishToSocialMedia.php`:

```php
protected function generatePostMessage(array $postData, string $platform): string
{
    // Customize Facebook post format here
    return "🚀 Check out our new {$postData['type']}: {$postData['title']}\n\n{$postData['excerpt']}";
}
```

### Multiple Facebook Pages

Create separate social accounts for each Facebook Page you want to post to.

### Scheduled Posting

Modify the job to support scheduled posting instead of immediate posting.

---

[← Back to Setup](../setup.md) | [Twitter Guide →](twitter.md) | [LinkedIn Guide →](linkedin.md)
