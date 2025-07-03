<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$attendance_id = $_POST['attendance_id'] ?? '';
$status = $_POST['status'] ?? '';
$time_in = $_POST['time_in'] ?? '';
$time_out = $_POST['time_out'] ?? '';

if (!$attendance_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

if ($status === 'absent') {
    $time_in = '00:00';
}

$sql = "UPDATE attendance SET status=?, time_in=?, time_out=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sssi', $status, $time_in, $time_out, $attendance_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update attendance.']);
}
$stmt->close();
$conn->close();
