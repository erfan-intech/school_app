<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../includes/db_connect.php';

// Get statistics
$stats = [];

// Total students
$result = $conn->query("SELECT COUNT(*) as count FROM students WHERE is_deleted = 0");
$stats['students'] = $result->fetch_assoc()['count'];

// Total teachers
$result = $conn->query("SELECT COUNT(*) as count FROM teachers WHERE is_deleted = 0");
$stats['teachers'] = $result->fetch_assoc()['count'];

// Total classes
$result = $conn->query("SELECT COUNT(*) as count FROM classes WHERE is_deleted = 0");
$stats['classes'] = $result->fetch_assoc()['count'];

// Total subjects
$result = $conn->query("SELECT COUNT(*) as count FROM subjects WHERE is_deleted = 0");
$stats['subjects'] = $result->fetch_assoc()['count'];

// Total exams
$result = $conn->query("SELECT COUNT(*) as count FROM exams WHERE is_deleted = 0");
$stats['exams'] = $result->fetch_assoc()['count'];

// Total parents
$result = $conn->query("SELECT COUNT(*) as count FROM parents WHERE is_deleted = 0");
$stats['parents'] = $result->fetch_assoc()['count'];

$conn->close();
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Dashboard</h1>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3><?php echo $stats['students']; ?></h3>
                    <p class="mb-0">Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3><?php echo $stats['teachers']; ?></h3>
                    <p class="mb-0">Teachers</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3><?php echo $stats['classes']; ?></h3>
                    <p class="mb-0">Classes</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3><?php echo $stats['subjects']; ?></h3>
                    <p class="mb-0">Subjects</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3><?php echo $stats['exams']; ?></h3>
                    <p class="mb-0">Exams</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h3><?php echo $stats['parents']; ?></h3>
                    <p class="mb-0">Parents</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <a href="students.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-users"></i> Manage Students
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="teachers.php" class="btn btn-outline-success w-100">
                                <i class="fas fa-chalkboard-teacher"></i> Manage Teachers
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="classes.php" class="btn btn-outline-info w-100">
                                <i class="fas fa-school"></i> Manage Classes
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="subjects.php" class="btn btn-outline-warning w-100">
                                <i class="fas fa-book"></i> Manage Subjects
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="exams.php" class="btn btn-outline-danger w-100">
                                <i class="fas fa-file-alt"></i> Manage Exams
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="attendance.php" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-calendar-check"></i> Attendance
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Activity</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Welcome to the School Management System!</p>
                    <p>This dashboard provides an overview of your school's data and quick access to all management features.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Student Management</li>
                        <li><i class="fas fa-check text-success"></i> Teacher Management</li>
                        <li><i class="fas fa-check text-success"></i> Class Management</li>
                        <li><i class="fas fa-check text-success"></i> Subject Management</li>
                        <li><i class="fas fa-check text-success"></i> Exam Management</li>
                        <li><i class="fas fa-check text-success"></i> Attendance Tracking</li>
                        <li><i class="fas fa-check text-success"></i> Grade Management</li>
                        <li><i class="fas fa-check text-success"></i> Report Generation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
