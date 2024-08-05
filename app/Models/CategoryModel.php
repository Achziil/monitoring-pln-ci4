<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['gl_account', 'gl_long_text'];

    public function getIdByGLAccount($gl_account)
    {
        return $this->select('id')
            ->where('gl_account', $gl_account)
            ->get()
            ->getRow()
            ->id ?? null;
    }
}
