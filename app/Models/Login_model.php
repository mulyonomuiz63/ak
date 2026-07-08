<?php

namespace App\Models;

use CodeIgniter\Model;

class Login_model extends Model
{

	public function cek_login($username, $password)
	{
		$query = "select * from v_pengguna where (username='" . $username . "' and password='" . $password . "') or (email='" . $username . "' and password='" . $password . "')";
		return $this->db->query($query);
	}

	public function cek_email($email)
	{
		$builder = $this->db->table('perusahaan');
		return $builder->getWhere(array('email' => $email));
	}

	public function cek_username($username)
	{
		$builder = $this->db->table('perusahaan');
		return $builder->getWhere(array('username' => $username));
	}

	public function simpanregistrasi($data)
	{
		$builder = $this->db->table('perusahaan');
		return $builder->insert($data);
	}

	public function kirimemail($from_email, $from_nama, $passwordemail, $email, $namaperusahaan, $encrypted_id, $kode_referal)
	{

		$textemail = '				
					<div style="font-weight: bold; font-size: 16px;">' . $namaperusahaan . '</div><br>
					<span>Terima kasih sudah mendaftar pada layanan kami
					<br>Silahkan aktivasi akun anda dibawah ini.</span><br><br>
					
					<div style="width: 100%;">
						<button style="background-color: #055F93; width: 300px; height: 50px; color: white"><a href="' . site_url("Login/verifikasi/" . $encrypted_id . '/' . $kode_referal) . '" style="color: white; text-decoration: none">AKTIVASI AKUN</a></button>			
					</div><br><br>

					<div style="width: 100%; font-size: 14px;">
						<b>Best Regards,</b> <div style="width: 100%; font-size: 14px;">
						TEAM AKUNTANMU.COM
						<br>Kantor Menara 165 Lt4 - Jakarta Selatan
						<br>Telepon: 021-86941220 / 0821-80744966</div>
					</div>			
			  		';

		$config = array();
		$config['protocol'] = "smtp";
		$config['mailType'] = "html";
		$config['SMTPHost'] = "smtp.hostinger.com";
		$config['SMTPPort'] = "465";
		$config['SMTPTimeout'] = "5";
		$config['SMTPUser'] = $from_email;
		$config['SMTPPass'] = $passwordemail;
		$config['SMTPCrypto'] = 'ssl';
		$config['CRLF'] = "\r\n";
		$config['newline'] = "\r\n";
		$config['wordWrap'] = TRUE;


		//memanggil library email dan set konfigurasi untuk pengiriman email
		$this->email = \Config\Services::email();
		$this->email->initialize($config);

		//konfigurasi pengiriman
		$this->email->setFrom($from_email, $from_nama);
		$this->email->setTo($email);
		$this->email->setSubject("Informasi Akun");
		$this->email->setMessage($textemail);

		return $this->email->send();
	}

	public function verifikasi_email($idencrypt, $data, $kode_referal)
	{
		$this->db->transBegin();

		$builder = $this->db->table('perusahaan');
		$rsperusahaan = $builder->getWhere(array('encryptkey' => $idencrypt))->getRow();

		// $this->db->start_cache();
		$builder2 = $this->db->table('perusahaan');
		$builder2->where('encryptkey', $idencrypt);
		// $this->db->stop_cache();
		$builder2->update($data);
		// $this->db->flush_cache();



		$query = "insert into akun 
					select concat(kdakun, '" . $rsperusahaan->idperusahaan . "') as idperusahaan, kdakun, nmakun, level, saldonormal, '" . $rsperusahaan->idperusahaan . "', status from akundefault order by kdakun";
		$this->db->query($query);

		$idpengguna = $this->db->query("SELECT create_idpengguna('" . date('Y-m-d') . "') as idpengguna")->getRow()->idpengguna;

		$dataPengguna = array(
			'idpengguna' => $idpengguna,
			'namapengguna' => $rsperusahaan->pengguna,
			'username' => $rsperusahaan->username,
			'password' => $rsperusahaan->password,
			'level' => '1',
			'idperusahaan' => $rsperusahaan->idperusahaan,
			'email' => $rsperusahaan->email,
		);

		$datapl = array(
			'idperusahaan' => $rsperusahaan->idperusahaan,
			'kode_referal' => $kode_referal,
			'idlangganan' => 1,
			'status' => 'B',
		);


		$builder3 = $this->db->table('pengguna');
		$builder3->insert($dataPengguna);

		$builder4 = $this->db->table('perusahaan_langganan');
		$builder4->insert($datapl);

		if ($this->db->transStatus() === FALSE) {
			$this->db->transRollback();
			return false;
		} else {
			$this->db->transCommit();
			return true;
		}
	}
	
