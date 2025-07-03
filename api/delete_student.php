<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$student_id = $_POST['id'] ?? '';
if (!$student_id) {
    echo json_encode(['success' => false, 'message' => 'Student ID required.']);
    exit;
}
$stmt = $conn->prepare('UPDATE students SET is_deleted=1 WHERE id=?');
$stmt->bind_param('i', $student_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete student.']);
}
$stmt->close();
$conn->close();
?>
