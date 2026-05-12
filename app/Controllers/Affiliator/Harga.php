<?php

namespace App\Controllers\Affiliator;

use App\Controllers\BaseController;

class Harga extends BaseController
{

    public $menuaktif = 'langganan';

    public function index()
    {
        $data['menuaktif'] = $this->menuaktif;
        return view('affiliator/harga/index', $data);
    }


    public function datatablesource()
    {
        $RsData = $this->harga_model->get_datatables();
        $data = array();
        if ($RsData->getNumRows() > 0) {
            foreach ($RsData->getResult() as $rowdata) {

                $row = array();
                $row[] = $rowdata->diskon;
                $row[] = $rowdata->komisi;
                // $row[] = $rowdata->bulan . ' Bulan';
                $row[] = $rowdata->nama_langganan;
                $row[] = 'Rp. ' . number_format($rowdata->nominal, 0, ".", ".");
                $row[] =
                    '<div class="dropdown custom-dropdown dropleft">
						<a class="badge btn-info" href="#" role="button" id="dropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="bi bi-three-dots"></i>
						</a>
						<div class="dropdown-menu" >
						<div class="mx-2">
						<a href="' . site_url('harga/edit/' . encrypt($rowdata->idlangganan)) . '" class="btn btn-sm btn-warning btn-circle tooltips" data-toggle="tooltip" data-placement="left" title="Ubah data Harga"><i class="fa fa-edit"></i></a>
						<a href="' . site_url('harga/delete/' . encrypt($rowdata->idlangganan)) . '" class="btn btn-sm btn-danger btn-circle" id="hapus"><i class="fa fa-trash tooltips" data-toggle="tooltip" data-placement="left" title="Hapus data Harga"></i></a>
						</div>
						</div>
					</div>';
                $data[] = $row;
            }
        }

        $output = array(
            "draw" => $this->request->getPost('draw'),
            "recordsTotal" => $this->harga_model->count_all(),
            "recordsFiltered" => $this->harga_model->count_filtered(),
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
        $data['diskon'] = $this->harga_model->getDiskon();
        $data['komisi'] = $this->harga_model->getKomisi();
        return view('affiliator/harga/inputdata', $data);
    }


    public function edit($id)
    {
        if ($this->harga_model->get_by_id($id)->getNumRows() < 1) {
            $pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Ilegal!</strong> Data tidak ditemukan
					    </div>
					</div>';
            $this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('harga');
            exit();
        };

        $data['ltambah'] = "0";
        $data['idlangganan'] = $id;
        $data['menuaktif'] = $this->menuaktif;
        $data['diskon'] = $this->harga_model->getDiskon();
        $data['komisi'] = $this->harga_model->getKomisi();
        return view('affiliator/harga/inputdata', $data);
    }

    public function simpan()
    {

        $idlangganan            = $this->request->getPost('idlangganan');
        $iddiskon               = $this->request->getPost('iddiskon');
        $idkomisi               = $this->request->getPost('idkomisi');
        // $bulan                  = $this->request->getPost('bulan');
        $nama_langganan         = $this->request->getPost('nama_langganan');
        $nominal                = $this->request->getPost('nominal');
        $ltambah                = $this->request->getPost('ltambah');

        if ($ltambah == '1') { // ini kondisi jika tambah data 


            $data = array(
                'iddiskon'          => $iddiskon,
                'idkomisi'          => $idkomisi,
                'bulan'             => '0',
                'nama_langganan'    => $nama_langganan,
                'nominal'           => $nominal,
            );
            $simpan = $this->harga_model->simpan($data);
        } else { // ini kondisi jika edit data


            $data = array(
                'iddiskon'          => $iddiskon,
                'idkomisi'          => $idkomisi,
                // 'bulan'             => $bulan,
                'nama_langganan'    => $nama_langganan,
                'nominal'           => $nominal,
            );
            $simpan = $this->harga_model->updateWhere($data, $idlangganan);
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
        return redirect()->to('harga');
    }

    public function get_edit_data()
    {
        $idlangganan = $this->request->getPost('idlangganan');
        $RsData = $this->harga_model->get_by_id($idlangganan)->getRow();

        $data = array(
            'idlangganan'       =>  $RsData->idlangganan,
            'iddiskon'          => $RsData->iddiskon,
            'idkomisi'          => $RsData->idkomisi,
            'bulan'             => $RsData->bulan,
            'nama_langganan'    => $RsData->nama_langganan,
            'nominal'           => $RsData->nominal,

        );

        return $this->response->setJSON($data);
    }

    public function delete($id)
    {
        $hapus = $this->harga_model->hapus($id);
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
        return redirect()->to('harga');
    }
}
