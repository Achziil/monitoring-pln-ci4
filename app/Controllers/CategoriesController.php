<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use CodeIgniter\Controller;

class CategoriesController extends Controller
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $data = [
            'page_title' => view(
                'layouts/partials/page_title',
                [
                    'title' => 'Categories',
                    'pagetitle' => 'Monitoring Anggaran Biaya Administrasi UP3'
                ]
            ),
            'categories' => $this->categoryModel->findAll(),
        ];
        return view('categories/index', $data);
    }

    public function create()
    {
        if ($this->request->isAJAX()) { // Memeriksa apakah permintaan ini adalah permintaan AJAX
            $validation = \Config\Services::validation(); // Mendapatkan layanan validasi
            $validation->setRules([
                'gl_account' => 'required', // Menentukan bahwa 'gl_account' harus diisi
                'gl_long_text' => 'required', // Menentukan bahwa 'gl_long_text' harus diisi
            ]);

            if ($validation->withRequest($this->request)->run()) { // Memvalidasi data yang dikirimkan
                $data = [
                    'gl_account' => $this->request->getPost('gl_account'), // Mengambil nilai 'gl_account' dari data POST
                    'gl_long_text' => $this->request->getPost('gl_long_text'), // Mengambil nilai 'gl_long_text' dari data POST
                ];

                if ($this->categoryModel->save($data)) { // Menyimpan data ke database
                    return $this->response->setJSON(['success' => true, 'message' => 'Category created successfully']); // Mengembalikan respon JSON sukses
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'Failed to create category']); // Mengembalikan respon JSON gagal
                }
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]); // Mengembalikan respon JSON dengan kesalahan validasi
            }
        }
    }

    public function edit($id)
    {
        if ($this->request->isAJAX()) { // Memeriksa apakah permintaan ini adalah permintaan AJAX
            $validation = \Config\Services::validation(); // Mendapatkan layanan validasi
            $validation->setRules([
                'gl_account' => 'required', // Menentukan bahwa 'gl_account' harus diisi
                'gl_long_text' => 'required', // Menentukan bahwa 'gl_long_text' harus diisi
            ]);

            if ($validation->withRequest($this->request)->run()) { // Memvalidasi data yang dikirimkan
                $data = [
                    'gl_account' => $this->request->getPost('gl_account'), // Mengambil nilai 'gl_account' dari data POST
                    'gl_long_text' => $this->request->getPost('gl_long_text'), // Mengambil nilai 'gl_long_text' dari data POST
                ];

                if ($this->categoryModel->update($id, $data)) { // Memperbarui data di database berdasarkan ID
                    return $this->response->setJSON(['success' => true, 'message' => 'Category updated successfully']); // Mengembalikan respon JSON sukses
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'Failed to update category']); // Mengembalikan respon JSON gagal
                }
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]); // Mengembalikan respon JSON dengan kesalahan validasi
            }
        }
    }

    public function delete($id)
    {
        if ($this->request->isAJAX()) { // Memeriksa apakah permintaan ini adalah permintaan AJAX
            if ($this->categoryModel->delete($id)) { // Menghapus data dari database berdasarkan ID
                return $this->response->setJSON(['success' => true, 'message' => 'Category deleted successfully']); // Mengembalikan respon JSON sukses
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete category']); // Mengembalikan respon JSON gagal
            }
        }
    }
}
