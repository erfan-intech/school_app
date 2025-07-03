<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';
$class_id = $_POST['class_id'] ?? '';
$section_id = $_POST['section_id'] ?? '';
$department_id = isset($_POST['department_id']) ? $_POST['department_id'] : 0;
$action = $_POST['action'] ?? '';
if (!$class_id || !$section_id || !in_array($action, ['add','remove'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit;
}
if ($action === 'add') {
    $stmt = $conn->prepare('INSERT IGNORE INTO class_departments (class_id, section_id, department_id) VALUES (?, ?, ?)');
    $stmt->bind_param('iii', $class_id, $section_id, $department_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
} else {
    $stmt = $conn->prepare('DELETE FROM class_departments WHERE class_id=? AND section_id=? AND department_id=?');
    $stmt->bind_param('iii', $class_id, $section_id, $department_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
}
$conn->close(); 