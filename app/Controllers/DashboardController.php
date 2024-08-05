<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use App\Models\CategoryModel;
use App\Models\RealisasiModel;
use App\Models\SumberDataModel;

class DashboardController extends Controller
{
    protected $userModel;
    protected $categoryModel;
    protected $realisasiModel;
    protected $sumberDataModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->categoryModel = new CategoryModel();
        $this->realisasiModel = new RealisasiModel();
        $this->sumberDataModel = new SumberDataModel();
    }

    public function index($role = null)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }
        // akan menyesuaikan level sesi berdasarkan peran pengguna
        $level = $role ?? $session->get('level');

        switch ($level) {
            case 'admin':
                return $this->adminDashboard();
            case 'wilayah':
                return $this->wilayahDashboard();
            case 'pelaksana':
                return $this->pelaksanaDashboard();
            default:
                return redirect()->to('/login');
        }
    }

    private function getCountData()
    {
        $userModel = new UserModel();
        $categoryModel = new CategoryModel();
        $realisasiModel = new RealisasiModel();
        $sumberDataModel = new SumberDataModel();

        $data = [
            'total_users' => $userModel->countAllResults(),
            'total_categories' => $categoryModel->countAllResults(),
            'total_data_realisasi' => $realisasiModel->countAllResults(),
            'total_sumber_data' => $sumberDataModel->countAllResults(),
        ];

        return $data;
    }

    public function getTotalYearlyRealisasi()
    {
        $formatter = new \NumberFormatter('id_ID', \NumberFormatter::CURRENCY); // Mengatur mata uang Indonesia
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0); // Mengatur tidak menampilkan angka desimal

        // Mendapatkan data tahunan dari model realisasi
        $yearlyData = $this->realisasiModel->getTopCategoryRealisasiYearly();

        // Debug log
        log_message('info', 'Yearly Data: ' . json_encode($yearlyData));

        // Menghitung total dari semua data tahunan
        $totalYearlyRealisasi = array_reduce($yearlyData, function ($carry, $item) {
            return $carry + $item['total'];
        }, 0);

        // Debug log
        log_message('info', 'Total Yearly Realisasi: ' . $totalYearlyRealisasi);
        $formatter->formatCurrency($totalYearlyRealisasi, 'IDR');

        return $formatter->formatCurrency($totalYearlyRealisasi, 'IDR');;  // Menggunakan helper formatCurrency
    }

    // role admin dashboard
    private function adminDashboard()
    {
        $data = [
            'title' => 'Dashboard Admin',
            'page_title' => view('layouts/partials/page_title', ['title' => 'Dashboard Admin', 'pagetitle' => 'Monitoring Administrasi']),
        ];

        $data = array_merge($data, $this->getCountData());
        $data['chartData'] = $this->getChartRealisasiPresentaseUnit();
        // $data['topCategoryData'] = $this->getTopCategoryRealisasi('monthly'); // top 5 categori realisasi dalam perbulan 
        $data['totalYearlyRealisasi'] = $this->getTotalYearlyRealisasi(); // Tambahkan total tahunan

        // Get total realisasi, optimasi, and sisa anggaran
        $totalData = $this->realisasiModel->getTotalRealisasiOptimasi();
        $data['total_realisasi'] = $totalData['total_realisasi'];
        $data['total_optimasi'] = $totalData['total_optimasi'];
        $data['sisa_anggaran'] = $totalData['sisa_anggaran'];
        return view('dashboard/admin', $data);
    }

    // role wilayah dashboard
    private function wilayahDashboard()
    {
        // Mendapatkan informasi sesi
        $session = session();
        $role = $session->get('level'); // Mendapatkan level pengguna dari sesi
        $busa = $role === 'pelaksana' || $role === 'wilayah' ? $session->get('busa') : null; // Jika peran pelaksana, dapatkan busa dari sesi, jika tidak, null

        $data = [
            'title' => 'Dashboard Wilayah',
            'page_title' => view('layouts/partials/page_title', ['title' => 'Dashboard Unit Wilayah', 'pagetitle' => 'Monitoring Administrasi']),
        ];

        $data = array_merge($data, $this->getCountData());
        $data['chartData'] = $this->getChartRealisasiPresentaseUnit();
        // $data['topCategoryData'] = $this->getTopCategoryRealisasi('monthly'); // top 5 categori realisasi dalam perbulan 
        $data['totalYearlyRealisasi'] = $this->getTotalYearlyRealisasi(); // Tambahkan total tahunan
        
        // Get total realisasi, optimasi, and sisa anggaran
        $totalData = $this->realisasiModel->getTotalRealisasiOptimasiByBusa($busa);
        $data['total_realisasi'] = $totalData['total_realisasi'];
        $data['total_optimasi'] = $totalData['total_optimasi'];
        $data['sisa_anggaran'] = $totalData['sisa_anggaran'];
        return view('dashboard/wilayah', $data);
    }


    // menampilkan 5 top categori realisasi dalam per periode (bulan yang di pilih)
    public function getTopCategoryRealisasi($period)
    {
        $session = session();
        $userLevel = $session->get('level');
        $userBusa = $session->get('busa');
        $selectedMonth = $period;
        $selectedYear = date('Y');
        $data = [];

        if ($userLevel === 'admin') {
            $data = $this->realisasiModel->getTopCategoryRealisasiByMonth(null, $selectedMonth, $selectedYear);
        } else if ($userLevel === 'pelaksana' || $userLevel === 'wilayah') {
            $data = $this->realisasiModel->getTopCategoryRealisasiByMonth($userBusa, $selectedMonth, $selectedYear);
        }

        return $data;
    }


    // DashboardController.php
    public function getTopCategoryRealisasiAjax()
    {
        $period = $this->request->getGet('period');
        $data = $this->getTopCategoryRealisasi($period);

        if (empty($data)) {
            return $this->response->setJSON(['message' => 'Tidak ada data untuk bulan yang dipilih.']);
        } else {
            return $this->response->setJSON(['data' => $data]);
        }
    }


    // menampilkan presentase chart realisasi per-unit
    public function getChartRealisasiPresentaseUnit()
    {
        // Mendapatkan informasi sesi
        $session = session();
        $role = $session->get('level'); // Mendapatkan level pengguna dari sesi
        $busa = $session->get('busa');  // Mendapatkan busa dari sesi

        // Mendapatkan koneksi database
        $db = \Config\Database::connect();
        $builder = $db->table('users'); // Mengambil tabel 'users'
        $builder->select('users.busa, users.nickname'); // Memilih kolom 'busa' dan 'nickname'
        $builder->distinct(); // Menghindari duplikasi data

        if ($role === 'pelaksana' || $role === 'wilayah' || $role === 'admin') {
            $builder->where('users.busa !=', '7600');
        } else {
            $builder->where('users.busa', $busa);
        }
        // Mendapatkan hasil query
        $userNicknames = $builder->get()->getResultArray();

        $labels = [];
        $data = [];

        // Loop melalui setiap pengguna yang ditemukan
        foreach ($userNicknames as $user) {
            $busa = $user['busa']; // Mengambil nilai busa
            $nickname = $user['nickname']; // Mengambil nickname
            // Mendapatkan persentase realisasi berdasarkan busa
            $realisasiPercentage = $this->realisasiModel->getRealisasiPercentageDashboard($busa);
            $totalPercentage = $realisasiPercentage['percentage']; // Mengambil total persentase
            $labels[] = 'Unit ' . $nickname; // Menambahkan nickname ke label
            $data[] = $totalPercentage; // Menambahkan persentase ke data
        }
        // Mengembalikan label dan data
        return ['labels' => $labels, 'data' => $data];
    }


    // menampilkan widget dashboard pelaksana
    private function getCountDataPelaksana()
    {
        $session = session();
        $busa = $session->get('busa');
        $userModel = new UserModel();
        $realisasiModel = new RealisasiModel();
        $sumberDataModel = new SumberDataModel();

        $userData = $userModel->where('busa', $busa)->first();
        $level = $userData['level'] ?? null;

        if ($level === 'pelaksana') {
            $data = [
                'total_data_realisasi' => $realisasiModel->where('busa', $busa)->countAllResults(),
                'total_sumber_data' => $sumberDataModel->where('busa', $busa)->countAllResults(),
            ];
        } else {
            $data = [
                'total_data_realisasi' => $realisasiModel->countAllResults(),
                'total_sumber_data' => $sumberDataModel->countAllResults(),
            ];
        }

        return $data;
    }



        // pelaksana dashboard
        private function pelaksanaDashboard()
        {
            // Menyiapkan data dasar untuk tampilan dashboard pelaksana
            $data = [
                'title' => 'Dashboard Pelaksana',
                'page_title' => view('layouts/partials/page_title', ['title' => 'Dashboard Unit Pelaksana', 'pagetitle' => 'Monitoring Administrasi']),
            ];
    
            // Menggabungkan data tambahan untuk tampilan dashboard pelaksana
            $data = array_merge($data, $this->getCountDataPelaksana());
    
            // Mendapatkan informasi sesi
            $session = session();
            $role = $session->get('level'); // Mendapatkan level pengguna dari sesi
            $busa = $role === 'pelaksana' ? $session->get('busa') : null; // Jika peran pelaksana, dapatkan busa dari sesi, jika tidak, null
    
            // Mengambil data chart realisasi presentase unit
            $data['chartData'] = $this->getChartRealisasiPresentaseUnit();
            $data['totalYearlyRealisasi'] = $this->getTotalYearlyRealisasi(); // Tambahkan total tahunan
    
            // Mengambil data total_realisasi, total_optimasi, dan sisa_anggaran
            $totalData = $this->realisasiModel->getTotalRealisasiOptimasiByBusa($busa);
            $data['total_realisasi'] = $totalData['total_realisasi'];
            $data['total_optimasi'] = $totalData['total_optimasi'];
            $data['sisa_anggaran'] = $totalData['sisa_anggaran'];
    
            // Mengembalikan tampilan dashboard pelaksana dengan data yang telah disiapkan
            return view('dashboard/pelaksana', $data);
        }
}
