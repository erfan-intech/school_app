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
    // Get sections for the selected class
    $sql = "SELECT DISTINCT s.id, s.name 
            FROM sections s 
            INNER JOIN class_dept_sec cds ON s.id = cds.section_id 
            WHERE cds.class_id = ? AND cds.is_deleted = 0 AND s.is_deleted = 0 
            ORDER BY s.name";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param('i', $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $sections
    ]);
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?> 