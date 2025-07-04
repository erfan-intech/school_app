$(document).ready(function() {
    var examId = $('#examDetailsApp').data('exam-id');
    
    // Load exam details on page load
    loadExamDetails();
    
    // Handle add subject button
    $('#addSubjectBtn').on('click', function() {
        loadAddSubjectModal();
    });
    

    
    // Handle add subject form submission
    $('#addSubjectForm').on('submit', function(e) {
        e.preventDefault();
        addSubjectToExam();
    });
    
    // Handle edit subject form submission
    $('#editSubjectForm').on('submit', function(e) {
        e.preventDefault();
        updateSubjectInExam();
    });
    

    
    // Handle department selection change for Add Subject modal
    $('#addSubjectDepartment').on('change', function() {
        updateSubjectsForDepartmentFromStructure('add');
    });
    
    // Handle department selection change for Edit Subject modal
    $('#editSubjectDepartment').on('change', function() {
        updateSubjectsForDepartmentFromStructure('edit');
    });
    
    // Handle subject selection change for Add Subject modal
    $('#addSubjectSelect').on('change', function() {
        updateTeachersForSubjectFromStructure('add');
    });
    
    // Handle subject selection change for Edit Subject modal
    $('#editSubjectSelect').on('change', function() {
        updateTeachersForSubjectFromStructure('edit');
    });
    
    // Handle quick action buttons
    $('#generateReportBtn').on('click', function() {
        showExamReport();
    });
    
    $('#publishExamBtn').on('click', function() {
        publishExam();
    });
    
    // Initialize the page
    init();
});

function loadExamDetails() {
    var examId = $('#examDetailsApp').data('exam-id');
    
    $.get('../api/get_exam_details.php', {exam_id: examId}, function(response) {
        if (response.success) {
            var exam = response.data;
            renderExamDetails(exam);
            
            // Load class structure for table rendering
            $.get('../api/get_class_structure.php', {class_id: exam.class_id}, function(structureResponse) {
                if (structureResponse.success) {
                    var classStructure = structureResponse.data;
                    window.currentClassStructure = classStructure;
                    window.currentExamParams = exam;
                    
                    // Re-render the subjects table with the class structure
                    loadExamSubjects();
                } else {
                    console.error('Failed to load class structure:', structureResponse.message);
                    // Still load subjects even if class structure fails
                    loadExamSubjects();
                }
            }).fail(function() {
                console.error('Failed to load class structure');
                // Still load subjects even if class structure fails
                loadExamSubjects();
            });
        } else {
            alert('Failed to load exam details: ' + response.message);
        }
    }).fail(function() {
        alert('Failed to load exam details. Please try again.');
    });
}

function renderExamDetails(exam) {
    // Update page header
    $('#examNameHeader').text(exam.exam_name);
    
    // Update exam information
    $('#examName').text(exam.exam_name);
    $('#examType').text(exam.exam_type_name || '-');
    $('#examClass').text(exam.class_name);
    $('#examDepartment').text(exam.department_name || '-');
    $('#examSection').text(exam.section_name || '-');
    $('#examYear').text(exam.academic_year);
    $('#examCreatedBy').text(exam.created_by);
    $('#examCreatedDate').text(formatDate(exam.creation_date));
    $('#subjectCount').text(exam.subject_count || 0);
    
    // Description
    if (exam.description) {
        $('#examDescription').text(exam.description);
    } else {
        $('#examDescription').text('No description provided.');
    }
    
    // Store exam data for edit modal
    window.currentExam = exam;
}

function loadExamSubjects() {
    var examId = $('#examDetailsApp').data('exam-id');
    
    $.get('../api/get_exam_subjects.php', {exam_id: examId}, function(response) {
        if (response.success) {
            renderExamSubjectsTable(response.data);
        } else {
            // Check if the error is due to no subjects being assigned
            if (response.message && response.message.includes('no subjects') || response.message.includes('No subjects')) {
                renderExamSubjectsTable([]); // Render empty state
            } else {
                alert('Failed to load exam subjects: ' + response.message);
            }
        }
    }).fail(function(xhr, status, error) {
        console.error('Failed to load exam subjects:', xhr, status, error);
        // Show a more user-friendly message for network errors
        alert('No subjects assigned to this exam yet. Please assign subjects to continue.');
    });
}

