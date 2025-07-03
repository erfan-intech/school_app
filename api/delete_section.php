<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$id = $_POST['id'] ?? '';
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Section ID required.']);
    exit;
}

// Check if section exists and is not already deleted
$check_stmt = $conn->prepare('SELECT id, name FROM sections WHERE id=? AND is_deleted=0');
$check_stmt->bind_param('i', $id);
$check_stmt->execute();
$result = $check_stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Section not found or already deleted.']);
    $check_stmt->close();
    $conn->close();
    exit;
}
$section = $result->fetch_assoc();
$check_stmt->close();

// Check dependencies - see if section is used in students
$student_stmt = $conn->prepare('SELECT COUNT(*) as count FROM students WHERE current_section_id=?');
$student_stmt->bind_param('i', $id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();
$student_count = $student_result->fetch_assoc()['count'];
$student_stmt->close();

if ($student_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete section '{$section['name']}'. It has {$student_count} student(s) assigned. Please reassign or remove all students first."
    ]);
    $conn->close();
    exit;
}

            // Check if section is used in class_dept_sec
        $class_dept_stmt = $conn->prepare('SELECT COUNT(*) as count FROM class_dept_sec WHERE section_id=? AND is_deleted = 0');
$class_dept_stmt->bind_param('i', $id);
$class_dept_stmt->execute();
$class_dept_result = $class_dept_stmt->get_result();
$class_dept_count = $class_dept_result->fetch_assoc()['count'];
$class_dept_stmt->close();

if ($class_dept_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete section '{$section['name']}'. It is assigned to {$class_dept_count} class-department combination(s). Please remove all class assignments first."
    ]);
    $conn->close();
    exit;
}

// Check if section is used in exams
$exam_stmt = $conn->prepare('SELECT COUNT(*) as count FROM exams WHERE section_id=?');
$exam_stmt->bind_param('i', $id);
$exam_stmt->execute();
$exam_result = $exam_stmt->get_result();
$exam_count = $exam_result->fetch_assoc()['count'];
$exam_stmt->close();

if ($exam_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete section '{$section['name']}'. It is used in {$exam_count} exam(s). Please remove all exam references first."
    ]);
    $conn->close();
    exit;
}

// Check if section is used in student_history
$history_stmt = $conn->prepare('SELECT COUNT(*) as count FROM student_history WHERE section_id=?');
$history_stmt->bind_param('i', $id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
$history_count = $history_result->fetch_assoc()['count'];
$history_stmt->close();

if ($history_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete section '{$section['name']}'. It has {$history_count} student history record(s). Please remove all history records first."
    ]);
    $conn->close();
    exit;
}

// If no dependencies found, perform soft delete
$stmt = $conn->prepare('UPDATE sections SET is_deleted=1 WHERE id=?');
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => "Section '{$section['name']}' has been deleted successfully."
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete section.']);
}
$stmt->close();
$conn->close();
?> 