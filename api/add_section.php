<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$name = $_POST['name'] ?? '';

if (!$name) {
    echo json_encode(['success' => false, 'message' => 'Section name required.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO sections (name) VALUES (?)");
$stmt->bind_param('s', $name);
$success = $stmt->execute();
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Section added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add section.']);
}
$stmt->close();
$conn->close();
?> 