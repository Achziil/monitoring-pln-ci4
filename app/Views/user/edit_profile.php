<?= $this->extend('layouts/dashboard-layout') ?>

<?= $this->section('container-fluid') ?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <h4 class="card-title mb-4">Edit Profile</h4>
                <form id="editProfileForm">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="nickname" class="form-label">Nickname</label>
                        <input type="text" class="form-control" id="nickname" name="nickname" value="<?= $user['nickname'] ?>" disabled style="background-color: #e9ecef;">
                    </div>
                    <div class="mb-3">
                        <label for="busa" class="form-label">Busa</label>
                        <input type="text" class="form-control" id="busa" name="busa" value="<?= $user['busa'] ?>" disabled style="background-color: #e9ecef;">
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= $user['username'] ?>" required>
                        <div class="invalid-feedback" id="username-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Ganti Password Baru</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                        <div class="invalid-feedback" id="password-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password baru</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                        <div class="invalid-feedback" id="confirm_password-error"></div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2 float-end">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/sweetalert2/sweetalert2.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        var role = '<?= session()->get('level') ?>'; // Get role dari session
        var baseUrl = '<?= site_url() ?>'; // Base URL dari CI 4

        // Membuat URL menggunakan template string
        var updateProfileUrl = `${baseUrl}${role}/update_profile`;
        var logoutUrl = `${baseUrl}logout`;

        $('#editProfileForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serializeArray();
            var updateData = {};

            $.each(formData, function(i, field) {
                updateData[field.name] = field.value;
            });

            var usernameChanged = updateData.username !== '<?= $user['username'] ?>';
            var passwordChanged = updateData.password !== '';

            if (!usernameChanged && !passwordChanged) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Changes',
                    text: 'Tidak ada perubahan pada data profile.'
                });
                return;
            }

            var confirmationText = '';
            if (usernameChanged && passwordChanged) {
                confirmationText = 'Apakah Anda yakin ingin mengubah username dan password?';
            } else if (usernameChanged) {
                confirmationText = 'Apakah Anda yakin ingin mengubah username?';
            } else if (passwordChanged) {
                confirmationText = 'Apakah Anda yakin ingin mengubah password?';
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: confirmationText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: updateProfileUrl,
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Logout Akun',
                                    cancelButtonText: 'Tetap Login',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = logoutUrl;
                                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                                        location.reload();
                                    }
                                });
                            } else {
                                if (response.errors) {
                                    $.each(response.errors, function(key, value) {
                                        $('#' + key).addClass('is-invalid');
                                        $('#' + key + '-error').html(value);
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message
                                    });
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>