<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';
$class_id = $_POST['class_id'] ?? '';
$teacher_id = $_POST['teacher_id'] ?? '';
$subject_id = $_POST['subject_id'] ?? null;
$department_id = $_POST['department_id'] ?? null;

// Convert empty strings to null
if ($subject_id === '' || $subject_id === '0' || $subject_id === 0) {
    $subject_id = null;
}
if ($department_id === '' || $department_id === '0' || $department_id === 0) {
    $department_id = null;
}
$action = $_POST['action'] ?? '';

if (!$class_id || !$teacher_id || !in_array($action, ['add','removeteacherfromclass','removeteachersubject','remove'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit;
}

if ($action === 'add') {
    // Check if assignment already exists (including soft deleted)
    $checkStmt = $conn->prepare('SELECT id, is_deleted FROM class_dept_sub_teacher WHERE class_id=? AND teacher_id=? AND (subject_id IS NULL OR subject_id=?) AND (department_id IS NULL OR department_id=?)');
    $checkStmt->bind_param('iiii', $class_id, $teacher_id, $subject_id, $department_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $existingRecord = $result->fetch_assoc();
    $checkStmt->close();
    
    if ($existingRecord) {
        if ($existingRecord['is_deleted'] == 1) {
            // Reactivate the soft deleted record
            $stmt = $conn->prepare('UPDATE class_dept_sub_teacher SET is_deleted = 0 WHERE id = ?');
            $stmt->bind_param('i', $existingRecord['id']);
            $success = $stmt->execute();
            $stmt->close();
        } else {
            // Record already exists and is active
            $success = true;
        }
    } else {
        // Insert new record
        $stmt = $conn->prepare('INSERT INTO class_dept_sub_teacher (class_id, department_id, teacher_id, subject_id) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('iiii', $class_id, $department_id, $teacher_id, $subject_id);
        $success = $stmt->execute();
        $stmt->close();
    }
    echo json_encode(['success' => $success]);
} else if ($action === 'removeteacherfromclass') {
    $stmt = $conn->prepare('UPDATE class_dept_sub_teacher SET is_deleted = 1 WHERE class_id=? AND teacher_id=? AND is_deleted = 0');
    $stmt->bind_param('ii', $class_id, $teacher_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
} else if ($action === 'removeteachersubject') {
    $stmt = $conn->prepare('UPDATE class_dept_sub_teacher SET is_deleted = 1 WHERE class_id=? AND department_id = ? AND teacher_id=? AND subject_id=? AND is_deleted = 0');
    $stmt->bind_param('iiii', $class_id, $department_id, $teacher_id, $subject_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
} else if ($action === 'remove') {
    // Remove all assignments for this teacher in this class
    $stmt = $conn->prepare('UPDATE class_dept_sub_teacher SET is_deleted = 1 WHERE class_id=? AND teacher_id=? AND is_deleted = 0');
    $stmt->bind_param('ii', $class_id, $teacher_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
}
$conn->close(); 