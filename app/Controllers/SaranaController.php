<?php

namespace App\Controllers;

use App\Models\SaranaModel;

class SaranaController extends BaseController
{
    protected $saranaModel;

    public function __construct()
    {
        $this->saranaModel = new SaranaModel();
    }

    public function index()
    {
        $data = [
            'page_title' => view('layouts/partials/page_title', ['title' => 'Sarana', 'pagetitle' => 'Monitoring Administrasi']),
            'sarana' => $this->saranaModel->getSarana() // Mengambil semua data sarana
        ];
        return view('sarana/index', $data);
    }

    public function detail($slug)
    {
        $sarana = $this->saranaModel->getSarana($slug); // Mengambil data sarana berdasarkan slug
        $data = [
            'page_title' => view('layouts/partials/page_title', ['title' => $sarana['detail'] ?? 'Detail Tidak Ditemukan', 'pagetitle' => $sarana['kategori'] ?? 'Kategori Tidak Ditemukan']),
            'sarana' => $sarana
        ];
        return view('sarana/detail', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => view('layouts/partials/page_title', ['title' => 'Tambah Sarana', 'pagetitle' => 'Siadm']),
            'validation' => \Config\Services::validation()
        ];
        return view('sarana/create', $data);
    }

    public function save()
    {
        if (!$this->validate([
            'detail' => [
                'rules' => 'required|is_unique[sarana.detail]',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'is_unique' => '{field} sudah terdaftar.'
                ]
            ],
            'pemilik' => 'required',
            'status' => 'required'
        ])) {
            $validation = \Config\Services::validation();
            return redirect()->to('/sarana/create')->withInput()->with('validation', $validation);
        }

        $slug = url_title($this->request->getVar('detail'), '-', true);
        $this->saranaModel->save([
            'kategori' => $this->request->getVar('kategori'),
            'detail' => $this->request->getVar('detail'),
            'slug' => $slug,
            'pemilik' => $this->request->getVar('pemilik'),
            'status' => $this->request->getVar('status')
        ]);
        session()->setFlashdata('success', 'Data berhasil ditambah');
        return redirect()->to('/sarana');
    }

    public function delete($id)
    {
        // Pastikan request method adalah POST
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/sarana')->with('error', 'Metode penghapusan tidak valid.');
        }

        // Verifikasi CSRF token
        if (!$this->validate(['csrf_token' => 'required|hash_check'])) {
            return redirect()->to('/sarana')->with('error', 'Token CSRF tidak valid.');
        }

        // Pastikan user memiliki hak akses yang tepat (contoh)
        // if (!session()->get('is_admin')) {
        //     return redirect()->to('/sarana')->with('error', 'Anda tidak memiliki izin untuk menghapus data ini.');
        // }

        // Lakukan penghapusan data
        $this->saranaModel->delete($id);
        session()->setFlashdata('success', 'Data berhasil dihapus');
        return redirect()->to('/sarana');
    }

    public function edit($slug)
    {
        $data = [
            'page_title' => view('layouts/partials/page_title', ['title' => 'Edit Sarana', 'pagetitle' => 'Siadm']),
            'validation' => \Config\Services::validation(),
            'sarana' => $this->saranaModel->getSarana($slug)
        ];
        return view('sarana/edit', $data);
    }

    public function update($id)
    {
        // $data = [
        //     'id' => $id,
        //     'kategori' => $this->request->getVar('kategori'),
        //     'detail' => $this->request->getVar('detail'),
        //     'pemilik' => $this->request->getVar('pemilik'),
        //     'status' => $this->request->getVar('status')
        // ];

        // $slug = url_title($this->request->getVar('detail'), '-', true);
        // if (!$this->validate([
        //     'detail' => 'required',
        //     'pemilik' => 'required',
        //     'status' => 'required'
        // ])) {
        //     return redirect()->to('/sarana/edit/' . $slug)->withInput();
        // }

        // $this->saranaModel->update($id, $data);
        // session()->setFlashdata('success', 'Data berhasil diperbarui');
        // return redirect()->to('/sarana');

        // Versi 2

        $slug = url_title($this->request->getVar('detail'), '-', true);
        if (!$this->validate([
            'detail' => [
                'rules' => 'required|is_unique[sarana.detail,id,' . $id . ']',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'is_unique' => '{field} sudah terdaftar.'
                ]
            ],
            'pemilik' => 'required',
            'status' => 'required'
        ])) {
            $validation = \Config\Services::validation();
            return redirect()->to('/sarana/edit/' . $slug)->withInput()->with('validation', $validation);
        }

        $this->saranaModel->save([
            'id' => $id, // tambahkan field 'id' untuk keperluan update
            'kategori' => $this->request->getVar('kategori'),
            'detail' => $this->request->getVar('detail'),
            'slug' => $slug,
            'pemilik' => $this->request->getVar('pemilik'),
            'status' => $this->request->getVar('status')
        ]);
        session()->setFlashdata('success', 'Data berhasil ditambah');
        return redirect()->to('/sarana');
    }
}
