$(document).ready(function() {
    var teachersData = [];
    var filteredData = [];
    var currentSort = { column: null, direction: 'asc' };
    var currentPage = 1;
    var itemsPerPage = 10;
    var viewAllMode = false;

    function loadTeachers(searchTerm = '') {
        var url = '../api/get_teachers.php';
        if (searchTerm) {
            url += '?search=' + encodeURIComponent(searchTerm);
        }
        
        $.get(url, function(res) {
            if (res.success) {
                teachersData = res.data;
                filteredData = [...teachersData];
                renderTeachersTable();
                updatePagination();
            }
        });
    }

    function renderTeachersTable() {
        // Apply sorting
        if (currentSort.column) {
            filteredData.sort(function(a, b) {
                var aVal, bVal;
                
                // Handle SL No sorting (use the PHP-generated sl_no)
                if (currentSort.column === 'sl_no') {
                    aVal = a.sl_no;
                    bVal = b.sl_no;
                    return currentSort.direction === 'asc' ? aVal - bVal : bVal - aVal;
                }
                
                // Handle numeric sorting for ID
                if (currentSort.column === 'id') {
                    aVal = a[currentSort.column];
                    bVal = b[currentSort.column];
                    return currentSort.direction === 'asc' ? aVal - bVal : bVal - aVal;
                }
                
                // Handle string sorting
                aVal = (a[currentSort.column] || '').toString().toLowerCase();
                bVal = (b[currentSort.column] || '').toString().toLowerCase();
                
                if (aVal < bVal) return currentSort.direction === 'asc' ? -1 : 1;
                if (aVal > bVal) return currentSort.direction === 'asc' ? 1 : -1;
                return 0;
            });
        }

        // Calculate pagination
        var totalPages = Math.ceil(filteredData.length / itemsPerPage);
        var startIndex, endIndex, pageData;
        
        if (viewAllMode) {
            // Show all data without pagination
            pageData = filteredData;
            startIndex = 0;
            endIndex = filteredData.length;
        } else {
            // Show paginated data
            startIndex = (currentPage - 1) * itemsPerPage;
            endIndex = Math.min(startIndex + itemsPerPage, filteredData.length);
            pageData = filteredData.slice(startIndex, endIndex);
        }

        // Render table rows
        let rows = '';
        pageData.forEach(function(t, index) {
            let pic = t.profile_picture ? `<img src='../uploads/teachers/${t.profile_picture}' alt='Profile' width='40' height='40' style='object-fit:cover;border-radius:50%;'>` : '';
            rows += `<tr data-teacher-id='${t.id}'>
                <td>${t.sl_no}</td>
                <td>${t.first_name} ${t.last_name}</td>
                <td>${t.phone || ''}</td>
                <td>${t.position || ''}</td>
                <td>${pic}</td>
                <td>${t.attendance_status_today || ''}</td>
                <td><button class="btn btn-sm btn-info toggle-details-btn" data-id="${t.id}">Details</button></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-warning editTeacherBtn" data-id="${t.id}">Edit</button>
                    <button class="btn btn-sm btn-danger deleteTeacherBtn" data-id="${t.id}">Delete</button>
                </td>
            </tr>`;
        });
        $('#teachersTable tbody').html(rows);

        // Update pagination info
        $('#startRecord').text(filteredData.length > 0 ? startIndex + 1 : 0);
        $('#endRecord').text(endIndex);
        $('#totalRecords').text(filteredData.length);
    }

    function updatePagination() {
        var totalPages = Math.ceil(filteredData.length / itemsPerPage);
        
        // Reset to page 1 if current page is out of bounds
        if (currentPage > totalPages && totalPages > 0) {
            currentPage = 1;
        }
        
        var $pagination = $('#pagination');
        $pagination.empty();
        
        if (viewAllMode) {
            // Hide pagination when in view all mode
            $pagination.hide();
            $('#viewAllBtn').text('View Less').removeClass('btn-outline-primary').addClass('btn-primary');
        } else {
            // Show pagination when in normal mode
            $pagination.show();
            $('#viewAllBtn').text('View All').removeClass('btn-primary').addClass('btn-outline-primary');
            
            // Previous button
            var prevDisabled = currentPage <= 1 ? 'disabled' : '';
            $pagination.append(`<li class="page-item ${prevDisabled}">
                <a class="page-link" href="#" id="prevPage">Previous</a>
            </li>`);
            
            // Page numbers
            var startPage = Math.max(1, currentPage - 2);
            var endPage = Math.min(totalPages, currentPage + 2);
            
            if (startPage > 1) {
                $pagination.append(`<li class="page-item">
                    <a class="page-link" href="#" data-page="1">1</a>
                </li>`);
                if (startPage > 2) {
                    $pagination.append(`<li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>`);
                }
            }
            
            for (var i = startPage; i <= endPage; i++) {
                var active = i === currentPage ? 'active' : '';
                $pagination.append(`<li class="page-item ${active}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`);
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    $pagination.append(`<li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>`);
                }
                $pagination.append(`<li class="page-item">
                    <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                </li>`);
            }
            
            // Next button
            var nextDisabled = currentPage >= totalPages ? 'disabled' : '';
            $pagination.append(`<li class="page-item ${nextDisabled}">
                <a class="page-link" href="#" id="nextPage">Next</a>
            </li>`);
        }
    }

    // Handle sortable column clicks
    $(document).on('click', '.sortable', function() {
        var column = $(this).data('sort');
        var direction = 'asc';
        
        // Remove previous sort indicators
        $('.sortable').removeClass('asc desc');
        
        // Determine sort direction
        if (currentSort.column === column && currentSort.direction === 'asc') {
            direction = 'desc';
            $(this).addClass('desc');
        } else {
            $(this).addClass('asc');
        }
        
        currentSort = { column: column, direction: direction };
        currentPage = 1; // Reset to first page when sorting
        renderTeachersTable();
        updatePagination();
    });

    loadTeachers();

    $('#addTeacherBtn').click(function() {
        $('#teacherForm')[0].reset();
        $('#teacherId').val('');
        $('#teacherModalLabel').text('Add Teacher');
        $('#teacherProfilePicture').val('');
        $('#teacherProfilePicture').replaceWith($('#teacherProfilePicture').clone(true));
        $('#teacherPicPreview').html('');
        $('#removeTeacherPhotoBtn').hide();
        $('#removeTeacherPhoto').val('0');
        $('#address').val('');
    });

    $(document).on('click', '.editTeacherBtn', function() {
        const id = $(this).data('id');
        $('#teacherProfilePicture').val('');
        $('#teacherProfilePicture').replaceWith($('#teacherProfilePicture').clone(true));
        $('#teacherPicPreview').html('');
        $('#removeTeacherPhotoBtn').hide();
        $('#removeTeacherPhoto').val('0');
        $.get('../api/get_teachers.php', function(res) {
            if (res.success) {
                const teacher = res.data.find(t => t.id == id);
                if (teacher) {
                    $('#teacherId').val(teacher.id);
                    $('#first_name').val(teacher.first_name);
                    $('#last_name').val(teacher.last_name);
                    $('#gender').val(teacher.gender);
                    $('#address').val(teacher.address);
                    $('#position').val(teacher.position);
                    $('#join_date').val(teacher.join_date);
                    $('#leave_date').val(teacher.leave_date);
                    $('#salary').val(teacher.salary);
                    if ($('#phone').length) $('#phone').val(teacher.phone || '');
                    if ($('#email').length) $('#email').val(teacher.email || '');
                    if (teacher.profile_picture) {
                        $('#teacherPicPreview').html(`<img src='../uploads/teachers/${teacher.profile_picture}' alt='Profile' width='80' height='80' style='object-fit:cover;border-radius:50%;'>`);
                        $('#removeTeacherPhotoBtn').show();
                        $('#removeTeacherPhoto').val('0');
                    } else {
                        $('#teacherPicPreview').html('');
                        $('#removeTeacherPhotoBtn').hide();
                        $('#removeTeacherPhoto').val('0');
                    }
                    // Set DOB field with correct format
                    let dob = teacher.dob;
                    if (!dob || dob === '0000-00-00') dob = '';
                    $('#dob').val(dob);
                    $('#teacherModalLabel').text('Edit Teacher');
                    var modal = new bootstrap.Modal(document.getElementById('teacherModal'));
                    modal.show();
                }
            }
        });
    });

    $('#teacherForm').submit(function(e) {
        e.preventDefault();
        const id = $('#teacherId').val();
        const url = id ? '../api/update_teacher.php' : '../api/add_teacher.php';
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
                    $('#teacherModal').modal('hide');
                    loadTeachers();
                } else {
                    alert(res.message);
                }
            }
        });
    });

    $(document).on('click', '.deleteTeacherBtn', function() {
        if (!confirm('Are you sure you want to delete this teacher?')) return;
        const id = $(this).data('id');
        $.post('../api/delete_teacher.php', {id}, function(res) {
            if (res.success) {
                loadTeachers();
            } else {
                alert(res.message);
            }
        }, 'json');
    });

    // Show image preview on file select
    $('#teacherProfilePicture').on('change', function() {
        const [file] = this.files;
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#teacherPicPreview').html(`<img src='${e.target.result}' alt='Profile' width='80' height='80' style='object-fit:cover;border-radius:50%;'>`);
                $('#removeTeacherPhotoBtn').show();
                $('#removeTeacherPhoto').val('0');
            };
            reader.readAsDataURL(file);
        } else {
            $('#teacherPicPreview').html('');
            $('#removeTeacherPhotoBtn').hide();
        }
    });

    // Remove photo button
    $('#removeTeacherPhotoBtn').click(function() {
        $('#teacherPicPreview').html('');
        $('#teacherProfilePicture').val('');
        $(this).hide();
        $('#removeTeacherPhoto').val('1');
    });

    // Pagination event handlers
    $(document).on('click', '#prevPage', function(e) {
        e.preventDefault();
        if (currentPage > 1) {
            currentPage--;
            renderTeachersTable();
            updatePagination();
        }
    });

    $(document).on('click', '#nextPage', function(e) {
        e.preventDefault();
        var totalPages = Math.ceil(filteredData.length / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            renderTeachersTable();
            updatePagination();
        }
    });

    $(document).on('click', '[data-page]', function(e) {
        e.preventDefault();
        var page = parseInt($(this).data('page'));
        if (page !== currentPage) {
            currentPage = page;
            renderTeachersTable();
            updatePagination();
        }
    });

    $('#teacherSearch').on('input', function() {
        currentPage = 1; // Reset to first page when searching
        viewAllMode = false; // Reset to pagination mode when searching
        var searchTerm = $(this).val();
        loadTeachers(searchTerm);
    });

    // View All button functionality
    $('#viewAllBtn').on('click', function() {
        viewAllMode = !viewAllMode; // Toggle view all mode
        renderTeachersTable();
        updatePagination();
    });

    // Toggle details row
    $(document).on('click', '.toggle-details-btn', function() {
        var teacherId = $(this).data('id');
        var $row = $(this).closest('tr');
        // If already open, close and return
        if ($row.next().hasClass('teacher-details-row')) {
            $row.next().find('.details-content').slideUp(200, function() {
                $row.next().remove();
            });
            return;
        }
        // Remove any open details row with animation
        var $openRow = $('#teachersTable tbody .teacher-details-row');
        if ($openRow.length) {
            $openRow.find('.details-content').slideUp(200, function() {
                $openRow.remove();
            });
        }
        // Find teacher data
        $.get('../api/get_teachers.php', function(res) {
            if (res.success) {
                var t = res.data.find(tt => tt.id == teacherId);
                if (!t) return;
                var details = `<tr class='teacher-details-row'><td colspan='10'><div class='details-content' style='display:none;'><ul class='mb-0'>
                    <li><strong>Email:</strong> ${t.email || 'N/A'}</li>
                    <li><strong>Date of Birth:</strong> ${(!t.dob || t.dob === '0000-00-00') ? 'N/A' : t.dob}</li>
                    <li><strong>Gender:</strong> ${t.gender || 'N/A'}</li>
                    <li><strong>Address:</strong> ${t.address || 'N/A'}</li>
                    <li><strong>Join Date:</strong> ${(!t.join_date || t.join_date === '0000-00-00') ? 'N/A' : t.join_date}</li>
                    <li><strong>Leave Date:</strong> ${(!t.leave_date || t.leave_date === '0000-00-00') ? 'N/A' : t.leave_date}</li>
                    <li><strong>Salary:</strong> ${t.salary || 'N/A'}</li>
                </ul></div></td></tr>`;
                var $detailsRow = $(details);
                $row.after($detailsRow);
                $detailsRow.find('.details-content').slideDown(200);
            }
        });
    });
});
