<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthDebugController extends Controller
{
    public function checkAuth(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $data = [
            'timestamp' => now()->toISOString(),
            'authenticated' => (bool) $user,
            'auth_check' => Auth::check(),
            'auth_id' => Auth::id(),
        ];

        if ($user) {
            $data['user'] = [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'created_at' => $user->created_at,
            ];
            
            $data['roles'] = $user->roles()->pluck('name')->toArray();
            $data['permissions'] = $user->getAllPermissions()->pluck('name')->toArray();
            
            // Check admin access
            $data['admin_access'] = [
                'has_super_admin' => $user->hasRole('Super Admin'),
                'has_admin_role' => $user->hasRole('Admin'),
                'has_any_admin_role' => $user->hasAnyRole(['Super Admin', 'Admin']),
                'can_access_admin_panel' => $user->can('access-admin-panel'),
            ];
            
            // Check database entries
            $data['database_entries'] = [
                'model_has_roles' => \DB::table('model_has_roles')
                    ->where('model_type', 'App\\Models\\User')
                    ->where('model_id', $user->id)
                    ->exists(),
                'role_count' => \DB::table('model_has_roles')
                    ->where('model_type', 'App\\Models\\User')
                    ->where('model_id', $user->id)
                    ->count(),
            ];
        }

        // Session info
        $data['session'] = [
            'id' => $request->session()->getId(),
            'started' => $request->session()->isStarted(),
            'has_auth_confirmed' => $request->session()->has('auth.password_confirmed_at'),
        ];

        // Log this check
        Log::channel('single')->info('Auth debug check', $data);

        return response()->json($data);
    }

    public function checkEnvironment(): JsonResponse
    {
        $data = [
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
            'debug' => config('app.debug'),
            'url' => config('app.url'),
            'filament_panels' => array_keys(config('filament.panels', [])),
            'auth_config' => [
                'guards' => array_keys(config('auth.guards', [])),
                'provider' => config('auth.defaults.provider'),
            ],
            'session_config' => [
                'driver' => config('session.driver'),
                'lifetime' => config('session.lifetime'),
                'domain' => config('session.domain'),
                'secure' => config('session.secure'),
                'path' => config('session.path'),
            ],
        ];

        Log::channel('single')->info('Environment debug check', $data);

        return response()->json($data);
    }
}
