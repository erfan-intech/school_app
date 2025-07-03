<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$sql = "SELECT id, name FROM sections WHERE is_deleted=0";
$result = $conn->query($sql);
$sections = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }
}
echo json_encode(['success' => true, 'data' => $sections]);
$conn->close();
?> 