function renderExamSubjectsTable(subjects) {
    var tbody = $('#examSubjectsTable tbody');
    tbody.empty();
    
    // Check if class has departments
    var hasDepartments = window.currentClassStructure && window.currentClassStructure.has_departments;
    // Show/hide Department column in thead
    if (hasDepartments) {
        $('#examSubjectsDeptCol').show();
    } else {
        $('#examSubjectsDeptCol').hide();
    }
    
    if (subjects.length === 0) {
        var colspan = hasDepartments ? 11 : 10;
        tbody.append('<tr><td colspan="' + colspan + '" class="text-center text-muted">No subjects assigned to this exam yet. Please assign subjects to continue.</td></tr>');
        return;
    }
    
    subjects.forEach(function(subject, idx) {
        var timeStr = '-';
        if (subject.start_time && subject.end_time) {
            timeStr = subject.start_time + ' - ' + subject.end_time;
        }
        
        // Get department name
        var departmentName = subject.department_name || 'All Departments';
        
        var row = '<tr>';
        row += '<td>' + (idx + 1) + '</td>';
        if (hasDepartments) {
            row += '<td>' + departmentName + '</td>';
        }
        row += '<td>' + subject.subject_name + '</td>';
        row += '<td>' + formatDate(subject.exam_date) + '</td>';
        row += '<td>' + timeStr + '</td>';
        row += '<td>' + (subject.teacher_full_name || '-') + '</td>';
        row += '<td>' + subject.total_marks + '</td>';
        row += '<td>' + subject.pass_mark + '</td>';
        row += '<td>' + getStatusBadge(subject.exam_status) + '</td>';
        row += '<td>' + (subject.room_number || '-') + '</td>';
        row += '<td>' +
            '<a href="exam_subject_details.php?exam_subject_id=' + subject.id + '" class="btn btn-sm btn-info me-1">' +
            '<i class="fas fa-eye"></i> View</a>' +
            '<button class="btn btn-sm btn-primary edit-subject-btn me-1" data-subject-id="' + subject.id + '">' +
            '<i class="fas fa-edit"></i></button>' +
            '<button class="btn btn-sm btn-danger remove-subject-btn" data-subject-id="' + subject.id + '">' +
            '<i class="fas fa-trash"></i></button>' +
            '</td>';
        row += '</tr>';
        tbody.append(row);
    });
    
    // Add remove subject event listeners
    $('.remove-subject-btn').on('click', function() {
        var subjectId = $(this).data('subject-id');
        if (confirm('Are you sure you want to remove this subject from the exam?')) {
            removeSubjectFromExam(subjectId);
        }
    });
    
    // Add edit subject event listeners
    $('.edit-subject-btn').on('click', function() {
        var subjectId = $(this).data('subject-id');
        loadEditSubjectModal(subjectId);
    });
}

function loadAddSubjectModal() {
    var examId = $('#examDetailsApp').data('exam-id');
    

    
    // Set exam ID in form
    $('#addSubjectExamId').val(examId);
    
    // Get exam details and class structure
    $.get('../api/get_exam_details.php', {exam_id: examId}, function(examResponse) {
        if (examResponse.success) {
            var exam = examResponse.data;
            
            // Get class structure
            $.get('../api/get_class_structure.php', {class_id: exam.class_id}, function(structureResponse) {
                if (structureResponse.success) {
                    var classStructure = structureResponse.data;
                    
                    // Store class structure globally for this modal session
                    window.currentClassStructure = classStructure;
                    window.currentExamParams = exam;
                    
                    // Configure modal based on class structure and exam parameters
                    configureAddSubjectModal(classStructure, exam);
                    
                    $('#addSubjectModal').modal('show');
                } else {
                    alert('Failed to load class structure: ' + structureResponse.message);
                }
            });
        } else {
            alert('Failed to load exam details: ' + examResponse.message);
        }
    });
}

function configureAddSubjectModal(classStructure, examParams) {
    
    // Reset all dropdowns
    var deptSelect = $('#addSubjectDepartment');
    var subjectSelect = $('#addSubjectSelect');
    var teacherSelect = $('#addSubjectTeacher');
    
    deptSelect.empty();
    subjectSelect.empty();
    teacherSelect.empty();
    
    // Check if class has departments
    if (classStructure.has_departments) {
        // Show department dropdown
        $('#addSubjectDepartmentRow').show();
        
        // Set required attribute for department field
        deptSelect.prop('required', true);
        
        // Check exam parameters for department selection
        if (examParams.department_id) {
            // Exam has specific department - preselect it and make it required
            deptSelect.append('<option value="">Select Department</option>');
            classStructure.departments.forEach(function(dept) {
                var selected = (dept.id == examParams.department_id) ? 'selected' : '';
                deptSelect.append(`<option value="${dept.id}" ${selected}>${dept.name}</option>`);
            });
            
            // Load subjects for the specific department
            loadSubjectsForDepartmentFromStructure('add', examParams.department_id);
        } else {
            // Exam has "All Department" - show all departments and require selection
            deptSelect.append('<option value="">Select Department</option>');
            classStructure.departments.forEach(function(dept) {
                deptSelect.append(`<option value="${dept.id}">${dept.name}</option>`);
            });
            
            // Disable subject and teacher dropdowns initially
            subjectSelect.append('<option value="">Select Department First</option>');
            subjectSelect.prop('disabled', true);
        }
        
        // Disable teacher dropdown initially
        teacherSelect.append('<option value="">Select Subject First</option>');
        teacherSelect.prop('disabled', true);
    } else {
        // Hide department dropdown - class has no departments
        $('#addSubjectDepartmentRow').hide();
        
        // Remove required attribute from hidden department field
        deptSelect.prop('required', false);
        
        // Load all subjects for the class
        loadSubjectsForDepartmentFromStructure('add', null);
        
        // Disable teacher dropdown initially
        teacherSelect.append('<option value="">Select Subject First</option>');
        teacherSelect.prop('disabled', true);
    }
}

function configureEditSubjectModal(classStructure, examParams, currentSubject) {
    
    // Reset all dropdowns
    var deptSelect = $('#editSubjectDepartment');
    var subjectSelect = $('#editSubjectSelect');
    var teacherSelect = $('#editSubjectTeacher');
    
    deptSelect.empty();
    subjectSelect.empty();
    teacherSelect.empty();
    
    // Check if class has departments
    if (classStructure.has_departments) {
        // Show department dropdown
        $('#editSubjectDepartmentRow').show();
        
        // Set required attribute for department field
        deptSelect.prop('required', true);
        
        // Check exam parameters for department selection
        if (examParams.department_id) {
            // Exam has specific department - preselect it and make it required
            deptSelect.append('<option value="">Select Department</option>');
            classStructure.departments.forEach(function(dept) {
                var selected = (dept.id == examParams.department_id) ? 'selected' : '';
                deptSelect.append(`<option value="${dept.id}" ${selected}>${dept.name}</option>`);
            });
            
            // Load subjects for the specific department
            loadSubjectsForDepartmentFromStructure('edit', examParams.department_id, currentSubject);
        } else {
            // Exam has "All Department" - show all departments and require selection
            deptSelect.append('<option value="">Select Department</option>');
            classStructure.departments.forEach(function(dept) {
                var selected = (currentSubject && currentSubject.department_id == dept.id) ? 'selected' : '';
                deptSelect.append(`<option value="${dept.id}" ${selected}>${dept.name}</option>`);
            });
            
            // Load subjects for the current department or all departments
            var currentDeptId = currentSubject ? currentSubject.department_id : null;
            loadSubjectsForDepartmentFromStructure('edit', currentDeptId, currentSubject);
        }
    } else {
        // Hide department dropdown - class has no departments
        $('#editSubjectDepartmentRow').hide();
        
        // Remove required attribute from hidden department field
        deptSelect.prop('required', false);
        
        // Load all subjects for the class
        loadSubjectsForDepartmentFromStructure('edit', null, currentSubject);
    }
}

