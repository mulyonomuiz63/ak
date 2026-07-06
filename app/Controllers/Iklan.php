<?php

namespace App\Controllers;

class Iklan extends BaseController
{

    public $menuaktif = 'iklan';


    public function index()
    {
        cek_pengguna();
        $data['menuaktif'] = $this->menuaktif;
        return view('iklan/index', $data);
    }

    public function tambah()
    {
        if (!(session()->get('databaseHitPengguna') <= session()->get('hitPengguna'))) {
            return redirect()->to('iklan');
        }
        $data['idiklan'] = "";
        $data['menuaktif'] = $this->menuaktif;
        return view('iklan/inputdata', $data);
    }

    public function edit($idiklan)
    {
        if ($this->iklan_model->get_by_id($idiklan)->getNumRows() < 1) {
            $pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Ilegal!</strong> Data tidak ditemukan 
					    </div>
					</div>';
            $this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('iklan');
        };

        $data['idiklan'] = $idiklan;
        $data['menuaktif'] = $this->menuaktif;
        return view('iklan/inputdata', $data);
    }

    public function datatablesource()
    {
        $RsData = $this->iklan_model->get_datatables();
        $no = $this->request->getPost('start');
        $data = array();

        if ($RsData->getNumRows() > 0) {
            foreach ($RsData->getResult() as $rowdata) {
                $no++;
                $row = array();
                // $row[] = $no;
                $row[] = '<input type="checkbox" class="check-item" name="idiklan[]" value="' . encrypt($rowdata->idiklan) . '">';
                $row[] = $rowdata->nama_iklan;
                $row[] = $rowdata->status;
                $row[] = '<div class="d-flex justify-content-center"><img src="'.base_url('uploads/iklan/thumbnails/'.$rowdata->file) .'" class="d-block w-50 rounded" alt=""></div>';
                $row[] =
                    '<div class="dropdown custom-dropdown dropleft">
						<a class="badge btn-info" href="#" role="button" id="dropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="bi bi-three-dots"></i>
						</a>
						<div class="dropdown-menu" >
							<a class="dropdown-item" href="' . site_url('iklan/edit/' . encrypt($rowdata->idiklan)) . '"  class="ml-2">Edit</a>
							<a class="dropdown-item" href="' . site_url('iklan/delete/' . encrypt($rowdata->idiklan)) . '"  class="ml-2"  id="hapus">Delete</a>
						</div>
					</div>';
                $data[] = $row;
            }
        }

        $output = array(
            "draw" => $this->request->getPost('draw'),
            "recordsTotal" => $this->iklan_model->count_all(),
            "recordsFiltered" => $this->iklan_model->count_filtered(),
            "data" => $data,
        );

        //output to json format
        return $this->response->setJSON($output);
    }

    public function delete($id)
    {

        if ($this->iklan_model->get_by_id($id)->getNumRows() < 1) {
            $pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Ilegal!</strong> Data tidak ditemukan 
					    </div>
					</div>';
            $this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('iklan');
        };




        $file = $this->iklan_model->get_by_id($id)->getRow()->file;
        $hapus = $this->iklan_model->hapus($id);
        if ($hapus) {
            if ($file != '' && $file != null) {
                unlink('./uploads/iklan/thumbnails/' . $file);
            }
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
			                <strong>Maaf.</strong> Data gagal dihapus
					    </div>
					</div>';
        }

        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('iklan');
    }

