<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';
$user_type = $_GET['user_type'] ?? 'student';
$class_id = $_GET['class_id'] ?? '';
$date = $_GET['date'] ?? '';
$user_id = $_GET['user_id'] ?? '';

$params = [];
$types = '';

if ($user_type === 'teacher') {
    $sql = "SELECT a.id, t.id AS teacher_id, CONCAT(t.first_name, ' ', t.last_name) AS teacher_name, a.date, a.status, a.time_in, a.time_out
            FROM attendance a
            JOIN teachers t ON a.user_id = t.user_id
            WHERE a.user_type='teacher'";
    if ($user_id) {
        $sql .= " AND t.id=?";
        $params[] = $user_id;
        $types .= 'i';
    }
    if ($date) {
        $sql .= " AND a.date=?";
        $params[] = $date;
        $types .= 's';
    }
    $sql .= " ORDER BY a.date DESC, t.first_name, t.last_name";
} else {
    $sql = "SELECT a.id, s.id AS student_id, CONCAT(s.first_name, ' ', s.last_name) AS student_name, c.name AS class_name, s.roll_no as roll_no, a.date, a.status, a.time_in, a.time_out
            FROM attendance a
            JOIN students s ON a.user_id = s.user_id
            JOIN classes c ON a.class_id = c.id
            WHERE a.user_type='student'";
    if ($class_id) {
        $sql .= " AND a.class_id=?";
        $params[] = $class_id;
        $types .= 'i';
    }
    if ($date) {
        $sql .= " AND a.date=?";
        $params[] = $date;
        $types .= 's';
    }
    $sql .= " ORDER BY a.date DESC, c.name, s.first_name, s.last_name";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$records = [];
while ($row = $result->fetch_assoc()) {
    $records[] = $row;
}
echo json_encode(['success' => true, 'data' => $records]);
$stmt->close();
$conn->close();
?>
