<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;

class LoginController extends BaseController
{	
	public function index()
	{
		$builder = $this->db->table('iklan')->whereIn('status',['banner-kanan']);
		$data['banner'] =  $builder->get()->getResultObject();
		return view('login/login', $data);
	}

	public function cekLogin()
	{
		$string = trim($this->request->getPost('username'));
		$password = trim($this->request->getPost('password'));
		$result = "$string";
		$username = preg_replace("/[^a-zA-Z0-9_.@]/", "", $result);
		$row =  preg_match('/\A[a-z0-9__.@]+\z/i', $string);
		
		$token = $this->request->getPost('recaptcha_token');

        if (!$this->verifyRecaptcha($token, 'login')) {
            $pesan = '<div class="alert alert-danger">Gagal login, Coba lagi..</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('login');
        }
        
        
		if ($row != '0') {
			if (empty($username) and empty($password)) {
				$pesan = '<div class="alert alert-danger">username atau kata sandi anda salah!</div>';
				$this->session->setFlashdata('pesan', $pesan);
				return redirect()->to('login');
			} else {
				$kirim = $this->login_model->cek_login($username, md5($password));
				if ($kirim->getNumRows() > 0) {


					$result = $kirim->getRow();
					if ($result->statusaktif == 0) {
						$pesan = '<div class="alert alert-danger">Akun anda dinonaktifkan</div>';
						$this->session->setFlashdata('pesan', $pesan);
						return redirect()->to('login');
					} else {

						$db = \Config\Database::connect();
						$cekpl = $db->table('perusahaan_langganan')->getWhere(array('idperusahaan' 	=> $result->idperusahaan))->getResultArray();
						$ceklangganan = $db->table('perusahaan')->getWhere(array('idperusahaan' => $result->idperusahaan))->getRow();
						if ($ceklangganan != null) {
							if ($ceklangganan->tglberakhir < date('Y-m-d') or $ceklangganan->tglberakhir == null) {
								$statuslangganan = 1;
								$status = 'B';
								$tgl_mulai = null;
								$tgl_akhir = null;
							} else {
								$statuslangganan = 2;
								$status = 'B';
								$tgl_mulai = $ceklangganan->tglregistrasi;
								$tgl_akhir = $ceklangganan->tglberakhir;
							}
						}

						if (count($cekpl) == 0) {
							$datapl = array(
								'idperusahaan' 	=> $result->idperusahaan,
								'kode_referal' 	=> null,
								'idlangganan' 	=> $statuslangganan,
								'va' 			=> null,
								'status' 		=> $status,
								'tgl_mulai'		=> $tgl_mulai,
								'tgl_akhir'		=> $tgl_akhir,
							);

							$this->login_model->simpanpl($datapl);
						}
						$level = $result->level == "3" ? "2" : $result->level;
						$data = array(
							'idpengguna' => $result->idpengguna,
							'username' => $result->username,
							'level' => $level,
							'level_super' => $result->level,
							'level_nama' => $result->level2,
							'namapengguna' => $result->namapengguna,
							'idperusahaan' => $result->idperusahaan,
							'namaperusahaan' => $result->namaperusahaan,
							'foto' => $result->foto,
							'kode_referal' => $result->kode_referal,
							'status_akun' => $result->status_akun,
						);

						cek_langganan();
						$this->session->set($data);
						return redirect()->to('dashboard');
					}
				} else {
					$pesan = '<div class="alert alert-danger">Username atau kata sandi anda salah!</div>';
					$this->session->setFlashdata('pesan', $pesan);
					return redirect()->to('login');
				}
			}
		} else {
			$pesan = '<div class="alert alert-danger">Username atau kata sandi anda salah!</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('login');
		}
	}
	
