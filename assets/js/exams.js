$(document).ready(function() {
    // Load data on page load
    loadExamTypesAndDropdowns();
    loadExams();
    
    // Handle form submissions
    $('#addExamForm').on('submit', function(e) {
        e.preventDefault();
        addExam();
    });
    
    $('#addExamTypeForm').on('submit', function(e) {
        e.preventDefault();
        addExamType();
    });
    
    // Handle class change to update departments and sections
    $('#examClass').on('change', function() {
        updateDepartmentsByClass();
        updateSectionsByClass();
        // Delay preview update to allow dropdowns to populate
        setTimeout(function() {
            updateExamNamePreview();
        }, 300);
    });
    
    // Handle department change to update sections
    $('#examDepartment').on('change', function() {
        updateSectionsByDepartment();
        // Delay preview update to allow sections dropdown to populate
        setTimeout(function() {
            updateExamNamePreview();
        }, 300);
    });
    
    // Handle section change to update exam name preview
    $('#examSection').on('change', function() {
        updateExamNamePreview();
    });
    
    // Handle exam type change to update exam name preview
    $('#examType').on('change', function() {
        updateExamNamePreview();
    });
    
    // Handle academic year change to update exam name preview
    $('#examYear').on('input', function() {
        updateExamNamePreview();
    });
    
    // Handle exam types table collapse toggle manually with smooth animation
    $('#examTypesToggleBtn').on('click', function(e) {
        e.preventDefault();
        var $collapse = $('#examTypesTableCollapse');
        var $icon = $(this).find('i');
        
        if ($collapse.hasClass('show')) {
            // Collapse the table with smooth animation
            $collapse.css('height', $collapse[0].scrollHeight + 'px');
            setTimeout(function() {
                $collapse.css('height', '0px');
                $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            }, 10);
            setTimeout(function() {
                $collapse.removeClass('show');
            }, 300);
        } else {
            // Expand the table with smooth animation
            $collapse.addClass('show');
            $collapse.css('height', '0px');
            setTimeout(function() {
                $collapse.css('height', $collapse[0].scrollHeight + 'px');
                $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            }, 10);
            setTimeout(function() {
                $collapse.css('height', 'auto');
            }, 300);
        }
    });
    
    // Initialize the page
    init();
});

function loadExamTypesAndDropdowns() {
    $.get('../api/get_exam_types.php', function(response) {
        if (response.success) {
            // Render the table
            renderExamTypesTable(response.data);
            
            // Populate the dropdown
            var typeSelect = $('#examType');
            typeSelect.empty().append('<option value="">Select Exam Type</option>');
            response.data.forEach(function(type) {
                typeSelect.append(`<option value="${type.id}">${type.type_name}</option>`);
            });
        } else {
            console.error('Failed to load exam types:', response.message);
        }
    });
}

function loadExamTypes() {
    $.get('../api/get_exam_types.php', function(response) {
        if (response.success) {
            renderExamTypesTable(response.data);
        } else {
            console.error('Failed to load exam types:', response.message);
        }
    });
}

