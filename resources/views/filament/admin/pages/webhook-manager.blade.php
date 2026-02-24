<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Quick Actions & Info -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-lg p-6 border border-blue-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Webhook Manager
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                        Manage webhooks to send form data to external services like CRMs and automation tools.
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="text-right">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Queue Status</div>
                        <div class="text-xs text-green-600 dark:text-green-400 font-medium">Active</div>
                    </div>
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                </div>
            </div>

            <!-- Quick Command Reference -->
            <div class="mt-4 p-3 bg-white dark:bg-gray-800 rounded-md border">
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Quick Command:</div>
                <code class="text-sm text-gray-900 dark:text-white font-mono">
                    php artisan webhook:add {form_id} {url}
                </code>
            </div>
        </div>

        <!-- Create Webhook Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Add New Webhook
                    </h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                        Form Required
                    </span>
                </div>

                <form wire:submit.prevent="create" class="space-y-6">
                    {{ $this->form }}

                    <div class="flex justify-end space-x-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <x-filament::button
                            type="submit"
                            color="primary"
                            wire:loading.attr="disabled"
                        >
                            <x-filament::loading-indicator wire:loading class="w-4 h-4 mr-2" />
                            Create Webhook
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Webhooks Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        All Webhooks
                    </h3>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $this->getTableRecords()->count() }} webhook(s)
                    </div>
                </div>

                {{ $this->table }}
            </div>
        </div>

        <!-- Usage Tips -->
        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-4 border border-amber-200 dark:border-amber-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">
                        Important: Start Queue Worker
                    </h3>
                    <div class="mt-2 text-sm text-amber-700 dark:text-amber-300">
                        <p>Webhooks require a queue worker to process requests. Run this command in your terminal:</p>
                        <code class="block mt-1 p-2 bg-amber-100 dark:bg-amber-800 rounded text-xs font-mono">
                            php artisan queue:work --tries=3 --backoff=60,300,900
                        </code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
