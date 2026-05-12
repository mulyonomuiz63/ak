<?php

namespace App\Controllers;

class ValidasiJurnal extends BaseController
{

	//validasi jurnal
	public function index($idjurnal)

	{
	    $data['rsDataJurnal'] = $this->jurnal_model->get_validasi_by_id($idjurnal)->getRow();
	    if(!empty($data['rsDataJurnal'])){
    	    $idperusahaan = encrypt($data['rsDataJurnal']->idperusahaan);
    		$idpengguna = encrypt($data['rsDataJurnal']->idpengguna);
    
    
    
    		$data['namaperusahaan'] = $this->perusahaan_model->get_by_id($idperusahaan)->getRow()->namaperusahaan;
    		$data['rsData'] = $this->jurnal_model->get_jurnal_cetak($idjurnal);
    		
    		 session()->setFlashdata('pesan', "
                        swal({
                            title: 'Informasi',
                            text: 'Jurnal Telah Terverifikasi',
                            type: 'success',
                            padding: '2em'
                            });
                        ");
    		return view('validasijurnal/index',$data);
	    }else{
	        session()->setFlashdata('pesan', "
                        swal({
                            title: 'Info!',
                            text: 'Data yang anda masukan tidak ditemukan...',
                            type: 'info',
                            padding: '2em'
                            });
                        ");
	        return redirect()->to('/');
	    }
	}
}

/* End of file Login.php */
/* Location: ./application/controllers/Login.php */