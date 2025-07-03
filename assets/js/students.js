$(document).ready(function() {
    function loadStudents() {
        $.get('../api/get_students.php', function(res) {
            if (res.success) {
                let rows = '';
                res.data.forEach(function(s) {
                    let pic = s.profile_picture ? `<img src='../uploads/students/${s.profile_picture}' alt='Profile' width='40' height='40' style='object-fit:cover;border-radius:50%;'>` : '';
                    rows += `<tr data-student-id='${s.id}'>
                        <td>${s.id}</td>
                        <td>${(s.first_name || s.last_name) ? `<a href='/school_app/pages/student_profile.php?id=${s.id}' class='student-profile-link'>${s.first_name} ${s.last_name || ''}</a>` : ''}</td>
                        <td>${pic}</td>
                        <td>${s.class_name ? `<a href='/school_app/pages/class_details.php?class_id=${s.current_class_id}' class='class-link'>${s.class_name}</a>` : ''}</td>
                        <td>${s.department_name || ''}</td>
                        <td>${s.section_name || ''}</td>
                        <td>${s.roll_no || ''}</td>
                        <td>${(s.local_guardian_first_name ? s.local_guardian_first_name + ' ' + (s.local_guardian_last_name || '') : '') + (s.local_guardian_phone ? ' (' + s.local_guardian_phone + ')' : '')}</td>
                        <td><button class="btn btn-sm btn-info toggle-details-btn" data-id="${s.id}">Details</button></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-warning editBtn" data-id="${s.id}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="${s.id}">Delete</button>
                            <button class="btn btn-sm btn-success promoteBtn" data-id="${s.id}">Promote</button>
                        </td>
                    </tr>`;
                });
                $('#studentsTable tbody').html(rows);
            }
        });
    }

    loadStudents();

    // Populate department and section dropdowns based on selected class
    function updateDeptAndSectionDropdowns(classId) {
        if (!classId) {
            $('#current_department_id').html('<option value="">No Department</option>').prop('disabled', true);
            $('#current_section_id').html('<option value="">No Section</option>').prop('disabled', true);
            return;
        }
        $.get('../api/get_class_details.php', {class_id: classId}, function(res) {
            if (res.success) {
                // Check for real departments (id != 0) that are assigned to this class
                var realDepartments = res.departments.filter(d => d.id != 0);
                
                // Departments - only show departments assigned to this class
                if (realDepartments.length > 0) {
                    let deptOptions = '<option value="">Select Department</option>';
                    realDepartments.forEach(function(d) {
                        deptOptions += `<option value="${d.id}">${d.name}</option>`;
                    });
                    $('#current_department_id').html(deptOptions).prop('disabled', false);
                } else {
                    $('#current_department_id').html('<option value="">No Department</option>').prop('disabled', true);
                }
                
                // Sections - only show sections assigned to this class
                if (res.sections && res.sections.length > 0) {
                    let sectionOptions = '<option value="">Select Section</option>';
                    res.sections.forEach(function(s) {
                        sectionOptions += `<option value="${s.id}">${s.name}</option>`;
                    });
                    $('#current_section_id').html(sectionOptions);
                    
                    // Disable section selection if class has departments but no department is selected
                    if (realDepartments.length > 0) {
                        $('#current_section_id').prop('disabled', true);
                        $('#current_section_id').attr('title', 'Please select a department first');
                    } else {
                        $('#current_section_id').prop('disabled', false);
                        $('#current_section_id').removeAttr('title');
                    }
                } else {
                    $('#current_section_id').html('<option value="">No Section</option>').prop('disabled', true);
                }
            } else {
                $('#current_department_id').html('<option value="">No Department</option>').prop('disabled', true);
                $('#current_section_id').html('<option value="">No Section</option>').prop('disabled', true);
            }
        }, 'json');
    }

    // On class change in add/edit modal
    $('#current_class_id').on('change', function() {
        updateDeptAndSectionDropdowns($(this).val());
    });

    // On department change in add/edit modal
    $('#current_department_id').on('change', function() {
        var selectedDept = $(this).val();
        var classId = $('#current_class_id').val();
        
        if (classId) {
            $.get('../api/get_class_details.php', {class_id: classId}, function(res) {
                if (res.success) {
                    var realDepartments = res.departments.filter(d => d.id != 0);
                    
                    // Filter sections based on selected department
                    if (selectedDept) {
                        var filteredSections = res.sections.filter(s => String(s.department_id) === String(selectedDept));
                        if (filteredSections.length > 0) {
                            let sectionOptions = '<option value="">Select Section</option>';
                            filteredSections.forEach(function(s) {
                                sectionOptions += `<option value="${s.id}">${s.name}</option>`;
                            });
                            $('#current_section_id').html(sectionOptions).prop('disabled', false);
                            $('#current_section_id').removeAttr('title');
                        } else {
                            $('#current_section_id').html('<option value="">No Sections Available</option>').prop('disabled', true);
                            $('#current_section_id').attr('title', 'No sections assigned to this department');
                        }
                    } else {
                        // If no department selected, show all sections for this class (if no departments exist)
                        if (realDepartments.length > 0) {
                            $('#current_section_id').html('<option value="">Select Section</option>').prop('disabled', true);
                            $('#current_section_id').attr('title', 'Please select a department first');
                        } else {
                            // Class has no departments, show all sections
                            if (res.sections && res.sections.length > 0) {
                                let sectionOptions = '<option value="">Select Section</option>';
                                res.sections.forEach(function(s) {
                                    sectionOptions += `<option value="${s.id}">${s.name}</option>`;
                                });
                                $('#current_section_id').html(sectionOptions).prop('disabled', false);
                                $('#current_section_id').removeAttr('title');
                            }
                        }
                    }
                }
            }, 'json');
        }
    });

    // When opening add modal, reset and disable dropdowns
    $('#addStudentBtn').click(function() {
        $('#studentForm')[0].reset();
        $('#studentId').val('');
        $('#studentModalLabel').text('Add Student');
        $('#profile_picture').val('');
        $('#profile_picture').replaceWith($('#profile_picture').clone(true));
        $('#profilePicPreview').html('');
        $('#removeStudentPhotoBtn').hide();
        $('#removeStudentPhoto').val('0');
        $('#current_department_id').html('<option value="">No Department</option>').prop('disabled', true);
        $('#current_section_id').html('<option value="">No Section</option>').prop('disabled', true);
    });

    // When opening edit modal, update dropdowns for the student's class
    $(document).on('click', '.editBtn', function() {
        const id = $(this).data('id');
        // Reset file input and preview
        $('#profile_picture').val('');
        $('#profile_picture').replaceWith($('#profile_picture').clone(true));
        $('#profilePicPreview').html('');
        $('#removeStudentPhotoBtn').hide();
        $('#removeStudentPhoto').val('0');
        $.get('../api/get_students.php', function(res) {
            if (res.success) {
                const student = res.data.find(s => s.id == id);
                if (student) {
                    $('#studentId').val(student.id);
                    $('#first_name').val(student.first_name);
                    $('#last_name').val(student.last_name);
                    $('#dob').val(student.dob);
                    $('#gender').val(student.gender);
                    $('#address').val(student.address);
                    $('#admission_date').val(student.admission_date);
                    $('#current_class_id').val(student.current_class_id);
                    updateDeptAndSectionDropdowns(student.current_class_id);
                    setTimeout(function() {
                        $('#current_department_id').val(student.current_department_id);
                        $('#current_section_id').val(student.current_section_id);
                        
                        // Check if section should be enabled based on department selection
                        var selectedDept = $('#current_department_id').val();
                        var realDepartments = res.departments ? res.departments.filter(d => d.id != 0) : [];
                        
                        if (realDepartments.length > 0) {
                            if (selectedDept) {
                                $('#current_section_id').prop('disabled', false);
                            } else {
                                $('#current_section_id').prop('disabled', true);
                            }
                        } else {
                            $('#current_section_id').prop('disabled', false);
                        }
                    }, 300);
                    if (student.father_id) {
                        $('#father_id').val(student.father_id);
                    } else {
                        $('#father_id').val('');
                    }
                    if (student.mother_id) {
                        $('#mother_id').val(student.mother_id);
                    } else {
                        $('#mother_id').val('');
                    }
                    if (student.local_guardian_id) {
                        $('#local_guardian_id').val(student.local_guardian_id);
                    } else {
                        $('#local_guardian_id').val('');
                    }
                    if (student.profile_picture) {
                        $('#profilePicPreview').html(`<img src='../uploads/students/${student.profile_picture}' alt='Profile' width='80' height='80' style='object-fit:cover;border-radius:50%;'>`);
                        $('#removeStudentPhotoBtn').show();
                        $('#removeStudentPhoto').val('0');
                    } else {
                        $('#profilePicPreview').html('');
                        $('#removeStudentPhotoBtn').hide();
                        $('#removeStudentPhoto').val('0');
                    }
                    $('#studentRollNo').val(student.roll_no);
                    $('#studentNote').val(student.note);
                    $('#studentModalLabel').text('Edit Student');
                    var modal = new bootstrap.Modal(document.getElementById('studentModal'));
                    modal.show();
                }
            }
        });
    });

    // Show image preview on file select
    $('#profile_picture').on('change', function() {
        const [file] = this.files;
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#profilePicPreview').html(`<img src='${e.target.result}' alt='Profile' width='80' height='80' style='object-fit:cover;border-radius:50%;'>`);
                $('#removeStudentPhotoBtn').show();
                $('#removeStudentPhoto').val('0');
            };
            reader.readAsDataURL(file);
        } else {
            $('#profilePicPreview').html('');
            $('#removeStudentPhotoBtn').hide();
        }
    });

    $('#studentForm').submit(function(e) {
        e.preventDefault();
        const id = $('#studentId').val();
        const url = id ? '../api/update_student.php' : '../api/add_student.php';
        var formData = new FormData(this);
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#studentModal').modal('hide');
                    loadStudents();
                } else {
                    alert(res.message);
                }
            }
        });
    });

    $(document).on('click', '.deleteBtn', function() {
        if (!confirm('Are you sure you want to delete this student?')) return;
        const id = $(this).data('id');
        $.post('../api/delete_student.php', {id}, function(res) {
            if (res.success) {
                loadStudents();
            } else {
                alert(res.message);
            }
        }, 'json');
    });

    // Promote student
    $(document).on('click', '.promoteBtn', function() {
        const id = $(this).data('id');
        $('#promoteStudentId').val(id);
        $('#promote_class_id').val('');
        $('#promote_department_id').val('');
        $('#promote_section_id').val('');
        var modal = new bootstrap.Modal(document.getElementById('promoteModal'));
        modal.show();
    });

    $('#promoteForm').submit(function(e) {
        e.preventDefault();
        $.post('../api/promote_student.php', $(this).serialize(), function(res) {
            if (res.success) {
                $('#promoteModal').modal('hide');
                loadStudents();
            } else {
                alert(res.message);
            }
        }, 'json');
    });

    // Remove photo button
    $('#removeStudentPhotoBtn').click(function() {
        $('#profilePicPreview').html('');
        $('#profile_picture').val('');
        $(this).hide();
        $('#removeStudentPhoto').val('1');
    });

    $('#studentSearch').on('input', function() {
        var value = $(this).val().toLowerCase();
        $('#studentsTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Toggle details row
    $(document).on('click', '.toggle-details-btn', function() {
        var studentId = $(this).data('id');
        var $row = $(this).closest('tr');
        // If already open, close and return
        if ($row.next().hasClass('student-details-row')) {
            $row.next().find('.details-content').slideUp(200, function() {
                $row.next().remove();
            });
            return;
        }
        // Remove any open details row with animation
        var $openRow = $('#studentsTable tbody .student-details-row');
        if ($openRow.length) {
            $openRow.find('.details-content').slideUp(200, function() {
                $openRow.remove();
            });
        }
        // Find student data
        $.get('../api/get_students.php', function(res) {
            if (res.success) {
                var s = res.data.find(ss => ss.id == studentId);
                if (!s) return;
                var details = `<tr class='student-details-row'><td colspan='11'><div class='details-content' style='display:none;'><ul class='mb-0'>
                    <li><strong>First Name:</strong> ${s.first_name || 'N/A'}</li>
                    <li><strong>Last Name:</strong> ${s.last_name || 'N/A'}</li>
                    <li><strong>Date of Birth:</strong> ${(!s.dob || s.dob === '0000-00-00') ? 'N/A' : s.dob}</li>
                    <li><strong>Gender:</strong> ${s.gender || 'N/A'}</li>
                    <li><strong>Address:</strong> ${s.address || 'N/A'}</li>
                    <li><strong>Admission Date:</strong> ${(!s.admission_date || s.admission_date === '0000-00-00') ? 'N/A' : s.admission_date}</li>
                    <li><strong>Father:</strong> ${(s.father_first_name ? s.father_first_name + ' ' + (s.father_last_name || '') : 'N/A') + (s.father_phone ? ' (' + s.father_phone + ')' : '')}</li>
                    <li><strong>Mother:</strong> ${(s.mother_first_name ? s.mother_first_name + ' ' + (s.mother_last_name || '') : 'N/A') + (s.mother_phone ? ' (' + s.mother_phone + ')' : '')}</li>
                    <li><strong>Note:</strong> ${s.note || 'N/A'}</li>
                </ul></div></td></tr>`;
                var $detailsRow = $(details);
                $row.after($detailsRow);
                $detailsRow.find('.details-content').slideDown(200);
            }
        });
    });
});