	public function _ubah_password($email, $password)
	{
	    $data = array(
			'password' => md5($password)
		);
	    // $this->db->start_cache();
		$builder2 = $this->db->table('pengguna');
		$builder2->where('email', $email);
		// $this->db->stop_cache();
		$builder2->update($data);
		// $this->db->flush_cache();
		if ($this->db->transStatus() === FALSE) {
			$this->db->transRollback();
			return false;
		} else {
			$this->db->transCommit();
			return true;
		}
	}


	public function kirimresetpassword($from_email, $from_nama, $passwordemail, $email, $password_reset)
	{
		/**
				Untuk mengaktifkan email google
				https://myaccount.google.com/lesssecureapps?pli=1
				Allow less secure apps: ON
		 **/

		$textemail = '				
					<span>Anda baru saja melakukan permintaan reset kata sandi? <br>
					Jika IYA, Silahkan klik link dibawah untuk merubah kata sandi anda. </span><br><br>
					
					<div>
						<a href="' . $password_reset . '" style="display: inline-block; width: 200px; height: 30px; background: #1C3FAA; color: #fff; text-decoration: none; border-radius: 5px; text-align: center; line-height: 30px; font-family: `Segoe UI`, Tahoma, Geneva, Verdana, sans-serif;">Ubah Kata Sandi</a>			
					</div><br><br>
 
					<div style="width: 100%; font-size: 14px;">
						<b>Best Regards,</b> 
						<div style="width: 100%; font-size: 14px;"> 
						TEAM AKUNTANMU.COM
						<br>Kantor Menara 165 Lt4 - Jakarta Selatan
						<br>Telepon: 021-86941220 / 0821-80744966</div>
						</div>
					</div>			
			  		';

		$config = array();
		$config['protocol'] = "smtp";
		$config['mailType'] = "html";
		$config['SMTPHost'] = "smtp.hostinger.com";
		$config['SMTPPort'] = "465";
		$config['SMTPTimeout'] = "5";
		$config['SMTPUser'] = $from_email;
		$config['SMTPPass'] = $passwordemail;
		$config['SMTPCrypto'] = 'ssl';
		$config['CRLF'] = "\r\n";
		$config['newline'] = "\r\n";
		$config['wordWrap'] = TRUE;

		//memanggil library email dan set konfigurasi untuk pengiriman email
		$this->email = \Config\Services::email();
		$this->email->initialize($config);

		//$datatemplate['namaperusahaan'] = $namaperusahaan;
		//$templateemail = $this->load->view('isiemailverifikasi', $datatemplate);

		//konfigurasi pengiriman
		$this->email->setFrom($from_email, $from_nama);
		$this->email->setTo($email);
		$this->email->setSubject("Reset Password Akun");
		$this->email->setMessage($textemail);

		return $this->email->send();
	}

	public function simpanresetpassword($data, $email)
	{
		$builder = $this->db->table('pengguna');
		$builder->where('email', $email);
		return $builder->update($data);
	}

	function cekEmail($email)
	{
		$sql = "select * from pengguna where email='$email'";

		$hasil = $this->db->query($sql);

		return $hasil->getResult();
	}
	function cekUsername($username)
	{
		$sql = "select * from pengguna where username='$username'";

		$hasil = $this->db->query($sql);

		return $hasil->getResult();
	}

	public function simpanpl($data)
	{
		$this->db->transBegin();

		$builder = $this->db->table('perusahaan_langganan');
		$builder->insert($data);

		if ($this->db->transStatus() === FALSE) {
			$this->db->transRollback();
			return false;
		} else {
			$this->db->transCommit();
			return true;
		}
	}
}


/* End of file Login_model.php */
/* Location: ./application/models/Login_model.php */