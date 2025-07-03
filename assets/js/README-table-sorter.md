# Common Table Sorter System

This system provides a reusable table sorting, pagination, and search functionality that can be applied to any table in the application.

## Files

1. **`table-sorter.js`** - The main common table sorter class
2. **`parents-table-sorter.js`** - Specific implementation for parents table
3. **`parents.js`** - Updated to use the new table sorter

## Usage

### Basic Usage

1. **Include the scripts in your PHP file:**
```html
<script src="../assets/js/table-sorter.js"></script>
<script src="../assets/js/your-specific-sorter.js"></script>
<script src="../assets/js/your-page.js"></script>
```

2. **Add the `sortable-table` class to your table:**
```html
<table class="table table-bordered sortable-table" id="yourTable">
```

3. **Add `sortable` class and `data-sort` attribute to column headers:**
```html
<th class="sortable" data-sort="field_name">Column Name <i class="fas fa-sort"></i></th>
```

4. **Initialize in your JavaScript:**
```javascript
var tableSorter = initTableSorter('#yourTable', {
    itemsPerPage: 10,
    enablePagination: true,
    enableSearch: true,
    enableViewAll: true,
    searchSelector: '#searchInput',
    paginationSelector: '#pagination',
    viewAllSelector: '#viewAllBtn'
});

// Load data
tableSorter.setData(yourData);
```

### Creating a Specific Implementation

For tables with custom rendering needs, create a specific implementation:

```javascript
class YourTableSorter extends TableSorter {
    constructor(tableSelector, options = {}) {
        const defaultOptions = {
            itemsPerPage: 10,
            enablePagination: true,
            enableSearch: true,
            enableViewAll: true,
            searchSelector: '#yourSearch',
            paginationSelector: '#yourPagination',
            viewAllSelector: '#yourViewAllBtn'
        };
        
        super(tableSelector, { ...defaultOptions, ...options });
    }
    
    createTableRow(item, rowIndex) {
        // Custom row rendering logic
        return $(`<tr>
            <td>${item.field1}</td>
            <td>${item.field2}</td>
            <td>${item.field3}</td>
        </tr>`);
    }
    
    searchInItem(item, searchTerm) {
        // Custom search logic
        return item.field1.toLowerCase().includes(searchTerm) ||
               item.field2.toLowerCase().includes(searchTerm);
    }
    
    sortData() {
        // Custom sorting logic if needed
        super.sortData();
    }
}

// Global function
window.initYourTableSorter = function(tableSelector, options = {}) {
    return new YourTableSorter(tableSelector, options);
};
```

## Features

### Sorting
- Click on column headers with `sortable` class to sort
- Visual indicators show sort direction
- Supports both string and numeric sorting
- Custom sorting logic can be implemented

### Pagination
- Configurable items per page
- Page navigation with Previous/Next buttons
- Page numbers with ellipsis for large datasets
- View All/Less toggle functionality

### Search
- Real-time search across all text fields
- Custom search logic can be implemented
- Resets to first page when searching

### Styling
- Automatic CSS injection for sort indicators
- Hover effects on sortable columns
- Bootstrap-compatible styling

## Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `itemsPerPage` | number | 10 | Number of items per page |
| `enablePagination` | boolean | true | Enable pagination functionality |
| `enableSearch` | boolean | true | Enable search functionality |
| `enableViewAll` | boolean | true | Enable view all/less toggle |
| `searchSelector` | string | null | CSS selector for search input |
| `paginationSelector` | string | null | CSS selector for pagination container |
| `viewAllSelector` | string | null | CSS selector for view all button |
| `onSort` | function | null | Callback when sorting occurs |
| `onSearch` | function | null | Callback when searching occurs |
| `onPageChange` | function | null | Callback when page changes |

## Example HTML Structure

```html
<div class="container">
    <!-- Header with search -->
    <div class="d-flex align-items-center mb-3">
        <button class="btn btn-primary" id="addBtn">Add Item</button>
        <input type="text" id="searchInput" class="form-control w-auto ms-auto" placeholder="Search...">
    </div>
    
    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered sortable-table" id="yourTable">
            <thead>
                <tr>
                    <th class="sortable" data-sort="id">ID <i class="fas fa-sort"></i></th>
                    <th class="sortable" data-sort="name">Name <i class="fas fa-sort"></i></th>
                    <th class="sortable" data-sort="email">Email <i class="fas fa-sort"></i></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="pagination-section">
        <div class="d-flex justify-content-between align-items-center">
            <div class="pagination-info">
                Showing <span id="startRecord">1</span> to <span id="endRecord">10</span> of <span id="totalRecords">0</span> records
            </div>
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-primary btn-sm me-3" id="viewAllBtn">View All</button>
                <nav aria-label="Pagination">
                    <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>
```

## Benefits

1. **Reusability** - One system for all tables
2. **Consistency** - Same behavior across all tables
3. **Maintainability** - Centralized code for table functionality
4. **Extensibility** - Easy to add new features
5. **Performance** - Optimized for large datasets
6. **User Experience** - Professional sorting and pagination

## Migration from Old Code

To migrate existing tables:

1. Add `sortable-table` class to table
2. Add `sortable` class and `data-sort` attributes to headers
3. Include the table sorter scripts
4. Replace custom sorting/pagination code with table sorter initialization
5. Update data loading to use `setData()` method 