function loadSubjectsForDepartmentFromStructure(modalType, departmentId, currentSubject) {
    var classStructure = window.currentClassStructure;
    var subjectSelect = modalType === 'add' ? $('#addSubjectSelect') : $('#editSubjectSelect');
    
    subjectSelect.empty();
    subjectSelect.append('<option value="">Select Subject</option>');
    subjectSelect.prop('disabled', false);
    
    var subjects = [];
    
    if (departmentId) {
        // Load subjects for specific department
        if (classStructure.subjects_by_department[departmentId]) {
            subjects = classStructure.subjects_by_department[departmentId];
        }
    } else {
        // Load all subjects (global + all departments)
        Object.keys(classStructure.subjects_by_department).forEach(function(deptId) {
            subjects = subjects.concat(classStructure.subjects_by_department[deptId]);
        });
    }
    
    if (subjects.length > 0) {
        subjects.forEach(function(subject) {
            var selected = (currentSubject && currentSubject.subject_id == subject.id) ? 'selected' : '';
            subjectSelect.append(`<option value="${subject.id}" ${selected}>${subject.name}</option>`);
        });
        
        // If editing and we have a current subject, load its teachers
        if (modalType === 'edit' && currentSubject && currentSubject.subject_id) {
            setTimeout(function() {
                loadTeachersForSubjectFromStructure('edit', currentSubject.subject_id, currentSubject.teacher_id, currentSubject.teacher_full_name);
            }, 100);
        }
    } else {
        var message = departmentId ? 'No subjects available for this department' : 'No subjects available';
        subjectSelect.append(`<option value="" disabled>${message}</option>`);
    }
    
    // Clear teacher dropdown
    var teacherSelect = modalType === 'add' ? $('#addSubjectTeacher') : $('#editSubjectTeacher');
    teacherSelect.empty();
    teacherSelect.append('<option value="">Select Subject First</option>');
    teacherSelect.prop('disabled', true);
}

function updateSubjectsForDepartmentFromStructure(modalType) {
    var departmentId = modalType === 'add' ? $('#addSubjectDepartment').val() : $('#editSubjectDepartment').val();
    var currentSubject = modalType === 'edit' ? window.currentEditSubject : null;
    
    loadSubjectsForDepartmentFromStructure(modalType, departmentId, currentSubject);
}

function loadTeachersForSubjectFromStructure(modalType, subjectId, currentTeacherId, currentTeacherName) {
    var classStructure = window.currentClassStructure;
    var departmentId = modalType === 'add' ? $('#addSubjectDepartment').val() : $('#editSubjectDepartment').val();
    var teacherSelect = modalType === 'add' ? $('#addSubjectTeacher') : $('#editSubjectTeacher');
    
    teacherSelect.empty();
    teacherSelect.append('<option value="">Select Teacher</option>');
    teacherSelect.prop('disabled', false);
    
    var teachers = [];
    
    // Get teachers for this subject
    if (departmentId) {
        // Get teachers for specific department
        var key = subjectId + '_' + departmentId;
        if (classStructure.teachers_by_subject[key]) {
            teachers = classStructure.teachers_by_subject[key];
        }
        
        // Also get teachers from global assignments for this subject
        var globalKey = subjectId + '_global';
        if (classStructure.teachers_by_subject[globalKey]) {
            teachers = teachers.concat(classStructure.teachers_by_subject[globalKey]);
        }
    } else {
        // Get all teachers for this subject (from all departments + global)
        Object.keys(classStructure.teachers_by_subject).forEach(function(key) {
            if (key.startsWith(subjectId + '_')) {
                teachers = teachers.concat(classStructure.teachers_by_subject[key]);
            }
        });
    }
    
    // Remove duplicates based on teacher ID
    var uniqueTeachers = [];
    var teacherIds = [];
    teachers.forEach(function(teacher) {
        if (!teacherIds.includes(teacher.id)) {
            uniqueTeachers.push(teacher);
            teacherIds.push(teacher.id);
        }
    });
    
    if (uniqueTeachers.length > 0) {
        uniqueTeachers.forEach(function(teacher) {
            var selected = (currentTeacherId && currentTeacherId == teacher.id) ? 'selected' : '';
            teacherSelect.append(`<option value="${teacher.id}" ${selected}>${teacher.full_name}</option>`);
        });
    } else {
        teacherSelect.append('<option value="" disabled>No teachers available for this subject</option>');
        teacherSelect.prop('disabled', true);
    }
}

function updateTeachersForSubjectFromStructure(modalType) {
    var subjectId = modalType === 'add' ? $('#addSubjectSelect').val() : $('#editSubjectSelect').val();
    var currentTeacherId = modalType === 'edit' && window.currentEditSubject ? window.currentEditSubject.teacher_id : null;
    var currentTeacherName = modalType === 'edit' && window.currentEditSubject ? window.currentEditSubject.teacher_full_name : null;
    
    if (subjectId) {
        loadTeachersForSubjectFromStructure(modalType, subjectId, currentTeacherId, currentTeacherName);
    } else {
        var teacherSelect = modalType === 'add' ? $('#addSubjectTeacher') : $('#editSubjectTeacher');
        teacherSelect.empty();
        teacherSelect.append('<option value="">Select Subject First</option>');
        teacherSelect.prop('disabled', true);
    }
}

