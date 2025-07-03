<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';
$class_id = $_POST['class_id'] ?? '';
$teacher_id = $_POST['teacher_id'] ?? '';
$subject_id = $_POST['subject_id'] ?? null;
$department_id = isset($_POST['department_id']) && $_POST['department_id'] !== '' ? $_POST['department_id'] : 0;
$action = $_POST['action'] ?? '';

if (!$class_id || !$teacher_id || !in_array($action, ['add','removeteacherfromclass','removeteachersubject','remove'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit;
}
if ($action === 'add') {
    $stmt = $conn->prepare('INSERT IGNORE INTO class_teachers (class_id, department_id, teacher_id, subject_id) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('iiii', $class_id, $department_id, $teacher_id, $subject_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
} else if ($action === 'removeteacherfromclass') {
    $stmt = $conn->prepare('DELETE FROM class_teachers WHERE class_id=? AND teacher_id=?');
    $stmt->bind_param('ii', $class_id, $teacher_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
} else if ($action === 'removeteachersubject') {
    $stmt = $conn->prepare('DELETE FROM class_teachers WHERE class_id=? AND department_id = ? AND teacher_id=? AND subject_id=?');
    $stmt->bind_param('iiii', $class_id, $department_id, $teacher_id, $subject_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
} else if ($action === 'remove') {
    // Remove all assignments for this teacher in this class
    $stmt = $conn->prepare('DELETE FROM class_teachers WHERE class_id=? AND teacher_id=?');
    $stmt->bind_param('ii', $class_id, $teacher_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
}
$conn->close(); 