<?php

namespace App\Controllers;

class PenggunaController extends BaseController
{

	public $menuaktif = 'Pengguna';


	public function index()
	{
		cek_pengguna();
		$data['menuaktif'] = $this->menuaktif;
		return view('pengguna/index', $data);
	}

	public function tambah()
	{
		if (!(session()->get('databaseHitPengguna') <= session()->get('hitPengguna'))) {
			return redirect()->to('pengguna');
		}
		$data['idpengguna'] = "";
		$data['menuaktif'] = $this->menuaktif;
		return view('pengguna/inputdata', $data);
	}

	public function edit($idpengguna)
	{
		if ($this->pengguna_model->get_by_id($idpengguna)->getNumRows() < 1) {
			$pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Ilegal!</strong> Data tidak ditemukan 
					    </div>
					</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('pengguna');
		};

		$data['idpengguna'] = $idpengguna;
		$data['menuaktif'] = $this->menuaktif;
		return view('pengguna/inputdata', $data);
	}

	public function datatablesource()
	{
		$RsData = $this->pengguna_model->get_datatables();
		$no = $this->request->getPost('start');
		$data = array();

		if ($RsData->getNumRows() > 0) {
			foreach ($RsData->getResult() as $rowdata) {
				$no++;
				$row = array();
				// $row[] = $no;
				$row[] = '<input type="checkbox" class="check-item" name="idpengguna[]" value="' . encrypt($rowdata->idpengguna) . '">';
				$row[] = $rowdata->email != '' ? $rowdata->email : '-';
				$row[] = $rowdata->namapengguna;
				$row[] = $rowdata->username;
				$row[] = $rowdata->level2;
				$row[] =
					'<div class="dropdown custom-dropdown dropleft">
						<a class="badge btn-info" href="#" role="button" id="dropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="bi bi-three-dots"></i>
						</a>
						<div class="dropdown-menu" >
							<a class="dropdown-item" href="' . site_url('pengguna/edit/' . encrypt($rowdata->idpengguna)) . '"  class="ml-2">Edit</a>
							<a class="dropdown-item" href="' . site_url('pengguna/delete/' . encrypt($rowdata->idpengguna)) . '"  class="ml-2"  id="hapus">Delete</a>
						</div>
					</div>';
				// $row[] = '<a href="' . site_url('Pengguna/edit/' . ($rowdata->idpengguna)) . '" class="btn btn-sm btn-warning btn-circle"><i class="fa fa-edit"></i></a> | 
				// <a href="' . site_url('Pengguna/delete/' . ($rowdata->idpengguna)) . '" class="btn btn-sm btn-danger btn-circle" id="hapus"><i class="fa fa-trash"></i></a>';
				$data[] = $row;
			}
		}

		$output = array(
			"draw" => $this->request->getPost('draw'),
			"recordsTotal" => $this->pengguna_model->count_all(),
			"recordsFiltered" => $this->pengguna_model->count_filtered(),
			"data" => $data,
		);

		//output to json format
		return $this->response->setJSON($output);
	}

	public function delete($id)
	{

		if ($this->pengguna_model->get_by_id($id)->getNumRows() < 1) {
			$pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Ilegal!</strong> Data tidak ditemukan 
					    </div>
					</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('pengguna');
		};

		if ($id == $this->session->get('idpengguna')) {
			$pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Maaf.</strong> Data pengguna sedang digunakan tidak dapat dihapus 
					    </div>
					</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('pengguna');
		}


		if ($id == '8888888888') {
			$pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Maaf.</strong> Data pengguna ini tidak bisa dihapus karena id super admin 
					    </div>
					</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('pengguna');
		}


		$foto = $this->pengguna_model->get_by_id($id)->getRow()->foto;
		$hapus = $this->pengguna_model->hapus($id);
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
			                <strong>Maaf.</strong> Data gagal dihapus
					    </div>
					</div>';
		}

