<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

// Get search parameter
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build SQL query with search
$sql = "SELECT t.*, d.name AS department_name FROM teachers t LEFT JOIN departments d ON t.department_id = d.id WHERE t.is_deleted=0";

if (!empty($search)) {
    $search = '%' . $search . '%';
    $sql .= " AND (t.first_name LIKE ? OR t.last_name LIKE ? OR t.phone LIKE ? OR t.position LIKE ? OR t.id LIKE ?)";
}

$sql .= " ORDER BY t.id";

$stmt = $conn->prepare($sql);

if (!empty($search)) {
    $stmt->bind_param('sssss', $search, $search, $search, $search, $search);
}

$stmt->execute();
$result = $stmt->get_result();
$teachers = [];
$today = date('Y-m-d');
$sl_no = 1;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (empty($row['profile_picture'])) {
            $row['profile_picture'] = null;
        }
        // Add sequential number
        $row['sl_no'] = $sl_no++;
        
        // Get today's attendance status
        $attendance_sql = "SELECT status FROM attendance WHERE user_id=? AND user_type='teacher' AND date=? LIMIT 1";
        $attendance_stmt = $conn->prepare($attendance_sql);
        $attendance_stmt->bind_param('is', $row['user_id'], $today);
        $attendance_stmt->execute();
        $attendance_stmt->bind_result($attendance_status);
        if ($attendance_stmt->fetch()) {
            $row['attendance_status_today'] = ucfirst($attendance_status);
        } else {
            $row['attendance_status_today'] = '';
        }
        $attendance_stmt->close();
        $teachers[] = $row;
    }
}

echo json_encode(['success' => true, 'data' => $teachers]);
$stmt->close();
$conn->close();
?>
