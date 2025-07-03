/**
 * Common Table Sorter
 * Usage: Add class 'sortable-table' to any table and 'sortable' class to sortable column headers
 * Add data-sort attribute to specify the field name for sorting
 */
class TableSorter {
    constructor(tableSelector, options = {}) {
        this.table = $(tableSelector);
        this.options = {
            itemsPerPage: options.itemsPerPage || 10,
            enablePagination: options.enablePagination !== false,
            enableSearch: options.enableSearch !== false,
            enableViewAll: options.enableViewAll !== false,
            searchSelector: options.searchSelector || null,
            paginationSelector: options.paginationSelector || null,
            viewAllSelector: options.viewAllSelector || null,
            onSort: options.onSort || null,
            onSearch: options.onSearch || null,
            onPageChange: options.onPageChange || null
        };
        
        this.data = [];
        this.filteredData = [];
        this.currentSort = { column: null, direction: 'asc' };
        this.currentPage = 1;
        this.viewAllMode = false;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.addStyles();
    }
    
    bindEvents() {
        // Sortable column clicks
        this.table.on('click', '.sortable', (e) => {
            e.preventDefault();
            this.handleSort($(e.currentTarget));
        });
        
        // Search functionality
        if (this.options.enableSearch && this.options.searchSelector) {
            $(this.options.searchSelector).on('input', (e) => {
                this.handleSearch($(e.currentTarget).val());
            });
        }
        
        // Pagination clicks
        if (this.options.enablePagination && this.options.paginationSelector) {
            $(document).on('click', this.options.paginationSelector + ' .page-link', (e) => {
                e.preventDefault();
                this.handlePagination($(e.currentTarget));
            });
        }
        
        // View all/less toggle
        if (this.options.enableViewAll && this.options.viewAllSelector) {
            $(this.options.viewAllSelector).on('click', (e) => {
                e.preventDefault();
                this.toggleViewAll();
            });
        }
    }
    
    handleSort($column) {
        const column = $column.data('sort');
        const direction = this.currentSort.column === column && this.currentSort.direction === 'asc' ? 'desc' : 'asc';
        
        // Remove previous sort indicators
        this.table.find('.sortable').removeClass('asc desc');
        
        // Add sort indicator
        $column.addClass(direction);
        
        this.currentSort = { column, direction };
        this.currentPage = 1; // Reset to first page when sorting
        
        this.sortData();
        this.renderTable();
        
        if (this.options.onSort) {
            this.options.onSort(column, direction);
        }
    }
    
    handleSearch(searchTerm) {
        if (searchTerm === '') {
            this.filteredData = [...this.data];
        } else {
            this.filteredData = this.data.filter(item => {
                return this.searchInItem(item, searchTerm.toLowerCase());
            });
        }
        
        this.currentPage = 1;
        this.renderTable();
        
        if (this.options.onSearch) {
            this.options.onSearch(searchTerm);
        }
    }
    
    searchInItem(item, searchTerm) {
        // Search in all string fields
        for (let key in item) {
            if (typeof item[key] === 'string' && item[key].toLowerCase().includes(searchTerm)) {
                return true;
            }
        }
        return false;
    }
    
    handlePagination($link) {
        const page = $link.data('page');
        
        if (page) {
            this.currentPage = parseInt(page);
        } else if ($link.attr('id') === 'prevPage') {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        } else if ($link.attr('id') === 'nextPage') {
            const totalPages = Math.ceil(this.filteredData.length / this.options.itemsPerPage);
            if (this.currentPage < totalPages) {
                this.currentPage++;
            }
        }
        
        this.renderTable();
        
        if (this.options.onPageChange) {
            this.options.onPageChange(this.currentPage);
        }
    }
    
    toggleViewAll() {
        this.viewAllMode = !this.viewAllMode;
        this.currentPage = 1;
        this.renderTable();
        this.updatePagination();
    }
    
    sortData() {
        if (!this.currentSort.column) return;
        
        this.filteredData.sort((a, b) => {
            let aVal, bVal;
            
            // Handle numeric sorting
            if (this.isNumeric(a[this.currentSort.column]) && this.isNumeric(b[this.currentSort.column])) {
                aVal = parseFloat(a[this.currentSort.column]) || 0;
                bVal = parseFloat(b[this.currentSort.column]) || 0;
                return this.currentSort.direction === 'asc' ? aVal - bVal : bVal - aVal;
            }
            
            // Handle string sorting
            aVal = (a[this.currentSort.column] || '').toString().toLowerCase();
            bVal = (b[this.currentSort.column] || '').toString().toLowerCase();
            
            if (aVal < bVal) return this.currentSort.direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return this.currentSort.direction === 'asc' ? 1 : -1;
            return 0;
        });
    }
    
