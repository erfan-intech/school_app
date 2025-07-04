<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$exam_id = $_GET['exam_id'] ?? '';
if (!$exam_id) {
    echo json_encode(['success' => false, 'message' => 'Exam ID required.']);
    exit;
}

try {
    // Get exam details
    $sql = "SELECT 
                e.id,
                e.exam_name,
                e.description,
                e.class_id,
                e.department_id,
                e.section_id,
                e.exam_type_id,
                e.academic_year,
                e.created_by,
                e.creation_date,
                c.name AS class_name,
                d.name AS department_name,
                s.name AS section_name,
                et.type_name AS exam_type_name
            FROM exams e
            LEFT JOIN classes c ON e.class_id = c.id
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN sections s ON e.section_id = s.id
            LEFT JOIN exam_types et ON e.exam_type_id = et.id
            WHERE e.id = ? AND e.is_deleted = 0
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result || $result->num_rows === 0) {
        throw new Exception('Exam not found.');
    }
    $exam = $result->fetch_assoc();
    $stmt->close();

    // Get exam subjects
    $sql_subjects = "SELECT 
                        es.id,
                        es.subject_id,
                        es.exam_date,
                        es.start_time,
                        es.end_time,
                        es.teacher_id,
                        es.total_marks,
                        es.pass_mark,
                        es.exam_status,
                        es.room_number,
                        es.instructions,
                        sub.name AS subject_name,
                        t.first_name AS teacher_first_name,
                        t.last_name AS teacher_last_name,
                        t.profile_picture AS teacher_profile_picture
                    FROM exam_subjects es
                    LEFT JOIN subjects sub ON es.subject_id = sub.id
                    LEFT JOIN teachers t ON es.teacher_id = t.id
                    WHERE es.exam_id = ? AND es.is_deleted = 0
                    ORDER BY es.exam_date ASC, es.start_time ASC";
    $stmt_subjects = $conn->prepare($sql_subjects);
    $stmt_subjects->bind_param('i', $exam_id);
    $stmt_subjects->execute();
    $result_subjects = $stmt_subjects->get_result();
    
    $exam_subjects = [];
    while ($row = $result_subjects->fetch_assoc()) {
        // Add teacher name as a combined field
        $row['teacher_name'] = ($row['teacher_first_name'] && $row['teacher_last_name']) 
            ? $row['teacher_first_name'] . ' ' . $row['teacher_last_name'] 
            : null;
        
        // Remove individual teacher name fields
        unset($row['teacher_first_name']);
        unset($row['teacher_last_name']);
        
        $exam_subjects[] = $row;
    }
    $stmt_subjects->close();

    // Combine exam and subjects data
    $exam['subjects'] = $exam_subjects;
    $exam['subject_count'] = count($exam_subjects);

    echo json_encode(['success' => true, 'data' => $exam]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
$conn->close();
?> 