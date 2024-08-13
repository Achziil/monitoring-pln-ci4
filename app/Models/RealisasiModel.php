<?php

namespace App\Models;

use CodeIgniter\Model;

class RealisasiModel extends Model
{
    protected $table = 'realisasi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'busa',
        'bulan',
        'category_id',
        'sumber_data_id',
        'amount_local_curr',
    ];

    public function getDatatables($busa = null)
    {
        // Inisialisasi query builder untuk tabel realisasi dan join dengan tabel categories
        $builder = $this->db->table($this->table)
            ->select('realisasi.*, categories.gl_long_text')
            ->join('categories', 'realisasi.category_id = categories.id');

        // Jika busa tidak null, tambahkan filter berdasarkan busa
        if ($busa !== null) {
            $builder->where('realisasi.busa', $busa);
        }

        // Filter data berdasarkan input pencarian dari datatable
        if (isset($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
            $searchTerms = explode(' ', $search); // Pisahkan kata-kata pencarian menjadi array

            $builder->groupStart(); // Mulai grup kondisi OR
            foreach ($searchTerms as $term) {
                $builder->groupStart();
                $builder->like('categories.gl_long_text', $term);
                $builder->orLike('realisasi.busa', $term);
                $builder->orLike('realisasi.bulan', $term);
                $builder->orLike('realisasi.amount_local_curr', $term);
                $builder->groupEnd();
            }
            $builder->groupEnd(); // Akhiri grup kondisi OR
        }

        if(isset($_POST['bulan']) && !empty($_POST['bulan'])) {
            $bulan = $_POST['bulan'];
            $builder->where('MONTH(realisasi.bulan)', $bulan);
        }

        if(isset($_POST['tahun']) && !empty($_POST['tahun'])) {
            $tahun = $_POST['tahun'];
            $builder->where('YEAR(realisasi.bulan)', $tahun);
        }

        // Urutkan data berdasarkan kolom yang dipilih dari datatable
        if (isset($_POST['order'])) {
            $order = $_POST['order'][0]['column'];
            $dir = $_POST['order'][0]['dir'];
            $builder->orderBy($this->fieldMapping($order), $dir);
        }

        // Batasi jumlah data yang diambil untuk pagination
        $builder->limit($_POST['length'], $_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    public function getListOfYear() {
        $builder = $this->db->table($this->table)
            ->select('YEAR(bulan) AS tahun', false)
            ->groupBy('YEAR(bulan)')
            ->orderBy('tahun', 'DESC');
        return $builder->get()->getResult();
    }

    public function countFiltered($busa = null)
    {
        // Inisialisasi query builder untuk menghitung total data yang difilter
        $builder = $this->db->table($this->table)
            ->select('realisasi.id')
            ->join('categories', 'realisasi.category_id = categories.id');

        // Jika busa tidak null, tambahkan filter berdasarkan busa
        if ($busa !== null) {
            $builder->where('realisasi.busa', $busa);
        }

        // Filter data berdasarkan input pencarian dari datatable
        if (isset($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
            $builder->groupStart(); // Mulai grup kondisi OR
            $builder->like('categories.gl_long_text', $search);
            $builder->orLike('realisasi.busa', $search);
            $builder->orLike('realisasi.bulan', $search);
            $builder->orLike('realisasi.amount_local_curr', $search);
            $builder->groupEnd(); // Akhiri grup kondisi OR
        }


        if(isset($_POST['bulan']) && !empty($_POST['bulan'])) {
            $bulan = $_POST['bulan'];
            $builder->where('MONTH(realisasi.bulan)', $bulan);
        }

        if(isset($_POST['tahun']) && !empty($_POST['tahun'])) {
            $tahun = $_POST['tahun'];
            $builder->where('YEAR(realisasi.bulan)', $tahun);
        }

        // Hitung total data yang difilter
        $query = $builder->countAllResults();
        return $query;
    }

    public function countAll($busa = null)
    {
        // Inisialisasi query builder untuk menghitung total seluruh data
        $builder = $this->db->table($this->table);

        // Jika busa tidak null, tambahkan filter berdasarkan busa
        if ($busa !== null) {
            $builder->where('busa', $busa);
        }

        // Hitung total data
        $query = $builder->selectCount($this->primaryKey, 'total')->get();
        return $query->getRow()->total;
    }


    // Mapping indeks kolom dari datatable ke nama kolom di database
    private function fieldMapping($orderIndex)
    {
        $fields = [
            1 => 'categories.gl_long_text',
            2 => 'busa',
            3 => 'bulan',
            4 => 'amount_local_curr',
        ];

        return $fields[$orderIndex] ?? null;
    }



    // ============================================
    // AKSI BUTTON MELIHAT DETAIL REALISASI
    public function getDetailDatatables($busa, $bulan, $categoryId)
    {

        // Menghitung tanggal akhir bulan berdasarkan nilai $bulan
        $lastDay = date('Y-m-t', strtotime($bulan));

        $builder = $this->db->table('sumber_data')
            ->select('sumber_data.*, categories.gl_long_text')
            ->join('categories', 'sumber_data.category_id = categories.id')
            ->where('sumber_data.busa', $busa)
            ->where('sumber_data.category_id', $categoryId)
            ->where('sumber_data.posting_date >=', $bulan)
            ->where('sumber_data.posting_date <=', $lastDay);

        if (isset($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
            $builder->groupStart()
                ->like('categories.gl_long_text', $search)
                ->orLike('sumber_data.busa', $search)
                ->orLike('sumber_data.posting_date', $search)
                ->orLike('sumber_data.amount_local_curr', $search)
                ->orLike('sumber_data.text', $search)
                ->orLike('sumber_data.reason_for_trip', $search)
                ->orLike('sumber_data.document_header_text', $search)
                ->orLike('sumber_data.account', $search)
                ->groupEnd();
        }

        if (isset($_POST['order']) && isset($_POST['order'][0]['column'])) {
            $order = $_POST['order'][0]['column'];
            $dir = $_POST['order'][0]['dir'] ?? 'asc';
            $columnOrder = $this->detailColumnOrder()[$order] ?? null;
            if ($columnOrder !== null) {
                $builder->orderBy($columnOrder, $dir);
            }
        }

        $builder->limit($_POST['length'], $_POST['start']);

        return $builder->get()->getResult();
    }

    public function countAllDetail($busa, $bulan, $categoryId)
    {
        $lastDay = date('Y-m-t', strtotime($bulan));

        return $this->db->table('sumber_data')
            ->where('busa', $busa)
            ->where('category_id', $categoryId)
            ->where('posting_date >=', $bulan)
            ->where('posting_date <=', $lastDay)
            ->countAllResults();
    }

    public function countFilteredDetail($busa, $bulan, $categoryId)
    {
        $lastDay = date('Y-m-t', strtotime($bulan));

        $builder = $this->db->table('sumber_data')
            ->select('sumber_data.*, categories.gl_long_text')
            ->join('categories', 'sumber_data.category_id = categories.id')
            ->where('sumber_data.busa', $busa)
            ->where('sumber_data.category_id', $categoryId)
            ->where('sumber_data.posting_date >=', $bulan)
            ->where('sumber_data.posting_date <=', $lastDay);

        if (isset($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
            $builder->groupStart()
                ->like('categories.gl_long_text', $search)
                ->orLike('sumber_data.busa', $search)
                ->orLike('sumber_data.posting_date', $search)
                ->orLike('sumber_data.amount_local_curr', $search)
                ->orLike('sumber_data.text', $search)
                ->orLike('sumber_data.reason_for_trip', $search)
                ->orLike('sumber_data.document_header_text', $search)
                ->orLike('sumber_data.account', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    private function detailColumnOrder()
    {
        return [
            1 => 'sumber_data.posting_date',
            2 => 'categories.gl_long_text',
            3 => 'sumber_data.busa',
            4 => 'sumber_data.amount_local_curr',
            5 => 'sumber_data.text',
            6 => 'sumber_data.reason_for_trip',
            7 => 'sumber_data.account',
        ];
    }


    public function getDetailDataByMonth($busa, $bulan, $categoryId)
    {
        $builder = $this->db->table('sumber_data')
            ->select('sumber_data.*, categories.gl_long_text')
            ->join('categories', 'sumber_data.category_id = categories.id')
            ->where('sumber_data.busa', $busa)
            ->where('sumber_data.category_id', $categoryId)
            ->where('DATE_FORMAT(sumber_data.posting_date, "%Y-%m")', $bulan);

        return $builder->get()->getResultArray();
    }


    // =============================================
    // REALISASI MENU PRESENTASE
    public function getAvailableMonths($busa = '7600')
    {
        $builder = $this->db->table($this->table)
            ->select('DISTINCT(bulan)', false)
            ->where('busa', $busa)
            ->orderBy('bulan', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function getRealisasiPercentage($busa = '7600', $selectedMonth = null)
    {
        if ($selectedMonth === null) {
            // Mendapatkan bulan terbaru untuk busa tertentu
            $selectedMonth = $this->db->table('realisasi')
            ->select('MAX(bulan) AS latest_month')
            ->where('busa', $busa)
            ->get()
            ->getRow()
            ->latest_month ?? null;
        }

        $previousMonth = date('Y-m-d', strtotime($selectedMonth . ' -1 month'));
        // Mengambil data kategori dan menghitung realisasi serta optimasi
        $data = $this->db->table('categories')
            ->select('categories.id AS category_id, categories.gl_long_text AS jenis_biaya')
            ->select('COALESCE((SELECT SUM(amount_local_curr) FROM realisasi WHERE category_id = categories.id AND busa = ' . $this->db->escape($busa) . ' AND bulan <= ' . $this->db->escape($selectedMonth) . ' AND YEAR(bulan) = YEAR(' . $this->db->escape($selectedMonth) . ')), 0) AS realisasi', false)
            ->select('COALESCE((SELECT SUM(target_amount) FROM target_optimasi WHERE category_id = categories.id AND busa = ' . $this->db->escape($busa) . ' AND bulan <= ' . $this->db->escape($selectedMonth) . ' AND YEAR(bulan) = YEAR(' . $this->db->escape($selectedMonth) . ')), 0) AS optimasi', false)
            ->select('COALESCE((SELECT SUM(amount_local_curr) FROM realisasi WHERE category_id = categories.id AND busa = ' . $this->db->escape($busa) . ' AND bulan <= ' . $this->db->escape($previousMonth) . ' AND YEAR(bulan) = YEAR(' . $this->db->escape($selectedMonth) . ')), 0) AS realisasi_prev', false)
            ->select('COALESCE((SELECT SUM(target_amount) FROM target_optimasi WHERE category_id = categories.id AND busa = ' . $this->db->escape($busa) . ' AND bulan <= ' . $this->db->escape($previousMonth) . ' AND YEAR(bulan) = YEAR(' . $this->db->escape($selectedMonth) . ')), 0) AS optimasi_prev', false)
            ->orderBy('categories.id')
            ->get()
            ->getResultArray();

        // Inisialisasi variabel total realisasi, optimasi, dan persentase
        $totalRealisasi = 0;
        $totalOptimasi = 0;
        $totalPercentage = 0;
        $totalRealisasiPrev = 0;
        $totalOptimasiPrev = 0;
        $totalPercentagePrev = 0;
        $hasRealisasi = false;

        foreach ($data as &$row) {
            $row['realisasi'] = $row['realisasi'] != 0 ? $row['realisasi'] : '-';
            $row['optimasi'] = $row['optimasi'] != 0 ? $row['optimasi'] : '-';
            $row['realisasi_prev'] = $row['realisasi_prev'] != 0 ? $row['realisasi_prev'] : '-';
            $row['optimasi_prev'] = $row['optimasi_prev'] != 0 ? $row['optimasi_prev'] : '-';

            if ($row['realisasi'] !== '-') {
                $hasRealisasi = true;
                $totalRealisasi += $row['realisasi'];
            }

            if ($row['realisasi_prev'] !== '-') {
                $hasRealisasi = true;
                $totalRealisasiPrev += $row['realisasi_prev'];
            }

            if ($row['optimasi'] !== '-') {
                $totalOptimasi += $row['optimasi'];
            }

            if ($row['optimasi_prev'] !== '-') {
                $totalOptimasiPrev += $row['optimasi_prev'];
            }

            if ($row['realisasi'] !== '-' && $row['optimasi'] !== '-') {
                $row['percentage'] = $row['optimasi'] != 0 ? round(($row['realisasi'] / $row['optimasi']) * 100, 2) : '-';
            } else {
                $row['percentage'] = '-';
            }

            if ($row['realisasi_prev'] !== '-' && $row['optimasi_prev'] !== '-') {
                $row['percentage_prev'] = $row['optimasi_prev'] != 0 ? round(($row['realisasi_prev'] / $row['optimasi_prev']) * 100, 2) : '-';
            } else {
                $row['percentage_prev'] = '-';
            }
        }

        // Menghitung total persentase realisasi terhadap optimasi
        if ($hasRealisasi) {
            $totalPercentage = $totalOptimasi != 0 ? round(($totalRealisasi / $totalOptimasi) * 100, 2) : round(($totalRealisasi / 1) * 100, 2);
            $totalPercentagePrev = $totalOptimasiPrev != 0 ? round(($totalRealisasiPrev / $totalOptimasiPrev) * 100, 2) : round(($totalRealisasiPrev / 1) * 100, 2);
        }

        // Menambahkan total realisasi, optimasi, dan persentase ke data
        $data[] = [
            'jenis_biaya' => 'Total',
            'realisasi' => $totalRealisasi != 0 ? $totalRealisasi : '-',
            'optimasi' => $totalOptimasi != 0 ? $totalOptimasi : '-',
            'percentage' => $hasRealisasi ? $totalPercentage . '%' : '-',
            'realisasi_prev' => $totalRealisasiPrev != 0 ? $totalRealisasiPrev : '-',
            'optimasi_prev' => $totalOptimasiPrev != 0 ? $totalOptimasiPrev : '-',
            'percentage_prev' => $hasRealisasi ? $totalPercentagePrev . '%' : '-',
        ];

        return $data;
    }




    // UNTUK DASHBOARD PRESENTASE
    public function getRealisasiPercentageDashboard($busa = '7600')
    {
        // Mendapatkan bulan terbaru untuk busa tertentu
        $latestMonth = $this->db->table('realisasi')
            ->select('MAX(bulan) AS latest_month')
            ->where('busa', $busa)
            ->get()
            ->getRow()
            ->latest_month ?? null;

        // Mengambil data kategori dan menghitung realisasi serta optimasi
        $data = $this->db->table('categories')
            ->select('categories.id AS category_id, categories.gl_long_text AS jenis_biaya')
            ->select('COALESCE((SELECT SUM(amount_local_curr) FROM realisasi WHERE category_id = categories.id AND busa = ' . $this->db->escape($busa) . ' AND bulan <= ' . $this->db->escape($latestMonth) . '), 0) AS realisasi', false)
            ->select('COALESCE((SELECT SUM(target_amount) FROM target_optimasi WHERE category_id = categories.id AND busa = ' . $this->db->escape($busa) . ' AND bulan <= ' . $this->db->escape($latestMonth) . '), 0) AS optimasi', false)
            ->orderBy('categories.id')
            ->get()
            ->getResultArray();

        // Inisialisasi variabel total realisasi dan optimasi
        $totalRealisasi = 0;
        $totalOptimasi = 0;

        // Menghitung total realisasi dan optimasi untuk setiap kategori
        foreach ($data as &$row) {
            $totalRealisasi += $row['realisasi'];
            $totalOptimasi += $row['optimasi'];
        }

        // Menghitung total persentase realisasi terhadap optimasi
        $totalPercentage = $totalOptimasi != 0 ? round(($totalRealisasi / $totalOptimasi) * 100, 2) : round(($totalRealisasi / 1) * 100, 2);

        // Mengembalikan data total realisasi, optimasi, dan persentase
        return [
            'jenis_biaya' => 'Total',
            'realisasi' => $totalRealisasi,
            'optimasi' => $totalOptimasi,
            'percentage' => $totalPercentage,
        ];
    }


    // Mengambil laporan tertinggi realisasi per tahun
    public function getTopCategoryRealisasiYearly($busa = null)
    {
        // Menginisialisasi query builder untuk tabel 'realisasi'
        $builder = $this->db->table('realisasi');
        $builder->select('categories.gl_long_text as category, SUM(realisasi.amount_local_curr) as total');
        $builder->join('categories', 'categories.id = realisasi.category_id');
        $builder->where('realisasi.bulan >= DATE_FORMAT(CURDATE() ,\'%Y-01-01\')'); // Adjust to fetch data from the start of the year

        // Menyaring data berdasarkan busa jika disediakan
        if ($busa) {
            $builder->where('realisasi.busa', $busa);
        }

        $builder->groupBy('categories.gl_long_text');
        $builder->orderBy('total', 'DESC');
        $builder->limit(5);

        // Mengambil data hasil query
        return $builder->get()->getResultArray();
    }


    // Mengambil laporan kategoris tertinggi realisasi per bulan dalam 1 tahun
    public function getTopCategoryRealisasiByMonth($busa = null, $month, $year)
    {
        $builder = $this->db->table('realisasi');
        $builder->select('categories.gl_long_text as category, SUM(realisasi.amount_local_curr) as total');
        $builder->join('categories', 'categories.id = realisasi.category_id');
        $builder->where('MONTH(realisasi.bulan)', $month);
        $builder->where('YEAR(realisasi.bulan)', $year);

        if ($busa) {
            $builder->where('realisasi.busa', $busa);
        }

        $builder->groupBy('categories.gl_long_text');
        $builder->orderBy('total', 'DESC');
        $builder->limit(5);

        return $builder->get()->getResultArray();
    }


    public function getTotalRealisasiOptimasi($busa = null)
    {
        $builder = $this->db->table($this->table);
        $builder->selectSum('amount_local_curr', 'total_realisasi');

        if ($busa === null) {
            // Mengambil total realisasi dari semua busa kecuali 7600
            $builder->where('busa !=', '7600');
        } else {
            $builder->where('busa', $busa);
        }
        
        $totalRealisasi = $builder->get()->getRow()->total_realisasi;

        // Mengambil total target optimasi tanpa kategori "All" dan busa "7600"
        $builder = $this->db->table('target_optimasi');
        $builder->selectSum('target_amount', 'total_optimasi');
        $builder->where('busa !=', 'All');
        $builder->where('busa !=', '7600');

        if ($busa !== null && $busa !== '7600') {
            $builder->where('busa', $busa);
        }

        $totalOptimasi = $builder->get()->getRow()->total_optimasi;

        $sisaAnggaran = $totalOptimasi - $totalRealisasi;

        return [
            'total_realisasi' => $totalRealisasi,
            'total_optimasi' => $totalOptimasi,
            'sisa_anggaran' => $sisaAnggaran
        ];
    }

    public function getTotalRealisasiOptimasiByBusa($busa)
    {
        $builder = $this->db->table($this->table);
        $builder->selectSum('amount_local_curr', 'total_realisasi');

        if ($busa === null) {
            // Mengambil total realisasi dari semua busa kecuali 7600
            $builder->where('busa !=', '7600');
        } else {
            $builder->where('busa', $busa);
        }
        
        $totalRealisasi = $builder->get()->getRow()->total_realisasi;

        // Mengambil total target optimasi tanpa kategori "All" dan busa "7600"
        $builder = $this->db->table('target_optimasi');
        $builder->selectSum('target_amount', 'total_optimasi');
        $builder->where('busa !=', 'All');
        $builder->where('busa !=', '7600');

        if ($busa !== null && $busa !== '7600') {
            $builder->where('busa', $busa);
        }

        $totalOptimasi = $builder->get()->getRow()->total_optimasi;

        $sisaAnggaran = $totalOptimasi - $totalRealisasi;

        return [
            'total_realisasi' => $totalRealisasi,
            'total_optimasi' => $totalOptimasi,
            'sisa_anggaran' => $sisaAnggaran
        ];
    }
    
    public function getTotalPerKategori($busa)
    {
        $builder = $this->db->table($this->table)
            ->select('categories.gl_long_text, SUM(realisasi.amount_local_curr) as total_amount_local_curr')
            ->join('categories', 'realisasi.category_id = categories.id')
            ->where('realisasi.busa', $busa)
            ->groupBy('categories.gl_long_text');

        return $builder->get()->getResultArray();
    }
}
