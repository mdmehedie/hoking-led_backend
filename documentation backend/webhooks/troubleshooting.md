# Troubleshooting Guide

This guide helps you diagnose and resolve common webhook issues.

## Quick Diagnosis

### 1. Check Queue Worker Status
```bash
# Check if queue worker is running
php artisan queue:status

# List running processes
ps aux | grep queue:work

# Check queue size
php artisan queue:status
```

### 2. Verify Webhook Configuration
```bash
php artisan tinker
```
```php
// Check webhook exists and is active
$form = App\Models\Form::find(1);
$form->webhooks()->where('active', true)->get();

// Test webhook URL accessibility
$webhook = $form->webhooks()->first();
Http::get($webhook->url); // Should return response
```

### 3. Monitor Logs
```bash
# Monitor webhook logs in real-time
tail -f storage/logs/laravel.log | grep webhook

# Check recent webhook activity
grep "webhook" storage/logs/laravel.log | tail -20
```

## Common Issues & Solutions

### Issue: Webhooks Not Sending

**Symptoms:**
- Form submissions work but no webhook delivery
- Queue worker running but no webhook jobs processed

**Solutions:**

1. **Check Queue Worker:**
   ```bash
   # Restart queue worker
   php artisan queue:restart

   # Start new worker
   php artisan queue:work --tries=3 --backoff=60,300,900
   ```

2. **Verify Webhook Active:**
   ```bash
   php artisan tinker
   ```
   ```php
   App\Models\Form::find(1)->webhooks()->where('active', true)->exists(); // Should return true
   ```

3. **Check Queue Connection:**
   ```bash
   # Verify .env queue settings
   php artisan config:cache
   php artisan queue:restart
   ```

### Issue: 401 Unauthorized Errors

**Symptoms:**
- Webhook requests failing with 401 status
- CRM returning authentication errors

**Solutions:**

1. **Check API Token:**
   ```bash
   # Verify token in webhook headers
   php artisan tinker
   ```
   ```php
   $webhook = App\Models\FormWebhook::find(1);
   dd($webhook->headers); // Check Authorization header
   ```

2. **Refresh Token:**
   ```bash
   # Update webhook with fresh token
   php artisan webhook:add 1 https://api.crm.com/endpoint \
     --headers='{"Authorization":"Bearer NEW_TOKEN"}'
   ```

3. **Test Token Manually:**
   ```bash
   curl -H "Authorization: Bearer YOUR_TOKEN" https://api.crm.com/test
   ```

### Issue: 400 Bad Request Errors

**Symptoms:**
- Webhook requests failing with 400 status
- CRM complaining about data format

**Solutions:**

1. **Check Data Format:**
   ```bash
   # Submit test form and check data structure
   curl -X POST http://localhost:8000/api/v1/forms/1/submit \
     -H "Content-Type: application/json" \
     -d '{"name":"Test","email":"test@example.com"}'
   ```

2. **Verify Field Mapping:**
   - Compare form field names with CRM field names
   - Check required fields are included
   - Verify data types match CRM expectations

3. **Test with Minimal Data:**
   ```bash
   # Send minimal payload to isolate issues
   curl -X POST https://your-crm.com/endpoint \
     -H "Authorization: Bearer token" \
     -H "Content-Type: application/json" \
     -d '{"test_field":"test_value"}'
   ```

### Issue: Timeouts

**Symptoms:**
- Webhook jobs failing with timeout errors
- Queue worker showing timeout exceptions

**Solutions:**

1. **Increase Timeout:**
   ```php
   // In SendWebhook job
   $httpClient = Http::timeout(60); // Increase from 30 to 60 seconds
   ```

2. **Check Network Connectivity:**
   ```bash
   # Test connection to webhook URL
   curl -m 10 https://your-webhook-url.com
   ```

3. **Reduce Payload Size:**
   - Remove unnecessary form fields
   - Compress data if supported by CRM

### Issue: Rate Limiting

**Symptoms:**
- Webhook requests failing with 429 status
- CRM returning rate limit exceeded errors

**Solutions:**

1. **Check Rate Limits:**
   ```bash
   # Review CRM API documentation for rate limits
   # Common limits: 100 requests/10 seconds (HubSpot)
   ```

2. **Implement Delays:**
   ```php
   // Add delay between webhook calls
   sleep(1); // 1 second delay
   ```

