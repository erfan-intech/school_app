<?php
include '../includes/header.php';
include '../includes/db_connect.php';
$student_id = $_GET['id'] ?? '';
if (!$student_id) {
    echo '<div class="alert alert-danger">Student ID required.</div>';
    include '../includes/footer.php';
    exit;
}
// Fetch student info
$sql = "SELECT s.*, c.name AS class_name, d.name AS department_name, sec.name AS section_name,
    f.first_name AS father_first_name, f.last_name AS father_last_name, f.phone AS father_phone,
    m.first_name AS mother_first_name, m.last_name AS mother_last_name, m.phone AS mother_phone,
    lg.first_name AS local_guardian_first_name, lg.last_name AS local_guardian_last_name, lg.phone AS local_guardian_phone
    FROM students s
    LEFT JOIN classes c ON s.current_class_id = c.id
    LEFT JOIN departments d ON s.current_department_id = d.id
    LEFT JOIN sections sec ON s.current_section_id = sec.id
    LEFT JOIN parents f ON s.father_id = f.id
    LEFT JOIN parents m ON s.mother_id = m.id
    LEFT JOIN parents lg ON s.local_guardian_id = lg.id
    WHERE s.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();
if (!$student) {
    echo '<div class="alert alert-danger">Student not found.</div>';
    include '../includes/footer.php';
    exit;
}
// Fetch attendance history
$attendance = [];
$sql = "SELECT date, status, time_in, time_out FROM attendance WHERE user_id=? AND user_type='student' ORDER BY date DESC LIMIT 30";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $student['user_id']);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $attendance[] = $row;
}
$stmt->close();
// Fetch grades and mark sheets (using new grades and exams tables)
$grades = [];
$sql = "SELECT e.exam_name, e.exam_date, s.name AS subject_name, g.marks_obtained, g.total_marks
        FROM grades g
        JOIN exams e ON g.exam_id = e.id
        JOIN subjects s ON g.subject_id = s.id
        WHERE g.student_id=?
        ORDER BY e.exam_date DESC, e.exam_name, s.name";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $grades[] = $row;
}
$stmt->close();
// Group grades by exam for mark sheets
$marksheets = [];
foreach ($grades as $g) {
    $marksheets[$g['exam_name'] . ' (' . date('Y', strtotime($g['exam_date'])) . ')'][] = $g;
}
?>
<div class="container mt-4">
  <a href="students.php" class="btn btn-secondary mb-3">&larr; Back to Students</a>
  <button class="btn btn-outline-primary mb-3 float-end" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
  <div class="card shadow">
    <div class="card-body">
      <ul class="nav nav-tabs mt-4" id="profileTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profileTab" type="button" role="tab">Profile</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendanceTab" type="button" role="tab">Attendance History</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="grades-tab" data-bs-toggle="tab" data-bs-target="#gradesTab" type="button" role="tab">Grades</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="marksheet-tab" data-bs-toggle="tab" data-bs-target="#marksheetTab" type="button" role="tab">Exam Mark Sheets</button>
        </li>
      </ul>
      <div class="tab-content p-3 border border-top-0 bg-white" id="profileTabsContent">
        <div class="tab-pane fade show active" id="profileTab" role="tabpanel">
          <div class="row align-items-center">
            <div class="col-md-3 text-center mb-3 mb-md-0">
              <?php if ($student['profile_picture']): ?>
                <img src="../uploads/students/<?php echo htmlspecialchars($student['profile_picture']); ?>" alt="Profile" class="img-fluid rounded-circle" style="width: 140px; height: 140px; object-fit: cover;">
              <?php else: ?>
                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 140px; height: 140px; color: #fff; font-size: 2.5rem;">
                  <span><?php echo strtoupper(substr($student['first_name'], 0, 1)); ?></span>
                </div>
              <?php endif; ?>
            </div>
            <div class="col-md-9">
              <h2 class="mb-1"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h2>
              <div class="mb-2">
                <span class="badge bg-primary me-2">Class: <?php echo htmlspecialchars($student['class_name']); ?></span>
                <?php if ($student['department_name']): ?>
                  <span class="badge bg-info me-2">Department: <?php echo htmlspecialchars($student['department_name']); ?></span>
                <?php endif; ?>
                <?php if ($student['section_name']): ?>
                  <span class="badge bg-secondary">Section: <?php echo htmlspecialchars($student['section_name']); ?></span>
                <?php endif; ?>
              </div>
              <div class="mb-2">
                <span class="badge bg-dark">Roll No: <?php echo htmlspecialchars($student['roll_no']); ?></span>
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-6">
              <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Date of Birth:</strong> <?php echo $student['dob'] && $student['dob'] !== '0000-00-00' ? htmlspecialchars($student['dob']) : 'N/A'; ?></li>
                <li class="list-group-item"><strong>Gender:</strong> <?php echo htmlspecialchars($student['gender'] ?: 'N/A'); ?></li>
                <li class="list-group-item"><strong>Address:</strong> <?php echo htmlspecialchars($student['address'] ?: 'N/A'); ?></li>
                <li class="list-group-item"><strong>Admission Date:</strong> <?php echo $student['admission_date'] && $student['admission_date'] !== '0000-00-00' ? htmlspecialchars($student['admission_date']) : 'N/A'; ?></li>
              </ul>
            </div>
            <div class="col-md-6">
              <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Father:</strong> <?php echo $student['father_first_name'] ? htmlspecialchars($student['father_first_name'] . ' ' . $student['father_last_name']) . ($student['father_phone'] ? ' (' . htmlspecialchars($student['father_phone']) . ')' : '') : 'N/A'; ?></li>
                <li class="list-group-item"><strong>Mother:</strong> <?php echo $student['mother_first_name'] ? htmlspecialchars($student['mother_first_name'] . ' ' . $student['mother_last_name']) . ($student['mother_phone'] ? ' (' . htmlspecialchars($student['mother_phone']) . ')' : '') : 'N/A'; ?></li>
                <li class="list-group-item"><strong>Local Guardian:</strong> <?php echo $student['local_guardian_first_name'] ? htmlspecialchars($student['local_guardian_first_name'] . ' ' . $student['local_guardian_last_name']) . ($student['local_guardian_phone'] ? ' (' . htmlspecialchars($student['local_guardian_phone']) . ')' : '') : 'N/A'; ?></li>
                <li class="list-group-item"><strong>Note:</strong> <?php echo htmlspecialchars($student['note'] ?: 'N/A'); ?></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="attendanceTab" role="tabpanel">
          <h5>Recent Attendance (Last 30)</h5>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead><tr><th>Date</th><th>Status</th><th>Time In</th><th>Time Out</th></tr></thead>
              <tbody>
                <?php foreach ($attendance as $a): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($a['date']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($a['status'])); ?></td>
                    <td><?php echo htmlspecialchars($a['time_in']); ?></td>
                    <td><?php echo htmlspecialchars($a['time_out']); ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($attendance)): ?><tr><td colspan="4">No attendance records found.</td></tr><?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="tab-pane fade" id="gradesTab" role="tabpanel">
          <h5>Grades</h5>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead><tr><th>Exam</th><th>Year</th><th>Subject</th><th>Marks Obtained</th><th>Total Marks</th></tr></thead>
              <tbody>
                <?php foreach ($grades as $g): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($g['exam_name']); ?></td>
                    <td><?php echo date('Y', strtotime($g['exam_date'])); ?></td>
                    <td><?php echo htmlspecialchars($g['subject_name']); ?></td>
                    <td><?php echo htmlspecialchars($g['marks_obtained']); ?></td>
                    <td><?php echo htmlspecialchars($g['total_marks']); ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($grades)): ?><tr><td colspan="5">No grades found.</td></tr><?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="tab-pane fade" id="marksheetTab" role="tabpanel">
          <h5>Exam Mark Sheets</h5>
          <?php if (!empty($marksheets)): ?>
            <?php foreach ($marksheets as $exam => $marks): ?>
              <div class="card mb-3">
                <div class="card-header bg-primary text-white"><strong><?php echo htmlspecialchars($exam); ?></strong></div>
                <div class="card-body p-2">
                  <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                      <thead><tr><th>Subject</th><th>Marks Obtained</th><th>Total Marks</th></tr></thead>
                      <tbody>
                        <?php foreach ($marks as $m): ?>
                          <tr>
                            <td><?php echo htmlspecialchars($m['subject_name']); ?></td>
                            <td><?php echo htmlspecialchars($m['marks_obtained']); ?></td>
                            <td><?php echo htmlspecialchars($m['total_marks']); ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="alert alert-info">No mark sheets found.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
<?php $conn->close(); ?> 