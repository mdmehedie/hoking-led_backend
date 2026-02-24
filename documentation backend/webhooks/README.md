# Form Webhooks Documentation

## Overview

The Form Webhooks system allows you to automatically send form submission data to external services like CRMs (HubSpot, Zoho, Salesforce), automation tools (Zapier, Make.com), or any custom webhook endpoint.

### Key Features

- ✅ **Automatic Integration**: Webhooks fire after every form submission
- ✅ **Retry Logic**: Failed webhooks retry up to 3 times with exponential backoff
- ✅ **Multiple CRMs**: Pre-configured examples for major CRM platforms
- ✅ **Custom Headers**: Support for authentication and custom headers
- ✅ **Admin Management**: View and manage webhooks through admin interface
- ✅ **Queue Processing**: Asynchronous processing for performance
- ✅ **Easy Setup**: Command-line tool for quick webhook creation
- ✅ **User-Friendly URLs**: Auto-add https:// protocol for domain inputs

### Current Status

**✅ Fully Working:**
- Webhook creation (modal form + command line)
- Webhook viewing and deletion
- Automatic webhook processing on form submissions
- Queue-based delivery with retry logic
- Command-line management tool

**⚠️ Known Limitations:**
- Edit functionality has UI compatibility issues (use command line alternatives)

### Architecture

```
Form Submission → Lead Storage → Webhook Dispatch → External Service
                              ↓
                       Queue Worker Processing
                              ↓
                       Retry Logic (3 attempts)
```

---

## Quick Start

1. **Add a webhook:**
   ```bash
   php artisan webhook:add 1 https://webhook.site/your-test-url
   ```

2. **Start queue worker:**
   ```bash
   php artisan queue:work --tries=3 --backoff=60,300,900
   ```

3. **Submit a test form:**
   ```bash
   curl -X POST http://localhost:8000/api/v1/forms/1/submit \
     -H "Content-Type: application/json" \
     -d '{"name":"Test User","email":"test@example.com"}'
   ```

4. **Check webhook delivery at webhook.site**

---

## Admin Interface

**Location:** Admin Panel → Forms → Edit Form → **Webhooks** tab

**Available Actions:**
- ✅ **Add Webhook**: Modal form with all configuration options
- ✅ **View Webhooks**: Table with status indicators and filters
- ✅ **Delete Webhooks**: Individual and bulk deletion
- ⚠️ **Edit Webhooks**: Use command line alternatives (see below)

---

## Command Line Tools

### Add Webhook
```bash
php artisan webhook:add {form_id} {url} [options]
```

**Examples:**
```bash
# Basic webhook
php artisan webhook:add 1 https://webhook.site/test

# With custom headers
php artisan webhook:add 1 https://api.crm.com/webhook \
  --headers='{"Authorization":"Bearer token"}'

# PUT method
php artisan webhook:add 1 https://api.crm.com/webhook --method=PUT

# Domain only (auto-adds https://)
php artisan webhook:add 1 webhook.site/test
```

### Edit Webhook (Alternatives)
```bash
# Option 1: Delete and recreate
php artisan webhook:add 1 https://new-url.com

# Option 2: Direct database update
php artisan tinker
$webhook = App\Models\FormWebhook::find(1);
$webhook->update(['url' => 'https://new-url.com']);
```

---

## Data Format

Webhooks receive the complete form submission data as JSON:

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "message": "Hello world"
}
```

---

## Support

For issues or questions:
1. Check the [Troubleshooting Guide](troubleshooting.md)
2. Verify queue worker is running: `php artisan queue:status`
3. Check logs: `tail -f storage/logs/laravel.log | grep webhook`
