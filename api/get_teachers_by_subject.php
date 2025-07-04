<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$class_id = $_GET['class_id'] ?? '';
$subject_id = $_GET['subject_id'] ?? '';
$department_id = $_GET['department_id'] ?? '';

if (empty($class_id) || empty($subject_id)) {
    echo json_encode(['success' => false, 'message' => 'Class ID and Subject ID are required']);
    exit;
}

try {
    // Get teachers for the selected class and subject
    $sql = "SELECT DISTINCT t.id, t.first_name, t.last_name 
            FROM teachers t 
            INNER JOIN class_dept_sub_teacher cdst ON t.id = cdst.teacher_id 
            WHERE cdst.class_id = ? AND cdst.subject_id = ? AND cdst.is_deleted = 0 AND t.is_deleted = 0";
    
    $params = [$class_id, $subject_id];
    $types = 'ii';
    
    // Add department filter if provided
    if (!empty($department_id)) {
        $sql .= " AND cdst.department_id = ?";
        $params[] = $department_id;
        $types .= 'i';
    } else {
        // If no department provided, look for teachers assigned to this subject without department (global subjects)
        $sql .= " AND cdst.department_id IS NULL";
    }
    
    $sql .= " ORDER BY t.first_name, t.last_name";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $teachers = [];
    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $teachers
    ]);
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?> 