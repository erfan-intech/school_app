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
                // Departments section - check for real departments (id != 0)
                var realDepartments = res.departments.filter(d => d.id != 0);
                if (realDepartments.length > 0) {
                    console.log('Real departments found:', realDepartments); // Debug
                    renderDepartments(realDepartments);
                    $('#departmentsSection').show();
                    $('#subjectsSection').hide();
                    // Replace Edit button with Add Subjects
                    $('#departmentsActionBtnContainer').html('<button class="btn btn-sm btn-outline-primary ms-2" id="addSubjectsBtn">Add/Remove Subjects</button>');
                } else {
                    $('#departmentsSection').hide();
                    renderSubjects(res.subjects, res.teachers, res.departments);
                    $('#subjectsSection').show();
                    // Change the header to "Global Subjects" when no real departments
                    $('#subjectsSection h4').html('Global Subjects <button class="btn btn-sm btn-outline-primary ms-2" id="editSubjectsBtn">Edit</button>');
                }
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
    // Edit class (departments) modal logic
    $('#editClassBtn').click(function() {
        $.get('../api/get_departments.php', function(deptRes) {
            if (deptRes.success) {
                $.get('../api/get_class_details.php', {class_id: classId}, function(res) {
                    if (res.success) {
                        var assigned = res.departments.map(d => d.id.toString());
                        var checkboxes = '';
                        deptRes.data.forEach(function(d) {
                            var checked = assigned.includes(d.id.toString()) ? 'checked' : '';
                            checkboxes += `<div class='form-check'><input class='form-check-input' type='checkbox' value='${d.id}' id='edit_class_dept_${d.id}' name='departments[]' ${checked}><label class='form-check-label' for='edit_class_dept_${d.id}'>${d.name}</label></div>`;
                        });
                        $('#editClassDepartmentsCheckboxes').html(checkboxes);
                        $('#editClassModal').modal('show');
                    }
                });
            }
        });
    });
    // Save class departments
    $('#editClassForm').submit(function(e) {
        e.preventDefault();
        var selectedDepts = [];
        $('#editClassDepartmentsCheckboxes input[type=checkbox]:checked').each(function() {
            selectedDepts.push($(this).val());
        });
        $.get('../api/get_departments.php', function(deptRes) {
            if (deptRes.success) {
                var allDeptIds = deptRes.data.map(d => d.id.toString());
                // Remove unselected
                allDeptIds.forEach(function(id) {
                    if (!selectedDepts.includes(id)) {
                        $.post('../api/assign_department_to_class.php', {class_id: classId, department_id: id, action: 'remove'});
                    }
                });
                // Add selected
                selectedDepts.forEach(function(id) {
                    $.post('../api/assign_department_to_class.php', {class_id: classId, department_id: id, action: 'add'});
                });
            }
        });
        setTimeout(function() {
            $('#editClassModal').modal('hide');
            loadClassDetails();
        }, 500);
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
    // Open edit modal for subjects
    $('#editSubjectsBtn').click(function() {
        $.get('../api/get_class_details.php', {class_id: classId}, function(res) {
            if (res.success) {
                var departments = res.departments;
                $.get('../api/get_subjects.php', function(subjRes) {
                    if (subjRes.success) {
                        var allSubjects = subjRes.data;
                        var html = '';
                        if (departments.length > 0) {
                            // Per-department subject assignment
                            departments.forEach(function(dept) {
                                html += `<div class='mb-3'><strong>${dept.name}</strong><div id='subjectsCheckboxes_dept_${dept.id}'></div></div>`;
                                // Assigned subjects for this department
                                var assigned = res.subjects.filter(s => parseInt(s.department_id) === parseInt(dept.id)).map(s => s.subject_id.toString());
                                var checkboxes = '';
                                allSubjects.forEach(function(s) {
                                    var checked = assigned.includes(s.id.toString()) ? 'checked' : '';
                                    checkboxes += `<div class='form-check d-inline-block me-3'><input class='form-check-input' type='checkbox' value='${s.id}' id='subj_${s.id}_dept_${dept.id}' name='subjects_dept_${dept.id}[]' ${checked}><label class='form-check-label' for='subj_${s.id}_dept_${dept.id}'>${s.name}</label></div>`;
                                });
                                setTimeout(function() { // Ensure DOM is ready
                                    $(`#subjectsCheckboxes_dept_${dept.id}`).html(checkboxes);
                                }, 0);
                            });
                        } else {
                            // No departments, assign globally
                            // Fetch assigned subject_ids for this class (no department)
                            var assigned = res.subjects.filter(s => !s.department_id).map(s => s.subject_id.toString());
                            var checkboxes = '';
                            allSubjects.forEach(function(s) {
                                checkboxes += `<div class='form-check d-inline-block me-3'><input class='form-check-input' type='checkbox' value='${s.id}' id='subj_${s.id}' name='subjects[]'><label class='form-check-label' for='subj_${s.id}'>${s.name}</label></div>`;
                            });
                            html += `<div class='mb-3'><strong>Subjects</strong><div id='subjectsCheckboxes'>${checkboxes}</div></div>`;
                            setTimeout(function() {
                                // Explicitly check assigned checkboxes
                                assigned.forEach(function(id) {
                                    $("#subjectsCheckboxes input[type=checkbox][value='"+id+"']").prop('checked', true);
                                });
                            }, 0);
                        }
                        $('#subjectsByDeptContainer').html(html);
                        $('#editSubjectsModal').modal('show');
                    }
                });
            }
        });
    });
    // Save subjects
    $('#editSubjectsForm').submit(function(e) {
        e.preventDefault();
        $.get('../api/get_class_details.php', {class_id: classId}, function(res) {
            if (res.success) {
                var departments = res.departments;
                $.get('../api/get_subjects.php', function(subjRes) {
                    if (subjRes.success) {
                        var allSubjects = subjRes.data.map(s => s.id.toString());
                        if (departments.length > 0) {
                            // Per-department
                            departments.forEach(function(dept) {
                                var selectedSubjs = [];
                                $(`#subjectsCheckboxes_dept_${dept.id} input[type=checkbox]:checked`).each(function() {
                                    selectedSubjs.push($(this).val());
                                });
                                // Remove unselected
                                allSubjects.forEach(function(id) {
                                    if (!selectedSubjs.includes(id)) {
                                        $.post('../api/assign_subject_to_class.php', {class_id: classId, subject_id: id, department_id: dept.id, action: 'remove'});
                                    }
                                });
                                // Add selected
                                selectedSubjs.forEach(function(id) {
                                    $.post('../api/assign_subject_to_class.php', {class_id: classId, subject_id: id, department_id: dept.id, action: 'add'});
                                });
                            });
                        } else {
                            // Global
                            var selectedSubjs = [];
                            $('#subjectsCheckboxes input[type=checkbox]:checked').each(function() {
                                selectedSubjs.push($(this).val());
                            });
                            // Check for duplicates
                            var unique = new Set(selectedSubjs);
                            if (unique.size !== selectedSubjs.length) {
                                alert('Duplicate subjects selected. Please select each subject only once.');
                                return;
                            }
                            // Remove unselected
                            allSubjects.forEach(function(id) {
                                if (!selectedSubjs.includes(id)) {
                                    $.post('../api/assign_subject_to_class.php', {class_id: classId, subject_id: id, department_id: 0, action: 'remove'});
                                }
                            });
                            // Add selected (prevent duplicate requests)
                            var added = new Set();
                            selectedSubjs.forEach(function(id) {
                                if (!added.has(id)) {
                                    $.post('../api/assign_subject_to_class.php', {class_id: classId, subject_id: id, department_id: 0, action: 'add'});
                                    added.add(id);
                                }
                            });
                        }
                        setTimeout(function() {
                            $('#editSubjectsModal').modal('hide');
                            loadClassDetails();
                        }, 500);
                    }
                });
            }
        });
    });
    // Add Subjects modal logic
    $(document).on('click', '#addSubjectsBtn', function() {
        $.get('../api/get_class_details.php', {class_id: classId}, function(res) {
            if (res.success) {
                var departments = res.departments;
                $.get('../api/get_subjects.php', function(subjRes) {
                    if (subjRes.success) {
                        var allSubjects = subjRes.data;
                        var html = '';
                        var assignedMap = {};
                        departments.forEach(function(dept) {
                            assignedMap[dept.id] = res.subjects.filter(s => parseInt(s.department_id) === parseInt(dept.id)).map(s => s.subject_id.toString());
                        });
                        departments.forEach(function(dept) {
                            html += `<div class='mb-3'><strong>${dept.name}</strong><div id='assignSubjectsCheckboxes_dept_${dept.id}'></div></div>`;
                            var checkboxes = '';
                            allSubjects.forEach(function(s) {
                                checkboxes += `<div class='form-check d-inline-block me-3'><input class='form-check-input assign-dept-subj-checkbox' type='checkbox' value='${s.id}' data-dept='${dept.id}' id='assign_subj_${s.id}_dept_${dept.id}' name='assign_subjects_dept_${dept.id}[]'><label class='form-check-label' for='assign_subj_${s.id}_dept_${dept.id}'>${s.name}</label></div>`;
                            });
                            html += `<div class='mb-2' id='assignSubjectsCheckboxes_dept_${dept.id}_container'>${checkboxes}</div>`;
                        });
                        $('#assignSubjectsModalBody').html(html);
                        // Explicitly check assigned checkboxes for each department
                        departments.forEach(function(dept) {
                            var assigned = assignedMap[dept.id] || [];
                            assigned.forEach(function(id) {
                                $(`#assignSubjectsCheckboxes_dept_${dept.id}_container input[type=checkbox][value='${id}']`).prop('checked', true);
                            });
                        });
                        $('#assignSubjectsModal').modal('show');
                    }
                });
            }
        });
    });
    // Save subjects for all departments
    $('#assignSubjectsForm').submit(function(e) {
        e.preventDefault();
        $.get('../api/get_class_details.php', {class_id: classId}, function(res) {
            if (res.success) {
                var departments = res.departments;
                $.get('../api/get_subjects.php', function(subjRes) {
                    if (subjRes.success) {
                        var allSubjects = subjRes.data.map(s => s.id.toString());
                        var duplicateFound = false;
                        departments.forEach(function(dept) {
                            var selectedSubjs = [];
                            $(`#assignSubjectsCheckboxes_dept_${dept.id}_container input[type=checkbox]:checked`).each(function() {
                                selectedSubjs.push($(this).val());
                            });
                            // Check for duplicates in this department
                            var unique = new Set(selectedSubjs);
                            if (unique.size !== selectedSubjs.length) {
                                duplicateFound = true;
                            }
                        });
                        if (duplicateFound) {
                            alert('Duplicate subjects selected for a department. Please select each subject only once per department.');
                            return;
                        }
                        departments.forEach(function(dept) {
                            var selectedSubjs = [];
                            $(`#assignSubjectsCheckboxes_dept_${dept.id}_container input[type=checkbox]:checked`).each(function() {
                                selectedSubjs.push($(this).val());
                            });
                            // Remove unselected
                            allSubjects.forEach(function(id) {
                                if (!selectedSubjs.includes(id)) {
                                    $.post('../api/assign_subject_to_class.php', {class_id: classId, subject_id: id, department_id: dept.id, action: 'remove'});
                                }
                            });
                            // Add selected
                            selectedSubjs.forEach(function(id) {
                                $.post('../api/assign_subject_to_class.php', {class_id: classId, subject_id: id, department_id: dept.id, action: 'add'});
                            });
                        });
                    }
                });
            }
        });
        setTimeout(function() {
            $('#assignSubjectsModal').modal('hide');
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
                                $.post('../api/assign_teacher_to_class.php', {class_id: classId, teacher_id: id, action: 'add'});
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
            function renderSubjectsForDepartments() {
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
            }
            // Initial render
            if (departments.length > 0) renderSubjectsForDepartments();
            // Toggle subjects list on department checkbox change
            $(document).off('change', '.edit-teacher-dept-checkbox').on('change', '.edit-teacher-dept-checkbox', function() {
                renderSubjectsForDepartments();
            });
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
    // Submit edit teacher subjects form
    $('#editTeacherSubjectsForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        var classId = $('#classDetailsApp').data('class-id');
        var teacherId = $(this).data('teacher-id');
        var departments = $('.edit-teacher-dept-checkbox:checked').map(function() { return $(this).val(); }).get();
        var subjects = $('.edit-teacher-subj-checkbox:checked').map(function() { return $(this).val(); }).get();
        $.post('../api/edit_teacher_subjects.php', {
            class_id: classId,
            teacher_id: teacherId,
            departments: departments,
            subjects: subjects
        }, function(res) {
            if (res.success) {
                $('#editTeacherSubjectsModal').modal('hide');
                loadClassDetails();
            } else {
                alert(res.message || 'Failed to update teacher assignments.');
            }
        }, 'json');
    });
    // Sections Modal Logic
    $('#editSectionsBtn').on('click', function() {
        $('#editSectionsModal').modal('show');
        $.get('../api/get_sections.php', function(sectionRes) {
            if (sectionRes.success) {
                // Get class details (departments and assigned sections)
                function renderSectionsModal(classDetails) {
                    var departments = (classDetails.departments || []).filter(d => d.id != 0);
                    var assignedSections = classDetails.sections || [];
                    var assignedMap = {};
                    
                    assignedSections.forEach(function(s) {
                        var deptId = String(s.department_id || 0);
                        if (!assignedMap[deptId]) assignedMap[deptId] = [];
                        assignedMap[deptId].push(String(s.id));
                    });
                    
                    var html = '';
                    if (departments.length > 0) {
                        // Grouped by department
                        departments.forEach(function(dept) {
                            html += `<div class='mb-2'><strong>${dept.name}</strong></div>`;
                            sectionRes.data.forEach(function(section) {
                                if (section.id == 0) return;
                                var checked = (assignedMap[String(dept.id)] || []).includes(String(section.id)) ? 'checked' : '';

                                html += `<div class='form-check ms-3'>
                                    <input class='form-check-input section-checkbox' type='checkbox' value='${section.id}' data-department-id='${dept.id}' id='sectionCheck${section.id}_${dept.id}' name='sections_${dept.id}[]' ${checked}>
                                    <label class='form-check-label' for='sectionCheck${section.id}_${dept.id}'>${section.name}</label>
                                </div>`;
                            });
                        });
                    } else {
                        // Flat list
                        var assignedIds = assignedSections.map(s => String(s.id));

                        sectionRes.data.forEach(function(section) {
                            if (section.id == 0) return;
                            var checked = assignedIds.includes(String(section.id)) ? 'checked' : '';

                            html += `<div class='form-check'>
                                <input class='form-check-input section-checkbox' type='checkbox' value='${section.id}' data-department-id='0' id='sectionCheck${section.id}' name='sections[]' ${checked}>
                                <label class='form-check-label' for='sectionCheck${section.id}'>${section.name}</label>
                            </div>`;
                        });
                    }
                    $('#editClassSectionsCheckboxes').html(html);
                }
                // Use lastClassDetails if available, else fetch
                if (window.lastClassDetails) {
                    renderSectionsModal(window.lastClassDetails);
                } else {
                    $.get('../api/get_class_details.php', { class_id: classId }, function(res) {
                        if (res.success) renderSectionsModal(res);
                    });
                }
            } else {
                $('#editClassSectionsCheckboxes').html('<div class="alert alert-danger">Failed to load sections.</div>');
            }
        });
    });
    $('#editSectionsForm').on('submit', function(e) {
        e.preventDefault();
        var selected = [];
        var prevAssigned = [];
        if (window.lastClassDetails && Array.isArray(window.lastClassDetails.sections)) {
            prevAssigned = window.lastClassDetails.sections.map(s => ({id: String(s.id), department_id: String(s.department_id || 0)}));
        }
        // Gather selected department-section pairs
        $('#editClassSectionsCheckboxes .section-checkbox:checked').each(function() {
            selected.push({id: $(this).val(), department_id: $(this).data('department-id').toString()});
        });
        // Find pairs to add and remove
        function pairKey(pair) { return pair.id + '_' + pair.department_id; }
        var prevKeys = prevAssigned.map(pairKey);
        var selectedKeys = selected.map(pairKey);
        var toAdd = selected.filter(pair => !prevKeys.includes(pairKey(pair)));
        var toRemove = prevAssigned.filter(pair => !selectedKeys.includes(pairKey(pair)));
        var requests = [];
        toAdd.forEach(function(pair) {
            requests.push($.post('../api/assign_sections_to_class.php', {
                class_id: classId,
                section_id: pair.id,
                department_id: pair.department_id,
                action: 'add'
            }));
        });
        toRemove.forEach(function(pair) {
            requests.push($.post('../api/assign_sections_to_class.php', {
                class_id: classId,
                section_id: pair.id,
                department_id: pair.department_id,
                action: 'remove'
            }));
        });
        $.when.apply($, requests).done(function() {
            $('#editSectionsModal').modal('hide');
            loadClassDetails();
        }).fail(function() {
            alert('Failed to update sections.');
        });
    });
    // TODO: Populate department, subject, and teacher selects with available options (AJAX or server-side)
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
    // Only treat as 'has departments' if there is at least one department with id != 0
    var hasRealDepartments = departments && departments.length > 0 && departments.some(d => d.id != 0);
    var html = '';
    if (hasRealDepartments) {
        // For each department (id != 0), show its subjects and assigned teachers
        departments.filter(d => d.id != 0).forEach(function(dept) {
            subjects.filter(s => String(s.department_id) == String(dept.id)).forEach(function(subj) {
                // Find all teachers for this subject and department
                var teacherNames = teachers
                    .filter(t => t.subject_id == subj.subject_id && String(t.department_id) == String(dept.id))
                    .map(t => t.first_name + ' ' + t.last_name);
                html += `<tr>
                    <td>${dept.name}</td>
                    <td>${subj.name}</td>
                    <td>${teacherNames.join(', ')}</td>
                </tr>`;
            });
        });
        $('#subjectsList thead').html('<tr><th>Department</th><th>Subject</th><th>Teachers</th></tr>');
    } else {
        subjects.forEach(function(subj) {
            // Find all teachers for this subject (no department or department_id == 0)
            var teacherNames = teachers
                .filter(t => t.subject_id == subj.subject_id && (!t.department_id || t.department_id == 0))
                .map(t => t.first_name + ' ' + t.last_name);
            html += `<tr>
                <td>${subj.name}</td>
                <td>${teacherNames.join(', ')}</td>
            </tr>`;
        });
        $('#subjectsList thead').html('<tr><th>Subject</th><th>Teachers</th></tr>');
    }
    $('#subjectsList tbody').html(html);
}

function renderSectionsTable(sections, departments) {
    var hasRealDepartments = departments && departments.length > 0 && departments.some(d => d.id != 0);
    var html = '';
    if (hasRealDepartments) {
        // Group by department using rowspan
        departments.filter(d => d.id != 0).forEach(function(dept) {
            var deptSections = sections.filter(s => String(s.department_id) === String(dept.id));
            if (deptSections.length > 0) {
                deptSections.forEach(function(section, index) {
                    if (index === 0) {
                        // First section for this department - include department name with rowspan
                        html += `<tr><td rowspan="${deptSections.length}">${dept.name}</td><td>${section.name}</td></tr>`;
                    } else {
                        // Additional sections for this department - no department name
                        html += `<tr><td>${section.name}</td></tr>`;
                    }
                });
            }
        });
        $('#sectionsDeptHeader').show();
    } else {
        // Flat list - only one column for section names
        sections.forEach(function(section) {
            html += `<tr><td>${section.name}</td></tr>`;
        });
        $('#sectionsDeptHeader').hide();
    }
    $('#sectionsList tbody').html(html);
} 