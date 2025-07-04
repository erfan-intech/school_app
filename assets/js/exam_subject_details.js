$(document).ready(function() {
    var examSubjectId = $('#examSubjectDetailsApp').data('exam-subject-id');
    
    // Load exam subject details on page load
    loadExamSubjectDetails();
    
    // Handle edit exam subject button
    $('#editExamSubjectBtn').on('click', function() {
        loadEditExamSubjectModal();
    });
    
    // Handle add grade button
    $('#addGradeBtn').on('click', function() {
        loadAddGradeModal();
    });
    
    // Handle edit form submission
    $('#editExamSubjectForm').on('submit', function(e) {
        e.preventDefault();
        updateExamSubject();
    });
    
    // Handle add grade form submission
    $('#addGradeForm').on('submit', function(e) {
        e.preventDefault();
        addGrade();
    });
    
    // Handle quick action buttons
    $('#takeAttendanceBtn').on('click', function() {
        takeAttendance();
    });
    
    $('#gradeStudentsBtn').on('click', function() {
        gradeStudents();
    });
    
    $('#generateSubjectReportBtn').on('click', function() {
        generateSubjectReport();
    });
    
    // Initialize the page
    init();
});

function loadExamSubjectDetails() {
    var examSubjectId = $('#examSubjectDetailsApp').data('exam-subject-id');
    
    $.get('../api/get_exam_subject_details.php', {exam_subject_id: examSubjectId}, function(response) {
        if (response.success) {
            renderExamSubjectDetails(response.data);
        } else {
            alert('Failed to load exam subject details: ' + response.message);
        }
    }).fail(function() {
        alert('Failed to load exam subject details. Please try again.');
    });
}

function renderExamSubjectDetails(examSubject) {
    console.log('Exam subject data:', examSubject);
    
    // Update page header and back link
    var subjectName = examSubject.subject_name || 'Subject';
    var examName = examSubject.exam_name || 'Exam';
    $('#examSubjectNameHeader').html(subjectName + '<br><span class="text-muted small">' + examName + '</span>');
    $('#backToExamLink').attr('href', 'exam_details.php?exam_id=' + examSubject.exam_id);
    
    // Update subject information
    $('#subjectName').text(examSubject.subject_name);
    $('#examDate').text(formatDate(examSubject.exam_date));
    
    var timeStr = '-';
    if (examSubject.start_time && examSubject.end_time) {
        timeStr = examSubject.start_time + ' - ' + examSubject.end_time;
    }
    $('#examTime').text(timeStr);
    
    $('#teacherName').text(examSubject.teacher_full_name || '-');
    $('#roomNumber').text(examSubject.room_number || '-');
    $('#totalMarks').text(examSubject.total_marks);
    $('#passMark').text(examSubject.pass_mark);
    $('#examStatus').html(getStatusBadge(examSubject.exam_status));
    $('#studentCount').text(examSubject.student_count);
    $('#gradeCount').text(examSubject.grade_count);
    
    // Instructions
    if (examSubject.instructions) {
        $('#instructions').text(examSubject.instructions);
    } else {
        $('#instructions').text('No instructions provided.');
    }
    
    // Render grades table
    renderGradesTable(examSubject.grades, examSubject.total_marks);
    
    // Render attendance table
    renderAttendanceTable(examSubject.attendance);
    
    // Store exam subject data for edit modal
    window.currentExamSubject = examSubject;
}

function renderGradesTable(grades, totalMarks) {
    var tbody = $('#gradesTable tbody');
    tbody.empty();
    
    if (grades.length === 0) {
        tbody.append('<tr><td colspan="9" class="text-center text-muted">No grades recorded yet</td></tr>');
        return;
    }
    
    grades.forEach(function(grade) {
        var percentage = grade.marks_obtained && grade.total_marks ? 
            ((grade.marks_obtained / grade.total_marks) * 100).toFixed(2) + '%' : '-';
        
        var row = `
            <tr>
                <td>${grade.roll_no}</td>
                <td>${grade.student_full_name}</td>
                <td>${grade.marks_obtained || '-'}</td>
                <td>${grade.total_marks || totalMarks}</td>
                <td>${percentage}</td>
                <td>${grade.grade_letter || '-'}</td>
                <td>${grade.grade_point || '-'}</td>
                <td>${grade.remarks || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-primary edit-grade-btn" data-grade-id="${grade.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-grade-btn" data-grade-id="${grade.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Add event listeners
    $('.edit-grade-btn').on('click', function() {
        var gradeId = $(this).data('grade-id');
        editGrade(gradeId);
    });
    
    $('.delete-grade-btn').on('click', function() {
        var gradeId = $(this).data('grade-id');
        if (confirm('Are you sure you want to delete this grade?')) {
            deleteGrade(gradeId);
        }
    });
}

function renderAttendanceTable(attendance) {
    var tbody = $('#attendanceTable tbody');
    tbody.empty();
    
    if (attendance.length === 0) {
        tbody.append('<tr><td colspan="5" class="text-center text-muted">No attendance recorded yet</td></tr>');
        return;
    }
    
    attendance.forEach(function(record) {
        var row = `
            <tr>
                <td>${record.roll_no}</td>
                <td>${record.student_full_name}</td>
                <td>${getAttendanceStatusBadge(record.status)}</td>
                <td>${record.remarks || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-primary edit-attendance-btn" data-attendance-id="${record.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Add event listeners
    $('.edit-attendance-btn').on('click', function() {
        var attendanceId = $(this).data('attendance-id');
        editAttendance(attendanceId);
    });
}

function loadEditExamSubjectModal() {
    var examSubject = window.currentExamSubject;
    if (!examSubject) return;
    
    // Populate form fields
    $('#editExamSubjectId').val(examSubject.id);
    $('#editSubjectName').val(examSubject.subject_name);
    $('#editExamDate').val(examSubject.exam_date);
    $('#editStartTime').val(examSubject.start_time);
    $('#editEndTime').val(examSubject.end_time);
    $('#editTotalMarks').val(examSubject.total_marks);
    $('#editPassMark').val(examSubject.pass_mark);
    $('#editExamStatus').val(examSubject.exam_status);
    $('#editRoomNumber').val(examSubject.room_number);
    $('#editInstructions').val(examSubject.instructions);
    
    // Load teachers
    loadTeachersForEdit(examSubject.exam_id);
    
    $('#editExamSubjectModal').modal('show');
}

function loadTeachersForEdit(examId) {
    $.get('../api/get_available_subjects_teachers.php', {exam_id: examId}, function(response) {
        if (response.success) {
            var teacherSelect = $('#editTeacher');
            teacherSelect.empty();
            teacherSelect.append('<option value="">Select Teacher</option>');
            
            response.data.teachers.forEach(function(teacher) {
                teacherSelect.append(`<option value="${teacher.id}">${teacher.full_name}</option>`);
            });
            
            // Set current teacher
            if (window.currentExamSubject.teacher_id) {
                teacherSelect.val(window.currentExamSubject.teacher_id);
            }
        }
    });
}

function updateExamSubject() {
    var formData = $('#editExamSubjectForm').serialize();
    
    $.post('../api/update_exam_subject.php', formData, function(response) {
        if (response.success) {
            alert('Exam subject updated successfully!');
            $('#editExamSubjectModal').modal('hide');
            loadExamSubjectDetails();
        } else {
            alert('Failed to update exam subject: ' + response.message);
        }
    }).fail(function() {
        alert('Failed to update exam subject. Please try again.');
    });
}

function loadAddGradeModal() {
    var examSubject = window.currentExamSubject;
    if (!examSubject) return;
    
    // Populate form fields
    $('#addGradeExamSubjectId').val(examSubject.id);
    $('#addGradeTotalMarks').val(examSubject.total_marks);
    
    // Load students
    var studentSelect = $('#addGradeStudentId');
    studentSelect.empty();
    studentSelect.append('<option value="">Select Student</option>');
    
    examSubject.students.forEach(function(student) {
        studentSelect.append(`<option value="${student.id}">${student.full_name} (Roll: ${student.roll_no})</option>`);
    });
    
    $('#addGradeModal').modal('show');
}

function addGrade() {
    var formData = $('#addGradeForm').serialize();
    
    $.post('../api/add_grade.php', formData, function(response) {
        if (response.success) {
            alert('Grade added successfully!');
            $('#addGradeModal').modal('hide');
            loadExamSubjectDetails();
        } else {
            alert('Failed to add grade: ' + response.message);
        }
    }).fail(function() {
        alert('Failed to add grade. Please try again.');
    });
}

function takeAttendance() {
    // Implementation for taking attendance
    alert('Attendance functionality will be implemented here.');
}

function gradeStudents() {
    // Implementation for bulk grading
    alert('Bulk grading functionality will be implemented here.');
}

function generateSubjectReport() {
    // Implementation for generating subject report
    alert('Report generation functionality will be implemented here.');
}

function editGrade(gradeId) {
    // Implementation for editing grade
    alert('Edit grade functionality will be implemented here.');
}

function deleteGrade(gradeId) {
    // Implementation for deleting grade
    alert('Delete grade functionality will be implemented here.');
}

function editAttendance(attendanceId) {
    // Implementation for editing attendance
    alert('Edit attendance functionality will be implemented here.');
}

function getStatusBadge(status) {
    var badges = {
        'scheduled': '<span class="badge bg-warning">Scheduled</span>',
        'ongoing': '<span class="badge bg-info">Ongoing</span>',
        'completed': '<span class="badge bg-success">Completed</span>',
        'cancelled': '<span class="badge bg-danger">Cancelled</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function getAttendanceStatusBadge(status) {
    var badges = {
        'present': '<span class="badge bg-success">Present</span>',
        'absent': '<span class="badge bg-danger">Absent</span>',
        'late': '<span class="badge bg-warning">Late</span>',
        'excused': '<span class="badge bg-info">Excused</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString();
}

function init() {
    // Initialize any additional functionality
    console.log('Exam Subject Details page initialized');
} 