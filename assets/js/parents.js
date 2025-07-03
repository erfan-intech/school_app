$(document).ready(function() {
    function loadParents() {
        $.get('../api/get_parents.php', function(res) {
            if (res.success) {
                let rows = '';
                res.data.forEach(function(p) {
                    let pic = p.profile_picture ? `<img src='../uploads/parents/${p.profile_picture}' alt='Profile' width='40' height='40' style='object-fit:cover;border-radius:50%;'>` : '';
                    rows += `<tr>
                        <td>${p.id}</td>
                        <td>${pic}</td>
                        <td>${p.first_name}</td>
                        <td>${p.last_name || ''}</td>
                        <td>${p.gender || ''}</td>
                        <td>${p.phone || ''}</td>
                        <td>${p.email || ''}</td>
                        <td>${p.address || ''}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-warning editParentBtn" data-id="${p.id}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteParentBtn" data-id="${p.id}">Delete</button>
                        </td>
                    </tr>`;
                });
                $('#parentsTable tbody').html(rows);
            }
        });
    }

    loadParents();

    $('#addParentBtn').click(function() {
        $('#parentForm')[0].reset();
        $('#parentId').val('');
        $('#parentModalLabel').text('Add Parent');
        $('#parentProfilePicture').val('');
        $('#parentProfilePicture').replaceWith($('#parentProfilePicture').clone(true));
        $('#parentPicPreview').html('');
        $('#removeParentPhotoBtn').hide();
        $('#removeParentPhoto').val('0');
    });

    // Show image preview on file select
    $('#parentProfilePicture').on('change', function() {
        const [file] = this.files;
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#parentPicPreview').html(`<img src='${e.target.result}' alt='Profile' width='80' height='80' style='object-fit:cover;border-radius:50%;'>`);
                $('#removeParentPhotoBtn').show();
                $('#removeParentPhoto').val('0');
            };
            reader.readAsDataURL(file);
        } else {
            $('#parentPicPreview').html('');
            $('#removeParentPhotoBtn').hide();
        }
    });

    // Remove photo button
    $('#removeParentPhotoBtn').click(function() {
        $('#parentPicPreview').html('');
        $('#parentProfilePicture').val('');
        $(this).hide();
        $('#removeParentPhoto').val('1');
    });

    // Add/Edit Parent
    $('#parentForm').submit(function(e) {
        e.preventDefault();
        const id = $('#parentId').val();
        const url = id ? '../api/update_parent.php' : '../api/add_parent.php';
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
                    $('#parentModal').modal('hide');
                    loadParents();
                } else {
                    alert(res.message);
                }
            }
        });
    });

    // Edit Parent
    $(document).on('click', '.editParentBtn', function() {
        const id = $(this).data('id');
        $('#parentProfilePicture').val('');
        $('#parentProfilePicture').replaceWith($('#parentProfilePicture').clone(true));
        $('#parentPicPreview').html('');
        $('#removeParentPhotoBtn').hide();
        $('#removeParentPhoto').val('0');
        $.get('../api/get_parents.php', function(res) {
            if (res.success) {
                const parent = res.data.find(p => p.id == id);
                if (parent) {
                    $('#parentId').val(parent.id);
                    $('#parentFirstName').val(parent.first_name);
                    $('#parentLastName').val(parent.last_name);
                    $('#parentGender').val(parent.gender);
                    $('#parentPhone').val(parent.phone);
                    $('#parentEmail').val(parent.email);
                    $('#parentAddress').val(parent.address);
                    if (parent.profile_picture) {
                        $('#parentPicPreview').html(`<img src='../uploads/parents/${parent.profile_picture}' alt='Profile' width='80' height='80' style='object-fit:cover;border-radius:50%;'>`);
                        $('#removeParentPhotoBtn').show();
                        $('#removeParentPhoto').val('0');
                    } else {
                        $('#parentPicPreview').html('');
                        $('#removeParentPhotoBtn').hide();
                        $('#removeParentPhoto').val('0');
                    }
                    $('#parentModalLabel').text('Edit Parent');
                    var modal = new bootstrap.Modal(document.getElementById('parentModal'));
                    modal.show();
                }
            }
        });
    });

    // Delete Parent
    $(document).on('click', '.deleteParentBtn', function() {
        if (!confirm('Are you sure you want to delete this parent?')) return;
        const id = $(this).data('id');
        $.post('../api/delete_parent.php', {id}, function(res) {
            if (res.success) {
                loadParents();
            } else {
                alert(res.message);
            }
        }, 'json');
    });

    $('#parentSearch').on('input', function() {
        var value = $(this).val().toLowerCase();
        $('#parentsTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
}); 