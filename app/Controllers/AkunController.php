<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Cell\DataType;

class AkunController extends BaseController
{

    public $menuaktif = 'Akun';
    public function index()
    {
        cek_akun();
        $data['menuaktif'] = $this->menuaktif;
        return view('akun/index', $data);
    }

    public function tambah()
    {
        if (!(session()->get('databaseHitAkun') <= session()->get('hitAkun'))) {
            return redirect()->to('akun');
        }
        $data['keyakun'] = "";
        $data['ltambah'] = "1";
        $data['menuaktif'] = $this->menuaktif;
        return view('akun/inputdata', $data);
    }

    public function edit($keyakun)
    {
        if ($this->akun_model->get_by_id($keyakun)->getNumRows() < 1) {
            $pesan = '<div>
        				<div class="alert alert-danger alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                <strong>Ilegal!</strong> Data tidak ditemukan
        			    </div>
        			</div>';
            $this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('akun');
            exit();
        };

        $data['ltambah'] = "0";
        $data['keyakun'] = $keyakun;
        $data['menuaktif'] = $this->menuaktif;
        return view('akun/inputdata', $data);
    }

    public function datatablesource()
    {
        $RsData = $this->akun_model->get_datatables();
        $no = $this->request->getPost('start');
        $data = array();

        if ($RsData->getNumRows() > 0) {
            foreach ($RsData->getResult() as $rowdata) {
                if ($rowdata->status == '0') {
                    $statusData = '<span class="badge bg-success text-white">Aktif</span>';
                    $statustext = 'Non Aktif';
                } else {
                    $statusData = '<span class="badge bg-danger text-white">Tidak Aktif</span>';
                    $statustext = 'Aktif';
                }
                
                $statusDrop = '<a href="' . base_url('akun/status/' . encrypt($rowdata->keyakun) . '/' . $rowdata->status . '') . '" class="dropdown-item ml-2 tooltips" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Klik status untuk merubah status">' . $statustext . '</a>';
                // if (($rowdata->kdakun == 100000) || ($rowdata->kdakun == 110000) || ($rowdata->kdakun == 111000) || ($rowdata->kdakun == 112000) || ($rowdata->kdakun == 113000) || ($rowdata->kdakun == 114000) || ($rowdata->kdakun == 117000) || ($rowdata->kdakun == 120000) || ($rowdata->kdakun == 130000) || ($rowdata->kdakun == 200000) || ($rowdata->kdakun == 210000) || ($rowdata->kdakun == 220000) || ($rowdata->kdakun == 300000) || ($rowdata->kdakun == 400000) || ($rowdata->kdakun == 500000) || ($rowdata->kdakun == 600000) || ($rowdata->kdakun == 700000) || ($rowdata->kdakun == 710000) || ($rowdata->kdakun == 720000) || ($rowdata->kdakun == 730000) || ($rowdata->kdakun == 115000) || ($rowdata->kdakun == 116000) ) {
                if (($rowdata->kdakun == 100000) || ($rowdata->kdakun == 110000) || ($rowdata->kdakun == 111000) || ($rowdata->kdakun == 112000) || ($rowdata->kdakun == 113000) || ($rowdata->kdakun == 114000) || ($rowdata->kdakun == 117000) || ($rowdata->kdakun == 120000) || ($rowdata->kdakun == 130000) || ($rowdata->kdakun == 200000) || ($rowdata->kdakun == 210000) || ($rowdata->kdakun == 220000) || ($rowdata->kdakun == 300000) || ($rowdata->kdakun == 400000) || ($rowdata->kdakun == 500000) || ($rowdata->kdakun == 600000) || ($rowdata->kdakun == 700000) || ($rowdata->kdakun == 710000) || ($rowdata->kdakun == 720000) || ($rowdata->kdakun == 730000)) {
                    $delleteAll = '<i class="bi bi-lock-fill"></i>';
                    $delete = '<a class="dropdown-item ml-2 " id="hapus" href="#">Delete</a>';

                    if (($rowdata->kdakun == 730000) || ($rowdata->kdakun == 117000)) { 
                        $edit = '<a class="dropdown-item ml-2" href="' . site_url('akun/edit/' . encrypt($rowdata->keyakun)) . '" >Edit</a>';
                        $statusTogel = '';
                    } else {
                        $edit = '';
                        $statusTogel = 'disabled';
                    }
                } else {
                    $statusTogel = '';
                    $delleteAll = '<input type="checkbox" class="check-item" name="keyakun[]" value="' . encrypt($rowdata->keyakun) . '">';
                    $delete = '<a class="dropdown-item ml-2" href="' . site_url('akun/delete/' . encrypt($rowdata->keyakun)) . '"  id="hapus">Delete</a>';
                    $edit = '<a class="dropdown-item ml-2" href="' . site_url('akun/edit/' . encrypt($rowdata->keyakun)) . '" >Edit</a>';
                }
                
                
                // if($statusTogel != 'disabled'){
                    $status = '<a href="' . base_url('akun/status/' . encrypt($rowdata->keyakun) . '/' . $rowdata->status . '') . '" class="disabled tooltips" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Klik status untuk merubah status">' . $statusData . '</a>';
                // }else{
                    // $status = $statusData;
                // }
                
                
                $no++;
                $row = array();
                // $row[] = $no;
                $row[] = $delleteAll;
                $row[] = $rowdata->kdakun;
                $row[] = $rowdata->nmakun;
                $row[] = $rowdata->saldonormal2;
                $row[] = $rowdata->level;
                $row[] = $status;
                $row[] =
                    '<div class="dropdown custom-dropdown dropleft " >
						<a class="badge btn-info" href="#" role="button" id="dropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="bi bi-three-dots"></i>
						</a>
						<div class="dropdown-menu" >
							' . $edit . '
                            ' . $delete . '
                            '.$statusDrop.'
						</div>
					</div>';
                $data[] = $row;
            }
        }

        $output = array(
            "draw" => $this->request->getPost('draw'),
            "recordsTotal" => $this->akun_model->count_all(),
            "recordsFiltered" => $this->akun_model->count_filtered(),
            "data" => $data,
        );

        //output to json format
        return $this->response->setJSON($output);
    }

