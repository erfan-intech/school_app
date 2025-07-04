<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';
$class_id = $_GET['class_id'] ?? '';
if (!$class_id) {
    echo json_encode(['success' => false, 'message' => 'Class ID required.']);
    exit;
}
// Get assigned departments (independent departments - department_id exists, section_id is null)
$departments = [];
$sql = 'SELECT DISTINCT d.id, d.name FROM class_dept_sec cd JOIN departments d ON cd.department_id = d.id WHERE cd.class_id=? AND cd.department_id IS NOT NULL AND cd.section_id IS NULL AND cd.is_deleted = 0';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $class_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $departments[] = $row;
}
$stmt->close();
// Get assigned subjects (with/without department)
$subjects = [];
$sql = 'SELECT cs.id, cs.subject_id, s.name, cs.department_id, d.name AS department_name FROM class_dept_sub cs JOIN subjects s ON cs.subject_id = s.id LEFT JOIN departments d ON cs.department_id = d.id WHERE cs.class_id=? AND cs.is_deleted = 0';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $class_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}
$stmt->close();
// Get assigned teachers (with subjects)
$teachers = [];
$sql = 'SELECT ct.id, t.id AS teacher_id, t.user_id AS teacher_user_id, t.first_name, t.last_name, t.profile_picture, s.id AS subject_id, s.name AS subject_name, ct.department_id, d.name AS department_name FROM class_dept_sub_teacher ct JOIN teachers t ON ct.teacher_id = t.id LEFT JOIN subjects s ON ct.subject_id = s.id LEFT JOIN departments d ON ct.department_id = d.id WHERE ct.class_id=? AND ct.is_deleted = 0';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $class_id);
$stmt->execute();
$result = $stmt->get_result();
$today = date('Y-m-d');
while ($row = $result->fetch_assoc()) {
    // Get today's attendance status for this teacher
    $attendance_sql = "SELECT status FROM attendance WHERE user_id=? AND user_type='teacher' AND date=? LIMIT 1";
    $attendance_stmt = $conn->prepare($attendance_sql);
    $attendance_stmt->bind_param('is', $row['teacher_user_id'], $today);
    $attendance_stmt->execute();
    $attendance_stmt->bind_result($attendance_status);
    if ($attendance_stmt->fetch()) {
        $row['attendance_status_today'] = ucfirst($attendance_status);
    } else {
        $row['attendance_status_today'] = '';
    }
    $attendance_stmt->close();
    $teachers[] = $row;
}
$stmt->close();
// Get enrolled students
$students = [];
$sql = 'SELECT s.id, s.user_id, s.first_name, s.last_name, s.profile_picture, s.current_department_id, s.current_section_id, d.name AS department_name, sec.name AS section_name FROM students s LEFT JOIN departments d ON s.current_department_id = d.id LEFT JOIN sections sec ON s.current_section_id = sec.id WHERE s.current_class_id=? AND s.is_deleted=0';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $class_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
$stmt->close();
// Get today's attendance for students
$attendance = [];
$date = date('Y-m-d');
$sql = 'SELECT a.user_id, a.status FROM attendance a WHERE a.class_id=? AND a.date=? AND a.user_type="student"';
$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $class_id, $date);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $attendance[$row['user_id']] = $row['status'];
}
$stmt->close();
// Attach attendance status to students
foreach ($students as &$student) {
    $student['attendance'] = isset($attendance[$student['user_id']]) ? $attendance[$student['user_id']] : null;
}
// Get class name
$class_name = '';
$sql = 'SELECT name FROM classes WHERE id=?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $class_id);
$stmt->execute();
$stmt->bind_result($class_name);
$stmt->fetch();
$stmt->close();
// Get assigned sections (both independent and with departments)
$sections = [];
$sql = 'SELECT DISTINCT s.id, s.name, cd.department_id, d.name AS department_name FROM class_dept_sec cd JOIN sections s ON cd.section_id = s.id LEFT JOIN departments d ON cd.department_id = d.id WHERE cd.class_id = ? AND cd.section_id IS NOT NULL AND cd.is_deleted = 0';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $class_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $sections[] = $row;
}
$stmt->close();
echo json_encode([
    'success' => true,
    'class_name' => $class_name,
    'departments' => $departments,
    'subjects' => $subjects,
    'teachers' => $teachers,
    'students' => $students,
    'sections' => $sections
]);
$conn->close(); 