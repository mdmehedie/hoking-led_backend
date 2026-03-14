# Performance Monitoring Guide

## 📋 Overview

This guide covers the comprehensive performance monitoring system, including cache management, performance metrics, monitoring tools, and troubleshooting procedures.

## 🚀 Features Overview

### **Cache Management Interface**
- Real-time cache statistics
- Manual cache operations
- Performance metrics
- Cache health monitoring

### **Performance Metrics**
- Redis performance monitoring
- Cache hit rates
- Memory usage tracking
- Response time monitoring

### **Monitoring Tools**
- Filament admin interface
- Command-line tools
- Performance logging
- Alert system

## 📊 Cache Management Interface

### Filament Cache Management Page

The Cache Management page provides comprehensive cache monitoring and control:

```php
// app/Filament/Pages/CacheManagement.php
class CacheManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.cache-management';

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
            ]);
    }
}
```

### Cache Statistics Display

```php
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
                'used_memory' => $this->formatBytes($info['used_memory'] ?? 0),
                'max_memory' => $this->formatBytes($info['maxmemory'] ?? 0),
                'memory_usage_percent' => $this->calculateMemoryUsage($info),
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
```

### Cache Operations

```php
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
```

## 📈 Performance Metrics

### Redis Performance Monitoring

```php
// app/Services/PerformanceMonitor.php
class PerformanceMonitor
{
    public function getRedisMetrics(): array
    {
        $redis = Cache::store('redis');
        $info = $redis->connection()->info('all');
        
        return [
            'server' => [
                'redis_version' => $info['redis_version'] ?? 'Unknown',
                'redis_mode' => $info['redis_mode'] ?? 'standalone',
                'os' => $info['os'] ?? 'Unknown',
                'arch_bits' => $info['arch_bits'] ?? 'Unknown',
                'uptime_in_seconds' => $info['uptime_in_seconds'] ?? 0,
                'uptime_in_days' => $info['uptime_in_days'] ?? 0,
            ],
            'memory' => [
                'used_memory' => $info['used_memory'] ?? 0,
                'used_memory_human' => $this->formatBytes($info['used_memory'] ?? 0),
                'used_memory_rss' => $info['used_memory_rss'] ?? 0,
                'used_memory_peak' => $info['used_memory_peak'] ?? 0,
                'maxmemory' => $info['maxmemory'] ?? 0,
                'maxmemory_human' => $this->formatBytes($info['maxmemory'] ?? 0),
                'memory_fragmentation_ratio' => $info['mem_fragmentation_ratio'] ?? 0,
            ],
            'clients' => [
                'connected_clients' => $info['connected_clients'] ?? 0,
                'client_recent_max_input_buffer' => $info['client_recent_max_input_buffer'] ?? 0,
                'client_recent_max_output_buffer' => $info['client_recent_max_output_buffer'] ?? 0,
                'blocked_clients' => $info['blocked_clients'] ?? 0,
            ],
            'stats' => [
                'total_connections_received' => $info['total_connections_received'] ?? 0,
                'total_commands_processed' => $info['total_commands_processed'] ?? 0,
                'instantaneous_ops_per_sec' => $info['instantaneous_ops_per_sec'] ?? 0,
                'total_net_input_bytes' => $info['total_net_input_bytes'] ?? 0,
                'total_net_output_bytes' => $info['total_net_output_bytes'] ?? 0,
                'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                'hit_rate' => $this->calculateHitRate($info),
            ],
            'keyspace' => [
                'keys' => $info['db0'] ?? [],
                'expires' => $info['db0'] ?? [],
                'avg_ttl' => $info['db0'] ?? [],
            ],
        ];
    }

    private function calculateHitRate(array $info): float
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;

        return $total > 0 ? round(($hits / $total) * 100, 2) : 0.0;
    }

    private function formatBytes($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
```

### Cache Hit Rate Monitoring

