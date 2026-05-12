<?php

namespace App\Controllers\Affiliator;

use App\Controllers\BaseController;

class Komisi extends BaseController
{

    public $menuaktif = 'komisi';

    public function index()
    {
        $data['menuaktif'] = $this->menuaktif;
        $data['komisi'] = $this->komisi_model->getkomisi();
        return view('affiliator/komisi/index', $data);
    }


    public function datatablesource()
    {
        $RsData = $this->komisi_model->get_datatables();
        $data = array();
        if ($RsData->getNumRows() > 0) {
            foreach ($RsData->getResult() as $rowdata) {

                $row = array();
                $row[] = $rowdata->komisi;
                $row[] =
                    '<div class="dropdown custom-dropdown dropleft">
						<a class="badge btn-info" href="#" role="button" id="dropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="bi bi-three-dots"></i>
						</a>
						<div class="dropdown-menu" >
						<div class="mx-2">
						<a href="' . site_url('komisi/edit/' . encrypt($rowdata->idkomisi)) . '" class="btn btn-sm btn-warning btn-circle tooltips" data-toggle="tooltip" data-placement="left" title="Ubah data komisi"><i class="fa fa-edit"></i></a>
						<a href="' . site_url('komisi/delete/' . encrypt($rowdata->idkomisi)) . '" class="btn btn-sm btn-danger btn-circle" id="hapus"><i class="fa fa-trash tooltips" data-toggle="tooltip" data-placement="left" title="Hapus data komisi"></i></a>
						</div>
						</div>
					</div>';
                $data[] = $row;
            }
        }

        $output = array(
            "draw" => $this->request->getPost('draw'),
            "recordsTotal" => $this->komisi_model->count_all(),
            "recordsFiltered" => $this->komisi_model->count_filtered(),
            "data" => $data,
        );

        //output to json format
        return $this->response->setJSON($output);
    }

    public function tambah()
    {
        $data['idkomisi'] = "";
        $data['ltambah'] = "1";
        $data['menuaktif'] = $this->menuaktif;
        return view('affiliator/komisi/inputdata', $data);
    }


    public function edit($id)
    {
        $idkomisi = ($id);
        if ($this->komisi_model->get_by_id($idkomisi)->getNumRows() < 1) {
            $pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Ilegal!</strong> Data tidak ditemukan
					    </div>
					</div>';
            $this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('komisi');
            exit();
        };

        $data['ltambah'] = "0";
        $data['idkomisi'] =  $idkomisi;
        $data['menuaktif'] = $this->menuaktif;
        return view('affiliator/komisi/inputdata', $data);
    }

    public function simpan()
    {

        $idkomisi         = $this->request->getPost('idkomisi');
        $komisi         = $this->request->getPost('komisi');
        $ltambah         = $this->request->getPost('ltambah');

        if ($ltambah == '1') { // ini kondisi jika tambah data 


            $data = array(
                'komisi'     => $komisi,
            );
            $simpan = $this->komisi_model->simpan($data);
        } else { // ini kondisi jika edit data


            $data = array(
                'komisi'     => $komisi,
            );
            $simpan = $this->komisi_model->updateWhere($data, $idkomisi);
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
        return redirect()->to('komisi');
    }

    public function get_edit_data()
    {
        $idkomisi = $this->request->getPost('idkomisi');
        $RsData = $this->komisi_model->get_by_id($idkomisi)->getRow();

        $data = array(
            'idkomisi' =>  $RsData->idkomisi,
            'komisi' => $RsData->komisi,
        );

        return $this->response->setJSON($data);
    }

    public function delete($id)
    {
        $hapus = $this->komisi_model->hapus($id);
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
			                <strong>Maaf,</strong> Data gagal dihapus karena sudah digunakan <br>
					    </div>
					</div>';
        }

        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('komisi');
    }
}

/* End of file komisi.php */
/* Location: ./application/controllers/komisi.php */