<?php

namespace App\Controllers\Affiliator;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{

	public $menuaktif = 'Dashboard';

	public function index()
	{
		$data['menuaktif'] = $this->menuaktif;
		$data['marketer'] = $this->marketer_model->getMarketer();
		return view('affiliator/dashboard', $data);
	}
}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */