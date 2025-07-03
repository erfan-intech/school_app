<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$sql = "SELECT id, name FROM classes WHERE is_deleted=0";
$result = $conn->query($sql);
$classes = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}
echo json_encode(['success' => true, 'data' => $classes]);
$conn->close();
?>
