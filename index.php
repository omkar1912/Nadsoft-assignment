<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Listing & Rating System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Raty CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/raty/3.1.1/jquery.raty.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-building me-2"></i>Business Listing
                    </h1>
                    <button type="button" class="btn btn-primary" id="btnAddBusiness">
                        <i class="bi bi-plus-circle me-1"></i>Add Business
                    </button>
                </div>
                <hr>
            </div>
        </div>

        <!-- Business Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="businessTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Business Name</th>
                                        <th>Address</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                        <th>Average Rating</th>
                                    </tr>
                                </thead>
                                <tbody id="businessTableBody">
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Business Modal -->
    <div class="modal fade" id="addBusinessModal" tabindex="-1" aria-labelledby="addBusinessModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBusinessModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Add New Business
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addBusinessForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="addName" class="form-label">Business Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="addName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="addAddress" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="addAddress" name="address" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="addPhone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="addPhone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="addEmail" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="addEmail" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveBusiness">
                            <i class="bi bi-check-circle me-1"></i>Save Business
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Business Modal -->
    <div class="modal fade" id="editBusinessModal" tabindex="-1" aria-labelledby="editBusinessModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBusinessModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Edit Business
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editBusinessForm">
                    <input type="hidden" id="editId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Business Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAddress" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="editAddress" name="address" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editPhone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="editPhone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnUpdateBusiness">
                            <i class="bi bi-check-circle me-1"></i>Update Business
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">
                        <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete this business?</p>
                    <p class="mb-0 fw-bold" id="deleteBusinessName"></p>
                    <input type="hidden" id="deleteId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDelete">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Modal -->
    <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ratingModalLabel">
                        <i class="bi bi-star-fill me-2 text-warning"></i>Submit Rating
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="ratingForm">
                    <input type="hidden" id="ratingBusinessId" name="business_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <h6 class="text-muted" id="ratingBusinessName"></h6>
                        </div>
                        <div class="mb-3 text-center">
                            <label class="form-label">Your Rating <span class="text-danger">*</span></label>
                            <div id="ratingStars" class="my-2"></div>
                            <input type="hidden" id="ratingValue" name="rating" value="0">
                        </div>
                        <div class="mb-3">
                            <label for="ratingName" class="form-label">Your Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ratingName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="ratingEmail" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="ratingEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="ratingPhone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="ratingPhone" name="phone" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="btnSubmitRating">
                            <i class="bi bi-star me-1"></i>Submit Rating
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Raty Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raty/3.1.1/jquery.raty.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/app.js"></script>
</body>
</html>
