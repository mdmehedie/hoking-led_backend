# Social Media Troubleshooting Guide

This guide helps diagnose and resolve common social media integration issues.

## Quick Diagnosis

### 1. Check System Status

```bash
# Verify queue worker is running
php artisan queue:status

# Check recent logs
tail -f storage/logs/laravel.log | grep -i "social\|publish" | tail -10

# Check failed jobs
php artisan queue:failed
```

### 2. Test Social Accounts

```bash
php artisan tinker

# Check active accounts
App\Models\SocialAccount::active()->toArray()

# Test specific platform
App\Models\SocialAccount::activeForPlatform('facebook')->toArray()
```

### 3. Verify Content Status

```bash
# Check recently published content
App\Models\Blog::where('status', 'published')->latest()->first()
App\Models\Product::where('status', 'published')->latest()->first()
```

## Common Issues & Solutions

### Issue: Posts Not Appearing on Social Media

**Symptoms:**
- Content is published but no social media posts
- Queue shows jobs processed but no results
- No errors in logs

**Solutions:**

1. **Check Queue Worker:**
   ```bash
   # Start queue worker if not running
   php artisan queue:work --tries=3 --backoff=60,300,900
   ```

2. **Verify Social Accounts:**
   ```bash
   php artisan tinker
   App\Models\SocialAccount::active()->count() // Should be > 0
   ```

3. **Check Content Status:**
   - Ensure content status is 'published'
   - Check that published_at timestamp is set

4. **Manual Test:**
   ```bash
   php artisan tinker
   $blog = App\Models\Blog::where('status', 'published')->first();
   App\Jobs\PublishToSocialMedia::dispatch($blog, 'blog');
   ```

### Issue: "No active social media accounts found"

**Symptoms:**
- Logs show "No active social media accounts found"
- Content publishes but no social sharing

**Solutions:**

1. **Add Social Accounts:**
   - Go to Admin → Settings → Social Media
   - Add and activate social accounts
   - Verify credentials are correct

2. **Check Account Status:**
   ```bash
   php artisan tinker
   App\Models\SocialAccount::where('is_active', false)->get() // Check inactive accounts
   ```

3. **Verify Credentials:**
   - Check API keys are not expired
   - Ensure correct platform selected
   - Test credentials manually

### Issue: Authentication Errors

**Symptoms:**
- "Invalid access token" errors
- "Authentication failed" in logs
- Platform-specific auth errors

**Solutions by Platform:**

#### Facebook
- Regenerate Page Access Token in Graph API Explorer
- Check `pages_manage_posts` permission
- Verify App Secret matches

#### Twitter
- Regenerate API keys and access tokens
- Ensure "Read and Write" app permissions
- Check OAuth 1.0a setup

#### LinkedIn
- Refresh access token (expires every 60 days)
- Verify `w_member_social` permissions
- Check organization ID if using company pages

### Issue: Rate Limiting Errors

**Symptoms:**
- "Rate limit exceeded" errors
- Posts fail with HTTP 429 errors
- Platform temporarily blocks posting

**Solutions:**

1. **Check Platform Limits:**
   - Facebook: ~200 posts/hour per page
   - Twitter: ~300 tweets/3 hours
   - LinkedIn: ~100 posts/day per app

2. **Add Delays:**
   ```php
   // In PublishToSocialMedia job
   sleep(5); // Add delay between posts
   ```

3. **Monitor Usage:**
   ```bash
   # Check recent posting frequency
   grep "published to" storage/logs/laravel.log | tail -20
   ```

### Issue: Content Not Auto-Publishing

**Symptoms:**
- Manual sharing works but auto-publishing doesn't
- Content publishes but no social media jobs created

**Solutions:**

1. **Check Observers:**
   ```bash
   # Verify observers are registered in AppServiceProvider
   grep -n "observe" app/Providers/AppServiceProvider.php
   ```

2. **Test Observer Manually:**
   ```bash
   php artisan tinker
   $blog = App\Models\Blog::find(1);
   $blog->status = 'published';
   $blog->save(); // Should trigger observer
   ```

3. **Check Model Events:**
   - Ensure Blog/Product model has correct status field
   - Verify published_at field exists

### Issue: Share Button Not Working

**Symptoms:**
- Share button doesn't appear in tables
- Clicking share button shows no modal
- Modal appears but sharing fails

