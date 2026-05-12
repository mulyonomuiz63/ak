<?php

namespace App\Controllers\Affiliator;

use App\Controllers\BaseController;

class Marketer extends BaseController
{

	public $menuaktif = 'marketer';



	public function index()
	{
		$data['menuaktif'] = $this->menuaktif;
		$idmarketer = $this->session->get('idmarketer');
		$data['marketer'] = $this->marketer_model->getMarketer($idmarketer);
var_dump($data['marketer']);
		// $agent =  $this->request->getUserAgent();
		// if ($agent->getMobile('iphone')) {
		// 	$data['share'] = 'whatsapp:';
		// } elseif ($agent->getMobile()) {
		// 	$data['share'] = 'whatsapp:';
		// } else {
		// 	$data['share'] =  'https://api.whatsapp.com';
		// }

		// return view('affiliator/marketer/index', $data);
	}

	public function simpan()
	{

		$idmarketer 		= $this->request->getPost('idmarketer');
		$nama				= $this->request->getPost('nama');
		$nik				= $this->request->getPost('nik');
		$agama				= $this->request->getPost('agama');
		$password			= $this->request->getPost();
		$jk					= $this->request->getPost('jk');
		$nohp				= $this->request->getPost('nohp');
		$tpt_lahir			= $this->request->getPost('tpt_lahir');
		$tgl_lahir			= $this->request->getPost();
		$norek				= $this->request->getPost('norek');
		$bank				= $this->request->getPost('bank');
		$kode_referal		= $this->request->getPost('kode_referal') == null ? mt_rand(100000, 999999) : $this->request->getPost('kode_referal');

		if ($password['password'] == '') {
			$data = array(
				'nama' 			=> $nama,
				'norek' 		=> $norek,
				'bank' 			=> $bank,
				'nohp' 			=> $nohp,
				'nik' 			=> $nik,
				'jk' 			=> $jk,
				'agama' 	 	=> $agama,
				'tpt_lahir' 	=> $tpt_lahir,
				'tgl_lahir' 	=> date('Y-m-d', strtotime($tgl_lahir['tgl_lahir'])),
				'kode_referal' 	=> $kode_referal,
				'status_kode_referal' 	=> 1,
			);
		} else {
			$data = array(
				'nama' 			=> $nama,
				'norek' 		=> $norek,
				'bank' 			=> $bank,
				'nohp' 			=> $nohp,
				'nik' 			=> $nik,
				'jk' 			=> $jk,
				'agama' 	 	=> $agama,
				'password' 	 	=> md5($password['password']),
				'tpt_lahir' 	=> $tpt_lahir,
				'tgl_lahir' 	=> date('Y-m-d', strtotime($tgl_lahir['tgl_lahir'])),
				'kode_referal' 	=> $kode_referal,
				'status_kode_referal' 	=> 1,
			);
		}
		$simpan = $this->marketer_model->updateWhere($data, $idmarketer);

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
			                <strong>Gagal,</strong> Data gagal di simpan <br>
					    </div>
					</div>';
		}

		$this->session->setFlashdata('pesan', $pesan);
		return redirect()->to('Affiliator/Marketer');
	}

	function cekNohp()
	{

		$nohp = $this->request->getPost('nohp');


		$hasil_nohp = $this->login_model_affiliator->cekNohp($nohp);

		if (!$this->validate([
			'nohp' => [
				'rules' => 'required|max_length[30]|regex_match[/\A[0-9]+\z/i]',
				'errors' => [
					'required' => '{field} Harus diisi',
					'regex_match' => 'No Hp tidak boleh mengandung karakter unik',
				]
			],

		])) {
			echo "3";
		} elseif (count($hasil_nohp) != 0) {
			echo "1";
		} else {
			echo "2";
		}
	}

	function cekNik()
	{

		$nik = $this->request->getPost('nik');


		$hasil_nik = $this->login_model_affiliator->cekNik($nik);

		if (!$this->validate([
			'nik' => [
				'rules' => 'required|max_length[30]|regex_match[/\A[0-9]+\z/i]',
				'errors' => [
					'required' => '{field} Harus diisi',
					'regex_match' => 'No Hp tidak boleh mengandung karakter unik',
				]
			],

		])) {
			echo "3";
		} elseif (count($hasil_nik) != 0) {
			echo "1";
		} else {
			echo "2";
		}
	}
}

/* End of file marketer.php */
/* Location: ./application/controllers/marketer.php */