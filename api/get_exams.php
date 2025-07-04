<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

try {
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
                et.type_name AS exam_type_name,
                COUNT(es.id) AS subject_count,
                COUNT(CASE WHEN es.exam_status = 'completed' THEN 1 END) AS completed_subjects,
                COUNT(CASE WHEN es.exam_status = 'ongoing' THEN 1 END) AS ongoing_subjects,
                COUNT(CASE WHEN es.exam_status = 'scheduled' THEN 1 END) AS scheduled_subjects,
                (SELECT COUNT(*) FROM class_dept_sec WHERE class_id = e.class_id AND department_id IS NOT NULL AND is_deleted = 0) AS class_has_departments,
                (SELECT COUNT(*) FROM class_dept_sec WHERE class_id = e.class_id AND section_id IS NOT NULL AND is_deleted = 0) AS class_has_sections
            FROM exams e
            LEFT JOIN classes c ON e.class_id = c.id
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN sections s ON e.section_id = s.id
            LEFT JOIN exam_types et ON e.exam_type_id = et.id
            LEFT JOIN exam_subjects es ON e.id = es.exam_id AND es.is_deleted = 0
            WHERE e.is_deleted = 0
            GROUP BY e.id
            ORDER BY e.creation_date DESC";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }
    
    $exams = [];
    while ($row = $result->fetch_assoc()) {
        // Format the exam name for display
        $display_name = $row['exam_name'];
        if ($row['subject_count'] > 0) {
            $display_name .= " ({$row['subject_count']} subjects)";
        }
        $row['display_name'] = $display_name;
        
        $exams[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $exams
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?> 