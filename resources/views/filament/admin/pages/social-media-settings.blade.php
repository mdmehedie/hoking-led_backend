<x-filament-panels::page>
<div class="space-y-6">
    <div class="filament-page-content">
        <div class="grid gap-6">
            <!-- Social Media Accounts Table -->
            <div class="filament-resources-table-widget">
                <div class="filament-card rounded-lg border border-gray-300 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="filament-card-body p-6">
                        {{ $this->table }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-filament-panels::page>
