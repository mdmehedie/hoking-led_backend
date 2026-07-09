<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class TableBuilder extends Field
{
    protected string $view = 'filament.forms.components.table-builder';

    protected int $initialRows = 3;
    protected int $initialColumns = 2;

    public function initialRows(int $rows): static
    {
        $this->initialRows = $rows;
        return $this;
    }

    public function initialColumns(int $columns): static
    {
        $this->initialColumns = $columns;
        return $this;
    }

    public function getInitialRows(): int
    {
        return $this->initialRows;
    }

    public function getInitialColumns(): int
    {
        return $this->initialColumns;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(function () {
            return [
                'rows' => $this->initialRows,
                'columns' => $this->initialColumns,
                'cells' => [],
            ];
        });

        // Transform key-value format to table format on load
        $this->formatStateUsing(function ($state) {
            if (empty($state)) {
                return [
                    'rows' => $this->initialRows,
                    'columns' => $this->initialColumns,
                    'cells' => [],
                ];
            }

            // If already in table format with cells array, rebuild with IDs
            if (isset($state['rows']) && isset($state['columns']) && isset($state['cells'])) {
                $rebuiltCells = [];
                
                // Check if any cell in the state has is_header set to true
                $hasHeader = false;
                foreach ($state['cells'] as $row) {
                    foreach ($row as $cell) {
                        if (isset($cell['is_header']) && $cell['is_header'] === true) {
                            $hasHeader = true;
                            break 2;
                        }
                    }
                }
                
                // If it is legacy data and has no header row flagged, insert an empty header row at row index 0
                if (!$hasHeader && !empty($state['cells'])) {
                    $headerRow = [];
                    $columnCount = (int) $state['columns'];
                    for ($c = 0; $c < $columnCount; $c++) {
                        $headerRow[] = [
                            'id' => 'cell-header-' . $c . '-' . time(),
                            'rowIndex' => 0,
                            'colIndex' => $c,
                            'value' => '',
                            'merged' => false,
                            'colspan' => 1,
                            'rowspan' => 1,
                            'selected' => false,
                            'is_header' => true,
                        ];
                    }
                    $rebuiltCells[] = $headerRow;
                    
                    // Append the legacy rows shifting index by 1 and marking is_header = false
                    foreach ($state['cells'] as $rowIndex => $row) {
                        $rebuiltRow = [];
                        foreach ($row as $cellIndex => $cell) {
                            $rebuiltRow[] = [
                                'id' => 'cell-' . ($rowIndex + 1) . '-' . $cellIndex . '-' . time(),
                                'rowIndex' => $rowIndex + 1,
                                'colIndex' => (int) ($cell['colIndex'] ?? $cellIndex),
                                'value' => $cell['value'] ?? '',
                                'merged' => false,
                                'colspan' => (int) ($cell['colspan'] ?? 1),
                                'rowspan' => (int) ($cell['rowspan'] ?? 1),
                                'selected' => false,
                                'is_header' => false,
                            ];
                        }
                        $rebuiltCells[] = $rebuiltRow;
                    }
                    
                    return [
                        'rows' => (int) $state['rows'] + 1,
                        'columns' => (int) $state['columns'],
                        'cells' => $rebuiltCells,
                    ];
                }
                
                // Normal rebuild if header is already present
                foreach ($state['cells'] as $rowIndex => $row) {
                    $rebuiltRow = [];
                    foreach ($row as $cellIndex => $cell) {
                        $rebuiltRow[] = [
                            'id' => 'cell-' . $rowIndex . '-' . $cellIndex . '-' . time(),
                            'rowIndex' => (int) $rowIndex,
                            'colIndex' => (int) ($cell['colIndex'] ?? $cellIndex),
                            'value' => $cell['value'] ?? '',
                            'merged' => false,
                            'colspan' => (int) ($cell['colspan'] ?? 1),
                            'rowspan' => (int) ($cell['rowspan'] ?? 1),
                            'selected' => false,
                            'is_header' => (bool) ($cell['is_header'] ?? ($rowIndex === 0)),
                        ];
                    }
                    $rebuiltCells[] = $rebuiltRow;
                }
                
                return [
                    'rows' => (int) $state['rows'],
                    'columns' => (int) $state['columns'],
                    'cells' => $rebuiltCells,
                ];
            }

            // Transform from key-value format to table format (legacy support)
            if (is_array($state) && isset($state[0]) && isset($state[0]['key'])) {
                $rows = count($state);
                $columns = 2;
                $cells = [];

                // Insert empty header row first
                $headerRow = [];
                for ($c = 0; $c < $columns; $c++) {
                    $headerRow[] = [
                        'id' => 'cell-header-0-' . $c . '-' . time(),
                        'rowIndex' => 0,
                        'colIndex' => $c,
                        'value' => '',
                        'merged' => false,
                        'colspan' => 1,
                        'rowspan' => 1,
                        'selected' => false,
                        'is_header' => true,
                    ];
                }
                $cells[] = $headerRow;

                // Push key-value items down by shifting index by 1
                foreach ($state as $rowIndex => $spec) {
                    $rowCells = [];
                    $rowCells[] = [
                        'id' => 'cell-' . ($rowIndex + 1) . '-0-' . time(),
                        'rowIndex' => $rowIndex + 1,
                        'colIndex' => 0,
                        'value' => $spec['key'] ?? '',
                        'merged' => false,
                        'colspan' => 1,
                        'rowspan' => 1,
                        'selected' => false,
                        'is_header' => false,
                    ];
                    $values = $spec['values'] ?? [];
                    $rowCells[] = [
                        'id' => 'cell-' . ($rowIndex + 1) . '-1-' . time(),
                        'rowIndex' => $rowIndex + 1,
                        'colIndex' => 1,
                        'value' => is_array($values) ? implode(', ', $values) : ($values ?? ''),
                        'merged' => false,
                        'colspan' => 1,
                        'rowspan' => 1,
                        'selected' => false,
                        'is_header' => false,
                    ];
                    $cells[] = $rowCells;
                }

                return [
                    'rows' => $rows + 1,
                    'columns' => $columns,
                    'cells' => $cells,
                ];
            }

            return $state;
        });

        // Store complete table state with metadata - no transformation
        $this->dehydrateStateUsing(function ($state) {
            if (empty($state)) {
                return [
                    'rows' => $this->initialRows,
                    'columns' => $this->initialColumns,
                    'cells' => [],
                ];
            }

            // Clean cell objects - keep only visible cells with minimal data
            $cleanCells = [];
            foreach ($state['cells'] ?? [] as $rowIndex => $row) {
                $cleanRow = [];
                $isRowFullyEmpty = true;
                foreach ($row as $cellIndex => $cell) {
                    // Skip merged cells (they're covered by colspan/rowspan of other cells)
                    if ($cell['merged'] ?? false) {
                        continue;
                    }
                    
                    $cellValue = trim($cell['value'] ?? '');
                    if ($cellValue !== '') {
                        $isRowFullyEmpty = false;
                    }
                    
                    $cleanRow[] = [
                        'colIndex' => (int) ($cell['colIndex'] ?? $cellIndex),
                        'value' => $cell['value'] ?? '',
                        'colspan' => (int) ($cell['colspan'] ?? 1),
                        'rowspan' => (int) ($cell['rowspan'] ?? 1),
                        'is_header' => (bool) ($cell['is_header'] ?? ($rowIndex === 0)),
                    ];
                }
                
                // Only store this row if it contains some values
                if (!$isRowFullyEmpty) {
                    $cleanCells[] = $cleanRow;
                }
            }

            return [
                'rows' => count($cleanCells),
                'columns' => (int) ($state['columns'] ?? $this->initialColumns),
                'cells' => $cleanCells,
            ];
        });
    }
}
