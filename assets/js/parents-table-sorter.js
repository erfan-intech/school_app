/**
 * Parents Table Sorter - Specific implementation for parents table
 * Extends the common TableSorter with parents-specific functionality
 */
class ParentsTableSorter extends TableSorter {
    constructor(tableSelector, options = {}) {
        // Set default options for parents table
        const defaultOptions = {
            itemsPerPage: 10,
            enablePagination: true,
            enableSearch: true,
            enableViewAll: true,
            searchSelector: '#parentSearch',
            paginationSelector: '#pagination',
            viewAllSelector: '#viewAllBtn',
            onSort: null,
            onSearch: null,
            onPageChange: null
        };
        
        super(tableSelector, { ...defaultOptions, ...options });
    }
    
    createTableRow(item, rowIndex) {
        const pic = item.profile_picture ? 
            `<img src='../uploads/parents/${item.profile_picture}' alt='Profile' width='40' height='40' style='object-fit:cover;border-radius:50%;'>` : '';
        
        const row = $(`
            <tr data-parent-id='${item.id}'>
                <td>${item.sl_no}</td>
                <td>${item.first_name} ${item.last_name || ''}</td>
                <td>${item.phone || ''}</td>
                <td>${item.email || ''}</td>
                <td>${pic}</td>
                <td>${item.address || ''}</td>
                <td>${item.gender || ''}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-warning editParentBtn" data-id="${item.id}">Edit</button>
                    <button class="btn btn-sm btn-danger deleteParentBtn" data-id="${item.id}">Delete</button>
                </td>
            </tr>
        `);
        
        return row;
    }
    
    searchInItem(item, searchTerm) {
        const fullName = (item.first_name + ' ' + (item.last_name || '')).toLowerCase();
        const phone = (item.phone || '').toLowerCase();
        const email = (item.email || '').toLowerCase();
        const address = (item.address || '').toLowerCase();
        const gender = (item.gender || '').toLowerCase();
        
        return fullName.includes(searchTerm) || 
               phone.includes(searchTerm) || 
               email.includes(searchTerm) || 
               address.includes(searchTerm) || 
               gender.includes(searchTerm);
    }
    
    sortData() {
        if (!this.currentSort.column) return;
        
        this.filteredData.sort((a, b) => {
            let aVal, bVal;
            
            // Handle SL No sorting (use the PHP-generated sl_no)
            if (this.currentSort.column === 'sl_no') {
                aVal = parseInt(a.sl_no);
                bVal = parseInt(b.sl_no);
                return this.currentSort.direction === 'asc' ? aVal - bVal : bVal - aVal;
            }
            
            // Handle name sorting (combine first and last name)
            if (this.currentSort.column === 'name') {
                aVal = (a.first_name + ' ' + (a.last_name || '')).toLowerCase();
                bVal = (b.first_name + ' ' + (b.last_name || '')).toLowerCase();
            } else {
                // Handle other string sorting
                aVal = (a[this.currentSort.column] || '').toString().toLowerCase();
                bVal = (b[this.currentSort.column] || '').toString().toLowerCase();
            }
            
            if (aVal < bVal) return this.currentSort.direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return this.currentSort.direction === 'asc' ? 1 : -1;
            return 0;
        });
    }
    
    updatePaginationInfo(startIndex, endIndex) {
        $('#startRecord').text(this.filteredData.length > 0 ? startIndex + 1 : 0);
        $('#endRecord').text(endIndex);
        $('#totalRecords').text(this.filteredData.length);
    }
}

// Global function to initialize parents table sorter
window.initParentsTableSorter = function(tableSelector, options = {}) {
    return new ParentsTableSorter(tableSelector, options);
}; 