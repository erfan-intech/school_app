<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$subject_id = $_GET['subject_id'] ?? '';
$exam_subject_id = $_GET['exam_subject_id'] ?? '';

if (!$subject_id) {
    echo json_encode(['success' => false, 'message' => 'Subject ID required.']);
    exit;
}

try {
    // Get exam details to understand the class/department context
    $exam_id = null;
    if ($exam_subject_id) {
        $sql_exam = "SELECT exam_id FROM exam_subjects WHERE id = ? AND is_deleted = 0";
        $stmt_exam = $conn->prepare($sql_exam);
        $stmt_exam->bind_param('i', $exam_subject_id);
        $stmt_exam->execute();
        $result_exam = $stmt_exam->get_result();
        if ($result_exam->num_rows > 0) {
            $exam_data = $result_exam->fetch_assoc();
            $exam_id = $exam_data['exam_id'];
        }
        $stmt_exam->close();
    }
    
    if (!$exam_id) {
        echo json_encode(['success' => false, 'message' => 'Exam context not found.']);
        exit;
    }
    
    // Get exam details
    $sql_exam_details = "SELECT class_id, department_id FROM exams WHERE id = ? AND is_deleted = 0";
    $stmt_exam_details = $conn->prepare($sql_exam_details);
    $stmt_exam_details->bind_param('i', $exam_id);
    $stmt_exam_details->execute();
    $result_exam_details = $stmt_exam_details->get_result();
    
    if ($result_exam_details->num_rows === 0) {
        throw new Exception("Exam not found");
    }
    
    $exam = $result_exam_details->fetch_assoc();
    $stmt_exam_details->close();
    
    // Get teachers assigned to this subject for this class/department
    $sql = "SELECT 
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
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiii', $exam['class_id'], $subject_id, $exam['department_id'], $exam['department_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }
    
    $teachers = [];
    while ($row = $result->fetch_assoc()) {
        $row['full_name'] = $row['first_name'] . ' ' . $row['last_name'];
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