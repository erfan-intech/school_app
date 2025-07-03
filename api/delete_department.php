<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$id = $_POST['id'] ?? '';
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Department ID required.']);
    exit;
}

// Check if department exists and is not already deleted
$check_stmt = $conn->prepare('SELECT id, name FROM departments WHERE id=? AND is_deleted=0');
$check_stmt->bind_param('i', $id);
$check_stmt->execute();
$result = $check_stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Department not found or already deleted.']);
    $check_stmt->close();
    $conn->close();
    exit;
}
$department = $result->fetch_assoc();
$check_stmt->close();

// Check dependencies - see if department is used in students
$student_stmt = $conn->prepare('SELECT COUNT(*) as count FROM students WHERE current_department_id=?');
$student_stmt->bind_param('i', $id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();
$student_count = $student_result->fetch_assoc()['count'];
$student_stmt->close();

if ($student_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete department '{$department['name']}'. It has {$student_count} student(s) assigned. Please reassign or remove all students first."
    ]);
    $conn->close();
    exit;
}

            // Check if department is used in class_dept_sec
        $class_dept_stmt = $conn->prepare('SELECT COUNT(*) as count FROM class_dept_sec WHERE department_id=? AND is_deleted = 0');
$class_dept_stmt->bind_param('i', $id);
$class_dept_stmt->execute();
$class_dept_result = $class_dept_stmt->get_result();
$class_dept_count = $class_dept_result->fetch_assoc()['count'];
$class_dept_stmt->close();

if ($class_dept_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete department '{$department['name']}'. It is assigned to {$class_dept_count} class(es). Please remove all class assignments first."
    ]);
    $conn->close();
    exit;
}

        // Check if department is used in class_dept_sub
        $class_subject_stmt = $conn->prepare('SELECT COUNT(*) as count FROM class_dept_sub WHERE department_id=? AND is_deleted = 0');
$class_subject_stmt->bind_param('i', $id);
$class_subject_stmt->execute();
$class_subject_result = $class_subject_stmt->get_result();
$class_subject_count = $class_subject_result->fetch_assoc()['count'];
$class_subject_stmt->close();

if ($class_subject_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete department '{$department['name']}'. It has {$class_subject_count} subject assignment(s). Please remove all subject assignments first."
    ]);
    $conn->close();
    exit;
}

        // Check if department is used in class_dept_sub_teacher
        $class_teacher_stmt = $conn->prepare('SELECT COUNT(*) as count FROM class_dept_sub_teacher WHERE department_id=? AND is_deleted = 0');
$class_teacher_stmt->bind_param('i', $id);
$class_teacher_stmt->execute();
$class_teacher_result = $class_teacher_stmt->get_result();
$class_teacher_count = $class_teacher_result->fetch_assoc()['count'];
$class_teacher_stmt->close();

if ($class_teacher_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete department '{$department['name']}'. It has {$class_teacher_count} teacher assignment(s). Please remove all teacher assignments first."
    ]);
    $conn->close();
    exit;
}

// Check if department is used in exams
$exam_stmt = $conn->prepare('SELECT COUNT(*) as count FROM exams WHERE department_id=?');
$exam_stmt->bind_param('i', $id);
$exam_stmt->execute();
$exam_result = $exam_stmt->get_result();
$exam_count = $exam_result->fetch_assoc()['count'];
$exam_stmt->close();

if ($exam_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete department '{$department['name']}'. It is used in {$exam_count} exam(s). Please remove all exam references first."
    ]);
    $conn->close();
    exit;
}

// Check if department is used in student_history
$history_stmt = $conn->prepare('SELECT COUNT(*) as count FROM student_history WHERE department_id=?');
$history_stmt->bind_param('i', $id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
$history_count = $history_result->fetch_assoc()['count'];
$history_stmt->close();

if ($history_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot delete department '{$department['name']}'. It has {$history_count} student history record(s). Please remove all history records first."
    ]);
    $conn->close();
    exit;
}

// If no dependencies found, perform soft delete
$stmt = $conn->prepare('UPDATE departments SET is_deleted=1 WHERE id=?');
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => "Department '{$department['name']}' has been deleted successfully."
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete department.']);
}
$stmt->close();
$conn->close();
?> 