<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

// Get parameters
$exam_id = $_GET['exam_id'] ?? '';
$subject_id = $_GET['subject_id'] ?? '';

if (!$exam_id || !$subject_id) {
    echo json_encode(['success' => false, 'message' => 'Exam ID and Subject ID required']);
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
    
    // Check what's in class_dept_sub for this class and subject
    $stmt = $conn->prepare("SELECT * FROM class_dept_sub WHERE class_id = ? AND subject_id = ? AND is_deleted = 0");
    $stmt->bind_param('ii', $exam['class_id'], $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $class_dept_sub_data = [];
    while ($row = $result->fetch_assoc()) {
        $class_dept_sub_data[] = $row;
    }
    $stmt->close();
    
    // Test the validation query
    if ($exam['department_id']) {
        // Exam has a specific department - check for that department or parent level
        $sql_subject_check = "SELECT id FROM class_dept_sub 
                             WHERE class_id = ? 
                             AND subject_id = ? 
                             AND is_deleted = 0
                             AND (department_id = ? OR department_id IS NULL)";
        $stmt = $conn->prepare($sql_subject_check);
        $stmt->bind_param('iii', $exam['class_id'], $subject_id, $exam['department_id']);
    } else {
        // Exam has "All Department" - check for any department or parent level
        $sql_subject_check = "SELECT id FROM class_dept_sub 
                             WHERE class_id = ? 
                             AND subject_id = ? 
                             AND is_deleted = 0";
        $stmt = $conn->prepare($sql_subject_check);
        $stmt->bind_param('ii', $exam['class_id'], $subject_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $validation_results = [];
    while ($row = $result->fetch_assoc()) {
        $validation_results[] = $row;
    }
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'exam_data' => $exam,
        'class_dept_sub_data' => $class_dept_sub_data,
        'validation_query' => $sql_subject_check,
        'validation_params' => [
            'class_id' => $exam['class_id'],
            'subject_id' => $subject_id,
            'department_id' => $exam['department_id']
        ],
        'validation_results' => $validation_results,
        'validation_passed' => count($validation_results) > 0
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?> 