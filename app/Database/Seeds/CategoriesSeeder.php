<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'gl_account' => '6107100201',
                'gl_long_text' => 'Beban Baca Meter Dengan Anak Perusahaan',
            ],
            [
                'gl_account' => '6107200200',
                'gl_long_text' => 'Pemakaian Perkakas & Peralatan',
            ],
            [
                'gl_account' => '6107200400',
                'gl_long_text' => 'Perjalanan Dinas Non Diklat',
            ],
            [
                'gl_account' => '6107200700',
                'gl_long_text' => 'Teknologi Informasi',
            ],
            [
                'gl_account' => '6107200701',
                'gl_long_text' => 'Teknologi Informasi dgn Anak Perusahaan',
            ],
            [
                'gl_account' => '6107200800',
                'gl_long_text' => 'Listrik, Gas dan Air',
            ],
            [
                'gl_account' => '6107200900',
                'gl_long_text' => 'Pos & Telekomunikasi',
            ],
            [
                'gl_account' => '6107201000',
                'gl_long_text' => 'Beban Bank',
            ],
            [
                'gl_account' => '6107201100',
                'gl_long_text' => 'Bahan Makanan & Konsumsi',
            ],
            [
                'gl_account' => '6107201400',
                'gl_long_text' => 'Alat dan Keperluan Kantor',
            ],
            [
                'gl_account' => '6107201500',
                'gl_long_text' => 'Barang Cetakan dan Penerbitan',
            ],
            [
                'gl_account' => '6107201600',
                'gl_long_text' => 'Pajak dan Retribusi',
            ],
            [
                'gl_account' => '6107201700',
                'gl_long_text' => 'Iuran, Abodemen & Iklan',
            ],
            [
                'gl_account' => '6107100100',
                'gl_long_text' => 'Beban Pengelolaan Pelanggan',
            ],
            [
                'gl_account' => '6107100600',
                'gl_long_text' => 'Beban Pemutusan dan Penyambungan Kembali',
            ],
            [
                'gl_account' => '6107100900',
                'gl_long_text' => 'Beban Pemasaran',
            ],
            [
                'gl_account' => '6107202100',
                'gl_long_text' => 'Beban Penyisihan Material',
            ],
            [
                'gl_account' => '6107100700',
                'gl_long_text' => 'Beban P2TL',
            ],
            [
                'gl_account' => '6107100800',
                'gl_long_text' => 'Beban Penyisihan Piutang',
            ],
            [
                'gl_account' => '6107201900',
                'gl_long_text' => 'Beban Keamanan',
            ],
        ];

        $uniqueData = [];
        foreach ($data as $item) {
            $key = $item['gl_account'];
            if (!isset($uniqueData[$key])) {
                $uniqueData[$key] = $item;
            }
        }

        $this->db->table('categories')->insertBatch(array_values($uniqueData));
    }
}
