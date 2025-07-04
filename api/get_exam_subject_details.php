<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$exam_subject_id = $_GET['exam_subject_id'] ?? '';
if (!$exam_subject_id) {
    echo json_encode(['success' => false, 'message' => 'Exam Subject ID required.']);
    exit;
}

try {
    $sql = "SELECT 
                es.id,
                es.exam_id,
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
                e.exam_name,
                t.first_name AS teacher_first_name,
                t.last_name AS teacher_last_name,
                t.profile_picture AS teacher_profile_picture,
                CONCAT(t.first_name, ' ', t.last_name) AS teacher_full_name,
                d.name AS department_name
            FROM exam_subjects es
            LEFT JOIN subjects sub ON es.subject_id = sub.id
            LEFT JOIN exams e ON es.exam_id = e.id
            LEFT JOIN teachers t ON es.teacher_id = t.id
            LEFT JOIN departments d ON es.department_id = d.id
            WHERE es.id = ? AND es.is_deleted = 0
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $exam_subject_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Exam subject not found");
    }
    
    $subject = $result->fetch_assoc();
    
    // Format time values for HTML time inputs (HH:MM format)
    if ($subject['start_time']) {
        $subject['start_time'] = date('H:i', strtotime($subject['start_time']));
    }
    if ($subject['end_time']) {
        $subject['end_time'] = date('H:i', strtotime($subject['end_time']));
    }
    
    echo json_encode([
        'success' => true,
        'data' => $subject
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