<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$id = $_POST['id'] ?? '';
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Subject ID required.']);
    exit;
}

// Check if subject exists and is not already deleted
$check_stmt = $conn->prepare('SELECT id, name FROM subjects WHERE id=? AND is_deleted=0');
$check_stmt->bind_param('i', $id);
$check_stmt->execute();
$result = $check_stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Subject not found or already deleted.']);
    $check_stmt->close();
    $conn->close();
    exit;
}
$subject = $result->fetch_assoc();
$check_stmt->close();

    // Check dependencies - see if subject is used in class_dept_sub
    $dependency_stmt = $conn->prepare('SELECT COUNT(*) as count FROM class_dept_sub WHERE subject_id=? AND is_deleted = 0');
$dependency_stmt->bind_param('i', $id);
$dependency_stmt->execute();
$dependency_result = $dependency_stmt->get_result();
$dependency_count = $dependency_result->fetch_assoc()['count'];
$dependency_stmt->close();

if ($dependency_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete subject '{$subject['name']}'. It is currently assigned to {$dependency_count} class(es). Please remove all class assignments first."
    ]);
    $conn->close();
    exit;
}

// Check if subject is used in exams
$exam_stmt = $conn->prepare('SELECT COUNT(*) as count FROM exams WHERE subject_id=?');
$exam_stmt->bind_param('i', $id);
$exam_stmt->execute();
$exam_result = $exam_stmt->get_result();
$exam_count = $exam_result->fetch_assoc()['count'];
$exam_stmt->close();

if ($exam_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete subject '{$subject['name']}'. It is used in {$exam_count} exam(s). Please remove all exam references first."
    ]);
    $conn->close();
    exit;
}

// Check if subject is used in grades
$grade_stmt = $conn->prepare('SELECT COUNT(*) as count FROM grades WHERE subject_id=?');
$grade_stmt->bind_param('i', $id);
$grade_stmt->execute();
$grade_result = $grade_stmt->get_result();
$grade_count = $grade_result->fetch_assoc()['count'];
$grade_stmt->close();

if ($grade_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete subject '{$subject['name']}'. It has {$grade_count} grade record(s). Please remove all grade records first."
    ]);
    $conn->close();
    exit;
}

// Check if subject is used in timetable
$timetable_stmt = $conn->prepare('SELECT COUNT(*) as count FROM timetable WHERE subject_id=?');
$timetable_stmt->bind_param('i', $id);
$timetable_stmt->execute();
$timetable_result = $timetable_stmt->get_result();
$timetable_count = $timetable_result->fetch_assoc()['count'];
$timetable_stmt->close();

if ($timetable_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete subject '{$subject['name']}'. It is scheduled in {$timetable_count} timetable entry(ies). Please remove all timetable entries first."
    ]);
    $conn->close();
    exit;
}

    // Check if subject is used in class_dept_sub_teacher
    $teacher_stmt = $conn->prepare('SELECT COUNT(*) as count FROM class_dept_sub_teacher WHERE subject_id=? AND is_deleted = 0');
$teacher_stmt->bind_param('i', $id);
$teacher_stmt->execute();
$teacher_result = $teacher_stmt->get_result();
$teacher_count = $teacher_result->fetch_assoc()['count'];
$teacher_stmt->close();

if ($teacher_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete subject '{$subject['name']}'. It is assigned to {$teacher_count} teacher(s). Please remove all teacher assignments first."
    ]);
    $conn->close();
    exit;
}

// If no dependencies found, perform soft delete
$stmt = $conn->prepare('UPDATE subjects SET is_deleted=1 WHERE id=?');
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => "Subject '{$subject['name']}' has been deleted successfully."
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete subject.']);
}
$stmt->close();
$conn->close(); 