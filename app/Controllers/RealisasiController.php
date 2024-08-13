<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\RealisasiModel;

class RealisasiController extends Controller
{
    protected $realisasiModel;
    protected $userModel;

    public function __construct()
    {
        $this->realisasiModel = new RealisasiModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {

        $busaWithNickname = $this->userModel
            ->select('busa, nickname')
            ->distinct()
            ->get()
            ->getResultArray();
            
            $session = session();
            $userBusa = $session->get('busa'); // Ambil nilai busa dari sesi pengguna    
        // array_unshift($busaWithNickname, ['busa' => '7600', 'nickname' => 'Data Papua dan Papua Barat']);
        $data = [

            'page_title' => view(
                'layouts/partials/page_title',
                [
                    'title' => 'Realisasi', 'pagetitle' => 'RKAP'
                ],
            ),

            'pagetitle' => 'Monitoring Administrasi',
            'busaOptions' => $busaWithNickname,
            'userBusa' => $userBusa, // Tambahkan nilai busa ke data yang dikirim ke view
            'listTahun' => $this->realisasiModel->getListOfYear(),
        ];

        echo view('realisasi/index', $data);
    }

    // Fungsi untuk memformat tanggal ke dalam format "Bulan Tahun"
    private function formatBulan($date)
    {
        // Daftar nama bulan dalam bahasa Indonesia
        $bulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $tanggal = strtotime($date); // Mengubah tanggal menjadi timestamp
        $namaBulan = $bulan[date('n', $tanggal) - 1]; // Mendapatkan nama bulan
        $tahun = date('Y', $tanggal); // Mendapatkan tahun

        return $namaBulan . ' ' . $tahun; // Mengembalikan format "Bulan Tahun"
    }

    // Fungsi untuk mendapatkan daftar data dengan format JSON untuk AJAX
    public function ajaxList()
    {
        $session = session();
        $busa = $this->request->getPost('busaFilter') ?? $session->get('busa');
        $level = $session->get('level');

        if ($level === 'admin' || $level === 'wilayah') {
            $busa = $this->request->getPost('busaFilter') ?? '7600';

            // Jika tidak ada filter busa yang dipilih, set busa menjadi null untuk menampilkan seluruh data
            if ($busa === 'all') {
                $busa = null;
            }
        }

        $list = $this->realisasiModel->getDatatables($busa);
        $data = [];
        $no = $_POST['start'];

        $formatter = new \NumberFormatter('id_ID', \NumberFormatter::CURRENCY);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);

        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '<span data-field="gl_long_text" data-id="' . $item->id . '">' . $item->gl_long_text . '</span>';
            $row[] = '<span data-field="busa" data-id="' . $item->id . '">' . $item->busa . '</span>';
            $row[] = '<span data-field="bulan" data-id="' . $item->id . '">' . $this->formatBulan($item->bulan) . '</span>';
            $row[] = '<span data-field="amount_local_curr" data-id="' . $item->id . '">' . $formatter->formatCurrency($item->amount_local_curr, 'IDR') . '</span>';
            $row[] = '<button class="btn btn-info btn-sm detail-btn" data-busa="' . $item->busa . '" data-bulan="' . $item->bulan .  '" data-category-id="' . $item->category_id . '">Detail</button>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->realisasiModel->countAll($busa),
            "recordsFiltered" => $this->realisasiModel->countFiltered($busa),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function getDetailDataByMonth($busa, $bulan, $categoryId)
    {
        $data = [
            'page_title' => view(
                'layouts/partials/page_title',
                [
                    'title' => 'Detail Realisasi', 'pagetitle' => 'Realisasi'
                ],
            ),
            'pagetitle' => 'Monitoring Administrasi',
            'busa' => $busa,
            'bulan' => $bulan,
            'categoryId' => $categoryId,
        ];
        return view('realisasi/detail', $data);
    }

    public function ajaxDetailList($busa, $bulan, $categoryId)
    {
        $list = $this->realisasiModel->getDetailDatatables($busa, $bulan, $categoryId);
        $data = [];
        $no = $_POST['start'];

        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = $item->posting_date;
            $row[] = $item->gl_long_text;
            $row[] = $item->busa;
            $row[] = number_format($item->amount_local_curr, 0, ',', '.');
            $row[] = $item->text;
            $row[] = !empty($item->reason_for_trip) ? $item->reason_for_trip : $item->document_header_text;
            $row[] = $item->account;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->realisasiModel->countAllDetail($busa, $bulan, $categoryId),
            "recordsFiltered" => $this->realisasiModel->countFilteredDetail($busa, $bulan, $categoryId),
            "data" => $data,
        );

        echo json_encode($output);
    }