```php
// Monitor cache performance and alert if needed
public function monitorCachePerformance(): void
{
    $metrics = $this->getRedisMetrics();
    $hitRate = $metrics['stats']['hit_rate'];
    $memoryUsage = $this->calculateMemoryUsage($metrics['memory']);

    // Alert if hit rate is low
    if ($hitRate < 80) {
        logger()->warning('Low cache hit rate detected', [
            'hit_rate' => $hitRate,
            'threshold' => 80,
            'timestamp' => now(),
        ]);

        $this->sendAlert('Low Cache Hit Rate', "Current hit rate: {$hitRate}%");
    }

    // Alert if memory usage is high
    if ($memoryUsage > 85) {
        logger()->warning('High Redis memory usage detected', [
            'memory_usage' => $memoryUsage,
            'threshold' => 85,
            'timestamp' => now(),
        ]);

        $this->sendAlert('High Memory Usage', "Current usage: {$memoryUsage}%");
    }
}
```

### Response Time Monitoring

```php
// Monitor response times for cached vs uncached requests
class ResponseTimeMonitor
{
    public function recordResponseTime(string $url, float $responseTime, bool $cached): void
    {
        $key = 'response_times:' . date('Y-m-d');
        
        $data = Cache::get($key, [
            'total_requests' => 0,
            'cached_requests' => 0,
            'uncached_requests' => 0,
            'total_time' => 0,
            'cached_time' => 0,
            'uncached_time' => 0,
        ]);

        $data['total_requests']++;
        $data['total_time'] += $responseTime;

        if ($cached) {
            $data['cached_requests']++;
            $data['cached_time'] += $responseTime;
        } else {
            $data['uncached_requests']++;
            $data['uncached_time'] += $responseTime;
        }

        Cache::put($key, $data, now()->addDays(7));
    }

    public function getResponseTimeStats(): array
    {
        $key = 'response_times:' . date('Y-m-d');
        $data = Cache::get($key, []);

        return [
            'avg_response_time' => $data['total_requests'] > 0 ? 
                round($data['total_time'] / $data['total_requests'], 3) : 0,
            'avg_cached_time' => $data['cached_requests'] > 0 ? 
                round($data['cached_time'] / $data['cached_requests'], 3) : 0,
            'avg_uncached_time' => $data['uncached_requests'] > 0 ? 
                round($data['uncached_time'] / $data['uncached_requests'], 3) : 0,
            'cache_effectiveness' => $data['uncached_time'] > 0 ? 
                round((1 - ($data['cached_time'] / $data['uncached_time'])) * 100, 2) : 0,
        ];
    }
}
```

## 🛠️ Monitoring Tools

### Command-Line Monitoring

```php
// app/Console/Commands/MonitorPerformance.php
class MonitorPerformance extends Command
{
    protected $signature = 'monitor:performance 
                            {--alert : Send alerts for issues}
                            {--export : Export metrics to file}';

    protected $description = 'Monitor application performance';

    public function handle()
    {
        $monitor = new PerformanceMonitor();
        $metrics = $monitor->getRedisMetrics();

        $this->displayMetrics($metrics);

        if ($this->option('alert')) {
            $monitor->monitorCachePerformance();
        }

        if ($this->option('export')) {
            $this->exportMetrics($metrics);
        }
    }

    private function displayMetrics(array $metrics): void
    {
        $this->info('=== Redis Performance Metrics ===');
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Version', $metrics['server']['redis_version']],
                ['Uptime', $metrics['server']['uptime_in_days'] . ' days'],
                ['Connected Clients', $metrics['clients']['connected_clients']],
                ['Hit Rate', $metrics['stats']['hit_rate'] . '%'],
                ['Memory Used', $metrics['memory']['used_memory_human']],
                ['Ops/sec', $metrics['stats']['instantaneous_ops_per_sec']],
            ]
        );

        // Alert if issues detected
        $hitRate = (float) $metrics['stats']['hit_rate'];
        if ($hitRate < 80) {
            $this->warn("⚠️  Low cache hit rate: {$hitRate}%");
        }

        $memoryUsage = $this->calculateMemoryUsage($metrics['memory']);
        if ($memoryUsage > 85) {
            $this->warn("⚠️  High memory usage: {$memoryUsage}%");
        }
    }

    private function exportMetrics(array $metrics): void
    {
        $filename = 'performance-metrics-' . date('Y-m-d-H-i-s') . '.json';
        $path = storage_path("app/{$filename}");
        
        file_put_contents($path, json_encode($metrics, JSON_PRETTY_PRINT));
        
        $this->info("Metrics exported to: {$filename}");
    }
}
```

