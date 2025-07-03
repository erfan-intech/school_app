<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$id = $_POST['id'] ?? '';
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Class ID required.']);
    exit;
}

// Check if class exists and is not already deleted
$check_stmt = $conn->prepare('SELECT id, name FROM classes WHERE id=? AND is_deleted=0');
$check_stmt->bind_param('i', $id);
$check_stmt->execute();
$result = $check_stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Class not found or already deleted.']);
    $check_stmt->close();
    $conn->close();
    exit;
}
$class = $result->fetch_assoc();
$check_stmt->close();

// Check dependencies - see if class is used in students
$student_stmt = $conn->prepare('SELECT COUNT(*) as count FROM students WHERE current_class_id=?');
if ($student_stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error: Unable to prepare students query.']);
    $conn->close();
    exit;
}
$student_stmt->bind_param('i', $id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();
$student_count = $student_result->fetch_assoc()['count'];
$student_stmt->close();

if ($student_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete class '{$class['name']}'. It has {$student_count} student(s) enrolled. Please reassign or remove all students first."
    ]);
    $conn->close();
    exit;
}

            // Check if class is used in class_dept_sec
        $class_dept_stmt = $conn->prepare('SELECT COUNT(*) as count FROM class_dept_sec WHERE class_id=? AND is_deleted = 0');
if ($class_dept_stmt === false) {
            echo json_encode(['success' => false, 'message' => 'Database error: Unable to prepare class_dept_sec query.']);
    $conn->close();
    exit;
}
$class_dept_stmt->bind_param('i', $id);
$class_dept_stmt->execute();
$class_dept_result = $class_dept_stmt->get_result();
$class_dept_count = $class_dept_result->fetch_assoc()['count'];
$class_dept_stmt->close();

if ($class_dept_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete class '{$class['name']}'. It has {$class_dept_count} department assignment(s). Please remove all department assignments first."
    ]);
    $conn->close();
    exit;
}

        // Check if class is used in class_dept_sub
        $class_subject_stmt = $conn->prepare('SELECT COUNT(*) as count FROM class_dept_sub WHERE class_id=? AND is_deleted = 0');
if ($class_subject_stmt === false) {
            echo json_encode(['success' => false, 'message' => 'Database error: Unable to prepare class_dept_sub query.']);
    $conn->close();
    exit;
}
$class_subject_stmt->bind_param('i', $id);
$class_subject_stmt->execute();
$class_subject_result = $class_subject_stmt->get_result();
$class_subject_count = $class_subject_result->fetch_assoc()['count'];
$class_subject_stmt->close();

if ($class_subject_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete class '{$class['name']}'. It has {$class_subject_count} subject assignment(s). Please remove all subject assignments first."
    ]);
    $conn->close();
    exit;
}

        // Check if class is used in class_dept_sub_teacher
        $class_teacher_stmt = $conn->prepare('SELECT COUNT(*) as count FROM class_dept_sub_teacher WHERE class_id=? AND is_deleted = 0');
if ($class_teacher_stmt === false) {
            echo json_encode(['success' => false, 'message' => 'Database error: Unable to prepare class_dept_sub_teacher query.']);
    $conn->close();
    exit;
}
$class_teacher_stmt->bind_param('i', $id);
$class_teacher_stmt->execute();
$class_teacher_result = $class_teacher_stmt->get_result();
$class_teacher_count = $class_teacher_result->fetch_assoc()['count'];
$class_teacher_stmt->close();

if ($class_teacher_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete class '{$class['name']}'. It has {$class_teacher_count} teacher assignment(s). Please remove all teacher assignments first."
    ]);
    $conn->close();
    exit;
}





// Check if class is used in exams
$exam_stmt = $conn->prepare('SELECT COUNT(*) as count FROM exams WHERE class_id=?');
if ($exam_stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error: Unable to prepare exams query.']);
    $conn->close();
    exit;
}
$exam_stmt->bind_param('i', $id);
$exam_stmt->execute();
$exam_result = $exam_stmt->get_result();
$exam_count = $exam_result->fetch_assoc()['count'];
$exam_stmt->close();

if ($exam_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete class '{$class['name']}'. It is used in {$exam_count} exam(s). Please remove all exam references first."
    ]);
    $conn->close();
    exit;
}

// Check if class is used in student_history
$history_stmt = $conn->prepare('SELECT COUNT(*) as count FROM student_history WHERE class_id=?');
if ($history_stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error: Unable to prepare student_history query.']);
    $conn->close();
    exit;
}
$history_stmt->bind_param('i', $id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
$history_count = $history_result->fetch_assoc()['count'];
$history_stmt->close();

if ($history_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete class '{$class['name']}'. It has {$history_count} student history record(s). Please remove all history records first."
    ]);
    $conn->close();
    exit;
}

// Check if class is used in timetable
$timetable_stmt = $conn->prepare('SELECT COUNT(*) as count FROM timetable WHERE class_id=?');
if ($timetable_stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error: Unable to prepare timetable query.']);
    $conn->close();
    exit;
}
$timetable_stmt->bind_param('i', $id);
$timetable_stmt->execute();
$timetable_result = $timetable_stmt->get_result();
$timetable_count = $timetable_result->fetch_assoc()['count'];
$timetable_stmt->close();

if ($timetable_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete class '{$class['name']}'. It is scheduled in {$timetable_count} timetable entry(ies). Please remove all timetable entries first."
    ]);
    $conn->close();
    exit;
}

// Check if class is used in attendance
$attendance_stmt = $conn->prepare('SELECT COUNT(*) as count FROM attendance WHERE class_id=?');
if ($attendance_stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error: Unable to prepare attendance query.']);
    $conn->close();
    exit;
}
$attendance_stmt->bind_param('i', $id);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();
$attendance_count = $attendance_result->fetch_assoc()['count'];
$attendance_stmt->close();

if ($attendance_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete class '{$class['name']}'. It has {$attendance_count} attendance record(s). Please remove all attendance records first."
    ]);
    $conn->close();
    exit;
}

// If no dependencies found, perform soft delete
$stmt = $conn->prepare('UPDATE classes SET is_deleted=1 WHERE id=?');
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error: Unable to prepare update query.']);
    $conn->close();
    exit;
}
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => "Class '{$class['name']}' has been deleted successfully."
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete class.']);
}
$stmt->close();
$conn->close();
?>
