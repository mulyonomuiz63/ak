<?php

namespace App\Controllers;

class DokumenKeluar extends BaseController
{

    public $menuaktif = 'Dokumen Keluar';

    // public function __construct()
    // {
    //     $idpengguna = session()->get('idpengguna');
    //     if (empty($idpengguna)) {
    //         $pesan = '<div class="alert alert-danger">Silahkan anda login</div>';
    //         session()->setFlashdata('pesan', $pesan);
    //         throw new \CodeIgniter\Router\Exceptions\RedirectException('login');
    //     }
    // }

    public function index()
    {
        $data['menuaktif'] = $this->menuaktif;
        return view('dokumenkeluar/index', $data);
    }

    public function tambah()
    {
        $data['ltambah'] = "1";
        $data['id'] = "";
        $data['menuaktif'] = $this->menuaktif;
        return view('dokumenkeluar/inputdata', $data);
    }

    public function edit($id)
    {
        if ($this->dokumen_model->get_by_id($id)->getNumRows() < 1) {
            $pesan = '<div>
        				<div class="alert alert-danger alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                <strong>Ilegal!</strong> Data tidak ditemukan
        			    </div>
        			</div>';
            $this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('dokumen-keluar');
            exit();
        };

        $data['ltambah'] = "0";
        $data['id'] = $id;
        $data['menuaktif'] = $this->menuaktif;
        return view('dokumenkeluar/inputdata', $data);
    }

    public function datatablesource()
    {
        $RsData = $this->dokumen_model->get_datatables('keluar');
        $no = $this->request->getPost('start');
        $data = array();

        if ($RsData->getNumRows() > 0) {
            foreach ($RsData->getResult() as $rowdata) {

                $no++;
                $row = array();
                // $row[] = $no;
                $row[] = '<input type="checkbox" class="check-item" name="id[]" value="' . encrypt($rowdata->id) . '">';
                $row[] =  date('d-m-Y', strtotime($rowdata->created_at));
                $row[] = '<a target="_blank"  href="' . site_url('dokumen-keluar/viewfile/' . ($rowdata->file)) . '" >' . $rowdata->nama_file . '</a>';
                $row[] =  $rowdata->nama_pengirim;
                $row[] =
                    '<div class="dropdown custom-dropdown dropleft">
						<a class="btn btn-info" href="#" role="button" id="dropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
								<line x1="3" y1="12" x2="21" y2="12"></line>
								<line x1="3" y1="6" x2="21" y2="6"></line>
								<line x1="3" y1="18" x2="21" y2="18"></line>
							</svg>
						</a>
						<div class="dropdown-menu" >
							<a class="dropdown-item ml-2" href="' . site_url('dokumen-keluar/edit/' . encrypt($rowdata->id)) . '" >Edit</a>
							<a class="dropdown-item ml-2" href="' . site_url('dokumen-keluar/delete/' . encrypt($rowdata->id)) . '"  id="hapus">Delete</a>
						</div>
					</div>';
                $data[] = $row;
            }
        }

        $output = array(
            "draw" => $this->request->getPost('draw'),
            "recordsTotal" => $this->dokumen_model->count_all(),
            "recordsFiltered" => $this->dokumen_model->count_filtered('keluar'),
            "data" => $data,
        );

        //output to json format
        return $this->response->setJSON($output);
    }

    public function delete($id)
    {
        // $id = $this->encrypt->decrypt($id);
        $data = $this->dokumen_model->get_by_id($id);

        if ($data->getNumRows() < 1) {
            $pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Ilegal!</strong> Data tidak ditemukan! 
					    </div>
					</div>';
            $this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('dokumen-keluar');
            exit();
        };

        if (file_exists('./uploads/arsip/thumbnails/' . $data->getRow()->file)) {
            unlink('./uploads/arsip/thumbnails/' . $data->getRow()->file);
        };
        $hapus = $this->dokumen_model->hapus($id);
        if ($hapus) {
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
			                <strong>Gagal.</strong> Dokumen sudah digunakan pada jurnal <br>
					    </div>
					</div>';
        }

        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('dokumen-keluar');
    }

