<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$id = $_POST['id'] ?? '';
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$dob = $_POST['dob'] ?? '';
$gender = $_POST['gender'] ?? '';
$address = $_POST['address'] ?? '';
$department_id = isset($_POST['department_id']) && $_POST['department_id'] !== '' ? $_POST['department_id'] : null;
$position = $_POST['position'] ?? '';
$join_date = $_POST['join_date'] ?? '';
$leave_date = $_POST['leave_date'] ?? null;
$salary = $_POST['salary'] ?? null;
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$remove_photo = isset($_POST['remove_photo']) ? $_POST['remove_photo'] : '0';

// Handle file upload
$profile_picture = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('teacher_', true) . '.' . $ext;
    $upload_dir = '../uploads/teachers/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $filename);
    $profile_picture = $filename;
}

if (!$id || !$first_name || !$last_name) {
    echo json_encode(['success' => false, 'message' => 'ID, first and last name required.']);
    exit;
}

if ($remove_photo === '1' && !$profile_picture) {
    // Get current photo filename
    $stmt = $conn->prepare('SELECT profile_picture FROM teachers WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($old_photo);
    $stmt->fetch();
    $stmt->close();
    if ($old_photo && file_exists("../uploads/teachers/" . $old_photo)) {
        unlink("../uploads/teachers/" . $old_photo);
    }
    $stmt = $conn->prepare('UPDATE teachers SET profile_picture=NULL WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

if ($profile_picture) {
    $stmt = $conn->prepare("UPDATE teachers SET first_name=?, last_name=?, dob=?, gender=?, phone=?, email=?, address=?, department_id=?, position=?, profile_picture=?, join_date=?, leave_date=?, salary=? WHERE id=?");
    $stmt->bind_param('ssssssssssssdi', $first_name, $last_name, $dob, $gender, $phone, $email, $address, $department_id, $position, $profile_picture, $join_date, $leave_date, $salary, $id);
} else {
    $stmt = $conn->prepare("UPDATE teachers SET first_name=?, last_name=?, dob=?, gender=?, phone=?, email=?, address=?, department_id=?, position=?, join_date=?, leave_date=?, salary=? WHERE id=?");
    $stmt->bind_param('sssssssssssdi', $first_name, $last_name, $dob, $gender, $phone, $email, $address, $department_id, $position, $join_date, $leave_date, $salary, $id);
}
$success = $stmt->execute();
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Teacher updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update teacher.']);
}
$stmt->close();
$conn->close();
?>
