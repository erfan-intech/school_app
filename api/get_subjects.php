<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$sql = 'SELECT id, name FROM subjects WHERE is_deleted=0';
$result = $conn->query($sql);
$subjects = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
}
echo json_encode(['success' => true, 'data' => $subjects]);
$conn->close(); 