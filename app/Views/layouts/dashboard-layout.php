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
    <link href="<?= base_url('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') ?>" id="app-style" rel="stylesheet" type="text/css" />

    <!-- Sweet Alert css-->
    <link href="<?= base_url('assets/libs/sweetalert2/sweetalert2.min.css') ?>" rel="stylesheet" type="text/css" />

    <!-- Fixed Column -->
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.0.1/css/fixedColumns.dataTables.min.css">


    <!-- Style -->
    <style>
    .negative-value {
        background-color: pink !important;
    }
    .exceeds-threshold {
        background-color: #fae29c !important;
    }
    .first-month-input .remove-month-btn {
        display: none;
    }
    </style>

</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        <?= $this->include('layouts/partials/_navbar') ?>
        <?= $this->include('layouts/partials/_sidebar') ?>
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <?= $page_title ?>
                    <?= $this->renderSection('container-fluid') ?>
                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            <?= $this->include('layouts/partials/_footer') ?>
        </div>
        <!-- end main content-->
    </div>
    <!-- section script -->
    <?= $this->renderSection('scripts'); ?>

    <!-- END layout-wrapper -->
    <?= $this->include('layouts/partials/_script') ?>
</body>

</html>