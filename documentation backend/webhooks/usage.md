# Webhook Usage Guide

## Basic Usage

### Add a Simple Webhook

```bash
php artisan webhook:add {form_id} {webhook_url}
```

**Example:**
```bash
php artisan webhook:add 1 https://webhook.site/abc123
```

### Command Options

| Option | Description | Default |
|--------|-------------|---------|
| `form_id` | ID of the form to add webhook to | Required |
| `url` | Webhook endpoint URL | Required |
| `--method` | HTTP method (POST/PUT) | POST |
| `--headers` | JSON string of headers | null |
| `--inactive` | Create inactive webhook | false |

## Examples

### 1. Basic Webhook
```bash
php artisan webhook:add 1 https://api.example.com/webhook
```

### 2. Webhook with Custom Headers
```bash
php artisan webhook:add 1 https://api.example.com/webhook \
  --headers='{"Authorization":"Bearer token123","Content-Type":"application/json"}'
```

### 3. PUT Method Webhook
```bash
php artisan webhook:add 1 https://api.example.com/webhook --method=PUT
```

### 4. Inactive Webhook
```bash
php artisan webhook:add 1 https://api.example.com/webhook --inactive
```

## Managing Webhooks

### View Webhooks in Admin
1. Go to Admin Panel
2. Navigate to Forms
3. Edit a form
4. Click "Webhooks" tab

### Add Webhooks via UI
- Click "Add Webhook" button in the webhooks table
- Fill out the modal form with URL, method, headers, and active status
- URL field accepts both full URLs and domains (automatically adds https://)

### Delete Webhooks
- Use the admin interface to delete webhooks
- Or use database commands

### Update Webhooks
Due to UI compatibility limitations, webhooks must be deleted and re-created for updates using:
- Command line: `php artisan webhook:add {form_id} {new_url}`
- Database: Direct update via tinker

## Testing Webhooks

### 1. Start Queue Worker
```bash
php artisan queue:work --tries=3 --backoff=60,300,900
```

### 2. Submit Test Form
```bash
# Via API
curl -X POST http://localhost:8000/api/v1/forms/1/submit \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com"}'

# Via Frontend
# Submit form through your website
```

### 3. Verify Delivery
- Check your webhook endpoint (webhook.site, etc.)
- Monitor Laravel logs: `tail -f storage/logs/laravel.log | grep webhook`

## Batch Operations

### List All Webhooks
```bash
php artisan tinker
```
```php
App\Models\FormWebhook::all()->map(function($w) {
    return [
        'id' => $w->id,
        'form' => $w->form->name,
        'url' => $w->url,
        'active' => $w->active
    ];
});
```

### Deactivate All Webhooks for a Form
```bash
php artisan tinker
```
```php
App\Models\Form::find(1)->webhooks()->update(['active' => false]);
```

### Delete All Webhooks for a Form
```bash
php artisan tinker
```
```php
App\Models\Form::find(1)->webhooks()->delete();
```

## Advanced Usage

### Custom Headers with Variables
```bash
# Using Laravel config values
HEADERS='{"Authorization":"Bearer '$API_KEY'","X-API-Key":"'$SECRET'"}'
php artisan webhook:add 1 https://api.example.com/webhook --headers="$HEADERS"
```

### Multiple Webhooks per Form
```bash
# Add multiple webhooks to same form
php artisan webhook:add 1 https://crm1.com/webhook --headers='{"key":"value1"}'
php artisan webhook:add 1 https://crm2.com/webhook --headers='{"key":"value2"}'
php artisan webhook:add 1 https://analytics.com/track
```

### Environment-Specific Webhooks
```bash
# Different webhooks for different environments
if [ "$APP_ENV" = "production" ]; then
    php artisan webhook:add 1 https://prod-crm.com/webhook
else
    php artisan webhook:add 1 https://staging-crm.com/webhook
fi
```

## Command Help

```bash
php artisan webhook:add --help
```

Output:
```
Description:
  Add a webhook to a form

Usage:
  webhook:add <form_id> <url> [options]

Arguments:
  form_id                The ID of the form
  url                    The webhook URL

Options:
  --method[=METHOD]      HTTP method (POST/PUT) [default: "POST"]
  --headers[=HEADERS]    JSON string of headers
  --inactive             Make webhook inactive
  -h, --help             Display help for the given command
  -q, --quiet            Do not output any message
  -V, --version          Display this application version
      --ansi|--no-ansi   Force (or disable --ansi) output
  -n, --no-interaction   Do not ask any interactive question
      --env[=ENV]        The environment the command should run under
  -v|vv|vvv, --verbose   Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```