    public function delete($id)
    {
        // $id = $this->encrypt->decrypt($id);

        if ($this->akun_model->get_by_id($id)->getNumRows() < 1) {
            $pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Ilegal!</strong> Data tidak ditemukan! 
					    </div>
					</div>';
            $this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('akun');
            exit();
        };

        $hapus = $this->akun_model->hapus($id);
        if ($hapus) {
            $pesan = '<div>
						<div class="alert alert-success alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Berhasil.</strong> Data telah dihapus
					    </div>
					</div>';
        } else {
            $eror = $this->db->error();
            $pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Gagal.</strong> Akun sudah pernah digunakan untuk transaksi <br>
					    </div>
					</div>';
        }

        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('akun');
    }

    public function deleteAll()
    {
        $dataAkun = $this->request->getPost('keyakun');
        $dataBerhasil = 0;
        $dataGagal = 0;
        foreach ($dataAkun as $id) {
            $hapus = $this->akun_model->hapus($id);
            if ($hapus) {
                $dataBerhasil +=  1;
            } else {
                $dataGagal += 1;
            }
        }
        $pesan = '<div>
						<div class="alert alert-success alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                Data telah berhasil dihapus ' . $dataBerhasil . ' dan data gagal dihapus ' . $dataGagal . '
					    </div>
					</div>';
        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('akun');
    }