**Solutions:**

1. **Check Button Visibility:**
   - Share button only shows for status = 'published'
   - Verify content status in database

2. **Clear Cache:**
   ```bash
   php artisan view:clear
   php artisan config:clear
   php artisan route:clear
   ```

3. **Check JavaScript Errors:**
   - Open browser developer tools
   - Check console for Filament JS errors

### Issue: Failed Jobs Accumulating

**Symptoms:**
- Queue:failed shows many social media jobs
- Jobs failing repeatedly

**Solutions:**

1. **Check Failed Jobs:**
   ```bash
   php artisan queue:failed
   ```

2. **Retry Failed Jobs:**
   ```bash
   # Retry all failed jobs
   php artisan queue:retry all

   # Retry specific job
   php artisan queue:retry {id}
   ```

3. **Delete Failed Jobs:**
   ```bash
   # Clear all failed jobs
   php artisan queue:flush
   ```

4. **Investigate Root Cause:**
   - Check job payload in failed_jobs table
   - Verify API credentials
   - Test platform APIs manually

## Platform-Specific Issues

### Facebook Issues

#### "(#200) Requires pages_manage_posts permission"
**Solution:** Regenerate access token with correct permissions

#### "Page not found"
**Solution:** Verify page_id is correct and accessible

#### "Application request limit reached"
**Solution:** Wait for rate limit reset or reduce posting frequency

### Twitter Issues

#### "Read-only application cannot POST"
**Solution:** Change app permissions to "Read and Write"

#### "Status is over 140 characters"
**Solution:** Check character count in post generation

#### "Timestamp out of bounds"
**Solution:** Check server time synchronization

### LinkedIn Issues

#### "Access token expired"
**Solution:** Generate new access token (60-day expiry)

#### "Not enough permissions to access"
**Solution:** Check organization membership for company pages

#### "Invalid UGC content"
**Solution:** Verify post format matches LinkedIn API requirements

## Monitoring & Maintenance

### Regular Tasks

#### Daily
- Check queue status: `php artisan queue:status`
- Monitor failed jobs: `php artisan queue:failed`
- Review recent logs for errors

#### Weekly
- Clear old failed jobs: `php artisan queue:flush`
- Test social media posting manually
- Verify API credentials still valid

#### Monthly
- Review posting success rates
- Check API usage against limits
- Update expired access tokens

### Automated Monitoring

```php
// In App\Console\Commands\MonitorSocialMedia.php
public function handle()
{
    $failedCount = DB::table('failed_jobs')
        ->where('payload', 'like', '%PublishToSocialMedia%')
        ->count();

    if ($failedCount > 10) {
        // Send alert
        Log::emergency("High social media job failure rate: {$failedCount}");
    }
}
```

### Log Analysis

```bash
# Count successful posts by platform
grep "published to" storage/logs/laravel.log | cut -d' ' -f10 | sort | uniq -c

# Find recent errors
grep "ERROR.*social" storage/logs/laravel.log | tail -10

# Check posting frequency
grep "PublishToSocialMedia.*processed" storage/logs/laravel.log | tail -20
```

## Emergency Procedures

### Complete Social Media Shutdown

```bash
# Stop queue worker
php artisan queue:restart

# Deactivate all social accounts
php artisan tinker
App\Models\SocialAccount::query()->update(['is_active' => false]);

# Clear pending jobs
php artisan queue:clear
```

### Re-enable Social Media

```bash
# Reactivate accounts
App\Models\SocialAccount::query()->update(['is_active' => true]);

# Restart queue worker
php artisan queue:work --tries=3 --backoff=60,300,900
```

## Getting Help

### Debug Information

When reporting issues, include:

1. **Laravel Version & Environment**
2. **Queue Driver & Status**
3. **Social Account Configuration** (redact credentials)
4. **Recent Log Entries**
5. **Failed Job Details**

### Support Checklist

- [ ] Queue worker running
- [ ] Social accounts configured and active
- [ ] API credentials valid and not expired
- [ ] Content properly published (status = 'published')
- [ ] Platform permissions correct
- [ ] Rate limits not exceeded
- [ ] Network connectivity to platform APIs

---

**Remember:** Most social media issues are related to authentication, permissions, or rate limits. Start with the basics and work systematically through the troubleshooting steps!
