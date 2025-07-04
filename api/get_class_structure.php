<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$class_id = $_GET['class_id'] ?? '';

if (!$class_id) {
    echo json_encode(['success' => false, 'message' => 'Class ID required.']);
    exit;
}

try {
    // Get class details
    $stmt = $conn->prepare("SELECT id, name FROM classes WHERE id = ? AND is_deleted = 0");
    if (!$stmt) {
        throw new Exception("SQL preparation failed for classes: " . $conn->error);
    }
    $stmt->bind_param('i', $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Class not found");
    }
    $class = $result->fetch_assoc();
    $stmt->close();

    // Get departments for this class
    $stmt = $conn->prepare("
        SELECT DISTINCT d.id, d.name 
        FROM departments d 
        INNER JOIN class_dept_sub cds ON d.id = cds.department_id 
        WHERE cds.class_id = ? AND cds.is_deleted = 0 AND d.is_deleted = 0 
        ORDER BY d.name
    ");
    if (!$stmt) {
        throw new Exception("SQL preparation failed for departments: " . $conn->error);
    }
    $stmt->bind_param('i', $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $departments = [];
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
    $stmt->close();

    // Get sections for this class (both global and department-specific)
    $stmt = $conn->prepare("
        SELECT 
            s.id,
            s.name,
            cds.department_id,
            d.name as department_name
        FROM sections s
        INNER JOIN class_dept_sec cds ON s.id = cds.section_id
        LEFT JOIN departments d ON cds.department_id = d.id
        WHERE cds.class_id = ? AND cds.is_deleted = 0 AND s.is_deleted = 0
        ORDER BY cds.department_id, s.name
    ");
    if (!$stmt) {
        throw new Exception("SQL preparation failed for sections: " . $conn->error);
    }
    $stmt->bind_param('i', $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }
    $stmt->close();

    // Get subjects for this class
    $sql_subjects = "
        SELECT 
            s.id as subject_id,
            s.name as subject_name,
            cds.department_id,
            d.name as department_name
        FROM subjects s
        INNER JOIN class_dept_sub cds ON s.id = cds.subject_id
        LEFT JOIN departments d ON cds.department_id = d.id
        WHERE cds.class_id = ? AND cds.is_deleted = 0 AND s.is_deleted = 0
        ORDER BY cds.department_id, s.name
    ";
    
    $stmt = $conn->prepare($sql_subjects);
    if (!$stmt) {
        throw new Exception("SQL preparation failed for subjects: " . $conn->error);
    }
    $stmt->bind_param('i', $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
    $stmt->close();

    // Get teacher assignments for this class
    $sql_teachers = "
        SELECT 
            cdst.subject_id,
            cdst.department_id,
            cdst.teacher_id,
            t.first_name as teacher_first_name,
            t.last_name as teacher_last_name,
            CONCAT(t.first_name, ' ', t.last_name) as teacher_full_name
        FROM class_dept_sub_teacher cdst
        LEFT JOIN teachers t ON cdst.teacher_id = t.id
        WHERE cdst.class_id = ? AND cdst.is_deleted = 0 AND t.is_deleted = 0
        ORDER BY cdst.department_id, cdst.subject_id
    ";
    
    $stmt = $conn->prepare($sql_teachers);
    if (!$stmt) {
        throw new Exception("SQL preparation failed for teachers: " . $conn->error);
    }
    $stmt->bind_param('i', $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacherAssignments = [];
    while ($row = $result->fetch_assoc()) {
        $teacherAssignments[] = $row;
    }
    $stmt->close();


    // Organize the data
    $classStructure = [
        'class' => $class,
        'has_departments' => count($departments) > 0,
        'has_sections' => count($sections) > 0,
        'departments' => $departments,
        'sections' => $sections,
        'subjects' => $subjects,
        'subjects_by_department' => [],
        'teachers_by_subject' => []
    ];

    // Organize subjects by department
    foreach ($subjects as $subject) {
        $deptId = $subject['department_id'] ?? 'global';
        if (!isset($classStructure['subjects_by_department'][$deptId])) {
            $classStructure['subjects_by_department'][$deptId] = [];
        }
        $classStructure['subjects_by_department'][$deptId][] = [
            'id' => $subject['subject_id'],
            'name' => $subject['subject_name']
        ];
    }

    // Organize teachers by subject
    foreach ($teacherAssignments as $assignment) {
        $subjectId = $assignment['subject_id'];
        $deptId = $assignment['department_id'] ?? 'global';
        $key = $subjectId . '_' . $deptId;
        
        if (!isset($classStructure['teachers_by_subject'][$key])) {
            $classStructure['teachers_by_subject'][$key] = [];
        }
        
        if ($assignment['teacher_id']) {
            $classStructure['teachers_by_subject'][$key][] = [
                'id' => $assignment['teacher_id'],
                'first_name' => $assignment['teacher_first_name'],
                'last_name' => $assignment['teacher_last_name'],
                'full_name' => $assignment['teacher_full_name']
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'data' => $classStructure
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?> 