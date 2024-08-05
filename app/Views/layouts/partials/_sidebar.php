<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="<?= base_url('/') ?>" class="logo ">
            <span class="logo-sm">
                <img src="<?= base_url('assets/images/logo-sm.png') ?>" alt="" style="height: 50px;">
            </span>
            <span class="logo-lg">
                <img src="<?= base_url('assets/images/logo.png') ?>" alt="" style="height: 40px;">
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
                <?php
                $menu_items = build_menu();
                foreach ($menu_items as $item) {
                    if (isset($item['type']) && $item['type'] === 'title') {
                        echo "<li class='menu-title'>" . $item['name'] . "</li>";
                    } elseif (isset($item['submenu'])) {
                        $active = false;
                        foreach ($item['submenu'] as $submenu) {
                            if (strpos(current_url(), $submenu['link']) === 0) {
                                $active = true;
                                break;
                            }
                        }
                        echo '<li class="dropdown ' . ($active ? 'mm-active' : '') . '">';
                        echo '<a href="javascript: void(0);" class="has-arrow waves-effect ' . ($active ? 'active' : '') . '">';
                        echo '<i class="' . $item['icon'] . '"></i><span class="badge rounded-pill bg-primary float-end"></span>';
                        echo '<span>' . $item['name'] . '</span>';
                        echo '</a>';
                        echo '<ul class="sub-menu dropdown-menu-end" aria-expanded="false">';
                        foreach ($item['submenu'] as $submenu) {
                            echo '<li class="' . (strpos(current_url(), $submenu['link']) === 0 ? 'mm-active' : '') . '">';
                            echo '<a class="dropdown-item ' . (strpos(current_url(), $submenu['link']) === 0 ? 'active' : '') . '" href="' . $submenu['link'] . '">';
                            echo '<i class="' . $submenu['icon'] . '"></i>' . $submenu['name'];
                            echo '</a>';
                            echo '</li>';
                        }
                        echo '</ul>';
                        echo '</li>';
                    } else {
                        echo '<li class="' . (strpos(current_url(), $item['link']) === 0 ? 'mm-active' : '') . '">';
                        echo '<a href="' . $item['link'] . '" class="' . (strpos(current_url(), $item['link']) === 0 ? 'active' : '') . '">';
                        echo '<i class="' . $item['icon'] . '"></i>';
                        echo '<span>' . $item['name'] . '</span>';
                        echo '</a>';
                        echo '</li>';
                    }
                }
                ?>
            </ul>
        </div>

    </div>
</div>
<!-- Left Sidebar End -->