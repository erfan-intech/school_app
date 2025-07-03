<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$class_id = $_POST['class_id'] ?? '';
$section_id = $_POST['section_id'] ?? '';
$department_id = isset($_POST['department_id']) && !empty($_POST['department_id']) ? $_POST['department_id'] : null;
$action = $_POST['action'] ?? '';

if (!$class_id || !$section_id || !in_array($action, ['add','remove'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit;
}

if ($action === 'add') {
    // Check if record already exists (including soft deleted)
    if ($department_id === null) {
        $checkStmt = $conn->prepare('SELECT id, is_deleted FROM class_dept_sec WHERE class_id=? AND section_id=? AND department_id IS NULL');
        $checkStmt->bind_param('ii', $class_id, $section_id);
    } else {
        $checkStmt = $conn->prepare('SELECT id, is_deleted FROM class_dept_sec WHERE class_id=? AND section_id=? AND department_id=?');
        $checkStmt->bind_param('iii', $class_id, $section_id, $department_id);
    }
    
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $checkStmt->close();
    
    if ($result->num_rows > 0) {
        // Record exists, check if it's soft deleted
        $existingRecord = $result->fetch_assoc();
        if ($existingRecord['is_deleted'] == 1) {
            // Reactivate the soft deleted record
            $stmt = $conn->prepare('UPDATE class_dept_sec SET is_deleted = 0 WHERE id = ?');
            $stmt->bind_param('i', $existingRecord['id']);
            $success = $stmt->execute();
            $stmt->close();
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Section reassigned to class successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to reassign section to class.']);
            }
        } else {
            // Record already exists and is active
            echo json_encode(['success' => true, 'message' => 'Section is already assigned to this class.']);
        }
    } else {
        // Record doesn't exist, insert new one
        if ($department_id === null) {
            $stmt = $conn->prepare('INSERT INTO class_dept_sec (class_id, section_id, department_id, is_deleted) VALUES (?, ?, NULL, 0)');
            $stmt->bind_param('ii', $class_id, $section_id);
        } else {
            $stmt = $conn->prepare('INSERT INTO class_dept_sec (class_id, section_id, department_id, is_deleted) VALUES (?, ?, ?, 0)');
            $stmt->bind_param('iii', $class_id, $section_id, $department_id);
        }
        
        $success = $stmt->execute();
        $stmt->close();
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Section assigned to class successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to assign section to class.']);
        }
    }
} else {
    // Handle NULL department_id properly for soft deletion
    if ($department_id === null) {
        $stmt = $conn->prepare('UPDATE class_dept_sec SET is_deleted = 1 WHERE class_id=? AND section_id=? AND department_id IS NULL AND is_deleted = 0');
        $stmt->bind_param('ii', $class_id, $section_id);
    } else {
        $stmt = $conn->prepare('UPDATE class_dept_sec SET is_deleted = 1 WHERE class_id=? AND section_id=? AND department_id=? AND is_deleted = 0');
        $stmt->bind_param('iii', $class_id, $section_id, $department_id);
    }
    
    $success = $stmt->execute();
    $stmt->close();
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Section removed from class successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove section from class.']);
    }
}

$conn->close(); 