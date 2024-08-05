<?= $this->extend('layouts/dashboard-layout'); ?>
<?= $this->section('container-fluid'); ?>

<h1 class="mb-3">Sumber Data</h1>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="border-0">
                    <div class="d-lg-flex mb-2">
                        <div>
                            <h5 class="mb-0">Tabel Sumber Data</h5>
                            <p class="text-sm mb-2">
                                Menampilkan seluruh data sumber dari import excel (.xls/.xlsx)
                            </p>
                        </div>
                        <div class="ml-auto ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                <button id="uploadExcelButton" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#uploadExcelModal">Upload Excel</button>
                                <button id="deleteAllButton" class="btn btn-danger mb-3">Delete All Data</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-100">
                    <table id="datatableQuery" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Document No</th>
                                <th>Doc Date</th>
                                <th>Posting Date</th>
                                <th>G/L Acct Long Text</th>
                                <th>Unit</th>
                                <th>Type</th>
                                <th>Jumlah Rupiah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="uploadExcelModal" tabindex="-1" role="dialog" aria-labelledby="uploadExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadExcelModalLabel">Upload Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="excelUploadForm" action="<?= site_url(session()->get('level') . '/sumberdata/upload'); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="excel_file">Select Excel File</label>
                        <input type="file" name="excel_file" class="form-control-file" id="excel_file" accept=".xls, .xlsx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
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
        var ajaxUrl = `${baseUrl}${role}/sumberdata/ajaxList`;
        var deleteUrl = `${baseUrl}${role}/sumberdata/delete`;
        var deleteAllUrl = `${baseUrl}${role}/sumberdata/deleteAll`;

        $('#datatableQuery').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: ajaxUrl,
                type: 'POST',
            },
            columns: [{
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
                    "data": "5"
                },
                {
                    "data": "6"
                },
                {
                    "data": "7"
                },
                {
                    "data": "8",
                    "orderable": false
                }
            ],
            order: [
                [3, 'asc']
            ]
        });

        // Event listener untuk tombol delete
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Harap tunggu sementara data sedang dihapus.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    // Kirim permintaan AJAX ke server untuk menghapus data
                    $.ajax({
                        url: deleteUrl,
                        method: 'POST',
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Data berhasil dihapus.',
                                'success'
                            ).then(() => {
                                // Reload DataTable setelah menghapus data
                                $('#datatableQuery').DataTable().ajax.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Terjadi kesalahan saat menghapus data');
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menghapus data.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        // Event listener untuk tombol "Hapus Semua Data"
        $('#deleteAllButton').on('click', function() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Semua data pada tabel sumber data dan data yang berkaitan akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // First confirmation received, ask for second confirmation
                    Swal.fire({
                        title: 'Konfirmasi lagi',
                        text: "Apakah Anda benar-benar yakin ingin menghapus semua data? Tindakan ini tidak dapat dibatalkan.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((secondResult) => {
                        if (secondResult.isConfirmed) {
                            Swal.fire({
                                title: 'Menghapus...',
                                text: 'Harap tunggu sementara semua data sedang dihapus.',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading()
                                }
                            });
                            // Double confirmation received, proceed to delete
                            $.ajax({
                                url: deleteAllUrl,
                                method: 'POST',
                                success: function(response) {
                                    Swal.fire(
                                        'Terhapus!',
                                        'Semua data berhasil dihapus.',
                                        'success'
                                    ).then(() => {
                                        // Reload DataTable after deleting all data
                                        $('#datatableQuery').DataTable().ajax.reload();
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire(
                                        'Gagal!',
                                        'Terjadi kesalahan saat menghapus semua data: ' + xhr.responseText,
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                }
            });
        });

        // upload handling excel frontend
        $('#excelUploadForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            Swal.fire({
                title: 'Memproses...',
                text: 'Harap tunggu sementara file sedang diunggah dan diproses.',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading()
                }
            });

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    Swal.fire(
                        'Selesai!',
                        'Data telah berhasil diimpor dari file Excel.',
                        'success'
                    ).then((result) => {
                        if (result.isConfirmed || result.isDismissed) {
                            // Memuat ulang halaman setelah konfirmasi berhasil
                            location.reload();
                        }
                    });
                },
                error: function(xhr, status, error) {
                    var errorMessage = xhr.status + ': ' + xhr.statusText
                    Swal.fire(
                        'Gagal!',
                        'Terjadi kesalahan dalam memproses permintaan Anda. ' + errorMessage,
                        'error'
                    );
                }
            });
        });
    });
</script>

<?= $this->endSection(); ?>
