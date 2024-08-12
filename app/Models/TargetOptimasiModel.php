<?php

namespace App\Models;

use CodeIgniter\Model;

class TargetOptimasiModel extends Model
{
    protected $table = 'target_optimasi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'busa',
        'bulan',
        'category_id',
        'target_amount',
        'total_target_amount',
    ];


    public function getDatatables($busa = null)
    {
        // Inisialisasi query builder untuk tabel target_optimasi dan join dengan tabel categories
        $builder = $this->db->table($this->table)
            ->select('target_optimasi.*, categories.gl_long_text')
            ->join('categories', 'target_optimasi.category_id = categories.id');

        // Filter data berdasarkan busa jika tidak 'All' dan tidak null
        if ($busa !== '' && $busa !== null) {
            $builder->where('target_optimasi.busa', $busa);
        }

        // Filter data berdasarkan input pencarian dari datatable
        if (isset($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
            $builder->groupStart();
            $builder->like('categories.gl_long_text', $search)
                    ->orLike('target_optimasi.busa', $search)
                    ->orLike('target_optimasi.bulan', $search)
                    ->orLike('target_optimasi.target_amount', $search);
            $builder->groupEnd();
        }

        
        // Filter data berdasarkan filter busa dari datatable
        if (isset($_POST['busaFilter']) && $_POST['busaFilter'] != '7600') {
            $builder->where('target_optimasi.busa', $_POST['busaFilter']);
        }

        if(isset($_POST['bulan']) && !empty($_POST['bulan'])) {
            $bulan = $_POST['bulan'];
            $builder->where('MONTH(target_optimasi.bulan)', $bulan);
        }

        if(isset($_POST['tahun']) && !empty($_POST['tahun'])) {
            $tahun = $_POST['tahun'];
            $builder->where('YEAR(target_optimasi.bulan)', $tahun);
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
            ->select('YEAR(bulan) as tahun')
            ->groupBy('YEAR(bulan)')
            ->orderBy('YEAR(bulan)', 'DESC');

        $query = $builder->get();
        return $query->getResult();
    }

    public function countFiltered($busa = null)
    {
        // Inisialisasi query builder untuk menghitung total data yang difilter
        $builder = $this->db->table($this->table)
            ->select('target_optimasi.id')
            ->join('categories', 'target_optimasi.category_id = categories.id');

        // Filter data berdasarkan busa jika tidak 'All' dan tidak null
        if ($busa !== '' && $busa !== null) {
            $builder->where('target_optimasi.busa', $busa);
        }

        // Filter data berdasarkan input pencarian dari datatable
        if (isset($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
            $builder->groupStart();
            $builder->like('categories.gl_long_text', $search)
                    ->orLike('target_optimasi.busa', $search)
                    ->orLike('target_optimasi.bulan', $search)
                    ->orLike('target_optimasi.target_amount', $search);
            $builder->groupEnd();
        }

        // Filter data berdasarkan filter busa dari datatable
        if (isset($_POST['busaFilter']) && $_POST['busaFilter'] != '7600') {
            $builder->where('target_optimasi.busa', $_POST['busaFilter']);
        }

        if(isset($_POST['bulan']) && !empty($_POST['bulan'])) {
            $bulan = $_POST['bulan'];
            $builder->where('MONTH(target_optimasi.bulan)', $bulan);
        }

        if(isset($_POST['tahun']) && !empty($_POST['tahun'])) {
            $tahun = $_POST['tahun'];
            $builder->where('YEAR(target_optimasi.bulan)', $tahun);
        }

        // Hitung total data yang difilter
        $query = $builder->countAllResults();
        return $query;
    }

    public function countAll($busa = null)
    {
        // Inisialisasi query builder untuk menghitung total seluruh data
        $builder = $this->db->table($this->table);

        // Filter data berdasarkan busa jika tidak 'All' dan tidak null
        if ($busa !== '7600' && $busa !== null) {
            $builder->where('busa', $busa);
        }

        // Hitung total seluruh data
        $query = $builder->selectCount($this->primaryKey, 'total')->get();
        return $query->getRow()->total;
    }

    // Mapping indeks kolom dari datatable ke nama kolom di database
    private function fieldMapping($orderIndex)
    {
        $fields = [
            1 => 'categories.gl_long_text',
            2 => 'target_optimasi.busa',
            3 => 'target_optimasi.bulan',
            4 => 'target_optimasi.target_amount',
        ];

        return $fields[$orderIndex] ?? null;
    }


    public function saveTargetOptimasi($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Simpan data target optimasi
        $this->insert($data);

        // Update data "All" berdasarkan kategori yang sama
        $this->updateAllData($data['category_id'], $data['bulan']);

        // Update total target amount dalam 1 tahun untuk busa yang bukan "All"
        if ($data['busa'] !== '7600') {
            $this->updateTotalTargetAmountForBusa($data['busa'], $data['category_id']);
        }

        // Update total target amount dalam 1 tahun untuk busa "All"
        $this->updateTotalTargetAmountForAll($data['category_id']);

        $db->transComplete();

        return $db->transStatus();
    }

    public function updateTargetOptimasi($id, $data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Update data target optimasi
        $this->update($id, $data);

        // Jika busa yang diupdate bukan "All", update data "All"
        if ($data['busa'] !== '7600') {
            $this->updateAllData($data['category_id'], $data['bulan']);
        }

        // Update total target amount dalam 1 tahun untuk busa yang bukan "All"
        if ($data['busa'] !== '7600') {
            $this->updateTotalTargetAmountForBusa($data['busa'], $data['category_id']);
        }

        // Update total target amount dalam 1 tahun untuk busa "All"
        $this->updateTotalTargetAmountForAll($data['category_id']);

        $db->transComplete();

        return $db->transStatus();
    }

    public function deleteTargetOptimasi($id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Ambil data target optimasi sebelum dihapus
        $targetOptimasi = $this->find($id);

        // Hapus data target optimasi
        $this->delete($id);

        // Jika busa yang dihapus bukan "All", update data "All"
        if ($targetOptimasi['busa'] !== '7600') {
            $this->updateAllData($targetOptimasi['category_id'], $targetOptimasi['bulan']);
        }

        // Update total target amount dalam 1 tahun untuk busa yang bukan "All"
        if ($targetOptimasi['busa'] !== '7600') {
            $this->updateTotalTargetAmountForBusa($targetOptimasi['busa'], $targetOptimasi['category_id']);
        }

        // Update total target amount dalam 1 tahun untuk busa "All"
        $this->updateTotalTargetAmountForAll($targetOptimasi['category_id']);

        $db->transComplete();

        return $db->transStatus();
    }

    private function updateTotalTargetAmountForBusa($busa, $categoryId)
    {
        // Hitung total target amount dalam 1 tahun berdasarkan busa dan kategori yang sama
        $total = $this->where('busa', $busa)
            ->where('category_id', $categoryId)
            ->selectSum('target_amount')
            ->get();

        $totalAmount = 0;

        // Mendapatkan baris total_target_amount sesuai busa dan kategori yang sama
        if ($total !== false && $total->getNumRows() > 0) {
            $totalAmount = $total->getRowArray()['target_amount'];
        }

        // Update total_target_amount untuk busa dan kategori yang sama
        $this->where('busa', $busa)
            ->where('category_id', $categoryId)
            ->set('total_target_amount', $totalAmount)
            ->update();
    }

    private function updateTotalTargetAmountForAll($categoryId)
    {
        // Hitung total target amount dalam 1 tahun untuk busa "All" berdasarkan kategori yang sama
        $total = $this->where('busa', '7600')
            ->where('category_id', $categoryId)
            ->selectSum('target_amount')
            ->get();

        $totalAmount = 0;

        // Mendapatkan baris total_target_amount untuk busa "All" sesuai kategori yang sama
        if ($total !== false && $total->getNumRows() > 0) {
            $totalAmount = $total->getRowArray()['target_amount'];
        }

        // Update total_target_amount untuk busa "All" dan kategori yang sama
        $this->where('busa', '7600')
            ->where('category_id', $categoryId)
            ->set('total_target_amount', $totalAmount)
            ->update();
    }



    private function updateAllData($categoryId, $bulan)
    {
        // Hitung total target amount berdasarkan kategori yang sama
        $total = $this->where('category_id', $categoryId)
            ->where('bulan', $bulan)
            ->where('busa !=', '7600')
            ->selectSum('target_amount')
            ->get();

        $totalAmount = 0;

        // mendapatkan baris target_amount sesuai kategori yang sama
        if ($total !== false && $total->getNumRows() > 0) {
            $totalAmount = $total->getRowArray()['target_amount'];
        }

        // Cek apakah data "All" sudah ada
        $allData = $this->where('category_id', $categoryId)
            ->where('bulan', $bulan)
            ->where('busa', '7600')
            ->get()
            ->getRowArray();

        if ($allData) {
            // Update data "All" jika sudah ada
            $this->update($allData['id'], ['target_amount' => $totalAmount]);
        } else {
            // Insert data "All" jika belum ada dan total amount lebih dari 0
            if ($totalAmount > 0) {
                $this->insert([
                    'busa' => '7600',
                    'bulan' => $bulan,
                    'category_id' => $categoryId,
                    'target_amount' => $totalAmount,
                ]);
            }
        }
    }
    // Tambahkan metode ini untuk menghitung total target optimasi
    public function getTotalTargetOptimasi($busa = null)
    {
        $builder = $this->db->table($this->table);
        $builder->selectSum('target_amount', 'total_target');
        
        if ($busa !== null && $busa !== '7600') {
            $builder->where('busa', $busa);
        }
        
        $result = $builder->get()->getRow();
        return $result ? $result->total_target : 0;
    }
    // Metode untuk menghitung total target optimasi per kategori
    public function getTotalPerKategori($busa)
    {
        $builder = $this->db->table($this->table)
            ->select('categories.gl_long_text, SUM(target_optimasi.target_amount) as total_target_amount')
            ->join('categories', 'target_optimasi.category_id = categories.id')
            ->where('target_optimasi.busa', $busa)
            ->groupBy('categories.gl_long_text');

        return $builder->get()->getResultArray();
    }
}
