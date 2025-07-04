<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$class_id = $_GET['class_id'] ?? '';
$department_id = $_GET['department_id'] ?? '';

if (!$class_id) {
    echo json_encode(['success' => false, 'message' => 'Class ID required.']);
    exit;
}

try {
    // Get subjects for the specific class and department combination
    if ($department_id) {
        // If department is specified, get subjects for that department
        $sql = "SELECT DISTINCT
                    s.id,
                    s.name,
                    s.description
                FROM subjects s
                INNER JOIN class_dept_sub_teacher cdst ON s.id = cdst.subject_id
                WHERE cdst.class_id = ? 
                AND cdst.department_id = ?
                AND cdst.is_deleted = 0
                AND s.is_deleted = 0
                ORDER BY s.name";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $class_id, $department_id);
    } else {
        // If no department specified, get subjects for the class (department_id IS NULL)
        $sql = "SELECT DISTINCT
                    s.id,
                    s.name,
                    s.description
                FROM subjects s
                INNER JOIN class_dept_sub_teacher cdst ON s.id = cdst.subject_id
                WHERE cdst.class_id = ? 
                AND cdst.department_id IS NULL
                AND cdst.is_deleted = 0
                AND s.is_deleted = 0
                ORDER BY s.name";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $class_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
    $stmt->close();

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

$conn->close();
?> 