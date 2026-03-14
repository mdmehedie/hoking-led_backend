<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class RedisStats extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-server';
    protected static ?string $navigationLabel = 'Redis Stats';
    protected static ?string $title = 'Redis Statistics';
    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.redis-stats';

    public $refreshInterval = 5;

    public function mount(): void
    {
        $this->refreshStats();
    }

    public function refreshStats(): void
    {
        // This method will be called to refresh stats
        // Stats are fetched in the properties below
    }

    public function getRedisInfo(): array
    {
        try {
            $info = Redis::info();
            return $info;
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Redis Connection Error')
                ->body($e->getMessage())
                ->send();
            return [];
        }
    }

    public function getMemoryUsage(): array
    {
        $info = $this->getRedisInfo();
        
        if (empty($info)) {
            return [];
        }

        $memory = $info['memory'] ?? [];
        
        return [
            'used' => $this->formatBytes($memory['used_memory'] ?? 0),
            'used_human' => $memory['used_memory_human'] ?? '0B',
            'used_peak' => $this->formatBytes($memory['used_memory_peak'] ?? 0),
            'used_peak_human' => $memory['used_memory_peak_human'] ?? '0B',
            'used_percentage' => isset($memory['maxmemory']) && $memory['maxmemory'] > 0 
                ? round(($memory['used_memory'] / $memory['maxmemory']) * 100, 2) 
                : 0,
            'maxmemory' => $this->formatBytes($memory['maxmemory'] ?? 0),
            'maxmemory_human' => $memory['maxmemory_human'] ?? '0B',
        ];
    }

    public function getStats(): array
    {
        $info = $this->getRedisInfo();
        
        if (empty($info)) {
            return [];
        }

        $stats = $info['stats'] ?? [];
        $keyspace = $info['keyspace'] ?? [];
        
        $totalKeys = 0;
        foreach ($keyspace as $db => $data) {
            if (preg_match('/keys=(\d+)/', $data, $matches)) {
                $totalKeys += (int)$matches[1];
            }
        }

        return [
            'total_connections_received' => number_format($stats['total_connections_received'] ?? 0),
            'total_commands_processed' => number_format($stats['total_commands_processed'] ?? 0),
            'instantaneous_ops_per_sec' => number_format($stats['instantaneous_ops_per_sec'] ?? 0),
            'total_net_input_bytes' => $this->formatBytes($stats['total_net_input_bytes'] ?? 0),
            'total_net_output_bytes' => $this->formatBytes($stats['total_net_output_bytes'] ?? 0),
            'keyspace_hits' => number_format($stats['keyspace_hits'] ?? 0),
            'keyspace_misses' => number_format($stats['keyspace_misses'] ?? 0),
            'hit_rate' => $this->calculateHitRate($stats['keyspace_hits'] ?? 0, $stats['keyspace_misses'] ?? 0),
            'total_keys' => number_format($totalKeys),
            'expired_keys' => number_format($stats['expired_keys'] ?? 0),
            'evicted_keys' => number_format($stats['evicted_keys'] ?? 0),
        ];
    }

    public function getCacheStats(): array
    {
        try {
            $cacheStore = Cache::store();
            
            return [
                'driver' => config('cache.default'),
                'prefix' => config('cache.prefix'),
                'connection' => config('cache.stores.redis.connection', 'cache'),
            ];
        } catch (\Exception $e) {
            return [
                'driver' => 'unknown',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getDatabaseInfo(): array
    {
        $info = $this->getRedisInfo();
        $keyspace = $info['keyspace'] ?? [];
        
        $databases = [];
        foreach ($keyspace as $db => $data) {
            if (preg_match('/keys=(\d+),expires=(\d+),avg_ttl=(\d+)/', $data, $matches)) {
                $databases[$db] = [
                    'keys' => (int)$matches[1],
                    'expires' => (int)$matches[2],
                    'avg_ttl' => (int)$matches[3],
                    'avg_ttl_human' => $this->formatDuration($matches[3]),
                ];
            }
        }
        
        return $databases;
    }

    private function calculateHitRate($hits, $misses): string
    {
        $total = $hits + $misses;
        if ($total === 0) {
            return '0%';
        }
        
        return round(($hits / $total) * 100, 2) . '%';
    }

    private function formatBytes($bytes): string
    {
        if ($bytes === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    private function formatDuration($microseconds): string
    {
        $seconds = $microseconds / 1000000;
        
        if ($seconds < 60) {
            return round($seconds, 2) . 's';
        } elseif ($seconds < 3600) {
            return round($seconds / 60, 2) . 'm';
        } else {
            return round($seconds / 3600, 2) . 'h';
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh Stats')
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->refreshStats()),
        ];
    }
}
