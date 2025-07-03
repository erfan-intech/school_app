<?php include '../includes/header.php'; ?>
<style>
    .sticky-header {
        background: white;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }
    
    .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    
    .table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        white-space: nowrap;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .pagination-section {
        background: white;
        border-top: 1px solid #dee2e6;
        padding-top: 1rem;
        margin-top: 1rem;
    }
    
    .sortable {
        cursor: pointer;
        user-select: none;
        position: relative;
    }
    
    .sortable:hover {
        background-color: #e9ecef !important;
    }
    
    .sortable i {
        margin-left: 5px;
        opacity: 0.5;
        transition: opacity 0.2s;
    }
    
    .sortable.asc i,
    .sortable.desc i {
        opacity: 1;
    }
    
    .sortable.asc i::before {
        content: "\f0de";
    }
    
    .sortable.desc i::before {
        content: "\f0dd";
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .pagination-info {
        font-size: 0.875rem;
        color: #6c757d;
    }
</style>
<div class="container mt-4">
    <!-- Sticky Header Section -->
    <div class="sticky-header bg-white border-bottom pb-3 mb-3" style="position: sticky; top: 56px; z-index: 1000;">
        <h2>Parent Management</h2>
        <div class="d-flex align-items-center">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#parentModal" id="addParentBtn">Add Parent</button>
            <input type="text" id="parentSearch" class="form-control w-auto ms-auto" placeholder="Search parents...">
        </div>
    </div>
    
    <div class="table-responsive" style="height: calc(100vh - 250px); overflow-y: auto;">
        <table class="table table-bordered sortable-table" id="parentsTable">
            <thead>
                <tr>
                    <th class="sortable" data-sort="sl_no" data-type="sl_no">SL No <i class="fas fa-sort"></i></th>
                    <th class="sortable" data-sort="name" data-type="name">Name <i class="fas fa-sort"></i></th>
                    <th class="sortable" data-sort="phone" data-type="phone">Phone <i class="fas fa-sort"></i></th>
                    <th class="sortable" data-sort="email" data-type="email">Email <i class="fas fa-sort"></i></th>
                    <th data-type="image">Profile Picture</th>
                    <th class="sortable" data-sort="address">Address <i class="fas fa-sort"></i></th>
                    <th class="sortable" data-sort="gender">Gender <i class="fas fa-sort"></i></th>
                    <th class="text-end" data-type="action">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Pagination Section -->
    <div class="pagination-section bg-white border-top pt-3" style="position: sticky; bottom: 0; z-index: 1000;">
        <div class="d-flex justify-content-between align-items-center">
            <div class="pagination-info">
                Showing <span id="startRecord">1</span> to <span id="endRecord">10</span> of <span id="totalRecords">0</span> parents
            </div>
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-primary btn-sm me-3" id="viewAllBtn">View All</button>
                <nav aria-label="Parent pagination">
                    <ul class="pagination pagination-sm mb-0" id="pagination">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" id="prevPage">Previous</a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="#" data-page="1">1</a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" id="nextPage">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- Parent Modal -->
<div class="modal fade" id="parentModal" tabindex="-1" aria-labelledby="parentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="parentForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="parentModalLabel">Add/Edit Parent</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="parentId">
          <div class="mb-3">
            <label for="parentFirstName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="parentFirstName" name="first_name" required>
          </div>
          <div class="mb-3">
            <label for="parentLastName" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="parentLastName" name="last_name">
          </div>
          <div class="mb-3">
            <label for="parentGender" class="form-label">Gender</label>
            <select class="form-select" id="parentGender" name="gender" required>
              <option value="">Select</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="parentPhone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="parentPhone" name="phone">
          </div>
          <div class="mb-3">
            <label for="parentEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="parentEmail" name="email">
          </div>
          <div class="mb-3">
            <label for="parentAddress" class="form-label">Address</label>
            <input type="text" class="form-control" id="parentAddress" name="address">
          </div>
          <div class="mb-3">
            <label for="parentProfilePicture" class="form-label">Profile Picture</label>
            <input type="file" class="form-control" id="parentProfilePicture" name="profile_picture" accept="image/*">
            <div id="parentPicPreview" class="mt-2"></div>
            <button type="button" class="btn btn-sm btn-danger mt-2" id="removeParentPhotoBtn" style="display:none;">Remove Photo</button>
            <input type="hidden" name="remove_photo" id="removeParentPhoto" value="0">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="../assets/js/universal-table-sorter.js"></script>
<script src="../assets/js/parents.js"></script>
<?php include '../includes/footer.php'; ?>
