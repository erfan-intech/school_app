$(document).ready(function() {
    // Hide Load Students button on page load
    $('#loadStudentsBtn').prop('disabled', true);

    function getCurrentTime() {
        const now = new Date();
        return now.toTimeString().slice(0,5);
    }

    // Populate students dropdown when class is selected
    $('#attendanceClass').change(function() {
        let classId = $(this).val();
        $('#attendanceStudent').prop('disabled', true).html('<option value="">Select Student</option>');
        $('#attendanceForm').hide();
        $('#singleAttendanceForm').hide();
        if (!classId) {
            $('#loadStudentsBtn').prop('disabled', true);
            return;
        }
        $.get('../api/get_students_by_class.php', {class_id: classId}, function(res) {
            if (res.success) {
                let options = '<option value="all">All Students</option>';
                res.data.forEach(function(s) {
                    options += `<option value="${s.id}">${s.first_name} ${s.last_name || ''}</option>`;
                });
                $('#attendanceStudent').html(options).prop('disabled', false);
                $('#attendanceStudent').val('all'); // Default to All Students
                $('#loadStudentsBtn').prop('disabled', false); // Enable button by default
            }
        }, 'json');
    });

    // Show appropriate attendance form
    $('#attendanceStudent').change(function() {
        let studentId = $(this).val();
        if (!studentId) {
            $('#attendanceForm').hide();
            $('#singleAttendanceForm').hide();
            $('#loadStudentsBtn').prop('disabled', true);
            return;
        }
        if (studentId === 'all') {
            $('#singleAttendanceForm').hide();
            $('#attendanceForm').hide();
            $('#loadStudentsBtn').prop('disabled', false);
        } else {
            $('#attendanceForm').hide();
            $('#singleAttendanceForm').show();
            $('#singleStudentId').val(studentId);
            $('#singleStatus').val('present');
            $('#singleTimeIn').val(getCurrentTime());
            $('#singleTimeOut').val('');
            $('#loadStudentsBtn').prop('disabled', true);
        }
    });

    // Load students for bulk attendance
    $('#loadStudentsBtn').click(function(e) {
        e.preventDefault();
        let classId = $('#attendanceClass').val();
        let date = $('#attendanceDate').val();
        if (!classId || !date) {
            alert('Please select class and date.');
            return;
        }
        $.get('../api/get_students_by_class.php', {class_id: classId}, function(res) {
            if (res.success) {
                let rows = '';
                res.data.forEach(function(s) {
                    let status = 'present'; // default status
                    let timeIn = getCurrentTime();
                    let timeInDisabled = '';
                    if (status === 'absent') {
                        timeIn = '00:00';
                        timeInDisabled = 'disabled';
                    }
                    rows += `<tr>
                        <td><input type='hidden' name='student_ids[]' value='${s.id}'>${s.id}</td>
                        <td>${s.first_name} ${s.last_name || ''}</td>
                        <td>
                            <select name='status[]' class='form-select'>
                                <option value='present' selected>Present</option>
                                <option value='absent'>Absent</option>
                                <option value='late'>Late</option>
                                <option value='excused'>Excused</option>
                            </select>
                        </td>
                        <td><input type='time' class='form-control' name='time_in[]' value='${timeIn}' ${timeInDisabled}></td>
                        <td><input type='time' class='form-control' name='time_out[]'></td>
                    </tr>`;
                });
                $('#studentsAttendanceTable tbody').html(rows);
                $('#attendanceForm').show();
                // Trigger change event on all status dropdowns to ensure correct time_in state
                $("#studentsAttendanceTable select[name='status[]']").each(function() {
                    $(this).trigger('change');
                });
            } else {
                alert('No students found for this class.');
                $('#attendanceForm').hide();
            }
        }, 'json');
    });

    // Mark attendance for all students
    $('#attendanceForm').submit(function(e) {
        e.preventDefault();
        let classId = $('#attendanceClass').val();
        let date = $('#attendanceDate').val();
        let formData = $(this).serializeArray();
        formData.push({name: 'class_id', value: classId});
        formData.push({name: 'date', value: date});
        $.post('../api/add_attendance.php', formData, function(res) {
            if (res.success) {
                alert('Attendance marked successfully!');
                $('#attendanceForm').hide();
                $('#studentsAttendanceTable tbody').html('');
            } else {
                alert(res.message);
            }
        }, 'json');
    });

    // Mark attendance for a single student
    $('#singleAttendanceForm').submit(function(e) {
        e.preventDefault();
        let classId = $('#attendanceClass').val();
        let date = $('#attendanceDate').val();
        let studentId = $('#singleStudentId').val();
        let status = $('#singleStatus').val();
        let timeIn = $('#singleTimeIn').val();
        let timeOut = $('#singleTimeOut').val();
        $.post('../api/add_attendance.php', {
            class_id: classId,
            date: date,
            student_ids: [studentId],
            status: [status],
            time_in: [timeIn],
            time_out: [timeOut]
        }, function(res) {
            if (res.success) {
                alert('Attendance marked successfully!');
                $('#singleAttendanceForm').hide();
            } else {
                alert(res.message);
            }
        }, 'json');
    });

    // Filter attendance records
    $('#filterAttendanceBtn').click(function(e) {
        e.preventDefault();
        let classId = $('#filterClass').val();
        let date = $('#filterDate').val();
        $.get('../api/get_attendance.php', {class_id: classId, date: date}, function(res) {
            if (res.success) {
                let rows = '';
                res.data.forEach(function(a) {
                    rows += `<tr>
                        <td>${a.student_id}</td>
                        <td>${a.student_name}</td>
                        <td>${a.class_name}</td>
                        <td>${a.roll_no || ''}</td>
                        <td>${a.date}</td>
                        <td>${a.status}</td>
                        <td>${a.time_in || ''}</td>
                        <td>${a.time_out || ''}</td>
                        <td><button class='btn btn-sm btn-primary edit-attendance-btn' 
                            data-id='${a.id}'
                            data-status='${a.status}'
                            data-time_in='${a.time_in || ''}'
                            data-time_out='${a.time_out || ''}'>Edit</button></td>
                    </tr>`;
                });
                $('#attendanceRecordsTable tbody').html(rows);
            } else {
                $('#attendanceRecordsTable tbody').html('<tr><td colspan="9">No records found.</td></tr>');
            }
        }, 'json');
    });

    // Auto-load today's attendance records on page load
    // $('#filterAttendanceBtn').click();

    // Handle status change in bulk attendance table
    $(document).on('change', "#studentsAttendanceTable select[name='status[]']", function() {
        var status = $(this).val();
        var timeInInput = $(this).closest('tr').find("input[name='time_in[]']");
        if (status === 'absent') {
            timeInInput.prop('disabled', false); // Enable first to allow value change
            timeInInput.val(''); // Clear first
            timeInInput.val('00:00'); // Then set to 00:00
            timeInInput[0].value = '00:00'; // Force value property for DOM
            timeInInput.prop('disabled', true);
        } else {
            timeInInput.prop('disabled', false);
            if (!timeInInput.val() || timeInInput.val() === '00:00') {
                var now = getCurrentTime();
                timeInInput.val(now);
                timeInInput[0].value = now; // Force value property for DOM
            }
        }
    });

    // Handle status change in single-student form
    $('#singleStatus').change(function() {
        var status = $(this).val();
        var timeInInput = $('#singleTimeIn');
        if (status === 'absent') {
            timeInInput.val('00:00').prop('disabled', true);
        } else {
            timeInInput.prop('disabled', false);
            if (!timeInInput.val() || timeInInput.val() === '00:00') {
                timeInInput.val(getCurrentTime());
            }
        }
    });

    // Open edit modal and fill data
    $(document).on('click', '.edit-attendance-btn', function() {
        var id = $(this).data('id');
        var status = $(this).data('status');
        var timeIn = $(this).data('time_in');
        var timeOut = $(this).data('time_out');
        var userType = $(this).data('user_type') || 'student';
        $('#editAttendanceId').val(id);
        $('#editAttendanceUserType').val(userType);
        $('#editStatus').val(status);
        $('#editTimeIn').val(timeIn);
        $('#editTimeOut').val(timeOut);
        $('#editAttendanceModal').modal('show');
    });

    // Submit edit attendance form
    $('#editAttendanceForm').submit(function(e) {
        e.preventDefault();
        var id = $('#editAttendanceId').val();
        var userType = $('#editAttendanceUserType').val();
        var status = $('#editStatus').val();
        var timeIn = $('#editTimeIn').val();
        var timeOut = $('#editTimeOut').val();
        $.post('../api/update_attendance.php', {
            attendance_id: id,
            status: status,
            time_in: timeIn,
            time_out: timeOut,
            user_type: userType
        }, function(res) {
            if (res.success) {
                $('#editAttendanceModal').modal('hide');
                if (userType === 'teacher') {
                    $('#filterTeacherAttendanceBtn').click();
                } else {
                    $('#filterAttendanceBtn').click();
                }
            } else {
                alert(res.message || 'Failed to update attendance.');
            }
        }, 'json');
    });

    // Populate teachers dropdown for teacher attendance
    function loadTeachersDropdown() {
        $.get('../api/get_teachers.php', function(res) {
            if (res.success) {
                let options = '<option value="all" selected>All Teachers</option>';
                res.data.forEach(function(t) {
                    options += `<option value="${t.id}">${t.first_name} ${t.last_name}</option>`;
                });
                $('#attendanceTeacher').html(options);
                $('#attendanceTeacher').val('all');
            }
        }, 'json');
    }
    loadTeachersDropdown();

    // Filter teacher attendance records
    $('#filterTeacherAttendanceBtn').click(function(e) {
        e.preventDefault();
        let teacherId = $('#attendanceTeacher').val();
        let date = $('#attendanceTeacherDate').val();
        // Build params for API
        let params = { user_type: 'teacher' };
        if (teacherId && teacherId !== 'all') params.user_id = teacherId;
        if (date) params.date = date;
        $.get('../api/get_attendance.php', params, function(res) {
            if (res.success) {
                let rows = '';
                res.data.forEach(function(a) {
                    rows += `<tr>
                        <td>${a.teacher_id || a.user_id || ''}</td>
                        <td>${a.teacher_name || a.name || ''}</td>
                        <td>${a.date}</td>
                        <td>${a.status}</td>
                        <td>${a.time_in || ''}</td>
                        <td>${a.time_out || ''}</td>
                        <td><button class='btn btn-sm btn-primary edit-attendance-btn' 
                            data-id='${a.id}'
                            data-status='${a.status}'
                            data-time_in='${a.time_in || ''}'
                            data-time_out='${a.time_out || ''}'
                            data-user_type='teacher'>Edit</button></td>
                    </tr>`;
                });
                $('#teacherAttendanceRecordsTable tbody').html(rows);
            } else {
                $('#teacherAttendanceRecordsTable tbody').html('<tr><td colspan="7">No records found.</td></tr>');
            }
        }, 'json');
    });

    // Populate teachers dropdown for mark teacher attendance
    function loadMarkTeachersDropdown() {
        $.get('../api/get_teachers.php', function(res) {
            if (res.success) {
                let options = '<option value="all" selected>All Teachers</option>';
                res.data.forEach(function(t) {
                    options += `<option value="${t.id}">${t.first_name} ${t.last_name}</option>`;
                });
                $('#markTeacherAttendanceTeacher').html(options);
            }
        }, 'json');
    }
    loadMarkTeachersDropdown();

    // Load teachers for marking attendance
    function loadTeachersAttendanceTable(teacherId, date) {
        $.get('../api/get_teachers.php', function(res) {
            if (res.success) {
                let teachers = res.data;
                if (teacherId !== 'all') {
                    teachers = teachers.filter(t => t.id == teacherId);
                }
                let rows = '';
                let now = getCurrentTime();
                teachers.forEach(function(t) {
                    let status = 'present';
                    let timeIn = now;
                    let timeOut = '';
                    rows += `<tr>
                        <td><input type='hidden' name='teacher_ids[]' value='${t.id}'>${t.id}</td>
                        <td>${t.first_name} ${t.last_name}</td>
                        <td>
                            <select name='status[]' class='form-select'>
                                <option value='present' selected>Present</option>
                                <option value='absent'>Absent</option>
                                <option value='late'>Late</option>
                                <option value='excused'>Excused</option>
                            </select>
                        </td>
                        <td><input type='time' class='form-control' name='time_in[]' value='${timeIn}'></td>
                        <td><input type='time' class='form-control' name='time_out[]' value='${timeOut}'></td>
                    </tr>`;
                });
                $('#teachersAttendanceTable tbody').html(rows);
                $('#markTeacherAttendanceForm').show();
            } else {
                alert('No teachers found.');
                $('#markTeacherAttendanceForm').hide();
            }
        }, 'json');
    }

    $('#loadMarkTeacherAttendanceBtn').prop('disabled', false);

    $('#markTeacherAttendanceTeacher').change(function() {
        let teacherId = $(this).val();
        let date = $('#markTeacherAttendanceDate').val();
        if (teacherId && teacherId !== 'all') {
            loadTeachersAttendanceTable(teacherId, date);
            $('#loadMarkTeacherAttendanceBtn').prop('disabled', true);
        } else {
            $('#teachersAttendanceTable tbody').html('');
            $('#markTeacherAttendanceForm').hide();
            $('#loadMarkTeacherAttendanceBtn').prop('disabled', false);
        }
    });

    $('#loadMarkTeacherAttendanceBtn').click(function(e) {
        e.preventDefault();
        let teacherId = $('#markTeacherAttendanceTeacher').val();
        let date = $('#markTeacherAttendanceDate').val();
        if (!teacherId || !date) {
            alert('Please select teacher(s) and date.');
            return;
        }
        loadTeachersAttendanceTable(teacherId, date);
    });

    // Mark attendance for teachers
    $('#markTeacherAttendanceForm').submit(function(e) {
        e.preventDefault();
        let date = $('#markTeacherAttendanceDate').val();
        let teacherIds = [];
        let statuses = [];
        let timeIns = [];
        let timeOuts = [];
        $('#teachersAttendanceTable tbody tr').each(function() {
            teacherIds.push($(this).find("input[name='teacher_ids[]']").val());
            statuses.push($(this).find("select[name='status[]']").val());
            timeIns.push($(this).find("input[name='time_in[]']").val());
            timeOuts.push($(this).find("input[name='time_out[]']").val());
        });
        if (!date || teacherIds.length === 0) {
            alert('Please select date and load teachers.');
            return;
        }
        $.post('../api/add_attendance.php', {
            user_type: 'teacher',
            date: date,
            teacher_ids: teacherIds,
            status: statuses,
            time_in: timeIns,
            time_out: timeOuts
        }, function(res) {
            if (res.success) {
                alert('Attendance marked successfully!');
                $('#markTeacherAttendanceForm').hide();
                $('#teachersAttendanceTable tbody').html('');
            } else {
                alert(res.message || 'Failed to mark attendance.');
            }
        }, 'json');
    });
}); 