<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$class_id = $_POST['class_id'] ?? '';
$teacher_id = $_POST['teacher_id'] ?? '';
$departments = $_POST['departments'] ?? [];
$subjects = $_POST['subjects'] ?? [];

if (!$class_id || !$teacher_id) {
    echo json_encode(['success' => false, 'message' => 'Missing class or teacher ID.']);
    exit;
}

// Get all current assignments for this teacher in this class
$currentAssignments = [];
$sql = "SELECT subject_id, department_id FROM class_teachers WHERE class_id=? AND teacher_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $class_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $key = $row['subject_id'] . '_' . (int)$row['department_id'];
    $currentAssignments[$key] = true;
}
$stmt->close();

$toAssign = [];
$toRemove = $currentAssignments;

if (!empty($departments)) {
    // With departments
    foreach ($departments as $dept_id) {
        foreach ($subjects as $subject_id) {
            // Check if subject belongs to this department
            $check = $conn->prepare('SELECT id FROM class_subjects WHERE class_id=? AND subject_id=? AND department_id=?');
            $check->bind_param('iii', $class_id, $subject_id, $dept_id);
            $check->execute();
            $check->store_result();
            if ($check->num_rows > 0) {
                $key = $subject_id . '_' . (int)$dept_id;
                // If not already assigned, add
                if (!isset($currentAssignments[$key])) {
                    $toAssign[] = ['subject_id' => $subject_id, 'department_id' => $dept_id];
                }
                // If checked, don't remove
                unset($toRemove[$key]);
            }
            $check->close();
        }
    }
} else {
    // No departments
    foreach ($subjects as $subject_id) {
        $check = $conn->prepare('SELECT id FROM class_subjects WHERE class_id=? AND subject_id=? AND (department_id IS NULL OR department_id=0)');
        $check->bind_param('ii', $class_id, $subject_id);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $key = $subject_id . '_0';
            if (!isset($currentAssignments[$key])) {
                $toAssign[] = ['subject_id' => $subject_id, 'department_id' => 0];
            }
            unset($toRemove[$key]);
        }
        $check->close();
    }
}

// Insert new assignments
foreach ($toAssign as $item) {
    // Check for existing assignment with all four fields
    $check = $conn->prepare('SELECT id FROM class_teachers WHERE class_id=? AND teacher_id=? AND subject_id=? AND department_id=?');
    $check->bind_param('iiii', $class_id, $teacher_id, $item['subject_id'], $item['department_id']);
    $check->execute();
    $check->store_result();
    if ($check->num_rows == 0) {
        $insert = $conn->prepare('INSERT INTO class_teachers (class_id, teacher_id, subject_id, department_id) VALUES (?, ?, ?, ?)');
        $insert->bind_param('iiii', $class_id, $teacher_id, $item['subject_id'], $item['department_id']);
        $insert->execute();
        $insert->close();
    }
    $check->close();
}
// Remove unchecked assignments
foreach ($toRemove as $key => $_) {
    list($subject_id, $department_id) = explode('_', $key);
    $delete = $conn->prepare('DELETE FROM class_teachers WHERE class_id=? AND teacher_id=? AND subject_id=? AND department_id=?');
    $delete->bind_param('iiii', $class_id, $teacher_id, $subject_id, $department_id);
    $delete->execute();
    $delete->close();
}
echo json_encode(['success' => true, 'message' => 'Teacher assignments updated.']);
$conn->close(); 