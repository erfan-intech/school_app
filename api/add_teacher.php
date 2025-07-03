<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

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

if (!$first_name || !$last_name) {
    echo json_encode(['success' => false, 'message' => 'First and last name required.']);
    exit;
}

// 1. Create user
$username = strtolower($first_name . '.' . $last_name . rand(100,999));
$password = password_hash('teacher123', PASSWORD_DEFAULT); // Default password
$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'teacher')");
$stmt->bind_param('ss', $username, $password);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to create user.']);
    $stmt->close();
    $conn->close();
    exit;
}
$user_id = $stmt->insert_id;
$stmt->close();

// 2. Create teacher
$stmt = $conn->prepare("INSERT INTO teachers (user_id, first_name, last_name, dob, gender, phone, email, address, department_id, position, profile_picture, join_date, leave_date, salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('isssssssissssd', $user_id, $first_name, $last_name, $dob, $gender, $phone, $email, $address, $department_id, $position, $profile_picture, $join_date, $leave_date, $salary);
$success = $stmt->execute();
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Teacher added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add teacher.']);
}
$stmt->close();
$conn->close();
?>
