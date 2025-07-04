<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$exam_id = $_GET['exam_id'] ?? '';
if (!$exam_id) {
    echo json_encode(['success' => false, 'message' => 'Exam ID required.']);
    exit;
}

try {
    // First, get exam details to understand the class/department/section
    $sql_exam = "SELECT class_id, department_id, section_id FROM exams WHERE id = ? AND is_deleted = 0";
    $stmt_exam = $conn->prepare($sql_exam);
    $stmt_exam->bind_param('i', $exam_id);
    $stmt_exam->execute();
    $result_exam = $stmt_exam->get_result();
    
    if ($result_exam->num_rows === 0) {
        throw new Exception("Exam not found");
    }
    
    $exam = $result_exam->fetch_assoc();
    $stmt_exam->close();

    // Get subjects assigned to this class/department combination
    $sql = "SELECT 
                cds.subject_id,
                sub.name AS subject_name,
                cds.department_id,
                d.name AS department_name
            FROM class_dept_sub cds
            JOIN subjects sub ON cds.subject_id = sub.id
            LEFT JOIN departments d ON cds.department_id = d.id
            WHERE cds.class_id = ? 
            AND cds.is_deleted = 0
            AND sub.is_deleted = 0
            AND (cds.department_id = ? OR (cds.department_id IS NULL AND ? IS NULL))
            ORDER BY sub.name";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $exam['class_id'], $exam['department_id'], $exam['department_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }
    
    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        // Get teachers assigned to this subject for this class/department
        $sql_teachers = "SELECT 
                            t.id,
                            t.first_name,
                            t.last_name,
                            t.profile_picture
                        FROM class_dept_sub_teacher cdt
                        JOIN teachers t ON cdt.teacher_id = t.id
                        WHERE cdt.class_id = ? 
                        AND cdt.subject_id = ?
                        AND cdt.is_deleted = 0
                        AND t.is_deleted = 0
                        AND (cdt.department_id = ? OR (cdt.department_id IS NULL AND ? IS NULL))
                        ORDER BY t.first_name, t.last_name";
        
        $stmt_teachers = $conn->prepare($sql_teachers);
        $stmt_teachers->bind_param('iiii', $exam['class_id'], $row['subject_id'], $exam['department_id'], $exam['department_id']);
        $stmt_teachers->execute();
        $result_teachers = $stmt_teachers->get_result();
        
        $teachers = [];
        while ($teacher = $result_teachers->fetch_assoc()) {
            $teacher['full_name'] = $teacher['first_name'] . ' ' . $teacher['last_name'];
            $teachers[] = $teacher;
        }
        $stmt_teachers->close();
        
        $row['teachers'] = $teachers;
        $subjects[] = $row;
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'data' => $subjects,
        'exam_info' => $exam
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?> 