		$this->session->setFlashdata('pesan', $pesan);
		return redirect()->to('pengguna');
	}

	public function deleteAll()
	{
		$dataPengguna = $this->request->getPost('idpengguna');
		$dataBerhasil = 0;
		$dataGagal = 0;
		foreach ($dataPengguna as $id) {
			if ($id == $this->session->get('idpengguna')) {
				$pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Maaf.</strong> Data pengguna sedang digunakan tidak dapat dihapus 
					    </div>
					</div>';
				$this->session->setFlashdata('pesan', $pesan);
				return redirect()->to('pengguna');
			}


			if ($id == '8888888888') {
				$pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Maaf.</strong> Data pengguna ini tidak bisa dihapus karena id super admin 
					    </div>
					</div>';
				$this->session->setFlashdata('pesan', $pesan);
				return redirect()->to('pengguna');
			}


			$foto = $this->pengguna_model->get_by_id($id)->getRow()->foto;
			$hapus = $this->pengguna_model->hapus($id);
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
		return redirect()->to('pengguna');
	}

	public function store()
	{
		// 1. Inisialisasi awal untuk menghindari error "Undefined variable" 
		// jika alur tidak masuk ke dalam if (!in_array($level, [1, 9]))
		$simpan = false;

		$idpengguna         = $this->request->getPost('idpengguna');
		$namapengguna       = $this->request->getPost('namapengguna');
		$level              = $this->request->getPost('level');
		$username           = $this->request->getPost('username');
		$email              = $this->request->getPost('email');
		$password           = $this->request->getPost('password') == '' ? $this->request->getPost('password_lama') : md5($this->request->getPost('password'));
		$idperusahaan       = $this->session->get('idperusahaan');
		$file_lama          = $this->request->getPost('file_lama');
		$upload_file        = $this->request->getFile('upload_file');
		$signature          = $this->request->getPost('signature');
		$nama_file          = $file_lama;

		$query = $this->db->query(
			"
        SELECT COUNT(*) as total 
        FROM pengguna 
        WHERE (
            (username = ? AND username != '') 
            OR 
            (email = ? AND email != '')
        ) 
        AND idpengguna != ?",
			[$username, $email, $idpengguna]
		);

		$row = $query->getRow();
		// 2. Safety Check: Pastikan $row tidak null
		if ($row && $row->total > 0) {
			return redirect()->back()->with('error', 'Username atau Email sudah terdaftar!');
		}

		if ($signature != null) {
			$nama_file = $idperusahaan . date("his") . ".png";
			@file_put_contents(FCPATH . 'uploads/ttd/' . $nama_file, @file_get_contents($signature));

			if ($file_lama != '') {
				// 3. Menggunakan FCPATH agar path konsisten dan file berhasil dihapus
				if (file_exists(FCPATH . 'uploads/ttd/' . $file_lama)) {
					unlink(FCPATH . 'uploads/ttd/' . $file_lama);
				};
			};
		}

		// 4. Safety check: Pastikan variabel merupakan objek file valid dan belum dipindahkan
		if ($upload_file && $upload_file->isValid() && !$upload_file->hasMoved()) {
			$path = FCPATH . 'uploads/ttd';
			$newName = $upload_file->getRandomName();
			if ($upload_file->move($path, $newName)) {

				// resizing newName
				$this->image->withFile($path . '/' . $newName)
					->resize(
						1012,
						1012,
						true,
						'height',
						'width'
					);
				if ($file_lama != '') {
					// Menggunakan FCPATH untuk penyeragaman
					if (file_exists(FCPATH . 'uploads/ttd/' . $file_lama)) {
						unlink(FCPATH . 'uploads/ttd/' . $file_lama);
					};
				}
				$nama_file = $newName;
			}
		}


		if (empty($idpengguna)) { // ini kondisi jika tambah data 
			$tglinsert = date('Y-m-d');

			// 5. Mencegah error 'Attempt to read property on null'
			$queryInsert = $this->db->query('SELECT create_idpengguna("' . $tglinsert . '") AS idpengguna')->getRow();
			$idpengguna  = $queryInsert ? $queryInsert->idpengguna : null;

			$foto        = $this->upload_foto($_FILES, "file", $idpengguna);

			if (empty($idpengguna)) {
				return redirect()->to('pengguna');
			}

			if (!in_array($level, [1, 9])) {
				$data = array(
					'idpengguna'    => $idpengguna,
					'namapengguna'  => $namapengguna,
					'idperusahaan'  => $idperusahaan,
					'email'         => $email,
					'level'         => $level,
					'username'      => $username,
					'password'      => $password,
					'foto'          => $foto,
				);
				if ($level == 2) {
					$data['file_ttd'] = $nama_file;
				} elseif ($level == 3) {
					$data['pic_ttd']  = $nama_file;
				}else{
					$data['file_ttd'] = $nama_file;
				}
				$simpan = $this->pengguna_model->simpan($data);
			}
		} else { // ini kondisi jika edit data
			$foto = $this->update_upload_foto($_FILES, "file", $file_lama, $idpengguna);

			if (empty($idpengguna)) {
				return redirect()->to('pengguna');
			}

			if (!in_array($level, [1, 9])) {

				$datafil = array(
					'file_ttd' =>  '',
				);

				$file = $this->request->getPost('hapusfile');
				if ($file  != '') {
					$this->pengguna_model->deleteFile($datafil, $idpengguna);
					if ($file != '' && $file != null) {
						// Menggunakan FCPATH untuk penyeragaman
						if (file_exists(FCPATH . 'uploads/ttd/' . $file)) {
							unlink(FCPATH . 'uploads/ttd/' . $file);
						};
					}
				}

				$data = array(
					'namapengguna'  => $namapengguna,
					'email'         => $email,
					'level'         => $level,
					'username'      => $username,
					'password'      => $password,
				);
				if ($level == 2) {
					$data['file_ttd'] = $nama_file;
				} elseif ($level == 3) {
					$data['pic_ttd']  = $nama_file;
				}else{
					$data['file_ttd'] = $nama_file;
				}
				$simpan = $this->pengguna_model->updateWhere($data, $idpengguna);
			}
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
                        <strong>Gagal,</strong> Data gagal di simpan <br>
                    </div>
                </div>';
		}

		$this->session->setFlashdata('pesan', $pesan);
		return redirect()->to('pengguna');
	}
	function deleteFile($idpengguna, $file, $level)
	{
		if (!in_array($level, [1, 9])) {
			$data = array(
				'file_ttd' =>  '',
			);
		}

		$this->pengguna_model->deleteFile($data, $idpengguna);



		if ($file != '' && $file != null) {

			if (file_exists('./uploads/ttd/' . $file)) {

				unlink('./uploads/ttd/' . $file);
			};
		}

		return redirect()->to('Pengguna/edit/' . encrypt($idpengguna));
	}

	public function getEdit()
	{
		$idpengguna = $this->request->getPost('idpengguna');
		$RsData = $this->pengguna_model->get_by_id($idpengguna)->getRow();

		$data = array(
			'idpengguna' =>  $RsData->idpengguna,
			'namapengguna' =>  $RsData->namapengguna,
			'level' =>  $RsData->level,
			'foto' =>  $RsData->foto,
			'username' =>  $RsData->username,
			'email' =>  $RsData->email,
			'password' =>  $RsData->password,
			'file_ttd' =>  $RsData->file_ttd,
			'pic_ttd' =>  $RsData->pic_ttd,
		);
		echo (json_encode($data));
	}

	public function upload_foto($file, $nama, $new_name)
	{

		if (!empty($file[$nama]['name'])) {
			$saved_file = $this->request->getFile($nama);
			$newName = $saved_file->getRandomName();
			$upload_path = FCPATH . 'uploads/pengguna';
			$moved = $saved_file->move($upload_path, $newName);
			if ($moved) {
				$foto = $newName;
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
				if (file_exists('./uploads/pengguna/' . $file_lama)) {
					unlink('./uploads/pengguna/' . $file_lama);
				};
			}

			$saved_file = $this->request->getFile($nama);
			$newName = $saved_file->getRandomName();
			$upload_path = FCPATH . 'uploads/pengguna';
			$moved = $saved_file->move($upload_path, $newName);
			if ($moved) {
				$foto = $newName;
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
		print_r($data);
		exit;
		$filePath = '';
		$saveto = './uploads/pengguna/' . $data['file_name'];
		$image = \Config\Services::image();
		$image->withFile($filePath)->resize(128, 128, true)->save($saveto, 80);
	}
}

/* End of file Pengguna.php */
/* Location: ./application/controllers/Pengguna.php */