<?= $this->extend('layouts/dashboard-layout'); ?>

<?= $this->section('container-fluid'); ?>
<h1 class="mb-3">Daftar Sarana</h1>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="border-0">
                    <div class="d-lg-flex mb-2">
                        <div>
                            <h5 class="mb-0">Tabel Sarana</h5>
                            <p class="text-sm mb-2">
                                menampilkan seluruh data sarana.
                            </p>
                        </div>
                        <div class="ml-auto ms-auto my-auto mt-lg-0 mt-4 ">
                            <div class="ms-auto my-auto ">
                                <a class="btn btn-primary mb-3" href="<?= url_to('sarana.create'); ?>">Tambah Sarana/Fasilitas</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (session()->has('success')) : ?>
                    <div class="alert alert-success" role="alert">
                        <?= session()->get('success'); ?>
                    </div>
                <?php endif; ?>
                <div class="w-100">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kategori</th>
                                <th>Detail</th>
                                <th>Status</th>
                                <th>Pemilik</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sarana as $s) : ?>
                                <tr>
                                    <td><?= esc($s['id']); ?></td>
                                    <td><?= esc($s['kategori']); ?></td>
                                    <td><?= esc($s['detail']); ?></td>
                                    <td><?= $s['status'] ? 'Tersedia' : 'Tidak Tersedia'; ?></td>
                                    <td><?= esc($s['pemilik']); ?></td>
                                    <td>
                                        <a href="<?= url_to('sarana.detail', $s['slug']); ?>" class="btn btn-primary">Lihat</a>
                                        <a href="<?= url_to('sarana.edit', $s['slug']); ?>" class="btn btn-success">Edit</a>


                                        <form action="<?= site_url('/sarana/' . $s['id']); ?>" method="post" style="display:inline;">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                                        </form>
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

<?= $this->endSection(); ?>