<?= $this->extend('layouts/dashboard-layout') ?>

<?= $this->section('container-fluid'); ?>
<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="mdi mdi-account-multiple-outline display-4 text-primary"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1"><span data-plugin="counterup"><?= $total_users ?></span></h4>
                    <p class="text-muted mb-0">Total Akun</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="mdi mdi-folder-outline display-4 text-success"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1"><span data-plugin="counterup"><?= $total_categories ?></span></h4>
                    <p class="text-muted mb-0">Total Kategori/GL Account</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="mdi mdi-chart-line-stacked display-4 text-warning"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1"><span data-plugin="counterup"><?= $total_data_realisasi ?></span></h4>
                    <p class="text-muted mb-0">Total Data Realisasi</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="mdi mdi-database-outline display-4 text-danger"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1"><span data-plugin="counterup"><?= $total_sumber_data ?></span></h4>
                    <p class="text-muted mb-0">Total Sumber Data</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="mdi mdi-cash display-4 text-info"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1"><span><?= number_format($total_realisasi, 0, ',', '.') ?></span></h4>
                    <p class="text-muted mb-0">Total Realisasi</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="mdi mdi-cash-multiple display-4 text-success"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1"><span><?= number_format($total_optimasi, 0, ',', '.') ?></span></h4>
                    <p class="text-muted mb-0">Total Optimasi</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="mdi mdi-wallet-outline display-4 text-warning"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1"><span><?= number_format($sisa_anggaran, 0, ',', '.') ?></span></h4>
                    <p class="text-muted mb-0">Sisa Anggaran</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-7">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Persentase Realisasi per Unit</h4>
                    <div>
                        <button id="switchMode" class="btn btn-primary btn-sm">
                            <i class="fas fa-exchange-alt mx-1"></i> Lebih Detail
                        </button>
                    </div>
                </div>
                <p class="text-muted mb-3" style="font-size: 12px;">
                    Klik <b>Lebih Detail</b> untuk beralih ke tampilan dengan skala logaritmik, mememudahkan visualisasi & perbandingan.
                </p>
                <canvas id="realisasiChart" height="80"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Top Kategori Realisasi</h4>
                    <div>
                        <select class="form-select" id="monthSelect">
                            <?php
                            $months = [
                                '01' => 'Januari',
                                '02' => 'Februari',
                                '03' => 'Maret',
                                '04' => 'April',
                                '05' => 'Mei',
                                '06' => 'Juni',
                                '07' => 'Juli',
                                '08' => 'Agustus',
                                '09' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember'
                            ];
                            $currentMonth = date('m');
                            foreach ($months as $value => $label) {
                                $selected = $value == $currentMonth ? 'selected' : '';
                                echo "<option value=\"$value\" $selected>$label</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div id="topCategoryContainer">
                    <div id="noDataMessage" style="display: none; background-color: #f8d7da; color: #721c24; padding: 10px; font-size: 16px; text-align: center;">
                    </div>
                    <canvas id="topCategoryChart" style="display: none;"></canvas>
                </div>

            </div>

        </div>
    </div>

</div>


<?= $this->endSection() ?>


<?= $this->section('scripts'); ?>
<script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>

<script>
    $(document).ready(function() {
        var chartData = <?= json_encode($chartData); ?>;
        var ctx = document.getElementById('realisasiChart').getContext('2d');
        var currentMode = 'normal';
        var realisasiChart;

        function createChart(data, labels) {
            if (realisasiChart) {
                realisasiChart.destroy();
            }
            realisasiChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total % Realisasi',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        data: data
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return Math.round(value) + '%'; // Menghilangkan desimal dengan pembulatan
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return ' ' + tooltipItem.label + ': ' + Math.round(tooltipItem.raw) + '%'; // Menghilangkan desimal dengan pembulatan
                                }
                            }
                        },
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        function toggleDetailView() {
            if (currentMode === 'normal') {
                var detailedData = chartData.data.map(value => value > 1000 ? 1000 + Math.log10(value - 1000 + 1) : value);
                createChart(detailedData, chartData.labels);
                currentMode = 'detailed';
            } else {
                createChart(chartData.data, chartData.labels);
                currentMode = 'normal';
            }
        }

        $('#switchMode').on('click', toggleDetailView);

        createChart(chartData.data, chartData.labels);
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var monthSelect = document.getElementById('monthSelect');
        var topCategoryContainer = document.getElementById('topCategoryContainer');
        var ctx = document.getElementById('topCategoryChart').getContext('2d');
        var topCategoryChart;

        function updateTopCategoryChart() {
            var selectedMonth = monthSelect.value;
            var url = '<?= base_url('wilayah/dashboard/top-category-realisasi') ?>?period=' + selectedMonth;

            fetch(url)
                .then(response => response.json())
                .then(responseData => {
                    var noDataMessage = document.getElementById('noDataMessage');
                    var topCategoryChart = document.getElementById('topCategoryChart');

                    if (responseData.data && responseData.data.length > 0) {
                        var data = responseData.data;
                        createTopCategoryChart(data);
                        noDataMessage.style.display = 'none';
                        topCategoryChart.style.display = 'block';
                    } else {
                        noDataMessage.innerText = 'Tidak ada data untuk bulan yang dipilih.';
                        noDataMessage.style.display = 'block';
                        topCategoryChart.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    var noDataMessage = document.getElementById('noDataMessage');
                    noDataMessage.innerText = 'Terjadi kesalahan saat mengambil data.';
                    noDataMessage.style.display = 'block';
                    var topCategoryChart = document.getElementById('topCategoryChart');
                    topCategoryChart.style.display = 'none';
                });
        }

        function createTopCategoryChart(data) {
            if (topCategoryChart) {
                topCategoryChart.destroy();
            }

            var labels = data.map(item => truncateLabel(item.category, 20));

            topCategoryChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Realisasi',
                        data: data.map(item => parseFloat(item.total)),
                        backgroundColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)'],
                        borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, values) {
                                    return value.toLocaleString('id-ID', {
                                        minimumFractionDigits: 0
                                    });
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = data[context.dataIndex].category || '';
                                    var value = parseFloat(data[context.dataIndex].total);
                                    var formattedValue = value.toLocaleString('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        minimumFractionDigits: 0
                                    });
                                    return label + ': ' + formattedValue;
                                }
                            }
                        }
                    }
                }
            });

            topCategoryContainer.style.display = 'block';
            document.getElementById('noDataMessage').style.display = 'none';
        }

        function truncateLabel(label, maxLength) {
            if (label.length > maxLength) {
                return label.substring(0, maxLength) + '...';
            }
            return label;
        }

        monthSelect.addEventListener('change', updateTopCategoryChart);

        updateTopCategoryChart();
    });
</script>

<?= $this->endSection(); ?>