    public function store()
    {

        $keyakun         = $this->request->getPost('keyakun');
        $kdakun         = $this->request->getPost('kdakun');
        $nmakun    = $this->request->getPost('nmakun');
        $level        = $this->request->getPost('level');
        $saldonormal        = $this->request->getPost('saldonormal');
        $ltambah        = $this->request->getPost('ltambah');
        $idperusahaan = $this->session->get('idperusahaan');


        if (strlen($kdakun) == 5) {
            if ($kdakun <= 73000) {
                $nex = true;
            } else {
                $nex = false;
            }
        } else {
            if ($kdakun <= 730000) {
                $nex = true;
            } else {
                $nex = false;
            }
        }
        if ($nex) {
            if ($ltambah == '1') { // ini kondisi jika tambah data 


                $builder = $this->db->table('akun');
                $rsperusahaan = $builder->where('idperusahaan', $idperusahaan)->where('kdakun', $kdakun)->get()->getRow();
                if (empty($rsperusahaan)) {
                    $keyakun = $kdakun . $idperusahaan;
                    $data = array(
                        'keyakun'     => $keyakun,
                        'kdakun'     => $kdakun,
                        'nmakun'     => $nmakun,
                        'level'         => $level,
                        'saldonormal'         => $saldonormal,
                        'idperusahaan'         => $idperusahaan,
                    );
                    $simpan = $this->akun_model->simpan($data);
                } else {
                    $simpan = false;
                }
                // var_dump($simpan);
            } else { // ini kondisi jika edit data


                $data = array(
                    'kdakun'     => $kdakun,
                    'nmakun'     => $nmakun,
                    'level'         => $level,
                    'saldonormal'         => $saldonormal,
                );

                $simpan = $this->akun_model->updateWhere($data, $keyakun);
            }
        } else {
            $simpan = false;
        }


        if ($simpan) {
            $pesan = '<div>
        				<div class="alert alert-success alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                <strong>Berhasil.</strong> Data telah disimpan
        			    </div>
        			</div>';
        } else {
            $pesan = '<div>
        				<div class="alert alert-danger alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                <strong>Maaf.</strong> Data gagal disimpan<br>
        			    </div>
        			</div>';
        }

        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('akun');
    }

    public function status($id, $status)
    {
        if ($status  == '0') {
            $statusNew = 1;
        } else {
            $statusNew = '0';
        }

        $cek_akun = $this->akun_model->cekakun($id);
        if(empty($cek_akun)){
            $data = array(
                'status' => $statusNew,
            );
            $this->akun_model->updateWhere($data, $id);
            $pesan = '<div>
    						<div class="alert alert-success alert-dismissable">
    			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
    			                <strong>Berhasil.</strong> Status telah diubah
    					    </div>
    					</div>';
        }else{
             $pesan = '<div>
    						<div class="alert alert-danger alert-dismissable">
    			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
    			                <strong>Maaf.</strong> Akun gagal dinon-aktifkan, karena akun sudah pernah digunakan
    					    </div>
    					</div>';
        }
        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('akun');
    }


    public function getEdit()
    {
        $keyakun = $this->request->getPost('keyakun');
        $RsData = $this->akun_model->get_by_id($keyakun)->getRow();

        $data = array(
            'keyakun' =>  $RsData->keyakun,
            'kdakun' =>  $RsData->kdakun,
            'nmakun' =>  $RsData->nmakun,
            'level' =>  $RsData->level,
            'saldonormal' =>  $RsData->saldonormal,
        );
        return $this->response->setJSON($data);
    }

    public function autocomplate()
    {
        $cari = $this->request->getPost('term');
        $idperusahaan = $this->session->get('idperusahaan');
        if ($idperusahaan == "9999999999") {
            $tampil =  $this->request->getPost('idperusahaan');
        } else {
            $tampil =  $idperusahaan;
        }


        $query = "SELECT * FROM v_akun WHERE level='4' and idperusahaan = '$tampil' and status='0' and
        		( kdakun like '%" . $cari . "%' or nmakun like '%" . $cari . "%' ) order by kdakun asc limit 10";
        $res = $this->db->query($query);
        $result = array();
        foreach ($res->getResult() as $row) {
            array_push($result, array(
                'keyakun' => $row->keyakun,
                'kdakun' => $row->kdakun,
                'nmakun' => $row->nmakun,
                'level' => $row->level,
                'saldonormal' => $row->saldonormal,
                'saldonormal2' => $row->saldonormal2,
            ));
        }
        return $this->response->setJSON($result);
    }

