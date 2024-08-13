<?= $this->extend('layouts/dashboard-layout'); ?>
<?= $this->section('container-fluid'); ?>

<h1 class="mb-3">Presentase Realisasi</h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="border-0">
                    <div class="d-lg-flex mb-2">
                        <div>
                            <h5 class="mb-0">Tabel Presentase Realisasi</h5>
                            <p class="text-sm mb-2">
                                Menampilkan presentase realisasi berdasarkan data terbaru dari tabel realisasi dan target optimasi.
                            </p>
                        </div>
                        <div class="ml-auto ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                <?php if (session()->get('level') === 'admin' || session()->get('level') === 'wilayah') : ?>
                                    <label for="busaFilter" class="form-label">Filter Unit</label>
                                    <select id="busaFilter" class="form-control">
                                        <?php foreach ($busaList as $busaItem) : ?>
                                            <option value="<?= $busaItem['busa'] ?>" <?= $busa === $busaItem['busa'] ? 'selected' : '' ?>>
                                                <?= $busaItem['busa'] ?> - <?= $busaItem['nickname'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                                <label for="yearFilter" class="form-label">Filter Tahun</label>
                                <select id="yearFilter" class="form-control">
                                    <option value="" selected disabled>-- Pilih Tahun --</option>
                                    <?php foreach ($monthsByYear as $key => $year) : ?>
                                        <option value="<?= $key ?>" <?= Date('Y', strtotime($selectedMonth)) == $key ? 'selected' : '' ?>>
                                            <?= $key ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <?php 
                                    $selectMonth = Date('Y', strtotime($selectedMonth));
                                    $months = $monthsByYear[$selectMonth] ?? [];
                                ?>

                                <label for="monthFilter" class="form-label">Filter Bulan</label>
                                <select id="monthFilter" class="form-control">
                                    <option value="" selected disabled>-- Pilih Bulan --</option>
                                    <?php foreach ($months as $month) : ?>
                                        <option value="<?= $month ?>" <?= $selectedMonth === $month ? 'selected' : '' ?>>
                                            <?= date('F', strtotime($month)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="realisasiPresentaseTable" class="table table-bordered border-2 dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Jenis Biaya</th>
                                <th>Busa</th>
                                <th>Optimasi Bulan Lalu</th>
                                <th>Realisasi Bulan Lalu</th>
                                <th>% Realisasi Bulan Lalu</th>
                                <th>Optimasi</th>
                                <th>Realisasi</th>
                                <th>% Realisasi</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $count = 1; ?>
                        <?php foreach ($realisasi_data as $row) : ?>
                            <tr style="background-color: <?= ($row['jenis_biaya'] == 'Total') ? '#dcdcdc' : '#FFFFE0'; ?>"> <!-- Warna latar belakang berbeda untuk baris total -->
                                <td><?= $count++; ?></td>
                                <td><?= esc($row['jenis_biaya']); ?></td>
                                <td><?= esc($busa); ?></td>
                                <td><?= is_numeric($row['optimasi_prev']) ? number_format($row['optimasi_prev'], 0, ',', '.') : esc($row['optimasi_prev']); ?></td>
                                <td><?= is_numeric($row['realisasi_prev']) ? number_format($row['realisasi_prev'], 0, ',', '.') : esc($row['realisasi_prev']); ?></td>
                                <td style="<?= is_numeric($row['percentage_prev']) && $row['percentage_prev'] > 100 ? 'background-color: #fae29c;' : '' ?>">
                                    <?= is_numeric($row['percentage_prev']) ? esc($row['percentage_prev']) . '%' : esc($row['percentage_prev']); ?>
                                </td>
                                <td><?= is_numeric($row['optimasi']) ? number_format($row['optimasi'], 0, ',', '.') : esc($row['optimasi']); ?></td>
                                <td><?= is_numeric($row['realisasi']) ? number_format($row['realisasi'], 0, ',', '.') : esc($row['realisasi']); ?></td>
                                <td style="<?= is_numeric($row['percentage']) && $row['percentage'] > 100 ? 'background-color: #fae29c;' : '' ?>">
                                    <?= is_numeric($row['percentage']) ? esc($row['percentage']) . '%' : esc($row['percentage']); ?>
                                </td>
                                <td>
                                    <?php if (isset($row['trend'])) : ?>
                                        <?php if ($row['trend'] > 0) : ?>
                                            <span style="color: red;">&#9650; <?= number_format($row['trend'], 2); ?>%</span> <!-- Panah ke atas merah -->
                                        <?php elseif ($row['trend'] < 0) : ?>
                                            <span style="color: green;">&#9660; <?= number_format($row['trend'], 2); ?>%</span> <!-- Panah ke bawah hijau -->
                                        <?php else : ?>
                                            <span>-</span>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <span>-</span>
                                    <?php endif; ?>
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

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        var role = '<?= session()->get('level') ?>';
        var baseUrl = '<?= site_url() ?>';
        var selectBusaUrl = `${baseUrl}${role}/presentase/realisasi/`;

        const listMonthByYear = <?= json_encode($monthsByYear) ?>;

        $('#busaFilter, #monthFilter, #yearFilter').on('change', function() {
            var selectedBusa = $('#busaFilter').val();
            var selectedMonth = $('#monthFilter').val();
            var selectedYear = $('#yearFilter').val();

            // replace year in selectedMonth to selectedYear
            selectedMonth = selectedMonth.replace(selectedMonth.substring(0, 4), selectedYear);
            
            // get list of month based on selected year
            var months = listMonthByYear[selectedYear];

            // replace month in selectedMonth to selectedYear
            selectedMonth = months.includes(selectedMonth) ? selectedMonth : months[0];
            
            window.location.href = `${selectBusaUrl}${selectedBusa}/${selectedMonth}`;
        });
    });
</script>
<?= $this->endSection(); ?>
