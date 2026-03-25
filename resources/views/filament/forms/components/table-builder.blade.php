@php
    $initialRows = $getInitialRows();
    $initialColumns = $getInitialColumns();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="tableBuilderFormComponent({
            state: $wire.$entangle('{{ $statePath }}'),
            initialRows: {{ $initialRows }},
            initialColumns: {{ $initialColumns }},
        })"
        x-init="init()"
        class="fi-fo-table-builder"
        style="display: grid; gap: 1rem;"
    >
        <!-- Toolbar -->
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.75rem; border: 1px solid var(--gray-300); border-radius: 0.5rem;" class="dark:!border-gray-600">
            <div style="display: flex; gap: 0.5rem;">
                <button
                    type="button"
                    x-on:click="addRow()"
                    class="fi-btn fi-btn-size-sm fi-color-primary"
                >
                    <span class="fi-btn-label" style="display: flex; align-items: center; gap: 0.375rem;">
                        <svg class="fi-icon fi-size-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Row
                    </span>
                </button>
                <button
                    type="button"
                    x-on:click="addColumn()"
                    class="fi-btn fi-btn-size-sm fi-btn-outlined"
                >
                    <span class="fi-btn-label" style="display: flex; align-items: center; gap: 0.375rem;">
                        <svg class="fi-icon fi-size-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Column
                    </span>
                </button>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button
                    type="button"
                    x-on:click="unmergeSelectedCells()"
                    class="fi-btn fi-btn-size-sm fi-btn-outlined"
                    x-show="mergeMode"
                    :disabled="!hasMergedCell()"
                    style="opacity: hasMergedCell() ? 1 : 0.5; cursor: hasMergedCell() ? 'pointer' : 'not-allowed';"
                >
                    <span class="fi-btn-label" style="display: flex; align-items: center; gap: 0.375rem;">
                        <svg class="fi-icon fi-size-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                        </svg>
                        Unmerge
                    </span>
                </button>
                <button
                    type="button"
                    x-on:click="toggleMergeMode()"
                    class="fi-btn fi-btn-size-sm"
                    :class="mergeMode ? 'fi-color-warning' : 'fi-btn-outlined'"
                >
                    <span class="fi-btn-label" style="display: flex; align-items: center; gap: 0.375rem;">
                        <svg class="fi-icon fi-size-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span x-text="mergeMode ? 'Exit Merge Mode' : 'Merge Cells'"></span>
                    </span>
                </button>
            </div>
        </div>

        <!-- Merge mode hint -->
        <div
            x-show="mergeMode"
            x-transition
            style="padding: 1rem; border: 1px solid var(--warning-300); border-radius: 0.5rem; color: var(--warning-800);"
            class="dark:!border-warning-700 dark:!text-warning-300"
        >
            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                <svg class="fi-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem; margin-top: 0.125rem; flex-shrink: 0;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <p style="margin: 0; font-size: 0.875rem;">Click on 2 or more adjacent cells in the same row to merge them.</p>
                    <p style="margin: 0; font-size: 0.875rem; opacity: 0.8;">Or click on a merged cell and use the "Unmerge" button to split it.</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div style="overflow-x: auto; border: 1px solid var(--gray-300); border-radius: 0.5rem;" class="dark:!border-gray-600">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <template x-for="colIndex in getVisibleColumnCount()" :key="'header-' + colIndex">
                            <th style="border-bottom: 1px solid var(--gray-300); border-right: 1px solid var(--gray-300); padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--gray-700);" class="dark:!border-gray-600 dark:!text-gray-300">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                    <span x-text="'Column ' + colIndex"></span>
                                    <button
                                        type="button"
                                        x-on:click="removeColumn(colIndex)"
                                        x-show="getVisibleColumnCount() > 2"
                                        class="fi-icon-btn"
                                        title="Remove column"
                                        style="padding: 0.25rem; background: none; border: none; cursor: pointer;"
                                    >
                                        <svg class="fi-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1rem; height: 1rem;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </th>
                        </template>
                        <th style="width: 3rem; border-bottom: 1px solid var(--gray-300); padding: 0.75rem 1rem;" class="dark:!border-gray-600"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, rowIndex) in getRows()" :key="'row-' + rowIndex">
                        <tr>
                            <template x-for="cell in row.cells" :key="'cell-' + cell.id">
                                <td
                                    x-show="!cell.merged"
                                    :colspan="cell.colspan || 1"
                                    :rowspan="cell.rowspan || 1"
                                    style="border-right: 1px solid var(--gray-300); padding: 0.75rem 1rem; vertical-align: top;"
                                    class="dark:!border-gray-600"
                                    :class="[
                                        mergeMode ? 'cursor-pointer' : '',
                                        cell.selected ? 'fi-active' : ''
                                    ]"
                                    @click="handleCellClick(rowIndex, cell)"
                                >
                                    <textarea
                                        x-model="cell.value"
                                        @change="updateState()"
                                        class="fi-input block"
                                        style="width: 100%; resize: none; border: 1px solid var(--gray-300); border-radius: 0.375rem; padding: 0.5rem 0.75rem; font-size: 0.875rem;"
                                        rows="2"
                                        placeholder="Enter value..."
                                    ></textarea>
                                </td>
                            </template>
                            <td style="border-right: 1px solid var(--gray-300); padding: 0.75rem 1rem; vertical-align: middle;" class="dark:!border-gray-600">
                                <button
                                    type="button"
                                    x-on:click="removeRow(rowIndex)"
                                    class="fi-icon-btn"
                                    title="Remove row"
                                >
                                    <svg class="fi-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Stats -->
        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; color: var(--gray-500);" class="dark:!text-gray-400">
            <span x-text="'Rows: ' + state.rows + ' | Columns: ' + state.columns"></span>
            <span x-show="mergeMode && selectedCells.length > 0" style="font-weight: 500;" class="fi-color-primary" x-text="selectedCells.length + ' cells selected'"></span>
        </div>
    </div>
