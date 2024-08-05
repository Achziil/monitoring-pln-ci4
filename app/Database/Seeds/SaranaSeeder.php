<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SaranaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'kategori' => 'Sarana',
                'detail' => 'Mobil X',
                'slug' => 'mobil x',
                'status' => '0',
                'pemilik' => 'UIW',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'kategori' => 'Fasilitas',
                'detail' => 'Ruangan X',
                'slug' => 'ruangan x',
                'status' => '1',
                'pemilik' => 'UIW',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'kategori' => 'Sarana',
                'detail' => 'Kertas HVS',
                'slug' => 'kertas-hvs',
                'status' => '1',
                'pemilik' => 'UIW',
                'created_at' => '2024-05-03 05:54:56',
                'updated_at' => '2024-05-03 05:54:56',
            ],
            [
                'kategori' => 'Fasilitas',
                'detail' => 'Musholla Baru',
                'slug' => 'musholla-baru',
                'status' => '1',
                'pemilik' => 'UIW',
                'created_at' => '2024-05-12 12:51:01',
                'updated_at' => '2024-06-02 04:41:00',
            ],
        ];

        $this->db->table('sarana')->insertBatch($data);
    }
}
