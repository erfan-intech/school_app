/**
 * Universal Table Sorter
 * A single file solution that handles both simple and complex tables
 * Usage: Add class 'sortable-table' to any table and 'sortable' class to sortable column headers
 * Add data-sort attribute to specify the field name for sorting
 * Add data-type attribute to specify special handling (image, name, action, etc.)
 */
class UniversalTableSorter {
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
            onPageChange: options.onPageChange || null,
            // Custom rendering options
            customRenderers: options.customRenderers || {},
            searchFields: options.searchFields || null,
            sortHandlers: options.sortHandlers || {}
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
        // If custom search fields are specified, use them
        if (this.options.searchFields) {
            return this.options.searchFields.some(field => {
                const value = this.getFieldValue(item, field);
                return value.toLowerCase().includes(searchTerm);
            });
        }
        
        // Default search in all string fields
        for (let key in item) {
            if (typeof item[key] === 'string' && item[key].toLowerCase().includes(searchTerm)) {
                return true;
            }
        }
        return false;
    }
    
    getFieldValue(item, field) {
        // Handle combined fields like 'first_name last_name'
        if (field.includes(' ')) {
            return field.split(' ').map(f => item[f] || '').join(' ');
        }
        return item[field] || '';
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
            // Check for custom sort handler
            if (this.options.sortHandlers[this.currentSort.column]) {
                return this.options.sortHandlers[this.currentSort.column](a, b, this.currentSort.direction);
            }
            
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
        const row = $('<tr>');
        const headers = this.table.find('thead th');
        
        headers.each((index, header) => {
            const $header = $(header);
            const sortField = $header.data('sort');
            const dataType = $header.data('type');
            
            if (sortField) {
                const cell = this.renderCell(item, sortField, dataType, rowIndex);
                row.append(cell);
            } else {
                // Handle non-sortable columns (like actions)
                const cell = this.renderCell(item, null, dataType, rowIndex);
                row.append(cell);
            }
        });
        
        return row;
    }
    
    renderCell(item, field, dataType, rowIndex) {
        const cell = $('<td>');
        
        // Handle different data types
        switch (dataType) {
            case 'sl_no':
                cell.text(rowIndex);
                break;
                
            case 'name':
                const firstName = item.first_name || '';
                const lastName = item.last_name || '';
                cell.text(`${firstName} ${lastName}`.trim());
                break;
                
            case 'image':
                // For image columns, we need to determine the field name based on table context
                let imageField = field;
                if (!imageField) {
                    const tableId = this.table.attr('id');
                    if (tableId.includes('parent')) {
                        imageField = 'profile_picture';
                    } else if (tableId.includes('student')) {
                        imageField = 'profile_picture';
                    } else if (tableId.includes('teacher')) {
                        imageField = 'profile_picture';
                    }
                }
                
                if (item[imageField]) {
                    const imgSrc = this.getImagePath(item, imageField);
                    cell.html(`<img src="${imgSrc}" alt="Profile" width="40" height="40" style="object-fit:cover;border-radius:50%;">`);
                }
                break;
                
            case 'action':
                cell.addClass('text-end');
                cell.html(this.renderActionButtons(item));
                break;
                
            case 'email':
                const email = item[field] || '';
                cell.html(`<a href="mailto:${email}">${email}</a>`);
                break;
                
            case 'phone':
                const phone = item[field] || '';
                cell.html(`<a href="tel:${phone}">${phone}</a>`);
                break;
                
            default:
                // Default text rendering
                if (field) {
                    cell.text(item[field] || '');
                }
                break;
        }
        
        return cell;
    }
    
    getImagePath(item, field) {
        // Determine image path based on table context
        const tableId = this.table.attr('id');
        let basePath = '../uploads/';
        
        if (tableId.includes('parent')) {
            basePath += 'parents/';
        } else if (tableId.includes('student')) {
            basePath += 'students/';
        } else if (tableId.includes('teacher')) {
            basePath += 'teachers/';
        }
        
        return basePath + item[field];
    }
    
    renderActionButtons(item) {
        const tableId = this.table.attr('id');
        let editBtnClass = 'editBtn';
        let deleteBtnClass = 'deleteBtn';
        
        // Determine button classes based on table context
        if (tableId.includes('parent')) {
            editBtnClass = 'editParentBtn';
            deleteBtnClass = 'deleteParentBtn';
        } else if (tableId.includes('student')) {
            editBtnClass = 'editStudentBtn';
            deleteBtnClass = 'deleteStudentBtn';
        } else if (tableId.includes('teacher')) {
            editBtnClass = 'editTeacherBtn';
            deleteBtnClass = 'deleteTeacherBtn';
        }
        
        return `
            <button class="btn btn-sm btn-warning ${editBtnClass}" data-id="${item.id}">Edit</button>
            <button class="btn btn-sm btn-danger ${deleteBtnClass}" data-id="${item.id}">Delete</button>
        `;
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
        if ($('#universal-table-sorter-styles').length === 0) {
            $('<style id="universal-table-sorter-styles">')
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

// Global function to initialize universal table sorter
window.initUniversalTableSorter = function(tableSelector, options = {}) {
    return new UniversalTableSorter(tableSelector, options);
};

// Convenience function for common table types
window.initTableSorter = function(tableSelector, options = {}) {
    return new UniversalTableSorter(tableSelector, options);
}; 