function loadDepartmentsForClass(classId, modalType) {
    $.get('../api/get_departments_by_class.php', {class_id: classId}, function(response) {
        if (response.success) {
            var deptSelect = modalType === 'add' ? $('#addSubjectDepartment') : $('#editSubjectDepartment');
            deptSelect.empty();
            deptSelect.append('<option value="">All Departments</option>');
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(dept) {
                    deptSelect.append(`<option value="${dept.id}">${dept.name}</option>`);
                });
            }
        } else {
            console.error('Failed to load departments:', response.message);
        }
    });
}

function loadSubjectsForClass(classId, modalType) {
    $.get('../api/get_subjects_by_department.php', {class_id: classId}, function(response) {
        if (response.success) {
            var subjectSelect = modalType === 'add' ? $('#addSubjectSelect') : $('#editSubjectSelect');
            subjectSelect.empty();
            subjectSelect.append('<option value="">Select Subject</option>');
            subjectSelect.prop('disabled', false);
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(subject) {
                subjectSelect.append(`<option value="${subject.id}">${subject.name}</option>`);
            });
            } else {
                subjectSelect.append('<option value="" disabled>No subjects available</option>');
            }
        } else {
            console.error('Failed to load subjects:', response.message);
        }
    });
}

function updateSubjectsForDepartment(modalType) {
    var departmentId = modalType === 'add' ? $('#addSubjectDepartment').val() : $('#editSubjectDepartment').val();
    var examId = $('#examDetailsApp').data('exam-id');
    
    // Get exam details to get class_id
    $.get('../api/get_exam_details.php', {exam_id: examId}, function(examResponse) {
        if (examResponse.success) {
            var exam = examResponse.data;
            
            $.get('../api/get_subjects_by_department.php', {
                class_id: exam.class_id, 
                department_id: departmentId
            }, function(response) {
                if (response.success) {
                    var subjectSelect = modalType === 'add' ? $('#addSubjectSelect') : $('#editSubjectSelect');
                    subjectSelect.empty();
                    subjectSelect.append('<option value="">Select Subject</option>');
                    subjectSelect.prop('disabled', false);
                    
                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function(subject) {
                            subjectSelect.append(`<option value="${subject.id}">${subject.name}</option>`);
                        });
                    } else {
                        subjectSelect.append('<option value="" disabled>No subjects available for this department</option>');
                    }
                    
                    // Clear teacher dropdown
                    var teacherSelect = modalType === 'add' ? $('#addSubjectTeacher') : $('#editSubjectTeacher');
                    teacherSelect.empty();
                    teacherSelect.append('<option value="">Select Subject First</option>');
                    teacherSelect.prop('disabled', true);
                    
                    // For edit modal, if we have a current subject, select it
                    if (modalType === 'edit' && window.currentEditSubject) {
                        setTimeout(function() {
                            if (window.currentEditSubject.subject_id) {
                                subjectSelect.val(window.currentEditSubject.subject_id);
                                // Load teachers for the current subject
                                loadEditTeachersForCurrentSubject(
                                    window.currentEditSubject.subject_id, 
                                    window.currentEditSubject.teacher_id, 
                                    window.currentEditSubject.teacher_full_name
                                );
                            }
                        }, 100);
                    }
                } else {
                    console.error('Failed to load subjects for department:', response.message);
                }
            });
        }
    });
}

