<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProductionDebugMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only log admin panel access attempts
        if (str_starts_with($request->path(), 'admin')) {
            $user = $request->user();
            
            $logData = [
                'timestamp' => now()->toISOString(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
                'auth_status' => [
                    'authenticated' => (bool) $user,
                    'user_id' => $user?->id,
                    'email' => $user?->email,
                ],
            ];

            if ($user) {
                $logData['roles'] = $user->roles()->pluck('name')->toArray();
                $logData['permissions'] = $user->getAllPermissions()->pluck('name')->toArray();
                $logData['admin_checks'] = [
                    'has_super_admin' => $user->hasRole('Super Admin'),
                    'has_admin_role' => $user->hasRole('Admin'),
                    'can_access_panel' => $user->can('access-admin-panel'),
                ];
            }

            Log::channel('single')->info('Admin access attempt', $logData);
        }

        $response = $next($request);

        // Log response status for admin routes
        if (str_starts_with($request->path(), 'admin')) {
            Log::channel('single')->info('Admin access response', [
                'status_code' => $response->getStatusCode(),
                'url' => $request->fullUrl(),
                'user_id' => $request->user()?->id,
            ]);
        }

        return $response;
    }
}
