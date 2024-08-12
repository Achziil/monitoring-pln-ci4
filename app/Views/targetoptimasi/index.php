<?= $this->extend('layouts/dashboard-layout'); ?>

<?= $this->section('container-fluid'); ?>

<h1 class="mb-3">Target Optimasi</h1>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center mb-3">
                    <div class="col-md-6">
                        <h5 class="mb-0">Tabel Target Optimasi</h5>
                        <p class="text-sm mb-2">
                            Menampilkan seluruh data target optimasi (inserted by Unit Wilayah).
                        </p>
                    </div>
                    <?php if ($userLevel === 'admin' || $userLevel === 'wilayah') : ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="busa" class="form-label">Unit</label>
                                <select name="busaFilter" id="busaFilter" class="form-control">
                                    <!-- <option value="7600">7600</option> -->
                                    <?php foreach ($busaOptions as $option) : ?>
                                        <option value="<?= $option['busa'] ?>"><?= $option['busa'] ?> - <?= $option['nickname'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row mt-2">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="busa" class="form-label">Tahun</label>
                                        <select name="tahun" id="tahunFilter" class="form-control">
                                            <option value="" disabled selected>-- pilih tahun --</option>
                                            <?php foreach ($listTahun as $option) : ?>
                                                <option value="<?= $option->tahun ?>"><?= $option->tahun ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-group">
                                        <label for="busa" class="form-label">Bulan</label>
                                        <select name="bulan" id="bulanFilter" class="form-control">
                                            <option value="" disabled selected>-- pilih bulan --</option>
                                            <option value="01">Januari</option>
                                            <option value="02">Februari</option>
                                            <option value="03">Maret</option>
                                            <option value="04">April</option>
                                            <option value="05">Mei</option>
                                            <option value="06">Juni</option>
                                            <option value="07">Juli</option>
                                            <option value="08">Agustus</option>
                                            <option value="09">September</option>
                                            <option value="10">Oktober</option>
                                            <option value="11">November</option>
                                            <option value="12">Desember</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-md-end mt-3">
                            <!-- <button id="createTargetOptimasiButton" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createTargetOptimasiModal">Add Target Optimasi</button> -->
                            <button id="createMultiMonthTargetOptimasiButton" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#multiMonthTargetOptimasiModal">Tambah Target Optimasi</button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="w-100">
                    <table id="datatableQuery" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>G/L Acct Long Text</th>
                                <th>Unit</th>
                                <th>Bulan</th>
                                <th>Target Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <!-- Tabel tambahan untuk menampilkan total per kategori -->
                <div class="w-100">
                <h5>Total Optimasi per Kategori</h5>
                <table id="totalPerKategoriTable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>G/L Acct Long Text</th>
                            <th>Total Target Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan di-populate menggunakan JavaScript -->
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Target Optimasi Modal -->
<div class="modal fade" id="createTargetOptimasiModal" tabindex="-1" role="dialog" aria-labelledby="createTargetOptimasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTargetOptimasiModalLabel">Create Target Optimasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createTargetOptimasiForm">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="busa">Unit</label>
                        <select class="form-control" id="busa" name="busa" required>
                            <option selected disabled value="">Pilih Busa</option>
                            <?php foreach ($busaOptions as $option) : ?>
                                <option value="<?= $option['busa'] ?>"><?= $option['busa'] ?> - <?= $option['nickname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="bulan">Bulan</label>
                        <input type="month" class="form-control" id="bulan" name="bulan" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="category_id">G/L Acct Long Text</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?= $category['id'] ?>"><?= $category['gl_long_text'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="target_amount">Target Amount</label>
                        <div class="input-group">
                            <div class="input-group-text">Rp</div>
                            <input type="text" class="form-control" id="target_amount" name="target_amount" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Multi-Month Target Optimasi Modal -->
<div class="modal fade" id="multiMonthTargetOptimasiModal" tabindex="-1" role="dialog" aria-labelledby="multiMonthTargetOptimasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="multiMonthTargetOptimasiModalLabel">Menambahkan Target Optimasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="multiMonthTargetOptimasiForm">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="multiBusa">Unit</label>
                        <select class="form-control" id="multiBusa" name="busa" required>
                            <option selected disabled value="">Pilih Busa</option>
                            <?php foreach ($busaOptions as $option) : ?>
                                <option value="<?= $option['busa'] ?>"><?= $option['busa'] ?> - <?= $option['nickname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="multiCategoryId">G/L Acct Long Text</label>
                        <select class="form-control" id="multiCategoryId" name="category_id" required>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?= $category['id'] ?>"><?= $category['gl_long_text'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Container untuk input bulan dan jumlah -->
                    <div id="multiMonthInputs">
                        <label>Bulan dan Target</label>
                        <!-- Javascript akan menambahkan input di sini -->
                    </div>
                    <button type="button" class="btn btn-primary" onclick="addMonthInput()">Add Month</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Target Optimasi Modal -->
<div class="modal fade" id="editTargetOptimasiModal" tabindex="-1" role="dialog" aria-labelledby="editTargetOptimasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTargetOptimasiModalLabel">Edit Target Optimasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTargetOptimasiForm">
                <div class="modal-body">
                    <input type="hidden" id="editTargetOptimasiId" name="id">
                    <div class="form-group mb-3">
                        <label for="editBusa">Busa</label>
                        <select class="form-control" id="editBusa" name="busa" required>
                            <option selected disabled value="">Pilih Busa</option>
                            <?php foreach ($busaOptions as $option) : ?>
                                <option value="<?= $option['busa'] ?>"><?= $option['busa'] ?> - <?= $option['nickname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="editBulan">Bulan</label>
                        <input type="month" class="form-control" id="editBulan" name="bulan" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="editCategoryId">G/L Acct Long Text</label>
                        <select class="form-control" id="editCategoryId" name="category_id" required>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?= $category['id'] ?>"><?= $category['gl_long_text'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="editTargetAmount">Target Amount</label>
                        <div class="input-group">
                            <div class="input-group-text">Rp</div>
                            <input type="text" class="form-control" id="editTargetAmount" name="target_amount" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
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

function resetForm(formId) {
    $('#' + formId)[0].reset();
    $('#' + formId + ' .input-group-text').next('input').val(''); // Reset custom input fields if any
    $('#' + formId + ' .is-invalid').removeClass('is-invalid'); // Clear any validation states
}

    function addMonthInput() {
    const container = document.getElementById('multiMonthInputs');
    const inputGroup = document.createElement('div');
    inputGroup.classList.add('row', 'mb-2', 'month-input-group');

    if (container.children.length === 0) {
        inputGroup.classList.add('first-month-input');
    }

    inputGroup.innerHTML = `
        <div class="col">
            <input type="month" class="form-control" name="bulan[]" required>
        </div>
        <div class="col">
            <div class="input-group">
                <div class="input-group-text">Rp</div>
                <input type="text" class="form-control" name="target_amount[]" required placeholder="Masukkan Jumlah Rupiah">
            </div>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-danger remove-month-btn">&times;</button>
        </div>
    `;
    container.appendChild(inputGroup);
    }

    document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('remove-month-btn')) {
        const inputGroups = document.querySelectorAll('.month-input-group');
        if (inputGroups.length > 1) {
            e.target.closest('.month-input-group').remove();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Bisa Dihapus',
                text: 'Setidaknya harus ada satu bulan yang terdaftar.',
            });
        }
    }
    });

    // Ensure at least one month input is present on modal open
    $('#multiMonthTargetOptimasiModal').on('shown.bs.modal', function () {
    const container = document.getElementById('multiMonthInputs');
    if (container.children.length === 0) {
        addMonthInput();
    }
    });

    // Format nilai input target_amount menjadi format IDR
    $('#target_amount, #editTargetAmount').on('input', function() {
        var amount = $(this).val().replace(/\D/g, '');
        $(this).val(formatIDR(amount));
    });

    // Fungsi untuk memformat angka menjadi format IDR tanpa simbol "Rp"
    function formatIDR(amount) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }

    // Format nilai input target_amount[] menjadi format IDR
    $(document).on('input', 'input[name="target_amount[]"]', function() {
        var amount = $(this).val().replace(/\D/g, '');
        $(this).val(formatIDR(amount));
    });

    

    $(document).ready(function() {
        var role = '<?= session()->get('level') ?>'; // Get role dari session
        var baseUrl = '<?= site_url() ?>'; // Base URL dari CI 4

        addMonthInput();

        // Membuat URL menggunakan template string
        var targetOptimasiUrl = `${baseUrl}${role}/targetoptimasi/ajaxList`;
        var saveUrl = `${baseUrl}${role}/targetoptimasi/save`;
        var savemultiUrl = `${baseUrl}${role}/targetoptimasi/savemulti`;
        var editUrl = `${baseUrl}${role}/targetoptimasi/edit/`;
        var updateUrl = `${baseUrl}${role}/targetoptimasi/update/`;
        var deleteUrl = `${baseUrl}${role}/targetoptimasi/delete/`;
        var totalPerKategoriUrl = `${baseUrl}${role}/targetoptimasi/totalPerKategori`;

        // Fungsi fetchtotal
        function fetchTotalPerKategori(busa) {
            $.ajax({
                url: totalPerKategoriUrl,
                method: 'POST',
                data: { busa: busa}, // Gunakan busa dari filter atau userBusa
                success: function(response) {
                    var data = response.data;
                    var tbody = $('#totalPerKategoriTable tbody');
                    tbody.empty();

                    $.each(data, function(index, item) {
                        var row = '<tr>';
                        row += '<td>' + item.gl_long_text + '</td>';
                        row += '<td>' + formatIDR(item.total_target_amount) + '</td>';
                        row += '</tr>';
                        tbody.append(row);
                    });
                }
            });
        }

        var table = $('#datatableQuery').DataTable({
            pageLength : 12,
            processing: true,
            serverSide: true,
            ajax: {
                url: targetOptimasiUrl,
                type: 'POST',
                data: function(d) {
                <?php if ($userLevel === 'admin' || $userLevel === 'wilayah') : ?>
                    $('#busaFilter').on('change', function() {
                        var selectedBusa = $(this).val();
                        table.ajax.url(targetOptimasiUrl + '?busaFilter=' + selectedBusa).load();
                        fetchTotalPerKategori(selectedBusa);
                    });
                    d.busaFilter = $('#busaFilter').val();
                    d.tahun = $('#tahunFilter').val();
                    d.bulan = $('#bulanFilter').val();
                <?php endif; ?>
            }
            },
            "columns": [{
                    "data": "0",
                    "orderable": false
                },
                {
                    "data": "1"
                },
                {
                    "data": "2"
                },
                {
                    "data": "3"
                },
                {
                    "data": "4"
                },
                {
                    "data": "5",
                    "orderable": false
                }
            ],
            "order": [
                [2, 'desc']
            ]
        });

        <?php if ($userLevel === 'admin' || $userLevel === 'wilayah') : ?>
            $('#busaFilter').on('change', function() {
                var selectedBusa = $('#busaFilter').val();
                var selectedTahun = $("#tahunFilter").val();
                var selectedBulan = $("#bulanFilter").val();
                table.ajax.url(targetOptimasiUrl + '?busaFilter=' + selectedBusa + '&tahun=' + selectedTahun + '&bulan=' + selectedBulan).load();
                fetchTotalPerKategori(selectedBusa);
            });

            $('#tahunFilter').on('change', function() {
                var selectedBusa = $('#busaFilter').val();
                var selectedTahun = $("#tahunFilter").val();
                var selectedBulan = $("#bulanFilter").val();
                table.ajax.url(targetOptimasiUrl + '?busaFilter=' + $('#busaFilter').val() + '&tahun=' + selectedTahun + '&bulan=' + selectedBulan).load();
            });

            $('#bulanFilter').on('change', function() {
                var selectedBusa = $('#busaFilter').val();
                var selectedTahun = $("#tahunFilter").val();
                var selectedBulan = $("#bulanFilter").val();
                table.ajax.url(targetOptimasiUrl + '?busaFilter=' + $('#busaFilter').val() + '&tahun=' + selectedTahun + '&bulan=' + selectedBulan).load();
            });
        <?php endif; ?>

        // Create Target Optimasi
        $('#createTargetOptimasiForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serializeArray();

            // Validasi form front end
            var isValid = true;
            $.each(formData, function(index, field) {
                if (field.value === '') {
                    isValid = false;
                    $('[name="' + field.name + '"]').addClass('is-invalid');
                } else {
                    $('[name="' + field.name + '"]').removeClass('is-invalid');
                }
            });

            if (isValid) {
                var amount = parseFloat($('#target_amount').val().replace(/\D/g, ''));
                formData.find(function(item) {
                    return item.name === 'target_amount';
                }).value = amount;

                $.ajax({
                    url: saveUrl,
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                            }).then(function() {
                                $('#createTargetOptimasiModal').modal('hide');
                                $('#createTargetOptimasiForm')[0].reset();
                                $('#datatableQuery').DataTable().ajax.reload();
                                fetchTotalPerKategori($('#busaFilter').val());
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

        // Create Multiple Month Target Optimasi
        $('#multiMonthTargetOptimasiForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serializeArray();

            // Validasi form di front end
            var isValid = true;
            $.each(formData, function(index, field) {
                if (field.value === '') {
                    isValid = false;
                    $('[name="' + field.name + '"]').addClass('is-invalid');
                } else {
                    $('[name="' + field.name + '"]').removeClass('is-invalid');
                }
            });

            // Memastikan bahwa setiap target amount adalah angka yang valid
            $('input[name="target_amount[]"]').each(function() {
                var amount = parseFloat($(this).val().replace(/\D/g, ''));
                if (isNaN(amount)) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).val(amount);  // memperbarui nilai dengan angka yang bersih
                    $(this).removeClass('is-invalid');
                }
            });

            if (isValid) {
                // Memformat data untuk dikirim
                var postData = $(this).serialize();  // Menggunakan serialize() untuk menghandle data array

                $.ajax({
                    url: savemultiUrl,  // Sesuaikan dengan URL yang tepat
                    method: 'POST',
                    data: postData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                            }).then(function() {
                                $('#multiMonthTargetOptimasiModal').modal('hide');
                                $('#multiMonthTargetOptimasiForm')[0].reset();
                                $('#datatableQuery').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan server, coba lagi nanti.',
                        });
                    }
                });
            }
        });

        $('#multiMonthTargetOptimasiModal').on('hidden.bs.modal', function () {
        // Cari container yang berisi input fields
        var container = document.getElementById('multiMonthInputs');
        // Hapus semua input kecuali template awal
        $(container).empty();

        // Tambahkan kembali satu set input default untuk penggunaan berikutnya
        addMonthInput();  // Fungsi ini menambahkan satu set input baru
        });

        // Confirm close and reset for Single Month Target Optimasi Modal
        $('#createTargetOptimasiModal .btn-close, #createTargetOptimasiModal .modal-footer .btn-secondary').click(function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "Changes you made may not be saved.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, close it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#createTargetOptimasiModal').modal('hide');
                    resetForm('createTargetOptimasiForm'); // Reset form jika modal ditutup
                }
            });
        });

        // Confirm close and reset for Multi-Month Target Optimasi Modal
        $('#multiMonthTargetOptimasiModal .btn-close, #multiMonthTargetOptimasiModal .modal-footer .btn-secondary').click(function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "Changes you made may not be saved.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, close it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#multiMonthTargetOptimasiModal').modal('hide');
                    resetForm('multiMonthTargetOptimasiForm'); // Reset form jika modal ditutup
                    $('#multiMonthInputs').empty(); // Khusus untuk multi-month, kosongkan semua input tambahan
                    addMonthInput(); // Tambahkan kembali satu set input default
                }
            });
        });

        // Edit Target Optimasi
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: editUrl + id,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        $('#editTargetOptimasiId').val(data.id);
                        $('#editBusa').val(data.busa);
                        $('#editBulan').val(data.bulan.substring(0, 7)); //format 'YYYY-MM'
                        $('#editCategoryId').val(data.category_id);
                        $('#editTargetAmount').val(formatIDR(data.target_amount));
                        $('#editTargetOptimasiModal').modal('show');
                    }
                }
            });
        });

        // Edit Target Optimasi
        $('#editTargetOptimasiForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serializeArray();
            var id = $('#editTargetOptimasiId').val();

            // Validasi form
            var isValid = true;
            $.each(formData, function(index, field) {
                if (field.value === '') {
                    isValid = false;
                    $('[name="' + field.name + '"]').addClass('is-invalid');
                } else {
                    $('[name="' + field.name + '"]').removeClass('is-invalid');
                }
            });

            if (isValid) {
                var amount = parseFloat($('#editTargetAmount').val().replace(/\D/g, ''));
                formData.find(function(item) {
                    return item.name === 'target_amount';
                }).value = amount;

                $.ajax({
                    url: updateUrl + id,
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                            }).then(function() {
                                $('#editTargetOptimasiModal').modal('hide');
                                $('#editTargetOptimasiForm')[0].reset();
                                $('#datatableQuery').DataTable().ajax.reload();
                                fetchTotalPerKategori($('#busaFilter').val());
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

        // Delete Target Optimasi
        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus target optimasi ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
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
                                    text: 'Target optimasi berhasil dihapus',
                                }).then(function() {
                                    $('#datatableQuery').DataTable().ajax.reload();
                                    fetchTotalPerKategori($('#busaFilter').val());
                                });
                            }
                        }
                    });
                }
            });
        });
        // Fetch initial total per kategori
    fetchTotalPerKategori($('#busaFilter').val());
    });
</script>
<?= $this->endSection(); ?>