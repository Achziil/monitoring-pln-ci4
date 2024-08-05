<?php

namespace App\Models;

use CodeIgniter\Model;
use Ngekoding\CodeIgniter4DataTables\DataTables;

class SumberDataModel extends Model
{
    protected $table = 'sumber_data';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'document_no',
        'doc_date',
        'posting_date',
        'category_id',
        'busa',
        'order',
        'wbs_element',
        'type',
        'amount_doc_curr',
        'amount_local_curr',
        'offst_acct',
        'text',
        'cost_ctr',
        'user_name',
        'cocd',
        'reason_for_trip',
        'document_header_text',
        'vendor',
        'account',
        'clrng_doc',
        'assignment'
    ];

    public function getDatatables()
    {
        // Inisialisasi query builder untuk tabel sumber_data dan join dengan tabel categories
        $builder = $this->db->table($this->table)
            ->select('sumber_data.id as sumber_data_id, sumber_data.*, categories.gl_long_text')
            ->join('categories', 'sumber_data.category_id = categories.id');

        // Filter data berdasarkan input pencarian dari datatable
        if (isset($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
            $builder->groupStart();
            $builder->like('document_no', $search);
            $builder->orLike('doc_date', $search);
            $builder->orLike('posting_date', $search);
            $builder->orLike('type', $search);
            $builder->orLike('amount_local_curr', $search);
            $builder->orLike('categories.gl_long_text', $search);
            $builder->orLike('busa', $search);
            $builder->groupEnd();
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

    public function countFiltered()
    {
        // Inisialisasi query builder untuk menghitung total data yang difilter
        $builder = $this->db->table($this->table)
            ->select('sumber_data.id')
            ->join('categories', 'sumber_data.category_id = categories.id');

        // Filter data berdasarkan input pencarian dari datatable
        if (isset($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
            $builder->groupStart();
            $builder->like('document_no', $search);
            $builder->orLike('doc_date', $search);
            $builder->orLike('posting_date', $search);
            $builder->orLike('type', $search);
            $builder->orLike('amount_local_curr', $search);
            $builder->orLike('categories.gl_long_text', $search);
            $builder->orLike('busa', $search);
            $builder->groupEnd();
        }

        // Hitung total data yang difilter
        $query = $builder->countAllResults();
        return $query;
    }

    public function countAll()
    {
        // Inisialisasi query builder untuk menghitung total seluruh data
        $builder = $this->db->table($this->table);
        $query = $builder->selectCount($this->primaryKey, 'total')->get();
        return $query->getRow()->total;
    }

    // Mapping indeks kolom dari datatable ke nama kolom di database
    private function fieldMapping($orderIndex)
    {
        $fields = [
            1 => 'document_no',
            2 => 'doc_date',
            3 => 'posting_date',
            4 => 'categories.gl_long_text',
            5 => 'busa',
            6 => 'type',
            7 => 'amount_local_curr',
        ];
        
        return $fields[$orderIndex] ?? 'document_no';
    }
}