</x-dynamic-component>

<script>
function tableBuilderFormComponent({ state, initialRows, initialColumns }) {
    return {
        state: state,
        mergeMode: false,
        selectedCells: [],

        init() {
            if (!this.state.rows || !this.state.columns || !this.state.cells) {
                this.initializeDefaultState();
            }
            this.ensureCellsMatchDimensions();
        },

        initializeDefaultState() {
            this.state = {
                rows: initialRows,
                columns: initialColumns,
                cells: [],
            };

            for (let r = 0; r < initialRows; r++) {
                let rowCells = [];
                for (let c = 0; c < initialColumns; c++) {
                    rowCells.push(this.createCell(r, c));
                }
                this.state.cells.push(rowCells);
            }
        },

        createCell(rowIndex, colIndex) {
            return {
                id: 'cell-' + rowIndex + '-' + colIndex + '-' + Date.now(),
                rowIndex: rowIndex,
                colIndex: colIndex,
                value: '',
                merged: false,
                colspan: 1,
                rowspan: 1,
                selected: false,
            };
        },

        ensureCellsMatchDimensions() {
            while (this.state.cells.length < this.state.rows) {
                let newRow = [];
                for (let c = 0; c < this.state.columns; c++) {
                    newRow.push(this.createCell(this.state.cells.length, c));
                }
                this.state.cells.push(newRow);
            }
            
            for (let r = 0; r < this.state.cells.length; r++) {
                while (this.state.cells[r].length < this.state.columns) {
                    this.state.cells[r].push(this.createCell(r, this.state.cells[r].length));
                }
            }
        },

        getRows() {
            // Return only rows that have cells, filter out any empty/undefined rows
            if (!this.state.cells || !Array.isArray(this.state.cells)) {
                return [];
            }
            
            return this.state.cells
                .map((row, rowIndex) => ({
                    rowIndex,
                    cells: row.filter(cell => !cell.merged)
                }))
                .filter(row => row.cells && row.cells.length > 0);
        },

        getVisibleColumnCount() {
            // Calculate actual visible columns by summing colspan values in the first row
            if (!this.state.cells || !this.state.cells[0]) {
                return this.state.columns || 0;
            }
            
            const firstRow = this.state.cells[0];
            let visibleCols = 0;
            
            firstRow.forEach(cell => {
                if (!cell.merged) {
                    visibleCols += (cell.colspan || 1);
                }
            });
            
            return visibleCols;
        },

        addRow() {
            let newRow = [];
            for (let c = 0; c < this.state.columns; c++) {
                newRow.push(this.createCell(this.state.rows, c));
            }
            this.state.cells.push(newRow);
            this.state.rows++;
        },

        addColumn() {
            for (let r = 0; r < this.state.cells.length; r++) {
                this.state.cells[r].push(this.createCell(r, this.state.columns));
            }
            this.state.columns++;
        },

        removeRow(rowIndex) {
            if (this.state.cells.length <= 1) {
                alert('Cannot remove the last row');
                return;
            }
            this.state.cells.splice(rowIndex, 1);
            this.state.rows--;

            this.state.cells.forEach((row, rIndex) => {
                row.forEach(cell => cell.rowIndex = rIndex);
            });
        },

        removeColumn(colIndex) {
            if (this.state.columns <= 2) {
                alert('Cannot remove the last column');
                return;
            }

            // Remove cells at the specified column index from each row
            this.state.cells.forEach((row) => {
                // Process each cell in the row (iterate backwards for safe removal)
                for (let i = row.length - 1; i >= 0; i--) {
                    const cell = row[i];
                    
                    // Skip merged cells (they're covered by colspan of other cells)
                    if (cell.merged) {
                        continue;
                    }
                    
                    const cellStart = cell.colIndex;
                    const cellEnd = cell.colIndex + cell.colspan; // Exclusive end
                    
                    // Case 1: Deleted column is within this cell's span (including start)
                    if (colIndex >= cellStart && colIndex < cellEnd) {
                        if (cell.colspan > 1) {
                            // Reduce colspan
                            cell.colspan--;
                            // If deleting the first column of the span, shift the cell left
                            if (colIndex === cellStart) {
                                cell.colIndex = Math.max(0, cellStart - 1);
                            }
                        } else {
                            // Single column cell - remove it
                            row.splice(i, 1);
                        }
                        continue;
                    }
                    
                    // Case 2: Cell is entirely after the deleted column - shift left
                    if (cellStart > colIndex) {
                        cell.colIndex--;
                    }
                    // Case 3: Cell is entirely before the deleted column - no change needed
                }
            });

            this.state.columns--;
            this.updateState();
        },

        toggleMergeMode() {
            this.mergeMode = !this.mergeMode;
            if (!this.mergeMode) {
                this.clearSelection();
            }
        },

        clearSelection() {
            this.selectedCells.forEach(item => {
                if (item.cell) item.cell.selected = false;
            });
            this.selectedCells = [];
        },

        handleCellClick(rowIndex, cell) {
            if (!this.mergeMode) return;

            // If clicking on a merged cell, select it for unmerging
            if (cell.colspan > 1 || cell.rowspan > 1) {
                this.clearSelection();
                this.selectedCells.push({ rowIndex, cell });
                cell.selected = true;
                return;
            }

            const cellIndex = this.selectedCells.findIndex(c => c.cell.id === cell.id);
            if (cellIndex > -1) {
                this.selectedCells.splice(cellIndex, 1);
                cell.selected = false;
            } else {
                this.selectedCells.push({ rowIndex, cell });
                cell.selected = true;
            }

            if (this.selectedCells.length >= 2) {
                setTimeout(() => this.mergeSelectedCells(), 150);
            }
        },

        hasMergedCell() {
            return this.selectedCells.some(item => item.cell.colspan > 1 || item.cell.rowspan > 1);
        },

        unmergeSelectedCells() {
            if (this.selectedCells.length === 0) return;

            const item = this.selectedCells[0];
            const cell = item.cell;
            const rowIndex = item.rowIndex;

            // Only unmerge if cell is actually merged
            if (cell.colspan <= 1 && cell.rowspan <= 1) return;

            const colspan = cell.colspan || 1;
            const startColIndex = cell.colIndex;

            // Reset the original cell
            cell.colspan = 1;
            cell.rowspan = 1;
            cell.selected = false;

            // Find or create cells for the split columns
            const row = this.state.cells[rowIndex];
            
            for (let c = 1; c < colspan; c++) {
                const targetColIndex = startColIndex + c;
                
                // Check if there's already a cell at this column index
                let existingCell = row.find(cell => cell.colIndex === targetColIndex && !cell.merged);
                
                if (existingCell) {
                    // Just unmerge it
                    existingCell.merged = false;
                    existingCell.colspan = 1;
                    existingCell.value = '';
                } else {
                    // Find if there's a merged cell at this position
                    let mergedCell = row.find(cell => {
                        if (!cell.merged) return false;
                        // Check if this merged cell covers our target column
                        return cell.colIndex < targetColIndex && 
                               (cell.colIndex + cell.colspan) > targetColIndex;
                    });

                    if (mergedCell) {
                        mergedCell.merged = false;
                        mergedCell.colspan = 1;
                        mergedCell.value = '';
                    } else {
                        // Create a new cell
                        let newCell = this.createCell(rowIndex, targetColIndex);
                        newCell.colIndex = targetColIndex;
                        row.push(newCell);
                    }
                }
            }

            // Sort row by colIndex
            row.sort((a, b) => a.colIndex - b.colIndex);

            this.clearSelection();
            this.updateState();
        },

        mergeSelectedCells() {
            if (this.selectedCells.length < 2) return;

            const firstCell = this.selectedCells[0].cell;
            const sameRow = this.selectedCells.every(item => item.cell.rowIndex === firstCell.rowIndex);
            
            if (!sameRow) {
                alert('Can only merge cells in the same row');
                this.clearSelection();
                return;
            }

            for (let i = 1; i < this.selectedCells.length; i++) {
                const cellToMerge = this.selectedCells[i].cell;
                cellToMerge.merged = true;
                cellToMerge.selected = false;
                firstCell.colspan = (firstCell.colspan || 1) + 1;
            }

            this.clearSelection();
        },

        updateState() {
            this.state = { ...this.state };
        }
    }
}
</script>
