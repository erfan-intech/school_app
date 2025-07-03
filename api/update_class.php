<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$id = $_POST['id'] ?? '';
$name = $_POST['name'] ?? '';

if (!$id || !$name) {
    echo json_encode(['success' => false, 'message' => 'ID and class name required.']);
    exit;
}

$stmt = $conn->prepare("UPDATE classes SET name=? WHERE id=?");
$stmt->bind_param('si', $name, $id);
$success = $stmt->execute();
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Class updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update class.']);
}
$stmt->close();
$conn->close();
?>
