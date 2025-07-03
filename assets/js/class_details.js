$(document).ready(function() {
    var classId = $('#classDetailsApp').data('class-id');
    function loadClassDetails() {
        $.get('../api/get_class_details.php', {class_id: classId}, function(res) {
            if (res.success) {
                console.log('Class details loaded:', res); // Debug
                // Show class name in header
                $('#className').text(res.class_name);
                $('#classNameHeader').html('Class Name: <span id="className">' + res.class_name + '</span>');
                $('#className').css('color', 'blue');
                // Departments section - show independent departments
                if (res.departments && res.departments.length > 0) {
                    console.log('Independent departments found:', res.departments); // Debug
                    renderDepartments(res.departments);
                    $('#departmentsSection').show();
                } else {
                    $('#departmentsSection').hide();
                }
                
                // Always show subjects section
                    renderSubjects(res.subjects, res.teachers, res.departments);
                    $('#subjectsSection').show();
                // Subjects by department
                var byDeptHtml = '';
                var depts = {};
                res.subjects.filter(s => s.department_id).forEach(function(s) {
                    if (!depts[s.department_id]) depts[s.department_id] = [];
                    depts[s.department_id].push(s);
                });
                Object.keys(depts).forEach(function(deptId) {
                    byDeptHtml += `<div class="mb-2"><strong>${depts[deptId][0].department_name}:</strong> `;
                    depts[deptId].forEach(function(s) {
                        byDeptHtml += `<span class="badge bg-secondary subject-badge me-2">${s.name}</span>`;
                    });
                    byDeptHtml += '</div>';
                });
                $('#subjectsByDepartment').html(byDeptHtml);
                // Teachers
                // Group teachers by teacher_id, then by department_id
                var teacherGroups = {};
                res.teachers.forEach(function(t) {
                    if (!teacherGroups[t.teacher_id]) {
                        teacherGroups[t.teacher_id] = {
                            first_name: t.first_name,
                            last_name: t.last_name,
                            profile_picture: t.profile_picture,
                            teacher_id: t.teacher_id,
                            departments: {}
                        };
                    }
                    var deptKey = t.department_id || 0;
                    if (!teacherGroups[t.teacher_id].departments[deptKey]) {
                        teacherGroups[t.teacher_id].departments[deptKey] = {
                            department_id: t.department_id,
                            department_name: t.department_id && t.department_id != 0 ? t.department_name : '',
                            subjects: []
                        };
                    }
                    if (t.subject_id && t.subject_name) {
                        teacherGroups[t.teacher_id].departments[deptKey].subjects.push({id: t.subject_id, name: t.subject_name});
                    }
                });
                // Check if class has departments
                var hasDepartments = res.departments && res.departments.length > 0;
                // Update table header for Department column
                var $thead = $('#classTeachersTable thead tr');
                if (hasDepartments) {
                    if ($thead.find('th').length === 5) {
                        // Add Department column if not present
                        $('<th>Department</th>').insertBefore($thead.find('th').eq(3));
                    }
                } else {
                    if ($thead.find('th').length === 6) {
                        $thead.find('th').eq(3).remove();
                    }
                }
                var teacherRows = '';
                Object.values(teacherGroups).forEach(function(teacher) {
                    var deptEntries = Object.values(teacher.departments);
                    // Filter out department_id 0 if there are other departments
                    var hasOnlyDept0 = deptEntries.length === 1 && (deptEntries[0].department_id == 0 || deptEntries[0].department_id === null);
                    var filteredDeptEntries = deptEntries.filter(function(dept) {
                        if (hasOnlyDept0) return true;
                        return dept.department_id != 0 && dept.department_id !== null;
                    });
                    var rowSpan = filteredDeptEntries.length;
                    // If no departments, ensure one row per teacher
                    if (!hasDepartments) {
                        teacherRows += `<tr>`;
                        teacherRows += `<td>${teacher.teacher_id}</td>`;
                        teacherRows += `<td>${teacher.first_name} ${teacher.last_name}</td>`;
                        let pic = teacher.profile_picture ? `<img src='../uploads/teachers/${teacher.profile_picture}' alt='Profile' width='40' height='40' style='object-fit:cover;border-radius:50%;'>` : '';
                        teacherRows += `<td>${pic}</td>`;
                        teacherRows += `<td></td>`; // Empty department
                        let subjects = '';
                        deptEntries.forEach(function(dept) {
                            if (dept.subjects.length > 0) {
                                dept.subjects.forEach(function(subj) {
                                    subjects += `<span class='badge bg-secondary subject-badge me-1'>${subj.name}</span>`;
                                });
                            }
                        });
                        teacherRows += `<td>${subjects}</td>`;
                        teacherRows += `<td>${res.teachers.find(t => t.teacher_id == teacher.teacher_id).attendance_status_today || ''}</td>`;
                        teacherRows += `<td class="text-end">
                            <button class="btn btn-sm btn-primary edit-teacher-subjects-btn me-1" data-teacher-id="${teacher.teacher_id}">Edit Subjects</button>
                            <button class="btn btn-sm btn-danger remove-teacher-btn" data-teacher-id="${teacher.teacher_id}">&times;</button>
                        </td>`;
                        teacherRows += `</tr>`;
                    } else {
                        filteredDeptEntries.forEach(function(dept, idx) {
                            teacherRows += `<tr>`;
                            if (idx === 0) {
                                teacherRows += `<td rowspan="${rowSpan}">${teacher.teacher_id}</td>`;
                                teacherRows += `<td rowspan="${rowSpan}">${teacher.first_name} ${teacher.last_name}</td>`;
                                let pic = teacher.profile_picture ? `<img src='../uploads/teachers/${teacher.profile_picture}' alt='Profile' width='40' height='40' style='object-fit:cover;border-radius:50%;'>` : '';
                                teacherRows += `<td rowspan="${rowSpan}">${pic}</td>`;
                            }
                            teacherRows += `<td>${dept.department_name}</td>`;
                            let subjects = '';
                            if (dept.subjects.length > 0) {
                                dept.subjects.forEach(function(subj) {
                                    subjects += `<span class='badge bg-secondary subject-badge me-1'>${subj.name}</span>`;
                                });
                            }
                            teacherRows += `<td>${subjects}</td>`;
                            if (idx === 0) {
                                teacherRows += `<td rowspan="${rowSpan}">${res.teachers.find(t => t.teacher_id == teacher.teacher_id).attendance_status_today || ''}</td>`;
                                teacherRows += `<td rowspan="${rowSpan}" class="text-end">
                                    <button class="btn btn-sm btn-primary edit-teacher-subjects-btn me-1" data-teacher-id="${teacher.teacher_id}">Edit Subjects</button>
                                    <button class="btn btn-sm btn-danger remove-teacher-btn" data-teacher-id="${teacher.teacher_id}">&times;</button>
                                </td>`;
                            }
                            teacherRows += `</tr>`;
                        });
                    }
                });
                $('#classTeachersTable tbody').html(teacherRows);
                // Students
                var studentRows = '';
                res.students.forEach(function(s) {
                    let pic = s.profile_picture ? `<img src='../uploads/students/${s.profile_picture}' alt='Profile' width='40' height='40' style='object-fit:cover;border-radius:50%;'>` : '';
                    let att = s.attendance ? `<span class="badge bg-success">${s.attendance}</span>` : '<span class="badge bg-secondary">N/A</span>';
                    studentRows += `<tr>
                        <td>${s.id}</td>
                        <td>${s.first_name} ${s.last_name || ''}</td>
                        <td>${pic}</td>
                        <td>${att}</td>
                    </tr>`;
                });
                $('#classStudentsTable tbody').html(studentRows);
                // Populate Assign Teacher dropdown (only teachers assigned to this class)
                var teacherOptions = '<option value="">Select Teacher</option>';
                var teacherIds = [];
                res.teachers.forEach(function(t) {
                    if (!teacherIds.includes(t.teacher_id)) {
                        teacherOptions += `<option value="${t.teacher_id}">${t.first_name} ${t.last_name}</option>`;
                        teacherIds.push(t.teacher_id);
                    }
                });
                $('#addTeacherSelect').html(teacherOptions);
                // Department dropdown logic
                if (res.departments && res.departments.length > 0) {
                    $('#departmentDropdownContainer').show();
                    var deptOptions = '<option value="">Select Department</option>';
                    res.departments.forEach(function(d) {
                        deptOptions += `<option value="${d.id}">${d.name}</option>`;
                    });
                    $('#addTeacherDepartmentSelect').html(deptOptions);
                } else {
                    $('#departmentDropdownContainer').hide();
                    $('#addTeacherDepartmentSelect').html('');
                }
                // Populate Subject dropdown (subjects assigned to this class, filter by department if present)
                function updateSubjectDropdown() {
                    var subjectOptions = '<option value="">Select Subject</option>';
                    var subjectIds = [];
                    var selectedDept = $('#addTeacherDepartmentSelect').val();
                    res.subjects.forEach(function(s) {
                        if (selectedDept) {
                            if (s.department_id == selectedDept && !subjectIds.includes(s.subject_id)) {
                                subjectOptions += `<option value="${s.subject_id}">${s.name}</option>`;
                                subjectIds.push(s.subject_id);
                            }
                        } else if (!selectedDept && (!s.department_id || s.department_id == 0) && !subjectIds.includes(s.subject_id)) {
                            subjectOptions += `<option value="${s.subject_id}">${s.name}</option>`;
                            subjectIds.push(s.subject_id);
                        }
                    });
                    $('#addTeacherSubjectSelect').html(subjectOptions);
                }
                updateSubjectDropdown();
                $('#addTeacherDepartmentSelect').off('change').on('change', function() {
                    updateSubjectDropdown();
                });
                window.lastClassDetails = res;
                console.log('Sections data:', res.sections); // Debug
                renderSectionsTable(res.sections, res.departments);
            }
        });
    }
    loadClassDetails();
    // Assign department
    $('#addDepartmentBtn').click(function() {
        var deptId = $('#addDepartmentSelect').val();
        if (!deptId) return;
        $.post('../api/assign_department_to_class.php', {class_id: classId, department_id: deptId, action: 'add'}, function(res) {
            if (res.success) loadClassDetails();
        }, 'json');
    });
    // Remove department
    $(document).on('click', '.remove-dept', function(e) {
        e.preventDefault();
        var deptId = $(this).data('id');
        $.post('../api/assign_department_to_class.php', {class_id: classId, department_id: deptId, action: 'remove'}, function(res) {
            if (res.success) loadClassDetails();
        }, 'json');
    });
    // Assign subject
    $('#addSubjectBtn').click(function() {
        var subjId = $('#addSubjectSelect').val();
        if (!subjId) return;
        $.post('../api/assign_subject_to_class.php', {class_id: classId, subject_id: subjId, action: 'add'}, function(res) {
            if (res.success) loadClassDetails();
        }, 'json');
    });
    // Remove subject
    $(document).on('click', '.remove-subj', function(e) {
        e.preventDefault();
        var subjId = $(this).data('id');
        var deptId = $(this).data('dept') || null;
        $.post('../api/assign_subject_to_class.php', {class_id: classId, subject_id: subjId, department_id: deptId, action: 'remove'}, function(res) {
            if (res.success) loadClassDetails();
        }, 'json');
    });
    // Assign teacher
    $('#addTeacherBtn').click(function() {
        var teacherId = $('#addTeacherSelect').val();
        var departmentId = $('#addTeacherDepartmentSelect').val() || 0;
        var subjId = $('#addTeacherSubjectSelect').val() || null;
        if (!teacherId) return;
        $.post('../api/assign_teacher_to_class.php', {class_id: classId, teacher_id: teacherId, department_id: departmentId, subject_id: subjId, action: 'add'}, function(res) {
            if (res.success) loadClassDetails();
        }, 'json');
    });
    // Remove teacher
    $(document).on('click', '.remove-teacher-btn', function() {
        var teacherId = $(this).data('teacher-id');
        var departmentId = $(this).data('department-id');
        $.post('../api/assign_teacher_to_class.php', {class_id: classId, teacher_id: teacherId, department_id: departmentId, action: 'removeteacherfromclass'}, function(res) {
            if (res.success) loadClassDetails();
        }, 'json');
    });

    // Toggle department to show assigned subjects only
    $(document).on('click', '.toggle-dept', function(e) {
        e.preventDefault();
        var deptId = $(this).data('id');
        var $toggleDiv = $('#subjectsToggle_' + deptId);
        var $icon = $(this).find('.toggle-icon');
        
        if ($toggleDiv.is(':visible')) {
            $toggleDiv.slideUp();
            $icon.css('transform', 'rotate(0deg)');
        } else {
            // Show assigned subjects and teachers for this department
            $.get('../api/get_class_details.php', {class_id: classId}, function(res) {
                if (res.success) {
                    var assigned = res.subjects.filter(s => String(s.department_id) == String(deptId));
                    var html = '';
                    if (assigned.length > 0) {
                        assigned.forEach(function(s) {
                            // Find teachers for this subject and department
                            var teacherNames = res.teachers
                                .filter(t => t.subject_id == s.subject_id && String(t.department_id) == String(deptId))
                                .map(t => t.first_name + ' ' + t.last_name);
                            html += `<li class='list-group-item'>${s.name}`;
                            if (teacherNames.length > 0) {
                                html += ` <span class='text-muted'>(Teachers: ${teacherNames.join(', ')})</span>`;
                            }
                            html += `</li>`;
                        });
                    } else {
                        html = '<li class="list-group-item text-muted">No subjects assigned.</li>';
                    }
                    $toggleDiv.html(html).slideDown();
                    $icon.css('transform', 'rotate(180deg)');
                }
            });
        }
    });
    // Save subject assignment for department (multi-select)
    $(document).on('click', '.save-dept-subjects', function(e) {
        e.preventDefault();
        var deptId = $(this).data('dept');
        var selectedSubjs = [];
        $(`#subjectsToggle_${deptId} input[type=checkbox]:checked`).each(function() {
            selectedSubjs.push($(this).val());
        });
        $.get('../api/get_subjects.php', function(subjRes) {
            if (subjRes.success) {
                var allSubjIds = subjRes.data.map(s => s.id.toString());
                // Remove unselected
                allSubjIds.forEach(function(id) {
                    if (!selectedSubjs.includes(id)) {
                        $.post('../api/assign_subject_to_class.php', {class_id: classId, subject_id: id, department_id: deptId, action: 'remove'});
                    }
                });
                // Add selected
                selectedSubjs.forEach(function(id) {
                    $.post('../api/assign_subject_to_class.php', {class_id: classId, subject_id: id, department_id: deptId, action: 'add'});
                });
            }
        });
        setTimeout(function() {
            loadClassDetails();
        }, 500);
    });


    // Open Assign Teachers modal
    $('#assignTeachersBtn').click(function() {
        // Fetch all teachers and assigned teachers
        $.get('../api/get_teachers.php', function(teachersRes) {
            if (teachersRes.success) {
                $.get('../api/get_class_details.php', {class_id: classId}, function(classRes) {
                    if (classRes.success) {
                        var assignedIds = classRes.teachers.map(t => t.teacher_id.toString());
                        var checkboxes = '';
                        teachersRes.data.forEach(function(t) {
                            var checked = assignedIds.includes(t.id.toString()) ? 'checked' : '';
                            var pic = t.profile_picture ? `<img src='../uploads/teachers/${t.profile_picture}' alt='Profile' width='30' height='30' style='object-fit:cover;border-radius:50%;margin-right:8px;'>` : '';
                            checkboxes += `<div class='form-check mb-2'><input class='form-check-input assign-teacher-checkbox' type='checkbox' value='${t.id}' id='assign_teacher_${t.id}' name='assign_teachers[]' ${checked}><label class='form-check-label' for='assign_teacher_${t.id}'>${pic}${t.first_name} ${t.last_name}</label></div>`;
                        });
                        $('#assignTeachersModalBody').html(checkboxes);
                        $('#assignTeachersModal').modal('show');
                    }
                });
            }
        });
    });
    // Save assigned teachers (modal)
    $('#assignTeachersForm').submit(function(e) {
        e.preventDefault();
        var selected = [];
        $('#assignTeachersModalBody input[type=checkbox]:checked').each(function() {
            selected.push($(this).val());
        });
        // Get all teacher IDs
        $.get('../api/get_teachers.php', function(teachersRes) {
            if (teachersRes.success) {
                var allIds = teachersRes.data.map(t => t.id.toString());
                // Get currently assigned
                $.get('../api/get_class_details.php', {class_id: classId}, function(classRes) {
                    if (classRes.success) {
                        var assignedIds = classRes.teachers.map(t => t.teacher_id.toString());
                        // Remove unchecked
                        assignedIds.forEach(function(id) {
                            if (!selected.includes(id)) {
                                $.post('../api/assign_teacher_to_class.php', {class_id: classId, teacher_id: id, action: 'remove'});
                            }
                        });
                        // Add checked
                        selected.forEach(function(id) {
                            if (!assignedIds.includes(id)) {
                                $.post('../api/assign_teacher_to_class.php', {class_id: classId, teacher_id: id, subject_id: null, department_id: null, action: 'add'});
                            }
                        });
                        setTimeout(function() {
                            $('#assignTeachersModal').modal('hide');
                            loadClassDetails();
                        }, 500);
                    }
                });
            }
        });
    });
    // Remove a subject for a teacher
    $(document).on('click', '.remove-teacher-subject', function(e) {
        e.preventDefault();
        var teacherId = $(this).data('teacher-id');
        var subjectId = $(this).data('subject-id');
        var departmentId = $(this).data('department-id');
        $.post('../api/assign_teacher_to_class.php', {class_id: classId, teacher_id: teacherId, subject_id: subjectId, department_id: departmentId, action: 'removeteachersubject'}, function(res) {
            if (res.success) loadClassDetails();
        }, 'json');
    });
    // Edit Teacher Subjects button click
    $(document).on('click', '.edit-teacher-subjects-btn', function() {
        var teacherId = $(this).data('teacher-id');
        var classId = $('#classDetailsApp').data('class-id');
        // Fetch class details for departments and subjects
        $.get('../api/get_class_details.php', {class_id: classId}, function(res) {
            if (!res.success) return;
            var departments = res.departments;
            var subjects = res.subjects;
            var teachers = res.teachers;
            var teacher = teachers.find(t => t.teacher_id == teacherId);
            // Departments (if any)
            if (departments.length > 0) {
                var deptHtml = '<label class="form-label">Departments</label><div class="list-group">';
                departments.forEach(function(dept) {
                    var checked = teachers.some(t => t.teacher_id == teacherId && t.department_id == dept.id) ? 'checked' : '';
                    deptHtml += `<div class='list-group-item'>
                        <div class='form-check'>
                            <input class='form-check-input edit-teacher-dept-checkbox' type='checkbox' value='${dept.id}' id='edit_teacher_dept_${dept.id}' name='departments[]' ${checked} data-dept-id='${dept.id}'>
                            <label class='form-check-label' for='edit_teacher_dept_${dept.id}'>${dept.name}</label>
                        </div>
                        <div class='edit-teacher-subjects-list' id='edit_teacher_subjects_list_${dept.id}' style='margin-left:2em; display:${checked ? 'block' : 'none'};'></div>
                    </div>`;
                });
                deptHtml += '</div>';
                $('#editTeacherDepartmentsContainer').html(deptHtml).show();
            } else {
                $('#editTeacherDepartmentsContainer').hide();
            }
            // Subjects rendering for departments
            window.renderSubjectsForDepartments = function() {
                departments.forEach(function(dept) {
                    var deptId = dept.id;
                    var show = $(`#edit_teacher_dept_${deptId}`).is(':checked');
                    var subjHtml = '';
                    if (show) {
                        var deptSubjects = subjects.filter(s => s.department_id == deptId);
                        if (deptSubjects.length > 0) {
                            subjHtml += '<ul class="list-group">';
                            deptSubjects.forEach(function(subj) {
                                var checked = teachers.some(t => t.teacher_id == teacherId && t.subject_id == subj.subject_id && t.department_id == subj.department_id) ? 'checked' : '';
                                subjHtml += `<li class='list-group-item'>
                                    <div class='form-check'>
                                        <input class='form-check-input edit-teacher-subj-checkbox' type='checkbox' value='${subj.subject_id}' data-department-id='${deptId}' id='edit_teacher_subj_${subj.subject_id}_${deptId}' name='subjects[]' ${checked}>
                                        <label class='form-check-label' for='edit_teacher_subj_${subj.subject_id}_${deptId}'>${subj.name}</label>
                                    </div>
                                </li>`;
                            });
                            subjHtml += '</ul>';
                        } else {
                            subjHtml += '<div class="text-muted">No subjects assigned to this department.</div>';
                        }
                    }
                    $(`#edit_teacher_subjects_list_${deptId}`).html(subjHtml).toggle(show);
                });
            };
            // Initial render
            if (departments.length > 0) renderSubjectsForDepartments();
            // For classes without departments
            if (departments.length === 0) {
                var subjHtml = '<label class="form-label">Subjects</label><ul class="list-group">';
                subjects.forEach(function(subj) {
                    var checked = teachers.some(t => t.teacher_id == teacherId && t.subject_id == subj.subject_id) ? 'checked' : '';
                    subjHtml += `<li class='list-group-item'>
                        <div class='form-check'>
                            <input class='form-check-input edit-teacher-subj-checkbox' type='checkbox' value='${subj.subject_id}' id='edit_teacher_subj_${subj.subject_id}' name='subjects[]' ${checked}>
                            <label class='form-check-label' for='edit_teacher_subj_${subj.subject_id}'>${subj.name}</label>
                        </div>
                    </li>`;
                });
                subjHtml += '</ul>';
                $('#editTeacherSubjectsContainer').html(subjHtml);
            } else {
                $('#editTeacherSubjectsContainer').html('');
            }
            // Store teacherId for submit
            $('#editTeacherSubjectsForm').data('teacher-id', teacherId);
            $('#editTeacherSubjectsModal').modal('show');
        });
    });
    // Handle individual subject checkbox changes
    $(document).off('change', '.edit-teacher-subj-checkbox').on('change', '.edit-teacher-subj-checkbox', function() {
        var classId = $('#classDetailsApp').data('class-id');
        var teacherId = $('#editTeacherSubjectsForm').data('teacher-id');
        var subjectId = $(this).val();
        var departmentId = $(this).data('department-id') || null;
        var action = $(this).is(':checked') ? 'add' : 'remove';
        
        $.post('../api/edit_teacher_subjects.php', {
            class_id: classId,
            teacher_id: teacherId,
            subject_id: subjectId,
            department_id: departmentId,
            action: action
        }, function(res) {
            if (res.success) {
                // Optionally refresh the class details to show updated state
                // loadClassDetails();
            } else {
                alert(res.message || 'Failed to update teacher assignment.');
                // Revert the checkbox state if the operation failed
                $(this).prop('checked', !$(this).is(':checked'));
            }
        }, 'json');
    });

    // Handle individual department checkbox changes
    $(document).off('change', '.edit-teacher-dept-checkbox').on('change', '.edit-teacher-dept-checkbox', function() {
        var classId = $('#classDetailsApp').data('class-id');
        var teacherId = $('#editTeacherSubjectsForm').data('teacher-id');
        var departmentId = $(this).val();
        var action = $(this).is(':checked') ? 'add' : 'remove';
        var $this = $(this);
        
        console.log('Department checkbox changed:', departmentId, action); // Debug log
        
        $.post('../api/edit_teacher_subjects.php', {
            class_id: classId,
            teacher_id: teacherId,
            department_id: departmentId,
            action: action
        }, function(res) {
            if (res.success) {
                // If removing department, also uncheck all subject checkboxes in that department
                if (action === 'remove') {
                    $(`.edit-teacher-subj-checkbox[data-department-id="${departmentId}"]`).prop('checked', false);
                }
                // Refresh the subjects list for this department
                if (window.renderSubjectsForDepartments) {
                    window.renderSubjectsForDepartments();
                }
            } else {
                alert(res.message || 'Failed to update teacher assignment.');
                // Revert the checkbox state if the operation failed
                $this.prop('checked', !$this.is(':checked'));
            }
        }, 'json');
    });

    // Submit edit teacher subjects form (now just closes the modal)
    $('#editTeacherSubjectsForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        $('#editTeacherSubjectsModal').modal('hide');
        loadClassDetails(); // Refresh to show any changes
    });

    // TODO: Populate department, subject, and teacher selects with available options (AJAX or server-side)
    
    // Edit Class Structure Modal Logic
    $('#editClassStructureBtn').on('click', function() {
        $('#editClassStructureModal').modal('show');
        loadClassStructureData();
    });
    
    function loadClassStructureData() {
        // Load all available data
        Promise.all([
            $.get('../api/get_departments.php'),
            $.get('../api/get_sections.php'),
            $.get('../api/get_subjects.php'),
            $.get('../api/get_class_details.php', { class_id: classId })
        ]).then(function(responses) {
            var departmentsRes = responses[0];
            var sectionsRes = responses[1];
            var subjectsRes = responses[2];
            var classDetailsRes = responses[3];
            
            if (departmentsRes.success && sectionsRes.success && subjectsRes.success && classDetailsRes.success) {
                // Store data globally for event handlers
                window.lastDepartmentsData = departmentsRes.data;
                window.lastSectionsData = sectionsRes.data;
                window.lastSubjectsData = subjectsRes.data;
                window.lastClassDetails = classDetailsRes;
                
                renderClassStructureModal(departmentsRes.data, sectionsRes.data, subjectsRes.data, classDetailsRes);
            } else {
                alert('Failed to load data for class structure editing.');
            }
        }).catch(function() {
            alert('Failed to load data for class structure editing.');
        });
    }
    
    function renderClassStructureModal(departments, sections, subjects, classDetails) {
        // Set up initial state based on current class structure
        var hasDepartments = classDetails.departments && classDetails.departments.length > 0;
        var hasSections = classDetails.sections && classDetails.sections.length > 0;
        
        $('#hasDepartmentsCheck').prop('checked', hasDepartments);
        $('#hasSectionsCheck').prop('checked', hasSections);
        
        // Debug logging
        console.log('renderClassStructureModal - departments:', departments);
        console.log('renderClassStructureModal - classDetails.departments:', classDetails.departments);
        console.log('renderClassStructureModal - hasDepartments:', hasDepartments);
        console.log('renderClassStructureModal - hasSections:', hasSections);
        
        // Render department checkboxes
        var deptHtml = '';
        departments.forEach(function(dept) {
            if (dept.id == 0) return; // Skip "None" department
            var checked = hasDepartments && classDetails.departments.some(d => d.id == dept.id) ? 'checked' : '';
            deptHtml += `<div class='form-check'>
                <input class='form-check-input department-checkbox' type='checkbox' value='${dept.id}' id='dept_${dept.id}' ${checked}>
                <label class='form-check-label' for='dept_${dept.id}'>${dept.name}</label>
            </div>`;
        });
        $('#departmentsCheckboxes').html(deptHtml);
        
        console.log('Department checkboxes HTML:', deptHtml);
        
        // Apply conditional logic based on current state
        updateModalSections(hasDepartments, hasSections, classDetails);
    }
    
    function updateModalSections(hasDepartments, hasSections, classDetails) {
        var selectedDepts = $('.department-checkbox:checked').map(function() {
            return parseInt($(this).val());
        }).get();
        
        console.log('updateModalSections - hasDepartments:', hasDepartments);
        console.log('updateModalSections - hasSections:', hasSections);
        console.log('updateModalSections - selectedDepts:', selectedDepts);
        
        // Hide all sections initially
        $('#selectDepartmentsSection').hide();
        $('#globalSectionsSection').hide();
        $('#globalSubjectsSection').hide();
        $('#departmentSectionsSection').hide();
        $('#departmentSubjectsSection').hide();
        
        // Case 1: No departments and no sections
        if (!hasDepartments && !hasSections) {
            $('#globalSubjectsSection').show();
            renderGlobalSubjects();
        }
        // Case 2: Only departments checked
        else if (hasDepartments && !hasSections) {
            $('#selectDepartmentsSection').show();
            // Always show department subjects section, but populate based on selection
            $('#departmentSubjectsSection').show();
            renderAllDepartmentSubjects(); // Render all subjects for all departments
            updateDepartmentSubjectsVisibility(selectedDepts); // Show only selected departments
        }
        // Case 3: Only sections checked
        else if (!hasDepartments && hasSections) {
            $('#globalSectionsSection').show();
            $('#globalSubjectsSection').show();
            renderGlobalSections();
            renderGlobalSubjects();
        }
        // Case 4: Both departments and sections checked
        else if (hasDepartments && hasSections) {
            $('#selectDepartmentsSection').show();
            $('#departmentSectionsSection').show();
            $('#departmentSubjectsSection').show();
            renderAllDepartmentSections(); // Render all sections for all departments
            renderAllDepartmentSubjects(); // Render all subjects for all departments
            updateDepartmentSectionsVisibility(selectedDepts); // Show only selected departments
            updateDepartmentSubjectsVisibility(selectedDepts); // Show only selected departments
        }
    }
    
    function renderGlobalSections() {
        if (!window.lastSectionsData) return;
        
        var assignedSections = window.lastClassDetails.sections || [];
        var assignedMap = {};
        
        // Group assigned sections by department
        assignedSections.forEach(function(s) {
            var deptId = s.department_id || 'global';
            if (!assignedMap[deptId]) assignedMap[deptId] = [];
            assignedMap[deptId].push(s.id.toString());
        });
        
        var globalSectionsHtml = '<div class="d-flex flex-wrap">';
        window.lastSectionsData.forEach(function(section) {
            if (section.id == 0) return;
            var checked = (assignedMap['global'] || []).includes(section.id.toString()) ? 'checked' : '';
            globalSectionsHtml += `<div class='form-check me-3 mb-2'>
                <input class='form-check-input section-checkbox' type='checkbox' value='${section.id}' data-department-id='global' id='global_section_${section.id}' ${checked}>
                <label class='form-check-label' for='global_section_${section.id}'>${section.name}</label>
            </div>`;
        });
        globalSectionsHtml += '</div>';
        $('#globalSectionsCheckboxes').html(globalSectionsHtml);
    }
    
    function renderGlobalSubjects() {
        if (!window.lastSubjectsData) return;
        
        var assignedSubjects = window.lastClassDetails.subjects || [];
        var assignedMap = {};
        
        // Group assigned subjects by department
        assignedSubjects.forEach(function(s) {
            var deptId = s.department_id || 'global';
            if (!assignedMap[deptId]) assignedMap[deptId] = [];
            assignedMap[deptId].push(s.subject_id.toString());
        });
        
        var globalSubjectsHtml = '<div class="d-flex flex-wrap">';
        window.lastSubjectsData.forEach(function(subject) {
            if (subject.id == 0) return;
            var checked = (assignedMap['global'] || []).includes(subject.id.toString()) ? 'checked' : '';
            globalSubjectsHtml += `<div class='form-check me-3 mb-2'>
                <input class='form-check-input subject-checkbox' type='checkbox' value='${subject.id}' data-department-id='global' id='global_subject_${subject.id}' ${checked}>
                <label class='form-check-label' for='global_subject_${subject.id}'>${subject.name}</label>
            </div>`;
        });
        globalSubjectsHtml += '</div>';
        $('#globalSubjectsCheckboxes').html(globalSubjectsHtml);
    }
    
    function renderDepartmentSections(selectedDeptIds) {
        if (!window.lastSectionsData || !selectedDeptIds || selectedDeptIds.length === 0) {
            $('#departmentSectionsCheckboxes').html('');
            return;
        }
        
        var assignedSections = window.lastClassDetails.sections || [];
        var assignedMap = {};
        
        // Group assigned sections by department
        assignedSections.forEach(function(s) {
            var deptId = s.department_id || 'global';
            if (!assignedMap[deptId]) assignedMap[deptId] = [];
            assignedMap[deptId].push(s.id.toString());
        });
        
        var deptSectionsHtml = '';
        var selectedDepartments = window.lastDepartmentsData.filter(d => selectedDeptIds.includes(d.id));
        
        selectedDepartments.forEach(function(dept) {
            deptSectionsHtml += `<div class='mb-3'>
                <div class='fw-bold'>${dept.name}:</div>`;
            window.lastSectionsData.forEach(function(section) {
                if (section.id == 0) return;
                var checked = (assignedMap[dept.id] || []).includes(section.id.toString()) ? 'checked' : '';
                deptSectionsHtml += `<div class='form-check ms-3'>
                    <input class='form-check-input section-checkbox' type='checkbox' value='${section.id}' data-department-id='${dept.id}' id='dept_section_${section.id}_${dept.id}' ${checked}>
                    <label class='form-check-label' for='dept_section_${section.id}_${dept.id}'>${section.name}</label>
                </div>`;
            });
            deptSectionsHtml += `</div>`;
        });
        $('#departmentSectionsCheckboxes').html(deptSectionsHtml);
    }
    
    function renderAllDepartmentSubjects() {
        console.log('renderAllDepartmentSubjects called');
        console.log('window.lastSubjectsData:', window.lastSubjectsData);
        console.log('window.lastDepartmentsData:', window.lastDepartmentsData);
        
        if (!window.lastSubjectsData || !window.lastDepartmentsData) {
            console.log('renderAllDepartmentSubjects - early return, data not available');
            $('#departmentSubjectsCheckboxes').html('');
            return;
        }
        
        var assignedSubjects = window.lastClassDetails.subjects || [];
        var assignedMap = {};
        
        // Group assigned subjects by department
        assignedSubjects.forEach(function(s) {
            var deptId = s.department_id || 'global';
            if (!assignedMap[deptId]) assignedMap[deptId] = [];
            assignedMap[deptId].push(s.subject_id.toString());
        });
        
        var deptSubjectsHtml = '';
        
        // Render subjects for ALL departments
        window.lastDepartmentsData.forEach(function(dept) {
            if (dept.id == 0) return; // Skip "None" department
            console.log('Processing department for subjects:', dept);
            deptSubjectsHtml += `<div class='mb-3 department-subjects-group' data-department-id='${dept.id}'>
                <div class='fw-bold mb-2'>${dept.name}:</div>
                <div class='d-flex flex-wrap'>`;
            window.lastSubjectsData.forEach(function(subject) {
                if (subject.id == 0) return;
                var checked = (assignedMap[dept.id] || []).includes(subject.id.toString()) ? 'checked' : '';
                deptSubjectsHtml += `<div class='form-check me-3 mb-2'>
                    <input class='form-check-input subject-checkbox' type='checkbox' value='${subject.id}' data-department-id='${dept.id}' id='dept_subject_${subject.id}_${dept.id}' ${checked}>
                    <label class='form-check-label' for='dept_subject_${subject.id}_${dept.id}'>${subject.name}</label>
                </div>`;
            });
            deptSubjectsHtml += `</div></div>`;
        });
        console.log('Generated all department subjects HTML:', deptSubjectsHtml);
        $('#departmentSubjectsCheckboxes').html(deptSubjectsHtml);
    }
    
    function updateDepartmentSubjectsVisibility(selectedDeptIds) {
        console.log('updateDepartmentSubjectsVisibility called with:', selectedDeptIds);
        
        // Hide all department subject groups
        $('.department-subjects-group').hide();
        
        // Show only selected departments
        if (selectedDeptIds && selectedDeptIds.length > 0) {
            selectedDeptIds.forEach(function(deptId) {
                $(`.department-subjects-group[data-department-id='${deptId}']`).show();
            });
        }
    }
    
    function renderAllDepartmentSections() {
        console.log('renderAllDepartmentSections called');
        
        if (!window.lastSectionsData || !window.lastDepartmentsData) {
            console.log('renderAllDepartmentSections - early return, data not available');
            $('#departmentSectionsCheckboxes').html('');
            return;
        }
        
        var assignedSections = window.lastClassDetails.sections || [];
        var assignedMap = {};
        
        // Group assigned sections by department
        assignedSections.forEach(function(s) {
            var deptId = s.department_id || 'global';
            if (!assignedMap[deptId]) assignedMap[deptId] = [];
            assignedMap[deptId].push(s.id.toString());
        });
        
        var deptSectionsHtml = '';
        
        // Render sections for ALL departments
        window.lastDepartmentsData.forEach(function(dept) {
            if (dept.id == 0) return; // Skip "None" department
            console.log('Processing department for sections:', dept);
            deptSectionsHtml += `<div class='mb-3 department-sections-group' data-department-id='${dept.id}'>
                <div class='fw-bold mb-2'>${dept.name}:</div>
                <div class='d-flex flex-wrap'>`;
            window.lastSectionsData.forEach(function(section) {
                if (section.id == 0) return;
                var checked = (assignedMap[dept.id] || []).includes(section.id.toString()) ? 'checked' : '';
                deptSectionsHtml += `<div class='form-check me-3 mb-2'>
                    <input class='form-check-input section-checkbox' type='checkbox' value='${section.id}' data-department-id='${dept.id}' id='dept_section_${section.id}_${dept.id}' ${checked}>
                    <label class='form-check-label' for='dept_section_${section.id}_${dept.id}'>${section.name}</label>
                </div>`;
            });
            deptSectionsHtml += `</div></div>`;
        });
        console.log('Generated all department sections HTML:', deptSectionsHtml);
        $('#departmentSectionsCheckboxes').html(deptSectionsHtml);
    }
    
    function updateDepartmentSectionsVisibility(selectedDeptIds) {
        console.log('updateDepartmentSectionsVisibility called with:', selectedDeptIds);
        
        // Hide all department section groups
        $('.department-sections-group').hide();
        
        // Show only selected departments
        if (selectedDeptIds && selectedDeptIds.length > 0) {
            selectedDeptIds.forEach(function(deptId) {
                $(`.department-sections-group[data-department-id='${deptId}']`).show();
            });
        }
    }
    
    function renderDepartmentSubjects(selectedDeptIds) {
        console.log('renderDepartmentSubjects called with:', selectedDeptIds);
        console.log('window.lastSubjectsData:', window.lastSubjectsData);
        console.log('window.lastDepartmentsData:', window.lastDepartmentsData);
        
        if (!window.lastSubjectsData || !selectedDeptIds || selectedDeptIds.length === 0) {
            console.log('renderDepartmentSubjects - early return, conditions not met');
            $('#departmentSubjectsCheckboxes').html('');
            return;
        }
        
        var assignedSubjects = window.lastClassDetails.subjects || [];
        var assignedMap = {};
        
        // Group assigned subjects by department
        assignedSubjects.forEach(function(s) {
            var deptId = s.department_id || 'global';
            if (!assignedMap[deptId]) assignedMap[deptId] = [];
            assignedMap[deptId].push(s.subject_id.toString());
        });
        
        var deptSubjectsHtml = '';
        var selectedDepartments = window.lastDepartmentsData.filter(d => selectedDeptIds.includes(d.id));
        
        selectedDepartments.forEach(function(dept) {
            console.log('Processing department:', dept);
            deptSubjectsHtml += `<div class='mb-3'>
                <div class='fw-bold'>${dept.name}:</div>`;
            window.lastSubjectsData.forEach(function(subject) {
                if (subject.id == 0) return;
                var checked = (assignedMap[dept.id] || []).includes(subject.id.toString()) ? 'checked' : '';
                deptSubjectsHtml += `<div class='form-check ms-3'>
                    <input class='form-check-input subject-checkbox' type='checkbox' value='${subject.id}' data-department-id='${dept.id}' id='dept_subject_${subject.id}_${dept.id}' ${checked}>
                    <label class='form-check-label' for='dept_subject_${subject.id}_${dept.id}'>${subject.name}</label>
                </div>`;
            });
            deptSubjectsHtml += `</div>`;
        });
        console.log('Generated department subjects HTML:', deptSubjectsHtml);
        $('#departmentSubjectsCheckboxes').html(deptSubjectsHtml);
    }
    
    function renderSectionsInModal(sections, classDetails, hasDepartments, selectedDeptIds) {
                    var assignedSections = classDetails.sections || [];
                    var assignedMap = {};
                    
        // Group assigned sections by department
                    assignedSections.forEach(function(s) {
            var deptId = s.department_id || 'global';
                        if (!assignedMap[deptId]) assignedMap[deptId] = [];
            assignedMap[deptId].push(s.id.toString());
        });
        
        // Global sections (no department)
        var globalSectionsHtml = '';
        sections.forEach(function(section) {
                                if (section.id == 0) return;
            var checked = (assignedMap['global'] || []).includes(section.id.toString()) ? 'checked' : '';
            globalSectionsHtml += `<div class='form-check'>
                <input class='form-check-input section-checkbox' type='checkbox' value='${section.id}' data-department-id='global' id='global_section_${section.id}' ${checked}>
                <label class='form-check-label' for='global_section_${section.id}'>${section.name}</label>
            </div>`;
        });
        $('#globalSectionsCheckboxes').html(globalSectionsHtml);
        
        // Department sections
        console.log('renderSectionsInModal - hasDepartments:', hasDepartments);
        console.log('renderSectionsInModal - selectedDeptIds:', selectedDeptIds);
        console.log('renderSectionsInModal - classDetails.departments:', classDetails.departments);
        
        if (hasDepartments && selectedDeptIds && selectedDeptIds.length > 0) {
            var deptSectionsHtml = '';
            // Use selectedDeptIds to get department details from window.lastDepartmentsData
            var selectedDepartments = window.lastDepartmentsData.filter(d => selectedDeptIds.includes(d.id));
            
            selectedDepartments.forEach(function(dept) {
                deptSectionsHtml += `<div class='mb-3'>
                    <div class='fw-bold'>${dept.name}:</div>`;
                sections.forEach(function(section) {
                    if (section.id == 0) return;
                    var checked = (assignedMap[dept.id] || []).includes(section.id.toString()) ? 'checked' : '';
                    deptSectionsHtml += `<div class='form-check ms-3'>
                        <input class='form-check-input section-checkbox' type='checkbox' value='${section.id}' data-department-id='${dept.id}' id='dept_section_${section.id}_${dept.id}' ${checked}>
                        <label class='form-check-label' for='dept_section_${section.id}_${dept.id}'>${section.name}</label>
                                </div>`;
                            });
                deptSectionsHtml += `</div>`;
                        });
            $('#departmentSectionsCheckboxes').html(deptSectionsHtml);
            console.log('Department sections HTML generated:', deptSectionsHtml);
                    } else {
            console.log('Department sections not rendered - condition not met');
            $('#departmentSectionsCheckboxes').html('');
        }
    }
    
    function renderSubjectsInModal(subjects, classDetails, hasDepartments, selectedDeptIds) {
        var assignedSubjects = classDetails.subjects || [];
        var assignedMap = {};
        
        // Group assigned subjects by department
        assignedSubjects.forEach(function(s) {
            var deptId = s.department_id || 'global';
            if (!assignedMap[deptId]) assignedMap[deptId] = [];
            assignedMap[deptId].push(s.subject_id.toString());
        });
        
        // Global subjects (no department)
        var globalSubjectsHtml = '';
        subjects.forEach(function(subject) {
            if (subject.id == 0) return;
            var checked = (assignedMap['global'] || []).includes(subject.id.toString()) ? 'checked' : '';
            globalSubjectsHtml += `<div class='form-check'>
                <input class='form-check-input subject-checkbox' type='checkbox' value='${subject.id}' data-department-id='global' id='global_subject_${subject.id}' ${checked}>
                <label class='form-check-label' for='global_subject_${subject.id}'>${subject.name}</label>
                            </div>`;
                        });
        $('#globalSubjectsCheckboxes').html(globalSubjectsHtml);
        
        // Department subjects
        console.log('renderSubjectsInModal - hasDepartments:', hasDepartments);
        console.log('renderSubjectsInModal - selectedDeptIds:', selectedDeptIds);
        console.log('renderSubjectsInModal - classDetails.departments:', classDetails.departments);
        
        if (hasDepartments && selectedDeptIds && selectedDeptIds.length > 0) {
            var deptSubjectsHtml = '';
            // Use selectedDeptIds to get department details from window.lastDepartmentsData
            var selectedDepartments = window.lastDepartmentsData.filter(d => selectedDeptIds.includes(d.id));
            
            selectedDepartments.forEach(function(dept) {
                deptSubjectsHtml += `<div class='mb-3'>
                    <div class='fw-bold'>${dept.name}:</div>`;
                subjects.forEach(function(subject) {
                    if (subject.id == 0) return;
                    var checked = (assignedMap[dept.id] || []).includes(subject.id.toString()) ? 'checked' : '';
                    deptSubjectsHtml += `<div class='form-check ms-3'>
                        <input class='form-check-input subject-checkbox' type='checkbox' value='${subject.id}' data-department-id='${dept.id}' id='dept_subject_${subject.id}_${dept.id}' ${checked}>
                        <label class='form-check-label' for='dept_subject_${subject.id}_${dept.id}'>${subject.name}</label>
                    </div>`;
                });
                deptSubjectsHtml += `</div>`;
            });
            $('#departmentSubjectsCheckboxes').html(deptSubjectsHtml);
            console.log('Department subjects HTML generated:', deptSubjectsHtml);
        } else {
            console.log('Department subjects not rendered - condition not met');
            $('#departmentSubjectsCheckboxes').html('');
        }
    }
    
    // Handle main option checkboxes
    $('#hasDepartmentsCheck, #hasSectionsCheck').on('change', function() {
        var hasDepts = $('#hasDepartmentsCheck').is(':checked');
        var hasSections = $('#hasSectionsCheck').is(':checked');
        
        // If departments are unchecked, uncheck all department checkboxes
        if (!hasDepts) {
            $('.department-checkbox').prop('checked', false);
        }
        
        // Update modal sections based on current state
                if (window.lastClassDetails) {
            updateModalSections(hasDepts, hasSections, window.lastClassDetails);
        }
    });
    
    // Handle department checkbox changes
    $(document).on('change', '.department-checkbox', function() {
        console.log('Department checkbox changed!');
        console.log('This checkbox value:', $(this).val());
        console.log('This checkbox checked:', $(this).is(':checked'));
        
        // Update modal sections when departments change
        if (window.lastClassDetails) {
            var hasDepts = $('#hasDepartmentsCheck').is(':checked');
            var hasSections = $('#hasSectionsCheck').is(':checked');
            var selectedDepts = $('.department-checkbox:checked').map(function() {
                return parseInt($(this).val());
            }).get();
            
            console.log('Department checkbox change - selectedDepts:', selectedDepts);
            
            // Update visibility based on selected departments
            if (hasDepts && !hasSections) {
                // Only departments checked - update subjects visibility
                updateDepartmentSubjectsVisibility(selectedDepts);
            } else if (hasDepts && hasSections) {
                // Both checked - update both sections and subjects visibility
                updateDepartmentSectionsVisibility(selectedDepts);
                updateDepartmentSubjectsVisibility(selectedDepts);
            }
        }
    });
    
    // Handle form submission
    $('#editClassStructureForm').on('submit', function(e) {
        e.preventDefault();
        
        var hasDepartments = $('#hasDepartmentsCheck').is(':checked');
        var hasSections = $('#hasSectionsCheck').is(':checked');
        var selectedDepartments = $('.department-checkbox:checked').map(function() {
            return parseInt($(this).val());
        }).get();
        
        var selectedSections = [];
        var selectedSubjects = [];
        
        // Gather selected sections
        $('.section-checkbox:checked').each(function() {
            var sectionId = parseInt($(this).val());
            var deptId = $(this).data('department-id');
            var departmentId = (deptId === 'global') ? null : deptId;
            selectedSections.push({id: sectionId, department_id: departmentId});
        });
        
        // Gather selected subjects
        $('.subject-checkbox:checked').each(function() {
            var subjectId = parseInt($(this).val());
            var deptId = $(this).data('department-id');
            var departmentId = (deptId === 'global') ? null : deptId;
            selectedSubjects.push({id: subjectId, department_id: departmentId});
        });
        
        console.log('Form submission - hasDepartments:', hasDepartments);
        console.log('Form submission - hasSections:', hasSections);
        console.log('Form submission - selectedDepartments:', selectedDepartments);
        console.log('Form submission - selectedSections:', selectedSections);
        console.log('Form submission - selectedSubjects:', selectedSubjects);
        
        // Save all changes
        saveClassStructure(hasDepartments, hasSections, selectedDepartments, selectedSections, selectedSubjects);
    });
    
    function saveClassStructure(hasDepartments, hasSections, selectedDepartments, selectedSections, selectedSubjects) {
        var promises = [];
        
        // Get current assignments for comparison
        var currentDepartments = window.lastClassDetails.departments || [];
        var currentSections = window.lastClassDetails.sections || [];
        var currentSubjects = window.lastClassDetails.subjects || [];
        
        console.log('saveClassStructure - currentDepartments:', currentDepartments);
        console.log('saveClassStructure - currentSections:', currentSections);
        console.log('saveClassStructure - currentSubjects:', currentSubjects);
        
        // Handle departments
        if (hasDepartments) {
            // Add new departments
            selectedDepartments.forEach(function(deptId) {
                if (!currentDepartments.some(d => d.id == deptId)) {
                    promises.push($.post('../api/assign_department_to_class.php', {
                        class_id: classId,
                        department_id: deptId,
                        action: 'add'
                    }));
                }
            });
            
            // Remove unselected departments
            currentDepartments.forEach(function(dept) {
                if (!selectedDepartments.includes(dept.id)) {
                    promises.push($.post('../api/assign_department_to_class.php', {
                        class_id: classId,
                        department_id: dept.id,
                        action: 'remove'
                    }));
                }
            });
            } else {
            // Remove all departments if departments are unchecked
            currentDepartments.forEach(function(dept) {
                promises.push($.post('../api/assign_department_to_class.php', {
                    class_id: classId,
                    department_id: dept.id,
                    action: 'remove'
                }));
            });
        }
        
        // Handle sections based on hasSections flag
        if (hasSections) {
            var currentSectionsMap = {};
            currentSections.forEach(function(section) {
                var key = section.id + '_' + (section.department_id || 'global');
                currentSectionsMap[key] = true;
            });
            
            var selectedSectionsMap = {};
            selectedSections.forEach(function(section) {
                var key = section.id + '_' + (section.department_id || 'global');
                selectedSectionsMap[key] = true;
            });
            
            // Add new sections
            selectedSections.forEach(function(section) {
                var key = section.id + '_' + (section.department_id || 'global');
                if (!currentSectionsMap[key]) {
                    promises.push($.post('../api/assign_sections_to_class.php', {
                        class_id: classId,
                        section_id: section.id,
                        department_id: section.department_id,
                        action: 'add'
                    }));
                }
            });
            
            // Remove unselected sections
            currentSections.forEach(function(section) {
                var key = section.id + '_' + (section.department_id || 'global');
                if (!selectedSectionsMap[key]) {
                    promises.push($.post('../api/assign_sections_to_class.php', {
                class_id: classId,
                        section_id: section.id,
                        department_id: section.department_id,
                        action: 'remove'
                    }));
                }
            });
        } else {
            // Remove all sections if sections are unchecked
            currentSections.forEach(function(section) {
                promises.push($.post('../api/assign_sections_to_class.php', {
                    class_id: classId,
                    section_id: section.id,
                    department_id: section.department_id,
                    action: 'remove'
                }));
            });
        }
        
        // Handle subjects (always present, but logic depends on departments)
        var currentSubjectsMap = {};
        currentSubjects.forEach(function(subject) {
            var key = subject.subject_id + '_' + (subject.department_id || 'global');
            currentSubjectsMap[key] = true;
        });
        
        var selectedSubjectsMap = {};
        selectedSubjects.forEach(function(subject) {
            var key = subject.id + '_' + (subject.department_id || 'global');
            selectedSubjectsMap[key] = true;
        });
        
        // Add new subjects
        selectedSubjects.forEach(function(subject) {
            var key = subject.id + '_' + (subject.department_id || 'global');
            if (!currentSubjectsMap[key]) {
                promises.push($.post('../api/assign_subject_to_class.php', {
                    class_id: classId,
                    subject_id: subject.id,
                    department_id: subject.department_id,
                action: 'add'
            }));
            }
        });
        
        // Remove unselected subjects
        currentSubjects.forEach(function(subject) {
            var key = subject.subject_id + '_' + (subject.department_id || 'global');
            if (!selectedSubjectsMap[key]) {
                promises.push($.post('../api/assign_subject_to_class.php', {
                class_id: classId,
                    subject_id: subject.subject_id,
                    department_id: subject.department_id,
                action: 'remove'
            }));
            }
        });
        
        // Execute all promises
        Promise.all(promises).then(function() {
            $('#editClassStructureModal').modal('hide');
            loadClassDetails();
            alert('Class structure updated successfully!');
        }).catch(function() {
            alert('Some changes failed to save. Please try again.');
        });
    }
});

