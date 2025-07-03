<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';
$class_id = $_GET['class_id'] ?? '';
if (!$class_id) {
    echo json_encode(['success' => false, 'message' => 'Class ID required.']);
    exit;
}
$sql = 'SELECT s.*, c.name AS class_name, d.name AS department_name, sec.name AS section_name,
    pf.first_name AS father_first_name, pf.last_name AS father_last_name, pf.phone AS father_phone,
    pm.first_name AS mother_first_name, pm.last_name AS mother_last_name, pm.phone AS mother_phone,
    pl.first_name AS local_guardian_first_name, pl.last_name AS local_guardian_last_name, pl.phone AS local_guardian_phone
FROM students s
LEFT JOIN classes c ON s.current_class_id = c.id
LEFT JOIN departments d ON s.current_department_id = d.id
LEFT JOIN sections sec ON s.current_section_id = sec.id
LEFT JOIN parents pf ON s.father_id = pf.id
LEFT JOIN parents pm ON s.mother_id = pm.id
LEFT JOIN parents pl ON s.local_guardian_id = pl.id
WHERE s.current_class_id=? AND s.is_deleted=0';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $class_id);
$stmt->execute();
$result = $stmt->get_result();
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
echo json_encode(['success' => true, 'data' => $students]);
$stmt->close();
$conn->close();
?> 