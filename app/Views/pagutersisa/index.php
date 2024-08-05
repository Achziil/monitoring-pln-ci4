<?= $this->extend('layouts/dashboard-layout'); ?>
<?= $this->section('container-fluid'); ?>

<h1 class="mb-3">Pagu Tersisa</h1>

<div class="row">
    <div class="col-12">
        <h4 id="lastRefreshDate" style="text-align: end;" class="text-bold"></h4>
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center mb-3">
                    <div class="col-md-6">
                        <h5 class="mb-0">Tabel Pagu Tersisa</h5>
                        <p class="text-sm mb-2">
                            Menampilkan hasil perhitungan pagu tersisa dari akumulasi perbulan hasil monitoring.
                        </p>
                    </div>
                    <?php if (session()->get('level') === 'admin' || session()->get('level') === 'wilayah') : ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="busaFilter" class="form-label">Filter Unit</label>
                                <select id="busaFilter" class="form-control">
                                    <?php foreach ($busaList as $busaItem) : ?>
                                        <option value="<?= $busaItem['busa'] ?>" <?= $busa === $busaItem['busa'] ? 'selected' : '' ?>>
                                            <?= $busaItem['busa'] ?> - <?= $busaItem['nickname'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 text-md-end mt-3">
                            <button id="refreshButton" class="btn btn-primary mb-3">Update Pagu Tersisa</button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table id="paguTersisaTable" class="table table-bordered border-2 dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>Jenis Biaya</th>
                                <th>Busa</th>
                                <th>JAN</th>
                                <th>FEB</th>
                                <th>MAR</th>
                                <th>APR</th>
                                <th>MEI</th>
                                <th>JUN</th>
                                <th>JUL</th>
                                <th>AGUS</th>
                                <th>SEP</th>
                                <th>OKT</th>
                                <th>NOV</th>
                                <th>DES</th>
                                <th style="white-space: nowrap">Total (%)</th>
                                <th>Actions</th>
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

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>

<script src="<?= base_url('assets/libs/sweetalert2/sweetalert2.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/moment/min/moment.min.js') ?>"></script>

<script>
    $(document).ready(function() {

        var role = '<?= session()->get('level') ?>'; // Get role dari session
        var baseUrl = '<?= site_url() ?>'; // Base URL dari CI 4
        var selectedBusa = '<?= $busa ?>';

        // Membuat URL menggunakan template string
        // var getDataUrl = `${baseUrl}${role}/pagu-tersisa/getData`;
        var getDataWithPercentageUrl = `${baseUrl}${role}/pagu-tersisa/getDataWithPercentage`;
        var refreshUrl = `${baseUrl}${role}/pagu-tersisa/refresh`;

        // Fungsi untuk memformat nilai mata uang
        function formatCurrency(value, negativeValue) {
            if (value === '0') {
                return '0';
            } else if (value === 0 && negativeValue < 0) {
                return '-';
            } else {
                return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
        }

        $('#busaFilter').on('change', function() {
            selectedBusa = $(this).val(); // Ini sekarang memperbarui variabel global
            fetchPaguTersisaData(selectedBusa);
        });


        // Fungsi untuk mengambil data pagu tersisa dan menampilkannya pada tabel
        function fetchPaguTersisaData(busa) {
            $.ajax({
                url: getDataWithPercentageUrl,
                method: 'POST',
                data: {
                    busa: busa
                },
                success: function(response) {
                    if ($.fn.DataTable.isDataTable('#paguTersisaTable')) {
                        $('#paguTersisaTable').DataTable().destroy(); // Hancurkan dulu
                    }
                    if (response.success) {
                        var data = response.data;
                        var tbody = $('#paguTersisaTable tbody');
                        tbody.empty();

                        $.each(data, function(index, item) {
                            var row = '<tr>';
                            // console.log(selectedBusa)
                            row += '<td>' + (item.gl_long_text || '-') + '</td>';
                            row += '<td>' + selectedBusa + '</td>';
                            row += '<td>' + formatCurrency(item.jan, item.jan_negative) + '</td>';
                            row += '<td>' + formatCurrency(item.feb, item.feb_negative) + '</td>';
                            row += '<td>' + formatCurrency(item.mar, item.mar_negative) + '</td>';
                            row += '<td>' + formatCurrency(item.apr, item.apr_negative) + '</td>';
                            row += '<td>' + formatCurrency(item.mei, item.mei_negative) + '</td>';
                            row += '<td>' + formatCurrency(item.jun, item.jun_negative) + '</td>';
                            row += '<td>' + formatCurrency(item.jul, item.jul_negative) + '</td>';
                            row += '<td>' + formatCurrency(item.aug, item.aug_negative) + '</td>';
                            row += '<td>' + formatCurrency(item.sep, item.sep_negative) + '</td>';
                            row += '<td>' + formatCurrency(item.okt, item.okt_negative) + '</td>';
                            row += '<td>' + formatCurrency(item.nov, item.nov_negative) + '</td>';
                            row += '<td>' + formatCurrency(item.des, item.des_negative) + '</td>';
                            row += '<td>' + (typeof item.percentage === 'number' ? item.percentage.toFixed(0) + '%' : '-') + '</td>';
                            if (role === 'admin' || role === 'wilayah') {
                                row += '<td><a href="<?= site_url(session()->get('level') . '/pagu-tersisa/detail'); ?>/' + item.category_id + '" class="btn btn-sm btn-info">Detail</a></td>';

                            } else {
                                row += '<td></td>';
                            }

                            tbody.append(row);
                        });


                        // Inisialisasi ulang DataTable
                        $('#paguTersisaTable').DataTable({
                            "pageLength": 25,
                            "paging": true,
                            "lengthChange": true,
                            "searching": true,
                            "ordering": true,
                            "info": true,
                            "autoWidth": false,
                            "responsive": false,
                        });

                        // Menampilkan tanggal terakhir refresh
                        var lastRefreshDate = response.last_refresh_date;
                        $('#lastRefreshDate').text('Last Update: ' + moment(lastRefreshDate).format('DD-MM-YYYY HH:mm'));
                    } else {
                        console.error('Failed to fetch data: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error: ' + status + ' - ' + error);
                }
            });
        }
        // Memanggil fungsi fetchPaguTersisaData saat halaman dimuat
        fetchPaguTersisaData();

        // Fungsi untuk melakukan refresh data pagu tersisa
        function refreshPaguTersisaData() {
            Swal.fire({
                title: 'Updating',
                text: 'Please wait while we update the data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: refreshUrl,
                method: 'POST',
                success: function(response) {
                    Swal.close(); // Close the loading alert
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        }).then(function() {
                            fetchPaguTersisaData();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                        });
                    }
                },
                error: function() {
                    Swal.close(); // Close the loading alert in case of error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'There was an error updating the data. Please try again.',
                    });
                }
            });
        }

        // Menambahkan event click pada tombol Refresh
        $('#refreshButton').on('click', function() {
            refreshPaguTersisaData();
        });
    });
</script>

<?= $this->endSection(); ?>