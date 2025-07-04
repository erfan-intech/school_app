<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

// session_start();
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(['success' => false, 'message' => 'User not authenticated']);
//     exit;
// }

try {
    if (!isset($_POST['exam_subject_id']) || empty($_POST['exam_subject_id'])) {
        throw new Exception("Exam Subject ID is required");
    }
    
    $exam_subject_id = intval($_POST['exam_subject_id']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Check if exam subject exists
        $sql_check = "SELECT id FROM exam_subjects WHERE id = ? AND is_deleted = 0";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("i", $exam_subject_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows === 0) {
            throw new Exception("Exam subject not found or already deleted");
        }
        $stmt_check->close();
        
        // Delete related grades first
        $sql_grades = "DELETE FROM grades WHERE exam_subject_id = ?";
        $stmt_grades = $conn->prepare($sql_grades);
        $stmt_grades->bind_param("i", $exam_subject_id);
        $stmt_grades->execute();
        $stmt_grades->close();
        
        // Delete related exam attendance
        $sql_attendance = "DELETE FROM exam_attendance WHERE exam_subject_id = ?";
        $stmt_attendance = $conn->prepare($sql_attendance);
        $stmt_attendance->bind_param("i", $exam_subject_id);
        $stmt_attendance->execute();
        $stmt_attendance->close();
        
        // Soft delete the exam subject
        $sql = "UPDATE exam_subjects SET is_deleted = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $exam_subject_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to remove exam subject: " . $stmt->error);
        }
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Exam subject not found or already deleted");
        }
        
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Exam subject removed successfully'
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?> 