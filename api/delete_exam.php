<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

// Check if user is logged in
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(['success' => false, 'message' => 'User not authenticated']);
//     exit;
// }

try {
    if (!isset($_POST['exam_id']) || empty($_POST['exam_id'])) {
        throw new Exception("Exam ID is required");
    }
    
    $exam_id = intval($_POST['exam_id']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete related grades first
        $sql = "DELETE FROM grades WHERE exam_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $exam_id);
        $stmt->execute();
        $stmt->close();
        
        // Delete related exam attendance
        $sql = "DELETE FROM exam_attendance WHERE exam_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $exam_id);
        $stmt->execute();
        $stmt->close();
        
        // Soft delete the exam (set is_deleted = 1)
        $sql = "UPDATE exams SET is_deleted = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $exam_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to delete exam: " . $stmt->error);
        }
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Exam not found or already deleted");
        }
        
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Exam deleted successfully'
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