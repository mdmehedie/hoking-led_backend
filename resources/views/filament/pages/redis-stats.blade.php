<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Connection Status -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-filament::card>
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-green-100 rounded-lg dark:bg-green-900">
                        <x-filament::icon 
                            icon="heroicon-o-server" 
                            class="w-6 h-6 text-green-600 dark:text-green-400"
                        />
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Redis Status</div>
                        <div class="text-lg font-semibold">
                            @if(!empty($this->getRedisInfo()))
                                <span class="text-green-600 dark:text-green-400">Connected</span>
                            @else
                                <span class="text-red-600 dark:text-red-400">Disconnected</span>
                            @endif
                        </div>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 rounded-lg dark:bg-blue-900">
                        <x-filament::icon 
                            icon="heroicon-o-cpu-chip" 
                            class="w-6 h-6 text-blue-600 dark:text-blue-400"
                        />
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Operations/Sec</div>
                        <div class="text-lg font-semibold">{{ $this->getStats()['instantaneous_ops_per_sec'] ?? '0' }}</div>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-purple-100 rounded-lg dark:bg-purple-900">
                        <x-filament::icon 
                            icon="heroicon-o-key" 
                            class="w-6 h-6 text-purple-600 dark:text-purple-400"
                        />
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Keys</div>
                        <div class="text-lg font-semibold">{{ $this->getStats()['total_keys'] ?? '0' }}</div>
                    </div>
                </div>
            </x-filament::card>
        </div>

        <!-- Memory Usage -->
        <x-filament::card>
            <x-filament::card.header>
                <h3 class="text-lg font-medium">Memory Usage</h3>
            </x-filament::card.header>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Used Memory</div>
                    <div class="text-lg font-semibold">{{ $this->getMemoryUsage()['used_human'] ?? '0B' }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Peak Memory</div>
                    <div class="text-lg font-semibold">{{ $this->getMemoryUsage()['used_peak_human'] ?? '0B' }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Max Memory</div>
                    <div class="text-lg font-semibold">{{ $this->getMemoryUsage()['maxmemory_human'] ?? 'Unlimited' }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Usage %</div>
                    <div class="text-lg font-semibold">{{ $this->getMemoryUsage()['used_percentage'] ?? '0' }}%</div>
                </div>
            </div>
        </x-filament::card>

        <!-- Performance Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-filament::card>
                <x-filament::card.header>
                    <h3 class="text-lg font-medium">Performance Statistics</h3>
                </x-filament::card.header>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Cache Hit Rate</span>
                        <span class="text-sm font-semibold">{{ $this->getStats()['hit_rate'] ?? '0%' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Keyspace Hits</span>
                        <span class="text-sm font-semibold">{{ $this->getStats()['keyspace_hits'] ?? '0' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Keyspace Misses</span>
                        <span class="text-sm font-semibold">{{ $this->getStats()['keyspace_misses'] ?? '0' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Expired Keys</span>
                        <span class="text-sm font-semibold">{{ $this->getStats()['expired_keys'] ?? '0' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Evicted Keys</span>
                        <span class="text-sm font-semibold">{{ $this->getStats()['evicted_keys'] ?? '0' }}</span>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <x-filament::card.header>
                    <h3 class="text-lg font-medium">Connection Statistics</h3>
                </x-filament::card.header>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Connections</span>
                        <span class="text-sm font-semibold">{{ $this->getStats()['total_connections_received'] ?? '0' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Commands</span>
                        <span class="text-sm font-semibold">{{ $this->getStats()['total_commands_processed'] ?? '0' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Network Input</span>
                        <span class="text-sm font-semibold">{{ $this->getStats()['total_net_input_bytes'] ?? '0B' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Network Output</span>
                        <span class="text-sm font-semibold">{{ $this->getStats()['total_net_output_bytes'] ?? '0B' }}</span>
                    </div>
                </div>
            </x-filament::card>
        </div>

        <!-- Database Information -->
        <x-filament::card>
            <x-filament::card.header>
                <h3 class="text-lg font-medium">Database Information</h3>
            </x-filament::card.header>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Database</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keys</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expires</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Avg TTL</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getDatabaseInfo() as $db => $info)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $db }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ number_format($info['keys']) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ number_format($info['expires']) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $info['avg_ttl_human'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                @if(empty($this->getDatabaseInfo()))
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        No database information available
                    </div>
                @endif
            </div>
        </x-filament::card>

        <!-- Cache Configuration -->
        <x-filament::card>
            <x-filament::card.header>
                <h3 class="text-lg font-medium">Cache Configuration</h3>
            </x-filament::card.header>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Cache Driver</div>
                    <div class="text-sm font-semibold">{{ $this->getCacheStats()['driver'] ?? 'Unknown' }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Cache Prefix</div>
                    <div class="text-sm font-semibold">{{ $this->getCacheStats()['prefix'] ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Redis Connection</div>
                    <div class="text-sm font-semibold">{{ $this->getCacheStats()['connection'] ?? 'N/A' }}</div>
                </div>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>
