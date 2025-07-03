<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';
$class_id = $_POST['class_id'] ?? '';
$subject_id = $_POST['subject_id'] ?? '';
$department_id = isset($_POST['department_id']) ? intval($_POST['department_id']) : 0;
$action = $_POST['action'] ?? '';
// Convert department_id=0 to NULL for global assignments
// if ($department_id === '0' || $department_id === 0) {
//     $department_id = null;
// }
if (!$class_id || !$subject_id || !in_array($action, ['add','remove'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit;
}
if ($action === 'add') {
    $stmt = $conn->prepare('INSERT IGNORE INTO class_subjects (class_id, subject_id, department_id) VALUES (?, ?, ?)');
    $stmt->bind_param('iii', $class_id, $subject_id, $department_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
} else {
    $stmt = $conn->prepare('DELETE FROM class_subjects WHERE class_id=? AND subject_id=? AND (department_id=? OR (? IS NULL AND department_id IS NULL))');
    $stmt->bind_param('iiii', $class_id, $subject_id, $department_id, $department_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
}
$conn->close(); 