# LinkedIn Integration Guide

This guide covers setting up LinkedIn integration for automatic content sharing.

## Overview

The LinkedIn integration allows you to automatically post blog posts and products to your LinkedIn profile or company page when they are published.

## Prerequisites

- LinkedIn Developer Account
- LinkedIn App with API access
- Laravel application with social media integration

## Step-by-Step Setup

### 1. Create LinkedIn Developer Account

1. Go to [LinkedIn Developers](https://developer.linkedin.com/)
2. Sign in with your LinkedIn account
3. Verify your account if required

### 2. Create a LinkedIn App

1. Click **"Create App"**
2. Fill in app details:
   - **App Name**: Your app name
   - **LinkedIn Page**: Select your company page (optional)
   - **App Logo**: Upload your logo
   - **Description**: Brief description

### 3. Configure App Settings

1. In **"Auth"** tab, note your credentials:
   - **Client ID**
   - **Client Secret**

2. In **"Products"** tab, add:
   - **Share on LinkedIn** (required)
   - **Sign In with LinkedIn** (optional)

### 4. Generate Access Token

#### Option A: OAuth Flow (Recommended)

1. Implement LinkedIn OAuth flow in your app
2. Request permissions: `w_member_social` and `w_organization_social`
3. Store the generated access token

#### Option B: Manual Token Generation

1. Use LinkedIn's token generator tools
2. Request necessary scopes
3. Copy the access token

### 5. Get Organization ID (Optional)

If posting to a company page:

1. Go to your LinkedIn Company Page
2. The organization ID is in the URL or API response
3. Or use LinkedIn API to get organizations:
   ```http
   GET https://api.linkedin.com/v2/organizations?q=vanityName&vanityName=YOUR_PAGE_VANITY_NAME
   ```

### 6. Configure in Laravel Admin

1. Go to **Admin Panel → Settings → Social Media**
2. Click **"Add Account"**
3. Fill in the form:
   - **Platform**: LinkedIn
   - **Account Name**: Your LinkedIn profile/company name
   - **Credentials**:
     - `client_id`: Your Client ID
     - `client_secret`: Your Client Secret
     - `access_token`: Your Access Token
     - `organization_id`: Your Organization ID (optional)
4. Toggle **Active** to ON
5. Click **Save**

## Testing the Integration

### 1. Create Test Content

1. Create a blog post or product in your admin panel
2. Set status to **"Published"**
3. The system should automatically post to LinkedIn

### 2. Manual Testing

1. Go to Blogs or Products table
2. Find a published item
3. Click the **"Share"** button
4. Select **LinkedIn** in the modal
5. Click **"Share Now"**

### 3. Check LinkedIn Profile/Page

- Go to your LinkedIn profile or company page
- Check if the post appears in the feed
- Verify the content and link are correct

## Troubleshooting

### "Invalid Access Token"

**Cause**: Token expired or invalid
**Solution**:
1. Generate a new access token
2. Update credentials in Social Media settings
3. Test again

### "Insufficient Permissions"

**Cause**: Missing API permissions
**Solution**:
1. Check that `w_member_social` permission is granted
2. Add `w_organization_social` for company posting
3. Re-authorize the app

### "Organization Not Found"

**Cause**: Invalid organization ID
**Solution**:
1. Verify organization ID is correct
2. Check that you have admin access to the page
3. Use profile posting instead of organization

### Posts Not Appearing

**Cause**: Various issues
**Solutions**:
1. Check queue worker: `php artisan queue:work`
2. Check logs: `tail -f storage/logs/laravel.log | grep linkedin`
3. Verify access token permissions
4. Check LinkedIn posting restrictions

## API Reference

### LinkedIn API Endpoints Used

- **Post to Profile**: `POST /v2/ugcPosts`
- **Post to Organization**: `POST /v2/ugcPosts`
- **Authentication**: Bearer token
- **Content Type**: UGC (User Generated Content)

### Post Format

```json
{
  "author": "urn:li:person:YOUR_PERSON_ID",
  "lifecycleState": "PUBLISHED",
  "specificContent": {
    "com.linkedin.ugc.ShareContent": {
      "shareCommentary": {
        "text": "🚀 New blog published: [Title]\n\n[Excerpt]\n\n#blog #newcontent"
      },
      "shareMediaCategory": "NONE"
    }
  },
  "visibility": {
    "com.linkedin.ugc.MemberNetworkVisibility": "PUBLIC"
  }
}
```

## Security Notes

- **Access Tokens**: Expire after 60 days
- **Client Secrets**: Never expose in client code
- **Permissions**: Only request necessary scopes
- **Rate Limits**: LinkedIn allows ~100 posts per day per app

## Advanced Configuration

### Organization vs Profile Posting

- **Profile**: Posts appear on your personal feed
- **Organization**: Posts appear on company page feed
- Configure different accounts for each type

### Rich Media Posts

Enhance posts with images, videos, or articles using LinkedIn's UGC API.

### Custom Post Templates

Modify post format in `app/Jobs/PublishToSocialMedia.php`:

```php
protected function generatePostMessage(array $postData, string $platform): string
{
    if ($platform === 'linkedin') {
        return "🚀 New {$postData['type']} published: {$postData['title']}\n\n{$postData['excerpt']}\n\n{$postData['url']}\n\n#{$postData['type']} #professional #content";
    }
    // ... other platforms
}
```

---

[← Back to Setup](../setup.md) | [Facebook Guide →](facebook.md) | [Twitter Guide →](twitter.md)
