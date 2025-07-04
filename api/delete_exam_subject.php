<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

try {
    $exam_subject_id = $_POST['exam_subject_id'] ?? '';
    if (!$exam_subject_id) {
        throw new Exception("Exam subject ID required");
    }

    // Check if exam subject exists
    $stmt = $conn->prepare("SELECT id, exam_status FROM exam_subjects WHERE id = ? AND is_deleted = 0");
    $stmt->bind_param('i', $exam_subject_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Exam subject not found");
    }
    $exam_subject = $result->fetch_assoc();
    $stmt->close();

    // Check if exam has started or completed
    if (in_array($exam_subject['exam_status'], ['ongoing', 'completed'])) {
        throw new Exception("Cannot delete exam subject that has started or completed");
    }

    // Soft delete the exam subject
    $sql = "UPDATE exam_subjects SET is_deleted = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $exam_subject_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to delete exam subject: " . $stmt->error);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Exam subject deleted successfully'
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