<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\SumberDataModel;
use App\Models\RealisasiModel;
use App\Models\PaguTersisaModel;
use App\Models\MonitoringModel;
use App\Models\CategoryModel;

class SumberDataController extends Controller

{
    protected $sumberDataModel;
    protected $categoryModel;
    protected $realisasiModel;
    protected $monitoringModel;
    protected $pagutersisaModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->sumberDataModel = new SumberDataModel();
        $this->categoryModel = new CategoryModel();
        $this->realisasiModel = new RealisasiModel();
        $this->monitoringModel = new MonitoringModel();
        $this->pagutersisaModel = new PaguTersisaModel();
    }

    public function index()
    {
        $data = [
            'page_title' => view(
                'layouts/partials/page_title',
                [
                    'title' => 'Sumber Data', 'pagetitle' => 'RKAP'
                ],
            ),

        ];

        echo view('sumberdata/index', $data);
    }

    public function ajaxList()
    {
        $model = new SumberDataModel();
        $list = $model->getDatatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '<span  data-field="document_no" data-id="' . $item->id . '">' . $item->document_no . '</span>';
            $row[] = '<span  data-field="doc_date" data-id="' . $item->id . '">' . date('d-m-Y', strtotime($item->doc_date)) . '</span>';
            $row[] = '<span  data-field="posting_date" data-id="' . $item->id . '">' . date('d-m-Y', strtotime($item->posting_date)) . '</span>';
            $row[] = '<span data-field="gl_long_text" data-id="' . $item->id . '">' . $item->gl_long_text . '</span>';
            $row[] = '<span  data-field="busa" data-id="' . $item->id . '">' . $item->busa . '</span>';
            $row[] = '<span  data-field="type" data-id="' . $item->id . '">' . $item->type . '</span>';
            $row[] = '<span  data-field="amount_local_curr" data-id="' . $item->id . '">' . number_format($item->amount_local_curr) . '</span>';
            $row[] = '
               
            <button class="btn btn-primary btn-sm save-btn" data-id="' . $item->id . '" style="display: none;">Save</button>

                <button class="btn btn-danger btn-sm delete-btn" data-id="' . $item->id . '">Delete</button>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $model->countAll(),
            "recordsFiltered" => $model->countFiltered(),
            "data" => $data,
        );
        echo json_encode($output);
    }


    public function delete($id)
    {
        $this->sumberDataModel->delete($id);
        return $this->response->setJSON(['success' => true]);
    }


    public function deleteAll()
    {
        $db = db_connect();
        $db->transStart();
        // $db->query('SET FOREIGN_KEY_CHECKS = 0;');
        $db->table('sumber_data')->truncate();
        // $db->query('SET FOREIGN_KEY_CHECKS = 1;');
        $db->table('realisasi')->truncate(); // Mengosongkan tabel realisasi
        $db->transComplete();

        $this->monitoringModel->refreshMonitoringData();
        $this->pagutersisaModel->refreshPaguTersisaData();

        if ($db->transStatus() === FALSE) {
            throw new \Exception('Gagal untuk menghapus seluruh data.');
        }
        return true;
    }

    public function upload()
    {
        set_time_limit(600);
        $file = $this->request->getFile('excel_file');

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads', $newName);

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            $reader->setReadEmptyCells(false);

            $spreadsheet = $reader->load(WRITEPATH . 'uploads/' . $newName);
            $sheet = $spreadsheet->getActiveSheet();

            $highestRow = $sheet->getHighestRow();

            $realisasiData = [];
            $batchInsertData = [];

            try {
                for ($row = 2; $row <= $highestRow; $row++) {
                    // Ambil nilai dari sel, dan cek apakah sel tersebut tidak kosong
                    $docDateValue = $sheet->getCell('A' . $row)->getValue();
                    $postingDateValue = $sheet->getCell('B' . $row)->getValue();

                    $docDate = $docDateValue ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($docDateValue)->format('Y-m-d') : null;
                    $postingDate = $postingDateValue ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($postingDateValue)->format('Y-m-d') : null;

                    $gl_account = $sheet->getCell('L' . $row)->getValue();
                    $gl_long_text = $sheet->getCell('C' . $row)->getValue();

                    // Cek apakah kategori sudah ada dalam database
                    $category_id = $this->categoryModel->getIdByGLAccount($gl_account);

                    if (!$category_id) {
                        // Jika kategori belum ada, lakukan insert ke tabel categories
                        $categoryData = [
                            'gl_account' => $gl_account,
                            'gl_long_text' => $gl_long_text,
                        ];
                        $category_id = $this->categoryModel->insert($categoryData);
                    }

                    $data = [
                        'document_no' => $sheet->getCell('F' . $row)->getValue(),
                        'doc_date' => $docDate,
                        'posting_date' => $postingDate,
                        'category_id' => $category_id,
                        'busa' => $sheet->getCell('G' . $row)->getValue(),
                        'order' => $sheet->getCell('H' . $row)->getValue(),
                        'wbs_element' => $sheet->getCell('I' . $row)->getValue(),
                        'type' => $sheet->getCell('K' . $row)->getValue(),
                        'amount_doc_curr' => $sheet->getCell('M' . $row)->getValue(),
                        'amount_local_curr' => $sheet->getCell('N' . $row)->getValue(),
                        'offst_acct' => $sheet->getCell('O' . $row)->getValue(),
                        'text' => $sheet->getCell('P' . $row)->getValue(),
                        'cost_ctr' => $sheet->getCell('Q' . $row)->getValue(),
                        'user_name' => $sheet->getCell('R' . $row)->getValue(),
                        'cocd' => $sheet->getCell('S' . $row)->getValue(),
                        'reason_for_trip' => $sheet->getCell('T' . $row)->getValue(),
                        'document_header_text' => $sheet->getCell('U' . $row)->getValue(),
                        'vendor' => $sheet->getCell('V' . $row)->getValue(),
                        'account' => $sheet->getCell('W' . $row)->getValue(),
                        'clrng_doc' => $sheet->getCell('D' . $row)->getValue(),
                        'assignment' => $sheet->getCell('E' . $row)->getValue()
                    ];

                    if ($docDate && $postingDate) {
                        $batchInsertData[] = $data;

                        // Simpan data ke array realisasi
                        $bulan = date('Y-m-01', strtotime($data['posting_date']));
                        $busa = $data['busa'];
                        $category_id = $data['category_id'];
                        $amount_local_curr = $data['amount_local_curr'];

                        if (!isset($realisasiData[$bulan][$busa][$category_id])) {
                            $realisasiData[$bulan][$busa][$category_id] = 0;
                        }
                        $realisasiData[$bulan][$busa][$category_id] += $amount_local_curr;
                    }
                }

                // Batch insert data sumberData
                if (!empty($batchInsertData)) {
                    $this->sumberDataModel->insertBatch($batchInsertData);
                }

                // Simpan data realisasi ke database
                $realisasiBatchData = [];
                foreach ($realisasiData as $bulan => $busaData) {
                    foreach ($busaData as $busa => $categoryData) {
                        foreach ($categoryData as $category_id => $amount_local_curr) {
                            $realisasiItem = [
                                'busa' => $busa,
                                'bulan' => $bulan,
                                'category_id' => $category_id,
                                'amount_local_curr' => $amount_local_curr,
                            ];
                            $realisasiBatchData[] = $realisasiItem;
                        }
                    }
                }

                if (!empty($realisasiBatchData)) {
                    $this->realisasiModel->insertBatch($realisasiBatchData);
                }

                // Tambahkan akumulasi data perbulan berdasarkan kategori
                $realisasiBatchDataAll = [];
                foreach ($realisasiData as $bulan => $busaData) {
                    $categoryTotals = [];
                    foreach ($busaData as $busa => $categoryData) {
                        foreach ($categoryData as $category_id => $amount_local_curr) {
                            if (!isset($categoryTotals[$category_id])) {
                                $categoryTotals[$category_id] = 0;
                            }
                            $categoryTotals[$category_id] += $amount_local_curr;
                        }
                    }

                    // Simpan akumulasi data perbulan dengan busa 'All'
                    foreach ($categoryTotals as $category_id => $totalAmount) {
                        $realisasiItemAll = [
                            'busa' => '7600',
                            'bulan' => $bulan,
                            'category_id' => $category_id,
                            'amount_local_curr' => $totalAmount,
                        ];
                        $realisasiBatchDataAll[] = $realisasiItemAll;
                    }
                }

                if (!empty($realisasiBatchDataAll)) {
                    $this->realisasiModel->insertBatch($realisasiBatchDataAll, batchSize: 1000);
                }

                $this->monitoringModel->refreshMonitoringData();
                $this->pagutersisaModel->refreshPaguTersisaData();

                return $this->response->setJSON(['status' => 'success', 'message' => 'Data successfully imported']);
            } catch (\Exception $e) {
                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
            }
        } else {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Failed to upload the Excel file.']);
        }
    }
}

