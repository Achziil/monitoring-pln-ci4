<!doctype html>
<html lang="en">

<head>
    <title><?= $title; ?></title>
    <link rel="icon" href="<?= base_url('assets/images/logo-sm.png') ?>" type="image/png" />
    <!-- Bootstrap Css -->
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?= base_url('assets/css/icons.min.css') ?>" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="<?= base_url('assets/css/app.min.css') ?>" id="app-style" rel="stylesheet" type="text/css" />
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
</head>

<body class="authentication-bg bg-soft-primary">
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <a href="index" class="mb-4 d-block auth-logo">
                        <img src="<?= base_url('assets/images/logo-sm.png') ?>" alt="" height="200" class="logo logo-dark">
                    </a>
                    <h3 class="mb-5 text-center"><strong>Web Monitoring Anggaran Biaya Administrasi Wilayah dan Pelaksana</strong></h3>
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="text-center mt-2">
                                <h5 class="text-primary">Selamat Datang !</h5>
                                <p class="text-muted">Silahkan Masukkan Informasi Akun anda </p>
                            </div>
                            <div class="p-2 mt-4">
                                <?php if (session()->getFlashdata('msg')) : ?>
                                    <div class="alert alert-warning">
                                        <?= session()->getFlashdata('msg') ?>
                                    </div>
                                <?php endif; ?>
                                <form action="<?= url_to('authenticate') ?>" method="post">

                                    <div class="mb-3">
                                        <label class="form-label" for="username">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter username">
                                    </div>

                                    <div class="mb-3 position-relative">
                                        <label class="form-label" for="password">Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                                            <span class="input-group-text cursor-pointer" id="toggle-password">
                                                <i class="bi bi-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-3 text-center">
                                        <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Log In</button>
                                    </div>

                                </form>
                            </div>

                        </div>
                    </div>

                    <div class="mt-5 text-center">
                        <p>© <script>
                                document.write(new Date().getFullYear())
                            </script> || PLN Unit Induk Wilayah Papua dan Papua Barat <i class="mdi mdi-lightning-bolt text-warning"></i></p>
                    </div>

                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>

    <style>
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
    <!-- <script src="assets/js/app.js"></script>-->
    <?= $this->include('layouts/partials/_script') ?>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
    </script>

</body>

</html>