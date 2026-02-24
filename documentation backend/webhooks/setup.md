# Webhook Setup Guide

## Prerequisites

- Laravel application with forms system installed
- Queue driver configured (database recommended for production)
- Database tables migrated

## Installation Steps

### 1. Database Migration

Run the migration to create the `form_webhooks` table:

```bash
php artisan migrate
```

### 2. Queue Configuration

For production, configure a proper queue driver. Update `.env`:

```env
QUEUE_CONNECTION=database
# or
QUEUE_CONNECTION=redis
# or
QUEUE_CONNECTION=sqs
```

Create jobs table (if using database queue):

```bash
php artisan queue:table
php artisan migrate
```

### 3. Start Queue Worker

**Development:**
```bash
php artisan queue:work --tries=3 --backoff=60,300,900 --timeout=90
```

**Production (Supervisor example):**
Create `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --tries=3 --backoff=60,300,900 --timeout=90
directory=/path/to/project
autostart=true
autorestart=true
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/worker.log
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## Verification

### Check Queue Status
```bash
php artisan queue:status
```

### Test Webhook Command
```bash
php artisan webhook:add --help
```

### Verify Database Tables
Check that these tables exist:
- `forms`
- `form_webhooks`
- `jobs` (for queue)

## Environment Variables

No additional environment variables required. The system uses standard Laravel queue configuration.

## Security Considerations

- Store API keys securely (use Laravel's encrypted config for sensitive data)
- Validate webhook URLs to prevent SSRF attacks
- Use HTTPS for webhook endpoints
- Implement rate limiting on webhook endpoints if needed

## Monitoring

### Queue Monitoring
```bash
# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

### Log Monitoring
```bash
# Monitor webhook logs
tail -f storage/logs/laravel.log | grep webhook
```

## Performance Tuning

### Queue Worker Settings
- **--tries**: Number of retry attempts (default: 3)
- **--backoff**: Delay between retries in seconds
- **--timeout**: Job timeout in seconds
- **--sleep**: Sleep time when no jobs available
- **--max-jobs**: Maximum jobs per worker before restart

### Recommended Production Settings
```bash
php artisan queue:work \
  --tries=3 \
  --backoff=60,300,900 \
  --timeout=120 \
  --sleep=3 \
  --max-jobs=1000 \
  --memory=128
```
