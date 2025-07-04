<?php include '../includes/header.php'; ?>
<?php
$exam_id = $_GET['exam_id'] ?? '';
if (!$exam_id) {
    echo '<div class="alert alert-danger">Exam ID required.</div>';
    include '../includes/footer.php';
    exit;
}
?>
<div class="container mt-4" id="examDetailsApp" data-exam-id="<?php echo htmlspecialchars($exam_id); ?>">
  <div class="d-flex align-items-center mb-3 justify-content-between">
    <div>
      <a href="exams.php" class="btn btn-secondary me-2">&larr; Back to Exams</a>
      <h2 class="mb-0 d-inline-block" id="examNameHeader">Exam Details</h2>
    </div>
    <div>
      <button class="btn btn-success" id="addSubjectBtn">
        <i class="fas fa-plus"></i> Add Subject
      </button>
    </div>
  </div>

  <!-- Exam Information Card -->
  <div class="row mb-4">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Exam Information</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Exam Name:</strong> <span id="examName">-</span></p>
              <p><strong>Exam Type:</strong> <span id="examType">-</span></p>
              <p><strong>Class:</strong> <span id="examClass">-</span></p>
              <p><strong>Department:</strong> <span id="examDepartment">-</span></p>
            </div>
            <div class="col-md-6">
              <p><strong>Section:</strong> <span id="examSection">-</span></p>
              <p><strong>Academic Year:</strong> <span id="examYear">-</span></p>
              <p><strong>Created By:</strong> <span id="examCreatedBy">-</span></p>
              <p><strong>Created Date:</strong> <span id="examCreatedDate">-</span></p>
              <p><strong>Subjects Count:</strong> <span id="subjectCount">-</span></p>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <p><strong>Description:</strong></p>
              <div id="examDescription" class="border rounded p-3 bg-light">-</div>
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
          <button class="btn btn-warning w-100 mb-2" id="generateReportBtn">
            <i class="fas fa-chart-bar"></i> Generate Report
          </button>
          <button class="btn btn-info w-100 mb-2" id="publishExamBtn">
            <i class="fas fa-globe"></i> Publish Exam
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Subjects Section -->
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Exam Subjects</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped" id="examSubjectsTable">
              <thead>
                <tr>
                  <th>Sl No.</th>
                  <th id="examSubjectsDeptCol">Department</th>
                  <th>Subject</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Teacher</th>
                  <th>Total Marks</th>
                  <th>Pass Mark</th>
                  <th>Status</th>
                  <th>Room</th>
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

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="addSubjectForm">
        <div class="modal-header">
          <h5 class="modal-title" id="addSubjectModalLabel">Add Subject to Exam</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="addSubjectExamId" name="exam_id">
          
          <div class="row" id="addSubjectDepartmentRow" style="display: none;">
            <div class="col-md-12 mb-3">
              <label for="addSubjectDepartment" class="form-label">Department *</label>
              <select class="form-select" id="addSubjectDepartment" name="department_id" required>
                <option value="">Select Department</option>
              </select>
              <div class="form-text">
                <i class="fas fa-info-circle text-info"></i>
                <small>Note: Select a department to filter available subjects and teachers. This will be stored with the exam subject.</small>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="addSubjectSelect" class="form-label">Subject *</label>
              <select class="form-select" id="addSubjectSelect" name="subject_id" required>
                <option value="">Select Department First</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="addSubjectTeacher" class="form-label">Responsible Teacher</label>
              <select class="form-select" id="addSubjectTeacher" name="teacher_id">
                <option value="">Select Subject First</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="addSubjectDate" class="form-label">Exam Date *</label>
              <input type="date" class="form-control" id="addSubjectDate" name="exam_date" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="addSubjectStartTime" class="form-label">Start Time</label>
              <input type="time" class="form-control" id="addSubjectStartTime" name="start_time" step="60">
            </div>
            <div class="col-md-4 mb-3">
              <label for="addSubjectEndTime" class="form-label">End Time</label>
              <input type="time" class="form-control" id="addSubjectEndTime" name="end_time" step="60">
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="addSubjectTotalMarks" class="form-label">Total Marks *</label>
              <input type="number" class="form-control" id="addSubjectTotalMarks" name="total_marks" min="1" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="addSubjectPassMark" class="form-label">Pass Mark *</label>
              <input type="number" class="form-control" id="addSubjectPassMark" name="pass_mark" min="1" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="addSubjectRoom" class="form-label">Room Number</label>
              <input type="text" class="form-control" id="addSubjectRoom" name="room_number">
            </div>
          </div>

          <div class="mb-3">
            <label for="addSubjectInstructions" class="form-label">Instructions</label>
            <textarea class="form-control" id="addSubjectInstructions" name="instructions" rows="3" placeholder="Enter exam instructions..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Subject</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal fade" id="editSubjectModal" tabindex="-1" aria-labelledby="editSubjectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editSubjectForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editSubjectModalLabel">Edit Exam Subject</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editSubjectId" name="exam_subject_id">
          <input type="hidden" id="editSubjectExamId" name="exam_id">
          
          <div class="row" id="editSubjectDepartmentRow" style="display: none;">
            <div class="col-md-12 mb-3">
              <label for="editSubjectDepartment" class="form-label">Department *</label>
              <select class="form-select" id="editSubjectDepartment" name="department_id" required>
                <option value="">Select Department</option>
              </select>
              <div class="form-text">
                <i class="fas fa-info-circle text-info"></i>
                <small>Note: Select a department to filter available subjects and teachers. This will be stored with the exam subject.</small>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="editSubjectSelect" class="form-label">Subject *</label>
              <select class="form-select" id="editSubjectSelect" name="subject_id" required>
                <option value="">Select Department First</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="editSubjectTeacher" class="form-label">Responsible Teacher</label>
              <select class="form-select" id="editSubjectTeacher" name="teacher_id">
                <option value="">Select Subject First</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="editSubjectDate" class="form-label">Exam Date *</label>
              <input type="date" class="form-control" id="editSubjectDate" name="exam_date" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="editSubjectStartTime" class="form-label">Start Time</label>
              <input type="time" class="form-control" id="editSubjectStartTime" name="start_time" step="60">
            </div>
            <div class="col-md-4 mb-3">
              <label for="editSubjectEndTime" class="form-label">End Time</label>
              <input type="time" class="form-control" id="editSubjectEndTime" name="end_time" step="60">
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="editSubjectTotalMarks" class="form-label">Total Marks *</label>
              <input type="number" class="form-control" id="editSubjectTotalMarks" name="total_marks" min="1" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="editSubjectPassMark" class="form-label">Pass Mark *</label>
              <input type="number" class="form-control" id="editSubjectPassMark" name="pass_mark" min="1" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="editSubjectRoom" class="form-label">Room Number</label>
              <input type="text" class="form-control" id="editSubjectRoom" name="room_number">
            </div>
          </div>

          <div class="mb-3">
            <label for="editSubjectInstructions" class="form-label">Instructions</label>
            <textarea class="form-control" id="editSubjectInstructions" name="instructions" rows="3" placeholder="Enter exam instructions..."></textarea>
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



