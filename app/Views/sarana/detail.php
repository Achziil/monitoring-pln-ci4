<?= $this->extend('layouts/dashboard-layout');; ?>

<?= $this->section('container-fluid');; ?>
<div class="cont">
  <div class="row">
    <div class="col text-end">
      <a href="javascript:history.back()" class="btn btn-close-white">Kembali</a>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <div class="card mb-3" style="max-width: 540px;">
        <div class="row g-0">
          <div class="col-md-4">
            <img src="https://via.placeholder.com/300" class="card-img-top" alt="Placeholder Image">
          </div>
          <div class="col-md-8">
            <div class="card-body">
              <h5 class="card-title"><?= $sarana['kategori']; ?></h5>
              <p class="card-text"><?= $sarana['detail']; ?></p>
              <p class="card-text"><small class="text-body-secondary"><?= $sarana['status'] == 0 ?  "Tidak Tersedia" : "Tersedia"; ?></small></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection();; ?>