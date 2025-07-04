<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$type_name = $_POST['type_name'] ?? '';
$description = $_POST['description'] ?? '';

if (empty($type_name)) {
    echo json_encode(['success' => false, 'message' => 'Exam type name is required']);
    exit;
}

try {
    // First, check if the exam_types table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'exam_types'");
    if ($tableCheck->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Exam types table does not exist']);
        exit;
    }
    
    // Check if exam type already exists (excluding soft-deleted)
    $stmt = $conn->prepare("SELECT id FROM exam_types WHERE type_name = ? AND is_deleted = 0");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('s', $type_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Exam type with this name already exists']);
        exit;
    }
    $stmt->close();
    
    // Insert new exam type
    $stmt = $conn->prepare("INSERT INTO exam_types (type_name, description) VALUES (?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param('ss', $type_name, $description);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Exam type added successfully',
            'id' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding exam type: ' . $stmt->error]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?> 