<?= $this->extend('layouts/dashboard-layout'); ?>
<?= $this->section('container-fluid'); ?>


<h1 class="mb-3">Management Categories</h1>


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="border-0">
                    <div class="d-lg-flex mb-2">
                        <div>
                            <h5 class="mb-0">Tabel Categories</h5>
                            <p class="text-sm mb-2">
                                Menampilkan seluruh data categories/Jenis biaya.
                            </p>
                        </div>
                        <div class="ml-auto ms-auto my-auto mt-lg-0 mt-4 ">
                            <div class="ms-auto my-auto ">
                                <button id="addCategoryBtn" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addCategoryModal">Add Category</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-100">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <!-- <th>ID</th> -->
                                <th>G/L Category</th>
                                <th>G/L Acct Long Text</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 1;
                            foreach ($categories as $category) : ?>
                                <tr>
                                    <td><?= $counter; ?></td>
                                    <!-- <td><?= esc($category['id']); ?></td> -->
                                    <td><?= esc($category['gl_account']); ?></td>
                                    <td><?= esc($category['gl_long_text']); ?></td>
                                    <td>
                                        <button class="btn btn-success btn-edit" data-id="<?= esc($category['id']); ?>" data-gl-account="<?= esc($category['gl_account']); ?>" data-gl-long-text="<?= esc($category['gl_long_text']); ?>">Edit</button>
                                        <button class="btn btn-danger btn-delete" data-id="<?= esc($category['id']); ?>">Delete</button>
                                    </td>
                                </tr>
                            <?php
                                $counter++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel"><i class="uil-apps"></i> Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="addCategoryForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group mb-3">
                            <label for="gl_account">G/L Category</label>
                            <input type="text" class="form-control" id="gl_account" name="gl_account" placeholder="Masukan Nomor G/L Category" required>
                            <div class="invalid-feedback gl_account-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="gl_long_text">G/L Acct Long Text</label>
                            <textarea class="form-control" id="gl_long_text" name="gl_long_text" placeholder="Masukan G/L Acct Long Text" required></textarea>
                            <div class="invalid-feedback gl_long_text-error"></div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel"><i class="uil-apps"></i> Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="editCategoryId" name="id">
                        <div class="form-group mb-3">
                            <label for="editGLAccount">GL Account</label>
                            <input type="text" class="form-control" id="editGLAccount" name="gl_account" required>
                            <div class="invalid-feedback gl_account-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="editGLLongText">GL Long Text</label>
                            <textarea class="form-control" id="editGLLongText" name="gl_long_text" required></textarea>
                            <div class="invalid-feedback gl_long_text-error"></div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editCategoryBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>

<script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>

<!-- Sweet Alerts js Local -->
<script src="<?= base_url('assets/libs/sweetalert2/sweetalert2.min.js') ?>"></script>

<script>
    $(document).ready(function() {

        var role = '<?= session()->get('level') ?>'; // Get role dari session
        var baseUrl = '<?= site_url() ?>'; // Base URL dari CI 4

        // Membuat URL menggunakan template string
        var createUrl = `${baseUrl}${role}/categories/create`;
        var editUrl = `${baseUrl}${role}/categories/edit/`;
        var deleteUrl = `${baseUrl}${role}/categories/delete/`;


        $('#addCategoryBtn').click(function() {
            $('#addCategoryModal').modal('show');
        });

        $('#saveCategoryBtn').click(function() {
            var formData = $('#addCategoryForm').serialize();
            $.ajax({
                url: createUrl,
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        // Menampilkan pesan error validasi
                        if (response.errors) {
                            $.each(response.errors, function(key, value) {
                                $('.' + key + '-error').html(value);
                                $('[name="' + key + '"]').addClass('is-invalid');
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                            });
                        }
                    }
                }
            });
        });

        // Edit category
        $('.btn-edit').click(function() {
            var id = $(this).data('id');
            var glAccount = $(this).data('gl-account');
            var glLongText = $(this).data('gl-long-text');
            $('#editCategoryId').val(id);
            $('#editGLAccount').val(glAccount);
            $('#editGLLongText').val(glLongText);
            $('#editCategoryModal').modal('show');
        });

        $('#editCategoryBtn').click(function() {
            var formData = $('#editCategoryForm').serialize();
            var id = $('#editCategoryId').val();
            $.ajax({
                url: editUrl + id,
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        // Menampilkan pesan error validasi
                        if (response.errors) {
                            $.each(response.errors, function(key, value) {
                                $('.' + key + '-error').html(value);
                                $('[name="' + key + '"]').addClass('is-invalid');
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                            });
                        }
                    }
                }
            });
        });

        // Delete category
        $('.btn-delete').click(function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl + id,
                        method: 'POST',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                });
                            }
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>