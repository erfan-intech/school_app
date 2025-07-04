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
    // Check if class has departments
    $deptSql = "SELECT COUNT(*) as dept_count FROM class_dept_sub 
                WHERE class_id = ? AND department_id IS NOT NULL AND is_deleted = 0";
    $deptStmt = $conn->prepare($deptSql);
    $deptStmt->bind_param('i', $class_id);
    $deptStmt->execute();
    $deptResult = $deptStmt->get_result();
    $deptCount = $deptResult->fetch_assoc()['dept_count'];
    $deptStmt->close();
    
    // Check if class has sections
    $secSql = "SELECT COUNT(*) as sec_count FROM class_dept_sec 
               WHERE class_id = ? AND section_id IS NOT NULL AND is_deleted = 0";
    $secStmt = $conn->prepare($secSql);
    $secStmt->bind_param('i', $class_id);
    $secStmt->execute();
    $secResult = $secStmt->get_result();
    $secCount = $secResult->fetch_assoc()['sec_count'];
    $secStmt->close();
    
    // Check if class has global subjects (no department)
    $globalSubSql = "SELECT COUNT(*) as global_count FROM class_dept_sub 
                     WHERE class_id = ? AND department_id IS NULL AND is_deleted = 0";
    $globalSubStmt = $conn->prepare($globalSubSql);
    $globalSubStmt->bind_param('i', $class_id);
    $globalSubStmt->execute();
    $globalSubResult = $globalSubStmt->get_result();
    $globalSubCount = $globalSubResult->fetch_assoc()['global_count'];
    $globalSubStmt->close();
    
    // Determine configuration type
    $hasDepartments = $deptCount > 0;
    $hasSections = $secCount > 0;
    $hasGlobalSubjects = $globalSubCount > 0;
    
    $configuration = 'none';
    if ($hasDepartments && $hasSections) {
        $configuration = 'both';
    } elseif ($hasDepartments && !$hasSections) {
        $configuration = 'departments_only';
    } elseif (!$hasDepartments && $hasSections) {
        $configuration = 'sections_only';
    } elseif (!$hasDepartments && !$hasSections && $hasGlobalSubjects) {
        $configuration = 'global_subjects_only';
    } else {
        $configuration = 'none';
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'has_departments' => $hasDepartments,
            'has_sections' => $hasSections,
            'has_global_subjects' => $hasGlobalSubjects,
            'configuration' => $configuration,
            'dept_count' => $deptCount,
            'sec_count' => $secCount,
            'global_subject_count' => $globalSubCount
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?> 