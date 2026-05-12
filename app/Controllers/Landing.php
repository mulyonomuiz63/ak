<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Landing extends BaseController
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$builder = $this->db->table('iklan')->where('status','depan');
		$data['dataIklan'] =  $builder->get();

		return view('landing/index', $data);
	}
	public function tes()
	{
		echo CI_VERSION;
	    // $email = \Config\Services::email();

        // $config = [
        //     'SMTPHost'    => 'mail.akuntanmu.com',
        //     'SMTPUser'    => 'no-replay@akuntanmu.com',
        //     'SMTPPass'    => 'Akuntanmu123@#',
        //     'SMTPPort'    => 465,
        //     'SMTPCrypto'  => 'ssl',
        //     'mailType'    => 'html',
        //     'charset'     => 'UTF-8',
        //     'newline'     => "\r\n",
        // ];
        
        // $email->initialize($config);
        
        // // PENTING: from HARUS sesuai domain
        // $email->setFrom('no-replay@akuntanmu.com', 'Akuntanmu');
        // $email->setTo('mulyonomuiz63@gmail.com');
        // $email->setSubject('Test SMTP CodeIgniter 4');
        // $email->setMessage('
        //     <h3>SMTP Berhasil 🎉</h3>
        //     <p>Email ini dikirim menggunakan SMTP Rumahweb.</p>
        // ');
        // $email->SMTPDebug = 3;
        // if ($email->send()) {
        //     return '✅ Email berhasil dikirim';
        // } else {
        //     return '<pre>' . print_r($email->printDebugger(['headers']), true) . '</pre>';
        // }
// 		$builder = $this->db->table('iklan')->where('status','depan');
// 		$data['dataIklan'] =  $builder->get();

// 		return view('landing/tes', $data);
	}
	public function pssk_atc()
	{
		return view('login/psskatc');
	}

	public function error()
	{
		return view('landing/index');
	}
}