	private function verifyRecaptcha($token, $action)
    {
        $secret = getenv('RECAPTCHA_SECRET_KEY');

        $response = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$token}"
        );

        $result = json_decode($response, true);

        if (!isset($result['success']) || $result['success'] !== true) {
            return false;
        }

        // cek action
        if ($result['action'] !== $action) {
            return false;
        }

        // cek score (0.0 – 1.0)
        if ($result['score'] < 0.5) {
            return false;
        }

        return true;
    }

	public function registrasi($kode_r = null)
	{
		$builder5 = $this->db->table('marketer');
		$rskodereferal = $builder5->getWhere(array('status' => '1', 'kode_referal' => $kode_r))->getRow();

		if ($rskodereferal == null) {
			$kode_referal = null;
		} else {
			$kode_referal = $rskodereferal->kode_referal;
		}
		$data['kode_referal'] = $kode_referal;
		$builder = $this->db->table('iklan')->whereIn('status',['banner-kanan']);
		$data['banner'] =  $builder->get()->getResultObject();
		return view('login/registrasi', $data);
	}


	public function simpanregistrasi()
	{
	    
	    	
		$token = $this->request->getPost('recaptcha_token');

        if (!$this->verifyRecaptcha($token, 'registrasi')) {
			$pesan = '<div class="alert alert-danger">Gagal registrasi<br>
			          </div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('registrasi')->withInput();
			exit();
        }
        
		$namaperusahaan	= $this->request->getPost('namaperusahaan');
		$tglmulaiusaha	= date('Y-m-d', strtotime($this->request->getPost('tglmulaiusaha')));
		$email			= $this->request->getPost('email');
		$string   		= $this->request->getPost('username');
		$password 		= md5($this->request->getPost('password'));
		$password2 		= md5($this->request->getPost('password2'));
		$tglregistrasi	= date('Y-m-d H:i:s');
		// $tglberakhir    = date('Y-m-d H:i:s', strtotime('+90 days', strtotime($tglregistrasi)));
		$tglberakhir    = null;
		$encrypted_id 	= md5(trim($email));
		$kode_referal	= $this->request->getPost('kode_referal');
		$pengguna	= $this->request->getPost('pengguna');


		$result = "$string";
		$username = preg_replace("/[^a-zA-Z0-9_.@]/", "", $result);

		if ($password != $password2) {
			$pesan = '<div class="alert alert-danger">Ulangi, kata sandi tidak sama</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('registrasi');
			exit();
		}



		$cekid = $this->login_model->cek_email($email);
		$cekuser = $this->login_model->cek_username($username);
		if ($cekuser->getNumRows() > 0) {
			$pesan = '<div class="alert alert-danger">Username sudah digunakan</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('registrasi');
			exit();
		}
		if (($cekid->getNumRows() > 0)) {
			if ($cekid->getRow()->statusverif == 1) {
				$pesan = '<div class="alert alert-danger">Email sudah terdaftar</div>';
				$this->session->setFlashdata('pesan', $pesan);
				return redirect()->to('registrasi');
				exit();
			} else {
				$pesan = '<div class="alert alert-danger">Email sudah terdaftar & belum diaktivasi</div>';
				$this->session->setFlashdata('pesan', $pesan);
				return redirect()->to('registrasi');
				exit();
			}
		} else {

			//kirim aktivasi
			$from_email = 'no-replay@akuntanmu.com';
			$from_nama = 'AKUNTANMU';
			$passwordemail = 'Akuntanmu123@#';

			$lkirimemail = $this->login_model->kirimemail($from_email, $from_nama, $passwordemail, $email, $namaperusahaan, $encrypted_id, $kode_referal);
			// echo($namaperusahaan);
			// exit();

			if ($lkirimemail) {
				$idperusahaan = $this->db->query("SELECT create_idperusahaan('" . $tglregistrasi . "') as idperusahaan")->getRow()->idperusahaan;

				$data = array(
					'idperusahaan' => $idperusahaan,
					'namaperusahaan' => $namaperusahaan,
					'tglmulaiusaha' => $tglmulaiusaha,
					'email' => $email,
					'username' => $username,
					'password' => $password,
					'tglregistrasi' => $tglregistrasi,
					'tglberakhir' => $tglberakhir,
					'statusaktif' => '1',
					'encryptkey' => $encrypted_id,
					'statusverif' => '0',
					'kode_referal' => $kode_referal == '' ? null : $kode_referal,
					'pengguna' => $pengguna,
					'status_akun' => 'T',
				);

				$simpan = $this->login_model->simpanregistrasi($data);
				if ($simpan) {
					$pesan = '<div class="alert alert-success">Buka email anda untuk aktivasi. <br>atau cek folder <b>SPAM</b></div>';
					$this->session->setFlashdata('pesan', $pesan);
					return redirect()->to('login');
					exit();
				} else {

					$eror = $this->db->error();
					$pesan = '<div class="alert alert-danger">Gagal registrasi<br>
					                Alasan : ' . $eror['code'] . ' ' . $eror['message'] . '
					          </div>';
					$this->session->setFlashdata('pesan', $pesan);
					return redirect()->to('registrasi');
					exit();
				}
			} else {
				$pesan = '<div class="alert alert-danger">Gagal kirim email aktivasi.</div>';
				$this->session->setFlashdata('pesan', $pesan);
				return redirect()->to('registrasi');
				exit();
			}
		}
	}


	public function verifikasi($hash = NULL, $kode_referal = null)
	{
		$data = array(
			'statusverif' => '1'
		);


		$cekverif = $this->db->query("select count(*) as jlh from perusahaan where encryptkey='" . $hash . "' and statusverif='1'")->getRow()->jlh;
		if ($cekverif > 0) {
			$pesan = '<div class="alert alert-danger">Email sudah di aktivasi.</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('login');
			exit();
		}

		$update = $this->login_model->verifikasi_email($hash, $data, $kode_referal);
		if ($update > 0) {
			$pesan = '<div class="alert alert-success">Berhasil di aktivasi</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('login');
		} else {
			$pesan = '<div class="alert alert-danger">Gagal di aktivasi</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('login');
		}
	}


	public function orders()
	{
		return view('login/orders');
	}



	public function lupapassword()
	{
	    $builder = $this->db->table('iklan')->whereIn('status',['banner-kanan']);
		$data['banner'] =  $builder->get()->getResultObject();
		return view('login/lupapassword', $data);
	}

    public function ubahPassword($token = null)
	{
	    $data['token'] = $token;
	    
	    if (!$token) {
	        $pesan = '<div class="alert alert-danger">Token tidak valid.</div>';
			$this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('/login');
        }

        $reset = $this->db->table('password_resets')->where('token', $token)->get()->getRow();

        if (!$reset) {
            $pesan = '<div class="alert alert-danger">Token tidak valid.</div>';
			$this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('/login');
        }

        if (Time::now()->isAfter($reset->expires_at)) {
            $pesan = '<div class="alert alert-danger">Token sudah kadaluarsa.</div>';
			$this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('/login');
        }

	    
	    $builder = $this->db->table('iklan')->whereIn('status',['banner-kanan']);
		$data['banner'] =  $builder->get()->getResultObject();
		return view('login/ubahpassword', $data);
	}
	
	public function updatePassword(){
    	$token    = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        $reset = $this->db->table('password_resets')->where('token', $token)->get()->getRow();
        if (!$reset) {
            $pesan = '<div class="alert alert-danger">Token tidak valid.</div>';
			$this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('/Login');
        }
    	
	    $update = $this->login_model->_ubah_password($reset->email, $password);
        // Hapus token agar tidak reuse
        $this->db->table('password_resets')->where('email', $reset->email)->delete();
        
		if ($update) {
			$pesan = '<div class="alert alert-success">Kata sandi berhasil diubah</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('login');
		} else {
			$pesan = '<div class="alert alert-danger">Kata sandi gagal diubah</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('login');
		}
	}
	
	public function kirimResetPassword()
	{
		$email = $this->request->getPost('email');
		$from_email = 'no-replay@akuntanmu.com';
		$from_nama = 'AKUNTANMU';
		$passwordemail = 'Akuntanmu123@#';
		
		$token = $this->request->getPost('recaptcha_token');

        if (!$this->verifyRecaptcha($token, 'lupapassword')) {
			$pesan = '<div class="alert alert-danger">Gagal reset kata sandi.</div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('lupapassword');
			exit();
        }
        
		
        $builder5 = $this->db->table('pengguna');
		$cekdata = $builder5->getWhere(array('email' => $email))->getRow();
		if(!empty($cekdata)){
		    
		    // Buat token
            $token = bin2hex(random_bytes(32));
    
            // Simpan ke DB
            $this->db->table('password_resets')->insert([
                'email'      => $email,
                'token'      => $token,
                'expires_at' => Time::now()->addMinutes(30) // expired 30 menit
            ]);
    
            // Link reset
    		$password_reset = base_url("ubah-password/$token"); 
    		
    		
    		
    		
    		$lkirimemail = $this->login_model->kirimResetPassword($from_email, $from_nama, $passwordemail, $email, $password_reset);
    		
    		
    		
    		if ($lkirimemail) {
    			$pesan = '<div class="alert alert-success">Reset kata sandi dikirim ke email <br>atau cek folder <b>SPAM</b>
    	       			</div>';
    			$this->session->setFlashdata('pesan', $pesan);
    			return redirect()->to('login');
    			exit();
    		} else {
    			$pesan = '<div class="alert alert-danger">Gagal reset kata sandi.</div>';
    			$this->session->setFlashdata('pesan', $pesan);
    			return redirect()->to('lupapassword');
    			exit();
    		}
		}else{
		        $pesan = '<div class="alert alert-danger">Email tidak ditemukan.</div>';
    			$this->session->setFlashdata('pesan', $pesan);
    			return redirect()->to('lupapassword');
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
					'required' => 'Username harus diisi',
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


		$hasil_email = $this->login_model->cekEmail($email);

		if (count($hasil_email) != 0) {
			echo "1";
		} else {
			echo "2";
		}
	}
	function cekUsername()
	{

		$username = $this->request->getPost('username');


		$hasilUser = $this->login_model->cekUsername($username);

		if (count($hasilUser) != 0) {
			echo "1";
		} else {
			echo "2";
		}
	}
	public function autocomplate()
	{
		$cari = $this->request->getPost('term');

		$query = "SELECT * FROM marketer WHERE ( kode_referal like '%" . $cari . "%' or nama like '%" . $cari . "%' ) order by nama asc limit 10";
		$res = $this->db->query($query);
		$result = array();
		foreach ($res->getResult() as $row) {
			array_push($result, array(
				'idmarketer' => $row->idmarketer,
				'nama' => $row->nama,
				'kode_referal' => $row->kode_referal,
			));
		}
		return $this->response->setJSON($result);
	}

	public function logout()
	{
// 		$this->session->destroy();
        $session = session();
        $sessionID = session_id();  // ambil ID dari PHP
        
        $session->destroy();        // hancurkan session aktif
        
        $file = WRITEPATH . 'session/ci_session' . $sessionID;
        if (is_file($file)) {
            unlink($file);
        }
		return redirect()->to('login/');
	}


	//untuk autentication upload file ke google drive
    public function authAdminDrive()
    {
        $client = new \Google\Client();
        $client->setAuthConfig(APPPATH . 'ThirdParty/oauth-credentials.json');
        $client->addScope(\Google\Service\Drive::DRIVE);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        // Sesuaikan redirect URI dengan yang ada di Google Console
        $client->setRedirectUri(base_url('google-callback-admin'));

        return redirect()->to($client->createAuthUrl());
    }

    public function adminDriveCallback()
    {
        $client = new \Google\Client();
        $client->setAuthConfig(APPPATH . 'ThirdParty/oauth-credentials.json');
        $client->setRedirectUri(base_url('google-callback-admin'));

        $token = $client->fetchAccessTokenWithAuthCode($this->request->getGet('code'));

        if (!isset($token['error'])) {
            // Simpan token ke folder writable agar aman
            file_put_contents(WRITEPATH . 'google-token-admin.json', json_encode($token));
            return "Berhasil! Token Admin tersimpan. Sekarang siswa bisa upload file ke Drive Anda.";
        }

        return "Gagal mendapatkan token: " . json_encode($token);
    }
}

/* End of file Login.php */
/* Location: ./application/controllers/Login.php */