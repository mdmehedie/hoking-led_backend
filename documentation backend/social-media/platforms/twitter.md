# Twitter (X) Integration Guide

This guide covers setting up Twitter (X) integration for automatic content sharing.

## Overview

The Twitter integration allows you to automatically tweet blog posts and products when they are published.

## Prerequisites

- Twitter Developer Account
- Twitter App with API access
- Laravel application with social media integration

## Step-by-Step Setup

### 1. Create Twitter Developer Account

1. Go to [Twitter Developer Portal](https://developer.twitter.com/)
2. Sign in with your Twitter account
3. Apply for developer account if you don't have one
4. Choose account type (usually "Hobbyist" or "Professional")

### 2. Create a Twitter App

1. In the developer portal, click **"Create Project"**
2. Fill in project details:
   - **Project Name**: Your project name
   - **Project Description**: Brief description
   - **Use Case**: "Publish content" or similar
3. Create the project and then create an app within it

### 3. Configure App Settings

1. In your app settings, go to **"Keys and Tokens"**
2. Note down your credentials:
   - **API Key**: Consumer key
   - **API Secret**: Consumer secret

### 4. Generate Access Tokens

1. In **"Keys and Tokens"** section
2. Click **"Generate"** next to Access Token and Secret
3. Note down:
   - **Access Token**
   - **Access Token Secret**

### 5. Configure App Permissions

1. Go to **"App Permissions"** in your app settings
2. Set permissions to **"Read and Write"**
3. Save changes

### 6. Configure in Laravel Admin

1. Go to **Admin Panel → Settings → Social Media**
2. Click **"Add Account"**
3. Fill in the form:
   - **Platform**: Twitter
   - **Account Name**: Your Twitter handle (@username)
   - **Credentials**:
     - `api_key`: Your API Key
     - `api_secret`: Your API Secret
     - `access_token`: Your Access Token
     - `access_token_secret`: Your Access Token Secret
4. Toggle **Active** to ON
5. Click **Save**

## Testing the Integration

### 1. Create Test Content

1. Create a blog post or product in your admin panel
2. Set status to **"Published"**
3. The system should automatically tweet

### 2. Manual Testing

1. Go to Blogs or Products table
2. Find a published item
3. Click the **"Share"** button
4. Select **Twitter** in the modal
5. Click **"Share Now"**

### 3. Check Twitter Account

- Go to your Twitter profile
- Check if the tweet appears in your timeline
- Verify the content and link are correct

## Troubleshooting

### "Authentication Error"

**Cause**: Invalid API credentials
**Solution**:
1. Double-check all four credentials
2. Regenerate tokens if needed
3. Ensure app permissions are "Read and Write"

### "Rate Limit Exceeded"

**Cause**: Too many tweets in short time
**Solution**:
1. Twitter allows ~300 tweets per 3 hours
2. Add delays between posts if needed
3. Monitor posting frequency

### "Tweet Not Appearing"

**Cause**: Various issues
**Solutions**:
1. Check queue worker: `php artisan queue:work`
2. Check logs: `tail -f storage/logs/laravel.log | grep twitter`
3. Verify credentials are correct
4. Test API access manually

### "App Suspended"

**Cause**: Violation of Twitter rules
**Solution**:
1. Review Twitter Developer Agreement
2. Ensure content complies with policies
3. Contact Twitter support if needed

## API Reference

### Twitter API v2 Endpoints Used

- **Post Tweet**: `POST /2/tweets`
- **Authentication**: OAuth 1.0a
- **Parameters**:
  - `text`: Tweet content (max 280 characters)

### Tweet Format

```
🚀 New blog published: [Title]

[Excerpt from content]

#blog #newcontent

[Link to blog post]
```

## Security Notes

- **API Keys**: Keep secure, never expose in client code
- **Access Tokens**: Regenerate periodically
- **Rate Limits**: Monitor usage to avoid restrictions
- **Content Policies**: Ensure tweets comply with Twitter rules

## Advanced Configuration

### Custom Tweet Templates

Modify tweet format in `app/Jobs/PublishToSocialMedia.php`:

```php
protected function generatePostMessage(array $postData, string $platform): string
{
    if ($platform === 'twitter') {
        return "🚀 New {$postData['type']}: {$postData['title']} {$postData['url']} #content";
    }
    // ... other platforms
}
```

### Thread Posting

Modify the job to create tweet threads for longer content.

### Media Attachments

Add image attachments to tweets using Twitter Media API.

---

[← Back to Setup](../setup.md) | [Facebook Guide →](facebook.md) | [LinkedIn Guide →](linkedin.md)
