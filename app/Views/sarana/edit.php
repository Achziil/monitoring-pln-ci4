<?= $this->extend('layouts/dashboard-layout'); ?>

<?= $this->section('container-fluid'); ?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <h2>Form Ubah Sarana/Fasilitas</h2>
                <?php if (session()->getFlashdata('validation')) : ?>
                    <?php $validation = session()->getFlashdata('validation'); ?>
                <?php endif; ?>
                <form action="<?= base_url('/sarana/update/'.$sarana['id']); ?>" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="slug" value="<?= $sarana['slug']; ?>">
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select class="form-select" aria-label="Kategori" name="kategori" required>
                            <option value="" selected disabled>Pilih Kategori</option>
                            <option value="Sarana" <?= ($sarana['kategori'] == 'Sarana') ? 'selected' : ''; ?>>Sarana</option>
                            <option value="Fasilitas" <?= ($sarana['kategori'] == 'Fasilitas') ? 'selected' : ''; ?>>Fasilitas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="detail" class="form-label">Detail</label>
                        <input type="text" class="form-control <?= ($validation->hasError('detail')) ? 'is-invalid' : ''; ?>" id="detail" name="detail" placeholder="Nama Sarana/Fasilitas" value="<?= $sarana['detail']; ?>" required>
                        <div class="invalid-feedback">
                            <?= $validation->getError('detail'); ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="pemilik" class="form-label">Pemilik</label>
                        <input type="text" class="form-control" id="pemilik" name="pemilik" value="<?= $sarana['pemilik']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" aria-label="Status" name="status" required>
                            <option value="" selected disabled>Pilih Status</option>
                            <option value="1" <?= ($sarana['status'] == 1) ? 'selected' : ''; ?>>Tersedia</option>
                            <option value="0" <?= ($sarana['status'] == 0) ? 'selected' : ''; ?>>Tidak Tersedia</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>