### Performance Dashboard

```php
// app/Filament/Widgets/PerformanceWidget.php
class PerformanceWidget extends Widget
{
    protected static string $view = 'filament.widgets.performance-widget';

    protected function getViewData(): array
    {
        $monitor = new PerformanceMonitor();
        $metrics = $monitor->getRedisMetrics();

        return [
            'hit_rate' => $metrics['stats']['hit_rate'],
            'memory_usage' => $this->calculateMemoryUsage($metrics['memory']),
            'connected_clients' => $metrics['clients']['connected_clients'],
            'ops_per_sec' => $metrics['stats']['instantaneous_ops_per_sec'],
            'uptime' => $metrics['server']['uptime_in_days'],
        ];
    }

    protected function calculateMemoryUsage(array $memory): float
    {
        $used = $memory['used_memory'] ?? 0;
        $max = $memory['maxmemory'] ?? 0;

        return $max > 0 ? round(($used / $max) * 100, 2) : 0.0;
    }
}
```

### Real-time Monitoring

```javascript
// resources/js/performance-monitor.js
class RealTimeMonitor {
    constructor() {
        this.ws = null;
        this.metrics = {};
        this.init();
    }

    init() {
        this.connectWebSocket();
        this.startMetricsCollection();
    }

    connectWebSocket() {
        this.ws = new WebSocket('ws://localhost:8080/monitor');
        
        this.ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.updateMetrics(data);
        };

        this.ws.onerror = (error) => {
            console.error('WebSocket error:', error);
        };

        this.ws.onclose = () => {
            setTimeout(() => this.connectWebSocket(), 5000);
        };
    }

    startMetricsCollection() {
        setInterval(() => {
            this.collectMetrics();
        }, 5000); // Collect every 5 seconds
    }

    collectMetrics() {
        const metrics = {
            timestamp: Date.now(),
            url: window.location.href,
            load_time: performance.timing.loadEventEnd - performance.timing.navigationStart,
            dom_content_loaded: performance.timing.domContentLoadedEventEnd - performance.timing.navigationStart,
            first_paint: performance.getEntriesByName('first-paint')[0]?.startTime,
            first_contentful_paint: performance.getEntriesByName('first-contentful-paint')[0]?.startTime,
        };

        this.sendMetrics(metrics);
    }

    sendMetrics(metrics) {
        if (this.ws && this.ws.readyState === WebSocket.OPEN) {
            this.ws.send(JSON.stringify(metrics));
        }
    }

    updateMetrics(data) {
        this.metrics = data;
        this.updateUI();
    }

    updateUI() {
        // Update performance dashboard UI
        const hitRateElement = document.getElementById('hit-rate');
        if (hitRateElement) {
            hitRateElement.textContent = this.metrics.hit_rate + '%';
        }

        const memoryElement = document.getElementById('memory-usage');
        if (memoryElement) {
            memoryElement.textContent = this.metrics.memory_usage + '%';
        }
    }
}

// Initialize monitor
new RealTimeMonitor();
```

## 📊 Performance Alerts

### Alert System

