<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$id = $_POST['id'] ?? '';
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Teacher ID required.']);
    exit;
}

// Check if teacher exists and is not already deleted
$check_stmt = $conn->prepare('SELECT id, first_name, last_name FROM teachers WHERE id=? AND is_deleted=0');
$check_stmt->bind_param('i', $id);
$check_stmt->execute();
$result = $check_stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Teacher not found or already deleted.']);
    $check_stmt->close();
    $conn->close();
    exit;
}
$teacher = $result->fetch_assoc();
$check_stmt->close();

    // Check dependencies - see if teacher is used in class_dept_sub_teacher
    $class_teacher_stmt = $conn->prepare('SELECT COUNT(*) as count FROM class_dept_sub_teacher WHERE teacher_id=? AND is_deleted = 0');
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
        'message' => "Cannot delete teacher '{$teacher['first_name']} {$teacher['last_name']}'. They are assigned to {$class_teacher_count} class(es). Please remove all class assignments first."
    ]);
    $conn->close();
    exit;
}

// Check if teacher is used in exams
$exam_stmt = $conn->prepare('SELECT COUNT(*) as count FROM exams WHERE teacher_id=?');
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
        'message' => "Cannot delete teacher '{$teacher['first_name']} {$teacher['last_name']}'. They are assigned to {$exam_count} exam(s). Please remove all exam assignments first."
    ]);
    $conn->close();
    exit;
}

// Check if teacher is used in timetable
$timetable_stmt = $conn->prepare('SELECT COUNT(*) as count FROM timetable WHERE teacher_id=?');
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
        'message' => "Cannot delete teacher '{$teacher['first_name']} {$teacher['last_name']}'. They are scheduled in {$timetable_count} timetable entry(ies). Please remove all timetable entries first."
    ]);
    $conn->close();
    exit;
}

// If no dependencies found, perform soft delete
$stmt = $conn->prepare('UPDATE teachers SET is_deleted=1 WHERE id=?');
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error: Unable to prepare update query.']);
    $conn->close();
    exit;
}
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => "Teacher '{$teacher['first_name']} {$teacher['last_name']}' has been deleted successfully."
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete teacher.']);
}
$stmt->close();
$conn->close();
?>