3. **Batch Requests:**
   ```php
   // Send multiple form submissions in single webhook
   // Instead of individual requests
   ```

### Issue: Cannot Edit Webhooks Through Admin UI

**Symptoms:**
- Edit button in webhooks table doesn't work
- Modal doesn't open or shows errors
- Cannot modify webhook settings through admin interface

**Solutions:**

1. **Use Command Line (Recommended):**
   ```bash
   # Delete old webhook and create new one
   php artisan webhook:add 1 https://new-url.com --headers='{"key":"value"}'
   ```

2. **Direct Database Update:**
   ```bash
   php artisan tinker
   ```
   ```php
   $webhook = App\Models\FormWebhook::find(1);
   $webhook->update([
       'url' => 'https://new-url.com',
       'method' => 'PUT',
       'headers' => ['Authorization' => 'Bearer token'],
       'active' => false
   ]);
   ```

3. **UI Workaround:**
   - Delete the old webhook through admin UI
   - Create a new webhook with updated settings

**Note:** This is a known UI compatibility limitation. The core webhook functionality works perfectly.

## Queue Management

### Failed Jobs

**View Failed Jobs:**
```bash
php artisan queue:failed
```

**Retry Failed Jobs:**
```bash
# Retry specific job
php artisan queue:retry {id}

# Retry all failed jobs
php artisan queue:retry all
```

**Delete Failed Jobs:**
```bash
# Delete specific failed job
php artisan queue:forget {id}

# Clear all failed jobs
php artisan queue:flush
```

### Queue Monitoring

**Queue Status:**
```bash
php artisan queue:status
```

**Queue Workload:**
```bash
# Check pending jobs
php artisan queue:status

# Monitor queue size over time
watch -n 5 'php artisan queue:status'
```

## Database Issues

### Webhook Not Found

**Symptoms:**
- Webhook command fails with "Form not found"

**Solutions:**
```bash
# Check form exists
php artisan tinker
```
```php
App\Models\Form::find(1); // Should return form object
```

### Migration Issues

**Run Missing Migrations:**
```bash
php artisan migrate:status
php artisan migrate
```

**Rollback and Re-run:**
```bash
php artisan migrate:rollback --step=1
php artisan migrate
```

## Network Issues

### DNS Resolution

**Test DNS:**
```bash
nslookup your-webhook-url.com
dig your-webhook-url.com
```

### Firewall Blocking

**Check Firewall:**
```bash
# Test outbound connection
curl -v https://your-webhook-url.com

# Check firewall rules
sudo ufw status
sudo iptables -L
```

### Proxy Issues

**Configure Proxy:**
```bash
export HTTPS_PROXY=https://proxy.company.com:8080
export HTTP_PROXY=http://proxy.company.com:8080
```

Or in Laravel:
```php
// In config/http.php or directly in job
Http::withOptions([
    'proxy' => 'tcp://proxy.company.com:8080'
]);
```

## CRM-Specific Issues

### HubSpot

**Common Issues:**
- **Rate Limit**: 100 requests/10 seconds
- **Property Names**: Must match HubSpot property names exactly
- **Authentication**: Use hapikey or OAuth tokens

**Debug:**
```bash
# Test HubSpot API directly
curl -H "Authorization: Bearer YOUR_TOKEN" \
  https://api.hubapi.com/contacts/v1/contacts
```

### Zoho CRM

**Common Issues:**
- **Token Expiration**: Zoho tokens expire frequently
- **XML vs JSON**: Some endpoints require XML
- **Duplicate Prevention**: Zoho rejects duplicates

**Debug:**
```bash
# Check token validity
curl -H "Authorization: Zoho-oauthtoken YOUR_TOKEN" \
  https://crm.zoho.com/crm/private/json/info
```

### Salesforce

**Common Issues:**
- **Session Timeout**: Access tokens expire
- **Field Validation**: Required fields missing
- **Object Permissions**: API user lacks permissions

**Debug:**
```bash
# Test Salesforce API
curl -H "Authorization: Bearer YOUR_TOKEN" \
  https://yourinstance.salesforce.com/services/data/v58.0/
```

## Performance Issues

### High Memory Usage

**Monitor Memory:**
```bash
# Check memory usage
php artisan queue:work --memory=512 --max-jobs=1000
```

