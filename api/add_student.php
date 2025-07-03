<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$dob = $_POST['dob'] ?? '';
$gender = $_POST['gender'] ?? '';
$address = $_POST['address'] ?? '';
$admission_date = $_POST['admission_date'] ?? '';
$current_class_id = isset($_POST['current_class_id']) && $_POST['current_class_id'] !== '' ? $_POST['current_class_id'] : null;
$current_section_id = isset($_POST['current_section_id']) && $_POST['current_section_id'] !== '' ? $_POST['current_section_id'] : null;
$current_department_id = isset($_POST['current_department_id']) && $_POST['current_department_id'] !== '' ? $_POST['current_department_id'] : null;
$father_id = isset($_POST['father_id']) && $_POST['father_id'] !== '' ? $_POST['father_id'] : null;
$mother_id = isset($_POST['mother_id']) && $_POST['mother_id'] !== '' ? $_POST['mother_id'] : null;
$local_guardian_id = isset($_POST['local_guardian_id']) && $_POST['local_guardian_id'] !== '' ? $_POST['local_guardian_id'] : null;
$roll_no = $_POST['roll_no'] ?? '';
$note = $_POST['note'] ?? '';

// Handle file upload
$profile_picture = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('student_', true) . '.' . $ext;
    $upload_dir = '../uploads/students/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $filename);
    $profile_picture = $filename;
}

if (!$first_name) {
    echo json_encode(['success' => false, 'message' => 'First name required.']);
    exit;
}

if (!$roll_no) {
    echo json_encode(['success' => false, 'message' => 'Roll number is required.']);
    exit;
}

// Check for unique roll_no in class
$stmt = $conn->prepare('SELECT id FROM students WHERE current_class_id=? AND roll_no=? AND is_deleted=0');
$stmt->bind_param('ii', $current_class_id, $roll_no);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Roll number already exists in this class.']);
    exit;
}
$stmt->close();

// 1. Create user
$username = strtolower($first_name . ($last_name ? '.' . $last_name : '') . rand(100,999));
$password = password_hash('student123', PASSWORD_DEFAULT); // Default password
$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'student')");
$stmt->bind_param('ss', $username, $password);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to create user.']);
    $stmt->close();
    $conn->close();
    exit;
}
$user_id = $stmt->insert_id;
$stmt->close();

// 2. Create student
$stmt = $conn->prepare("INSERT INTO students (user_id, first_name, last_name, profile_picture, dob, gender, address, admission_date, current_class_id, current_section_id, current_department_id, father_id, mother_id, local_guardian_id, roll_no, note, is_deleted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
$stmt->bind_param('isssssssiiiiiiss', $user_id, $first_name, $last_name, $profile_picture, $dob, $gender, $address, $admission_date, $current_class_id, $current_section_id, $current_department_id, $father_id, $mother_id, $local_guardian_id, $roll_no, $note);
$success = $stmt->execute();
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Student added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add student.']);
}
$stmt->close();
$conn->close();
?> 