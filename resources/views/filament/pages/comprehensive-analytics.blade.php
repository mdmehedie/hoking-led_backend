<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button onclick="showTab('traffic')" id="traffic-tab" class="tab-button active py-2 px-1 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600">
                    Traffic Analytics
                </button>
                <button onclick="showTab('behavior')" id="behavior-tab" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Behavior Analysis
                </button>
                <button onclick="showTab('performance')" id="performance-tab" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Performance Monitoring
                </button>
            </nav>
        </div>

        <!-- Traffic Tab -->
        <div id="traffic-content" class="tab-content">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Sessions Overview -->
                <x-filament::widget>
                    <x-filament::widget::header>
                        <h3 class="font-semibold text-gray-900">Sessions Overview</h3>
                    </x-filament::widget::header>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Sessions</span>
                            <span class="text-lg font-semibold">{{ number_format($this->getTrafficData()['sessions']['sessions'] ?? 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Bounce Rate</span>
                            <span class="text-lg font-semibold">{{ $this->getTrafficData()['sessions']['bounce_rate'] ?? 0 }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Engagement Rate</span>
                            <span class="text-lg font-semibold">{{ $this->getTrafficData()['sessions']['engagement_rate'] ?? 0 }}%</span>
                        </div>
                    </div>
                </x-filament::widget>

                <!-- Device Breakdown -->
                <x-filament::widget>
                    <x-filament::widget::header>
                        <h3 class="font-semibold text-gray-900">Device Breakdown</h3>
                    </x-filament::widget::header>
                    <div class="space-y-2">
                        @forelse ($this->getTrafficData()['devices'] as $device)
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-900">{{ ucfirst($device['device']) }}</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $device['sessions'] / ($this->getTrafficData()['sessions']['sessions'] ?? 1) * 100 }}%"></div>
                                    </div>
                                    <span class="ml-2 text-sm font-medium">{{ number_format($device['sessions']) }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No device data available</p>
                        @endforelse
                    </div>
                </x-filament::widget>

                <!-- Traffic Sources -->
                <x-filament::widget>
                    <x-filament::widget::header>
                        <h3 class="font-semibold text-gray-900">Traffic Sources</h3>
                    </x-filament::widget::header>
                    <div class="space-y-2">
                        @forelse ($this->getTrafficData()['sources'] as $source)
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-900 truncate">{{ $source['source'] }}</span>
                                <span class="text-sm font-medium">{{ number_format($source['sessions']) }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No traffic source data available</p>
                        @endforelse
                    </div>
                </x-filament::widget>
            </div>

            <!-- Top Pages -->
            <x-filament::widget class="col-span-12">
                <x-filament::widget::header>
                    <h3 class="font-semibold text-gray-900">Top Pages (Last 7 Days)</h3>
                </x-filament::widget::header>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($this->getTrafficData()['top_pages'] as $page)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $page['page'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($page['page_views']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">No page data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::widget>
        </div>

        <!-- Behavior Tab -->
        <div id="behavior-content" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Custom Events -->
                <x-filament::widget>
                    <x-filament::widget::header>
                        <h3 class="font-semibold text-gray-900">Custom Events</h3>
                    </x-filament::widget::header>
                    <div class="space-y-2">
                        @forelse ($this->getBehaviorData()['events'] as $event)
                            <div class="border-b border-gray-200 py-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $event['event_name'] }}</span>
                                    <span class="text-sm text-gray-500">{{ number_format($event['total_events']) }} events</span>
                                </div>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-xs text-gray-500">{{ number_format($event['unique_users']) }} unique users</span>
                                    @if($event['avg_duration'])
                                        <span class="text-xs text-gray-500">{{ round($event['avg_duration'], 2) }s avg duration</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No custom events tracked</p>
                        @endforelse
                    </div>
                </x-filament::widget>

                <!-- User Paths -->
                <x-filament::widget>
                    <x-filament::widget::header>
                        <h3 class="font-semibold text-gray-900">Popular User Paths</h3>
                    </x-filament::widget::header>
                    <div class="space-y-2">
                        @forelse ($this->getBehaviorData()['user_paths'] as $path)
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-900 truncate">{{ $path['path'] }}</span>
                                <span class="text-sm text-gray-500">{{ number_format($path['unique_users']) }} users</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No user path data available</p>
                        @endforelse
                    </div>
                </x-filament::widget>
            </div>

            <!-- Funnel Analysis -->
            <x-filament::widget class="col-span-12">
                <x-filament::widget::header>
                    <h3 class="font-semibold text-gray-900">Conversion Funnel</h3>
                </x-filament::widget::header>
                <div class="space-y-6">
                    @foreach ($this->getBehaviorData()['funnels'] as $funnel)
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-4">{{ $funnel['name'] }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                @foreach ($funnel['conversion_rates'] as $step)
                                    <div class="text-center p-4 border border-gray-200 rounded-lg">
                                        <div class="text-2xl font-bold text-gray-900">{{ $step['rate'] }}%</div>
                                        <div class="text-sm text-gray-600">{{ $step['label'] }}</div>
                                        <div class="text-xs text-gray-500 mt-1">Step {{ $step['step'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::widget>
        </div>

        <!-- Performance Tab -->
        <div id="performance-content" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Core Web Vitals -->
                <x-filament::widget>
                    <x-filament::widget::header>
                        <h3 class="font-semibold text-gray-900">Core Web Vitals (Last 7 Days)</h3>
                    </x-filament::widget::header>
                    <div class="space-y-4">
                        <!-- LCP -->
                        <div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">LCP (Largest Contentful Paint)</span>
                                <span class="text-sm font-medium">{{ round($this->getPerformanceData()['core_web_vitals']['lcp']['average'] ?? 0, 2) }}ms</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ $this->getPerformanceData()['core_web_vitals']['lcp']['count'] ?? 0 }} samples</div>
                        </div>

                        <!-- CLS -->
                        <div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">CLS (Cumulative Layout Shift)</span>
                                <span class="text-sm font-medium">{{ round($this->getPerformanceData()['core_web_vitals']['cls']['average'] ?? 0, 3) }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ $this->getPerformanceData()['core_web_vitals']['cls']['count'] ?? 0 }} samples</div>
                        </div>

                        <!-- INP -->
                        <div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">INP (Interaction to Next Paint)</span>
                                <span class="text-sm font-medium">{{ round($this->getPerformanceData()['core_web_vitals']['inp']['average'] ?? 0, 2) }}ms</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ $this->getPerformanceData()['core_web_vitals']['inp']['count'] ?? 0 }} samples</div>
                        </div>
                    </div>
                </x-filament::widget>

                <!-- Performance Trends -->
                <x-filament::widget>
                    <x-filament::widget::header>
                        <h3 class="font-semibold text-gray-900">Performance Trends</h3>
                    </x-filament::widget::header>
                    <div class="space-y-3">
                        @foreach ($this->getPerformanceData()['performance_trends'] as $metric => $trend)
                            <div class="border-b border-gray-200 pb-3">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-900">{{ ucfirst($metric) }}</span>
                                    <span class="text-sm text-gray-600">
                                        Current: {{ round($trend['current_avg'], 2) }}
                                        @if($trend['trend'] !== 'stable')
                                            <span class="ml-2 {{ $trend['trend'] === 'improving' ? 'text-green-600' : 'text-red-600' }}">
                                                ({{ $trend['trend'] }})
                                            </span>
                                        @endif
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500">
                                    Previous: {{ round($trend['previous_avg'], 2) }} | 
                                    Samples: {{ $trend['daily_data'][0]['count'] ?? 0 }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-filament::widget>
            </div>

            <!-- PageSpeed Data -->
            @if (!empty($this->getPerformanceData()['pagespeed_data']))
                <x-filament::widget class="col-span-12">
                    <x-filament::widget::header>
                        <h3 class="font-semibold text-gray-900">PageSpeed Analysis</h3>
                    </x-filament::widget::header>
                    <div class="space-y-4">
                        @foreach ($this->getPerformanceData()['pagespeed_data'] as $urlData)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-2">{{ $urlData['url'] }}</h4>
                                
                                @if (isset($urlData['data']['pagespeed']))
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-gray-900">
                                                {{ round($urlData['data']['pagespeed']['overall_score']) }}
                                            </div>
                                            <div class="text-sm text-gray-600">Performance Score</div>
                                        </div>
                                        
                                        @foreach ($urlData['data']['pagespeed']['core_web_vitals'] as $vital => $data)
                                            <div class="text-center">
                                                <div class="text-lg font-semibold text-gray-900">{{ $data['value'] }}</div>
                                                <div class="text-sm text-gray-600">{{ ucfirst($vital) }}</div>
                                                <div class="text-xs text-gray-500 capitalize">{{ $data['rating'] }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-filament::widget>
            @endif
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active state from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-indigo-500', 'text-indigo-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-content').classList.remove('hidden');
            
            // Add active state to selected tab
            const activeTab = document.getElementById(tabName + '-tab');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-indigo-500', 'text-indigo-600');
        }
    </script>

    <style>
        .tab-button {
            transition: all 0.2s ease-in-out;
        }
        
        .tab-content {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</x-filament-panels::page>