```php
// app/Services/AlertService.php
class AlertService
{
    public function sendAlert(string $type, string $message, array $context = []): void
    {
        $alert = [
            'type' => $type,
            'message' => $message,
            'context' => $context,
            'timestamp' => now(),
            'severity' => $this->getSeverity($type),
        ];

        // Log alert
        logger()->warning($message, $context);

        // Send to monitoring service
        $this->sendToMonitoringService($alert);

        // Send email if critical
        if ($alert['severity'] === 'critical') {
            $this->sendEmailAlert($alert);
        }

        // Send webhook
        $this->sendWebhook($alert);
    }

    private function getSeverity(string $type): string
    {
        $severities = [
            'low_cache_hit_rate' => 'warning',
            'high_memory_usage' => 'warning',
            'redis_connection_failed' => 'critical',
            'cache_size_exceeded' => 'critical',
        ];

        return $severities[$type] ?? 'info';
    }

    private function sendToMonitoringService(array $alert): void
    {
        // Send to your monitoring service (Datadog, New Relic, etc.)
        Http::post(config('monitoring.webhook_url'), $alert);
    }

    private function sendEmailAlert(array $alert): void
    {
        // Send email to administrators
        Mail::to(config('monitoring.admin_email'))
            ->send(new PerformanceAlert($alert));
    }

    private function sendWebhook(array $alert): void
    {
        // Send to Slack, Discord, etc.
        Http::post(config('monitoring.slack_webhook'), [
            'text' => "🚨 {$alert['type']}: {$alert['message']}",
            'attachments' => [
                [
                    'color' => $alert['severity'] === 'critical' ? 'danger' : 'warning',
                    'fields' => array_map(
                        fn($key, $value) => ['title' => $key, 'value' => $value, 'short' => true],
                        array_keys($alert['context']),
                        $alert['context']
                    ),
                ],
            ],
        ]);
    }
}
```

### Alert Conditions

```php
// Define alert conditions
class AlertConditions
{
    public function checkAllConditions(): void
    {
        $this->checkCacheHitRate();
        $this->checkMemoryUsage();
        $this->checkRedisConnection();
        $this->checkCacheSize();
        $this->checkResponseTime();
    }

    private function checkCacheHitRate(): void
    {
        $monitor = new PerformanceMonitor();
        $metrics = $monitor->getRedisMetrics();
        $hitRate = (float) $metrics['stats']['hit_rate'];

        if ($hitRate < 80) {
            app(AlertService::class)->sendAlert('low_cache_hit_rate', 
                "Cache hit rate is below 80%", 
                ['hit_rate' => $hitRate, 'threshold' => 80]
            );
        }
    }

    private function checkMemoryUsage(): void
    {
        $monitor = new PerformanceMonitor();
        $metrics = $monitor->getRedisMetrics();
        $memoryUsage = $this->calculateMemoryUsage($metrics['memory']);

        if ($memoryUsage > 85) {
            app(AlertService::class)->sendAlert('high_memory_usage', 
                "Redis memory usage is above 85%", 
                ['memory_usage' => $memoryUsage, 'threshold' => 85]
            );
        }
    }

    private function checkRedisConnection(): void
    {
        try {
            Cache::store('redis')->ping();
        } catch (\Exception $e) {
            app(AlertService::class)->sendAlert('redis_connection_failed', 
                "Redis connection failed", 
                ['error' => $e->getMessage()]
            );
        }
    }

    private function checkCacheSize(): void
    {
        $monitor = new PerformanceMonitor();
        $metrics = $monitor->getRedisMetrics();
        $maxMemory = $metrics['memory']['maxmemory'];
        $usedMemory = $metrics['memory']['used_memory'];

        if ($maxMemory > 0 && ($usedMemory / $maxMemory) > 0.9) {
            app(AlertService::class)->sendAlert('cache_size_exceeded', 
                "Cache size exceeded 90% of max memory", 
                ['used' => $usedMemory, 'max' => $maxMemory]
            );
        }
    }

    private function checkResponseTime(): void
    {
        $monitor = new ResponseTimeMonitor();
        $stats = $monitor->getResponseTimeStats();

        if ($stats['avg_response_time'] > 2.0) {
            app(AlertService::class)->sendAlert('slow_response_time', 
                "Average response time is above 2 seconds", 
                ['avg_time' => $stats['avg_response_time']]
            );
        }
    }
}
```

## 📈 Usage Examples

### Basic Monitoring

```bash
# Monitor performance
php artisan monitor:performance

# Monitor with alerts
php artisan monitor:performance --alert

# Export metrics
php artisan monitor:performance --export
```

### Programmatic Monitoring

```php
// Get performance metrics
$monitor = new PerformanceMonitor();
$metrics = $monitor->getRedisMetrics();

// Check hit rate
if ($metrics['stats']['hit_rate'] < 80) {
    // Take action
}

// Monitor cache performance
$monitor->monitorCachePerformance();
```

### Custom Alerts

```php
// Create custom alert
app(AlertService::class)->sendAlert('custom_alert', 
    'Custom performance issue detected', 
    ['metric' => 'value']
);
```

