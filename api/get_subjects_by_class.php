<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

try {
    $class_id = $_GET['class_id'] ?? '';
    $department_id = $_GET['department_id'] ?? '';
    
    if (!$class_id) {
        throw new Exception("Class ID is required");
    }
    
    $sql = "SELECT DISTINCT 
                cs.subject_id,
                s.name,
                cs.department_id,
                d.name AS department_name
            FROM class_dept_sub cs
            JOIN subjects s ON cs.subject_id = s.id
            LEFT JOIN departments d ON cs.department_id = d.id
            WHERE cs.class_id = ? AND cs.is_deleted = 0";
    
    $params = [$class_id];
    $types = "i";
    
    // Add department filter if provided
    if ($department_id) {
        $sql .= " AND cs.department_id = ?";
        $params[] = $department_id;
        $types .= "i";
    }
    
    $sql .= " ORDER BY s.name";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Database query failed: " . $stmt->error);
    }
    
    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $subjects
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?> 