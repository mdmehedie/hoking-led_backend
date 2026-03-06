<?php

namespace App\Http\Controllers;

use App\Services\RedisConfigService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RedisConfigController extends Controller
{
    /**
     * Test Redis connection
     */
    public function testConnection(): JsonResponse
    {
        $result = RedisConfigService::testConnection();
        
        return response()->json($result);
    }

    /**
     * Get Redis server information
     */
    public function getServerInfo(): JsonResponse
    {
        $result = RedisConfigService::getServerInfo();
        
        return response()->json($result);
    }

    /**
     * Get current Redis configuration
     */
    public function getConfig(): JsonResponse
    {
        $config = RedisConfigService::getConfig();
        
        // Hide password in response
        if (isset($config['password'])) {
            $config['password'] = $config['password'] ? '***' : null;
        }
        
        return response()->json([
            'success' => true,
            'config' => $config,
        ]);
    }

    /**
     * Clear Redis configuration cache
     */
    public function clearCache(): JsonResponse
    {
        RedisConfigService::clearConfigCache();
        
        return response()->json([
            'success' => true,
            'message' => 'Redis configuration cache cleared successfully',
        ]);
    }
}
