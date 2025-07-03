$(document).ready(function() {
    function loadSubjects() {
        $.get('../api/get_subjects.php', function(res) {
            if (res.success) {
                let rows = '';
                res.data.forEach(function(s) {
                    rows += `<tr>
                        <td>${s.id}</td>
                        <td>${s.name}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-warning editSubjectBtn" data-id="${s.id}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteSubjectBtn" data-id="${s.id}">Delete</button>
                        </td>
                    </tr>`;
                });
                $('#subjectsTable tbody').html(rows);
            }
        });
    }

    loadSubjects();

    $('#addSubjectBtn').click(function() {
        $('#subjectForm')[0].reset();
        $('#subjectId').val('');
        $('#subjectModalLabel').text('Add Subject');
    });

    // Add/Edit Subject
    $('#subjectForm').submit(function(e) {
        e.preventDefault();
        const id = $('#subjectId').val();
        const url = id ? '../api/update_subject.php' : '../api/add_subject.php';
        $.post(url, $(this).serialize(), function(res) {
            if (res.success) {
                $('#subjectModal').modal('hide');
                loadSubjects();
            } else {
                alert(res.message);
            }
        }, 'json');
    });

    // Edit Subject
    $(document).on('click', '.editSubjectBtn', function() {
        const id = $(this).data('id');
        $.get('../api/get_subjects.php', function(res) {
            if (res.success) {
                const subject = res.data.find(s => s.id == id);
                if (subject) {
                    $('#subjectId').val(subject.id);
                    $('#subjectName').val(subject.name);
                    $('#subjectModalLabel').text('Edit Subject');
                    var modal = new bootstrap.Modal(document.getElementById('subjectModal'));
                    modal.show();
                }
            }
        });
    });

    // Delete Subject
    $(document).on('click', '.deleteSubjectBtn', function() {
        if (!confirm('Are you sure you want to delete this subject?')) return;
        const id = $(this).data('id');
        $.post('../api/delete_subject.php', {id}, function(res) {
            if (res.success) {
                loadSubjects();
            } else {
                alert(res.message);
            }
        }, 'json');
    });
}); 