<?php

namespace App\Models;

use CodeIgniter\Model;

class MonitoringModel extends Model
{
    protected $table = 'monitoring_optimasi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['busa', 'bulan', 'category_id', 'target_amount', 'realisasi_amount', 'selisih_amount', 'last_refresh_date'];

    // menampiklkan index.php monitorring = all untuk level admin dan wilayah sedangkan pelaksana hanya user itu sendiri
    public function getMonitoringData($busa = '7600')
    {
        $tahun = $_POST['tahun'] ?? date('Y');
        
        $builder = $this->db->table('categories');
        $builder->select('categories.id AS category_id, categories.gl_long_text');
        $builder->select("
        CASE WHEN SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 1 THEN monitoring_optimasi.selisih_amount ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 1 THEN monitoring_optimasi.selisih_amount ELSE 0 END) END AS jan,
        CASE WHEN SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 2 THEN monitoring_optimasi.selisih_amount ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 2 THEN monitoring_optimasi.selisih_amount ELSE 0 END) END AS feb,
        CASE WHEN SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 3 THEN monitoring_optimasi.selisih_amount ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 3 THEN monitoring_optimasi.selisih_amount ELSE 0 END) END AS mar,
        CASE WHEN SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 4 THEN monitoring_optimasi.selisih_amount ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 4 THEN monitoring_optimasi.selisih_amount ELSE 0 END) END AS apr,
        CASE WHEN SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 5 THEN monitoring_optimasi.selisih_amount ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 5 THEN monitoring_optimasi.selisih_amount ELSE 0 END) END AS mei,
        CASE WHEN SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 6 THEN monitoring_optimasi.selisih_amount ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 6 THEN monitoring_optimasi.selisih_amount ELSE 0 END) END AS jun,
        CASE WHEN SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 7 THEN monitoring_optimasi.selisih_amount ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 7 THEN monitoring_optimasi.selisih_amount ELSE 0 END) END AS jul,
        CASE WHEN SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 8 THEN monitoring_optimasi.selisih_amount ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 8 THEN monitoring_optimasi.selisih_amount ELSE 0 END) END AS aug,
        CASE WHEN SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 9 THEN monitoring_optimasi.selisih_amount ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 9 THEN monitoring_optimasi.selisih_amount ELSE 0 END) END AS sep,
        CASE WHEN SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 10 THEN monitoring_optimasi.selisih_amount ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 10 THEN monitoring_optimasi.selisih_amount ELSE 0 END) END AS okt,
        CASE WHEN SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 11 THEN monitoring_optimasi.selisih_amount ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 11 THEN monitoring_optimasi.selisih_amount ELSE 0 END) END AS nov,
        CASE WHEN SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 12 THEN monitoring_optimasi.selisih_amount ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(monitoring_optimasi.bulan) = 12 THEN monitoring_optimasi.selisih_amount ELSE 0 END) END AS des,
        CASE WHEN SUM(monitoring_optimasi.selisih_amount) = 0 THEN '-' ELSE SUM(monitoring_optimasi.selisih_amount) END AS total
    ");
        $builder->join('monitoring_optimasi', 'categories.id = monitoring_optimasi.category_id AND monitoring_optimasi.busa = "' . $busa . '"', 'left');
        $builder->where('YEAR(monitoring_optimasi.bulan)', $tahun);
        $builder->groupBy('categories.id');
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getListOfYear() {
        $builder = $this->db->table('target_optimasi');
        $builder->select('YEAR(bulan) as tahun');
        $builder->groupBy('tahun');
        $builder->orderBy('tahun', 'DESC');
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function refreshMonitoringData()
    {
        $db = \Config\Database::connect();
        $db->transStart(); // Memulai transaksi database

        // Menghapus semua data monitoring optimasi yang lama
        $this->db->table($this->table)->truncate();

        // Mengambil semua data kategori dari tabel kategori
        $categories = $this->db->table('categories')->get()->getResultArray();

        foreach ($categories as $category) {
            $annualTotals = []; // Array untuk menyimpan total tahunan per busa

            // Mengambil data target optimasi per kategori
            $targetOptimasi = $this->db->table('target_optimasi')
                ->where('category_id', $category['id'])
                ->get()
                ->getResultArray();

            // Mengambil data realisasi per kategori
            $realisasi = $this->db->table('realisasi')
                ->where('category_id', $category['id'])
                ->get()
                ->getResultArray();

            // Menghitung selisih antara target optimasi dan realisasi untuk setiap bulan
            foreach ($targetOptimasi as $target) {
                $bulan = $target['bulan'];
                $busa = $target['busa'];
                $targetAmount = (float) $target['target_amount'];
                $realisasiAmount = 0;

                foreach ($realisasi as $real) {
                    if ($real['bulan'] === $bulan && $real['busa'] === $busa) {
                        $realisasiAmount = (float) $real['amount_local_curr'];
                        break; // Keluar dari loop jika sudah menemukan data yang cocok
                    }
                }

                $selisihAmount = $targetAmount - $realisasiAmount; // Menghitung selisih

                // Menyimpan data ke tabel monitoring optimasi
                $data = [
                    'busa' => $busa,
                    'bulan' => $bulan,
                    'category_id' => $category['id'],
                    'target_amount' => $targetAmount,
                    'realisasi_amount' => $realisasiAmount,
                    'selisih_amount' => $selisihAmount,
                ];

                $this->db->table($this->table)->insert($data);

                // Menambahkan selisih ke total tahunan untuk busa dan kategori tertentu
                if (!isset($annualTotals[$busa])) {
                    $annualTotals[$busa] = 0;
                }
                $annualTotals[$busa] += $selisihAmount;
            }

            // Memperbarui total tahunan di database untuk setiap busa
            foreach ($annualTotals as $busa => $total) {
                $this->db->table($this->table)
                    ->where('category_id', $category['id'])
                    ->where('busa', $busa)
                    ->update(['annual_total_amount' => $total]);
            }
        }

        // Menyimpan tanggal terakhir dilakukan refresh
        $this->db->table($this->table)->update(['last_refresh_date' => date('Y-m-d H:i:s')]);

        $db->transComplete(); // Menyelesaikan transaksi
    }


    // mengambil dan mencari update waktu terakhir refresh
    public function getLastRefreshDate()
    {
        $builder = $this->db->table($this->table);
        $builder->select('last_refresh_date');
        $builder->orderBy('last_refresh_date', 'DESC');
        $builder->limit(1);
        $query = $builder->get();
        $row = $query->getRowArray();
        return $row['last_refresh_date'] ?? '';
    }


    // menampilkan detail monitroing (action = detail) detail.php
    public function getMonitoringDataByBusa($categoryId)
    {
        $builder = $this->db->table('categories');
        $builder->select('categories.id AS category_id, categories.gl_long_text, target_optimasi.busa');
        $builder->select("
            CASE WHEN SUM(CASE WHEN MONTH(target_optimasi.bulan) = 1 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(target_optimasi.bulan) = 1 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) END AS jan,
            CASE WHEN SUM(CASE WHEN MONTH(target_optimasi.bulan) = 2 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(target_optimasi.bulan) = 2 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) END AS feb,
            CASE WHEN SUM(CASE WHEN MONTH(target_optimasi.bulan) = 3 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(target_optimasi.bulan) = 3 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) END AS mar,
            CASE WHEN SUM(CASE WHEN MONTH(target_optimasi.bulan) = 4 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(target_optimasi.bulan) = 4 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) END AS apr,
            CASE WHEN SUM(CASE WHEN MONTH(target_optimasi.bulan) = 5 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(target_optimasi.bulan) = 5 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) END AS mei,
            CASE WHEN SUM(CASE WHEN MONTH(target_optimasi.bulan) = 6 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(target_optimasi.bulan) = 6 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) END AS jun,
            CASE WHEN SUM(CASE WHEN MONTH(target_optimasi.bulan) = 7 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(target_optimasi.bulan) = 7 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) END AS jul,
            CASE WHEN SUM(CASE WHEN MONTH(target_optimasi.bulan) = 8 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(target_optimasi.bulan) = 8 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) END AS aug,
            CASE WHEN SUM(CASE WHEN MONTH(target_optimasi.bulan) = 9 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(target_optimasi.bulan) = 9 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) END AS sep,
            CASE WHEN SUM(CASE WHEN MONTH(target_optimasi.bulan) = 10 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(target_optimasi.bulan) = 10 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) END AS okt,
            CASE WHEN SUM(CASE WHEN MONTH(target_optimasi.bulan) = 11 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(target_optimasi.bulan) = 11 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) END AS nov,
            CASE WHEN SUM(CASE WHEN MONTH(target_optimasi.bulan) = 12 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) = 0 THEN '-' ELSE SUM(CASE WHEN MONTH(target_optimasi.bulan) = 12 THEN COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0) ELSE 0 END) END AS des,
            CASE WHEN SUM(COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0)) = 0 THEN '-' ELSE SUM(COALESCE(target_optimasi.target_amount, 0) - COALESCE(realisasi.amount_local_curr, 0)) END AS total
        ");
        $builder->join('target_optimasi', 'categories.id = target_optimasi.category_id AND target_optimasi.busa != "7600"', 'left');
        $builder->join('realisasi', 'categories.id = realisasi.category_id AND target_optimasi.busa = realisasi.busa AND target_optimasi.bulan = realisasi.bulan', 'left');
        $builder->where('categories.id', $categoryId);
        $builder->groupBy('categories.id, target_optimasi.busa');
        $query = $builder->get();
        return $query->getResultArray();
    }
}
