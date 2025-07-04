<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}
try {
    $required = ['exam_id', 'student_id', 'marks_obtained'];
    foreach ($required as $field) {
        if (!isset($_POST[$field]) || $_POST[$field] === '') {
            throw new Exception("Required field '$field' is missing");
        }
    }
    $exam_id = $_POST['exam_id'];
    $student_id = $_POST['student_id'];
    $marks_obtained = $_POST['marks_obtained'];
    $grade_letter = $_POST['grade_letter'] ?? null;
    $grade_point = $_POST['grade_point'] ?? null;
    $remarks = $_POST['remarks'] ?? null;
    // Get total_marks from exam
    $sql_exam = "SELECT total_marks FROM exams WHERE id = ?";
    $stmt_exam = $conn->prepare($sql_exam);
    $stmt_exam->bind_param('i', $exam_id);
    $stmt_exam->execute();
    $stmt_exam->bind_result($total_marks);
    if (!$stmt_exam->fetch()) {
        throw new Exception('Exam not found.');
    }
    $stmt_exam->close();
    // Upsert grade
    $sql = "INSERT INTO grades (student_id, exam_id, subject_id, year, marks_obtained, total_marks, grade_letter, grade_point, remarks, marked_by)
            VALUES (?, ?, (SELECT subject_id FROM exams WHERE id = ?), YEAR(CURDATE()), ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE marks_obtained=VALUES(marks_obtained), total_marks=VALUES(total_marks), grade_letter=VALUES(grade_letter), grade_point=VALUES(grade_point), remarks=VALUES(remarks), marked_by=VALUES(marked_by), marked_at=NOW()";
    $stmt = $conn->prepare($sql);
    $marked_by = $_SESSION['user_id'];
    $stmt->bind_param('iiididsssi', $student_id, $exam_id, $exam_id, $marks_obtained, $total_marks, $grade_letter, $grade_point, $remarks, $marked_by);
    if (!$stmt->execute()) {
        throw new Exception('Failed to update grade: ' . $stmt->error);
    }
    echo json_encode(['success' => true, 'message' => 'Grade updated successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
$stmt->close();
$conn->close();
?> 