<?php

namespace App\Controllers\Affiliator;

use App\Controllers\BaseController;

class Histori extends BaseController
{

    public $menuaktif = 'berlangganan';

    public function __construct()
    {
        $idpengguna = session()->get('idpengguna');
        if (empty($idpengguna)) {
            $pesan = '<div class="alert alert-danger">Silahkan anda login</div>';
            session()->setFlashdata('pesan', $pesan);
            throw new \CodeIgniter\Router\Exceptions\RedirectException('login');
        }
        if (session()->get('level') == '2') {

            if (session()->get('hitAlert') == 'free') {
                $pesan = '<div class="alert alert-success" role="alert">
                            <strong>Segera berlangganan!</strong> Anda sedang menggunakan paket Free yang semua fitur terbatas ☺️ Segera update akun anda ke berlangganan untuk bisa menggunakan fitur tanpa batas.
                        </div>';
                session()->setFlashdata('pesan', $pesan);
                throw new \CodeIgniter\Router\Exceptions\RedirectException('Dashboard');
            } elseif (session()->get('hitAlert') == 'tidak') {
                $pesan = '<div class="alert alert-success" role="alert">
                        <strong>Segera perpanjang paket langganan anda!</strong> Supaya dapat menggunakan firut-fitur akuntanmu kembali ☺
                    </div>';
                session()->setFlashdata('pesan', $pesan);
                throw new \CodeIgniter\Router\Exceptions\RedirectException('Dashboard');
            } else {
                $pesan = '<div class="alert alert-success" role="alert">
                        <strong>Gunakan akun dengan level admin!</strong> Untuk proses memperpanjang langganan atau mendaftakan akun ke berlangganan..
                    </div>';
                session()->setFlashdata('pesan', $pesan);
                throw new \CodeIgniter\Router\Exceptions\RedirectException('Dashboard');
            }
        }
    }

