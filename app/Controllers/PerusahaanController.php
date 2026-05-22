<?php

namespace App\Controllers;

class PerusahaanController extends BaseController
{

	public $menuaktif = 'Perusahaan';

	public function __construct()
	{
		$idpengguna = session()->get('idpengguna');
		if (empty($idpengguna)) {
			$pesan = '<div class="alert alert-danger">Silahkan anda masuk</div>';
			session()->setFlashdata('pesan', $pesan);
			throw new \CodeIgniter\Router\Exceptions\RedirectException('login');
		}
	}

	public function index()
	{
		$data['menuaktif'] = $this->menuaktif;
		return view('perusahaan/index', $data);
	}

	public function tambah()
	{
		$data['menuaktif'] = $this->menuaktif;
		return view('perusahaan/inputdata', $data);
	}

	public function edit($idperusahaan)
	{
		// $idperusahaan = $this->encrypt->decrypt($idperusahaan);

		if ($this->perusahaan_model->get_by_id($idperusahaan)->getNumRows() < 1) {
			$pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Ilegal!</strong> Data tidak ditemukan 
					    </div>
					</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('perusahaan');
			exit();
		};

		$data['idperusahaan'] = $idperusahaan;
		$data['menuaktif'] = $this->menuaktif;
		return view('perusahaan/edit', $data);
	}

	public function datatablesource()
	{
		$RsData = $this->perusahaan_model->get_datatables();
		$no = $this->request->getPost('start');
		$data = array();

		if ($RsData->getNumRows() > 0) {
			foreach ($RsData->getResult() as $rowdata) {
			    if(session()->get('idpengguna') == '8888888888'):
			        $delete = '<a class="dropdown-item" href="' . site_url('Perusahaan/delete/' . encrypt($rowdata->idperusahaan)) . '"  class="ml-2"  id="hapus">Delete</a>';
			    else:
			        $delete = '';
			    endif;
				$no++;
				$row = array();
				// $row[] = $no;
				$row[] = '<input type="checkbox" class="check-item" name="idperusahaan[]" value="' . encrypt($rowdata->idperusahaan) . '">';
				//$row[] = $rowdata->idperusahaan;
				$row[] = $rowdata->namaperusahaan;
				$row[] = date('d-m-Y', strtotime($rowdata->tglmulaiusaha));
				$row[] = date('d-m-Y', strtotime($rowdata->tglregistrasi)) == '01-01-1970'? '-': date('d-m-Y', strtotime($rowdata->tglregistrasi));
				$row[] = !empty($rowdata->tglberakhir)? date('d-m-Y', strtotime($rowdata->tglberakhir)):'-';
				if ($rowdata->statusaktif == '1') {
					$row[] = '<span class="badge badge-success">' . $rowdata->statusaktif2 . '</span>';
				} else {
					$row[] = '<span class="badge badge-danger">' . $rowdata->statusaktif2 . '</span>';
				}

				$row[] =
					'<div class="dropdown custom-dropdown dropleft">
						<a class="badge btn-info" href="#" role="button" id="dropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="bi bi-three-dots"></i>
						</a>
						<div class="dropdown-menu" >
							<a class="dropdown-item" href="' . site_url('perusahaan/edit/' . encrypt($rowdata->idperusahaan)) . '"  class="ml-2">Edit</a>
							'.$delete.'
						</div>
					</div>';
				$data[] = $row;
			}
		}

		$output = array(
			"draw" => $this->request->getPost('draw'),
			"recordsTotal" => $this->perusahaan_model->count_all(),
			"recordsFiltered" => $this->perusahaan_model->count_filtered(),
			"data" => $data,
		);

		//output to json format
		return $this->response->setJSON($output);
	}

	public function delete($id)
	{
		// $id = $this->encrypt->decrypt($id);

		if ($this->perusahaan_model->get_by_id($id)->getNumRows() < 1) {
			$pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Ilegal!</strong> Data tidak ditemukan 
					    </div>
					</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('perusahaan');
			exit();
		};

		$foto = $this->perusahaan_model->get_by_id($id)->getRow()->foto;
		$hapus = $this->perusahaan_model->hapus($id);
		if ($hapus) {
			if ($foto != '' && $foto != null) {
				unlink('./uploads/pengguna/' . $foto);
			}
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
			                <strong>Gagal.</strong> Perusahaan tidak dapat dihapus<br>
					    </div>
					</div>';
		}

		$this->session->setFlashdata('pesan', $pesan);
		return redirect()->to('perusahaan');
	}

