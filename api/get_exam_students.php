<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$exam_id = $_GET['exam_id'] ?? '';
if (!$exam_id) {
    echo json_encode(['success' => false, 'message' => 'Exam ID required.']);
    exit;
}

try {
    // Get exam info for class, department, section, subject
    $sql_exam = "SELECT class_id, department_id, section_id, subject_id FROM exams WHERE id = ? AND is_deleted = 0";
    $stmt_exam = $conn->prepare($sql_exam);
    $stmt_exam->bind_param('i', $exam_id);
    $stmt_exam->execute();
    $result_exam = $stmt_exam->get_result();
    if (!$result_exam || $result_exam->num_rows === 0) {
        throw new Exception('Exam not found.');
    }
    $exam = $result_exam->fetch_assoc();
    $stmt_exam->close();

    // Get students for this class/department/section
    $sql_students = "SELECT s.id AS student_id, s.roll_no, s.first_name, s.last_name, s.current_department_id, s.current_section_id, d.name AS department_name, sec.name AS section_name
        FROM students s
        LEFT JOIN departments d ON s.current_department_id = d.id
        LEFT JOIN sections sec ON s.current_section_id = sec.id
        WHERE s.current_class_id = ? AND s.is_deleted = 0";
    $stmt_students = $conn->prepare($sql_students);
    $stmt_students->bind_param('i', $exam['class_id']);
    $stmt_students->execute();
    $result_students = $stmt_students->get_result();
    $students = [];
    while ($row = $result_students->fetch_assoc()) {
        $students[$row['student_id']] = $row;
    }
    $stmt_students->close();

    // Get grades for this exam
    $sql_grades = "SELECT * FROM grades WHERE exam_id = ?";
    $stmt_grades = $conn->prepare($sql_grades);
    $stmt_grades->bind_param('i', $exam_id);
    $stmt_grades->execute();
    $result_grades = $stmt_grades->get_result();
    while ($row = $result_grades->fetch_assoc()) {
        if (isset($students[$row['student_id']])) {
            $students[$row['student_id']]['marks_obtained'] = $row['marks_obtained'];
            $students[$row['student_id']]['total_marks'] = $row['total_marks'];
            $students[$row['student_id']]['grade_letter'] = $row['grade_letter'];
            $students[$row['student_id']]['grade_point'] = $row['grade_point'];
        }
    }
    $stmt_grades->close();

    // Get exam attendance for this exam
    $sql_att = "SELECT * FROM exam_attendance WHERE exam_id = ?";
    $stmt_att = $conn->prepare($sql_att);
    $stmt_att->bind_param('i', $exam_id);
    $stmt_att->execute();
    $result_att = $stmt_att->get_result();
    while ($row = $result_att->fetch_assoc()) {
        if (isset($students[$row['student_id']])) {
            $students[$row['student_id']]['attendance_status'] = $row['status'];
        }
    }
    $stmt_att->close();

    // Return as array
    echo json_encode(['success' => true, 'data' => array_values($students)]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
$conn->close();
?> 