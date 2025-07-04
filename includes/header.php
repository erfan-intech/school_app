<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">
        <i class="fas fa-graduation-cap me-2"></i>School Management System
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="students.php">
                <i class="fas fa-users me-1"></i>Students
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="teachers.php">
                <i class="fas fa-chalkboard-teacher me-1"></i>Teachers
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="classes.php">
                <i class="fas fa-school me-1"></i>Classes
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="subjects.php">
                <i class="fas fa-book me-1"></i>Subjects
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="parents.php">
                <i class="fas fa-user-friends me-1"></i>Parents
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="attendance.php">
                <i class="fas fa-calendar-check me-1"></i>Attendance
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="exams.php">
                <i class="fas fa-file-alt me-1"></i>Exams
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="../api/logout.php">
                <i class="fas fa-sign-out-alt me-1"></i>Logout
            </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
