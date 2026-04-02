/**
 * Business Listing & Rating System
 * Main JavaScript Application
 */

$(document).ready(function() {
    // Initialize application
    loadBusinesses();
    initRatingStars();

    // Add Business button click
    $('#btnAddBusiness').click(function() {
        $('#addBusinessForm')[0].reset();
        $('#addBusinessModal').modal('show');
    });

    // Add Business form submit
    $('#addBusinessForm').submit(function(e) {
        e.preventDefault();
        saveBusiness();
    });

    // Edit Business form submit
    $('#editBusinessForm').submit(function(e) {
        e.preventDefault();
        updateBusiness();
    });

    // Delete confirmation
    $('#btnConfirmDelete').click(function() {
        deleteBusiness();
    });

    // Rating form submit
    $('#ratingForm').submit(function(e) {
        e.preventDefault();
        submitRating();
    });

    // Edit button click (delegated)
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        loadBusinessForEdit(id);
    });

    // Delete button click (delegated)
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#deleteId').val(id);
        $('#deleteBusinessName').text(name);
        $('#deleteConfirmModal').modal('show');
    });

    // Rating click on table (delegated)
    $(document).on('click', '.rating-cell .raty-star', function() {
        const id = $(this).closest('tr').find('.btn-edit').data('id');
        const name = $(this).closest('tr').find('td:nth-child(2)').text();
        openRatingModal(id, name);
    });
});

/**
 * Initialize Raty stars in rating modal
 */
function initRatingStars() {
    $('#ratingStars').raty({
        score: 0,
        half: true,
        halfShow: true,
        starType: 'i',
        starOff: 'bi bi-star fs-4 text-muted',
        starOn: 'bi bi-star-fill fs-4 text-warning',
        starHalf: 'bi bi-star-half fs-4 text-warning',
        click: function(score) {
            $('#ratingValue').val(score);
        }
    });
}

/**
 * Load all businesses via AJAX
 */
function loadBusinesses() {
    $.ajax({
        url: 'api/business.php?action=list',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderBusinessTable(response.data);
            } else {
                showAlert('danger', 'Failed to load businesses');
            }
        },
        error: function(xhr) {
            showAlert('danger', 'Error loading businesses: ' + (xhr.responseJSON?.error || 'Server error'));
        }
    });
}

/**
 * Render business table with data
 */
function renderBusinessTable(businesses) {
    const tbody = $('#businessTableBody');
    tbody.empty();

    if (businesses.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    No businesses found. Click "Add Business" to create one.
                </td>
            </tr>
        `);
        return;
    }

    businesses.forEach(function(business) {
        const row = createBusinessRow(business);
        tbody.append(row);
        initRowRaty(business.id, parseFloat(business.avg_rating));
    });
}

/**
 * Create a table row for a business
 */
function createBusinessRow(business) {
    return `
        <tr id="business-row-${business.id}" class="fade-in">
            <td>${business.id}</td>
            <td>${escapeHtml(business.name)}</td>
            <td>${escapeHtml(business.address)}</td>
            <td>${escapeHtml(business.phone)}</td>
            <td>${escapeHtml(business.email)}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary btn-action btn-edit" data-id="${business.id}" title="Edit">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger btn-action btn-delete" data-id="${business.id}" data-name="${escapeHtml(business.name)}" title="Delete">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
            <td class="rating-cell">
                <div class="rating-container">
                    <div id="raty-${business.id}" class="raty-star" data-score="${business.avg_rating}" data-business-id="${business.id}"></div>
                    <span class="rating-text" id="rating-text-${business.id}">${business.avg_rating}</span>
                </div>
            </td>
        </tr>
    `;
}

/**
 * Initialize Raty for a table row
 */
function initRowRaty(businessId, avgRating) {
    $(`#raty-${businessId}`).raty({
        score: avgRating,
        readOnly: true,
        half: true,
        halfShow: true,
        starType: 'i',
        starOff: 'bi bi-star text-muted',
        starOn: 'bi bi-star-fill text-warning',
        starHalf: 'bi bi-star-half text-warning',
        click: function(score) {
            openRatingModal(businessId, $(`#business-row-${businessId} td:nth-child(2)`).text());
        }
    });
}

/**
 * Update Raty score for a business row
 */
function updateRowRaty(businessId, avgRating) {
    $(`#raty-${businessId}`).raty('score', avgRating);
    $(`#rating-text-${businessId}`).text(avgRating);
}

/**
 * Save new business
 */
function saveBusiness() {
    const formData = $('#addBusinessForm').serialize();
    const btn = $('#btnSaveBusiness');
    
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

    $.ajax({
        url: 'api/business.php?action=create',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#addBusinessModal').modal('hide');
                $('#addBusinessForm')[0].reset();
                addBusinessRow(response.data);
                showAlert('success', 'Business added successfully!');
            } else {
                showAlert('danger', response.error || 'Failed to add business');
            }
        },
        error: function(xhr) {
            showAlert('danger', 'Error: ' + (xhr.responseJSON?.error || 'Server error'));
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Save Business');
        }
    });
}

