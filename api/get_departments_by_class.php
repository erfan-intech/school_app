<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$class_id = $_GET['class_id'] ?? '';

if (empty($class_id)) {
    echo json_encode(['success' => false, 'message' => 'Class ID is required']);
    exit;
}

try {
    // Get departments for the selected class
    $sql = "SELECT DISTINCT d.id, d.name 
            FROM departments d 
            INNER JOIN class_dept_sub cds ON d.id = cds.department_id 
            WHERE cds.class_id = ? AND cds.is_deleted = 0 AND d.is_deleted = 0 
            ORDER BY d.name";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param('i', $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $departments = [];
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $departments
    ]);
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?> 