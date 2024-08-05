<?= $this->extend('layouts/dashboard-layout'); ?>

<?= $this->section('container-fluid'); ?>
<h1 class="mb-3">Management User</h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="border-0">
                    <div class="d-lg-flex mb-2">
                        <div>
                            <h5 class="mb-0">Tabel Users</h5>
                            <p class="text-sm mb-2">
                                Menampilkan seluruh data users.
                            </p>
                        </div>
                        <div class="ml-auto ms-auto my-auto mt-lg-0 mt-4 ">
                            <div class="ms-auto my-auto ">
                                <button id="addUserBtn" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addUserModal">Tambah Pengguna</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-100">
                    <?php if (session()->has('msg')) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?= session()->get('msg'); ?>
                        </div>
                    <?php endif; ?>
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Nickname</th>
                                <th>Busa</th>
                                <th>Level</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user) : ?>
                                <tr>
                                    <td><?= esc($user['id']); ?></td>
                                    <td><?= esc($user['username']); ?></td>
                                    <td><?= esc($user['nickname']); ?></td>
                                    <td><?= esc($user['busa']); ?></td>
                                    <td>
                                        <?php
                                        if ($user['level'] == 'admin') {
                                            echo '<span class="badge bg-primary">Admin</span>';
                                        } elseif ($user['level'] == 'pelaksana') {
                                            echo '<span class="badge bg-success">Unit Pelaksana</span>';
                                        } elseif ($user['level'] == 'wilayah') {
                                            echo '<span class="badge bg-warning text-dark">Unit Wilayah</span>';
                                        } else {
                                            echo esc($user['level']);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-view" data-id="<?= esc($user['id']); ?>" data-bs-toggle="modal" data-bs-target="#viewUserModal">Lihat</button>
                                        <button class="btn btn-success btn-edit" data-id="<?= esc($user['id']); ?>" data-username="<?= esc($user['username']); ?>" data-nickname="<?= esc($user['nickname']); ?>" data-level="<?= esc($user['level']); ?>" data-busa="<?= esc($user['busa']); ?>">Edit</button>
                                        <button class="btn btn-danger btn-delete" data-id="<?= esc($user['id']); ?>">Hapus</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel"><i class="uil-user-circle"></i> Tambah Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group mb-3">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
                            <div class="invalid-feedback username-error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="nickname">Nickname</label>
                            <input type="text" class="form-control" id="nickname" name="nickname" placeholder="Masukkan nickname" required>
                            <div class="invalid-feedback nickname-error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="busa">Busa</label>
                            <input type="number" class="form-control" id="busa" name="busa" placeholder="Masukkan nomor busa" required>
                            <div class="invalid-feedback busa-error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                            <div class="invalid-feedback password-error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password_confirm">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Konfirmasi password" required>
                            <div class="invalid-feedback password_confirm-error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="level">Level</label>
                            <select class="form-control" id="level" name="level" required>
                                <option disabled selected value="">Pilih Level</option>
                                <option value="admin">Admin</option>
                                <option value="pelaksana">Unit Pelaksana</option>
                                <option value="wilayah">Unit Wilayah</option>
                            </select>
                            <div class="invalid-feedback level-error"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="saveUserBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View User -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUserModalLabel">Detail User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="viewUsername" class="form-label">Username</label>
                    <input type="text" class="form-control" id="viewUsername" readonly>
                </div>
                <div class="mb-3">
                    <label for="viewNickname" class="form-label">Nickname</label>
                    <input type="text" class="form-control" id="viewNickname" readonly>
                </div>
                <div class="mb-3">
                    <label for="viewBusa" class="form-label">Busa</label>
                    <input type="text" class="form-control" id="viewBusa" readonly>
                </div>
                <div class="mb-3">
                    <label for="viewLevel" class="form-label">Level</label>
                    <input type="text" class="form-control" id="viewLevel" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">
                    <i class="uil-user-circle"></i> Edit Pengguna
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="editUserId" name="id">
                        <div class="form-group mb-3">
                            <label for="editUsername">Username</label>
                            <input type="text" class="form-control" id="editUsername" name="username" required>
                            <div class="invalid-feedback username-error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="editNickname">Nickname</label>
                            <input type="text" class="form-control" id="editNickname" name="nickname" required>
                            <div class="invalid-feedback nickname-error"></div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="editPassword">Password</label>
                            <input type="password" class="form-control" id="editPassword" name="password">
                            <small class="form-text text-muted">Kosongkan password jika tidak ingin mengubah password</small>
                            <div class="invalid-feedback password-error"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="editBusa">Busa</label>
                            <input type="number" class="form-control" id="editBusa" name="busa" required>
                            <div class="invalid-feedback busa-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="editLevel">Level</label>
                            <select class="form-control" id="editLevel" name="level" required>
                                <option value="">Pilih Level</option>
                                <option value="admin">Admin</option>
                                <option value="pelaksana">Unit Pelaksana</option>
                                <option value="wilayah">Unit Wilayah</option>
                            </select>
                            <div class="invalid-feedback level-error"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="editUserBtn">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>

<script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/sweetalert2/sweetalert2.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        var role = '<?= session()->get('level') ?>'; // Get role dari session
        var baseUrl = '<?= site_url() ?>'; // Base URL dari CI 4

        // Membuat URL menggunakan template string
        var createUrl = `${baseUrl}${role}/users/create`;
        var editUrl = `${baseUrl}${role}/users/edit/`;
        var updateDataUrl = `${baseUrl}${role}/realisasi/updateData`;
        var deleteUrl = `${baseUrl}${role}/users/delete/`;
        var viewUrl = `${baseUrl}${role}/users/view/`;

        // Fungsi untuk memvalidasi form
        function validateForm(formId) {
            var isValid = true;
            $('#' + formId + ' input, #' + formId + ' select').each(function() {
                if ($(this).val() === '') {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            return isValid;
        }


        $('.btn-view').click(function() {
            var userId = $(this).data('id');
            console.log(userId);
            $.ajax({
                url: viewUrl + userId,
                type: 'GET',

                dataType: 'json',

                success: function(response) {
                    if (response.success) {
                        $('#viewUsername').val(response.data.username);
                        $('#viewNickname').val(response.data.nickname);
                        $('#viewBusa').val(response.data.busa);
                        $('#viewLevel').val(response.data.level);
                        $('#viewUserModal').modal('show');
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {

                    alert('Terjadi kesalahan saat mengambil data user');
                }
            });
        });

        // Tambah Pengguna
        $('#addUserBtn').click(function() {
            $('#addUserModal').modal('show');
        });

        function validateUsername(username) {
            return !/\s/.test(username);
        }

        $('#saveUserBtn').click(function() {
            if (!validateForm('addUserForm')) {
                return;
            }

            var username = $('#username').val();
            if (!validateUsername(username)) {
                $('.username-error').html('Username tidak boleh mengandung spasi');
                $('#username').addClass('is-invalid');
                return;
            }

            var formData = $('#addUserForm').serialize();
            $.ajax({
                url: createUrl,
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
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
                                title: 'Gagal',
                                text: response.message,
                            });
                        }
                    }
                }
            });
        });

        $('.btn-edit').click(function() {
            var id = $(this).data('id');
            var username = $(this).data('username');
            var nickname = $(this).data('nickname');
            var level = $(this).data('level');
            var busa = $(this).data('busa'); // Tambahkan baris ini
            $('#editUserId').val(id);
            $('#editUsername').val(username);
            $('#editNickname').val(nickname);
            $('#editLevel').val(level);
            $('#editBusa').val(busa); // Tambahkan baris ini
            $('#editUserModal').modal('show');
        });

        $('#editUserBtn').click(function() {
            if (!validateForm('editUserForm')) {
                return;
            }

            var username = $('#editUsername').val();
            if (!validateUsername(username)) {
                $('.username-error').html('Username tidak boleh mengandung spasi');
                $('#editUsername').addClass('is-invalid');
                return;
            }

            var formData = $('#editUserForm').serialize();
            var id = $('#editUserId').val();
            $.ajax({
                url: editUrl + id,
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
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
                                title: 'Gagal',
                                text: response.message,
                            });
                        }
                    }
                }
            });
        });

        // Hapus Pengguna
        $('.btn-delete').click(function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda tidak akan dapat mengembalikan data ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl + id,
                        method: 'POST',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
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