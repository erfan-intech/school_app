<?php include '../includes/header.php'; ?>
<?php
$class_id = $_GET['class_id'] ?? '';
if (!$class_id) {
    echo '<div class="alert alert-danger">Class ID required.</div>';
    include '../includes/footer.php';
    exit;
}
?>
<div class="container mt-4" id="classDetailsApp" data-class-id="<?php echo htmlspecialchars($class_id); ?>">
  <div class="d-flex align-items-center mb-3 justify-content-between">
    <div>
      <a href="classes.php" class="btn btn-secondary me-2">&larr; Back to Classes</a>
      <h2 class="mb-0 d-inline-block" id="classNameHeader">Class Name: <span id="className">Class 8</span></h2>
      <button class="btn btn-sm btn-outline-warning ms-2" id="editClassStructureBtn">Edit Class Structure</button>
    </div>
    <a href="class_details.php?class_id=<?php echo intval($class_id) - 1; ?>" class="btn btn-primary" id="nextClassBtn">&larr; Prev Class</a>
    <a href="class_details.php?class_id=<?php echo intval($class_id) + 1; ?>" class="btn btn-primary" id="nextClassBtn">Next Class &rarr;</a>
  </div>
  <div class="row mb-4">
    <div class="col-lg-8 col-md-12">
      <div id="departmentsSection">
        <h4 class="d-flex align-items-center">Departments with Subjects <span id="departmentsActionBtnContainer"></span></h4>
        <ul id="departmentsList" class="list-group mb-2"></ul>
      </div>
      <div id="subjectsSection">
        <h4>Subjects</h4>
        <table class="table table-bordered" id="subjectsList">
          <thead>
            <tr>
              <th>Subject</th>
              <th>Teachers</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
    <div class="col-lg-4 col-md-12">
      <h4>Sections</h4>
      <table class="table table-bordered" id="sectionsList">
        <thead>
          <tr>
            <th>Section Name</th>
            <th id="sectionsDeptHeader" style="display:none;">Department</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col-md-12">
      <h4>Teachers Assigned to This Class</h4>
      <button class="btn btn-sm btn-outline-primary mb-2" id="assignTeachersBtn">Assign Teacher</button>
      <div class="table-responsive">
        <table class="table table-bordered" id="classTeachersTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Profile</th>
              <th>Department</th>
              <th>Subject</th>
              <th>Attendance (Today)</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <div class="mt-2" id="assignTeacherSection" style="background: #c5c9ce; border-radius: 6px; padding: 16px;">
        <h5 class="mb-3">Bind Teachers for Subjects(This can be removed later if user ask to)</h5>
        <label for="addTeacherSelect">Assign Teacher:</label>
        <select id="addTeacherSelect" class="form-select w-auto d-inline-block"></select>
        <span id="departmentDropdownContainer" style="display:none">
          <label for="addTeacherDepartmentSelect">Department:</label>
          <select id="addTeacherDepartmentSelect" class="form-select w-auto d-inline-block"></select>
        </span>
        <label for="addTeacherSubjectSelect">Subject:</label>
        <select id="addTeacherSubjectSelect" class="form-select w-auto d-inline-block"></select>
        <button class="btn btn-primary btn-sm" id="addTeacherBtn">Assign</button>
      </div>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col-md-12">
      <h4>Enrolled Students</h4>
      <div class="table-responsive">
        <table class="table table-bordered" id="classStudentsTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Profile</th>
              <th>Attendance (Today)</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Assign Teachers Modal -->
<div class="modal fade" id="assignTeachersModal" tabindex="-1" aria-labelledby="assignTeachersModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="assignTeachersForm">
        <div class="modal-header">
          <h5 class="modal-title" id="assignTeachersModalLabel">Assign Teachers to Class</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="assignTeachersModalBody">
          <!-- Dynamic content: checkboxes for each teacher -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Edit Teacher Subjects Modal -->
<div class="modal fade" id="editTeacherSubjectsModal" tabindex="-1" aria-labelledby="editTeacherSubjectsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editTeacherSubjectsForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editTeacherSubjectsModalLabel">Edit Teacher's Departments & Subjects</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="editTeacherDepartmentsContainer" class="mb-3"></div>
          <div id="editTeacherSubjectsContainer"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Edit Class Structure Modal -->
<div class="modal fade" id="editClassStructureModal" tabindex="-1" aria-labelledby="editClassStructureModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editClassStructureForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editClassStructureModalLabel">Edit Class Structure</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Main Options -->
          <div class="mb-4">
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="hasDepartmentsCheck">
              <label class="form-check-label" for="hasDepartmentsCheck">
                <strong>This class have Departments</strong>
              </label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="hasSectionsCheck">
              <label class="form-check-label" for="hasSectionsCheck">
                <strong>This class have Sections</strong>
              </label>
            </div>
          </div>

          <!-- Select Departments Section -->
          <div class="mb-4" id="selectDepartmentsSection" style="display: none;">
            <h6><strong>Select Departments: (only departments to check)</strong></h6>
            <div id="departmentsCheckboxes"></div>
          </div>

          <!-- Global Sections Section -->
          <div class="mb-4" id="globalSectionsSection" style="display: none;">
            <h6><strong>Select Section: (global)</strong></h6>
            <div id="globalSectionsCheckboxes"></div>
          </div>

          <!-- Global Subjects Section -->
          <div class="mb-4" id="globalSubjectsSection" style="display: none;">
            <h6><strong>Select Subjects: (global)</strong></h6>
            <div id="globalSubjectsCheckboxes"></div>
          </div>

          <!-- Department Sections Section -->
          <div class="mb-4" id="departmentSectionsSection" style="display: none;">
            <h6><strong>Assign Section on Each Department:</strong></h6>
            <div id="departmentSectionsCheckboxes"></div>
          </div>

          <!-- Department Subjects Section -->
          <div class="mb-4" id="departmentSubjectsSection" style="display: none;">
            <h6><strong>Assign Subjects for Departments:</strong></h6>
            <div id="departmentSubjectsCheckboxes"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="../assets/js/class_details.js"></script>
<?php include '../includes/footer.php'; ?> 