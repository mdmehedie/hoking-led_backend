<x-filament-panels::page
    x-data="{
        record: @js($record),
    }"
    class="filament-resource-view-record"
>
    <div class="space-y-6">
        <!-- Lead Information Section -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Lead Information
                </h3>
                <div class="space-y-3">
                    <table class="min-w-full">
                        <tbody>
                            <tr>
                                <td class="py-2 pr-4 text-sm font-medium text-gray-700 dark:text-gray-300">Lead ID : </td>
                                <td class="py-2 text-sm text-gray-900 dark:text-white font-mono">{{ $record->id }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4 text-sm font-medium text-gray-700 dark:text-gray-300">Form Name : </td>
                                <td class="py-2 text-sm text-gray-900 dark:text-white">{{ $record->form->name }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4 text-sm font-medium text-gray-700 dark:text-gray-300">Submitted At : </td>
                                <td class="py-2 text-sm text-gray-900 dark:text-white font-mono">{{ $record->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <br><br>

        <!-- Submitted Data Section -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Submitted Data
                </h3>
                <div>
                    <div class="overflow-x-auto">
                        <style>
                            .form-data-table {
                                border-collapse: separate;
                                border-spacing: 0;
                                border-radius: 8px;
                                overflow: hidden;
                                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                            }
                            .form-data-table thead th {
                                background: linear-gradient(135deg, {{ \App\Models\AppSetting::first()?->primary_color ?? '#667eea' }} 0%, {{ \App\Models\AppSetting::first()?->secondary_color ?? '#764ba2' }} 100%);
                                color: white;
                                font-weight: 600;
                                padding: 16px 24px;
                                text-align: left;
                                font-size: 0.875rem;
                                text-transform: uppercase;
                                letter-spacing: 0.05em;
                                border-bottom: none;
                            }
                            .form-data-table tbody tr {
                                transition: all 0.2s ease;
                            }
                            .form-data-table tbody tr:hover {
                                background-color: rgba(102, 126, 234, 0.05);
                                transform: translateY(-1px);
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                            }
                            .form-data-table tbody td {
                                padding: 16px 24px;
                                border-bottom: 1px solid #e5e7eb;
                                background-color: white;
                                color: #374151;
                            }
                            .dark .form-data-table tbody td {
                                background-color: #1f2937;
                                color: #f9fafb;
                                border-bottom: 1px solid #374151;
                            }
                            .form-data-table tbody td:first-child {
                                font-weight: 600;
                                color: #1f2937;
                            }
                            .dark .form-data-table tbody td:first-child {
                                color: #f3f4f6;
                            }
                            .form-data-table tbody td:nth-child(2) {
                                font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
                                background-color: #f8fafc;
                            }
                            .dark .form-data-table tbody td:nth-child(2) {
                                background-color: #111827;
                                font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
                            }
                        </style>
                        <table class="min-w-full form-data-table">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($record->data as $key => $value)
                                <tr>
                                    <td>{{ ucfirst(str_replace(['_', '-'], ' ', $key)) }}</td>
                                    <td>{{ is_array($value) || is_object($value) ? json_encode($value) : $value }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
