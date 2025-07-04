<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = $_POST['id'] ?? '';

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'Exam type ID is required']);
    exit;
}

try {
    // First, check if the exam_types table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'exam_types'");
    if ($tableCheck->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Exam types table does not exist']);
        exit;
    }
    
    // Check if exam type exists and is not already deleted
    $stmt = $conn->prepare("SELECT id FROM exam_types WHERE id = ? AND is_deleted = 0");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Exam type not found or already deleted']);
        exit;
    }
    $stmt->close();
    
    // Check if exam type is being used by any exams
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM exams WHERE exam_type_id = ? AND is_deleted = 0");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $examCount = $result->fetch_assoc()['count'];
    $stmt->close();
    
    if ($examCount > 0) {
        echo json_encode([
            'success' => false, 
            'message' => "Cannot delete exam type. It is being used by $examCount exam(s). Please delete or update those exams first."
        ]);
        exit;
    }
    
    // Perform soft delete by setting is_deleted = 1
    $stmt = $conn->prepare("UPDATE exam_types SET is_deleted = 1 WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Exam type deleted successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting exam type: ' . $stmt->error]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?> 