    public function index()
    {
        $data['menuaktif'] = $this->menuaktif;
        $data['langganan'] = $this->histori_model->getLangganan();
        $data['totalpayment'] = $this->db->table('payment')->select('sum(nominal) - sum(nominal_komisi) as total')->where('status', 'S')->get()->getRow();
        $data['caraBayar'] = $row = $this->db->table('v_langganan')
                                            ->select("
                                                SUM(CASE WHEN status = 'P' THEN 1 ELSE 0 END) AS total_p,
                                                SUM(CASE WHEN status = 'V' THEN 1 ELSE 0 END) AS total_v
                                            ")
                                            ->where('idperusahaan', session()->get('idperusahaan'))
                                            ->get()
                                            ->getRow();
        
        return view('affiliator/histori/index', $data);
    }
    
    public function verifikasi()
    {
        $data['menuaktif'] = $this->menuaktif;
        $data['langganan'] = $this->histori_model->getLangganan();
        $data['totalpayment'] = $this->db->table('payment')->select('sum(nominal) - sum(nominal_komisi) as total')->where('status', 'S')->get()->getRow();
        return view('affiliator/histori/index', $data);
    }
    
    public function tidakaktif()
    {
        $data['menuaktif'] = $this->menuaktif;
        $data['langganan'] = $this->histori_model->getLangganan();
        $data['totalpayment'] = $this->db->table('payment')->select('sum(nominal) - sum(nominal_komisi) as total')->where('status', 'S')->get()->getRow();
        return view('affiliator/histori/index', $data);
    }


    public function datatablesource()
    {
        $statusTransaksis = $this->request->getPost('status');
        if($statusTransaksis ==  ""){
            $statusTransaksi ='B';
        }elseif($statusTransaksis == 'verifikasi'){
            $statusTransaksi ='V';
        }elseif($statusTransaksis == 'tidakaktif'){
            $statusTransaksi ='S';
        }
        $RsData = $this->histori_model->get_datatables($statusTransaksi);
        $data = array();
        if ($RsData->getNumRows() > 0) {
            foreach ($RsData->getResult() as $rowdata) {
                if ($rowdata->status == 'P') {
                    $status = '<span class="badge bg-secondary text-white">Pengajuan</span>';
        
                    if ($rowdata->status_transfer == 'T'):
                        $uploadfile = '<button type="button" class="btn btn-sm btn-primary shadow-sm btn-pilih-idpl" 
                            data-toggle="modal" 
                            data-target="#pilihPembayaran" 
                            data-idpl="'.$rowdata->idpl.'"
                             data-kode_unik="' . $rowdata->kode_unik . '" data-nama="' . $rowdata->nama_langganan . '" data-nominal="' . $rowdata->nominal . '">
                                <i class="bi bi-credit-card-2-back-fill mr-1"></i> Metode Pembayaran
                           </button>';

                    elseif($rowdata->status_transfer == 'M'):
                        $uploadfile = '<button type="button" class="btn btn-sm btn-primary btn-circle tooltips mx-1" data-toggle="modal" id="uploadBukti" data-kode_unik="' . $rowdata->kode_unik . '" data-nama="' . $rowdata->nama_langganan . '" data-nominal="' . $rowdata->nominal . '" data-target="#uploadFile" data-placement="left" title="">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                          </button>
                          ';
                    else:
                        $uploadfile = '<button type="button" class="btn btn-sm btn-primary shadow-sm btn-pilih-idpl" id="pay-button-midtrans"  
                                    data-idpl="'.$rowdata->idpl.'"
                                    data-kode_unik="' . $rowdata->kode_unik . '" data-nama="' . $rowdata->nama_langganan . '" data-nominal="' . $rowdata->nominal . '">
                                        <i class="bi bi-credit-card-2-back-fill mr-1"></i> Bayar Sekarang
                                   </button>';
                    endif;
        
                } elseif ($rowdata->status == 'V') {
                    $status = '<span class="badge bg-info text-white">Verifikasi Pembayaran</span>';
                    $uploadfile = '<a href="javascript:void(0)" data-url="Halo Akuntanmu,%20saya%20ingin%20konfirmasi%20pembayaran%20paket%20langganan..."
                       class="btn btn-sm btn-primary btn-circle myFunctionKonfirmasi"  data-toggle="tooltip" data-placement="left" title="Segera Konfirmasi Pembayaran"><i class="fa fa-file "></i></a>';
                } elseif ($rowdata->status == 'B') {
                    $status = '<span class="badge bg-success text-white">Aktif</span>';
                    $uploadfile = '<a href="' . site_url('histori/detail/' . encrypt($rowdata->idperusahaan)) . '" class="btn btn-sm btn-success btn-circle"  data-status="' . $rowdata->status . '" data-idperusahaan="' . $rowdata->idperusahaan . '" data-target="#Approv" data-placement="left" title="">
            <i class="fa fa-file "></i>
          </a>';
                } else {
                    $status = '<span class="badge bg-danger text-white">Tidak Aktif</span>';
                    $uploadfile = '<a href="' . site_url('histori/detail/' . encrypt($rowdata->idperusahaan)) . '" class="btn btn-sm btn-success btn-circle"  data-status="' . $rowdata->status . '" data-idperusahaan="' . $rowdata->idperusahaan . '" data-target="#Approv" data-placement="left" title="">
            <i class="fa fa-file "></i>
          </a>';
                }

                if (session()->get('idperusahaan') == '9999999999') {
                    if ($rowdata->status == 'V') {
                        $uploadfile = '<button type="button" class="btn btn-sm btn-primary btn-circle tooltips mx-1" data-toggle="modal" id="approv" data-status="' . $rowdata->status . '" data-idperusahaan="' . $rowdata->idperusahaan . '" data-target="#Approv" data-placement="left" title="">
            <i class="fa fa-upload" aria-hidden="true"></i>
          </button>';
                    } else {
                        $uploadfile = '<a href="' . site_url('histori/detail/' . encrypt($rowdata->idperusahaan)) . '" class="btn btn-sm btn-success btn-circle"  data-status="' . $rowdata->status . '" data-idperusahaan="' . $rowdata->idperusahaan . '" data-target="#Approv" data-placement="left" title="">
            <i class="fa fa-file "></i>
          </a>';
                    }
                }
                $row = array();
                $row[] = $rowdata->nama_langganan;
                $row[] = $rowdata->namaperusahaan;
                // $row[] = number_format($rowdata->nominal, 0, ".", ".");
                $row[] = $status;
                $row[] = $rowdata->jumlah_hari < 1 ? '0' : $rowdata->jumlah_hari;
                $row[] = $rowdata->tgl_mulai == null ? '-' : date('d-m-Y', strtotime($rowdata->tgl_mulai));
                $row[] = $rowdata->tgl_akhir == null ? '-' : date('d-m-Y', strtotime($rowdata->tgl_akhir));
                $row[] = $uploadfile;
                $data[] = $row;
            }
        }

        $output = array(
            "draw" => $this->request->getPost('draw'),
            "recordsTotal" => $this->histori_model->count_all($statusTransaksi),
            "recordsFiltered" => $this->histori_model->count_filtered($statusTransaksi),
            "data" => $data,
        );

        //output to json format
        return $this->response->setJSON($output);
    }


    public function simpan()
    {

        $kode_referal           = $this->request->getPost('kode_referal');
        $idlangganan            = $this->request->getPost('idlangganan');
        $harga                  = $this->request->getPost('nominal');
        $bulan                  = $this->request->getPost('bulan');
        $diskon                 = $this->request->getPost('diskon');


        $builder5 = $this->db->table('marketer');
        $rskodereferal = $builder5->getWhere(array('status' => '1', 'kode_referal' => $kode_referal))->getRow();
        $builder6 = $this->db->table('langganan a');
        $builder6->select('a.*, b.diskon');
        $builder6->join('diskon b', 'a.iddiskon = b.iddiskon');
        $rslangganan = $builder6->getWhere(array('a.idlangganan' => $idlangganan))->getRow();
        if ($rskodereferal == null) {
            $nominal = $harga;
        } else {
            $nominal = $harga - ($harga * $rslangganan->diskon / 100);
        }

        $db = \Config\Database::connect();
        $status = ['P', 'V'];
        $ceklangganan = $db->table('v_langganan')->whereIn('status', $status)->getWhere(array('idperusahaan' => session()->get('idperusahaan')))->getResultArray();
        if (count($ceklangganan) == '0') {
            if ($idlangganan == '2') {
                $tambahBulan = '+' . $bulan . ' month';
            } elseif ($idlangganan == '3') {
                $tambahBulan = '+' . $bulan . ' year';
            } else {
                $tambahBulan = '+' . $bulan . ' month';
            }

            $builder3 = $this->db->table('perusahaan_langganan');
            $rsperusahaan = $builder3->getWhere(array('status' => 'B', 'idperusahaan' => session()->get('idperusahaan')))->getRow();

            if ($rsperusahaan == null) {
                $tgl_mulai = date('Y-m-d');
            } else {
                if ($rsperusahaan->tgl_akhir < date('Y-m-d')) {
                    $tgl_mulai = date('Y-m-d');
                } else {
                    $tgl_mulai = $rsperusahaan->tgl_akhir;
                }
            }

            $tgl_akhir = date('Y-m-d', strtotime($tambahBulan, strtotime($tgl_mulai)));

            $data = array(
                'idperusahaan'      => session()->get('idperusahaan'),
                'kode_referal'      => $kode_referal == '' ? null : $kode_referal,
                'idlangganan'       => $idlangganan,
                'nominal'           => $nominal,
                'kode_unik'         => mt_rand(100, 999),
                'status'            => 'P',
                'tgl_mulai'         => date('Y-m-d'),
                'tgl_akhir'         => $tgl_akhir,
            );
            $data1 = array(
                'kode_referal'      => $kode_referal == '' ? null : $kode_referal,
            );

            $this->session->set($data1);
            $this->histori_model->simpan($data, $data1);
            $pesan = '<div>
                				<div class="alert alert-success alert-dismissable">
                	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                	                <strong>Berhasil.</strong> Berlangganan berhasil diajukan silahkan lakukan pembayaran.
                			    </div>
                			</div>';
        } else {
            $pesan = '<div>
                				<div class="alert alert-danger alert-dismissable">
                	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                	                <strong>Gagal!</strong> Berlangganan gagal diajukan! <br>
                			    </div>
                			</div>';
        }

        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('histori');
    }

    public function uploadPembayaran()
    {

        $files = $this->request->getFile('fileBukti');
        $nama_langganan = $this->request->getPost('nama_langganan');
        $nominal = $this->request->getPost('nominalUpload');
        $kode_unik = $this->request->getPost('kode_unik');
        $newName = $files->getRandomName();
        // thumnail foto_ktp path
        $thumbnail_path = FCPATH . 'uploads/buktitransaksi/thumbnails';
        $path = FCPATH . 'uploads/buktitransaksi';

        if ($files->move($path, $newName)) {
            // resizing newName
            $this->image->withFile($path . '/' . $newName)
                ->resize(1012, 1012, true, 'auto') // maintain ratio, auto dimensi
                ->save($thumbnail_path . '/' . $newName, 80);

            if (file_exists('./uploads/buktitransaksi/' . $newName)) {
                unlink('./uploads/buktitransaksi/' . $newName);
            };
        }
        $data = [
            'idperusahaan'      => session()->get('idperusahaan'),
            'nama_langganan'    => $nama_langganan,
            'nominal'           => $nominal,
            'kode_unik'         => $kode_unik,
            'bukti_pembayaran'  => $newName,
            'status'            => 'P',
        ];
        $data1 = [
            'status' => 'V',
        ];
        $hasil =  $this->histori_model->simpanUpload($data, $data1);
        if ($hasil) {
            $pesan = '<div>
        				<div class="alert alert-success alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                <strong>Berhasil.</strong> upload bukti  pembayaran.
        			    </div>
        			</div>';
        } else {
            $pesan = '<div>
        				<div class="alert alert-danger alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                <strong>Gagal!</strong> Gagal upload bukti pembayaran <br>
        			    </div>
        			</div>';
        }


        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('histori');
    }

    public function approve()
    {

        $idperusahaan = $this->request->getPost('idperusahaan');
        $nominal_komisi = $this->request->getPost('nominal_komisi');


        $hasil =  $this->histori_model->simpanApprove($nominal_komisi, $idperusahaan);
        if ($hasil) {
            $pesan = '<div>
        				<div class="alert alert-success alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                <strong>Berhasil.</strong> Approve bukti  pembayaran.
        			    </div>
        			</div>';
        } else {
            $pesan = '<div>
        				<div class="alert alert-danger alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                <strong>Gagal!</strong> Gagal approve upload bukti pembayaran <br>
        			    </div>
        			</div>';
        }


        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('histori');
    }

    public function get_edit_data()
    {
        $idperusahaan = $this->request->getPost('idperusahaan');
        $RsData = $this->histori_model->get_by_id($idperusahaan)->getRow();

        $builder1 = $this->db->table('marketer');
        $rsmarketer = $builder1->where(array('kode_referal' => $RsData->kode_referal))->get()->getRow();
        if ($rsmarketer == null) {
            $kode_referal = null;
        } else {
            $kode_referal = $rsmarketer->kode_referal;
        }
        $data = array(
            'idperusahaan'      =>  $RsData->idperusahaan,
            'namaperusahaan'    =>  $RsData->namaperusahaan,
            'nama_langganan'    =>  $RsData->nama_langganan,
            'nominal'           =>  $RsData->nominal,
            'komisi'            =>  $RsData->komisi,
            'bukti_pembayaran'  =>  $RsData->bukti_pembayaran,
            'status'            =>  $RsData->status,
            'kode_referal'      =>  $kode_referal,
            'kode_unik'         =>  $RsData->kode_unik
        );
        return $this->response->setJSON($data);
    }

    public function get_caraBayar()
    {
        $idperusahaan = session()->get('idperusahaan');
        $RsData = $this->histori_model->get_caraBayar($idperusahaan)->getRow();

        $data = array(
            'nominal'           =>  $RsData->nominal,
            'kode_unik'         =>  $RsData->kode_unik,
        );
        return $this->response->setJSON($data);
    }



    public function detail($id)
    {
        $data['menuaktif'] = $this->menuaktif;
        $data['idperusahaan'] = ($id);
        return view('affiliator/histori/detail/index', $data);
    }

    public function datatablesourceDetail()
    {
        $idperusahaan = $this->request->getPost('idperusahaan');
        $RsData = $this->histori_model->get_datatablesDetail($idperusahaan);
        $data = array();
        if ($RsData->getNumRows() > 0) {
            foreach ($RsData->getResult() as $rowdata) {
                if ($rowdata->status == 'T') {
                    $status = '<span class="badge bg-danger text-white">Dibatalkan</span>';
                    $uploadfile = '<i class="fa fa-file btn btn-sm btn-success btn-circle"></i>';
                } else {
                    $status = '<span class="badge bg-success text-white">Selesai</span>';
                    $uploadfile = '<button type="button" class="btn btn-sm btn-success btn-circle" data-toggle="modal" id="approv"  data-idpayment="' . $rowdata->idpayment . '" data-target="#Approv" data-placement="left" title="Approve">
            <i class="fa fa-file " aria-hidden="true"></i>
          </button>';
                }

                $row = array();
                $row[] = $rowdata->namaperusahaan;
                $row[] = date('d-m-Y H:i:s', strtotime($rowdata->created_at));
                $row[] = $rowdata->nama_langganan;
                $row[] = number_format($rowdata->nominal, 0, ".", ".");
                $row[] = $rowdata->kode_unik;
                $row[] = $status;
                $row[] = $uploadfile;
                $data[] = $row;
            }
        }

        $output = array(
            "draw" => $this->request->getPost('draw'),
            "recordsTotal" => $this->histori_model->count_all($idperusahaan),
            "recordsFiltered" => $this->histori_model->count_filtered($idperusahaan),
            "data" => $data,
        );

        //output to json format
        return $this->response->setJSON($output);
    }

    public function get_edit_dataDetail()
    {
        $idpayment = $this->request->getPost('idpayment');
        $RsData = $this->histori_model->get_by_idDetail($idpayment)->getRow();

        $data = array(
            'idperusahaan'      =>  $RsData->idperusahaan,
            'namaperusahaan'    =>  $RsData->namaperusahaan,
            'nama_langganan'    =>  $RsData->nama_langganan,
            'nominal'           =>  $RsData->nominal,
            'bukti_pembayaran'  =>  $RsData->bukti_pembayaran,
            'kode_unik'         =>  $RsData->kode_unik,
            'status'            =>  $RsData->status
        );
        return $this->response->setJSON($data);
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
    
    public function autoload_expired_pembayran(){
       try {
            $this->db->table('transaksi_pl')
                ->where('status', 'P')
                ->where('tgl_mulai <', date('Y-m-d'))
                ->delete();
    
            return true;
    
        } catch (\Throwable $e) {
            log_message('error', 'Cron bersihkanExpired: ' . $e->getMessage());
            return false;
        }
    }
    
    public function batalkan_pesanan(){
        $hasil = $this->db->table('transaksi_pl')
                ->where('idperusahaan', session()->get('idperusahaan'))
                ->where('status', 'P')
                ->delete();
        if ($hasil) {
            $pesan = '<div>
        				<div class="alert alert-success alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                <strong>Berhasil.</strong> pesanan telah dibatalkan.
        			    </div>
        			</div>';
        } else {
            $pesan = '<div>
        				<div class="alert alert-danger alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                <strong>Gagal!</strong> pesanan tidak bisa dibatalkan <br>
        			    </div>
        			</div>';
        }


        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('histori');
    }
}

/* End of file diskon.php */
/* Location: ./application/controllers/diskon.php */