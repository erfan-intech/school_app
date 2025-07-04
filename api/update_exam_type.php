<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

// Debug logging
error_log("update_exam_type.php called with POST data: " . print_r($_POST, true));

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$id = $_POST['id'] ?? '';
$type_name = trim($_POST['type_name'] ?? '');
$description = trim($_POST['description'] ?? '');

// Debug logging
error_log("Parsed data - ID: $id, Type Name: $type_name, Description: $description");

// Validate required fields
if (empty($id) || empty($type_name)) {
    echo json_encode(['success' => false, 'message' => 'ID and Type Name are required']);
    exit;
}

try {
    // First, check if the exam_types table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'exam_types'");
    if ($tableCheck->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Exam types table does not exist']);
        exit;
    }
    
    // Check if exam type exists
    $stmt = $conn->prepare("SELECT id FROM exam_types WHERE id = ? AND is_deleted = 0");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        error_log("Exam type not found with ID: $id");
        echo json_encode(['success' => false, 'message' => 'Exam type not found']);
        exit;
    }
    $stmt->close();
    
    // Check if type name already exists (excluding current record)
    $stmt = $conn->prepare("SELECT id FROM exam_types WHERE type_name = ? AND id != ? AND is_deleted = 0");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('si', $type_name, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        error_log("Exam type name already exists: $type_name");
        echo json_encode(['success' => false, 'message' => 'Exam type name already exists']);
        exit;
    }
    $stmt->close();
    
    // Update the exam type
    $stmt = $conn->prepare("UPDATE exam_types SET type_name = ?, description = ? WHERE id = ? AND is_deleted = 0");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param('ssi', $type_name, $description, $id);
    
    if ($stmt->execute()) {
        error_log("Exam type updated successfully - ID: $id, Name: $type_name");
        echo json_encode(['success' => true, 'message' => 'Exam type updated successfully']);
    } else {
        error_log("Failed to update exam type - ID: $id");
        echo json_encode(['success' => false, 'message' => 'Failed to update exam type: ' . $stmt->error]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    error_log("Database error in update_exam_type.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?> 