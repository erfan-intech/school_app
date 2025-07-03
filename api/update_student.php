<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$id = $_POST['id'] ?? '';
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
$remove_photo = isset($_POST['remove_photo']) ? $_POST['remove_photo'] : '0';

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

if (!$id || !$first_name) {
    echo json_encode(['success' => false, 'message' => 'ID and first name required.']);
    exit;
}

if (!$roll_no) {
    echo json_encode(['success' => false, 'message' => 'Roll number is required.']);
    exit;
}

// Check for unique roll_no in class (excluding current student)
$stmt = $conn->prepare('SELECT id FROM students WHERE current_class_id=? AND roll_no=? AND id!=? AND is_deleted=0');
$stmt->bind_param('iii', $current_class_id, $roll_no, $id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Roll number already exists in this class.']);
    exit;
}
$stmt->close();

if ($remove_photo === '1' && !$profile_picture) {
    // Get current photo filename
    $stmt = $conn->prepare('SELECT profile_picture FROM students WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($old_photo);
    $stmt->fetch();
    $stmt->close();
    if ($old_photo && file_exists("../uploads/students/" . $old_photo)) {
        unlink("../uploads/students/" . $old_photo);
    }
    $stmt = $conn->prepare('UPDATE students SET profile_picture=NULL WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

if ($profile_picture) {
    $stmt = $conn->prepare("UPDATE students SET first_name=?, last_name=?, profile_picture=?, dob=?, gender=?, address=?, admission_date=?, current_class_id=?, current_section_id=?, current_department_id=?, father_id=?, mother_id=?, local_guardian_id=?, roll_no=?, note=? WHERE id=?");
    $stmt->bind_param('sssssssiiiiiiisi', $first_name, $last_name, $profile_picture, $dob, $gender, $address, $admission_date, $current_class_id, $current_section_id, $current_department_id, $father_id, $mother_id, $local_guardian_id, $roll_no, $note, $id);
} else {
    $stmt = $conn->prepare("UPDATE students SET first_name=?, last_name=?, dob=?, gender=?, address=?, admission_date=?, current_class_id=?, current_section_id=?, current_department_id=?, father_id=?, mother_id=?, local_guardian_id=?, roll_no=?, note=? WHERE id=?");
    $stmt->bind_param('ssssssiiiiiiisi', $first_name, $last_name, $dob, $gender, $address, $admission_date, $current_class_id, $current_section_id, $current_department_id, $father_id, $mother_id, $local_guardian_id, $roll_no, $note, $id);
}
$success = $stmt->execute();
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Student updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update student.']);
}
$stmt->close();
$conn->close();
?>
