<?= $this->extend('layouts/dashboard-layout'); ?>
<?= $this->section('container-fluid'); ?>

<h1 class="mb-3">Monitoring Optimasi</h1>

<div class="row">
    <div class="col-12">
        <h4 id="lastRefreshDate" style="text-align: end;" class="text-bold"></h4>
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center mb-3">
                    <div class="col-md-6">
                        <h5 class="mb-0">Tabel Monitoring Optimasi</h5>
                        <p class="text-sm mb-2">
                            Menampilkan hasil perhitungan (target optimasi) di kurangi (realisasi) perbulan dalam 1 tahun berdasarkan Jenis Biaya & Unit.
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
                            <button id="refreshButton" class="btn btn-primary mb-3">Update Monitoring</button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table id="monitoringTable" class="table table-bordered border-2 dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
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
                                <th>TOTAL</th>
                                <?php if (session()->get('level') === 'admin' || session()->get('level') === 'wilayah') : ?>
                                    <th>Actions</th>
                                <?php endif; ?>
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
<script src="<?= base_url('assets/libs/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/moment/min/moment.min.js') ?>"></script>


<script>
    $(document).ready(function() {
        var role = '<?= session()->get('level') ?>'; // Get role dari session
        var baseUrl = '<?= site_url() ?>'; // Base URL dari CI 4
        var selectedBusa = '<?= $busa ?>'; // Set selected busa dari view

        var getData = `${baseUrl}${role}/monitoring/getData`;
        var refreshUrl = `${baseUrl}${role}/monitoring/refresh`;

        function formatCurrency(value) {
            if (value === null || value === undefined || value === 0) {
                return '-';
            }
            return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        $('#busaFilter').on('change', function() {
            selectedBusa = $(this).val();
            fetchMonitoringData(selectedBusa);
        });

        function fetchMonitoringData(busa) {
            $.ajax({
                url: getData,
                method: 'POST',
                data: {
                    busa: selectedBusa
                },
                success: function(response) {
                    if ($.fn.DataTable.isDataTable('#monitoringTable')) {
                        $('#monitoringTable').DataTable().destroy(); // Hancurkan dulu
                    }
                    if (response.success) {
                        var data = response.data;
                        var tbody = $('#monitoringTable tbody');
                        tbody.empty();

                        $.each(data, function(index, item) {
                            var row = '<tr>';
                            var values = [
                                item.jan, item.feb, item.mar, item.apr, item.mei,
                                item.jun, item.jul, item.aug, item.sep, item.okt,
                                item.nov, item.des, item.total
                            ];
                            row += '<td>' + (item.gl_long_text || '-') + '</td>';
                            row += '<td>' + selectedBusa + '</td>';

                            values.forEach(function(value) {
                                var formattedValue = formatCurrency(value);
                                var cellClass = (value < 0) ? 'negative-value' : '';
                                row += '<td class="' + cellClass + '">' + formattedValue + '</td>';
                            });

                            if (role === 'admin' || role === 'wilayah') {
                                row += '<td><a href="<?= site_url(session()->get('level') . '/monitoring/detail'); ?>/' + item.category_id + '" class="btn btn-sm btn-info">Detail</a></td>';
                            }

                            row += '</tr>';
                            tbody.append(row);
                        });

                        $('#monitoringTable').DataTable({
                            "pageLength": 25,
                            "paging": false,
                            "lengthChange": true,
                            "searching": true,
                            "ordering": true,
                            "info": true,
                            "autoWidth": false,
                            "responsive": false,
                            "scrollcollapse":true,
                            "fixedColumns": {
                                "leftColumns": 2  // Adjust this according to the number of columns you want to fix
                            }
                        });

                        var lastRefreshDate = response.last_refresh_date;
                        $('#lastRefreshDate').text('Last Update: ' + moment(lastRefreshDate).format('DD MMMM YYYY (HH:mm:ss)'));
                    }
                }
            });
        }
        fetchMonitoringData();

        function refreshMonitoringData() {
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
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        }).then(function() {
                            fetchMonitoringData();
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
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'There was an error updating the data. Please try again.',
                    });
                }
            });
        }

        $('#refreshButton').on('click', function() {
            refreshMonitoringData();
        });
    });
</script>
<?= $this->endSection(); ?>