function updateTeachersForSubject(modalType) {
    var subjectId = modalType === 'add' ? $('#addSubjectSelect').val() : $('#editSubjectSelect').val();
    var departmentId = modalType === 'add' ? $('#addSubjectDepartment').val() : $('#editSubjectDepartment').val();
    var examId = $('#examDetailsApp').data('exam-id');
    

    
    if (!subjectId) {
        var teacherSelect = modalType === 'add' ? $('#addSubjectTeacher') : $('#editSubjectTeacher');
        teacherSelect.empty();
        teacherSelect.append('<option value="">Select a subject first</option>');
        teacherSelect.prop('disabled', true);
        return;
    }
    
    // Get teachers for the specific subject and department
    $.get('../api/get_teachers_for_subject.php', {
        exam_id: examId, 
        subject_id: subjectId, 
        department_id: departmentId
    }, function(response) {

        
        if (response.success) {
            var teacherSelect = modalType === 'add' ? $('#addSubjectTeacher') : $('#editSubjectTeacher');
            teacherSelect.empty();
            teacherSelect.append('<option value="">Select Teacher</option>');
            teacherSelect.prop('disabled', false);
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(teacher) {
                teacherSelect.append(`<option value="${teacher.id}">${teacher.full_name}</option>`);
            });
            } else {
                teacherSelect.append('<option value="" disabled>No teachers available for this subject</option>');
                teacherSelect.prop('disabled', true);
            }
        } else {
            console.error('Failed to load teachers for subject:', response.message);
            var teacherSelect = modalType === 'add' ? $('#addSubjectTeacher') : $('#editSubjectTeacher');
            teacherSelect.empty();
            teacherSelect.append('<option value="" disabled>Failed to load teachers</option>');
            teacherSelect.prop('disabled', true);
        }
    }).fail(function(xhr, status, error) {
        console.error('AJAX request failed for teachers:', xhr, status, error);
        var teacherSelect = modalType === 'add' ? $('#addSubjectTeacher') : $('#editSubjectTeacher');
        teacherSelect.empty();
        teacherSelect.append('<option value="" disabled>Failed to load teachers</option>');
        teacherSelect.prop('disabled', true);
    });
}



function updateEditTeachersForSubject() {
    var subjectId = $('#editSubjectSelect').val();
    var examId = $('#examDetailsApp').data('exam-id');
    
    if (!subjectId) {
        var teacherSelect = $('#editSubjectTeacher');
        teacherSelect.empty();
        teacherSelect.append('<option value="">Select a subject first</option>');
        teacherSelect.prop('disabled', true);
        return;
    }
    
    // Get teachers for the specific subject
    $.get('../api/get_teachers_for_subject.php', {exam_id: examId, subject_id: subjectId}, function(response) {

        
        if (response.success) {
            var teacherSelect = $('#editSubjectTeacher');
            teacherSelect.empty();
            teacherSelect.append('<option value="">Select Teacher</option>');
            teacherSelect.prop('disabled', false);
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(teacher) {
                    teacherSelect.append(`<option value="${teacher.id}">${teacher.full_name}</option>`);
                });
            } else {
                teacherSelect.append('<option value="" disabled>No teachers available for this subject</option>');
                teacherSelect.prop('disabled', true);
            }
        } else {
            console.error('Failed to load teachers for subject:', response.message);
            var teacherSelect = $('#editSubjectTeacher');
            teacherSelect.empty();
            teacherSelect.append('<option value="" disabled>Failed to load teachers</option>');
            teacherSelect.prop('disabled', true);
        }
    }).fail(function(xhr, status, error) {
        console.error('AJAX request failed for edit teachers:', xhr, status, error);
        var teacherSelect = $('#editSubjectTeacher');
        teacherSelect.empty();
        teacherSelect.append('<option value="" disabled>Failed to load teachers</option>');
        teacherSelect.prop('disabled', true);
    });
}

function loadEditTeachersForCurrentSubject(subjectId, currentTeacherId, currentTeacherName) {
    var examId = $('#examDetailsApp').data('exam-id');
    var departmentId = $('#editSubjectDepartment').val();
    
    if (!subjectId) {
        var teacherSelect = $('#editSubjectTeacher');
        teacherSelect.empty();
        teacherSelect.append('<option value="">Select a subject first</option>');
        teacherSelect.prop('disabled', true);
        return;
    }
    
    // Get teachers for the specific subject and department
    $.get('../api/get_teachers_for_subject.php', {
        exam_id: examId, 
        subject_id: subjectId, 
        department_id: departmentId
    }, function(response) {

        
        if (response.success) {
            var teacherSelect = $('#editSubjectTeacher');
            teacherSelect.empty();
            teacherSelect.append('<option value="">Select Teacher</option>');
            teacherSelect.prop('disabled', false);
            
            // Add the current teacher first (even if not in available teachers)
            if (currentTeacherId && currentTeacherName) {
                teacherSelect.append(`<option value="${currentTeacherId}" selected>${currentTeacherName}</option>`);
            }
            
            // Add other available teachers (excluding the current one)
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(teacher) {
                    if (teacher.id != currentTeacherId) {
                teacherSelect.append(`<option value="${teacher.id}">${teacher.full_name}</option>`);
                    }
                });
            }
            
            // If no teachers available and no current teacher
            if (teacherSelect.find('option').length === 1) {
                teacherSelect.append('<option value="" disabled>No teachers available for this subject</option>');
                teacherSelect.prop('disabled', true);
            }
        } else {
            console.error('Failed to load teachers for current subject:', response.message);
            var teacherSelect = $('#editSubjectTeacher');
            teacherSelect.empty();
            teacherSelect.append('<option value="" disabled>Failed to load teachers</option>');
            teacherSelect.prop('disabled', true);
        }
    }).fail(function(xhr, status, error) {
        console.error('AJAX request failed for edit current teachers:', xhr, status, error);
        var teacherSelect = $('#editSubjectTeacher');
        teacherSelect.empty();
        teacherSelect.append('<option value="" disabled>Failed to load teachers</option>');
        teacherSelect.prop('disabled', true);
    });
}

