# API Reference

## Webhook Data Structure

### Request Payload

Webhooks receive the complete form submission data as JSON:

```json
{
  "field_name_1": "value1",
  "field_name_2": "value2",
  "email": "user@example.com",
  "phone": "+1234567890"
}
```

### Headers

Custom headers are sent with each webhook request:

```http
Content-Type: application/json
Authorization: Bearer your_token
X-Webhook-Source: Laravel-Forms
X-Form-ID: 123
X-Submission-ID: 456
```

## Database Schema

### form_webhooks Table

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint unsigned | Primary key |
| `form_id` | bigint unsigned | Foreign key to forms table |
| `url` | varchar(255) | Webhook endpoint URL |
| `method` | enum('POST','PUT') | HTTP method |
| `headers` | json | Custom headers (nullable) |
| `active` | boolean | Webhook active status |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Update timestamp |

### Relationships

- **Form**: `form_webhooks` belongs to `forms`
- **Form**: `forms` has many `form_webhooks`

## Job Classes

### SendWebhook Job

**Class:** `App\Jobs\SendWebhook`

**Properties:**
- `$tries = 3` - Maximum retry attempts
- `$backoff = [60, 300, 900]` - Retry delays (1min, 5min, 15min)
- `$timeout = 30` - HTTP request timeout

**Constructor:**
```php
public function __construct(FormWebhook $webhook, array $data)
```

**Methods:**
- `handle()` - Execute webhook sending
- `failed()` - Handle permanent failures

## Artisan Commands

### webhook:add Command

**Signature:**
```bash
webhook:add {form_id} {url} {--method=POST} {--headers=} {--inactive}
```

**Arguments:**
- `form_id` - ID of the form (required)
- `url` - Webhook endpoint URL (required)

**Options:**
- `--method` - HTTP method (POST/PUT, default: POST)
- `--headers` - JSON string of headers
- `--inactive` - Create inactive webhook

**Example:**
```bash
php artisan webhook:add 1 https://api.example.com/webhook --headers='{"Authorization":"Bearer token"}'
```

## Models

### FormWebhook Model

**Class:** `App\Models\FormWebhook`

**Fillable Fields:**
```php
protected $fillable = [
    'form_id',
    'url',
    'method',
    'headers',
    'active',
];
```

**Casts:**
```php
protected $casts = [
    'headers' => 'array',
    'active' => 'boolean',
];
```

**Relationships:**
```php
public function form(): BelongsTo
{
    return $this->belongsTo(Form::class);
}
```

### Form Model (Extended)

**Additional Relationship:**
```php
public function webhooks(): HasMany
{
    return $this->hasMany(FormWebhook::class);
}
```

## HTTP Client Configuration

### Guzzle HTTP Client Settings

```php
$httpClient = Http::timeout(30);

// Custom headers
if ($webhook->headers) {
    foreach ($webhook->headers as $key => $value) {
        $httpClient = $httpClient->withHeaders([$key => $value]);
    }
}

// Send request
$response = match ($webhook->method) {
    'POST' => $httpClient->post($webhook->url, $data),
    'PUT' => $httpClient->put($webhook->url, $data),
};
```

## Queue Configuration

### Job Configuration

```php
// In SendWebhook job
public int $tries = 3;
public int $backoff = 60;
public array $backoffStrategy = [60, 300, 900];
```

### Queue Worker Command

```bash
php artisan queue:work \
  --tries=3 \
  --backoff=60,300,900 \
  --timeout=90 \
  --max-jobs=1000
```

## Error Handling

### Retry Logic

1. **Attempt 1**: Immediate execution
2. **Attempt 2**: 1 minute delay
3. **Attempt 3**: 5 minutes delay
4. **Attempt 4**: 15 minutes delay (final)

### Failure Handling

```php
public function failed(Throwable $exception): void
{
    // Log permanent failure
    Log::error("Webhook failed permanently", [
        'webhook_id' => $this->webhook->id,
        'error' => $exception->getMessage(),
    ]);

    // Optional: Send notification to admin
}
```

## Logging

### Success Log
```php
Log::info("Webhook sent successfully", [
    'webhook_id' => $webhook->id,
    'url' => $webhook->url,
    'status_code' => $response->status(),
    'attempt' => $this->attempts(),
]);
```

### Error Log
```php
Log::error("Webhook failed", [
    'webhook_id' => $webhook->id,
    'url' => $webhook->url,
    'error' => $exception->getMessage(),
    'attempt' => $this->attempts(),
]);
```

## Events (Optional Enhancement)

### Webhook Events

```php
// App\Events\WebhookSent
class WebhookSent
{
    public $webhook;
    public $data;
    public $response;
}

// App\Events\WebhookFailed
class WebhookFailed
{
    public $webhook;
    public $data;
    public $exception;
    public $attempt;
}
```

### Usage in Job

```php
// In SendWebhook::handle()
if ($response->successful()) {
    event(new WebhookSent($this->webhook, $this->data, $response));
} else {
    throw new Exception("HTTP {$response->status()}: {$response->body()}");
}
```

## Rate Limiting (Optional)

### Laravel Rate Limiting

```php
// In routes/api.php or middleware
RateLimiter::for('webhooks', function (Request $request) {
    return Limit::perMinute(60); // 60 requests per minute
});
```

### External Rate Limiting

```php
// Check before sending
if ($this->webhook->rateLimitExceeded()) {
    Log::warning("Rate limit exceeded for webhook {$this->webhook->id}");
    return;
}
```

## Monitoring & Metrics

### Queue Metrics

```php
// Check queue status
php artisan queue:status

// Monitor failed jobs
php artisan queue:failed
php artisan queue:retry all
```

### Custom Metrics (Optional)

```php
// Track webhook performance
Metric::increment('webhooks.sent');
Metric::increment('webhooks.failed');

// Response time tracking
$start = microtime(true);
// ... webhook sending ...
$duration = microtime(true) - $start;
Metric::histogram('webhook.duration', $duration);
```

## Environment Variables

### Required
```env
QUEUE_CONNECTION=database
```

### Optional
```env
WEBHOOK_TIMEOUT=30
WEBHOOK_MAX_RETRIES=3
WEBHOOK_BACKOFF_STRATEGY=60,300,900
```

## Security Considerations

### Input Validation
- URL validation in command
- JSON validation for headers
- Form ID validation

### Output Sanitization
- No sensitive data in logs
- Safe error messages
- Rate limiting protection

### Network Security
- HTTPS enforcement
- Timeout protection
- SSRF prevention

## Performance Optimization

### Database Indexing
```sql
CREATE INDEX idx_form_webhooks_form_id_active ON form_webhooks(form_id, active);
CREATE INDEX idx_form_webhooks_active ON form_webhooks(active);
```

### Connection Pooling
```php
// For high-volume webhooks
Http::pool(function (Pool $pool) {
    foreach ($webhooks as $webhook) {
        $pool->post($webhook->url, $data);
    }
});
```

### Caching
```php
// Cache active webhooks
$activeWebhooks = Cache::remember(
    "form.{$formId}.webhooks",
    3600, // 1 hour
    fn() => Form::find($formId)->webhooks()->where('active', true)->get()
);
```
