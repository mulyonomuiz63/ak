<?php

namespace App\Models;

use CodeIgniter\Model;

class Login_model_affiliator extends Model
{

	public function cek_login($username, $password)
	{
		$query = "select * from marketer where (username='" . $username . "' and password='" . $password . "') or (email='" . $username . "' and password='" . $password . "')";
		return $this->db->query($query);
	}

	public function cek_email($email)
	{
		$builder = $this->db->table('marketer');
		return $builder->getWhere(array('email' => $email));
	}

	public function cek_username($username)
	{
		$builder = $this->db->table('marketer');
		return $builder->getWhere(array('username' => $username));
	}

	public function simpanregistrasi($data)
	{
		$builder = $this->db->table('marketer');
		return $builder->insert($data);
	}

	public function kirimemail($from_email, $from_nama, $passwordemail, $email, $namamarketer, $encrypted_id)
	{

		$textemail = '				
					<div style="font-weight: bold; font-size: 16px;">' . $namamarketer . '</div><br>
					<span>Terima kasih sudah mendaftar pada layanan kami
					<br>Silahkan aktivasi akun anda dibawah ini.</span><br><br>
					
					<div style="width: 100%;">
						<button style="background-color: #055F93; width: 300px; height: 50px; color: white"><a href="' . site_url("Affiliator/Login/verifikasi/" . $encrypted_id) . '" style="color: white; text-decoration: none">AKTIVASI AKUN</a></button>			
					</div><br><br>

					<div style="width: 100%; font-size: 14px;">
						<b>Best Regards,</b> <div style="width: 100%; font-size: 14px;">
						TEAM AKUNTANMU.COM
						<br>Menara Samawa No 1106 - Jakarta Timur
						<br>Telepon: 021-86941220 / 081380935185</div>
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

	public function verifikasi_email($idencrypt, $data)
	{
		$this->db->transBegin();
		$builder2 = $this->db->table('marketer');
		$builder2->where('encryptkey', $idencrypt);
		$builder2->update($data);

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
					<span>Anda baru saja mereset password? <br>
					Silahkan login dengan password baru anda: </span><br><br>
					
					<div style="width: 100%;">
						<div style="background-color: #055F93; width: 300px; height: 50px; font-size: 35px; text-align:center; color: white">' . $password_reset . '</div>			
					</div><br><br>

					<div style="width: 100%; font-size: 14px;">
						<b>Best Regards,</b> 
						<div style="width: 100%; font-size: 14px;"> 
						TEAM AKUNTANMU.COM
						<br>Menara Samawa No 1106 - Jakarta Timur
						<br>Telepon: 021-86941220 / 081380935185</div>
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

		//$datatemplate['namamarketer'] = $namamarketer;
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
		$builder = $this->db->table('marketer');
		$builder->where('email', $email);
		return $builder->update($data);
	}

	function cekEmail($email)
	{
		$sql = "select * from marketer where email='$email'";

		$hasil = $this->db->query($sql);

		return $hasil->getResult();
	}
	function cekUsername($username)
	{
		$sql = "select * from marketer where username='$username'";

		$hasil = $this->db->query($sql);

		return $hasil->getResult();
	}

	function cekNohp($nohp)
	{
		$sql = "select * from marketer where nohp='$nohp'";

		$hasil = $this->db->query($sql);

		return $hasil->getResult();
	}
	function cekNik($nik)
	{
		$sql = "select * from marketer where nik='$nik'";

		$hasil = $this->db->query($sql);

		return $hasil->getResult();
	}
}


/* End of file Login_model.php */
/* Location: ./application/models/Login_model.php */