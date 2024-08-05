<?php

function build_menu()
{
    $session = \Config\Services::session();
    $role = $session->get('level');  // Ambil role pengguna dari session

    $menus = [
        'admin' => [
            [
                'type' => 'title',
                'name' => 'Menu'
            ],
            [
                'name' => 'Dashboard',
                'icon' => 'uil-home-alt',
                'link' => site_url('admin/dashboard')
            ],
           
            [
                'name' => 'Kategori/GL Account',
                'icon' => 'uil-apps',
                'link' => site_url('admin/categories')
            ],
            [
                'name' => 'Menu Monitoring',
                'icon' => 'uil-notes',
                'submenu' => [
                    [
                        'name' => 'Sumber Data',
                        'icon' => 'uil-database me-1',
                        'link' => site_url('admin/sumberdata')
                    ],
                    [
                        'name' => 'Realisasi',
                        'icon' => 'uil-chart me-1',
                        'link' => site_url('admin/realisasi')
                    ],
                    [
                        'name' => 'Target Optimasi',
                        'icon' => 'uil-bullseye me-1',
                        'link' => site_url('admin/targetoptimasi')
                    ],
                    [
                        'name' => 'Monitoring Penggunaan',
                        'icon' => 'uil-monitor me-1',
                        'link' => site_url('admin/monitoring')
                    ],
                    [
                        'name' => 'Pagu Terisa',
                        'icon' => 'uil-money-withdrawal me-1',
                        'link' => site_url('admin/pagu-tersisa')
                    ],
                    [
                        'name' => 'Presentase',
                        'icon' => 'uil-percentage me-1',
                        'link' => site_url('admin/presentase/realisasi')
                    ],
                ]
            ],
            // [
            //     'name' => '',
            //     'icon' => 'uil-building',
            //     'link' => site_url('admin/sarana')
            // ],
            // [
            //     'name' => 'Justifikasi',
            //     'icon' => 'uil-apps',
            //     'link' => site_url('wilayah/categories')
            // ],
            [
                'type' => 'title',
                'name' => 'Menu Akun'
            ],
            [
                'name' => 'Kelola Akun',
                'icon' => 'uil-user-circle',
                'link' => site_url('admin/users')
            ],
        ],
        'wilayah' => [
            [
                'type' => 'title',
                'name' => 'Menu Wilayah'
            ],

            [
                'name' => 'Dashboard',
                'icon' => 'uil-home-alt',
                'link' => site_url('wilayah/dashboard')
            ],
            [
                'name' => 'Kategori/GL Account',
                'icon' => 'uil-apps',
                'link' => site_url('wilayah/categories')
            ],
           
            [
                'name' => 'Menu Monitoring',
                'icon' => 'uil-notes',
                'submenu' => [
                    [
                        'name' => 'Sumber Data',
                        'icon' => 'uil-database me-1',
                        'link' => site_url('wilayah/sumberdata')
                    ],
                    [
                        'name' => 'Realisasi',
                        'icon' => 'uil-chart me-1',
                        'link' => site_url('wilayah/realisasi')
                    ],
                    [
                        'name' => 'Target Optimasi',
                        'icon' => 'uil-bullseye me-1',
                        'link' => site_url('wilayah/targetoptimasi')
                    ],
                    [
                        'name' => 'Monitoring Penggunaan',
                        'icon' => 'uil-monitor me-1',
                        'link' => site_url('wilayah/monitoring')
                    ],
                    [
                        'name' => 'Pagu Terisa',
                        'icon' => 'uil-money-withdrawal me-1',
                        'link' => site_url('wilayah/pagu-tersisa')
                    ],
                    [
                        'name' => 'Presentase',
                        'icon' => 'uil-percentage me-1',
                        'link' => site_url('wilayah/presentase/realisasi')
                    ],

                ]

            ],

            [
                'type' => 'title',
                'name' => 'Menu Akun'
            ],
            [
                'name' => 'Kelola Akun',
                'icon' => 'uil-user-circle',
                'link' => site_url('wilayah/users')
            ],
        ],
        'pelaksana' => [
            [
                'type' => 'title',
                'name' => 'Menu Pelaksana'
            ],
            [
                'name' => 'Dashboard',
                'icon' => 'uil-home-alt',
                'link' => site_url('pelaksana/dashboard')
            ],
            [
                'name' => 'Menu Monitoring',
                'icon' => 'uil-notes',
                'submenu' => [

                    [
                        'name' => 'Realisasi',
                        'icon' => 'uil-chart me-1',
                        'link' => site_url('pelaksana/realisasi')
                    ],
                    [
                        'name' => 'Target Optimasi',
                        'icon' => 'uil-bullseye me-1',
                        'link' => site_url('pelaksana/targetoptimasi')
                    ],
                    [
                        'name' => 'Monitoring Penggunaan',
                        'icon' => 'uil-monitor me-1',
                        'link' => site_url('pelaksana/monitoring')
                    ],
                    [
                        'name' => 'Pagu Terisa',
                        'icon' => 'uil-money-withdrawal me-1',
                        'link' => site_url('pelaksana/pagu-tersisa')
                    ],
                    [
                        'name' => 'Presentase',
                        'icon' => 'uil-percentage me-1',
                        'link' => site_url('pelaksana/presentase/realisasi')
                    ],
                ]
            ]
        ]
    ];

    return $menus[$role] ?? [];
}
