<x-filament-panels::page>
    <form wire:submit="clearCache">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="clearCache"
            >
                <x-filament::loading-indicator wire:loading wire:target="clearCache" class="mr-2" />
                Clear Selected Cache
            </x-filament::button>
        </div>
    </form>

    {{-- Cache Statistics --}}
    <div class="mt-8">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Cache Statistics
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($this->getCacheStatistics() as $key => $value)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ Str::title(str_replace('_', ' ', $key)) }}
                    </div>
                    <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $value }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Cache Operations Results --}}
    <div x-data="{ showResults: false }" x-init="
        $wire.on('cache-cleared', (event) => {
            showResults = true;
        })
    " x-show="showResults" class="mt-8">
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <h4 class="text-green-800 dark:text-green-200 font-medium mb-2">
                Cache Operations Results
            </h4>
            <div class="text-green-700 dark:text-green-300 text-sm space-y-1">
                <!-- Results will be shown here -->
            </div>
        </div>
    </div>

    {{-- Performance Tips --}}
    <div class="mt-8">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Performance Tips
        </h3>
        
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                <li class="flex items-start">
                    <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Clear cache after deploying new code or updating configurations</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Use Redis for better performance in production environments</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Response cache is automatically cleared when content is updated</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Monitor Redis memory usage to prevent performance issues</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- Cache Configuration --}}
    <div class="mt-8">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Current Configuration
        </h3>
        
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cache Driver</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ config('cache.default') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Response Cache</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                        {{ config('responsecache.enabled') ? 'Enabled' : 'Disabled' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cache Lifetime</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                        {{ config('responsecache.cache_lifetime_in_seconds') }} seconds
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Environment</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ app()->environment() }}</dd>
                </div>
            </dl>
        </div>
    </div>
</x-filament-panels::page>
