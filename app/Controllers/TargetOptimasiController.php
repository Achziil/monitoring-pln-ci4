<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\TargetOptimasiModel;
use App\Models\CategoryModel;

class TargetOptimasiController extends Controller
{
    protected $targetOptimasiModel;
    protected $categoryModel;
    protected $userModel;

    public function __construct()
    {
        $this->targetOptimasiModel = new TargetOptimasiModel();
        $this->categoryModel = new CategoryModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $session = session();
        $userBusa = $session->get('busa');
        $level = $session->get('level');

        $busaWithNickname = $this->userModel
            ->select('busa, nickname')
            ->distinct()
            ->get()
            ->getResultArray();

        if ($level === 'pelaksana') {
            $busaWithNickname = array_filter($busaWithNickname, function ($item) use ($userBusa) {
                return $item['busa'] === $userBusa;
            });
        }

        $data = [
            'page_title' => view(
                'layouts/partials/page_title',
                [
                    'title' => 'Target Optimasi', 'pagetitle' => 'RKAP'
                ],
            ),
            'categories' => $this->categoryModel->findAll(),
            'busaOptions' => $busaWithNickname,
            'listTahun' => $this->targetOptimasiModel->getListOfYear(),
            'userLevel' => $level,
        ];

        echo view('targetoptimasi/index', $data);
    }

    private function formatBulan($date)
    {
        $bulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $tanggal = strtotime($date);
        $namaBulan = $bulan[date('n', $tanggal) - 1];
        $tahun = date('Y', $tanggal);

        return $namaBulan . ' ' . $tahun;
    }

    public function ajaxList()
    {
        $session = session();
        $busa = $session->get('busa');
        $level = $session->get('level');

        // Jika level pengguna adalah admin atau wilayah, busa dapat disesuaikan dengan filter
        if ($level === 'admin' || $level === 'wilayah') {
            $busa = $this->request->getGet('busaFilter') ?? '7600';
        }

        // Mengambil data target optimasi berdasarkan busa
        $list = $this->targetOptimasiModel->getDatatables($busa);
        $data = [];
        $no = $_POST['start'];

        // Formatter untuk format mata uang
        $formatter = new \NumberFormatter('id_ID', \NumberFormatter::CURRENCY);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);

        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '<span data-field="gl_long_text" data-id="' . $item->id . '">' . $item->gl_long_text . '</span>';
            $row[] = '<span data-field="busa" data-id="' . $item->id . '">' . $item->busa . '</span>';
            $row[] = '<span data-field="bulan" data-id="' . $item->id . '">' . $this->formatBulan($item->bulan) . '</span>';
            $row[] = '<span data-field="target_amount" data-id="' . $item->id . '">' . $formatter->formatCurrency($item->target_amount, 'IDR') . '</span>';

            // Jika level pengguna adalah pelaksana, tidak menampilkan tombol aksi
            if ($level === 'pelaksana') {
                $row[] = '-';
                // Jika busa adalah 'All' dan level pengguna adalah admin atau wilayah, hanya menampilkan tombol Delete
            } elseif ($item->busa === '7600' && ($level === 'admin' || $level === 'wilayah')) {
                $row[] = '<button class="btn btn-danger btn-sm delete-btn" data-id="' . $item->id . '">Delete</button>';
            } else {
                // Menampilkan tombol Edit dan Delete untuk level selain pelaksana
                $row[] = '
                <button class="btn btn-success btn-sm edit-btn" data-id="' . $item->id . '">Edit</button>
                <button class="btn btn-danger btn-sm delete-btn" data-id="' . $item->id . '">Delete</button>
            ';
            }

