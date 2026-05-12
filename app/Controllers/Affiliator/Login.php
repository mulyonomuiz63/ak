<?php

namespace App\Controllers\Affiliator;

use App\Controllers\BaseController;

class Login extends BaseController
{
	public function keluar()
	{
		$this->session->destroy();
		return redirect()->to('Affiliator/Login');
	}

	public function index()
	{
		$username = session()->get('user');
		if (isset($username)) {
			return redirect()->to('Affiliator/Dashboard');
		} else {
			return view('affiliator/login/login');
		}
	}

	public function cek_login()
	{
		$string = trim($this->request->getPost('username'));
		$password = trim($this->request->getPost('password'));
		$result = "$string";
		$username = preg_replace("/[^a-zA-Z0-9_.@]/", "", $result);
		$row =  preg_match('/\A[a-z0-9__.@]+\z/i', $string);
		if ($row != '0') {
			if (empty($username) and empty($password)) {
				$pesan = '<div class="alert alert-danger">username atau password anda salah </div>';
				$this->session->setFlashdata('pesan', $pesan);
				return redirect()->to('Affiliator/Login');
			} else {
				$kirim = $this->login_model_affiliator->cek_login($username, md5($password));
				if ($kirim->getNumRows() > 0) {


					$result = $kirim->getRow();
					if ($result->status == 0) {
						$pesan = '<div class="alert alert-danger">Akun anda dinonaktifkan</div>';
						$this->session->setFlashdata('pesan', $pesan);
						return redirect()->to('Affiliator/Login');
					} elseif ($result->statusverif == 0) {
						$pesan = '<div class="alert alert-danger">Akun belum ter-verifikasi </div>';
						$this->session->setFlashdata('pesan', $pesan);
						return redirect()->to('Affiliator/Login');
					} else {
						$data = array(
							'idmarketer' => $result->idmarketer,
							'user' => $result->username,
							'nama' => $result->nama,
							'namapengguna' => $result->nama,
							'kode_referal' => $result->kode_referal,
						);

						$this->session->set($data);
						return redirect()->to('Affiliator/Dashboard');
					}
				} else {
					$pesan = '<div class="alert alert-danger">Username atau password anda salah </div>';
					$this->session->setFlashdata('pesan', $pesan);
					return redirect()->to('Affiliator/Login');
				}
			}
		} else {
			$pesan = '<div class="alert alert-danger">Username atau password anda salah </div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('Affiliator/Login');
		}
	}


	public function registrasi()
	{
		return view('affiliator/login/registrasi');
	}


	public function simpanregistrasi()
	{
		$email			= $this->request->getPost('email');
		$string   		= $this->request->getPost('username');
		$password 		= md5($this->request->getPost('password'));
		$password2 		= md5($this->request->getPost('password2'));
		$encrypted_id 	= md5(trim($email));


		$result = "$string";
		$username = preg_replace("/[^a-zA-Z0-9_.@]/", "", $result);

		if ($password != $password2) {
			$pesan = '<div class="alert alert-danger">Ulangi Password tidak sama</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('affiliator/Login/registrasi');
			exit();
		}



		$cekid = $this->login_model_affiliator->cek_email($email);
		if ($cekid->getNumRows() > 0) {
			if ($cekid->getRow()->statusverif == 1) {
				$pesan = '<div class="alert alert-danger">Email sudah terdaftar</div>';
				$this->session->setFlashdata('pesan', $pesan);
				return redirect()->to('affiliator/Login/registrasi');
				exit();
			} else {
				$pesan = '<div class="alert alert-danger">Email sudah terdaftar & belum diaktivasi</div>';
				$this->session->setFlashdata('pesan', $pesan);
				return redirect()->to('affiliator/Login/registrasi');
				exit();
			}
		} else {

			//kirim aktivasi
			$from_email = 'no-replay@akuntanmu.com';
			$from_nama = 'AKUNTANMU';
			$passwordemail = 'Akuntanmu123@#';

			$lkirimemail = $this->login_model_affiliator->kirimemail($from_email, $from_nama, $passwordemail, $email, $username, $encrypted_id);

			if ($lkirimemail) {

				$data = array(
					'email' => $email,
					'username' => $username,
					'password' => $password,
					'status' => '1',
					'encryptkey' => $encrypted_id,
					'statusverif' => '0',
					'kode_referal' => null,
					'status_kode_referal' => '0',
				);

				$simpan = $this->login_model_affiliator->simpanregistrasi($data);
				if ($simpan) {
					$pesan = '<div class="alert alert-success">Buka email anda untuk aktivasi. <br>Atau Cek folder <b>SPAM</b></div>';
					$this->session->setFlashdata('pesan', $pesan);
					return redirect()->to('Affiliator/Login');
					exit();
				} else {

					$eror = $this->db->error();
					$pesan = '<div class="alert alert-danger">Gagal registrasi<br>
					                Alasan : ' . $eror['code'] . ' ' . $eror['message'] . '
					          </div>';
					$this->session->setFlashdata('pesan', $pesan);
					return redirect()->to('affiliator/Login/registrasi');
					exit();
				}
			} else {
				$pesan = '<div class="alert alert-danger">Gagal kirim email aktivasi.</div>';
				$this->session->setFlashdata('pesan', $pesan);
				return redirect()->to('affiliator/Login/registrasi');
				exit();
			}
		}
	}


