<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="<?= base_url('/') ?>" class="logo ">
            <span class="logo-sm">
                <img src="<?= base_url('assets/images/logo-sm.png') ?>" alt="" height="30   ">
            </span>
            <span class="logo-lg">
                <img src="<?= base_url('assets/images/Logo.png') ?>" alt="" height="50">
            </span>
        </a>
    </div>

    <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
        <i class="fa fa-fw fa-bars"></i>
    </button>

    <div data-simplebar class="sidebar-menu-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>

                <li>
                    <a href="<?= site_url('dashboard'); ?>">
                        <i class="uil-home-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- <li>
                    <a href="<?= site_url('/'); ?>">
                        <i class="uil-home-alt"></i><span class="badge rounded-pill bg-primary float-end">01</span>
                        <span>Dashboard</span>
                    </a>
                </li> -->

                <?php if (session()->get('busa') === '7600') : ?>
                    <li>
                        <a href="<?= base_url("admin/categories") ?>">
                            <i class="uil-apps"></i><span class="badge rounded-pill bg-primary float-end"></span>
                            <span>Categories</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="dropdown">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="uil-notes"></i><span class="badge rounded-pill bg-primary float-end"></span>
                        <span>RKAP</span>
                    </a>
                    <ul class="sub-menu dropdown-menu-end" aria-expanded="false">
                        <li>
                            <a class="dropdown-item" href="<?= url_to("sumberdata.index") ?>">
                                <i class="uil-database me-1"></i>Sumber Data
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= url_to("realisasi.index") ?>">
                                <i class="uil-chart me-1"></i>Realisasi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= url_to("targetoptimasi.index") ?>">
                                <i class="uil-bullseye me-1"></i>Target Optimasi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= url_to("monitoring.index") ?>">
                                <i class="uil-monitor me-1"></i>Monitoring
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= url_to("pagutersisa.index") ?>">
                                <i class="uil-money-withdrawal me-1"></i>Pagu Terisa
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- <li>
                    <a href="<?= base_url("pages/about") ?>">
                        <i class="uil-home-alt"></i><span class="badge rounded-pill bg-primary float-end"></span>
                        <span>About</span>
                    </a>
                </li>

                <li>
                    <a href="<?= base_url("pages/contacts") ?>">
                        <i class="uil-home-alt"></i><span class="badge rounded-pill bg-primary float-end"></span>
                        <span>Contact</span>
                    </a>
                </li> -->

                <?php if (session()->get('level') === 'admin') : ?>
                    <li>
                        <a href="<?= base_url("admin/sarana") ?>">
                            <i class="uil-building"></i><span class="badge rounded-pill bg-primary float-end"></span>
                            <span>Sarana</span>
                        </a>
                    </li>
                <?php endif; ?>
                <!-- <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="uil-plane"></i>
                        <span>SPPD</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="<?= base_url('auth-login'); ?>"> <i class="uil-file-plus-alt"></i> Pengajuan SPPD</a></li>
                        
                        <li><a href="<?= base_url('auth-login'); ?>"> <i class="uil-focus-target"></i> Monitoring SPPD</a></li>
                    </ul>
                </li> -->

                <!-- Files Layout Sample Menu With Sub Menu -->
                <!-- <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="uil-window-section"></i>
                        <span><?= lang('Files.Layouts') ?></span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li>
                            <a href="javascript: void(0);" class="has-arrow"><?= lang('Files.Vertical') ?></a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="<?= base_url('layouts-dark-sidebar'); ?>"><?= lang('Files.Dark Sidebar') ?></a></li>
                                <li><a href="<?= base_url('layouts-compact-sidebar'); ?>"><?= lang('Files.Compact Sidebar') ?></a></li>
                                <li><a href="<?= base_url('layouts-icon-sidebar'); ?>"><?= lang('Files.Icon Sidebar') ?></a></li>
                                <li><a href="<?= base_url('layouts-boxed'); ?>"><?= lang('Files.Boxed Width') ?></a></li>
                                <li><a href="<?= base_url('layouts-preloader'); ?>"><?= lang('Files.Preloader') ?></a></li>
                                <li><a href="<?= base_url('layouts-colored-sidebar'); ?>"><?= lang('Files.Colored Sidebar') ?></a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="javascript: void(0);" class="has-arrow"><?= lang('Files.Horizontal') ?></a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="<?= base_url('layouts-horizontal'); ?>"><?= lang('Files.Horizontal') ?></a></li>
                                <li><a href="<?= base_url('layouts-hori-topbar-dark'); ?>"><?= lang('Files.Dark Topbar') ?></a></li>
                                <li><a href="<?= base_url('layouts-hori-boxed-width'); ?>"><?= lang('Files.Boxed Width') ?></a></li>
                                <li><a href="<?= base_url('layouts-hori-preloader'); ?>"><?= lang('Files.Preloader') ?></a></li>
                            </ul>
                        </li>
                    </ul>
                </li> -->


                <?php if (session()->get('level') === 'admin') : ?>
                    <li class="menu-title">Menu Akun</li>

                    <li>
                        <a href="<?= base_url("admin/users") ?>">
                            <i class="uil-user-circle"></i><span class="badge rounded-pill bg-primary float-end"></span>
                            <span>Kelola Akun</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="uil-file-alt"></i>
                        <span><?= lang('Files.Utility') ?></span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="<?= base_url('pages-starter'); ?>"><?= lang('Files.Starter Page') ?></a></li>
                        <li><a href="<?= base_url('pages-maintenance'); ?>"><?= lang('Files.Maintenance') ?></a></li>
                        <li><a href="<?= base_url('pages-comingsoon'); ?>"><?= lang('Files.Coming Soon') ?></a></li>
                        <li><a href="<?= base_url('pages-404'); ?>"><?= lang('Files.Error') ?> 404</a></li>
                        <li><a href="<?= base_url('pages-500'); ?>"><?= lang('Files.Error') ?> 500</a></li>
                    </ul>
                </li> -->

                <!-- <li class="menu-title"><?= lang('Files.Components') ?></li> -->

                <!-- <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="uil-share-alt"></i>
                        <span><?= lang('Files.Multi Level') ?></span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li><a href="javascript: void(0);"><?= lang('Files.Level') ?> 1.1</a></li>
                        <li><a href="javascript: void(0);" class="has-arrow"><?= lang('Files.Level') ?> 1.2</a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="javascript: void(0);"><?= lang('Files.Level') ?> 2.1</a></li>
                                <li><a href="javascript: void(0);"><?= lang('Files.Level') ?> 2.2</a></li>
                            </ul>
                        </li>
                    </ul>
                </li> -->

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->