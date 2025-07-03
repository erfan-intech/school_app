<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$id = $_POST['id'] ?? '';

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Parent ID required.']);
    exit;
}

// Check if parent exists and is not already deleted
$stmt = $conn->prepare('SELECT id, first_name, last_name, user_id FROM parents WHERE id = ? AND is_deleted = 0');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Parent not found or already deleted.']);
    $stmt->close();
    $conn->close();
    exit;
}

$parent = $result->fetch_assoc();
$stmt->close();

// Check if parent has any students assigned
$stmt = $conn->prepare('SELECT COUNT(*) as student_count FROM students WHERE (father_id = ? OR mother_id = ? OR local_guardian_id = ?) AND is_deleted = 0');
$stmt->bind_param('iii', $id, $id, $id);
$stmt->execute();
$result = $stmt->get_result();
$studentCount = $result->fetch_assoc()['student_count'];
$stmt->close();

if ($studentCount > 0) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete parent. This parent is assigned to ' . $studentCount . ' student(s). Please reassign or delete the students first.']);
    $conn->close();
    exit;
}

// Soft delete the parent
$stmt = $conn->prepare('UPDATE parents SET is_deleted = 1 WHERE id = ?');
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    // Also soft delete the associated user account
    $stmt2 = $conn->prepare('UPDATE users SET is_deleted = 1 WHERE id = ?');
    $stmt2->bind_param('i', $parent['user_id']);
    $stmt2->execute();
    $stmt2->close();
    
    echo json_encode(['success' => true, 'message' => 'Parent deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete parent.']);
}

$stmt->close();
$conn->close(); 