    isNumeric(value) {
        return !isNaN(parseFloat(value)) && isFinite(value);
    }
    
    renderTable() {
        const totalPages = Math.ceil(this.filteredData.length / this.options.itemsPerPage);
        let startIndex, endIndex, pageData;
        
        if (this.viewAllMode) {
            pageData = this.filteredData;
            startIndex = 0;
            endIndex = this.filteredData.length;
        } else {
            startIndex = (this.currentPage - 1) * this.options.itemsPerPage;
            endIndex = Math.min(startIndex + this.options.itemsPerPage, this.filteredData.length);
            pageData = this.filteredData.slice(startIndex, endIndex);
        }
        
        // Update table body
        const tbody = this.table.find('tbody');
        tbody.empty();
        
        pageData.forEach((item, index) => {
            const row = this.createTableRow(item, startIndex + index + 1);
            tbody.append(row);
        });
        
        // Update pagination info
        this.updatePaginationInfo(startIndex, endIndex);
        
        // Update pagination controls
        if (this.options.enablePagination) {
            this.updatePagination(totalPages);
        }
    }
    
    createTableRow(item, rowIndex) {
        // This method should be overridden by specific implementations
        // Default implementation creates a simple row
        const row = $('<tr>');
        for (let key in item) {
            row.append($('<td>').text(item[key]));
        }
        return row;
    }
    
    updatePaginationInfo(startIndex, endIndex) {
        const infoSelector = this.options.paginationSelector ? 
            this.options.paginationSelector.replace('#', '') + '-info' : 
            '.pagination-info';
        
        $(infoSelector).html(
            `Showing <span>${this.filteredData.length > 0 ? startIndex + 1 : 0}</span> to <span>${endIndex}</span> of <span>${this.filteredData.length}</span> records`
        );
    }
    
    updatePagination(totalPages) {
        if (!this.options.paginationSelector) return;
        
        const $pagination = $(this.options.paginationSelector);
        $pagination.empty();
        
        if (this.viewAllMode) {
            $pagination.hide();
            if (this.options.viewAllSelector) {
                $(this.options.viewAllSelector).text('View Less').removeClass('btn-outline-primary').addClass('btn-primary');
            }
        } else {
            $pagination.show();
            if (this.options.viewAllSelector) {
                $(this.options.viewAllSelector).text('View All').removeClass('btn-primary').addClass('btn-outline-primary');
            }
            
            // Previous button
            const prevDisabled = this.currentPage <= 1 ? 'disabled' : '';
            $pagination.append(`<li class="page-item ${prevDisabled}">
                <a class="page-link" href="#" id="prevPage">Previous</a>
            </li>`);
            
            // Page numbers
            const startPage = Math.max(1, this.currentPage - 2);
            const endPage = Math.min(totalPages, this.currentPage + 2);
            
            if (startPage > 1) {
                $pagination.append(`<li class="page-item">
                    <a class="page-link" href="#" data-page="1">1</a>
                </li>`);
                if (startPage > 2) {
                    $pagination.append(`<li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>`);
                }
            }
            
            for (let i = startPage; i <= endPage; i++) {
                const active = i === this.currentPage ? 'active' : '';
                $pagination.append(`<li class="page-item ${active}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`);
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    $pagination.append(`<li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>`);
                }
                $pagination.append(`<li class="page-item">
                    <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                </li>`);
            }
            
            // Next button
            const nextDisabled = this.currentPage >= totalPages ? 'disabled' : '';
            $pagination.append(`<li class="page-item ${nextDisabled}">
                <a class="page-link" href="#" id="nextPage">Next</a>
            </li>`);
        }
    }
    
    setData(data) {
        this.data = data;
        this.filteredData = [...data];
        this.currentPage = 1;
        this.renderTable();
    }
    
    addStyles() {
        if ($('#table-sorter-styles').length === 0) {
            $('<style id="table-sorter-styles">')
                .html(`
                    .sortable { cursor: pointer; user-select: none; }
                    .sortable:hover { background-color: #e9ecef !important; }
                    .sortable i { margin-left: 5px; opacity: 0.5; transition: opacity 0.2s; }
                    .sortable.asc i, .sortable.desc i { opacity: 1; }
                    .sortable.asc i::before { content: "\\f0de"; }
                    .sortable.desc i::before { content: "\\f0dd"; }
                    .table tbody tr:hover { background-color: #f8f9fa; }
                `)
                .appendTo('head');
        }
    }
}

// Global function to initialize table sorter
window.initTableSorter = function(tableSelector, options = {}) {
    return new TableSorter(tableSelector, options);
}; 