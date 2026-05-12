<?php

namespace App\Controllers\Affiliator;

use App\Controllers\BaseController;

class KomisiMarketer extends BaseController
{

    public $menuaktif = 'komisi-marketer';

    public function index()
    {
        $data['menuaktif'] = $this->menuaktif;
        $data['totaltd'] = $this->db->table('tarikdana')->select('sum(nominal) as total')->where('status', 'S')->get()->getRow();
        return view('affiliator/komisimarketer/index', $data);
    }


    public function datatablesource()
    {
        $RsData = $this->komisimarketer_model->get_datatables();
        $data = array();
        if ($RsData->getNumRows() > 0) {
            foreach ($RsData->getResult() as $rowdata) {
                if ($rowdata->status == 'P') {
                    $status = '<span class="badge bg-info text-white">Pengajuan dana</span>';
                    $opsi = '<button type="button" class="btn btn-sm btn-primary btn-circle tooltips mx-1" >
            <i class="fa fa-file" aria-hidden="true"></i>
          </button>';
                } elseif ($rowdata->status == 'V') {
                    $status = '<span class="badge bg-primary text-white">Verifikasi dana</span>';
                    $opsi = '<button type="button" class="btn btn-sm btn-primary btn-circle tooltips mx-1" data-toggle="modal" id="tarikdana" data-id="' . $rowdata->idtd . '" data-target="#tarikDana" data-placement="left" title="Untuk approve tarik dana">
            <i class="fa fa-upload" aria-hidden="true"></i>
          </button>';
                } elseif ($rowdata->status == 'S') {
                    $status = '<span class="badge bg-success text-white">Dana berhasil ditarik </span>';
                    $opsi = '<button type="button" class="btn btn-sm btn-success btn-circle tooltips mx-1" >
            <i class="fa fa-file" aria-hidden="true"></i>
          </button>';
                }

                $row = array();
                $row[]  = $rowdata->nama;
                $row[]  = $status;
                $row[]  = $rowdata->tgl_pengajuan == null ? '-' : $rowdata->tgl_pengajuan;
                $row[]  = $rowdata->tgl_pencairan == null ? '-' : $rowdata->tgl_pencairan;
                $row[]  = number_format($rowdata->nominal, 0, ".", ".");
                $row[]  = $opsi;
                $data[] = $row;
            }
        }

        $output = array(
            "draw" => $this->request->getPost('draw'),
            "recordsTotal" => $this->komisimarketer_model->count_all(),
            "recordsFiltered" => $this->komisimarketer_model->count_filtered(),
            "data" => $data,
        );

        //output to json format
        return $this->response->setJSON($output);
    }

    public function tambah()
    {
        $data['idlangganan'] = "";
        $data['ltambah'] = "1";
        $data['menuaktif'] = $this->menuaktif;
        $data['diskon'] = $this->komisimarketer_model->getDiskon();
        $data['komisi'] = $this->komisimarketer_model->getKomisi();
        return view('affiliator/komisimarketer/inputdata', $data);
    }


    public function edit($id)
    {
        if ($this->komisimarketer_model->get_by_id($id)->getNumRows() < 1) {
            $pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Ilegal!</strong> Data tidak ditemukan
					    </div>
					</div>';
            $this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('komisi-marketer');
            exit();
        };

        $data['ltambah'] = "0";
        $data['idlangganan'] = $id;
        $data['menuaktif'] = $this->menuaktif;
        $data['diskon'] = $this->komisimarketer_model->getDiskon();
        $data['komisi'] = $this->komisimarketer_model->getKomisi();
        return view('affiliator/komisimarketer/inputdata', $data);
    }

    public function simpan()
    {

        $idlangganan            = $this->request->getPost('idlangganan');
        $iddiskon               = $this->request->getPost('iddiskon');
        $idkomisi               = $this->request->getPost('idkomisi');
        $bulan                  = $this->request->getPost('bulan');
        $nama_langganan         = $this->request->getPost('nama_langganan');
        $nominal                = $this->request->getPost('nominal');
        $ltambah                = $this->request->getPost('ltambah');

        if ($ltambah == '1') { // ini kondisi jika tambah data 


            $data = array(
                'iddiskon'          => $iddiskon,
                'idkomisi'          => $idkomisi,
                'bulan'             => $bulan,
                'nama_langganan'    => $nama_langganan,
                'nominal'           => $nominal,
            );
            $simpan = $this->komisimarketer_model->simpan($data);
        } else { // ini kondisi jika edit data


            $data = array(
                'iddiskon'          => $iddiskon,
                'idkomisi'          => $idkomisi,
                'bulan'             => $bulan,
                'nama_langganan'    => $nama_langganan,
                'nominal'           => $nominal,
            );
            $simpan = $this->komisimarketer_model->updateWhere($data, $idlangganan);
        }

        if ($simpan) {
            $pesan = '<div>
						<div class="alert alert-success alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Berhasil.</strong> Data telah disimpan
					    </div>
					</div>';
        } else {
            $eror = $this->db->error();
            $pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Gagal!</strong> Data gagal disimpan! <br>
			                Pesan Error : ' . $eror['code'] . ' ' . $eror['message'] . '
					    </div>
					</div>';
        }

        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('komisi-marketer');
    }

    public function get_edit_data()
    {
        $idtd = $this->request->getPost('idtd');
        $RsData = $this->komisimarketer_model->get_by_id($idtd)->getRow();

        $data = array(
            'idtd'              => $RsData->idtd,
            'nama'              => $RsData->nama,
            'status'            => $RsData->status,
            'nominal'           => $RsData->nominal,
            'tgl_pengajuan'     => $RsData->tgl_pengajuan,
            'tgl_pencairan'     => $RsData->tgl_pencairan,
            'norek'             => $RsData->norek,
            'bank'              => $RsData->bank,
            'nohp'              => $RsData->nohp,

        );

        return $this->response->setJSON($data);
    }

    public function approve()
    {

        $idtd = $this->request->getPost('idtd');


        $hasil =  $this->komisimarketer_model->simpanApprove($idtd);
        if ($hasil) {
            $pesan = '<div>
        				<div class="alert alert-success alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                <strong>Berhasil.</strong> Penarikan dana.
        			    </div>
        			</div>';
        } else {
            $pesan = '<div>
        				<div class="alert alert-danger alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                <strong>Gagal!</strong> Gagal approve penarikan dana <br>
        			    </div>
        			</div>';
        }


        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('komisi-marketer');
    }
}

/* End of file diskon.php */
/* Location: ./application/controllers/diskon.php */