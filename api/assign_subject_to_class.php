<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';
$class_id = $_POST['class_id'] ?? '';
$subject_id = $_POST['subject_id'] ?? '';
$department_id = $_POST['department_id'] ?? '';
$action = $_POST['action'] ?? '';

// Convert empty string or '0' to NULL for global assignments
if ($department_id === '' || $department_id === '0' || $department_id === 0) {
    $department_id = null;
}
if (!$class_id || !$subject_id || !in_array($action, ['add','remove'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit;
}
if ($action === 'add') {
    // First check if record already exists (including soft deleted)
    if ($department_id === null) {
        $checkStmt = $conn->prepare('SELECT id, is_deleted FROM class_dept_sub WHERE class_id=? AND subject_id=? AND department_id IS NULL');
        $checkStmt->bind_param('ii', $class_id, $subject_id);
    } else {
        $checkStmt = $conn->prepare('SELECT id, is_deleted FROM class_dept_sub WHERE class_id=? AND subject_id=? AND department_id=?');
        $checkStmt->bind_param('iii', $class_id, $subject_id, $department_id);
    }
    
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $existingRecord = $result->fetch_assoc();
    $checkStmt->close();
    
    if ($existingRecord) {
        if ($existingRecord['is_deleted'] == 1) {
            // Reactivate the soft deleted record
            $stmt = $conn->prepare('UPDATE class_dept_sub SET is_deleted = 0 WHERE id = ?');
            $stmt->bind_param('i', $existingRecord['id']);
            $success = $stmt->execute();
            $stmt->close();
        } else {
            // Record already exists and is active
            $success = true;
        }
    } else {
        // Record doesn't exist, insert new one
        if ($department_id === null) {
            $stmt = $conn->prepare('INSERT INTO class_dept_sub (class_id, subject_id, department_id, is_deleted) VALUES (?, ?, NULL, 0)');
            $stmt->bind_param('ii', $class_id, $subject_id);
        } else {
            $stmt = $conn->prepare('INSERT INTO class_dept_sub (class_id, subject_id, department_id, is_deleted) VALUES (?, ?, ?, 0)');
            $stmt->bind_param('iii', $class_id, $subject_id, $department_id);
        }
        $success = $stmt->execute();
        $stmt->close();
    }
    echo json_encode(['success' => $success]);
} else {
    if ($department_id === null) {
        $stmt = $conn->prepare('UPDATE class_dept_sub SET is_deleted = 1 WHERE class_id=? AND subject_id=? AND department_id IS NULL AND is_deleted = 0');
        $stmt->bind_param('ii', $class_id, $subject_id);
    } else {
        $stmt = $conn->prepare('UPDATE class_dept_sub SET is_deleted = 1 WHERE class_id=? AND subject_id=? AND department_id=? AND is_deleted = 0');
        $stmt->bind_param('iii', $class_id, $subject_id, $department_id);
    }
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
}
$conn->close(); 