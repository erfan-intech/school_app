<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$exam_id = $_GET['exam_id'] ?? '';
$subject_id = $_GET['subject_id'] ?? '';
$department_id = $_GET['department_id'] ?? '';

if (!$exam_id || !$subject_id) {
    echo json_encode(['success' => false, 'message' => 'Exam ID and Subject ID required.']);
    exit;
}

try {
    // Get exam details to determine class and department
    $stmt = $conn->prepare("SELECT class_id, department_id FROM exams WHERE id = ? AND is_deleted = 0");
    $stmt->bind_param('i', $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Exam not found");
    }
    $exam = $result->fetch_assoc();
    $stmt->close();

    // Get teachers for the specific subject
    if ($department_id) {
        // If department is specified, get teachers specifically assigned to this subject for this department
        // Also include teachers assigned to this subject at the parent department level (department_id IS NULL)
        $sql_teachers = "SELECT DISTINCT
                            t.id,
                            t.first_name,
                            t.last_name,
                            t.profile_picture,
                            CONCAT(t.first_name, ' ', t.last_name) AS full_name
                        FROM teachers t
                        INNER JOIN class_dept_sub_teacher cdst ON t.id = cdst.teacher_id
                        WHERE cdst.class_id = ? 
                        AND cdst.subject_id = ?
                        AND (cdst.department_id = ? OR cdst.department_id IS NULL)
                        AND cdst.is_deleted = 0
                        AND t.is_deleted = 0
                        ORDER BY t.first_name, t.last_name";
        
        $stmt = $conn->prepare($sql_teachers);
        $stmt->bind_param('iii', $exam['class_id'], $subject_id, $department_id);
    } else {
        // If no department specified, get teachers specifically assigned to this subject at parent department level only
        $sql_teachers = "SELECT DISTINCT
                            t.id,
                            t.first_name,
                            t.last_name,
                            t.profile_picture,
                            CONCAT(t.first_name, ' ', t.last_name) AS full_name
                        FROM teachers t
                        INNER JOIN class_dept_sub_teacher cdst ON t.id = cdst.teacher_id
                        WHERE cdst.class_id = ? 
                        AND cdst.department_id IS NULL
                        AND cdst.subject_id = ?
                        AND cdst.is_deleted = 0
                        AND t.is_deleted = 0
                        ORDER BY t.first_name, t.last_name";
        
        $stmt = $conn->prepare($sql_teachers);
        $stmt->bind_param('ii', $exam['class_id'], $subject_id);
    }
    
    $stmt->execute();
    $result_teachers = $stmt->get_result();
    
    $teachers = [];
    while ($row = $result_teachers->fetch_assoc()) {
        $teachers[] = $row;
    }
    $stmt->close();

    echo json_encode([
        'success' => true,
        'data' => $teachers
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?> 