function renderExamTypesTable(examTypes) {
    var tbody = $('#examTypesTable tbody');
    tbody.empty();
    
    if (examTypes.length === 0) {
        tbody.append('<tr><td colspan="4" class="text-center text-muted">No exam types found</td></tr>');
        return;
    }
    
    examTypes.forEach(function(type) {
        var row = `
            <tr>
                <td>${type.id}</td>
                <td>${type.type_name}</td>
                <td>${type.description || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-primary edit-exam-type-btn me-1" data-id="${type.id}" data-name="${type.type_name}" data-description="${type.description || ''}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-exam-type-btn" data-id="${type.id}" data-name="${type.type_name}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Add event listeners
    $('.edit-exam-type-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var description = $(this).data('description');
        
        // Populate edit modal
        $('#editExamTypeId').val(id);
        $('#editExamTypeName').val(name);
        $('#editExamTypeDescription').val(description);
        $('#editExamTypeModal').modal('show');
    });
    
    $('.delete-exam-type-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        if (confirm(`Are you sure you want to delete the exam type "${name}"?`)) {
            deleteExamType(id);
        }
    });
}

function loadExams() {
    $.get('../api/get_exams.php', function(response) {
        if (response.success) {
            renderExamsTable(response.data);
        } else {
            console.error('Failed to load exams:', response.message);
        }
    });
}

function renderExamsTable(exams) {
    var tbody = $('#examsTable tbody');
    tbody.empty();
    
    if (exams.length === 0) {
        tbody.append('<tr><td colspan="9" class="text-center text-muted">No exams found</td></tr>');
        return;
    }
    
    exams.forEach(function(exam, index) {
        var statusText = '';
        if (exam.completed_subjects > 0) {
            statusText = `<span class="badge bg-success">${exam.completed_subjects} Completed</span>`;
        } else if (exam.ongoing_subjects > 0) {
            statusText = `<span class="badge bg-info">${exam.ongoing_subjects} Ongoing</span>`;
        } else if (exam.scheduled_subjects > 0) {
            statusText = `<span class="badge bg-warning">${exam.scheduled_subjects} Scheduled</span>`;
        } else {
            statusText = '<span class="badge bg-secondary">No Subjects</span>';
        }
        
        // Determine department display text
        var departmentDisplay = '-';
        if (exam.class_has_departments > 0) {
            if (exam.department_name) {
                departmentDisplay = exam.department_name;
            } else {
                departmentDisplay = 'All Department';
            }
        }
        
        // Determine section display text
        var sectionDisplay = '-';
        if (exam.class_has_sections > 0) {
            if (exam.section_name) {
                sectionDisplay = exam.section_name;
            } else {
                sectionDisplay = 'All Section';
            }
        }
        
        var row = `
            <tr>
                <td>${index + 1}</td>
                <td>${exam.exam_name}</td>
                <td>${exam.exam_type_name || '-'}</td>
                <td>${exam.class_name}</td>
                <td>${departmentDisplay}</td>
                <td>${sectionDisplay}</td>
                <td>${exam.subject_count} subjects</td>
                <td>${statusText}</td>
                <td>
                    <a href="exam_details.php?exam_id=${exam.id}" class="btn btn-sm btn-info me-1">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <button class="btn btn-sm btn-primary edit-exam-btn me-1" data-id="${exam.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-exam-btn" data-id="${exam.id}" data-name="${exam.exam_name}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Add event listeners
    $('.edit-exam-btn').on('click', function() {
        var examId = $(this).data('id');
        loadEditExamModal(examId);
    });
    
    $('.delete-exam-btn').on('click', function() {
        var examId = $(this).data('id');
        var examName = $(this).data('name');
        
        if (confirm(`Are you sure you want to delete the exam "${examName}"?`)) {
            deleteExam(examId);
        }
    });
}

function addExam() {
    // Get form data
    var formData = new FormData($('#addExamForm')[0]);
    
    // Get the selected values for dynamic exam name generation
    var classId = $('#examClass').val();
    var departmentId = $('#examDepartment').val();
    var sectionId = $('#examSection').val();
    var examTypeId = $('#examType').val();
    var academicYear = $('#examYear').val();
    
    // Validate required fields
    if (!classId || !examTypeId || !academicYear) {
        alert('Please fill in all required fields (Class, Exam Type, and Academic Year)');
        return;
    }
    
    // Get the text values for exam name generation
    var className = $('#examClass option:selected').text();
    var examTypeName = $('#examType option:selected').text();
    
    // Check if department dropdown is enabled and has options (excluding the first "Select" option)
    var deptSelect = $('#examDepartment');
    var hasDepartments = !deptSelect.prop('disabled') && deptSelect.find('option').length > 1;
    
    // Check if section dropdown is enabled and has options (excluding the first "Select" option)
    var secSelect = $('#examSection');
    var hasSections = !secSelect.prop('disabled') && secSelect.find('option').length > 1;
    
    // Generate exam name dynamically
    var examName = className;
    
    // Handle department part
    if (hasDepartments) {
        if (departmentId) {
            var departmentName = $('#examDepartment option:selected').text();
            examName += '_' + departmentName;
        } else {
            var deptDefaultText = deptSelect.find('option:first').text();
            examName += '_' + deptDefaultText;
        }
    }
    
    // Handle section part
    if (hasSections) {
        if (sectionId) {
            var sectionName = $('#examSection option:selected').text();
            examName += '_' + sectionName;
        } else {
            var secDefaultText = secSelect.find('option:first').text();
            examName += '_' + secDefaultText;
        }
    }
    
    examName += '_' + examTypeName + '_' + academicYear;
    
    // Add the generated exam name to form data
    formData.set('exam_name', examName);
    
    // Convert FormData to object for AJAX
    var data = {};
    formData.forEach(function(value, key) {
        data[key] = value;
    });
    
    $.post('../api/add_exam.php', data, function(response) {
        if (response.success) {
            $('#addExamForm')[0].reset();
            loadExams();
            alert('Exam created successfully!');
        } else {
            alert('Failed to create exam: ' + response.message);
        }
    }).fail(function() {
        alert('Failed to create exam. Please try again.');
    });
}

function addExamType() {
    var formData = $('#addExamTypeForm').serialize();
    
    $.post('../api/add_exam_type.php', formData, function(response) {
        if (response.success) {
            $('#addExamTypeForm')[0].reset();
            loadExamTypesAndDropdowns();
            alert('Exam type added successfully!');
        } else {
            alert('Failed to add exam type: ' + response.message);
        }
    }).fail(function() {
        alert('Failed to add exam type. Please try again.');
    });
}

function updateExamType() {
    var formData = $('#editExamTypeForm').serialize();
    
    $.post('../api/update_exam_type.php', formData, function(response) {
        
        if (response.success) {
            loadExamTypesAndDropdowns();
            alert('Exam type updated successfully!');
        } else {
            var errorMessage = response.message || 'Unknown error occurred';
            alert('Failed to update exam type: ' + errorMessage);
        }
    }).fail(function(xhr, status, error) {
        alert('Failed to update exam type. Please try again.');
    });
}

function deleteExamType(id) {
    $.post('../api/delete_exam_type.php', {id: id}, function(response) {
        if (response.success) {
            loadExamTypesAndDropdowns();
            alert('Exam type deleted successfully!');
        } else {
            alert('Failed to delete exam type: ' + response.message);
        }
    }).fail(function() {
        alert('Failed to delete exam type. Please try again.');
    });
}

function deleteExam(id) {
    $.post('../api/delete_exam.php', {exam_id: id}, function(response) {
        if (response.success) {
            loadExams();
            alert('Exam deleted successfully!');
        } else {
            alert('Failed to delete exam: ' + response.message);
        }
    }).fail(function() {
        alert('Failed to delete exam. Please try again.');
    });
}

function updateDepartmentsByClass() {
    var classId = $('#examClass').val();
    var deptSelect = $('#examDepartment');
    
    if (!classId) {
        deptSelect.empty().append('<option value="">All Department</option>');
        deptSelect.prop('disabled', true);
        return;
    }
    
    $.get('../api/get_departments_by_class.php', {class_id: classId}, function(response) {
        if (response.success) {
            deptSelect.empty();
            if (response.data.length === 0) {
                deptSelect.append('<option value="">No departments available for this class</option>');
                deptSelect.prop('disabled', true);
            } else {
                deptSelect.append('<option value="">All Department</option>');
                response.data.forEach(function(dept) {
                    deptSelect.append(`<option value="${dept.id}">${dept.name}</option>`);
                });
                deptSelect.prop('disabled', false);
            }
        }
    });
}

function updateSectionsByClass() {
    var classId = $('#examClass').val();
    var secSelect = $('#examSection');
    
    if (!classId) {
        secSelect.empty().append('<option value="">All Section</option>');
        secSelect.prop('disabled', true);
        return;
    }
    
    $.get('../api/get_sections_by_class.php', {class_id: classId}, function(response) {
        if (response.success) {
            secSelect.empty();
            if (response.data.length === 0) {
                secSelect.append('<option value="">No sections available for this class</option>');
                secSelect.prop('disabled', true);
            } else {
                secSelect.append('<option value="">All Section</option>');
                response.data.forEach(function(sec) {
                    secSelect.append(`<option value="${sec.id}">${sec.name}</option>`);
                });
                secSelect.prop('disabled', false);
            }
        }
    });
}

function updateSectionsByDepartment() {
    var classId = $('#examClass').val();
    var deptId = $('#examDepartment').val();
    var secSelect = $('#examSection');
    
    if (!classId) {
        secSelect.empty().append('<option value="">All Section</option>');
        secSelect.prop('disabled', true);
        return;
    }
    
    // If no department is selected, load all sections for the class
    if (!deptId) {
        $.get('../api/get_sections_by_class.php', {class_id: classId}, function(response) {
            if (response.success) {
                secSelect.empty();
                if (response.data.length === 0) {
                    secSelect.append('<option value="">No sections available for this class</option>');
                    secSelect.prop('disabled', true);
                } else {
                    secSelect.append('<option value="">All Section</option>');
                    response.data.forEach(function(sec) {
                        secSelect.append(`<option value="${sec.id}">${sec.name}</option>`);
                    });
                    secSelect.prop('disabled', false);
                }
            }
        });
        return;
    }
    
    // If department is selected, load sections for that specific department
    $.get('../api/get_sections_by_class_dept.php', {class_id: classId, department_id: deptId}, function(response) {
        if (response.success) {
            secSelect.empty();
            if (response.data.length === 0) {
                secSelect.append('<option value="">No sections available for this department</option>');
                secSelect.prop('disabled', true);
            } else {
                secSelect.append('<option value="">All Section</option>');
                response.data.forEach(function(sec) {
                    secSelect.append(`<option value="${sec.id}">${sec.name}</option>`);
                });
                secSelect.prop('disabled', false);
            }
        }
    });
}

function loadExamTypeDropdowns() {
    $.get('../api/get_exam_types.php', function(response) {
        if (response.success) {
            var typeSelect = $('#examType');
            typeSelect.empty().append('<option value="">Select Exam Type</option>');
            response.data.forEach(function(type) {
                typeSelect.append(`<option value="${type.id}">${type.type_name}</option>`);
            });
        }
    });
}

function loadClassDropdowns() {
    $.get('../api/get_classes.php', function(response) {
        if (response.success) {
            var classSelect = $('#examClass');
            classSelect.empty().append('<option value="">Select Class</option>');
            response.data.forEach(function(cls) {
                classSelect.append(`<option value="${cls.id}">${cls.name}</option>`);
            });
            
            // Initially disable department and section dropdowns
            $('#examDepartment').prop('disabled', true);
            $('#examSection').prop('disabled', true);
        }
    });
}

function getStatusBadge(status) {
    var badges = {
        'draft': '<span class="badge bg-secondary">Draft</span>',
        'published': '<span class="badge bg-success">Published</span>',
        'completed': '<span class="badge bg-info">Completed</span>',
        'cancelled': '<span class="badge bg-danger">Cancelled</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function updateExamNamePreview() {
    var classId = $('#examClass').val();
    var departmentId = $('#examDepartment').val();
    var sectionId = $('#examSection').val();
    var examTypeId = $('#examType').val();
    var academicYear = $('#examYear').val();
    
    if (!classId || !examTypeId || !academicYear) {
        $('#examNamePreview').text('Please select Class, Exam Type, and Academic Year');
        return;
    }
    
    var className = $('#examClass option:selected').text();
    var examTypeName = $('#examType option:selected').text();
    
    // Check if dropdowns are enabled and have options
    var deptSelect = $('#examDepartment');
    var secSelect = $('#examSection');
    var hasDepartments = !deptSelect.prop('disabled') && deptSelect.find('option').length > 1;
    var hasSections = !secSelect.prop('disabled') && secSelect.find('option').length > 1;
    
    var examName = className;
    
    // Handle department part
    if (hasDepartments) {
        if (departmentId) {
            var departmentName = $('#examDepartment option:selected').text();
            examName += '_' + departmentName;
        } else {
            var deptDefaultText = deptSelect.find('option:first').text();
            examName += '_' + deptDefaultText;
        }
    }
    
    // Handle section part
    if (hasSections) {
        if (sectionId) {
            var sectionName = $('#examSection option:selected').text();
            examName += '_' + sectionName;
        } else {
            var secDefaultText = secSelect.find('option:first').text();
            examName += '_' + secDefaultText;
        }
    }
    
    examName += '_' + examTypeName + '_' + academicYear;
    $('#examNamePreview').text('Generated Exam Name: ' + examName);
}

function updateEditExamNamePreview() {
    var classId = $('#editExamClass').val();
    var departmentId = $('#editExamDepartment').val();
    var sectionId = $('#editExamSection').val();
    var examTypeId = $('#editExamType').val();
    var academicYear = $('#editExamYear').val();
    
    if (!classId || !examTypeId || !academicYear) {
        $('#editExamNamePreview').text('Please select Class, Exam Type, and Academic Year');
        return;
    }
    
    var className = $('#editExamClass option:selected').text();
    var examTypeName = $('#editExamType option:selected').text();
    
    // Check if dropdowns are enabled and have options
    var deptSelect = $('#editExamDepartment');
    var secSelect = $('#editExamSection');
    var hasDepartments = !deptSelect.prop('disabled') && deptSelect.find('option').length > 1;
    var hasSections = !secSelect.prop('disabled') && secSelect.find('option').length > 1;
    
    var examName = className;
    
    // Handle department part
    if (hasDepartments) {
        if (departmentId) {
            var departmentName = $('#editExamDepartment option:selected').text();
            examName += '_' + departmentName;
        } else {
            var deptDefaultText = deptSelect.find('option:first').text();
            examName += '_' + deptDefaultText;
        }
    }
    
    // Handle section part
    if (hasSections) {
        if (sectionId) {
            var sectionName = $('#editExamSection option:selected').text();
            examName += '_' + sectionName;
        } else {
            var secDefaultText = secSelect.find('option:first').text();
            examName += '_' + secDefaultText;
        }
    }
    
    examName += '_' + examTypeName + '_' + academicYear;
    $('#editExamNamePreview').text('Generated Exam Name: ' + examName);
}

function loadEditExamModal(examId) {
    
    // Load exam details
    $.get('../api/get_exam_details.php', {exam_id: examId}, function(response) {
        
        if (response.success) {
            var exam = response.data;
            
            // Set exam ID
            $('#editExamId').val(exam.id);
            
            // Load dropdowns first
            loadEditDropdowns().then(function() {
                
                // Set form values
                $('#editExamClass').val(exam.class_id);
                $('#editExamType').val(exam.exam_type_id);
                $('#editExamYear').val(exam.academic_year);
                $('#editExamDescription').val(exam.description || '');
                
                // Update dependent dropdowns and wait for them to complete
                updateEditDepartmentsByClass();
                updateEditSectionsByClass();
                
                // Set department and section after dropdowns are populated
                setTimeout(function() {
                    if (exam.department_id) {
                        $('#editExamDepartment').val(exam.department_id);
                        updateEditSectionsByDepartment();
                    }
                    if (exam.section_id) {
                        $('#editExamSection').val(exam.section_id);
                    }
                    
                    // Update preview after all values are set
                    setTimeout(function() {
                        updateEditExamNamePreview();
                        
                        // Show modal
                        $('#editExamModal').modal('show');
                    }, 500); // Additional delay to ensure all dropdowns are properly set
                }, 1000); // Delay to ensure dropdowns are populated
            });
        } else {
            alert('Failed to load exam details: ' + response.message);
        }
    }).fail(function(xhr, status, error) {
        console.error('Failed to load exam details:', xhr, status, error);
        alert('Failed to load exam details. Please try again.');
    });
}

function loadEditDropdowns() {
    return new Promise(function(resolve) {
        var promises = [];
        
        // Load classes
        var classPromise = $.get('../api/get_classes.php').then(function(response) {
            if (response.success) {
                var classSelect = $('#editExamClass');
                classSelect.empty().append('<option value="">Select Class</option>');
                response.data.forEach(function(cls) {
                    classSelect.append(`<option value="${cls.id}">${cls.name}</option>`);
                });
            }
        });
        promises.push(classPromise);
        
        // Load exam types
        var typePromise = $.get('../api/get_exam_types.php').then(function(response) {
            if (response.success) {
                var typeSelect = $('#editExamType');
                typeSelect.empty().append('<option value="">Select Exam Type</option>');
                response.data.forEach(function(type) {
                    typeSelect.append(`<option value="${type.id}">${type.type_name}</option>`);
                });
            }
        });
        promises.push(typePromise);
        
        // Wait for all promises to resolve
        Promise.all(promises).then(function() {
            resolve();
        });
    });
}

function updateEditDepartmentsByClass() {
    var classId = $('#editExamClass').val();
    var deptSelect = $('#editExamDepartment');
    
    if (!classId) {
        deptSelect.empty().append('<option value="">All Department</option>');
        deptSelect.prop('disabled', true);
        return;
    }
    
    $.get('../api/get_departments_by_class.php', {class_id: classId}, function(response) {
        if (response.success) {
            deptSelect.empty();
            if (response.data.length === 0) {
                deptSelect.append('<option value="">No departments available for this class</option>');
                deptSelect.prop('disabled', true);
            } else {
                deptSelect.append('<option value="">All Department</option>');
                response.data.forEach(function(dept) {
                    deptSelect.append(`<option value="${dept.id}">${dept.name}</option>`);
                });
                deptSelect.prop('disabled', false);
            }
        }
    });
}

function updateEditSectionsByClass() {
    var classId = $('#editExamClass').val();
    var secSelect = $('#editExamSection');
    
    if (!classId) {
        secSelect.empty().append('<option value="">All Section</option>');
        secSelect.prop('disabled', true);
        return;
    }
    
    $.get('../api/get_sections_by_class.php', {class_id: classId}, function(response) {
        if (response.success) {
            secSelect.empty();
            if (response.data.length === 0) {
                secSelect.append('<option value="">No sections available for this class</option>');
                secSelect.prop('disabled', true);
            } else {
                secSelect.append('<option value="">All Section</option>');
                response.data.forEach(function(sec) {
                    secSelect.append(`<option value="${sec.id}">${sec.name}</option>`);
                });
                secSelect.prop('disabled', false);
            }
        }
    });
}

function updateEditSectionsByDepartment() {
    var classId = $('#editExamClass').val();
    var deptId = $('#editExamDepartment').val();
    var secSelect = $('#editExamSection');
    
    if (!classId) {
        secSelect.empty().append('<option value="">All Section</option>');
        secSelect.prop('disabled', true);
        return;
    }
    
    // If no department is selected, load all sections for the class
    if (!deptId) {
        $.get('../api/get_sections_by_class.php', {class_id: classId}, function(response) {
            if (response.success) {
                secSelect.empty();
                if (response.data.length === 0) {
                    secSelect.append('<option value="">No sections available for this class</option>');
                    secSelect.prop('disabled', true);
                } else {
                    secSelect.append('<option value="">All Section</option>');
                    response.data.forEach(function(sec) {
                        secSelect.append(`<option value="${sec.id}">${sec.name}</option>`);
                    });
                    secSelect.prop('disabled', false);
                }
            }
        });
        return;
    }
    
    // If department is selected, load sections for that specific department
    $.get('../api/get_sections_by_class_dept.php', {class_id: classId, department_id: deptId}, function(response) {
        if (response.success) {
            secSelect.empty();
            if (response.data.length === 0) {
                secSelect.append('<option value="">No sections available for this department</option>');
                secSelect.prop('disabled', true);
            } else {
                secSelect.append('<option value="">All Section</option>');
                response.data.forEach(function(sec) {
                    secSelect.append(`<option value="${sec.id}">${sec.name}</option>`);
                });
                secSelect.prop('disabled', false);
            }
        }
    });
}

function updateExam() {
    // Get form data
    var formData = new FormData($('#editExamForm')[0]);
    
    // Get the selected values for dynamic exam name generation
    var classId = $('#editExamClass').val();
    var departmentId = $('#editExamDepartment').val();
    var sectionId = $('#editExamSection').val();
    var examTypeId = $('#editExamType').val();
    var academicYear = $('#editExamYear').val();
    
    // Validate required fields
    if (!classId || !examTypeId || !academicYear) {
        alert('Please fill in all required fields (Class, Exam Type, and Academic Year)');
        return;
    }
    
    // Get the text values for exam name generation
    var className = $('#editExamClass option:selected').text();
    var examTypeName = $('#editExamType option:selected').text();
    
    // Check if department dropdown is enabled and has options (excluding the first "Select" option)
    var deptSelect = $('#editExamDepartment');
    var hasDepartments = !deptSelect.prop('disabled') && deptSelect.find('option').length > 1;
    
    // Check if section dropdown is enabled and has options (excluding the first "Select" option)
    var secSelect = $('#editExamSection');
    var hasSections = !secSelect.prop('disabled') && secSelect.find('option').length > 1;
    
    // Generate exam name dynamically
    var examName = className;
    
    // Handle department part
    if (hasDepartments) {
        if (departmentId) {
            var departmentName = $('#editExamDepartment option:selected').text();
            examName += '_' + departmentName;
        } else {
            var deptDefaultText = deptSelect.find('option:first').text();
            examName += '_' + deptDefaultText;
        }
    }
    
    // Handle section part
    if (hasSections) {
        if (sectionId) {
            var sectionName = $('#editExamSection option:selected').text();
            examName += '_' + sectionName;
        } else {
            var secDefaultText = secSelect.find('option:first').text();
            examName += '_' + secDefaultText;
        }
    }
    
    examName += '_' + examTypeName + '_' + academicYear;
    
    // Add the generated exam name to form data
    formData.set('exam_name', examName);
    
    // Convert FormData to object for AJAX
    var data = {};
    formData.forEach(function(value, key) {
        data[key] = value;
    });
    
    $.post('../api/update_exam.php', data, function(response) {
        if (response.success) {
            $('#editExamModal').modal('hide');
            loadExams();
            alert('Exam updated successfully!');
        } else {
            alert('Failed to update exam: ' + response.message);
        }
    }).fail(function() {
        alert('Failed to update exam. Please try again.');
    });
}

function init() {
    loadClassDropdowns();
    
    // Add the missing edit exam type form listener
    $('#editExamTypeForm').on('submit', function(e) {
        e.preventDefault();
        updateExamType();
    });
    
    // Add edit exam form listener
    $('#editExamForm').on('submit', function(e) {
        e.preventDefault();
        updateExam();
    });
    
    // Add edit modal dropdown change listeners
    $('#editExamClass').on('change', function() {
        updateEditDepartmentsByClass();
        updateEditSectionsByClass();
        // Delay preview update to allow dropdowns to populate
        setTimeout(function() {
            updateEditExamNamePreview();
        }, 300);
    });
    
    $('#editExamDepartment').on('change', function() {
        updateEditSectionsByDepartment();
        // Delay preview update to allow sections dropdown to populate
        setTimeout(function() {
            updateEditExamNamePreview();
        }, 300);
    });
    
    $('#editExamSection').on('change', function() {
        updateEditExamNamePreview();
    });
    
    $('#editExamType').on('change', function() {
        updateEditExamNamePreview();
    });
    
    $('#editExamYear').on('input', function() {
        updateEditExamNamePreview();
    });
    
    // Add modal shown event listener to ensure preview is updated when modal opens
    $('#editExamModal').on('shown.bs.modal', function() {
        setTimeout(function() {
            updateEditExamNamePreview();
        }, 100);
    });
    
    // Add modal shown event listener for add exam modal
    $('#addExamModal').on('shown.bs.modal', function() {
        setTimeout(function() {
            updateExamNamePreview();
        }, 100);
    });
    

} 