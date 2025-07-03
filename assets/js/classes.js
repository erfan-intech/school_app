$(document).ready(function() {
    function loadClasses() {
        $.get('../api/get_classes.php', function(res) {
            if (res.success) {
                let rows = '';
                res.data.forEach(function(c) {
                    rows += `<tr>
                        <!--<td>${c.id}</td>-->
                        <td><a href="class_details.php?class_id=${c.id}">${c.name}</a></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-warning editClassBtn" data-id="${c.id}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteClassBtn" data-id="${c.id}">Delete</button>
                        </td>
                    </tr>`;
                });
                $('#classesTable tbody').html(rows);
            }
        });
    }

    loadClasses();

    $('#addClassBtn').click(function() {
        $('#classForm')[0].reset();
        $('#classId').val('');
        $('#classModalLabel').text('Add Class');
    });

    $(document).on('click', '.editClassBtn', function() {
        const id = $(this).data('id');
        $.get('../api/get_classes.php', function(res) {
            if (res.success) {
                const cls = res.data.find(c => c.id == id);
                if (cls) {
                    $('#classId').val(cls.id);
                    $('#name').val(cls.name);
                    $('#classModalLabel').text('Edit Class');
                    var modal = new bootstrap.Modal(document.getElementById('classModal'));
                    modal.show();
                }
            }
        });
    });

    $('#classForm').submit(function(e) {
        e.preventDefault();
        const id = $('#classId').val();
        const url = id ? '../api/update_class.php' : '../api/add_class.php';
        $.post(url, $(this).serialize(), function(res) {
            if (res.success) {
                $('#classModal').modal('hide');
                loadClasses();
            } else {
                alert(res.message);
            }
        }, 'json');
    });

    $(document).on('click', '.deleteClassBtn', function() {
        if (!confirm('Are you sure you want to delete this class?')) return;
        const id = $(this).data('id');
        $.post('../api/delete_class.php', {id}, function(res) {
            if (res.success) {
                loadClasses();
            } else {
                alert(res.message);
            }
        }, 'json');
    });
}); 