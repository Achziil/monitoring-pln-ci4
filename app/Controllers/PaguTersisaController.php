<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use App\Models\PaguTersisaModel;

class PaguTersisaController extends Controller
{
    protected $paguTersisaModel;
    protected $userModel;

    public function __construct()
    {
        $this->paguTersisaModel = new PaguTersisaModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        
        $session = session();
        $busa = $session->get('busa');
        $level = $session->get('level');

        if ($level === 'admin' || $level === 'wilayah') {
            $busa = '7600';
        }

        $userModel = new UserModel();
        $busaList = $userModel->select('busa, nickname')->distinct()->findAll();

        $data = [
            'page_title' => view('layouts/partials/page_title', ['title' => 'Pagu Tersisa', 'pagetitle' => 'RKAP']),
            'busa' => $busa,
            'level' => $level,
            'busaList' => $busaList,
        ];

        return view('pagutersisa/index', $data);
    }

    public function getData()
    {
        $session = session(); // Mendapatkan sesi saat ini
        $busa = $session->get('busa'); // Mendapatkan nilai busa dari sesi
        $level = $session->get('level'); // Mendapatkan nilai level dari sesi

        if ($level === 'admin' || $level === 'wilayah') {
            // Jika level pengguna adalah admin atau wilayah, gunakan nilai busa yang dikirimkan melalui POST, atau gunakan 'All' jika tidak ada
            $busa = $_POST['busa'] ?? '7600';
        }

        // Memperbarui data pagu tersisa di model
        $this->paguTersisaModel->refreshPaguTersisaData();
        // Mengambil data pagu tersisa berdasarkan busa
        $data = $this->paguTersisaModel->getPaguTersisaData($busa);
        // Mengambil tanggal terakhir refresh data
        $lastRefreshDate = $this->paguTersisaModel->getLastRefreshDate();
        // Mengembalikan data dalam format JSON
        return $this->response->setJSON(['success' => true, 'data' => $data, 'last_refresh_date' => $lastRefreshDate]);
    }

    public function getDataWithPercentage()
    {
        $session = session(); // Mendapatkan sesi saat ini
        $busa = $session->get('busa'); // Mendapatkan nilai busa dari sesi
        $level = $session->get('level'); // Mendapatkan nilai level dari sesi

        if ($level === 'admin' || $level === 'wilayah') {
            // Jika level pengguna adalah admin atau wilayah, gunakan nilai busa yang dikirimkan melalui POST, atau gunakan 'All' jika tidak ada
            $busa = $_POST['busa'] ?? '7600';
        }

        // Mengambil data pagu tersisa dengan persentase berdasarkan busa
        $data = $this->paguTersisaModel->getPaguTersisaDataWithPercentage($busa);
        // Mengambil tanggal terakhir refresh data
        $lastRefreshDate = $this->paguTersisaModel->getLastRefreshDate();
        // Mengembalikan data dalam format JSON
        return $this->response->setJSON(['success' => true, 'data' => $data, 'last_refresh_date' => $lastRefreshDate]);
    }

    public function refresh()
{
    log_message('debug', 'Masuk ke fungsi refresh');
    try {
        $this->paguTersisaModel->refreshPaguTersisaData();
        log_message('debug', 'Data berhasil diperbarui');
        return $this->response->setJSON(['success' => true, 'message' => 'Data pagu tersisa berhasil diperbarui']);
    } catch (\Exception $e) {
        log_message('error', 'Kesalahan: ' . $e->getMessage());
        return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui data.']);
    }
}

    public function detail($category_id)
    {
        // Menyiapkan data untuk halaman detail pagu tersisa
        $data = [
            'page_title' => view('layouts/partials/page_title', ['title' => 'Detail Pagu Tersisa', 'pagetitle' => 'Detail Pagu Tersisa']),
            'category_id' => $category_id, // Menyimpan ID kategori untuk detail
            'data' => $this->paguTersisaModel->getPaguTersisaDataByBusaWithPercentage($category_id), // Mengambil data pagu tersisa berdasarkan kategori
        ];

        // Output debug untuk memeriksa data
        // log_message('debug', 'Detail Data: ' . print_r($data, true));

        // Mengembalikan tampilan detail pagu tersisa dengan data yang disiapkan
        return view('pagutersisa/detail', $data);
    }
}
