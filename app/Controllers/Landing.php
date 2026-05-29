<?php

namespace App\Controllers;

class Landing extends BaseController
{
	public function index()
	{
		$builder = $this->db->table('iklan')->where('status','depan');
		$data['dataIklan'] =  $builder->get();

		return view('landing/index', $data);
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
