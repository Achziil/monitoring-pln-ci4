<?= $this->extend('layouts/dashboard-layout'); ?>
<?= $this->section('container-fluid'); ?>

<h1 class="mb-3">Realisasi</h1>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="border-0">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <h5 class="mb-0">Tabel Realisasi</h5>
                            <p class="text-sm mb-2">
                                Menampilkan akumulasi sumber data `amount` per bulan berdasarkan Jenis Biaya & Unit.
                            </p>
                        </div>
                        <?php if (session()->get('level') === 'admin' || session()->get('level') === 'wilayah') : ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="busaFilter" class="form-label">Unit</label>
                                <select name="busaFilter" id="busaFilter" class="form-control">
                                    <option value="all">Wilayah/Admin</option>
                                    <?php foreach ($busaOptions as $option) : ?>
                                        <option value="<?= $option['busa'] ?>"><?= $option['busa'] ?> - <?= $option['nickname'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                            <div class="col-md-2 text-end">
                                <button id="deleteAllButton" class="btn btn-danger mb-3">Delete All Data</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="w-100">
                    <table id="datatableQuery" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>G/L Acct Long Text</th>
                                <th>Unit</th>
                                <th>Bulan</th>
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
        <div class="card">
            <div class="card-body">
            <div id="tableContainer">
                    <h5 class="">Total Realisasi per Kategori</h5>
                    <table id="totalPerKategoriTable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Total Realisasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/sweetalert2/sweetalert2.min.js') ?>"></script>

<script>
    $(document).ready(function() {
        var role = '<?= session()->get('level') ?>';
        var baseUrl = '<?= site_url() ?>';

        var ajaxUrl = `${baseUrl}${role}/realisasi/ajaxList`;
        var getDetailDataUrl = `${baseUrl}${role}/realisasi/detail`;
        var deleteAllUrl = `${baseUrl}${role}/realisasi/deleteAll`;
        var totalPerKategoriUrl = `${baseUrl}${role}/realisasi/totalPerKategori`;

        function fetchTotalPerKategori(busa) {
            $.ajax({
                url: totalPerKategoriUrl,
                method: 'POST',
                data: { busaFilter: busa }, // Gunakan busa dari filter atau userBusa
                success: function(response) {
                    var data = response.data;
                    var tbody = $('#totalPerKategoriTable tbody');
                    tbody.empty();

                    $.each(data, function(index, item) {
                        var row = '<tr>';
                        row += '<td>' + item.gl_long_text + '</td>';
                        row += '<td>' + formatIDR(item.total_amount_local_curr) + '</td>';
                        row += '</tr>';
                        tbody.append(row);
                    });
                }
            });
        }

        var table = $('#datatableQuery').DataTable({
            processing: true,
            serverSide: true,
            pageLength : 12,
            ajax: {
                url: ajaxUrl,
                type: 'POST',
                data: function(d) {
                    var selectedBusa = $('#busaFilter').val();
                    d.busaFilter = selectedBusa;
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
                [3, 'asc']
            ]
        });

        // Event listener untuk filter unit
        $('#busaFilter').on('change', function() {
            table.ajax.reload();
            fetchTotalPerKategori($(this).val()); // Reload Total Realisasi per Kategori when Unit filter changes
        });

        // Format numbers to IDR format
        function formatIDR(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number);
        }

        // Initially fetch Total Realisasi per Kategori
        fetchTotalPerKategori($('#busaFilter').val());

        // DETAIL WITH NON MODAL
        $(document).on('click', '.detail-btn', function() {
            const busa = $(this).data('busa');
            const bulan = $(this).data('bulan');
            const categoryId = $(this).data('category-id');
            window.location.href = `${getDetailDataUrl}/${busa}/${bulan}/${categoryId}`;
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
                    $.ajax({
                        url: deleteUrl + id,
                        method: 'POST',
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Data berhasil dihapus.',
                                'success'
                            ).then(() => {
                                $('#datatableQuery').DataTable().ajax.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Terjadi kesalahan saat menghapus data');
                        }
                    });
                }
            });
        });

        // Event listener untuk tombol "Hapus Semua Data"
        $('#deleteAllButton').on('click', function() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Semua data pada tabel realisasi dan data yang berkaitan akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
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
                            $.ajax({
                                url: deleteAllUrl,
                                method: 'POST',
                                success: function(response) {
                                    Swal.fire(
                                        'Terhapus!',
                                        'Semua data berhasil dihapus.',
                                        'success'
                                    ).then(() => {
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
    });
</script>

<?= $this->endSection(); ?>