## 🔧 Configuration

### Environment Variables

```env
# Performance Monitoring
MONITORING_ENABLED=true
MONITORING_ALERT_EMAIL=admin@example.com
MONITORING_WEBHOOK_URL=https://hooks.slack.com/...
MONITORING_SLACK_WEBHOOK=https://hooks.slack.com/...

# Alert Thresholds
MONITORING_HIT_RATE_THRESHOLD=80
MONITORING_MEMORY_THRESHOLD=85
MONITORING_RESPONSE_TIME_THRESHOLD=2.0
```

### Monitoring Configuration

```php
// config/monitoring.php
return [
    'enabled' => env('MONITORING_ENABLED', true),
    'alert_email' => env('MONITORING_ALERT_EMAIL'),
    'webhook_url' => env('MONITORING_WEBHOOK_URL'),
    'slack_webhook' => env('MONITORING_SLACK_WEBHOOK'),
    'thresholds' => [
        'hit_rate' => env('MONITORING_HIT_RATE_THRESHOLD', 80),
        'memory_usage' => env('MONITORING_MEMORY_THRESHOLD', 85),
        'response_time' => env('MONITORING_RESPONSE_TIME_THRESHOLD', 2.0),
    ],
];
```

## 🛠️ Troubleshooting

### Common Issues

#### **Redis Connection Issues**
```bash
# Check Redis connection
php artisan tinker
>>> Cache::store('redis')->ping()

# Check Redis configuration
redis-cli ping

# Check Redis logs
tail -f /var/log/redis/redis-server.log
```

#### **Low Cache Hit Rate**
```bash
# Check cache keys
redis-cli keys "*"

# Check cache statistics
redis-cli info stats

# Monitor Redis in real-time
redis-cli monitor
```

#### **High Memory Usage**
```bash
# Check memory usage
redis-cli info memory

# Check largest keys
redis-cli --bigkeys

# Clean up expired keys
redis-cli --scan --pattern "*" | xargs redis-cli del
```

### Debug Tools

```php
// Enable debug mode
// config/monitoring.php
'debug' => env('APP_DEBUG', false),

// In your monitoring service
if (config('monitoring.debug')) {
    logger()->debug('Monitoring data', $data);
}
```

## 📚 Best Practices

### **Performance Monitoring**
1. **Monitor key metrics**: Hit rate, memory usage, response times
2. **Set appropriate thresholds**: Based on your application needs
3. **Alert on issues**: Get notified before problems become critical
4. **Regular checks**: Schedule automated monitoring

### **Cache Management**
1. **Clear cache regularly**: Prevent stale data
2. **Monitor cache size**: Avoid memory issues
3. **Optimize cache keys**: Use efficient naming
4. **Use appropriate TTL**: Balance freshness and performance

### **Alert Management**
1. **Don't alert too frequently**: Use rate limiting
2. **Provide context**: Include relevant data in alerts
3. **Use multiple channels**: Email, Slack, webhook
4. **Test alerts**: Ensure they work when needed

## 🎯 Advanced Features

### **Custom Metrics**

```php
// Add custom metrics
class CustomMetrics
{
    public function recordCustomMetric(string $name, float $value): void
    {
        $key = 'custom_metrics:' . date('Y-m-d');
        $data = Cache::get($key, []);
        
        $data[$name] = [
            'value' => $value,
            'timestamp' => now(),
        ];
        
        Cache::put($key, $data, now()->addDays(7));
    }
}
```

### **Performance Profiling**

```php
// Profile specific operations
class PerformanceProfiler
{
    public function profile(string $operation, callable $callback): mixed
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        $result = $callback();
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        $this->recordProfile($operation, [
            'duration' => $endTime - $startTime,
            'memory_used' => $endMemory - $startMemory,
            'timestamp' => now(),
        ]);
        
        return $result;
    }
}
```

## 📞 Support

For performance monitoring issues:
1. Check the **Performance Monitoring** page in Filament
2. Review the **Command-line tools** output
3. Check Redis logs and configuration
4. Monitor system resources

---

*Last updated: March 7, 2025*
