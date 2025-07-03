<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$id = $_POST['id'] ?? '';
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Teacher ID required.']);
    exit;
}
$stmt = $conn->prepare('UPDATE teachers SET is_deleted=1 WHERE id=?');
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete teacher.']);
}
$stmt->close();
$conn->close();
?>
