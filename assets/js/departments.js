$(document).ready(function() {
    function loadDepartments() {
        $.get('../api/get_departments.php', function(res) {
            if (res.success) {
                let rows = '';
                res.data.forEach(function(d) {
                    rows += `<tr>
                        <!--<td>${d.id}</td>-->
                        <td>${d.name}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-warning editDepartmentBtn" data-id="${d.id}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteDepartmentBtn" data-id="${d.id}">Delete</button>
                        </td>
                    </tr>`;
                });
                $('#departmentsTable tbody').html(rows);
            }
        });
    }

    loadDepartments();

    $('#addDepartmentBtn').click(function() {
        $('#departmentForm')[0].reset();
        $('#departmentId').val('');
        $('#departmentModalLabel').text('Add Department');
    });

    $(document).on('click', '.editDepartmentBtn', function() {
        const id = $(this).data('id');
        $.get('../api/get_departments.php', function(res) {
            if (res.success) {
                const dept = res.data.find(d => d.id == id);
                if (dept) {
                    $('#departmentId').val(dept.id);
                    $('#department_name').val(dept.name);
                    $('#departmentModalLabel').text('Edit Department');
                    var modal = new bootstrap.Modal(document.getElementById('departmentModal'));
                    modal.show();
                }
            }
        });
    });

    $('#departmentForm').submit(function(e) {
        e.preventDefault();
        const id = $('#departmentId').val();
        const url = id ? '../api/update_department.php' : '../api/add_department.php';
        $.post(url, $(this).serialize(), function(res) {
            if (res.success) {
                $('#departmentModal').modal('hide');
                loadDepartments();
            } else {
                alert(res.message);
            }
        }, 'json');
    });

    $(document).on('click', '.deleteDepartmentBtn', function() {
        if (!confirm('Are you sure you want to delete this department?')) return;
        const id = $(this).data('id');
        $.post('../api/delete_department.php', {id}, function(res) {
            if (res.success) {
                loadDepartments();
            } else {
                alert(res.message);
            }
        }, 'json');
    });
}); 