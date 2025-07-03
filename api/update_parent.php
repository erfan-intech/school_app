<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$id = $_POST['id'] ?? '';
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$gender = $_POST['gender'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$address = $_POST['address'] ?? '';
$profile_picture = null;
$remove_photo = isset($_POST['remove_photo']) ? $_POST['remove_photo'] : '0';

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Parent ID is required.']);
    exit;
}

// Check if parent exists
$stmt = $conn->prepare('SELECT id, email, phone FROM parents WHERE id = ? AND is_deleted = 0');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Parent not found.']);
    $stmt->close();
    $conn->close();
    exit;
}

$existingParent = $result->fetch_assoc();
$stmt->close();

// Validate email format if provided
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// Check if email already exists (excluding current parent)
if ($email && $email !== $existingParent['email']) {
    $stmt = $conn->prepare("SELECT id FROM parents WHERE email = ? AND id != ? AND is_deleted = 0");
    $stmt->bind_param('si', $email, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email address already exists.']);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
}

// Check if phone already exists (excluding current parent)
if ($phone && $phone !== $existingParent['phone']) {
    $stmt = $conn->prepare("SELECT id FROM parents WHERE phone = ? AND id != ? AND is_deleted = 0");
    $stmt->bind_param('si', $phone, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Phone number already exists.']);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
}

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

if ($remove_photo === '1' && !$profile_picture) {
    // Get current photo filename
    $stmt = $conn->prepare('SELECT profile_picture FROM parents WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($old_photo);
    $stmt->fetch();
    $stmt->close();
    if ($old_photo && file_exists("../uploads/parents/" . $old_photo)) {
        unlink("../uploads/parents/" . $old_photo);
    }
    $stmt = $conn->prepare('UPDATE parents SET profile_picture=NULL WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

if ($profile_picture) {
    $stmt = $conn->prepare("UPDATE parents SET first_name=?, last_name=?, gender=?, phone=?, email=?, address=?, profile_picture=? WHERE id=?");
    $stmt->bind_param('sssssssi', $first_name, $last_name, $gender, $phone, $email, $address, $profile_picture, $id);
} else {
    $stmt = $conn->prepare("UPDATE parents SET first_name=?, last_name=?, gender=?, phone=?, email=?, address=? WHERE id=?");
    $stmt->bind_param('ssssssi', $first_name, $last_name, $gender, $phone, $email, $address, $id);
}
$success = $stmt->execute();
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Parent information updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update parent information.']);
}
$stmt->close();
$conn->close(); 