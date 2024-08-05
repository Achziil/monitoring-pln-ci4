<?php

namespace App\Controllers;

use App\Models\MonitoringModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class MonitoringController extends Controller
{
    protected $monitoringModel;
    protected $userModel;

    public function __construct()
    {
        $this->monitoringModel = new MonitoringModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $session = session();
        $level = $session->get('level');
        $busa = '7600';

        if ($level !== 'admin' && $level !== 'wilayah') {
            $busa = $session->get('busa');
        }

        $userModel = new UserModel();
        $busaList = $userModel->select('busa, nickname')->distinct()->findAll();

        $data = [
            'page_title' => view(
                'layouts/partials/page_title',
                [
                    'title' => 'Monitoring Optimasi',
                    'pagetitle' => 'RKAP'
                ]
            ),
            'busa' => $busa,
            'level' => $level,
            'busaList' => $busaList,
        ];

        return view('monitoring/index', $data);
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

        // Mengambil data monitoring berdasarkan busa
        $data = $this->monitoringModel->getMonitoringData($busa);
        // Mengambil tanggal terakhir refresh data
        $lastRefreshDate = $this->monitoringModel->getLastRefreshDate();

        // Mengembalikan data dalam format JSON
        return $this->response->setJSON(['success' => true, 'data' => $data, 'last_refresh_date' => $lastRefreshDate]);
    }

    public function refresh()
    {
        // Memperbarui data monitoring di model
        $this->monitoringModel->refreshMonitoringData();
        // Mengembalikan pesan sukses dalam format JSON
        return $this->response->setJSON(['success' => true, 'message' => 'Data monitoring optimasi berhasil diperbarui']);
    }

    public function detail($category_id)
    {
        // Menyiapkan data untuk halaman detail monitoring optimasi
        $data = [
            'page_title' => view('layouts/partials/page_title', ['title' => 'Detail Monitoring Optimasi', 'pagetitle' => 'Monitoring Optimasi']),
            'category_id' => $category_id, // Menyimpan ID kategori untuk detail
            'data' => $this->monitoringModel->getMonitoringDataByBusa($category_id), // Mengambil data monitoring berdasarkan kategori
        ];
        // Mengembalikan tampilan detail monitoring dengan data yang disiapkan
        return view('monitoring/detail', $data);
    }
}
