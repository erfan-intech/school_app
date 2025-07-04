<?php include '../includes/header.php'; ?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Exam Management</h2>
    <div>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExamModal">
        <i class="fas fa-plus"></i> Create New Exam
      </button>
    </div>
  </div>

  <!-- Exam Types Management -->
  <div class="card mb-4">
    <div class="card-header">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
          <i class="fas fa-tags me-2"></i>Exam Types Management
        </h5>
        <div class="d-flex align-items-center">
          <button class="btn btn-outline-secondary btn-sm" type="button" id="examTypesToggleBtn">
            <i class="fas fa-chevron-down"></i>
          </button>
        </div>
      </div>
    </div>
    <div class="collapse" id="examTypesTableCollapse" style="transition: all 0.3s ease-in-out;">
      <div class="card-body">
        <div class="d-flex justify-content-end mb-3">
          <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addExamTypeModal">
            <i class="fas fa-plus"></i> Add Exam Type
          </button>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover" id="examTypesTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Type Name</th>
                <th>Description</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <!-- Dynamic content will be loaded here -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Exams Table -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">
        <i class="fas fa-file-alt me-2"></i>All Exams
      </h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-hover" id="examsTable">
          <thead>
            <tr>
              <th>Sl No.</th>
              <th>Exam Name</th>
              <th>Type</th>
              <th>Class</th>
              <th>Department</th>
              <th>Section</th>
              <th>Subjects</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <!-- Dynamic content will be loaded here -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Add Exam Modal -->
<div class="modal fade" id="addExamModal" tabindex="-1" aria-labelledby="addExamModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="addExamForm">
        <div class="modal-header">
          <h5 class="modal-title" id="addExamModalLabel">Create New Exam</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 mb-3">
              <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Exam name will be generated automatically based on your selections (Class_Department_Section_ExamType_Year)
              </div>
              <div id="examNamePreview" class="text-muted small"></div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="examClass" class="form-label">Class *</label>
              <select class="form-select" id="examClass" name="class_id" required>
                <option value="">Select Class</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label for="examDepartment" class="form-label">Department</label>
              <select class="form-select" id="examDepartment" name="department_id">
                <option value="">Select Department</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label for="examSection" class="form-label">Section</label>
              <select class="form-select" id="examSection" name="section_id">
                <option value="">Select Section</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="examType" class="form-label">Exam Type *</label>
              <select class="form-select" id="examType" name="exam_type_id" required>
                <option value="">Select Exam Type</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="examYear" class="form-label">Academic Year *</label>
              <input type="number" class="form-control" id="examYear" name="academic_year" value="<?php echo date('Y'); ?>" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="examDescription" class="form-label">Description</label>
            <textarea class="form-control" id="examDescription" name="description" rows="3" placeholder="Enter exam description..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Create Exam</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Exam Modal -->
<div class="modal fade" id="editExamModal" tabindex="-1" aria-labelledby="editExamModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editExamForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editExamModalLabel">Edit Exam</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editExamId" name="exam_id">
          
          <div class="row">
            <div class="col-md-12 mb-3">
              <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Exam name will be regenerated automatically based on your selections (Class_Department_Section_ExamType_Year)
              </div>
              <div id="editExamNamePreview" class="text-muted small"></div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="editExamClass" class="form-label">Class *</label>
              <select class="form-select" id="editExamClass" name="class_id" required>
                <option value="">Select Class</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label for="editExamDepartment" class="form-label">Department</label>
              <select class="form-select" id="editExamDepartment" name="department_id">
                <option value="">Select Department</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label for="editExamSection" class="form-label">Section</label>
              <select class="form-select" id="editExamSection" name="section_id">
                <option value="">Select Section</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="editExamType" class="form-label">Exam Type *</label>
              <select class="form-select" id="editExamType" name="exam_type_id" required>
                <option value="">Select Exam Type</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="editExamYear" class="form-label">Academic Year *</label>
              <input type="number" class="form-control" id="editExamYear" name="academic_year" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="editExamDescription" class="form-label">Description</label>
            <textarea class="form-control" id="editExamDescription" name="description" rows="3" placeholder="Enter exam description..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Exam</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Exam Type Modal -->
<div class="modal fade" id="addExamTypeModal" tabindex="-1" aria-labelledby="addExamTypeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addExamTypeForm">
        <div class="modal-header">
          <h5 class="modal-title" id="addExamTypeModalLabel">Add Exam Type</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="typeName" class="form-label">Type Name *</label>
            <input type="text" class="form-control" id="typeName" name="type_name" required>
          </div>
          <div class="mb-3">
            <label for="typeDescription" class="form-label">Description</label>
            <textarea class="form-control" id="typeDescription" name="description" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Add Exam Type</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Exam Type Modal -->
<div class="modal fade" id="editExamTypeModal" tabindex="-1" aria-labelledby="editExamTypeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editExamTypeForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editExamTypeModalLabel">Edit Exam Type</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editExamTypeId" name="id">
          <div class="mb-3">
            <label for="editExamTypeName" class="form-label">Type Name *</label>
            <input type="text" class="form-control" id="editExamTypeName" name="type_name" required>
          </div>
          <div class="mb-3">
            <label for="editExamTypeDescription" class="form-label">Description</label>
            <textarea class="form-control" id="editExamTypeDescription" name="description" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Update Exam Type</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this item? This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

<script src="../assets/js/exams.js"></script>
<?php include '../includes/footer.php'; ?> 