    public function exportExcel()
    {
        $rsakun = $this->akun_model->get_all();

        $namaperusahaan = session()->get('namapengguna');
        $file_name = 'data akun ' . $namaperusahaan . '.xlsx';

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'kdakun');
        $sheet->setCellValue('B1', 'nmakun');
        $sheet->setCellValue('C1', 'level');
        $sheet->setCellValue('D1', 'saldonormal');
        $sheet->setCellValue('E1', 'idperusahaan');
        $no = 2;
        foreach ($rsakun->getResult() as $row) {
            $sheet->setCellValue("A$no", $row->kdakun);
            $sheet->setCellValue("B$no", $row->nmakun);
            $sheet->setCellValue("C$no", $row->level);
            $sheet->setCellValue("D$no", $row->saldonormal);
            $sheet->setCellValueExplicit("E$no", $row->idperusahaan, DataType::TYPE_NUMERIC);
            $no++;
        }
        $sheet->getColumnDimension("B")->setWidth(25);
        $sheet->getColumnDimension("E")->setWidth(25);


        $writer = new Xlsx($spreadsheet);

        $writer->save($file_name);

        header("Content-Type: application/vnd.ms-excel");

        header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');

        header('Expires: 0');

        header('Cache-Control: must-revalidate');

        header('Pragma: public');

        header('Content-Length:' . filesize($file_name));

        flush();

        readfile($file_name);
        
        if (file_exists($file_name)) {
            unlink($file_name);
        }