function addSubjectToExam() {
    // Check if department dropdown is hidden (class has no departments)
    var deptRow = $('#addSubjectDepartmentRow');
    if (deptRow.is(':hidden')) {
        // Remove department_id from form data since it's not applicable
        var form = $('#addSubjectForm')[0];
        var formData = new FormData(form);
        formData.delete('department_id'); // Remove department_id when not applicable
        
        // Convert FormData to URLSearchParams for logging
        var params = new URLSearchParams();
        for (var pair of formData.entries()) {
            params.append(pair[0], pair[1]);
        }
        var formDataString = params.toString();
    } else {
    var formData = $('#addSubjectForm').serialize();
        var formDataString = formData;
    }
    
    // Debug: Log the form data being sent
    
    
    $.post('../api/add_exam_subject.php', formDataString, function(response) {
        if (response.success) {
            $('#addSubjectModal').modal('hide');
            loadExamSubjects();
            alert('Subject added to exam successfully!');
        } else {
            // Debug: If validation fails, call debug API
            if (response.message && response.message.includes('Subject is not assigned')) {
                var formDataObj = new URLSearchParams(formData);
                var examId = formDataObj.get('exam_id');
                var subjectId = formDataObj.get('subject_id');
                
                console.log('Validation failed, calling debug API...');
                $.get('../api/debug_exam_subject.php', {
                    exam_id: examId,
                    subject_id: subjectId
                }, function(debugResponse) {
                });
            }
            
            alert('Failed to add subject: ' + response.message);
        }
    }).fail(function(xhr, status, error) {
        console.error('API call failed:', xhr, status, error);
        alert('Failed to add subject. Please try again.');
    });
}

function removeSubjectFromExam(subjectId) {
    $.post('../api/remove_exam_subject.php', {exam_subject_id: subjectId}, function(response) {
        if (response.success) {
            loadExamSubjects();
            alert('Subject removed from exam successfully!');
        } else {
            alert('Failed to remove subject: ' + response.message);
        }
    }).fail(function() {
        alert('Failed to remove subject. Please try again.');
    });
}

function loadEditSubjectModal(subjectId) {
    var examId = $('#examDetailsApp').data('exam-id');
    

    
    // Set exam subject ID and exam ID in form
    $('#editSubjectId').val(subjectId);
    $('#editSubjectExamId').val(examId);
    
    // Load subject details
    $.get('../api/get_exam_subject_details.php', {exam_subject_id: subjectId}, function(response) {

        
        if (response.success) {
            var subject = response.data;
            // Store current subject data for edit modal
            window.currentEditSubject = subject;
            
            // Get exam details and class structure
            $.get('../api/get_exam_details.php', {exam_id: examId}, function(examResponse) {
                if (examResponse.success) {
                    var exam = examResponse.data;
                    
                    // Get class structure
                    $.get('../api/get_class_structure.php', {class_id: exam.class_id}, function(structureResponse) {
                        if (structureResponse.success) {
                            var classStructure = structureResponse.data;
                            
                            // Store class structure globally for this modal session
                            window.currentClassStructure = classStructure;
                            window.currentExamParams = exam;
                            
                            // Configure modal based on class structure and exam parameters
                            configureEditSubjectModal(classStructure, exam, subject);
                            
                            // Set form values
                            $('#editSubjectDate').val(subject.exam_date);
                            $('#editSubjectStartTime').val(subject.start_time || '');
                            $('#editSubjectEndTime').val(subject.end_time || '');
                            
                            // Set time values
                            
                            $('#editSubjectTotalMarks').val(subject.total_marks);
                            $('#editSubjectPassMark').val(subject.pass_mark);
                            $('#editSubjectRoom').val(subject.room_number || '');
                            $('#editSubjectInstructions').val(subject.instructions || '');
                            
                            $('#editSubjectModal').modal('show');
                        } else {
                            alert('Failed to load class structure: ' + structureResponse.message);
                        }
                    });
                } else {
                    alert('Failed to load exam details: ' + examResponse.message);
                }
            });
        } else {
            alert('Failed to load subject details: ' + response.message);
        }
    }).fail(function() {
        alert('Failed to load subject details. Please try again.');
    });
}

