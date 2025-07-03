<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$user_type = $_POST['user_type'] ?? 'student';
$date = $_POST['date'] ?? '';
$class_id = $_POST['class_id'] ?? '';
$student_ids = $_POST['student_ids'] ?? [];
$statuses = $_POST['status'] ?? [];
$time_ins = $_POST['time_in'] ?? [];
$time_outs = $_POST['time_out'] ?? [];

// Support single-student attendance (from singleAttendanceForm)
if (isset($_POST['student_ids']) && is_array($_POST['student_ids']) && count($_POST['student_ids']) === 1) {
    $student_ids = [$_POST['student_ids'][0]];
    $statuses = [$_POST['status'][0]];
    $time_ins = [$_POST['time_in'][0]];
    $time_outs = [$_POST['time_out'][0]];
} elseif (isset($_POST['student_id'])) {
    $student_ids = [$_POST['student_id']];
    $statuses = [$_POST['status']];
    $time_ins = [$_POST['time_in']];
    $time_outs = [$_POST['time_out']];
}

if ($user_type === 'teacher') {
    $teacher_ids = $_POST['teacher_ids'] ?? [];
    if (!$date || empty($teacher_ids)) {
        echo json_encode(['success' => false, 'message' => 'Date and teachers required.']);
        exit;
    }
    $duplicate_teachers = [];
    for ($i = 0; $i < count($teacher_ids); $i++) {
        $teacher_id = $teacher_ids[$i];
        $status = $statuses[$i] ?? 'present';
        $time_in = $time_ins[$i] ?? null;
        $time_out = $time_outs[$i] ?? null;
        // Get user_id for teacher
        $sql = "SELECT user_id, first_name, last_name FROM teachers WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $teacher_id);
        $stmt->execute();
        $stmt->bind_result($user_id, $first_name, $last_name);
        $stmt->fetch();
        $stmt->close();
        // Check for duplicate
        $check = $conn->prepare("SELECT id FROM attendance WHERE user_id=? AND date=? AND user_type='teacher'");
        $check->bind_param('is', $user_id, $date);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $duplicate_teachers[] = trim($first_name . ' ' . $last_name);
            $check->close();
            continue;
        }
        $check->close();
        $query = "INSERT INTO attendance (user_id, user_type, date, time_in, time_out, status) VALUES (?, 'teacher', ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('issss', $user_id, $date, $time_in, $time_out, $status);
        $stmt->execute();
        $stmt->close();
    }
    if (!empty($duplicate_teachers)) {
        echo json_encode(['success' => false, 'message' => 'Attendance already marked for: ' . implode(', ', $duplicate_teachers)]);
        $conn->close();
        exit;
    }
    echo json_encode(['success' => true, 'message' => 'Attendance marked successfully.']);
    $conn->close();
    exit;
}

if (!$class_id || !$date || empty($student_ids)) {
    echo json_encode(['success' => false, 'message' => 'Class, date, and students required.']);
    exit;
}

$duplicate_students = [];
for ($i = 0; $i < count($student_ids); $i++) {
    $student_id = $student_ids[$i];
    $status = $statuses[$i] ?? 'present';
    $time_in = $time_ins[$i] ?? null;
    $time_out = $time_outs[$i] ?? null;
    // If status is absent, force time_in to 00:00
    if ($status === 'absent') {
        $time_in = '00:00';
    }
    // Get user_id for student
    $sql = "SELECT user_id, first_name, last_name FROM students WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $stmt->bind_result($user_id, $first_name, $last_name);
    $stmt->fetch();
    $stmt->close();
    // Check for duplicate
    $check = $conn->prepare("SELECT id FROM attendance WHERE user_id=? AND class_id=? AND date=? AND user_type='student'");
    $check->bind_param('iis', $user_id, $class_id, $date);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $duplicate_students[] = trim($first_name . ' ' . $last_name);
        $check->close();
        continue;
    }
    $check->close();
    // If time_in is empty, use current server time
    if (!$time_in) {
        $time_in_sql = 'NOW()';
    } else {
        $time_in_sql = '?';
    }
    $query = "INSERT INTO attendance (user_id, user_type, class_id, date, time_in, time_out, status) VALUES (?, 'student', ?, ?, $time_in_sql, ?, ?)";
    if ($time_in) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iissss', $user_id, $class_id, $date, $time_in, $time_out, $status);
    } else {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iisss', $user_id, $class_id, $date, $time_out, $status);
    }
    $stmt->execute();
    $stmt->close();
}
if (!empty($duplicate_students)) {
    echo json_encode(['success' => false, 'message' => 'Attendance already marked for: ' . implode(', ', $duplicate_students)]);
    $conn->close();
    exit;
}
echo json_encode(['success' => true, 'message' => 'Attendance marked successfully.']);
$conn->close();
?>
