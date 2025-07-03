<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$id = $_POST['id'] ?? '';
$name = $_POST['name'] ?? '';
if (!$id || !$name) {
    echo json_encode(['success' => false, 'message' => 'ID and name required.']);
    exit;
}
$stmt = $conn->prepare('UPDATE subjects SET name=? WHERE id=?');
$stmt->bind_param('si', $name, $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Subject updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update subject.']);
}
$stmt->close();
$conn->close(); 