    public function deleteAll()
    {
        $data = $this->request->getPost('id');
        $dataBerhasil = 0;
        $dataGagal = 0;
        foreach ($data as $id) {
            $data = $this->dokumen_model->get_by_id($id);
            if (file_exists('./uploads/arsip/thumbnails/' . $data->getRow()->file)) {
                unlink('./uploads/arsip/thumbnails/' . $data->getRow()->file);
            };



            $hapus = $this->dokumen_model->hapus($id);
            if ($hapus) {
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
        return redirect()->to('dokumen-keluar');
    }

    public function simpan()
    {

        $id         = $this->request->getPost('id');
        $idperusahaan         = $this->request->getPost('idperusahaan');
        $nama_file         = $this->request->getPost('nama_file');
        $rows    = $this->request->getFile('file');
        $file_lama    = $this->request->getPost('file_lama');
        $status        = $this->request->getPost('status');
        $nama_pengirim        = $this->request->getPost('nama_pengirim');
        $ltambah        = $this->request->getPost('ltambah');

        if ($ltambah == '1') { // ini kondisi jika tambah data 
            $newName = $rows->getRandomName();
            $thumbnail_path = FCPATH . 'uploads/arsip/thumbnails';
            $path = FCPATH . 'uploads/arsip';
            if ($rows->isValid()) {
                if ($rows->guessExtension() == 'pdf') {
                    $nameSb = substr($newName, 0, 10);
                    // $newName =  $nameSb . '_' . $rows->getName();
                    $rows->move($thumbnail_path, $newName);
                } else {
                    // thumnail foto_ktp path
                    if ($rows->move(
                        $path,
                        $newName
                    )) {
                        // resizing newName
                        $this->image->withFile($path . '/' . $newName)
                            ->resize(1012, 1012, true, 'auto') // maintain ratio, auto dimensi
                            ->save($thumbnail_path . '/' . $newName, 80);

                        if (file_exists('./uploads/arsip/' . $newName)) {
                            unlink('./uploads/arsip/' . $newName);
                        };
                    }
                }
            }

            $data = array(
                'idperusahaan'          => $idperusahaan,
                'nama_pengirim'         => $nama_pengirim,
                'nama_file'             => $nama_file,
                'file'                  => $newName,
                'status'                => $status,
            );
            $simpan = $this->dokumen_model->simpan($data);
        } else { // ini kondisi jika edit data
            $newName = $rows->getRandomName();
            // thumnail foto_ktp path
            $thumbnail_path = FCPATH . 'uploads/arsip/thumbnails';
            $path = FCPATH . 'uploads/arsip';
            if ($rows->isValid()) {
                if (strtolower(substr($newName, -3)) == 'pdf') {
                    $nameSb = substr($newName, 0, 10);
                    // $newName =  $nameSb . '_' . $rows->getName();
                    $rows->move($thumbnail_path, $newName);
                } else {

                    if ($rows->move(
                        $path,
                        $newName
                    )) {
                        // resizing newName
                        $this->image->withFile($path . '/' . $newName)
                            ->resize(1012, 1012, true, 'auto') // maintain ratio, auto dimensi
                            ->save($thumbnail_path . '/' . $newName, 80);

                        if (file_exists('./uploads/arsip/' . $newName)) {
                            unlink('./uploads/arsip/' . $newName);
                        };
                    }
                }
                if ($file_lama != '' || $file_lama != null) {
                    if (file_exists('./uploads/arsip/thumbnails/' . $file_lama)) {
                        unlink('./uploads/arsip/thumbnails/' . $file_lama);
                    };
                }
            } else {
                $newName = $file_lama;
            }




            $data = array(
                'idperusahaan'          => $idperusahaan,
                'nama_pengirim'         => $nama_pengirim,
                'nama_file'             => $nama_file,
                'file'                  => $newName,
                'status'                => $status,

            );
            $simpan = $this->dokumen_model->updateWhere($data, $id);
        }

        if ($simpan) {
            $pesan = '<div>
						<div class="alert alert-success alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Berhasil.</strong> Data telah dikirim
					    </div>
					</div>';
        } else {
            $eror = $this->db->error();
            $pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Gagal!</strong> Data gagal dikirim! <br>
			                Pesan Error : ' . $eror['code'] . ' ' . $eror['message'] . '
					    </div>
					</div>';
        }

        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('dokumen-keluar');
    }


    public function get_edit_data()
    {
        $id = $this->request->getPost('id');
        $RsData = $this->dokumen_model->get_by_id($id)->getRow();

        $data = array(
            'id' =>  $RsData->id,
            'idperusahaan' =>  $RsData->idperusahaan,
            'nama_file' =>  $RsData->nama_file,
            'file' =>  $RsData->file,
            'status' =>  $RsData->status,
            'nama_pengirim' =>  $RsData->nama_pengirim,
        );
        return $this->response->setJSON($data);
    }

    public function viewfile($id = null)
    {
        echo '<iframe style="height:100%;width:100%;" src="' . base_url('uploads/arsip/thumbnails/' . $id) . '" title="description"></iframe>';
    }
}
