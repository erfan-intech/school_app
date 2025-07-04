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
    $sql = "SELECT e.*, c.name as class_name FROM exams e 
            JOIN classes c ON e.class_id = c.id 
            WHERE e.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $examData = $result->fetch_assoc();
    $stmt->close();
    
    if (!$examData) {
        echo json_encode(['success' => false, 'message' => 'Exam not found.']);
        exit;
    }
    
    // Get total students in the class
    $sql = "SELECT COUNT(*) as total FROM students WHERE current_class_id = ? AND is_deleted = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $examData['class_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_students = $result->fetch_assoc()['total'];
    $stmt->close();
    
    // Get attendance statistics
    $sql = "SELECT status, COUNT(*) as count FROM exam_attendance WHERE exam_id = ? GROUP BY status";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];
    while ($row = $result->fetch_assoc()) {
        $attendance[$row['status']] = (int)$row['count'];
    }
    $stmt->close();
    
    $present_students = $attendance['present'];
    $absent_students = $attendance['absent'];
    $attendance_percentage = $total_students ? round(($present_students / $total_students) * 100, 2) : 0;
    
    // Get grades and performance statistics
    $sql = "SELECT g.marks_obtained, g.total_marks, s.name as student_name, s.roll_number,
            ea.status as attendance_status
            FROM grades g 
            JOIN students s ON g.student_id = s.id 
            LEFT JOIN exam_attendance ea ON g.student_id = ea.student_id AND g.exam_id = ea.exam_id
            WHERE g.exam_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $total_obtained = 0;
    $count = 0;
    $pass_count = 0;
    $scores = [];
    $grade_distribution = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0, 'N/A' => 0];
    $student_details = [];
    
    while ($row = $result->fetch_assoc()) {
        $count++;
        $marks_obtained = $row['marks_obtained'];
        $total_marks = $row['total_marks'];
        $total_obtained += $marks_obtained;
        $scores[] = $marks_obtained;
        
        // Calculate grade based on percentage
        $percentage = $total_marks > 0 ? ($marks_obtained / $total_marks) * 100 : 0;
        if ($percentage >= 90) $grade = 'A';
        elseif ($percentage >= 80) $grade = 'B';
        elseif ($percentage >= 70) $grade = 'C';
        elseif ($percentage >= 60) $grade = 'D';
        elseif ($percentage >= 0) $grade = 'F';
        else $grade = 'N/A';
        
        if ($percentage >= $examData['pass_mark']) $pass_count++;
        
        $grade_distribution[$grade]++;
        
        $student_details[] = [
            'student_name' => $row['student_name'],
            'roll_number' => $row['roll_number'],
            'score' => $marks_obtained,
            'grade' => $grade,
            'attendance' => $row['attendance_status'] ?? 'N/A'
        ];
    }
    $stmt->close();
    
    $average_score = $count ? round($total_obtained / $count, 2) : 0;
    $highest_score = $scores ? max($scores) : 0;
    $lowest_score = $scores ? min($scores) : 0;
    $pass_rate = $total_students ? round(($pass_count / $total_students) * 100, 2) : 0;
    
    // Prepare report data
    $reportData = [
        'exam_name' => $examData['name'],
        'class_name' => $examData['class_name'],
        'exam_date' => $examData['exam_date'],
        'total_students' => $total_students,
        'present_students' => $present_students,
        'absent_students' => $absent_students,
        'attendance_percentage' => $attendance_percentage,
        'average_score' => $average_score,
        'highest_score' => $highest_score,
        'lowest_score' => $lowest_score,
        'pass_rate' => $pass_rate,
        'grade_distribution' => $grade_distribution,
        'student_details' => $student_details
    ];
    
    // Check if CSV format is requested
    if (isset($_GET['format']) && $_GET['format'] === 'csv') {
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="exam_report_' . $exam_id . '_' . date('Y-m-d') . '.csv"');
        
        // Create CSV content
        $csvContent = "Exam Report: " . $examData['name'] . "\n";
        $csvContent .= "Class: " . $examData['class_name'] . " | Date: " . $examData['exam_date'] . "\n\n";
        
        // Summary statistics
        $csvContent .= "Summary Statistics\n";
        $csvContent .= "Total Students," . $total_students . "\n";
        $csvContent .= "Present Students," . $present_students . "\n";
        $csvContent .= "Absent Students," . $absent_students . "\n";
        $csvContent .= "Attendance Percentage," . $attendance_percentage . "%\n";
        $csvContent .= "Average Score," . $average_score . "\n";
        $csvContent .= "Highest Score," . $highest_score . "\n";
        $csvContent .= "Lowest Score," . $lowest_score . "\n";
        $csvContent .= "Pass Rate," . $pass_rate . "%\n\n";
        
        // Grade distribution
        $csvContent .= "Grade Distribution\n";
        $csvContent .= "Grade,Count\n";
        foreach ($grade_distribution as $grade => $count) {
            $csvContent .= $grade . "," . $count . "\n";
        }
        $csvContent .= "\n";
        
        // Student details
        $csvContent .= "Student Details\n";
        $csvContent .= "Student Name,Roll Number,Score,Grade,Attendance\n";
        foreach ($student_details as $student) {
            $csvContent .= '"' . $student['student_name'] . '",';
            $csvContent .= '"' . $student['roll_number'] . '",';
            $csvContent .= ($student['score'] ?? 'N/A') . ',';
            $csvContent .= ($student['grade'] ?? 'N/A') . ',';
            $csvContent .= ($student['attendance'] ?? 'N/A') . "\n";
        }
        
        echo $csvContent;
        exit;
    }
    
    // Return JSON response
    echo json_encode([
        'success' => true,
        'data' => $reportData
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error generating report: ' . $e->getMessage()
    ]);
}

$conn->close();
?> 