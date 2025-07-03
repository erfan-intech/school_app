<?php include '../includes/header.php'; ?>
<?php
include '../includes/db_connect.php';
// Fetch classes for dropdown
$classes = [];
$result = $conn->query("SELECT id, name FROM classes");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}
$conn->close();
?>
<div class="container mt-4">
    <div class="card mb-5">
        <div class="card-header bg-primary text-white"><h3 class="mb-0">Students Attendance Panel</h3></div>
        <div class="card-body">
            <h5 class="mb-3">Students Attendance Marking</h5>
            <div class="row g-2 align-items-end mb-4">
                <div class="col-12 col-md-3">
                    <label for="attendanceClass" class="form-label">Class</label>
                    <select class="form-select" id="attendanceClass">
                        <option value="">Select Class</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label for="attendanceStudent" class="form-label">Student</label>
                    <select class="form-select" id="attendanceStudent" disabled>
                        <option value="">Select Student</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label for="attendanceDate" class="form-label">Date</label>
                    <input type="date" class="form-control" id="attendanceDate" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-12 col-md-3 d-grid">
                    <button class="btn btn-primary" id="loadStudentsBtn">Load Students</button>
                </div>
            </div>
            <!-- Bulk Attendance Table -->
            <form id="attendanceForm" style="display:none;">
                <div id="studentsAttendanceSection">
                    <h6>Mark Attendance (Bulk)</h6>
                    <table class="table table-bordered" id="studentsAttendanceTable">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <button type="submit" class="btn btn-success">Mark Attendance</button>
                </div>
            </form>
            <!-- Single Student Attendance Form -->
            <form id="singleAttendanceForm" style="display:none;">
                <div id="singleAttendanceSection">
                    <h6>Mark Attendance (Single Student)</h6>
                    <input type="hidden" name="student_id" id="singleStudentId">
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="singleStatus" class="form-label">Status</label>
                            <select class="form-select" id="singleStatus" name="status">
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="late">Late</option>
                                <option value="excused">Excused</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="singleTimeIn" class="form-label">Time In</label>
                            <input type="time" class="form-control" id="singleTimeIn" name="time_in">
                        </div>
                        <div class="col-md-3">
                            <label for="singleTimeOut" class="form-label">Time Out</label>
                            <input type="time" class="form-control" id="singleTimeOut" name="time_out">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Mark Attendance</button>
                </div>
            </form>
            <hr class="my-5">
            <h5 class="mb-3">View Students Attendance</h5>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="filterClass" class="form-label">Class</label>
                    <select class="form-select" id="filterClass">
                        <option value="">All Classes</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filterDate" class="form-label">Date</label>
                    <input type="date" class="form-control" id="filterDate" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-secondary" id="filterAttendanceBtn">Filter</button>
                </div>
            </div>
            <table class="table table-bordered" id="attendanceRecordsTable">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Roll No</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div class="card mb-5">
        <div class="card-header bg-info text-white"><h3 class="mb-0">Teachers Attendance Panel</h3></div>
        <div class="card-body">
            <h5 class="mb-3">Teachers Attendance Marking</h5>
            <div class="row g-2 align-items-end mb-4">
                <div class="col-12 col-md-4">
                    <label for="markTeacherAttendanceTeacher" class="form-label">Teacher</label>
                    <select class="form-select" id="markTeacherAttendanceTeacher">
                        <option value="all" selected>All Teachers</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label for="markTeacherAttendanceDate" class="form-label">Date</label>
                    <input type="date" class="form-control" id="markTeacherAttendanceDate" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-12 col-md-4 d-grid">
                    <button class="btn btn-primary" id="loadMarkTeacherAttendanceBtn">Load Teachers</button>
                </div>
            </div>
            <form id="markTeacherAttendanceForm" style="display:none;">
                <div id="teachersAttendanceSection">
                    <h6>Mark Attendance (Bulk)</h6>
                    <table class="table table-bordered" id="teachersAttendanceTable">
                        <thead>
                            <tr>
                                <th>Teacher ID</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <button type="submit" class="btn btn-success">Mark Attendance</button>
                </div>
            </form>
            <hr class="my-5">
            <h5 class="mb-3">View Teachers Attendance</h5>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="attendanceTeacher" class="form-label">Teacher</label>
                    <select class="form-select" id="attendanceTeacher">
                        <option value="all" selected>All Teachers</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="attendanceTeacherDate" class="form-label">Date</label>
                    <input type="date" class="form-control" id="attendanceTeacherDate" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-secondary" id="filterTeacherAttendanceBtn">Filter</button>
                </div>
            </div>
            <table class="table table-bordered" id="teacherAttendanceRecordsTable">
                <thead>
                    <tr>
                        <th>Teacher ID</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <!-- Edit Attendance Modal (for both students and teachers) -->
    <div class="modal fade" id="editAttendanceModal" tabindex="-1" aria-labelledby="editAttendanceModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="editAttendanceForm">
            <div class="modal-header">
              <h5 class="modal-title" id="editAttendanceModalLabel">Edit Attendance</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" id="editAttendanceId" name="attendance_id">
              <input type="hidden" id="editAttendanceUserType" name="user_type">
              <div class="mb-3">
                <label for="editStatus" class="form-label">Status</label>
                <select class="form-select" id="editStatus" name="status">
                  <option value="present">Present</option>
                  <option value="absent">Absent</option>
                  <option value="late">Late</option>
                  <option value="excused">Excused</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="editTimeIn" class="form-label">Time In</label>
                <input type="time" class="form-control" id="editTimeIn" name="time_in">
              </div>
              <div class="mb-3">
                <label for="editTimeOut" class="form-label">Time Out</label>
                <input type="time" class="form-control" id="editTimeOut" name="time_out">
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
</div>
<script src="../assets/js/attendance.js"></script>
<?php include '../includes/footer.php'; ?>
