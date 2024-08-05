<?= $this->extend('layouts/dashboard-layout'); ?>
<?= $this->section('container-fluid'); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Detail Data</h4>
                    <!-- Case -->
                    <?php
                    $backUrl = '';
                    $level = session()->get('level');
                    switch ($level) {
                        case 'admin':
                            $backUrl = route_to('admin.realisasi.index');
                            break;
                        case 'wilayah':
                            $backUrl = route_to('wilayah.realisasi.index');
                            break;
                        case 'pelaksana':
                            $backUrl = route_to('pelaksana.realisasi.index');
                            break;
                        default:
                            // Default URL jika peran tidak ditemukan
                            $backUrl = route_to('default.route');
                            break;
                    }
                    ?>
                    <a href="<?= $backUrl ?>" class="btn btn-secondary">Kembali</a>
                </div>
                <div class="table-responsive">
                    <table id="detailTable" class="table table-bordered nowrap" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Posting Date</th>
                                <th>Category</th>
                                <th>BUSA</th>
                                <th>Amount Local Curr</th>
                                <th>Text</th>
                                <th>Reason for Trip / Document Header Text</th>
                                <th>Account</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi oleh server-side -->
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
        var ajaxDetailListUrl = `${baseUrl}${role}/realisasi/ajaxDetailList/`;

        var busa = '<?= $busa ?>';
        var bulan = '<?= $bulan ?>';
        var categoryId = '<?= $categoryId ?>';
        var detailTable = $('#detailTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `${ajaxDetailListUrl}${busa}/${bulan}/${categoryId}`,
                "type": "POST"
            },
            
            "columns": [{
                    "data": 0
                },
                {
                    "data": 1
                },
                {
                    "data": 2
                },
                {
                    "data": 3
                },
                {
                    "data": 4
                },
                {
                    "data": 5
                },
                {
                    "data": 6
                },
                {
                    "data": 7
                }
            ],
            responsive: true
        });
    });
</script>
<?= $this->endSection(); ?>