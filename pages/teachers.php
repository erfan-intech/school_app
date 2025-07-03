<?php include '../includes/header.php'; ?>
<?php
// Fetch departments for dropdown
include '../includes/db_connect.php';
$departments = [];
$result = $conn->query("SELECT id, name FROM departments");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}
$conn->close();
?>
<div class="container mt-4">
    <!-- Sticky Header Section -->
    <div class="sticky-header bg-white border-bottom pb-3 mb-3" style="position: sticky; top: 56px; z-index: 1000;">
        <h2>Teacher Management</h2>
        <div class="d-flex align-items-center">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#teacherModal" id="addTeacherBtn">Add Teacher</button>
            <input type="text" id="teacherSearch" class="form-control w-auto ms-auto" placeholder="Search teachers...">
        </div>
    </div>
    
    <div class="table-responsive" style="height: calc(100vh - 250px); overflow-y: auto;">
        <table class="table table-bordered" id="teachersTable">
            <thead>
                <tr>
                    <th class="sortable" data-sort="sl_no">SL No <i class="fas fa-sort"></i></th>
                    <th class="sortable" data-sort="first_name">First Name <i class="fas fa-sort"></i></th>
                    <th class="sortable" data-sort="last_name">Last Name <i class="fas fa-sort"></i></th>
                    <th class="sortable" data-sort="phone">Phone <i class="fas fa-sort"></i></th>
                    <th class="sortable" data-sort="position">Position <i class="fas fa-sort"></i></th>
                    <th>Profile Picture</th>
                    <th class="sortable" data-sort="attendance">Status <i class="fas fa-sort"></i></th>
                    <th>Details</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Pagination Section -->
    <div class="pagination-section bg-white border-top pt-3" style="position: sticky; bottom: 0; z-index: 1000;">
        <div class="d-flex justify-content-between align-items-center">
            <div class="pagination-info">
                Showing <span id="startRecord">1</span> to <span id="endRecord">10</span> of <span id="totalRecords">0</span> teachers
            </div>
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-primary btn-sm me-3" id="viewAllBtn">View All</button>
                <nav aria-label="Teacher pagination">
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
<!-- Teacher Modal -->
<div class="modal fade" id="teacherModal" tabindex="-1" aria-labelledby="teacherModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="teacherForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="teacherModalLabel">Add/Edit Teacher</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="teacherId">
          <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
          </div>
          <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required>
          </div>
          <div class="mb-3">
            <label for="dob" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="dob" name="dob">
          </div>
          <div class="mb-3">
            <label for="gender" class="form-label">Gender</label>
            <select class="form-select" id="gender" name="gender">
              <option value="">Select</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email">
          </div>
          <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address">
          </div>
          <div class="mb-3">
            <label for="department_id" class="form-label">Department</label>
            <select class="form-select" id="department_id" name="department_id">
              <option value="">No Department</option>
              <?php foreach ($departments as $department): ?>
                <option value="<?php echo $department['id']; ?>"><?php echo htmlspecialchars($department['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="position" class="form-label">Position</label>
            <input type="text" class="form-control" id="position" name="position">
          </div>
          <div class="mb-3">
            <label for="teacherProfilePicture" class="form-label">Profile Picture</label>
            <input type="file" class="form-control" id="teacherProfilePicture" name="profile_picture" accept="image/*">
            <div id="teacherPicPreview" class="mt-2"></div>
            <button type="button" class="btn btn-sm btn-danger mt-2" id="removeTeacherPhotoBtn" style="display:none;">Remove Photo</button>
            <input type="hidden" name="remove_photo" id="removeTeacherPhoto" value="0">
          </div>
          <div class="mb-3">
            <label for="join_date" class="form-label">Join Date</label>
            <input type="date" class="form-control" id="join_date" name="join_date">
          </div>
          <div class="mb-3">
            <label for="leave_date" class="form-label">Leave Date</label>
            <input type="date" class="form-control" id="leave_date" name="leave_date">
          </div>
          <div class="mb-3">
            <label for="salary" class="form-label">Salary</label>
            <input type="number" step="0.01" class="form-control" id="salary" name="salary">
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
<script src="../assets/js/teachers.js"></script>
<?php include '../includes/footer.php'; ?>
