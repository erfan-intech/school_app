<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$name = $_POST['name'] ?? '';
if (!$name) {
    echo json_encode(['success' => false, 'message' => 'Subject name required.']);
    exit;
}
$stmt = $conn->prepare('INSERT INTO subjects (name, is_deleted) VALUES (?, 0)');
$stmt->bind_param('s', $name);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Subject added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add subject.']);
}
$stmt->close();
$conn->close(); 