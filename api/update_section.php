<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$id = $_POST['id'] ?? '';
$name = $_POST['name'] ?? '';

if (!$id || !$name) {
    echo json_encode(['success' => false, 'message' => 'ID and section name required.']);
    exit;
}

$stmt = $conn->prepare("UPDATE sections SET name=? WHERE id=?");
$stmt->bind_param('si', $name, $id);
$success = $stmt->execute();
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Section updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update section.']);
}
$stmt->close();
$conn->close();
?> 