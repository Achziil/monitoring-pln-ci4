<?= $this->extend('layouts/dashboard-layout'); ?>
<?= $this->section('container-fluid'); ?>

<h1 class="mb-3">Detail Monitoring Optimasi</h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="border-0">
                    <div class="d-lg-flex mb-2">
                        <div>
                            <h5 class="mb-0">Tabel Detail Monitoring Optimasi</h5>
                            <p class="text-sm mb-2">
                                Menampilkan hasil perhitungan (target optimasi) di kurangi (realisasi) perbulan dalam 1 tahun berdasarkan Jenis Biaya & Unit.
                            </p>
                        </div>
                        <div class="ml-auto ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                <!-- Case -->
                                <?php
                                $backUrl = '';
                                $level = session()->get('level');
                                switch ($level) {
                                    case 'admin':
                                        $backUrl = route_to('admin.monitoring.index');
                                        break;
                                    case 'wilayah':
                                        $backUrl = route_to('wilayah.monitoring.index');
                                        break;
                                    case 'pelaksana':
                                        $backUrl = route_to('pelaksana.monitoring.index');
                                        break;
                                    default:
                                        // Default URL jika peran tidak ditemukan
                                        $backUrl = route_to('default.route');
                                        break;
                                }
                                ?>
                                <a href="<?= $backUrl ?>" class="btn btn-secondary mb-3">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="detailMonitoringTable" class="table table-bordered border-2 dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $item) : ?>
                                <tr>
                                    <td><?= $item['gl_long_text'] ?></td>
                                    <td><?= $item['busa'] ?></td>
                                    <td><?= $item['jan'] !== '-' ? number_format($item['jan'], 0, ',', '.') : '-' ?></td>
                                    <td><?= $item['feb'] !== '-' ? number_format($item['feb'], 0, ',', '.') : '-' ?></td>
                                    <td><?= $item['mar'] !== '-' ? number_format($item['mar'], 0, ',', '.') : '-' ?></td>
                                    <td><?= $item['apr'] !== '-' ? number_format($item['apr'], 0, ',', '.') : '-' ?></td>
                                    <td><?= $item['mei'] !== '-' ? number_format($item['mei'], 0, ',', '.') : '-' ?></td>
                                    <td><?= $item['jun'] !== '-' ? number_format($item['jun'], 0, ',', '.') : '-' ?></td>
                                    <td><?= $item['jul'] !== '-' ? number_format($item['jul'], 0, ',', '.') : '-' ?></td>
                                    <td><?= $item['aug'] !== '-' ? number_format($item['aug'], 0, ',', '.') : '-' ?></td>
                                    <td><?= $item['sep'] !== '-' ? number_format($item['sep'], 0, ',', '.') : '-' ?></td>
                                    <td><?= $item['okt'] !== '-' ? number_format($item['okt'], 0, ',', '.') : '-' ?></td>
                                    <td><?= $item['nov'] !== '-' ? number_format($item['nov'], 0, ',', '.') : '-' ?></td>
                                    <td><?= $item['des'] !== '-' ? number_format($item['des'], 0, ',', '.') : '-' ?></td>
                                    <td><?= $item['total'] !== '-' ? number_format($item['total'], 0, ',', '.') : '-' ?></td>
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
<script src="<?= base_url('assets/libs/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>

<script>
    $(document).ready(function() {
        $('#detailMonitoringTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
</script>

<?= $this->endSection(); ?>