<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Spatie\ResponseCache\Facades\ResponseCache;

class CacheManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 15;

    protected static ?string $title = 'Cache Management';

    protected static ?string $navigationLabel = 'Cache Management';

    protected static string $view = 'filament.pages.cache-management';

    public function getHeading(): string
    {
        return 'Cache Management';
    }

    public function getSubheading(): string
    {
        return 'Manage application and response caching';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Cache Statistics')
                    ->description('View current cache status and statistics')
                    ->schema([
                        Forms\Components\Placeholder::make('cache_driver')
                            ->label('Cache Driver')
                            ->content(fn() => config('cache.default')),
                        Forms\Components\Placeholder::make('redis_status')
                            ->label('Redis Status')
                            ->content(fn() => $this->getRedisStatus()),
                        Forms\Components\Placeholder::make('cache_size')
                            ->label('Estimated Cache Size')
                            ->content(fn() => $this->getCacheSize()),
                        Forms\Components\Placeholder::make('response_cache_status')
                            ->label('Response Cache Status')
                            ->content(fn() => config('responsecache.enabled') ? 'Enabled' : 'Disabled'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Cache Operations')
                    ->description('Perform cache management operations')
                    ->schema([
                        Forms\Components\CheckboxList::make('operations')
                            ->label('Select Operations')
                            ->options([
                                'application' => 'Clear Application Cache',
                                'response' => 'Clear Response Cache',
                                'config' => 'Clear Configuration Cache',
                                'routes' => 'Clear Route Cache',
                                'views' => 'Clear View Cache',
                                'redis' => 'Clear Redis Cache (if using Redis)',
                            ])
                            ->required()
                            ->columns(2),
                    ]),
            ]);
    }

    public function clearCache(array $data): void
    {
        $operations = $data['operations'];
        $results = [];

        foreach ($operations as $operation) {
            try {
                switch ($operation) {
                    case 'application':
                        Artisan::call('cache:clear');
                        $results[] = 'Application cache cleared successfully';
                        break;

                    case 'response':
                        ResponseCache::clear();
                        $results[] = 'Response cache cleared successfully';
                        break;

                    case 'config':
                        Artisan::call('config:clear');
                        $results[] = 'Configuration cache cleared successfully';
                        break;

                    case 'routes':
                        Artisan::call('route:clear');
                        $results[] = 'Route cache cleared successfully';
                        break;

                    case 'views':
                        Artisan::call('view:clear');
                        $results[] = 'View cache cleared successfully';
                        break;

                    case 'redis':
                        if (config('cache.default') === 'redis') {
                            Cache::store('redis')->flush();
                            $results[] = 'Redis cache cleared successfully';
                        } else {
                            $results[] = 'Redis cache not in use';
                        }
                        break;
                }
            } catch (\Exception $e) {
                $results[] = "Error clearing {$operation} cache: " . $e->getMessage();
            }
        }

        $this->dispatch('cache-cleared', results: $results);

        Notification::make()
            ->title('Cache Operations Completed')
            ->body(implode("\n", $results))
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('clear_cache')
                ->label('Clear Selected Cache')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->action('clearCache')
                ->requiresConfirmation(),
        ];
    }

    private function getRedisStatus(): string
    {
        if (config('cache.default') !== 'redis') {
            return 'Not in use';
        }

        try {
            $redis = Cache::store('redis');
            $redis->ping();
            return 'Connected';
        } catch (\Exception $e) {
            return 'Connection failed: ' . $e->getMessage();
        }
    }

    private function getCacheSize(): string
    {
        if (config('cache.default') !== 'redis') {
            return 'N/A (not Redis)';
        }

        try {
            $redis = Cache::store('redis');
            $info = $redis->connection()->info('memory');
            $usedMemory = $info['used_memory'] ?? 0;
            
            // Convert to human readable format
            if ($usedMemory < 1024) {
                return $usedMemory . ' bytes';
            } elseif ($usedMemory < 1048576) {
                return round($usedMemory / 1024, 2) . ' KB';
            } else {
                return round($usedMemory / 1048576, 2) . ' MB';
            }
        } catch (\Exception $e) {
            return 'Unable to determine';
        }
    }

    public function getCacheStatistics(): array
    {
        $stats = [];

        try {
            if (config('cache.default') === 'redis') {
                $redis = Cache::store('redis');
                $info = $redis->connection()->info();
                
                $stats = [
                    'redis_version' => $info['redis_version'] ?? 'Unknown',
                    'connected_clients' => $info['connected_clients'] ?? 0,
                    'total_commands_processed' => $info['total_commands_processed'] ?? 0,
                    'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                    'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                    'hit_rate' => $this->calculateHitRate($info),
                ];
            }
        } catch (\Exception $e) {
            $stats['error'] = $e->getMessage();
        }

        return $stats;
    }

    private function calculateHitRate(array $info): string
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;

        if ($total === 0) {
            return '0%';
        }

        return round(($hits / $total) * 100, 2) . '%';
    }
}
