<?php include '../includes/header.php'; ?>
<?php
$exam_subject_id = $_GET['exam_subject_id'] ?? '';
if (!$exam_subject_id) {
    echo '<div class="alert alert-danger">Exam Subject ID required.</div>';
    include '../includes/footer.php';
    exit;
}
?>
<div class="container mt-4" id="examSubjectDetailsApp" data-exam-subject-id="<?php echo htmlspecialchars($exam_subject_id); ?>">
  <div class="d-flex align-items-center mb-3 justify-content-between">
    <div>
      <a href="exam_details.php?exam_id=" id="backToExamLink" class="btn btn-secondary me-2">&larr; Back to Exam</a>
      <h2 class="mb-0 d-inline-block" id="examSubjectNameHeader">Exam Subject Details</h2>
    </div>
    <div>
      <button class="btn btn-success me-2" id="addGradeBtn">
        <i class="fas fa-plus"></i> Add Grade
      </button>
    </div>
  </div>

  <!-- Exam Subject Information Card -->
  <div class="row mb-4">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Subject Information</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Subject:</strong> <span id="subjectName">-</span></p>
              <p><strong>Exam Date:</strong> <span id="examDate">-</span></p>
              <p><strong>Time:</strong> <span id="examTime">-</span></p>
              <p><strong>Teacher:</strong> <span id="teacherName">-</span></p>
              <p><strong>Room:</strong> <span id="roomNumber">-</span></p>
            </div>
            <div class="col-md-6">
              <p><strong>Total Marks:</strong> <span id="totalMarks">-</span></p>
              <p><strong>Pass Mark:</strong> <span id="passMark">-</span></p>
              <p><strong>Status:</strong> <span id="examStatus">-</span></p>
              <p><strong>Students:</strong> <span id="studentCount">-</span></p>
              <p><strong>Grades:</strong> <span id="gradeCount">-</span></p>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <p><strong>Instructions:</strong></p>
              <div id="instructions" class="border rounded p-3 bg-light">-</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Quick Actions</h5>
        </div>
        <div class="card-body">
          <button class="btn btn-warning w-100 mb-2" id="takeAttendanceBtn">
            <i class="fas fa-clipboard-check"></i> Take Attendance
          </button>
          <button class="btn btn-info w-100 mb-2" id="gradeStudentsBtn">
            <i class="fas fa-star"></i> Grade Students
          </button>
          <button class="btn btn-success w-100 mb-2" id="generateSubjectReportBtn">
            <i class="fas fa-chart-bar"></i> Generate Report
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Grades Section -->
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Student Grades</h5>
          <button class="btn btn-sm btn-primary" id="bulkGradeBtn">
            <i class="fas fa-edit"></i> Bulk Grade
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped" id="gradesTable">
              <thead>
                <tr>
                  <th>Roll No</th>
                  <th>Student Name</th>
                  <th>Marks Obtained</th>
                  <th>Total Marks</th>
                  <th>Percentage</th>
                  <th>Grade</th>
                  <th>Grade Point</th>
                  <th>Remarks</th>
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
  </div>

  <!-- Attendance Section -->
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Exam Attendance</h5>
          <button class="btn btn-sm btn-primary" id="bulkAttendanceBtn">
            <i class="fas fa-clipboard-check"></i> Bulk Attendance
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped" id="attendanceTable">
              <thead>
                <tr>
                  <th>Roll No</th>
                  <th>Student Name</th>
                  <th>Status</th>
                  <th>Remarks</th>
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
  </div>
</div>

<!-- Add Grade Modal -->
<div class="modal fade" id="addGradeModal" tabindex="-1" aria-labelledby="addGradeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addGradeForm">
        <div class="modal-header">
          <h5 class="modal-title" id="addGradeModalLabel">Add Grade</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="addGradeExamSubjectId" name="exam_subject_id">
          <input type="hidden" id="addGradeStudentId" name="student_id">
          
          <div class="mb-3">
            <label for="addGradeStudentName" class="form-label">Student</label>
            <input type="text" class="form-control" id="addGradeStudentName" readonly>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="addGradeMarksObtained" class="form-label">Marks Obtained *</label>
              <input type="number" class="form-control" id="addGradeMarksObtained" name="marks_obtained" min="0" step="0.01" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="addGradeTotalMarks" class="form-label">Total Marks</label>
              <input type="number" class="form-control" id="addGradeTotalMarks" readonly>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="addGradeRemarks" class="form-label">Remarks</label>
            <textarea class="form-control" id="addGradeRemarks" name="remarks" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Grade</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Exam Subject Modal -->
<div class="modal fade" id="editExamSubjectModal" tabindex="-1" aria-labelledby="editExamSubjectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editExamSubjectForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editExamSubjectModalLabel">Edit Exam Subject</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editExamSubjectId" name="exam_subject_id">
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="editSubjectName" class="form-label">Subject</label>
              <input type="text" class="form-control" id="editSubjectName" readonly>
            </div>
            <div class="col-md-6 mb-3">
              <label for="editTeacher" class="form-label">Responsible Teacher</label>
              <select class="form-select" id="editTeacher" name="teacher_id">
                <option value="">Select Teacher</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="editExamDate" class="form-label">Exam Date *</label>
              <input type="date" class="form-control" id="editExamDate" name="exam_date" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="editStartTime" class="form-label">Start Time</label>
              <input type="time" class="form-control" id="editStartTime" name="start_time">
            </div>
            <div class="col-md-4 mb-3">
              <label for="editEndTime" class="form-label">End Time</label>
              <input type="time" class="form-control" id="editEndTime" name="end_time">
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="editTotalMarks" class="form-label">Total Marks *</label>
              <input type="number" class="form-control" id="editTotalMarks" name="total_marks" min="1" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="editPassMark" class="form-label">Pass Mark *</label>
              <input type="number" class="form-control" id="editPassMark" name="pass_mark" min="1" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="editRoomNumber" class="form-label">Room Number</label>
              <input type="text" class="form-control" id="editRoomNumber" name="room_number">
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="editExamStatus" class="form-label">Status</label>
              <select class="form-select" id="editExamStatus" name="exam_status">
                <option value="scheduled">Scheduled</option>
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label for="editInstructions" class="form-label">Instructions</label>
            <textarea class="form-control" id="editInstructions" name="instructions" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Subject</button>
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

<script src="../assets/js/exam_subject_details.js"></script>
<?php include '../includes/footer.php'; ?> 