/**
 * Add a new row to the table
 */
function addBusinessRow(business) {
    const row = createBusinessRow(business);
    
    const tbody = $('#businessTableBody');
    
    if (tbody.find('tr td[colspan]').length > 0) {
        tbody.empty();
    }
    
    tbody.prepend(row);
    initRowRaty(business.id, parseFloat(business.avg_rating));
}

/**
 * Load business data for editing
 */
function loadBusinessForEdit(id) {
    $.ajax({
        url: 'api/business.php?action=get&id=' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const business = response.data;
                $('#editId').val(business.id);
                $('#editName').val(business.name);
                $('#editAddress').val(business.address);
                $('#editPhone').val(business.phone);
                $('#editEmail').val(business.email);
                $('#editBusinessModal').modal('show');
            } else {
                showAlert('danger', response.error || 'Failed to load business');
            }
        },
        error: function(xhr) {
            showAlert('danger', 'Error: ' + (xhr.responseJSON?.error || 'Server error'));
        }
    });
}

/**
 * Update business
 */
function updateBusiness() {
    const id = $('#editId').val();
    const formData = $('#editBusinessForm').serialize();
    const btn = $('#btnUpdateBusiness');
    
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Updating...');

    $.ajax({
        url: 'api/business.php?action/update&id=' + id,
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#editBusinessModal').modal('hide');
                updateBusinessRow(response.data);
                showAlert('success', 'Business updated successfully!');
            } else {
                showAlert('danger', response.error || 'Failed to update business');
            }
        },
        error: function(xhr) {
            showAlert('danger', 'Error: ' + (xhr.responseJSON?.error || 'Server error'));
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Update Business');
        }
    });
}

/**
 * Update existing row in the table
 */
function updateBusinessRow(business) {
    const row = $(`#business-row-${business.id}`);
    row.fadeOut(200, function() {
        const newRow = createBusinessRow(business);
        row.replaceWith(newRow);
        $(`#business-row-${business.id}`).hide().fadeIn(200);
        initRowRaty(business.id, parseFloat(business.avg_rating));
    });
}

/**
 * Delete business
 */
function deleteBusiness() {
    const id = $('#deleteId').val();
    const btn = $('#btnConfirmDelete');
    const row = $(`#business-row-${id}`);
    
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Deleting...');

    $.ajax({
        url: 'api/business.php?action=delete&id=' + id,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#deleteConfirmModal').modal('hide');
                row.fadeOut(300, function() {
                    $(this).remove();
                    if ($('#businessTableBody tr').length === 0) {
                        $('#businessTableBody').append(`
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No businesses found. Click "Add Business" to create one.
                                </td>
                            </tr>
                        `);
                    }
                });
                showAlert('success', 'Business deleted successfully!');
            } else {
                showAlert('danger', response.error || 'Failed to delete business');
            }
        },
        error: function(xhr) {
            showAlert('danger', 'Error: ' + (xhr.responseJSON?.error || 'Server error'));
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="bi bi-trash me-1"></i>Delete');
        }
    });
}

/**
 * Open rating modal
 */
function openRatingModal(businessId, businessName) {
    $('#ratingBusinessId').val(businessId);
    $('#ratingBusinessName').text(businessName);
    $('#ratingForm')[0].reset();
    $('#ratingStars').raty('score', 0);
    $('#ratingValue').val(0);
    $('#ratingModal').modal('show');
}

/**
 * Submit rating
 */
function submitRating() {
    const businessId = $('#ratingBusinessId').val();
    const formData = $('#ratingForm').serialize();
    const btn = $('#btnSubmitRating');
    
    if ($('#ratingValue').val() == 0) {
        showAlert('warning', 'Please select a rating');
        return;
    }
    
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Submitting...');

    $.ajax({
        url: 'api/rating.php?action=submit&business_id=' + businessId,
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#ratingModal').modal('hide');
                updateRowRaty(businessId, response.avg_rating);
                showAlert('success', response.message + (response.action === 'updated' ? ' (Rating updated)' : ''));
            } else {
                showAlert('danger', response.error || 'Failed to submit rating');
            }
        },
        error: function(xhr) {
            showAlert('danger', 'Error: ' + (xhr.responseJSON?.error || 'Server error'));
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="bi bi-star me-1"></i>Submit Rating');
        }
    });
}

/**
 * Show alert message
 */
function showAlert(type, message) {
    const alertId = 'alert-' + Date.now();
    const alertHtml = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999; min-width: 300px;" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${escapeHtml(message)}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    $('body').append(alertHtml);
    
    setTimeout(function() {
        $(`#${alertId}`).fadeOut(300, function() {
            $(this).remove();
        });
    }, 4000);
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
