<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$gender = $_POST['gender'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$address = $_POST['address'] ?? '';
$profile_picture = null;

// Handle file upload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('parent_', true) . '.' . $ext;
    $upload_dir = '../uploads/parents/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $filename);
    $profile_picture = $filename;
}

if (!$first_name) {
    echo json_encode(['success' => false, 'message' => 'First name is required.']);
    exit;
}

// Validate email format if provided
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// Check if email already exists
if ($email) {
    $stmt = $conn->prepare("SELECT id FROM parents WHERE email = ? AND is_deleted = 0");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email address already exists.']);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
}

// Check if phone already exists
if ($phone) {
    $stmt = $conn->prepare("SELECT id FROM parents WHERE phone = ? AND is_deleted = 0");
    $stmt->bind_param('s', $phone);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Phone number already exists.']);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
}

// 1. Create user
$username = strtolower($first_name . ($last_name ? '.' . $last_name : '') . rand(100,999));
$password = password_hash('parent123', PASSWORD_DEFAULT); // Default password
$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'parent')");
$stmt->bind_param('ss', $username, $password);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to create user.']);
    $stmt->close();
    $conn->close();
    exit;
}
$user_id = $stmt->insert_id;
$stmt->close();

// 2. Create parent
$stmt = $conn->prepare("INSERT INTO parents (user_id, first_name, last_name, gender, phone, email, address, profile_picture, is_deleted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
$stmt->bind_param('isssssss', $user_id, $first_name, $last_name, $gender, $phone, $email, $address, $profile_picture);
$success = $stmt->execute();
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Parent added successfully. Default password is "parent123".']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add parent.']);
}
$stmt->close();
$conn->close(); 