        exit;
        // return view('akun/exporttoexcel', $data);
    }

    public function storeExcel()
    {
        $file_excel = $this->request->getFile('fileexcel');
        $ext = $file_excel->getClientExtension();
        if ($ext == 'xls') {
            $render = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        } else {
            $render = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }
        $spreadsheet = $render->load($file_excel);

        // var_dump($render);
        $data = $spreadsheet->getActiveSheet()->toArray();
        $dataBerhasil = 0;
        $dataGagal = 0;
        foreach ($data as $x => $row) {
            if ($x == 0) {
                continue;
            }

            $kdakun = $row[0];
            $nmakun = $row[1];
            $level = $row[2];
            $saldonormal = $row[3];
            $idperusahaan = $row[4];

            $db = \Config\Database::connect();

            $cekperusahaan = $db->table('perusahaan')->getWhere(['idperusahaan' => $idperusahaan])->getResultArray();
            if (count($cekperusahaan) == 0) {
                $dataGagal += 1;
            } else {
                $cekakun = $db->table('akun')->getWhere(['kdakun' => $kdakun, 'idperusahaan' => $idperusahaan])->getResult();
                if (count($cekakun) == 0) {
                    $keyakun = $kdakun . $idperusahaan;
                    if (strlen($kdakun) == 5) {
                        if ($kdakun <= 73000) {
                            $nex = true;
                        } else {
                            $nex = false;
                        }
                    } else {
                        if ($kdakun <= 730000) {
                            $nex = true;
                        } else {
                            $nex = false;
                        }
                    }
                    if ($nex) {
                        $data = array(
                            'keyakun'           => $keyakun,
                            'kdakun'            => $kdakun,
                            'nmakun'            => $nmakun,
                            'level'             => $level,
                            'saldonormal'       => $saldonormal,
                            'idperusahaan'      => $idperusahaan,
                        );
                        $this->akun_model->simpan($data);
                        $dataBerhasil +=  1;
                    } else {
                        $dataGagal += 1;
                    }
                } else {
                    $dataGagal += 1;
                }
            }
        }
        $pesan = '<div>
        				<div class="alert alert-success alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                Data telah berhasil upload ' . $dataBerhasil . ' dan data gagal upload ' . $dataGagal . '
        			    </div>
        			</div>';
        $this->session->setFlashdata('pesan', $pesan);

        return redirect()->to('akun');
    }

    function export()
    {

        $file_name = 'template akun.xlsx';

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'kdakun');
        $sheet->setCellValue('B1', 'nmakun');
        $sheet->setCellValue('C1', 'level');
        $sheet->setCellValue('D1', 'saldonormal');
        $sheet->setCellValue('E1', 'idperusahaan');
        $sheet->setCellValue('A2', '12345');
        $sheet->setCellValue('B2', 'Contoh 1');
        $sheet->setCellValue('C2', '1');
        $sheet->setCellValue('D2', 'D');
        // $sheet->setCellValue('E2', '2123243242');
        $sheet->setCellValue('A3', '12346');
        $sheet->setCellValue('B3', 'Contoh 2');
        $sheet->setCellValue('C3', '1');
        $sheet->setCellValue('D3', 'K');
        // $sheet->setCellValue('E3', '2123243242');
        $sheet->setCellValueExplicit('E2', '2123243242', DataType::TYPE_NUMERIC);
        $sheet->setCellValueExplicit('E3', '2123243242', DataType::TYPE_NUMERIC);
        $sheet->getColumnDimension('E')->setWidth(25);


        $writer = new Xlsx($spreadsheet);

        $writer->save($file_name);

        header("Content-Type: application/vnd.ms-excel");

        header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');

        header('Expires: 0');

        header('Cache-Control: must-revalidate');

        header('Pragma: public');

        header('Content-Length:' . filesize($file_name));

        flush();

        readfile($file_name);

        exit;
    }


    public function updateAkun()
    {
        $idperusahaan = $this->session->get('idperusahaan');
        $this->db->transBegin();

        $query = "SELECT * FROM akun WHERE idperusahaan = '$idperusahaan'";

        $res = $this->db->query($query);

        foreach ($res->getResult() as $row) {
            // var_dump($row->kdakun . '0', $row->keyakun);
            $builder = $this->db->table('akun');
            $builder->where('keyakun', $row->keyakun);
            $data = array(
                'kdakun'     => $row->kdakun . '0',
            );
            $builder->update($data);
        }

        //untuk update status akun di tabel perusahaan
        $builder1 = $this->db->table('perusahaan');
        $builder1->where('idperusahaan', $idperusahaan);
        $data1 = array(
            'status_akun'     => 'T',
        );
        $builder1->update($data1);

        $data2 = array(
            'status_akun' => 'T',
        );

        $this->session->set($data2);

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            $simpan = false;
        } else {
            $this->db->transCommit();
            $simpan = true;
        }

        if ($simpan) {
            $pesan = '<div>
        				<div class="alert alert-success alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                <strong>Berhasil.</strong> Data telah diperbarui
        			    </div>
        			</div>';
        } else {
            $eror = $this->db->error();
            $pesan = '<div>
        				<div class="alert alert-danger alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                <strong>Maaf.</strong> Data gagal diperbarui! <br>
        	                Pesan Error : ' . $eror['code'] . ' ' . $eror['message'] . '
        			    </div>
        			</div>';
        }

        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('akun');
    }

    public function updateAkunAll()
    {
        $this->db->transBegin();

        $query = "SELECT * FROM akundefault ";

        $res = $this->db->query($query);

        foreach ($res->getResult() as $row) {
            // var_dump($row->kdakun . '0', $row->keyakun);
            $builder = $this->db->table('akundefault');
            $builder->where('kdakun', $row->kdakun);
            $data = array(
                'kdakun'     => $row->kdakun . '0',
            );
            $builder->update($data);
        }
        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            echo 'gagal';
        } else {
            $this->db->transCommit();
            echo "berhasil";
        }
    }
}

/* End of file Akun.php */
/* Location: ./application/controllers/Akun.php */