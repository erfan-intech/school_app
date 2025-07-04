<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$exam_id = $_GET['exam_id'] ?? '';
if (!$exam_id) {
    echo json_encode(['success' => false, 'message' => 'Exam ID required.']);
    exit;
}

try {
    // Get exam details
    $stmt = $conn->prepare("SELECT class_id, department_id, section_id FROM exams WHERE id = ? AND is_deleted = 0");
    $stmt->bind_param('i', $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Exam not found");
    }
    $exam = $result->fetch_assoc();
    $stmt->close();

    // Get available subjects for this class-department combination
    if ($exam['department_id']) {
        // If department is assigned, get subjects for that specific department
    $sql_subjects = "SELECT 
                        s.id,
                            s.name
                    FROM subjects s
                    INNER JOIN class_dept_sub cds ON s.id = cds.subject_id
                    WHERE cds.class_id = ? 
                    AND cds.department_id = ? 
                    AND cds.is_deleted = 0
                    AND s.is_deleted = 0
                    AND s.id NOT IN (
                        SELECT subject_id 
                        FROM exam_subjects 
                        WHERE exam_id = ? AND is_deleted = 0
                    )
                    ORDER BY s.name";
    
    $stmt = $conn->prepare($sql_subjects);
    $stmt->bind_param('iii', $exam['class_id'], $exam['department_id'], $exam_id);
    } else {
        // If no department is assigned, get all subjects for the class
        $sql_subjects = "SELECT 
                            s.id,
                            s.name
                        FROM subjects s
                        INNER JOIN class_dept_sub cds ON s.id = cds.subject_id
                        WHERE cds.class_id = ? 
                        AND cds.department_id IS NULL
                        AND cds.is_deleted = 0
                        AND s.is_deleted = 0
                        AND s.id NOT IN (
                            SELECT subject_id 
                            FROM exam_subjects 
                            WHERE exam_id = ? AND is_deleted = 0
                        )
                        ORDER BY s.name";
        
        $stmt = $conn->prepare($sql_subjects);
        $stmt->bind_param('ii', $exam['class_id'], $exam_id);
    }
    $stmt->execute();
    $result_subjects = $stmt->get_result();
    
    $subjects = [];
    while ($row = $result_subjects->fetch_assoc()) {
        $subjects[] = $row;
    }
    $stmt->close();

    // Get all available teachers for this class (we'll filter by subject later in JavaScript)
    if ($exam['department_id']) {
        // If department is assigned, get teachers for that specific department
    $sql_teachers = "SELECT DISTINCT
                        t.id,
                        t.first_name,
                        t.last_name,
                        t.profile_picture,
                        CONCAT(t.first_name, ' ', t.last_name) AS full_name
                    FROM teachers t
                    INNER JOIN class_dept_sub_teacher cdst ON t.id = cdst.teacher_id
                    WHERE cdst.class_id = ? 
                    AND cdst.department_id = ? 
                    AND cdst.is_deleted = 0
                    AND t.is_deleted = 0
                    ORDER BY t.first_name, t.last_name";
    
    $stmt = $conn->prepare($sql_teachers);
    $stmt->bind_param('ii', $exam['class_id'], $exam['department_id']);
    } else {
        // If no department is assigned, get all teachers for the class
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
                        AND cdst.is_deleted = 0
                        AND t.is_deleted = 0
                        ORDER BY t.first_name, t.last_name";
        
        $stmt = $conn->prepare($sql_teachers);
        $stmt->bind_param('i', $exam['class_id']);
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
        'data' => [
            'subjects' => $subjects,
            'teachers' => $teachers
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?> 