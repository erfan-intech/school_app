<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$sql = 'SELECT id, first_name, last_name, gender, phone, email, address, profile_picture FROM parents WHERE is_deleted=0 ORDER BY id ASC';
$result = $conn->query($sql);
$parents = [];
$sl_no = 1;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (empty($row['profile_picture'])) {
            $row['profile_picture'] = null;
        }
        $row['sl_no'] = $sl_no++;
        $parents[] = $row;
    }
}

echo json_encode(['success' => true, 'data' => $parents]);
$conn->close(); 