	public function verifikasi($hash = NULL)
	{
		$data = array(
			'statusverif' => '1'
		);


		$cekverif = $this->db->query("select count(*) as jlh from marketer where encryptkey='" . $hash . "' and statusverif='1'")->getRow()->jlh;
		if ($cekverif > 0) {
			$pesan = '<div class="alert alert-danger">Email sudah di aktivasi.</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('Affiliator/Login');
			exit();
		}

		$update = $this->login_model_affiliator->verifikasi_email($hash, $data);
		if ($update > 0) {
			$pesan = '<div class="alert alert-success">Berhasil di aktivasi</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('Affiliator/Login');
		} else {
			$pesan = '<div class="alert alert-danger">Gagal di aktivasi</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('Affiliator/Login');
		}
	}


	public function lupapassword()
	{
		return view('affiliator/login/lupapassword');
	}

	public function kirimresetpassword()
	{

		$email = $this->request->getPost('email');
		$from_email = 'no-replay@akuntanmu.com';
		$from_nama = 'AKUNTANMU';
		$passwordemail = 'Akuntanmu123@#';
		$password_reset = $this->generateRandomString(10);
		$lkirimemail = $this->login_model_affiliator->kirimresetpassword($from_email, $from_nama, $passwordemail, $email, $password_reset);

		if ($lkirimemail) {
			$data = array(
				'password' => md5($password_reset),
			);

			$simpan = $this->login_model_affiliator->simpanresetpassword($data, $email);
			if ($simpan) {
				$pesan = '<div class="alert alert-success">Reset password dikirim ke email <br>Atau Cek folder <b>SPAM</b>
		       			</div>';
				$this->session->setFlashdata('pesan', $pesan);
				return redirect()->to('Affiliator/Login');
				exit();
			} else {
				$pesan = 'error';
				$this->session->setFlashdata('pesan', $pesan);
				return redirect()->to('affiliator/Login/lupapassword');
				exit();
			}
		} else {
			$pesan = '<div class="alert alert-danger">Gagal reset password.</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('affiliator/Login/lupapassword');
			exit();
		}
	}



	function generateRandomString($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	function validasi_username_login()
	{
		if (!$this->validate([
			'username' => [
				'rules' => 'required|max_length[30]|regex_match[/\A[a-z0-9__.@]+\z/i]',
				'errors' => [
					'required' => '{field} Harus diisi',
					'regex_match' => 'username tidak boleh mengandung karakter unik',
				]
			],

		])) {
			echo $this->validator->getError();
		}
	}

	function cekEmail()
	{

		$email = $this->request->getPost('email');


		$hasil_email = $this->login_model_affiliator->cekEmail($email);

		if (count($hasil_email) != 0) {
			echo "1";
		} else {
			echo "2";
		}
	}
	function cekUsername()
	{

		$username = $this->request->getPost('username');


		$hasilUser = $this->login_model_affiliator->cekUsername($username);

		if (count($hasilUser) != 0) {
			echo "1";
		} else {
			echo "2";
		}
	}
}

/* End of file Login.php */
/* Location: ./application/controllers/Login.php */