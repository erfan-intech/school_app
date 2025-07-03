<?php include '../includes/header.php'; ?>
<?php
// Fetch parents for dropdown
include '../includes/db_connect.php';
$parents = [];
$result = $conn->query("SELECT id, first_name, last_name, gender, phone FROM parents WHERE is_deleted=0");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $parents[] = $row;
    }
}
// Fetch classes for dropdown
$classes = [];
$result = $conn->query("SELECT id, name FROM classes");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}
// Fetch departments for dropdown
$departments = [];
$result = $conn->query("SELECT id, name FROM departments");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}
// Fetch sections for dropdown
$sections = [];
$result = $conn->query("SELECT id, name FROM sections");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }
}
$conn->close();
?>


<div class="container mt-4">
    <h2>Student Management</h2>
    <div class="d-flex align-items-center mb-3">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#studentModal" id="addStudentBtn">Add Student</button>
      <input type="text" id="studentSearch" class="form-control w-auto ms-auto" placeholder="Search students...">
    </div>
    <div class="table-responsive">
        <table class="table table-bordered" id="studentsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Profile Picture</th>
                    <th>Class</th>
                    <th>Department</th>
                    <th>Section</th>
                    <th>Roll No</th>
                    <th>Local Guardian</th>
                    <th>Details</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<!-- Student Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="studentForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="studentModalLabel">Add/Edit Student</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="studentId">
          <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
          </div>
          <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name">
          </div>
          <div class="mb-3">
            <label for="profile_picture" class="form-label">Profile Picture</label>
            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
            <div id="profilePicPreview" class="mt-2"></div>
            <button type="button" class="btn btn-sm btn-danger mt-2" id="removeStudentPhotoBtn" style="display:none;">Remove Photo</button>
            <input type="hidden" name="remove_photo" id="removeStudentPhoto" value="0">
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
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address">
          </div>
          <div class="mb-3">
            <label for="admission_date" class="form-label">Admission Date</label>
            <input type="date" class="form-control" id="admission_date" name="admission_date">
          </div>
          <div class="mb-3">
            <label for="current_class_id" class="form-label">Class</label>
            <select class="form-select" id="current_class_id" name="current_class_id">
              <option value="">Select Class</option>
              <?php foreach ($classes as $class): ?>
                <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="current_department_id" class="form-label">Department</label>
            <select class="form-select" id="current_department_id" name="current_department_id">
              <option value="">No Department</option>
              <?php foreach ($departments as $department): ?>
                <option value="<?php echo $department['id']; ?>"><?php echo htmlspecialchars($department['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="current_section_id" class="form-label">Section</label>
            <select class="form-select" id="current_section_id" name="current_section_id">
              <option value="">No Section</option>
              <?php foreach ($sections as $section): ?>
                <option value="<?php echo $section['id']; ?>"><?php echo htmlspecialchars($section['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="father_id" class="form-label">Father</label>
            <select class="form-select" id="father_id" name="father_id">
              <option value="">No Father</option>
              <?php foreach ($parents as $parent): if (strtolower($parent['gender']) === 'male'): ?>
                <option value="<?php echo $parent['id']; ?>"><?php echo htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name'] . ' (' . $parent['phone'] . ')'); ?></option>
              <?php endif; endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="mother_id" class="form-label">Mother</label>
            <select class="form-select" id="mother_id" name="mother_id">
              <option value="">No Mother</option>
              <?php foreach ($parents as $parent): if (strtolower($parent['gender']) === 'female'): ?>
                <option value="<?php echo $parent['id']; ?>"><?php echo htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name'] . ' (' . $parent['phone'] . ')'); ?></option>
              <?php endif; endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="local_guardian_id" class="form-label">Local Guardian</label>
            <select class="form-select" id="local_guardian_id" name="local_guardian_id">
              <option value="">No Local Guardian</option>
              <?php foreach ($parents as $parent): ?>
                <option value="<?php echo $parent['id']; ?>"><?php echo htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name'] . ' (' . $parent['phone'] . ')'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="studentRollNo" class="form-label">Roll Number</label>
            <input type="number" class="form-control" id="studentRollNo" name="roll_no" required>
          </div>
          <div class="mb-3">
            <label for="studentNote" class="form-label">Note</label>
            <textarea class="form-control" id="studentNote" name="note"></textarea>
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
<!-- Promote Student Modal -->
<div class="modal fade" id="promoteModal" tabindex="-1" aria-labelledby="promoteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="promoteForm">
        <div class="modal-header">
          <h5 class="modal-title" id="promoteModalLabel">Promote Student</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="promoteStudentId">
          <div class="mb-3">
            <label for="promote_class_id" class="form-label">New Class</label>
            <select class="form-select" id="promote_class_id" name="current_class_id" required>
              <option value="">Select Class</option>
              <?php foreach ($classes as $class): ?>
                <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="promote_department_id" class="form-label">New Department</label>
            <select class="form-select" id="promote_department_id" name="current_department_id">
              <option value="">No Department</option>
              <?php foreach ($departments as $department): ?>
                <option value="<?php echo $department['id']; ?>"><?php echo htmlspecialchars($department['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="promote_section_id" class="form-label">New Section</label>
            <select class="form-select" id="promote_section_id" name="current_section_id">
              <option value="">No Section</option>
              <?php foreach ($sections as $section): ?>
                <option value="<?php echo $section['id']; ?>"><?php echo htmlspecialchars($section['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Promote</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="../assets/js/students.js"></script>
<?php include '../includes/footer.php'; ?>
