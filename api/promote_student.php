<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$id = $_POST['id'] ?? '';
$current_class_id = isset($_POST['current_class_id']) && $_POST['current_class_id'] !== '' ? $_POST['current_class_id'] : null;
$current_department_id = isset($_POST['current_department_id']) && $_POST['current_department_id'] !== '' ? $_POST['current_department_id'] : null;
$current_section_id = isset($_POST['current_section_id']) && $_POST['current_section_id'] !== '' ? $_POST['current_section_id'] : null;

if (!$id || !$current_class_id) {
    echo json_encode(['success' => false, 'message' => 'Student ID and new class are required.']);
    exit;
}

$stmt = $conn->prepare("UPDATE students SET current_class_id=?, current_department_id=?, current_section_id=? WHERE id=?");
$stmt->bind_param('iiii', $current_class_id, $current_department_id, $current_section_id, $id);
$success = $stmt->execute();
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Student promoted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to promote student.']);
}
$stmt->close();
$conn->close();
?> 