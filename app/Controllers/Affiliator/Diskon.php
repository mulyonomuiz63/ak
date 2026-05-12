<?php

namespace App\Controllers\Affiliator;

use App\Controllers\BaseController;

class Diskon extends BaseController
{

    public $menuaktif = 'diskon';

    public function index()
    {
        $data['menuaktif'] = $this->menuaktif;
        $data['diskon'] = $this->diskon_model->getDiskon();
        return view('affiliator/diskon/index', $data);
    }


    public function datatablesource()
    {
        $RsData = $this->diskon_model->get_datatables();
        $data = array();
        if ($RsData->getNumRows() > 0) {
            foreach ($RsData->getResult() as $rowdata) {

                $row = array();
                $row[] = $rowdata->diskon;
                $row[] =
                    '<div class="dropdown custom-dropdown dropleft">
						<a class="badge btn-info" href="#" role="button" id="dropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="bi bi-three-dots"></i>
						</a>
						<div class="dropdown-menu" >
						<div class="mx-2">
						<a href="' . site_url('diskon/edit/' . encrypt($rowdata->iddiskon)) . '" class="btn btn-sm btn-warning btn-circle tooltips" data-toggle="tooltip" data-placement="left" title="Ubah data diskon"><i class="fa fa-edit"></i></a>
						<a href="' . site_url('diskon/delete/' . encrypt($rowdata->iddiskon)) . '" class="btn btn-sm btn-danger btn-circle" id="hapus"><i class="fa fa-trash tooltips" data-toggle="tooltip" data-placement="left" title="Hapus data diskon"></i></a>
						</div>
						</div>
					</div>';
                $data[] = $row;
            }
        }

        $output = array(
            "draw" => $this->request->getPost('draw'),
            "recordsTotal" => $this->diskon_model->count_all(),
            "recordsFiltered" => $this->diskon_model->count_filtered(),
            "data" => $data,
        );

        //output to json format
        return $this->response->setJSON($output);
    }

    public function tambah()
    {
        $data['iddiskon'] = "";
        $data['ltambah'] = "1";
        $data['menuaktif'] = $this->menuaktif;
        return view('affiliator/diskon/inputdata', $data);
    }


    public function edit($id)
    {
        if ($this->diskon_model->get_by_id($id)->getNumRows() < 1) {
            $pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Ilegal!</strong> Data tidak ditemukan
					    </div>
					</div>';
            $this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('diskon');
            exit();
        };

        $data['ltambah'] = "0";
        $data['iddiskon'] = $id;
        $data['menuaktif'] = $this->menuaktif;
        return view('affiliator/diskon/inputdata', $data);
    }

    public function simpan()
    {

        $iddiskon         = $this->request->getPost('iddiskon');
        $diskon         = $this->request->getPost('diskon');
        $ltambah         = $this->request->getPost('ltambah');

        if ($ltambah == '1') { // ini kondisi jika tambah data 


            $data = array(
                'diskon'     => $diskon,
            );
            $simpan = $this->diskon_model->simpan($data);
        } else { // ini kondisi jika edit data


            $data = array(
                'diskon'     => $diskon,
            );
            $simpan = $this->diskon_model->updateWhere($data, $iddiskon);
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
        return redirect()->to('diskon');
    }

    public function get_edit_data()
    {
        $iddiskon = $this->request->getPost('iddiskon');
        $RsData = $this->diskon_model->get_by_id($iddiskon)->getRow();

        $data = array(
            'iddiskon' =>  $RsData->iddiskon,
            'diskon' => $RsData->diskon,
        );

        return $this->response->setJSON($data);
    }

    public function delete($id)
    {
        $hapus = $this->diskon_model->hapus($id);
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
        return redirect()->to('diskon');
    }
}

/* End of file diskon.php */
/* Location: ./application/controllers/diskon.php */