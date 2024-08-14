<?php

namespace App\Models;

use CodeIgniter\Model;

class PaguTersisaModel extends Model
{
    protected $table = 'pagu_tersisa';
    protected $allowedFields = ['busa', 'bulan', 'category_id', 'pagu_amount_negative', 'pagu_amount', 'last_refresh_date'];

    public function getPaguTersisaData($busa = '7600')
    {
        $tahun = $_POST['tahun'] ?? date('Y');
        // Menginisialisasi query builder untuk tabel 'categories'
        $builder = $this->db->table('categories');
        // Memilih kolom-kolom yang diperlukan dari tabel 'categories'
        $builder->select('categories.id AS category_id, categories.gl_long_text');
        // Menambahkan subquery untuk mendapatkan jumlah pagu tersisa per bulan dan mengubah agar negatif menjadi nol
        $builder->select("
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 1 THEN pagu_tersisa.pagu_amount END), '-') AS jan,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 2 THEN pagu_tersisa.pagu_amount END), '-') AS feb,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 3 THEN pagu_tersisa.pagu_amount END), '-') AS mar,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 4 THEN pagu_tersisa.pagu_amount END), '-') AS apr,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 5 THEN pagu_tersisa.pagu_amount END), '-') AS mei,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 6 THEN pagu_tersisa.pagu_amount END), '-') AS jun,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 7 THEN pagu_tersisa.pagu_amount END), '-') AS jul,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 8 THEN pagu_tersisa.pagu_amount END), '-') AS aug,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 9 THEN pagu_tersisa.pagu_amount END), '-') AS sep,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 10 THEN pagu_tersisa.pagu_amount END), '-') AS okt,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 11 THEN pagu_tersisa.pagu_amount END), '-') AS nov,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 12 THEN pagu_tersisa.pagu_amount END), '-') AS des,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 1 THEN pagu_tersisa.pagu_amount_negative END), 0) AS jan_negative,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 2 THEN pagu_tersisa.pagu_amount_negative END), 0) AS feb_negative,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 3 THEN pagu_tersisa.pagu_amount_negative END), 0) AS mar_negative,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 4 THEN pagu_tersisa.pagu_amount_negative END), 0) AS apr_negative,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 5 THEN pagu_tersisa.pagu_amount_negative END), 0) AS mei_negative,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 6 THEN pagu_tersisa.pagu_amount_negative END), 0) AS jun_negative,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 7 THEN pagu_tersisa.pagu_amount_negative END), 0) AS jul_negative,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 8 THEN pagu_tersisa.pagu_amount_negative END), 0) AS aug_negative,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 9 THEN pagu_tersisa.pagu_amount_negative END), 0) AS sep_negative,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 10 THEN pagu_tersisa.pagu_amount_negative END), 0) AS okt_negative,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 11 THEN pagu_tersisa.pagu_amount_negative END), 0) AS nov_negative,
            COALESCE(MAX(CASE WHEN MONTH(pagu_tersisa.bulan) = 12 THEN pagu_tersisa.pagu_amount_negative END), 0) AS des_negative
        ");
        // Melakukan join dengan tabel 'pagu_tersisa' berdasarkan category_id dan busa
        $builder->join('pagu_tersisa', 'categories.id = pagu_tersisa.category_id AND pagu_tersisa.busa = "' . $busa . '"', 'left');
        $builder->where('YEAR(pagu_tersisa.bulan)', $tahun);
        // Mengelompokkan data berdasarkan kategori
        $builder->groupBy('categories.id');
        // Menjalankan query dan mendapatkan hasilnya
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


    public function getPaguTersisaDataWithPercentage($busa = '7600')
    {
        // Mengambil semua data pagu tersisa dengan busa "All"
        // $data = $this->getPaguTersisaData();
        $data = $this->getPaguTersisaData($busa);
        foreach ($data as &$item) {
            // Mengambil total_target_amount dari tabel target_optimasi
            // untuk setiap kategori dengan busa "All"
            $targetOptimasi = $this->db->table('target_optimasi')
                ->select('SUM(target_amount) AS total_target_amount')
                ->where('category_id', $item['category_id'])
                ->where('busa', '7600')
                ->get()
                ->getRow();

            // Mengambil nilai total_target_amount, jika tidak ada maka diset ke 0
            $totalTargetAmount = $targetOptimasi->total_target_amount ?? 0;

            // Menghitung persentase jika total_target_amount > 0 dan nilai des bukan '-'
            if ($totalTargetAmount > 0 && $item['des'] !== '-') {
                // Menghitung persentase dengan membagi nilai des (dinegatifkan) dengan total_target_amount
                $item['percentage'] = ($item['des'] / $totalTargetAmount) * 100;
            } else {
                // Jika tidak memenuhi kondisi, set persentase menjadi '-'
                $item['percentage'] = '-';
            }
        }

        // Mengembalikan data pagu tersisa dengan persentase
        return $data;
    }

    public function refreshPaguTersisaData()
    {
        $this->db->table($this->table)->truncate();

        // Retrieve monitoring data grouped by busa and category with cumulative sum calculation for selisih_amount
        $monitoringData = $this->db->query("
            SELECT busa, category_id, bulan, 
                   realisasi_amount, target_amount, 
                   selisih_amount, 
                   SUM(selisih_amount) OVER (PARTITION BY busa, category_id ORDER BY bulan) as cumulative_pagu
            FROM monitoring_optimasi
            ORDER BY busa, category_id, bulan
        ")->getResultArray();
        
        $firstMonthHandled = [];
        $insertMonitoringData = [];

        foreach ($monitoringData as $data) {
            $busa = $data['busa'];
            $category = $data['category_id'];
            $bulan = $data['bulan'];

            if (!isset($firstMonthHandled[$busa][$category])) {
                // Handle the first month by calculating directly from realisasi_amount and target_amount
                $paguTersisa = $data['target_amount'] - $data['realisasi_amount'];
                $firstMonthHandled[$busa][$category] = true;
            } else {
                // For subsequent months, use the cumulative calculation
                $paguTersisa = $data['cumulative_pagu'];
            }

            $paguTersisaNegative = 0;
            if ($paguTersisa < 0) {
                $paguTersisaNegative = $paguTersisa;
                $paguTersisa = 0;
            }
            $insertMonitoringData[] = [
                'busa' => $busa,
                'bulan' => $bulan,
                'category_id' => $category,
                'pagu_amount' => $paguTersisa,
                'pagu_amount_negative' => $paguTersisaNegative,
                'last_refresh_date' => date('Y-m-d H:i:s')
            ];
            // $this->insert([
            //     'busa' => $busa,
            //     'bulan' => $bulan,
            //     'category_id' => $category,
            //     'pagu_amount' => $paguTersisa,
            //     'pagu_amount_negative' => $paguTersisaNegative,
            //     'last_refresh_date' => date('Y-m-d H:i:s')
            // ]);
        }

        if(!empty($insertMonitoringData)) $this->db->table($this->table)->insertBatch($insertMonitoringData);
    }

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

    // ditampilkan pada detail pagu tersisa (Actions)
    public function getPaguTersisaDataByBusa($categoryId)
    {
        // Menginisialisasi query builder untuk tabel 'categories'
        $builder = $this->db->table('categories');
        // Memilih kolom-kolom yang diperlukan dari tabel 'categories' dan 'pagu_tersisa'
        $builder->select('categories.gl_long_text, pagu_tersisa.busa, pagu_tersisa.bulan, pagu_tersisa.pagu_amount');
        // Melakukan join dengan tabel 'pagu_tersisa' berdasarkan category_id dan busa yang bukan 'All'
        $builder->join('pagu_tersisa', 'categories.id = pagu_tersisa.category_id AND pagu_tersisa.busa != "7600"', 'left');
        // Menambahkan kondisi untuk memfilter berdasarkan categoryId
        $builder->where('categories.id', $categoryId);
        // Menjalankan query dan mendapatkan hasilnya
        $query = $builder->get();
        $result = $query->getResultArray();

        // Inisialisasi array untuk menampung data hasil pemrosesan
        $data = [];

        // Mapping nama bulan dalam bahasa Inggris ke format singkatan
        $bulanMapping = [
            'Jan' => 'jan',
            'Feb' => 'feb',
            'Mar' => 'mar',
            'Apr' => 'apr',
            'May' => 'mei',
            'Jun' => 'jun',
            'Jul' => 'jul',
            'Aug' => 'aug',
            'Sep' => 'sep',
            'Oct' => 'okt',
            'Nov' => 'nov',
            'Dec' => 'des'
        ];

        // Melakukan iterasi pada hasil query
        foreach ($result as $row) {
            // Mendapatkan singkatan nama bulan dari kolom 'bulan'
            $bulan = date('M', strtotime($row['bulan']));
            // Memastikan bahwa bulan ada dalam mapping
            if (isset($bulanMapping[$bulan])) {
                // Mengubah singkatan nama bulan menjadi format yang diinginkan
                $bulan = $bulanMapping[$bulan];
                // Inisialisasi data untuk busa yang belum ada dalam array
                if (!isset($data[$row['busa']])) {
                    $data[$row['busa']] = [
                        'gl_long_text' => $row['gl_long_text'],
                        'busa' => $row['busa'],
                        'jan' => 0,
                        'feb' => 0,
                        'mar' => 0,
                        'apr' => 0,
                        'mei' => 0,
                        'jun' => 0,
                        'jul' => 0,
                        'aug' => 0,
                        'sep' => 0,
                        'okt' => 0,
                        'nov' => 0,
                        'des' => 0,
                    ];
                }
                // Menambahkan pagu_amount ke bulan yang sesuai
                $data[$row['busa']][$bulan] += $row['pagu_amount'];
            }
        }

        // Mengembalikan hasil dalam bentuk array dengan kunci numerik
        return array_values($data);
    }


    public function getPaguTersisaDataByBusaWithPercentage($categoryId)
    {
        // Mengambil data pagu tersisa berdasarkan kategori
        $data = $this->getPaguTersisaDataByBusa($categoryId);

        foreach ($data as &$item) {
            // Mengambil total_target_amount dari tabel target_optimasi
            // untuk setiap kategori dan busa yang sesuai
            $targetOptimasi = $this->db->table('target_optimasi')
                ->select('SUM(target_amount) AS total_target_amount')
                ->where('category_id', $categoryId)
                ->where('busa', $item['busa'])
                ->get()
                ->getRow();

            // Mengambil nilai total_target_amount, jika tidak ada maka diset ke 0
            $totalTargetAmount = $targetOptimasi->total_target_amount ?? 0;

            // Menghitung persentase jika total_target_amount > 0 dan nilai des > 0
            if ($totalTargetAmount > 0 && $item['des'] > 0) {
                // Menghitung persentase dengan membagi nilai des dengan total_target_amount
                $item['percentage'] = ($item['des'] / $totalTargetAmount) * 100;
            } else {
                // Jika tidak memenuhi kondisi, set persentase menjadi null
                $item['percentage'] = null;
            }
        }

        // Mengembalikan data pagu tersisa dengan persentase
        return $data;
    }
}
