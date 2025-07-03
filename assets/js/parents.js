// Alert function for showing messages
function showAlert(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'danger' ? 'alert-danger' : 
                      type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Remove existing alerts
    $('.alert').remove();
    
    // Add new alert at the top of the container
    $('.container').prepend(alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}

$(document).ready(function() {
    // Initialize the universal table sorter for parents
    var parentsTableSorter = initUniversalTableSorter('#parentsTable', {
        itemsPerPage: 10,
        enablePagination: true,
        enableSearch: true,
        enableViewAll: true,
        searchSelector: '#parentSearch',
        paginationSelector: '#pagination',
        viewAllSelector: '#viewAllBtn',
        searchFields: ['first_name last_name', 'phone', 'email', 'address', 'gender'],
        sortHandlers: {
            'sl_no': function(a, b, direction) {
                const aVal = parseInt(a.sl_no);
                const bVal = parseInt(b.sl_no);
                return direction === 'asc' ? aVal - bVal : bVal - aVal;
            },
            'name': function(a, b, direction) {
                const aVal = (a.first_name + ' ' + (a.last_name || '')).toLowerCase();
                const bVal = (b.first_name + ' ' + (b.last_name || '')).toLowerCase();
                if (aVal < bVal) return direction === 'asc' ? -1 : 1;
                if (aVal > bVal) return direction === 'asc' ? 1 : -1;
                return 0;
            }
        }
    });

    function loadParents() {
        $.get('../api/get_parents.php', function(res) {
            if (res.success) {
                parentsTableSorter.setData(res.data);
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
        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');
        const originalText = $submitBtn.text();
        
        // Show loading state
        $submitBtn.prop('disabled', true).text('Saving...');
        
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
                    showAlert(res.message || 'Parent saved successfully!', 'success');
                    loadParents();
                } else {
                    showAlert(res.message || 'Failed to save parent.', 'danger');
                }
            },
            error: function() {
                showAlert('Network error occurred. Please try again.', 'danger');
            },
            complete: function() {
                // Reset button state
                $submitBtn.prop('disabled', false).text(originalText);
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
        if (!confirm('Are you sure you want to delete this parent? This action cannot be undone.')) return;
        const id = $(this).data('id');
        const $btn = $(this);
        const originalText = $btn.text();
        
        // Show loading state
        $btn.prop('disabled', true).text('Deleting...');
        
        $.post('../api/delete_parent.php', {id}, function(res) {
            if (res.success) {
                // Show success message
                showAlert('Parent deleted successfully!', 'success');
                loadParents();
            } else {
                showAlert(res.message || 'Failed to delete parent.', 'danger');
            }
        }, 'json').fail(function() {
            showAlert('Network error occurred. Please try again.', 'danger');
        }).always(function() {
            // Reset button state
            $btn.prop('disabled', false).text(originalText);
        });
    });



}); 