**Optimize Job:**
```php
// In SendWebhook job
public function handle()
{
    // Process in chunks for large datasets
    // Clear variables after use
    unset($largeVariable);
}
```

### Slow Response Times

**Optimize HTTP Client:**
```php
// Add connection pooling
Http::pool(function (Pool $pool) use ($webhooks, $data) {
    foreach ($webhooks as $webhook) {
        $pool->post($webhook->url, $data);
    }
});
```

## Advanced Debugging

### Enable Debug Logging

**In .env:**
```env
LOG_LEVEL=debug
```

**In SendWebhook job:**
```php
Log::debug("Webhook debug info", [
    'webhook' => $this->webhook->toArray(),
    'data' => $this->data,
    'headers' => $this->webhook->headers,
]);
```

### Custom Error Handling

```php
// In SendWebhook job
try {
    $response = $httpClient->post($this->webhook->url, $this->data);

    if ($response->serverError()) {
        Log::error("Server error", [
            'status' => $response->status(),
            'body' => $response->body(),
            'headers' => $response->headers(),
        ]);
    }
} catch (Throwable $e) {
    Log::error("Connection error", [
        'error' => $e->getMessage(),
        'webhook_url' => $this->webhook->url,
    ]);
    throw $e;
}
```

### Health Checks

**Create Health Check Endpoint:**
```php
// In routes/web.php
Route::get('/webhook-health', function () {
    $webhooks = \App\Models\FormWebhook::where('active', true)->count();
    $failedJobs = \Illuminate\Support\Facades\DB::table('failed_jobs')->count();

    return response()->json([
        'status' => 'ok',
        'active_webhooks' => $webhooks,
        'failed_jobs' => $failedJobs,
        'queue_status' => \Illuminate\Support\Facades\Artisan::call('queue:status'),
    ]);
});
```

## Support Resources

### Laravel Documentation
- [Queue Documentation](https://laravel.com/docs/queues)
- [HTTP Client](https://laravel.com/docs/http-client)
- [Logging](https://laravel.com/docs/logging)

### CRM Documentation
- [HubSpot API](https://developers.hubspot.com/docs/api/overview)
- [Zoho CRM API](https://www.zoho.com/crm/developer/docs/api/v2/)
- [Salesforce API](https://developer.salesforce.com/docs/)

### Community Resources
- [Laravel.io Forums](https://laravel.io/)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/laravel)
- CRM-specific developer communities

## Emergency Procedures

### Stop All Webhooks
```bash
php artisan tinker
```
```php
// Disable all webhooks
App\Models\FormWebhook::query()->update(['active' => false]);

// Clear queue
App\Models\FormWebhook::query()->delete();
```

### Restart Everything
```bash
# Stop queue workers
php artisan queue:restart

# Clear caches
php artisan config:clear
php artisan cache:clear

# Restart workers
php artisan queue:work --tries=3 --backoff=60,300,900 --daemon
```

### Emergency Logging
```php
// Log all webhook activity for debugging
Log::emergency("Webhook emergency", [
    'all_webhooks' => App\Models\FormWebhook::all()->toArray(),
    'queue_status' => shell_exec('php artisan queue:status'),
    'failed_jobs' => DB::table('failed_jobs')->count(),
]);
```

## Preventive Maintenance

### Regular Tasks
- **Monitor Failed Jobs:** `php artisan queue:failed` weekly
- **Update Tokens:** Refresh API tokens before expiration
- **Review Logs:** Check logs for unusual patterns
- **Test Webhooks:** Send test data monthly

### Automated Monitoring
```php
// Create scheduled command for monitoring
protected function schedule(Schedule $schedule)
{
    $schedule->command('queue:failed')->weekly();
    $schedule->call(function () {
        $failedCount = DB::table('failed_jobs')->count();
        if ($failedCount > 100) {
            // Send alert
        }
    })->daily();
}
```

---

## Need More Help?

1. **Check Logs:** `tail -f storage/logs/laravel.log | grep webhook`
2. **Test Manually:** Use curl to test webhook endpoints directly
3. **Verify Configuration:** Double-check API keys and URLs
4. **Contact Support:** Provide specific error messages and logs

Remember: Most webhook issues are related to authentication, data format, or network connectivity. Start with the basics and work your way up! 🚀
