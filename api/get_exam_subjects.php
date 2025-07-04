<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$exam_id = $_GET['exam_id'] ?? '';
if (!$exam_id) {
    echo json_encode(['success' => false, 'message' => 'Exam ID required.']);
    exit;
}

try {
    $sql = "SELECT 
                es.id,
                es.subject_id,
                es.teacher_id,
                es.department_id,
                es.exam_date,
                es.start_time,
                es.end_time,
                es.total_marks,
                es.pass_mark,
                es.exam_status,
                es.room_number,
                es.instructions,
                es.created_at,
                es.updated_at,
                sub.name AS subject_name,
                t.first_name AS teacher_first_name,
                t.last_name AS teacher_last_name,
                t.profile_picture AS teacher_profile_picture,
                CONCAT(t.first_name, ' ', t.last_name) AS teacher_full_name,
                d.name AS department_name
            FROM exam_subjects es
            LEFT JOIN subjects sub ON es.subject_id = sub.id
            LEFT JOIN teachers t ON es.teacher_id = t.id
            LEFT JOIN departments d ON es.department_id = d.id
            WHERE es.exam_id = ? AND es.is_deleted = 0
            ORDER BY es.exam_date ASC, es.start_time ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $subjects
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