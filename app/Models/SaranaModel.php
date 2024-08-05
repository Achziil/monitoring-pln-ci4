<?php

namespace App\Models;

use CodeIgniter\Model;

class SaranaModel extends Model
{
    protected $table = 'sarana'; // Ganti dengan nama tabel yang sesuai
    protected $primaryKey = 'id'; // Ganti dengan primary key tabel, jika berbeda
    protected $useTimestamps = true; // Ubah menjadi true jika tabel memiliki kolom created_at dan updated_at
    protected $allowedFields = ['kategori', 'detail', 'slug', 'pemilik', 'status']; // Isi dengan nama kolom yang sesuai

    public function getSarana($slug = false)
    {
        if ($slug == false) {
            return $this->findAll();
        }

        return $this->where(['slug' => $slug])->first();
    }
}
?>
