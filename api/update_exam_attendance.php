<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}
try {
    $required = ['exam_id', 'student_id', 'status'];
    foreach ($required as $field) {
        if (!isset($_POST[$field]) || $_POST[$field] === '') {
            throw new Exception("Required field '$field' is missing");
        }
    }
    $exam_id = $_POST['exam_id'];
    $student_id = $_POST['student_id'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'] ?? null;
    // Upsert attendance
    $sql = "INSERT INTO exam_attendance (exam_id, student_id, status, remarks)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE status=VALUES(status), remarks=VALUES(remarks), created_at=NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiss', $exam_id, $student_id, $status, $remarks);
    if (!$stmt->execute()) {
        throw new Exception('Failed to update attendance: ' . $stmt->error);
    }
    echo json_encode(['success' => true, 'message' => 'Attendance updated successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
$stmt->close();
$conn->close();
?> 