<!-- Edit Grade Modal -->
<div class="modal fade" id="editGradeModal" tabindex="-1" aria-labelledby="editGradeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editGradeForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editGradeModalLabel">Edit Grade</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editGradeStudentId" name="student_id">
          <input type="hidden" id="editGradeExamId" name="exam_id">
          <div class="mb-3">
            <label for="editMarksObtained" class="form-label">Marks Obtained</label>
            <input type="number" class="form-control" id="editMarksObtained" name="marks_obtained" min="0" step="0.01" required>
          </div>
          <div class="mb-3">
            <label for="editGradeLetter" class="form-label">Grade Letter</label>
            <input type="text" class="form-control" id="editGradeLetter" name="grade_letter" maxlength="5">
          </div>
          <div class="mb-3">
            <label for="editGradePoint" class="form-label">Grade Point</label>
            <input type="number" class="form-control" id="editGradePoint" name="grade_point" min="0" max="5" step="0.01">
          </div>
          <div class="mb-3">
            <label for="editGradeRemarks" class="form-label">Remarks</label>
            <textarea class="form-control" id="editGradeRemarks" name="remarks" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Grade</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Attendance Modal -->
<div class="modal fade" id="editAttendanceModal" tabindex="-1" aria-labelledby="editAttendanceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editAttendanceForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editAttendanceModalLabel">Edit Attendance</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editAttendanceStudentId" name="student_id">
          <input type="hidden" id="editAttendanceExamId" name="exam_id">
          <div class="mb-3">
            <label for="editAttendanceStatus" class="form-label">Attendance Status</label>
            <select class="form-select" id="editAttendanceStatus" name="status" required>
              <option value="present">Present</option>
              <option value="absent">Absent</option>
              <option value="late">Late</option>
              <option value="excused">Excused</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="editAttendanceRemarks" class="form-label">Remarks</label>
            <textarea class="form-control" id="editAttendanceRemarks" name="remarks" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Attendance</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Exam Report Modal -->
<div class="modal fade" id="examReportModal" tabindex="-1" aria-labelledby="examReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="examReportModalLabel">Exam Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-success" id="downloadReportBtn">
                            <i class="fas fa-download"></i> Download CSV Report
                        </button>
                    </div>
                </div>
                
                <div id="reportContent">
                    <!-- Report content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/exam_details.js"></script>
<?php include '../includes/footer.php'; ?> 