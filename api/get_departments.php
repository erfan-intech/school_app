<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

// $sql = "SELECT id, name FROM departments";
$sql = "SELECT id, name FROM departments WHERE id != 0 AND is_deleted=0";
$result = $conn->query($sql);
$departments = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}
echo json_encode(['success' => true, 'data' => $departments]);
$conn->close();
?> 