# Production Debug Guide for 403 Forbidden Issue

## Problem
- Admin panel works on localhost but returns 403 Forbidden on production
- URL: `https://admin.hongkongking.nexoda.io/admin`

## Debug Tools Added

### 1. Production Debug Middleware
- `App\Http\Middleware\ProductionDebugMiddleware`
- Logs admin access attempts to `storage/logs/laravel.log`
- Added to Filament admin middleware stack

### 2. Debug Routes
- `/debug/auth` - Check authentication status and roles (requires login)
- `/debug/env` - Check environment configuration (requires login)

## Debugging Steps

### Step 1: Test Authentication
1. Login to your application
2. Visit: `https://admin.hongkongking.nexoda.io/debug/auth`
3. Check the JSON response for:
   - `authenticated: true`
   - `admin_access.can_access_panel: true` OR
   - `admin_access.has_super_admin: true` OR
   - `admin_access.has_admin_role: true`

### Step 2: Check Environment
1. Visit: `https://admin.hongkongking.nexoda.io/debug/env`
2. Verify:
   - `environment: production`
   - `session.domain` matches your domain
   - `session.secure: true`

### Step 3: Monitor Logs
```bash
# On production server
tail -f storage/logs/laravel.log | grep "Admin access"
```

Look for entries with:
- "Admin access attempt" - shows incoming requests
- "Admin access response" - shows response codes
- Status 403 indicates permission/authentication issues

## Common 403 Causes

### 1. Session Issues
- Session domain mismatch between localhost and production
- HTTPS/HTTP inconsistency
- Session driver issues on production

### 2. Role/Permission Issues
- User missing `Super Admin` or `Admin` role
- Missing `access-admin-panel` permission
- Stale permission cache

### 3. Middleware/Configuration Issues
- Different middleware stack on production
- Filament panel configuration differences
- Web server configuration blocking access

### 4. Database Issues
- `model_has_roles` table missing entries
- Roles/permissions not properly seeded
- Database connection issues

## Quick Fixes to Try

### Clear Permission Cache
```bash
php artisan permission:cache-reset
php artisan config:clear
php artisan cache:clear
```

### Check User Roles
Run this SQL on production:
```sql
SELECT u.email, r.name as role_name 
FROM users u 
LEFT JOIN model_has_roles mr ON u.id = mr.model_id 
LEFT JOIN roles r ON mr.role_id = r.id 
WHERE u.id = YOUR_USER_ID;
```

### Verify Session Configuration
Check your `.env` on production:
```env
SESSION_DOMAIN=.hongkongking.nexoda.io
SESSION_SECURE=true
```

## Expected Debug Results

If working correctly, `/debug/auth` should show:
```json
{
  "authenticated": true,
  "admin_access": {
    "has_super_admin": true,
    "has_admin_role": true,
    "can_access_panel": true
  }
}
```

## Security Reminder

⚠️ **REMOVE DEBUG TOOLS AFTER FIXING**
1. Remove `ProductionDebugMiddleware` from `AdminPanelProvider`
2. Delete `AuthDebugController.php`
3. Remove debug routes from `web.php`
4. Delete this guide file

## Next Steps

1. Deploy debug tools to production
2. Access the debug endpoints
3. Review logs and debug output
4. Identify the specific cause
5. Apply the fix
6. Remove debug tools