    public function deleteAll()
    {
        $dataIklan = $this->request->getPost('idiklan');
        $dataBerhasil = 0;
        $dataGagal = 0;
        foreach ($dataIklan as $id) {




            $file = $this->iklan_model->get_by_id($id)->getRow()->file;
            $hapus = $this->iklan_model->hapus($id);
            if ($hapus) {
                if ($file != '' && $file != null) {
                    unlink('./uploads/iklan/thumbnails/' . $file);
                }
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
        return redirect()->to('iklan');
    }

    public function simpan()
    {
 
        $idiklan        = $this->request->getPost('idiklan');
        $nama_iklan     = $this->request->getPost('nama_iklan');
        $file           = $this->request->getFile('file');
        $file_lama      = $this->request->getPost('file_lama');
        $status         = $this->request->getPost('status');
        $url            = $this->request->getPost('url');

        if (empty($idiklan)) { // ini kondisi jika tambah data 

            if ($file->isValid()) {
                $newName = $file->getRandomName();
                // thumnail foto_ktp path
                $thumbnail_path = FCPATH . 'uploads/iklan/thumbnails';

                $path = FCPATH . 'uploads/iklan';
                if ($file->move($path, $newName)) {
                    // resizing newName
                    $this->image->withFile($path . '/' . $newName)
                        // ->resize(1012, 1012, true, 'auto') // maintain ratio, auto dimensi
                        ->save($thumbnail_path . '/' . $newName, 80); 

                    if (file_exists('./uploads/iklan/' . $newName)) {
                        unlink('./uploads/iklan/' . $newName);
                    };
                }
            }

            $data = array(
                'nama_iklan'    => $nama_iklan,
                'file'          => $newName,
                'url'           => $url,
                'status'        => $status,
            );
            $simpan = $this->iklan_model->simpan($data);
        } else { // ini kondisi jika edit data



            if ($file->isValid()) {
                $newName = $file->getRandomName();
                // thumnail foto_ktp path
                $thumbnail_path = FCPATH . 'uploads/iklan/thumbnails';

                $path = FCPATH . 'uploads/iklan';
                if ($file->move($path, $newName)) {
                    // resizing newName
                    $this->image->withFile($path . '/' . $newName)
                        // ->resize(1012, 1012, true, 'auto') // maintain ratio, auto dimensi
                        ->save($thumbnail_path . '/' . $newName, 80);

                    if (file_exists('./uploads/iklan/thumbnails/' . $file_lama)) {
                        unlink('./uploads/iklan/thumbnails/' . $file_lama);
                    };
                    
                    if (file_exists('./uploads/iklan/' . $newName)) {
                        unlink('./uploads/iklan/' . $newName);
                    };
                }
            } else {
                $newName = $file_lama;
            }

            $data = array(
                'nama_iklan'    => $nama_iklan,
                'file'          => $newName,
                'url'           => $url,
                'status'        => $status,
            );

            $simpan = $this->iklan_model->updateWhere($data, $idiklan);
        }

        if ($simpan) {
            $pesan = '<div>
						<div class="alert alert-success alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Berhasil.</strong> Data telah disimpan
					    </div>
					</div>';
        } else {
            $eror = $this->db->error();
            $pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Gagal,</strong> Data gagal di simpan <br>
					    </div>
					</div>';
        }

        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('iklan');
    }

    public function get_edit_data()
    {
        $idiklan = $this->request->getPost('idiklan');
        $RsData = $this->iklan_model->get_by_id($idiklan)->getRow();

        $data = array(
            'idiklan' =>  $RsData->idiklan,
            'nama_iklan' =>  $RsData->nama_iklan,
            'file' =>  $RsData->file,
            'url' =>  $RsData->url,
            'status' => $RsData->status,
        );
        echo (json_encode($data));
    }

    public function upload_foto($file, $nama, $new_name)
    {

        if (!empty($file[$nama]['name'])) {
            $saved_file = $this->request->getFile($nama);
            $newName = $saved_file->getRandomName();
            $upload_path = FCPATH . 'uploads/iklan/thumbnails';
            $moved = $saved_file->move($upload_path, $newName);
            if ($moved) {
                $foto = $newName;
            } else {
                $foto = "";
            }
        } else {
            $foto = "";
        }
        return $foto;
    }

    public function update_upload_foto($file, $nama, $file_lama, $new_name)
    {
        if (!empty($file[$nama]['name'])) {
            if ($file_lama != '' && $file_lama != null) {
                //hapus file lama
                if (file_exists('./uploads/iklan/thumbnails/' . $file_lama)) {
                    unlink('./uploads/iklan/thumbnails/' . $file_lama);
                };
            }

            $saved_file = $this->request->getFile($nama);
            $newName = $saved_file->getRandomName();
            $upload_path = FCPATH . 'uploads/iklan/thumbnails';
            $moved = $saved_file->move($upload_path, $newName);
            if ($moved) {
                $foto = $newName;
            } else {
                $foto = $file_lama;
            }
        } else {
            $foto = $file_lama;
        }

        return $foto;
    }
}