	public function deleteAll()
	{
		$dataPerusahaan = $this->request->getPost('idperusahaan');
		$dataBerhasil = 0;
		$dataGagal = 0;
		foreach ($dataPerusahaan as $id) {
			$foto = $this->perusahaan_model->get_by_id($id)->getRow()->foto;
			$hapus = $this->perusahaan_model->hapus($id);
			if ($hapus) {
				if ($foto != '' && $foto != null) {
					unlink('./uploads/pengguna/' . $foto);
				}

				$dataBerhasil +=  1;
			} else {
				$dataGagal += 1;
			}
		}
		$pesan = '<div>
						<div class="alert alert-success alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Berhasil.</strong> Data telah berhasil dihapus ' . $dataBerhasil . ' dan data gagal dihapus ' . $dataGagal . '
					    </div>
					</div>';
		$this->session->setFlashdata('pesan', $pesan);
		return redirect()->to('perusahaan');
	}

	public function store()
	{

		$idperusahaan 		= $this->request->getPost('idperusahaan');
		$namaperusahaan		= $this->request->getPost('namaperusahaan');
		$alamat				= $this->request->getPost('alamat');
		$notelp				= $this->request->getPost('notelp');
		$tglmulaiusaha		= date('Y-m-d', strtotime($this->request->getPost('tglmulaiusaha')));
		$tglberakhir        = date('Y-m-d', strtotime($this->request->getPost('tglberakhir')));
		$email				= $this->request->getPost('email');
		$username			= $this->request->getPost('username');
		$password			= $this->request->getPost('password');
		$statusaktif			= $this->request->getPost('statusaktif');
		$tglupdate			= date('Y-m-d H:i:s');
		$pengguna			= $this->request->getPost('pengguna');
		$email_pengguna		= $this->request->getPost('email_pengguna');

		if (empty($idperusahaan)) { // ini kondisi jika tambah data 
			$tglinsert = date('Y-m-d');
			$idperusahaan 			= $this->db->query('SELECT create_idperusahaan("' . $tglinsert . '") AS idperusahaan')->getRow()->idperusahaan;

			if (empty($idperusahaan)) {
				return redirect()->to('perusahaan');
				exit();
			}

			$data = array(
				'idperusahaan' 		=> $idperusahaan,
				'namaperusahaan' 	=> $namaperusahaan,
				'alamat' 			=> $alamat,
				'notelp' 			=> $notelp,
				'tglmulaiusaha' 	=> $tglmulaiusaha,
				'tglberakhir'       => $tglberakhir,
				'email' 	 		=> $email,
				'username' 	 		=> $username,
				'password' 	 		=> md5($password),
				'statusaktif' 	 	=> $statusaktif,
				'tglregistrasi' 	 => $tglupdate,
				'tglupdate' 	 	=> $tglupdate,
				'statusverif' 	 	=> '1'
			);

			$idpengguna 			= $this->db->query('SELECT create_idpengguna("' . $tglinsert . '") AS idpengguna')->getRow()->idpengguna;

			$dataPengguna = array(
				'idpengguna' 	 	=> $idpengguna,
				'idperusahaan' 	 	=> $idperusahaan,
				'email' 	 		=> $email,
				'username' 	 		=> $username,
				'password' 	 		=> md5($password),
				'level' 	 		=> '2',
				'namapengguna' 	 		=> $namaperusahaan,
			);


			$simpan = $this->perusahaan_model->simpan($data, $dataPengguna);
		} else { // ini kondisi jika edit data

			$data = array(
				'idperusahaan' 		=> $idperusahaan,
				'namaperusahaan' 	=> $namaperusahaan,
				'alamat' 			=> $alamat,
				'notelp' 			=> $notelp,
				'email' 	 		=> $email,
				'tglmulaiusaha' 	=> $tglmulaiusaha,
				//'statusaktif' 	 	=> $statusaktif,
				'tglupdate' 	 	=> $tglupdate,
				'pengguna' 	 		=> $pengguna,
				'email_pengguna' 	=> $email_pengguna
			);

			if ($statusaktif != '') {
				$data['statusaktif'] = $statusaktif;
				$data['tglberakhir'] = $tglberakhir;
			}

			$simpan = $this->perusahaan_model->updateWhere($data, $idperusahaan);
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
		return redirect()->to('perusahaan');
	}

	public function getEdit()
	{
		$idperusahaan = $this->request->getPost('idperusahaan');
		$RsData = $this->perusahaan_model->get_by_id($idperusahaan)->getRow();

		$data = array(
			'idperusahaan' =>  $RsData->idperusahaan,
			'namaperusahaan' =>  $RsData->namaperusahaan,
			'alamat' =>  $RsData->alamat,
			'notelp' =>  $RsData->notelp,
			'email' =>  $RsData->email,
			'tglmulaiusaha' =>  date('d-m-Y', strtotime($RsData->tglmulaiusaha)),
			'tglberakhir' => date('d-m-Y', strtotime($RsData->tglberakhir)),
			'statusaktif' =>  $RsData->statusaktif,
			'pic' =>  $RsData->pic,
			'pic_ttd' =>  $RsData->pic_ttd,
			'username' =>  $RsData->username,
			'pengguna' =>  $RsData->pengguna,
			'email_pengguna' =>  $RsData->email_pengguna,
		);
		echo (json_encode($data));
	}

	public function upload_foto($file, $nama, $new_name)
	{

		if (!empty($file[$nama]['name'])) {
			$config['upload_path']          = './uploads/pengguna/';
			$config['allowed_types']        = 'gif|jpg|png|jpeg';
			$config['file_name'] 			= $new_name;
			$config['remove_space']         = TRUE;
			$config['max_size']            	= '2000KB';

			$this->load->library('upload', $config);

			if ($this->upload->do_upload($nama)) {
				$foto = $this->upload->data('file_name');
				$size = $this->upload->data('file_size');
				$ext  = $this->upload->data('file_ext');

				$this->resize_foto($this->upload->data());
			} else {
				$foto = "";
			}
		} else {
			$foto = "";
		}
		return $foto;
	}

	public function update_upload_foto($file, $nama, $file_lama, $new_name)
	{
		if (!empty($file[$nama]['name'])) {


			if ($file_lama != '' && $file_lama != null) {
				//hapus file lama
				unlink('./uploads/pengguna/' . $file_lama);
			}

			$config['upload_path']          = './uploads/pengguna/';
			$config['allowed_types']        = 'gif|jpg|png|jpeg';
			$config['file_name'] 			= $new_name;
			$config['remove_space']         = TRUE;
			$config['max_size']            = '2000KB';


			$this->load->library('upload', $config);
			if ($this->upload->do_upload($nama)) {
				$foto = $this->upload->data('file_name');
				$size = $this->upload->data('file_size');
				$ext  = $this->upload->data('file_ext'); //extension .pdf

				$this->resize_foto($this->upload->data());
			} else {
				$foto = $file_lama;
			}
		} else {
			$foto = $file_lama;
		}

		return $foto;
	}

	function resize_foto($data)
	{

		$config['quality'] = '80';
		$config['width'] = 128;
		$config['height'] = 128;
		$config['new_image'] = './uploads/pengguna/' . $data['file_name'];
		$image = \Config\Services::image();
		// $image->withFile();
		$image->clear();
		$image->initialize($config);
		$image->resize();
	}


	public function autocomplate()
	{
		$cari = $this->request->getPost('term');
		$query = "SELECT * FROM v_perusahaan WHERE 
    		( idperusahaan like '%" . $cari . "%' or namaperusahaan like '%" . $cari . "%' ) order by namaperusahaan asc limit 10";
		$res = $this->db->query($query);
		$result = array();
		foreach ($res->getResult() as $row) {
			array_push($result, array(
				'idperusahaan' => $row->idperusahaan,
				'namaperusahaan' => $row->namaperusahaan,
				'tglmulaiusaha' => $row->tglmulaiusaha,
			));
		}
		echo json_encode($result);
	}
}

/* End of file Perusahaan.php */
/* Location: ./application/controllers/Perusahaan.php */