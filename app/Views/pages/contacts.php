<?= $this->extend('layouts/dashboard-layout'); ?>

<?= $this->section('container-fluid'); ?>
<section>
    <div class="container">
    <h1>Contacts</h1>
    <?php foreach($alamat as $a): ?>
    <ul>
        <li><?= $a['tipe']; ?></li>
        <li><?= $a['alamat']; ?></li>
        <li><?= $a['kota']; ?></li>
    </ul>
    <?php endforeach; ?>
    </div>
</section>
<?= $this->endSection(); ?>
