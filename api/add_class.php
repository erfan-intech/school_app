<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$name = $_POST['name'] ?? '';

if (!$name) {
    echo json_encode(['success' => false, 'message' => 'Class name required.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO classes (name) VALUES (?)");
$stmt->bind_param('s', $name);
$success = $stmt->execute();
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Class added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add class.']);
}
$stmt->close();
$conn->close();
?>