            $data[] = $row;
        }

        // Menyiapkan output untuk datatable
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->targetOptimasiModel->countAll($busa),
            "recordsFiltered" => $this->targetOptimasiModel->countFiltered($busa),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function save()
    {
        // Validasi input
        $validation = $this->validate([
            'busa' => 'required',
            'bulan' => 'required',
            'category_id' => 'required',
            'target_amount' => 'required|numeric',
        ]);

        // Jika validasi gagal
        if (!$validation) {
            return $this->response->setJSON(['success' => false, 'message' => 'Harap isi semua kolom.']);
        }

        // Menyimpan data jika permintaan adalah AJAX
        if ($this->request->isAJAX()) {
            $data = [
                'busa' => $this->request->getPost('busa'),
                'bulan' => $this->request->getPost('bulan') . '-01',
                'category_id' => $this->request->getPost('category_id'),
                'target_amount' => $this->request->getPost('target_amount'),
            ];

            // Menyimpan data ke database
            if (!$this->targetOptimasiModel->saveTargetOptimasi($data)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to create target optimasi']);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Target optimasi created successfully']);
        }

        return $this->response->setStatusCode(403, 'No direct script access allowed');
    }

    public function edit($id)
    {
        // Mengambil data target optimasi berdasarkan ID
        $targetOptimasi = $this->targetOptimasiModel->find($id);

        // Tidak dapat mengedit data dengan busa 'All'
        if ($targetOptimasi['busa'] === '7600') {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak bisa mengedit untuk data dengan busa "7600"']);
        }

        return $this->response->setJSON(['success' => true, 'data' => $targetOptimasi]);
    }

    public function update($id)
    {
        // Validasi input
        $validation = $this->validate([
            'busa' => 'required',
            'bulan' => 'required',
            'category_id' => 'required',
            'target_amount' => 'required|numeric',
        ]);

        // Jika validasi gagal
        if (!$validation) {
            return $this->response->setJSON(['success' => false, 'message' => 'Harap isi semua kolom.']);
        }

        // Mengambil data target optimasi berdasarkan ID
        $targetOptimasi = $this->targetOptimasiModel->find($id);

        // Tidak dapat mengedit data dengan busa 'All'
        if ($targetOptimasi['busa'] === '7600') {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot update data with busa "7600"']);
        }

        // Mengupdate data jika permintaan adalah AJAX
        if ($this->request->isAJAX()) {
            $data = [
                'busa' => $this->request->getPost('busa'),
                'bulan' => $this->request->getPost('bulan') . '-01',
                'category_id' => $this->request->getPost('category_id'),
                'target_amount' => $this->request->getPost('target_amount'),
            ];

            // Jika tidak ada data maka update namun tidak menambahkan data
            if (!$this->targetOptimasiModel->updateTargetOptimasi($id, $data)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to update target optimasi']);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Target optimasi updated successfully']);
        }

        return $this->response->setStatusCode(403, 'No direct script access allowed');
    }

    public function delete($id)
    {
        // Menghapus data target optimasi berdasarkan ID
        if (!$this->targetOptimasiModel->deleteTargetOptimasi($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete target optimasi']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Target optimasi deleted successfully']);
    }

    public function saveMultiMonthTargetOptimasi()
    {
        $busa = $this->request->getPost('busa');
        $category_id = $this->request->getPost('category_id');
        $bulan = $this->request->getPost('bulan');
        $target_amount = $this->request->getPost('target_amount');

        for ($i = 0; $i < count($bulan); $i++) {
            $data = [
                'busa' => $busa,
                'bulan' => $bulan[$i] . '-01',
                'category_id' => $category_id,
                'target_amount' => $target_amount[$i],
            ];
            // Simpan setiap bulan menggunakan model
            $this->targetOptimasiModel->saveTargetOptimasi($data);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Multiple month target optimasi created successfully']);
    }
    
    public function totalPerKategori()
    {
        $busa = $this->request->getPost('busa');
        if (!$busa) {
            $session = session();
            $busa = $session->get('busa'); // Gunakan busa dari sesi jika tidak ada filter yang dipilih
        }
        $totalPerKategori = $this->targetOptimasiModel->getTotalPerKategori($busa);
    
        return $this->response->setJSON([
            'data' => $totalPerKategori
        ]);
    }

}
