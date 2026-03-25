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

                foreach ($state as $rowIndex => $spec) {
                    $rowCells = [];
                    $rowCells[] = [
                        'id' => 'cell-' . $rowIndex . '-0-' . time(),
                        'rowIndex' => $rowIndex,
                        'colIndex' => 0,
                        'value' => $spec['key'] ?? '',
                        'merged' => false,
                        'colspan' => 1,
                        'rowspan' => 1,
                        'selected' => false,
                    ];
                    $values = $spec['values'] ?? [];
                    $rowCells[] = [
                        'id' => 'cell-' . $rowIndex . '-1-' . time(),
                        'rowIndex' => $rowIndex,
                        'colIndex' => 1,
                        'value' => is_array($values) ? implode(', ', $values) : ($values ?? ''),
                        'merged' => false,
                        'colspan' => 1,
                        'rowspan' => 1,
                        'selected' => false,
                    ];
                    $cells[] = $rowCells;
                }

                return [
                    'rows' => $rows,
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
                foreach ($row as $cellIndex => $cell) {
                    // Skip merged cells (they're covered by colspan/rowspan of other cells)
                    if ($cell['merged'] ?? false) {
                        continue;
                    }
                    
                    $cleanRow[] = [
                        'colIndex' => (int) ($cell['colIndex'] ?? $cellIndex),
                        'value' => $cell['value'] ?? '',
                        'colspan' => (int) ($cell['colspan'] ?? 1),
                        'rowspan' => (int) ($cell['rowspan'] ?? 1),
                    ];
                }
                $cleanCells[] = $cleanRow;
            }

            return [
                'rows' => (int) ($state['rows'] ?? $this->initialRows),
                'columns' => (int) ($state['columns'] ?? $this->initialColumns),
                'cells' => $cleanCells,
            ];
        });
    }
}
