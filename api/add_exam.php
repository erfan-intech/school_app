<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

// session_start();
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(['success' => false, 'message' => 'User not authenticated']);
//     exit;
// }

try {
    // Validate required fields for the new exam structure
    $required_fields = ['exam_name', 'class_id', 'exam_type_id', 'academic_year'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Required field '$field' is missing");
        }
    }

    // Assign variables safely
    $exam_name     = $_POST['exam_name'];
    $description   = isset($_POST['description']) && $_POST['description'] !== '' ? $_POST['description'] : null;
    $class_id      = $_POST['class_id'];
    $department_id = isset($_POST['department_id']) && $_POST['department_id'] !== '' ? $_POST['department_id'] : null;
    $section_id    = isset($_POST['section_id']) && $_POST['section_id'] !== '' ? $_POST['section_id'] : null;
    $exam_type_id  = $_POST['exam_type_id'];
    $academic_year = $_POST['academic_year'];
    $created_by    = 'dev_user';

    // Prepare the SQL statement for the new exam structure
    $sql = "INSERT INTO exams (
                exam_name, description, class_id, department_id, section_id, 
                exam_type_id, academic_year, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
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
        $created_by
    );
    
    // Execute the statement
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $exam_id = $conn->insert_id;
    
    echo json_encode([
        'success' => true,
        'message' => 'Exam created successfully',
        'exam_id' => $exam_id
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