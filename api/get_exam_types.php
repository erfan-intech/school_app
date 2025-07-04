<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

try {
    // First, check if the exam_types table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'exam_types'");
    if ($tableCheck->num_rows === 0) {
        echo json_encode([
            'success' => true,
            'data' => []
        ]);
        exit;
    }
    
    // Get all exam types that are not soft-deleted
    $sql = "SELECT id, type_name, description FROM exam_types WHERE is_deleted = 0 ORDER BY type_name";
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }
    
    $exam_types = [];
    while ($row = $result->fetch_assoc()) {
        $exam_types[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $exam_types
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?> 