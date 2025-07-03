<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$id = $_POST['id'] ?? '';
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID required.']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM departments WHERE id=?");
$stmt->bind_param('i', $id);
$success = $stmt->execute();
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Department deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete department.']);
}
$stmt->close();
$conn->close();
?> 