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
        $builder = $this->db->table($this->table)
            ->select('target_optimasi.*, categories.gl_long_text')
            ->join('categories', 'target_optimasi.category_id = categories.id');

        if ($busa !== '7600' && $busa !== null) {
            $builder->where('target_optimasi.busa', $busa);
        }

        if (isset($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
            $builder->groupStart();
            $builder->like('categories.gl_long_text', $search);
            $builder->orLike('target_optimasi.busa', $search);
            $builder->orLike('target_optimasi.bulan', $search);
            $builder->orLike('target_optimasi.target_amount', $search);
            $builder->groupEnd();
        }

        if (isset($_POST['busa']) && $_POST['busa'] != '7600') {
            $builder->where('target_optimasi.busa', $_POST['busa']);
        }

        if (isset($_POST['order'])) {
            $order = $_POST['order'][0]['column'];
            $dir = $_POST['order'][0]['dir'];
            $builder->orderBy($this->fieldMapping($order), $dir);
        }

        $builder->limit($_POST['length'], $_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    public function countFiltered($busa = null)
    {
        $builder = $this->db->table($this->table)
            ->select('target_optimasi.id')
            ->join('categories', 'target_optimasi.category_id = categories.id');

        if ($busa !== '7600' && $busa !== null) {
            $builder->where('target_optimasi.busa', $busa);
        }

        if (isset($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
            $builder->groupStart();
            $builder->like('categories.gl_long_text', $search);
            $builder->orLike('target_optimasi.busa', $search);
            $builder->orLike('target_optimasi.bulan', $search);
            $builder->orLike('target_optimasi.target_amount', $search);
            $builder->groupEnd();
        }

        if (isset($_POST['busa']) && $_POST['busa'] != '7600') {
            $builder->where('target_optimasi.busa', $_POST['busa']);
        }

        $query = $builder->countAllResults();
        return $query;
    }

    public function countAll($busa = null)
    {
        $builder = $this->db->table($this->table);

        if ($busa !== '7600' && $busa !== null) {
            $builder->where('busa', $busa);
        }
        $query = $builder->selectCount($this->primaryKey, 'total')->get();
        return $query->getRow()->total;
    }

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

        $db->transComplete();

        return $db->transStatus();
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
}
