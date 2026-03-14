<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Stats Overview -->
        <x-filament::widget class="col-span-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($this->getStats() as $stat)
                    <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-600">{{ $stat->label }}</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stat->value }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $stat->description }}
                                    @if($stat->descriptionIcon)
                                        <span class="inline-flex items-center ml-1">
                                            <x-heroicon-m-{{ $stat->descriptionIcon }} class="w-3 h-3" />
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div class="ml-4">
                                <div class="w-12 h-12 bg-{{ $stat->color ?? 'primary' }}-100 rounded-full flex items-center justify-center">
                                    <span class="text-{{ $stat->color ?? 'primary' }}-600 font-semibold">
                                        {{ substr($stat->value, 0, 1) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::widget>

        <!-- Charts and Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Pages -->
            <x-filament::widget>
                <x-filament::widget::header>
                    <h3 class="font-semibold text-gray-900">Top Pages (Last 7 Days)</h3>
                </x-filament::widget::header>
                <div class="space-y-2">
                    @forelse ($this->getTopPages() as $page)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $page['page'] }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($page['page_views']) }} views</p>
                            </div>
                            <div class="text-sm font-semibold text-gray-700">
                                {{ number_format($page['page_views']) }}
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm py-4">No page data available</p>
                    @endforelse
                </div>
            </x-filament::widget>

            <!-- Device Breakdown -->
            <x-filament::widget>
                <x-filament::widget::header>
                    <h3 class="font-semibold text-gray-900">Device Breakdown (Last 7 Days)</h3>
                </x-filament::widget::header>
                <div class="space-y-2">
                    @forelse ($this->getDeviceBreakdown() as $device)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ ucfirst($device['device']) }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($device['sessions']) }} sessions</p>
                            </div>
                            <div class="text-sm font-semibold text-gray-700">
                                {{ number_format($device['sessions']) }}
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm py-4">No device data available</p>
                    @endforelse
                </div>
            </x-filament::widget>
        </div>

        <!-- Second Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Traffic Sources -->
            <x-filament::widget>
                <x-filament::widget::header>
                    <h3 class="font-semibold text-gray-900">Traffic Sources (Last 7 Days)</h3>
                </x-filament::widget::header>
                <div class="space-y-2">
                    @forelse ($this->getTrafficSources() as $source)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $source['source'] }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($source['sessions']) }} sessions</p>
                            </div>
                            <div class="text-sm font-semibold text-gray-700">
                                {{ number_format($source['sessions']) }}
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm py-4">No traffic source data available</p>
                    @endforelse
                </div>
            </x-filament::widget>

            <!-- Custom Events -->
            <x-filament::widget>
                <x-filament::widget::header>
                    <h3 class="font-semibold text-gray-900">Custom Events (Last 7 Days)</h3>
                </x-filament::widget::header>
                <div class="space-y-2">
                    @forelse ($this->getCustomEvents() as $event)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $event['event_name'] }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($event['unique_users']) }} users</p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-700">
                                    {{ number_format($event['count']) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ number_format($event['unique_users']) }} users
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm py-4">No custom events tracked</p>
                    @endforelse
                </div>
            </x-filament::widget>
        </div>

        <!-- Engagement Metrics -->
        <x-filament::widget class="col-span-12">
            <x-filament::widget::header>
                <h3 class="font-semibold text-gray-900">Engagement Metrics (Last 7 Days)</h3>
            </x-filament::widget::header>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Engagement Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->getEngagementRate()['engagement_rate'] }}%</p>
                    <p class="text-xs text-gray-500">Percentage of engaged sessions</p>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Avg Session Duration</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->getEngagementRate()['avg_session_duration'] }}s</p>
                    <p class="text-xs text-gray-500">Average time on site</p>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Total Events</p>
                    <p class="text-2xl font-bold text-gray-900">{{ count($this->getCustomEvents()) }}</p>
                    <p class="text-xs text-gray-500">Custom events tracked</p>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Active Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $this->getStats()[2]->value }}</p>
                    <p class="text-xs text-gray-500">Users in last 7 days</p>
                </div>
            </div>
        </x-filament::widget>
    </div>
</x-filament-panels::page>
