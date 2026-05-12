<?php

namespace App\Controllers\Affiliator;

use App\Controllers\BaseController;

class Saldo extends BaseController
{

	public $menuaktif = 'marketer';

	public function __construct()
	{
		$idmarketer = session()->get('idmarketer');
		if (empty($idmarketer)) {
			$pesan = '<div class="alert alert-danger">Silahkan anda login</div>';
			session()->setFlashdata('pesan', $pesan);
			throw new \CodeIgniter\Router\Exceptions\RedirectException('Affiliator/Login');
		}
	}

	public function index()
	{
		$data['menuaktif'] = $this->menuaktif;
		$data['marketer'] = $this->marketer_model->getMarketer();
		$data['tarikDana'] = $this->marketer_model->getTarikDana();
		return view('affiliator/saldo/index', $data);
	}

	public function tarikdana($encryptkey, $saldo)
	{
		$db = \Config\Database::connect();
		$marketer = $db->table('marketer')->getWhere(['encryptkey' => $encryptkey, 'saldo' => $saldo])->getResultArray();
		$cekpengajuan = $db->table('tarikdana')->getWhere(['idmarketer' => session()->get('idmarketer'), 'status' => 'V'])->getResultArray();
		if (count($cekpengajuan) == 0) {
			if (count($marketer) == 0) {
				$pesan = '<div>
						<div class="alert alert-success alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Gagal.</strong> Pengajuan tarik dana gagal, coba lagi.
					    </div>
					</div>';
			} else {
				$data = array(
					'idmarketer'     	=> session()->get('idmarketer'),
					'status'     		=> 'V',
					'tgl_pengajuan'     => date("Y-m-d H:i:s"),
					'tgl_pencairan'     => null,
					'nominal'           => $saldo,
				);


				$this->marketer_model->insertTarikdana($data);
				$pesan = '<div>
						<div class="alert alert-success alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Berhasil.</strong> Pengajuan tarik dana berhasil diajukan
					    </div>
					</div>';
			}
		} else {
			$pesan = '<div>
						<div class="alert alert-success alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Gagal.</strong> Tidak bisa mengajuka tarik dana karna ada data yang sedang dalam verifikasi, coba lagi.
					    </div>
					</div>';
		}

		$this->session->setFlashdata('pesan', $pesan);
		return redirect()->to('Affiliator/Saldo');
	}
	public function datatablesource()
	{
		$RsData = $this->marketer_model->get_datatables();
		$data = array();
		if ($RsData->getNumRows() > 0) {
			foreach ($RsData->getResult() as $rowdata) {
				if ($rowdata->status == 'P') {
					$statusData = '<span class="badge bg-danger text-white">Pengajuan</span>';
				} elseif ($rowdata->status == 'V') {
					$statusData = '<span class="badge bg-info text-white">Menunggu Verifikasi</span>';
				} else {
					$statusData = '<span class="badge bg-success text-white">Dana berhasil ditarik</span>';
				}
				$row = array();
				$row[] = $rowdata->nama;
				$row[] = $statusData;
				$row[] = $rowdata->tgl_pengajuan == null ? '' : date('d-m-Y', strtotime($rowdata->tgl_pengajuan));
				$row[] = $rowdata->tgl_pencairan == null ? '' : date('d-m-Y', strtotime($rowdata->tgl_pencairan));
				$row[] = 'Rp. ' . number_format($rowdata->nominal, 0, ".", ".");
				$data[] = $row;
			}
		}

		$output = array(
			"draw" => $this->request->getPost('draw'),
			"recordsTotal" => $this->marketer_model->count_all(),
			"recordsFiltered" => $this->marketer_model->count_filtered(),
			"data" => $data,
		);

		//output to json format
		return $this->response->setJSON($output);
	}
}

/* End of file marketer.php */
/* Location: ./application/controllers/marketer.php */