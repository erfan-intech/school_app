$(document).ready(function() {
    function loadSections() {
        $.get('../api/get_sections.php', function(res) {
            if (res.success) {
                let rows = '';
                res.data.forEach(function(s) {
                    rows += `<tr>
                        <!--<td>${s.id}</td>-->
                        <td>${s.name}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-warning editSectionBtn" data-id="${s.id}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteSectionBtn" data-id="${s.id}">Delete</button>
                        </td>
                    </tr>`;
                });
                $('#sectionsTable tbody').html(rows);
            }
        });
    }

    loadSections();

    $('#addSectionBtn').click(function() {
        $('#sectionForm')[0].reset();
        $('#sectionId').val('');
        $('#sectionModalLabel').text('Add Section');
    });

    $(document).on('click', '.editSectionBtn', function() {
        const id = $(this).data('id');
        $.get('../api/get_sections.php', function(res) {
            if (res.success) {
                const sec = res.data.find(s => s.id == id);
                if (sec) {
                    $('#sectionId').val(sec.id);
                    $('#section_name').val(sec.name);
                    $('#sectionModalLabel').text('Edit Section');
                    var modal = new bootstrap.Modal(document.getElementById('sectionModal'));
                    modal.show();
                }
            }
        });
    });

    $('#sectionForm').submit(function(e) {
        e.preventDefault();
        const id = $('#sectionId').val();
        const url = id ? '../api/update_section.php' : '../api/add_section.php';
        $.post(url, $(this).serialize(), function(res) {
            if (res.success) {
                $('#sectionModal').modal('hide');
                loadSections();
            } else {
                alert(res.message);
            }
        }, 'json');
    });

    $(document).on('click', '.deleteSectionBtn', function() {
        if (!confirm('Are you sure you want to delete this section?')) return;
        const id = $(this).data('id');
        $.post('../api/delete_section.php', {id}, function(res) {
            if (res.success) {
                loadSections();
            } else {
                alert(res.message);
            }
        }, 'json');
    });
}); 