function updateSubjectInExam() {
    // Check if department dropdown is hidden (class has no departments)
    var deptRow = $('#editSubjectDepartmentRow');
    var formData;
    
    if (deptRow.is(':hidden')) {
        // Remove department_id from form data since it's not applicable
        var form = $('#editSubjectForm')[0];
        var formDataObj = new FormData(form);
        formDataObj.delete('department_id'); // Remove department_id when not applicable
        
        // Convert FormData to object for $.post
        var data = {};
        for (var pair of formDataObj.entries()) {
            data[pair[0]] = pair[1];
        }
        formData = data;
    } else {
        // Use serializeArray and convert to object
        var serializedArray = $('#editSubjectForm').serializeArray();
        var data = {};
        serializedArray.forEach(function(item) {
            data[item.name] = item.value;
        });
        formData = data;
    }
    

    
    $.post('../api/update_exam_subject.php', formData, function(response) {

        if (response.success) {
            $('#editSubjectModal').modal('hide');
            loadExamSubjects();
            alert('Subject updated successfully!');
        } else {
            alert('Failed to update subject: ' + response.message);
        }
    }).fail(function(xhr, status, error) {
        console.error('AJAX failed:', xhr, status, error);
        alert('Failed to update subject. Please try again.');
    });
}



function publishExam() {
    var examId = $('#examDetailsApp').data('exam-id');
    
    if (confirm('Are you sure you want to publish this exam? This will make it visible to students and teachers.')) {
        $.post('../api/publish_exam.php', {exam_id: examId}, function(response) {
            if (response.success) {
                loadExamDetails();
                alert('Exam published successfully!');
            } else {
                alert('Failed to publish exam: ' + response.message);
            }
        }).fail(function() {
            alert('Failed to publish exam. Please try again.');
        });
    }
}

function showExamReport() {
    var examId = $('#examDetailsApp').data('exam-id');
    
    $.get('../api/get_exam_report.php', {exam_id: examId}, function(response) {
        if (response.success) {
            displayExamReport(response.data);
        } else {
            alert('Failed to generate report: ' + response.message);
        }
    }).fail(function() {
        alert('Failed to generate report. Please try again.');
    });
}

function displayExamReport(data) {
    var reportHtml = `
        <div class="modal fade" id="examReportModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Exam Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6>Exam: ${data.exam_name}</h6>
                                <p><strong>Class:</strong> ${data.class_name}</p>
                                <p><strong>Total Subjects:</strong> ${data.subject_count}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Summary</h6>
                                <p><strong>Status:</strong> ${data.exam_status}</p>
                                <p><strong>Created:</strong> ${formatDate(data.creation_date)}</p>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button class="btn btn-primary" id="downloadReportBtn">
                                <i class="fas fa-download"></i> Download Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    $('#examReportModal').remove();
    
    // Add new modal to body
    $('body').append(reportHtml);
    
    // Show modal
    $('#examReportModal').modal('show');
}

function getStatusBadge(status) {
    var badges = {
        'draft': '<span class="badge bg-secondary">Draft</span>',
        'published': '<span class="badge bg-success">Published</span>',
        'completed': '<span class="badge bg-info">Completed</span>',
        'cancelled': '<span class="badge bg-danger">Cancelled</span>',
        'scheduled': '<span class="badge bg-primary">Scheduled</span>',
        'ongoing': '<span class="badge bg-warning">Ongoing</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString();
}

function init() {
    // Any additional initialization code

}

 