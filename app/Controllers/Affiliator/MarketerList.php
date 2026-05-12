<?php

namespace App\Controllers\Affiliator;

use App\Controllers\BaseController;

class MarketerList extends BaseController
{

    public $menuaktif = 'marketerlist';

    public function __construct()
    {
        $idpengguna = session()->get('idpengguna');
        if (empty($idpengguna)) {
            $pesan = '<div class="alert alert-danger">Silahkan anda login</div>';
            session()->setFlashdata('pesan', $pesan);
            throw new \CodeIgniter\Router\Exceptions\RedirectException('login');
        }
    }

    public function index()
    {
        $data['menuaktif'] = $this->menuaktif;
        return view('affiliator/marketer/list', $data);
    }

    public function datatablesource()
    {
        $RsData = $this->marketer_model->get_datatableslist();
        $data = array();
        if ($RsData->getNumRows() > 0) {
            foreach ($RsData->getResult() as $rowdata) {
                if ($rowdata->status == '1') {
                    $status = '<span class="badge bg-success text-white">Aktif</span>';
                } else {
                    $status = '<span class="badge bg-danger text-white">Tidak Aktif</span>';
                }
                if ($rowdata->kode_referal != null) {
                    $row = array();
                    $row[] = $rowdata->nama;
                    $row[] = $rowdata->nohp;
                    $row[] = $rowdata->kode_referal;
                    $row[] = $status;
                    $data[] = $row;
                }
            }
        }

        $output = array(
            "draw" => $this->request->getPost('draw'),
            "recordsTotal" => $this->marketer_model->count_alllist(),
            "recordsFiltered" => $this->marketer_model->count_filteredlist(),
            "data" => $data,
        );

        //output to json format
        return $this->response->setJSON($output);
    }
}