function renderDepartments(departments) {
    var html = '';
    departments.forEach(function(dept) {
        html += `<li class="list-group-item">
            <a href="#" class="toggle-dept d-flex align-items-center text-decoration-none" data-id="${dept.id}">
                <i class="fas fa-chevron-down me-2 toggle-icon" style="transition: transform 0.3s ease;"></i>
                <span>${dept.name}</span>
            </a>
            <ul class="subjects-toggle list-group mt-2" id="subjectsToggle_${dept.id}" style="display:none;"></ul>
        </li>`;
    });
    $('#departmentsList').html(html);
}

function renderSubjects(subjects, teachers, departments) {
    var html = '';
    var subjectsWithDepts = subjects.filter(s => s.department_id && s.department_id !== null && s.department_id !== 0);
    var independentSubjects = subjects.filter(s => !s.department_id || s.department_id === null || s.department_id === 0);
    
    // Show subjects with departments first (if any)
    if (subjectsWithDepts.length > 0) {
        // Group by department using rowspan
        var deptGroups = {};
        subjectsWithDepts.forEach(function(subject) {
            if (!deptGroups[subject.department_id]) {
                deptGroups[subject.department_id] = {
                    name: subject.department_name,
                    subjects: []
                };
            }
            deptGroups[subject.department_id].subjects.push(subject);
        });
        
        Object.values(deptGroups).forEach(function(deptGroup) {
            deptGroup.subjects.forEach(function(subject, index) {
                // Find all teachers for this subject and department
                var teacherNames = teachers
                    .filter(t => t.subject_id == subject.subject_id && String(t.department_id) == String(subject.department_id))
                    .map(t => t.first_name + ' ' + t.last_name);
                
                if (index === 0) {
                    // First subject for this department - include department name with rowspan
                    html += `<tr><td rowspan="${deptGroup.subjects.length}">${deptGroup.name}</td><td>${subject.name}</td><td>${teacherNames.join(', ')}</td></tr>`;
                } else {
                    // Additional subjects for this department - no department name
                    html += `<tr><td>${subject.name}</td><td>${teacherNames.join(', ')}</td></tr>`;
                }
            });
        });
        $('#subjectsList thead').html('<tr><th>Department</th><th>Subject</th><th>Teachers</th></tr>');
    }
    
    // Show independent subjects (if any)
    if (independentSubjects.length > 0) {
        independentSubjects.forEach(function(subject) {
            // Find all teachers for this subject (no department or department_id == 0)
            var teacherNames = teachers
                .filter(t => t.subject_id == subject.subject_id && (!t.department_id || t.department_id == 0))
                .map(t => t.first_name + ' ' + t.last_name);
            
            if (subjectsWithDepts.length > 0) {
                // If we have subjects with departments, add department column as empty
                html += `<tr><td></td><td>${subject.name}</td><td>${teacherNames.join(', ')}</td></tr>`;
            } else {
                // Only independent subjects - no department column
                html += `<tr><td>${subject.name}</td><td>${teacherNames.join(', ')}</td></tr>`;
            }
        });
    }
    
    // Show/hide department header based on whether we have subjects with departments
    if (subjectsWithDepts.length > 0) {
        $('#subjectsList thead').html('<tr><th>Department</th><th>Subject</th><th>Teachers</th></tr>');
    } else {
        $('#subjectsList thead').html('<tr><th>Subject</th><th>Teachers</th></tr>');
    }
    
    $('#subjectsList tbody').html(html);
}

