<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

// session_start();
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(['success' => false, 'message' => 'User not authenticated']);
//     exit;
// }

try {
    // Validate required fields for the exam update
    $required_fields = ['exam_id', 'exam_name', 'class_id', 'exam_type_id', 'academic_year'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Required field '$field' is missing");
        }
    }

    // Assign variables safely
    $exam_id       = $_POST['exam_id'];
    $exam_name     = $_POST['exam_name'];
    $description   = isset($_POST['description']) && $_POST['description'] !== '' ? $_POST['description'] : null;
    $class_id      = $_POST['class_id'];
    $department_id = isset($_POST['department_id']) && $_POST['department_id'] !== '' ? $_POST['department_id'] : null;
    $section_id    = isset($_POST['section_id']) && $_POST['section_id'] !== '' ? $_POST['section_id'] : null;
    $exam_type_id  = $_POST['exam_type_id'];
    $academic_year = $_POST['academic_year'];

    // Prepare the SQL statement for updating the exam
    $sql = "UPDATE exams SET 
                exam_name = ?, 
                description = ?, 
                class_id = ?, 
                department_id = ?, 
                section_id = ?, 
                exam_type_id = ?, 
                academic_year = ?
            WHERE id = ? AND is_deleted = 0";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    
    // Bind parameters
    $stmt->bind_param("ssiiiiis",
        $exam_name,
        $description,
        $class_id,
        $department_id,
        $section_id,
        $exam_type_id,
        $academic_year,
        $exam_id
    );
    
    // Execute the statement
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    if ($stmt->affected_rows === 0) {
        throw new Exception("No changes made or exam not found");
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Exam updated successfully'
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