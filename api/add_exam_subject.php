<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

// session_start();
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(['success' => false, 'message' => 'User not authenticated']);
//     exit;
// }

try {
    // Validate required fields
    $required_fields = ['exam_id', 'subject_id', 'exam_date', 'total_marks', 'pass_mark'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Required field '$field' is missing");
        }
    }

    // Assign variables safely
    $exam_id = $_POST['exam_id'];
    $subject_id = $_POST['subject_id'];
    $teacher_id = isset($_POST['teacher_id']) && $_POST['teacher_id'] !== '' ? $_POST['teacher_id'] : null;
    $exam_date = $_POST['exam_date'];
    
    // Format time values to HH:MM:SS format for MySQL
    $start_time = null;
    if (isset($_POST['start_time']) && $_POST['start_time'] !== '') {
        $start_time = $_POST['start_time'];
        // If time is in HH:MM format, convert to HH:MM:SS
        if (preg_match('/^\d{2}:\d{2}$/', $start_time)) {
            $start_time .= ':00';
        }
    }
    
    $end_time = null;
    if (isset($_POST['end_time']) && $_POST['end_time'] !== '') {
        $end_time = $_POST['end_time'];
        // If time is in HH:MM format, convert to HH:MM:SS
        if (preg_match('/^\d{2}:\d{2}$/', $end_time)) {
            $end_time .= ':00';
        }
    }
    
    $total_marks = $_POST['total_marks'];
    $pass_mark = $_POST['pass_mark'];
    $room_number = isset($_POST['room_number']) && $_POST['room_number'] !== '' ? $_POST['room_number'] : null;
    $instructions = isset($_POST['instructions']) && $_POST['instructions'] !== '' ? $_POST['instructions'] : null;
    
    // Get department_id from form (not from exam)
    $department_id = isset($_POST['department_id']) && $_POST['department_id'] !== '' ? $_POST['department_id'] : null;

    // Check if exam exists
    $stmt = $conn->prepare("SELECT class_id, department_id, section_id FROM exams WHERE id = ? AND is_deleted = 0");
    $stmt->bind_param('i', $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Exam not found");
    }
    $exam = $result->fetch_assoc();
    $stmt->close();

    // Check if subject is already added to this exam
    $stmt = $conn->prepare("SELECT id FROM exam_subjects WHERE exam_id = ? AND subject_id = ? AND is_deleted = 0");
    $stmt->bind_param('ii', $exam_id, $subject_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception("Subject is already added to this exam");
    }
    $stmt->close();

    // Check if subject is assigned to the class-department combination
    // Use the form's department_id for validation
    if ($department_id) {
        // Form has a specific department - check for that department or parent level
        $sql_subject_check = "SELECT id FROM class_dept_sub 
                             WHERE class_id = ? 
                             AND subject_id = ? 
                             AND is_deleted = 0
                             AND (department_id = ? OR department_id IS NULL)";
        $stmt = $conn->prepare($sql_subject_check);
        if (!$stmt) {
            throw new Exception("Prepare statement failed for subject validation: " . $conn->error);
        }
        $stmt->bind_param('iii', $exam['class_id'], $subject_id, $department_id);
    } else {
        // Form has no department selected - check for any department or parent level
        $sql_subject_check = "SELECT id FROM class_dept_sub 
                             WHERE class_id = ? 
                             AND subject_id = ? 
                             AND is_deleted = 0";
        $stmt = $conn->prepare($sql_subject_check);
        if (!$stmt) {
            throw new Exception("Prepare statement failed for subject validation: " . $conn->error);
        }
        $stmt->bind_param('ii', $exam['class_id'], $subject_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Subject is not assigned to this class-department combination");
    }
    $stmt->close();

    // If teacher is specified, check if teacher is assigned to this subject for this class
    if ($teacher_id) {
        // Use the form's department_id for validation
        if ($department_id) {
            // Form has a specific department - check for that department or parent level
            $sql_teacher_check = "SELECT id FROM class_dept_sub_teacher 
                                 WHERE class_id = ? 
                                 AND subject_id = ?
                                 AND teacher_id = ? 
                                 AND is_deleted = 0
                                 AND (department_id = ? OR department_id IS NULL)";
            $stmt = $conn->prepare($sql_teacher_check);
            if (!$stmt) {
                throw new Exception("Prepare statement failed for teacher validation: " . $conn->error);
            }
            $stmt->bind_param('iiii', $exam['class_id'], $subject_id, $teacher_id, $department_id);
        } else {
            // Form has no department selected - check for any department or parent level
            $sql_teacher_check = "SELECT id FROM class_dept_sub_teacher 
                                 WHERE class_id = ? 
                                 AND subject_id = ?
                                 AND teacher_id = ? 
                                 AND is_deleted = 0";
            $stmt = $conn->prepare($sql_teacher_check);
            if (!$stmt) {
                throw new Exception("Prepare statement failed for teacher validation: " . $conn->error);
            }
            $stmt->bind_param('iii', $exam['class_id'], $subject_id, $teacher_id);
        }
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            throw new Exception("Teacher is not assigned to this subject for this class-department combination");
        }
        $stmt->close();
    }

    // Validate marks
    if ($pass_mark > $total_marks) {
        throw new Exception("Pass mark cannot be greater than total marks");
    }

    // Insert exam subject
    $sql = "INSERT INTO exam_subjects (
                exam_id, class_id, department_id, subject_id, teacher_id, exam_date, start_time, end_time,
                total_marks, pass_mark, room_number, instructions
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    
    $stmt->bind_param("iiiiisssiiss",
        $exam_id,
        $exam['class_id'],
        $department_id,
        $subject_id,
        $teacher_id,
        $exam_date,
        $start_time,
        $end_time,
        $total_marks,
        $pass_mark,
        $room_number,
        $instructions
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $exam_subject_id = $conn->insert_id;
    
    echo json_encode([
        'success' => true,
        'message' => 'Subject added to exam successfully',
        'exam_subject_id' => $exam_subject_id
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