function renderSectionsTable(sections, departments) {
    var html = '';
    var sectionsWithDepts = sections.filter(s => s.department_id && s.department_id !== null);
    var independentSections = sections.filter(s => !s.department_id || s.department_id === null);
    
    // Show sections with departments first (if any)
    if (sectionsWithDepts.length > 0) {
        // Group by department using rowspan
        var deptGroups = {};
        sectionsWithDepts.forEach(function(section) {
            if (!deptGroups[section.department_id]) {
                deptGroups[section.department_id] = {
                    name: section.department_name,
                    sections: []
                };
            }
            deptGroups[section.department_id].sections.push(section);
        });
        
        Object.values(deptGroups).forEach(function(deptGroup) {
            deptGroup.sections.forEach(function(section, index) {
                    if (index === 0) {
                        // First section for this department - include department name with rowspan
                    html += `<tr><td rowspan="${deptGroup.sections.length}">${deptGroup.name}</td><td>${section.name}</td></tr>`;
                    } else {
                        // Additional sections for this department - no department name
                        html += `<tr><td>${section.name}</td></tr>`;
                    }
                });
        });
        $('#sectionsDeptHeader').show();
    }
    
    // Show independent sections (if any)
    if (independentSections.length > 0) {
        independentSections.forEach(function(section) {
            if (sectionsWithDepts.length > 0) {
                // If we have sections with departments, add department column as empty
                html += `<tr><td></td><td>${section.name}</td></tr>`;
    } else {
                // Only independent sections - no department column
            html += `<tr><td>${section.name}</td></tr>`;
            }
        });
    }
    
    // Show/hide department header based on whether we have sections with departments
    if (sectionsWithDepts.length > 0) {
        $('#sectionsDeptHeader').show();
    } else {
        $('#sectionsDeptHeader').hide();
    }
    
    $('#sectionsList tbody').html(html);
} 