    // Fungsi untuk menghapus semua data
    public function deleteAll()
    {
        $db = db_connect(); // Menghubungkan ke database
        $db->transStart(); // Memulai transaksi
        $db->table('realisasi')->truncate(); // Mengosongkan tabel realisasi
        $db->transComplete(); // Menyelesaikan transaksi

        if ($db->transStatus() === FALSE) {
            throw new \Exception('Gagal untuk menghapus seluruh data.'); // Mengembalikan error jika transaksi gagal
        }
        return true; // Mengembalikan true jika berhasil
    }



    // REALISASI PRESENTASE

    public function indexPresentase($busa = '7600', $selectedMonth = null)
    {
        $userModel = new UserModel();
        $busaListWithNickname = $userModel->select('busa, nickname')->distinct()->findAll();

        $session = session();
        $userBusa = $session->get('busa');
        $level = $session->get('level');

        if ($level === 'pelaksana') {
            $busa = $userBusa;
        }

        $months = $this->realisasiModel->getAvailableMonths($busa);

        $monthsByYear = [];

        foreach ($months as $month) {
            $year = substr($month['bulan'], 0, 4);
            $monthsByYear[$year][] = $month['bulan'];
        }

        if ($selectedMonth === null) {
            $selectedMonth = isset($months[0]['bulan']) ? $months[0]['bulan'] : date('Y-m') . '-01';
        }

        $realisasiData = $this->realisasiModel->getRealisasiPercentage($busa, $selectedMonth);
        $trendData = $this->calculateTrend($realisasiData);

        $data = [
            'page_title' => view(
                'layouts/partials/page_title',
                [
                    'title' => 'Realisasi Presentase', 'pagetitle' => 'RKAP'
                ],
            ),
            'pagetitle' => 'Monitoring Presentase',
            'busa' => $busa,
            'busaList' => $busaListWithNickname,
            'months' => $months,
            'selectedMonth' => $selectedMonth,
            'realisasi_data' => $trendData,
            'monthsByYear' => $monthsByYear,
        ];

        echo view('realisasi/presentase', $data);
    }
    public function calculateTrend($data)
    {
        $trendData = [];
        $totalIndex = count($data) - 1;

        foreach ($data as $index => $row) {
            $trend = null;
            if (is_numeric($row['percentage_prev']) && is_numeric($row['percentage'])) {
                $trend = $row['percentage'] - $row['percentage_prev'];
            }
            $trendData[] = array_merge($row, ['trend' => $trend]);
        }

        // Menghitung total trend untuk baris total
        if ($totalIndex >= 0 && isset($trendData[$totalIndex])) {
            $totalPercentagePrev = rtrim($trendData[$totalIndex]['percentage_prev'], '%');
            $totalPercentage = rtrim($trendData[$totalIndex]['percentage'], '%');
            if (is_numeric($totalPercentagePrev) && is_numeric($totalPercentage)) {
                $trendData[$totalIndex]['trend'] = $totalPercentage - $totalPercentagePrev;
            }
        }

        return $trendData;
    }

    public function reloadData()
    {
        $session = session();
        $busa = $session->get('busa');
        $level = $session->get('level');

        if ($level === 'admin' || $level === 'wilayah') {
            $busa = '7600';
        }

        $list = $this->realisasiModel->getDatatables($busa);
        $data = [];
        $no = 0;

        $formatter = new \NumberFormatter('id_ID', \NumberFormatter::CURRENCY);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);

        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '<span data-field="gl_long_text" data-id="' . $item->id . '">' . $item->gl_long_text . '</span>';
            $row[] = '<span data-field="busa" data-id="' . $item->id . '">' . $item->busa . '</span>';
            $row[] = '<span data-field="bulan" data-id="' . $item->id . '">' . $this->formatBulan($item->bulan) . '</span>';
            $row[] = '<span data-field="amount_local_curr" data-id="' . $item->id . '">' . $formatter->formatCurrency($item->amount_local_curr, 'IDR') . '</span>';

            if ($level === 'admin' || $level === 'wilayah') {
                $row[] = '
                    <!-- <button class="btn btn-success btn-sm edit-btn" data-id="' . $item->id . '">Edit</button>  -->
                    <!-- <button class="btn btn-danger btn-sm delete-btn" data-id="' . $item->id . '">Delete</button> -->';
            } else {
                $row[] = '-';
            }
            $data[] = $row;
        }

        $output = array(
            "data" => $data,
        );

        return $this->response->setJSON($output);
    }

    public function totalPerKategori()
    {
        $busa = $this->request->getPost('busaFilter');
        if (!$busa) {
            $session = session();
            $busa = $session->get('busa'); // Gunakan busa dari sesi jika tidak ada filter yang dipilih
        }
        $data = $this->realisasiModel->getTotalPerKategori($busa);

        return $this->response->setJSON(['data' => $data]);
    }
}
