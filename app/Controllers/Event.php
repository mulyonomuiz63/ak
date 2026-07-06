<?php

namespace App\Controllers;

class Event extends BaseController
{

    public $menuaktif = 'event';


    public function index()
    {
        cek_pengguna();
        $data['menuaktif'] = $this->menuaktif;
        return view('event/index', $data);
    }

    public function tambah()
    {
        if (!(session()->get('databaseHitPengguna') <= session()->get('hitPengguna'))) {
            return redirect()->to('event');
        }
        $data['idevent'] = "";
        $data['menuaktif'] = $this->menuaktif;
        return view('event/inputdata', $data);
    }

    public function edit($idevent)
    {
        if ($this->event_model->get_by_id($idevent)->getNumRows() < 1) {
            $pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Ilegal!</strong> Data tidak ditemukan 
					    </div>
					</div>';
            $this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('event');
        };

        $data['idevent'] = $idevent;
        $data['menuaktif'] = $this->menuaktif;
        return view('event/inputdata', $data);
    }

    public function datatablesource()
    {
        $RsData = $this->event_model->get_datatables();
        $no = $this->request->getPost('start');
        $data = array();

        if ($RsData->getNumRows() > 0) {
            foreach ($RsData->getResult() as $rowdata) {
                $no++;
                $row = array();
                // $row[] = $no;
                $row[] = '<input type="checkbox" class="check-item" name="idevent[]" value="' . encrypt($rowdata->idevent) . '">';
                $row[] = $rowdata->nama_event;
                $row[] = '<div class="d-flex justify-content-center"><img src="'.base_url('uploads/event/thumbnails/'.$rowdata->file) .'" class="d-block w-50 rounded" alt=""></div>';
                $row[] =
                    '<div class="dropdown custom-dropdown dropleft">
						<a class="badge btn-info" href="#" role="button" id="dropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="bi bi-three-dots"></i>
						</a>
						<div class="dropdown-menu" >
							<a class="dropdown-item" href="' . site_url('event/edit/' . encrypt($rowdata->idevent)) . '"  class="ml-2">Edit</a>
							<a class="dropdown-item" href="' . site_url('event/delete/' . encrypt($rowdata->idevent)) . '"  class="ml-2"  id="hapus">Delete</a>
						</div>
					</div>';
                $data[] = $row;
            }
        }

        $output = array(
            "draw" => $this->request->getPost('draw'),
            "recordsTotal" => $this->event_model->count_all(),
            "recordsFiltered" => $this->event_model->count_filtered(),
            "data" => $data,
        );

        //output to json format
        return $this->response->setJSON($output);
    }

    public function delete($id)
    {

        if ($this->event_model->get_by_id($id)->getNumRows() < 1) {
            $pesan = '<div>
						<div class="alert alert-danger alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
			                <strong>Ilegal!</strong> Data tidak ditemukan 
					    </div>
					</div>';
            $this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('event');
        };




        $file = $this->event_model->get_by_id($id)->getRow()->file;
        $hapus = $this->event_model->hapus($id);
        if ($hapus) {
            if ($file != '' && $file != null) {
                unlink('./uploads/event/thumbnails/' . $file);
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
        return redirect()->to('event');
    }

    public function deleteAll()
    {
        $dataEvent = $this->request->getPost('idevent');
        $dataBerhasil = 0;
        $dataGagal = 0;
        foreach ($dataEvent as $id) {




            $file = $this->event_model->get_by_id($id)->getRow()->file;
            $hapus = $this->event_model->hapus($id);
            if ($hapus) {
                if ($file != '' && $file != null) {
                    unlink('./uploads/event/thumbnails/' . $file);
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
        return redirect()->to('event');
    }

    public function simpan()
    {
 
        $idevent        = $this->request->getPost('idevent');
        $nama_event     = $this->request->getPost('nama_event');
        $file           = $this->request->getFile('file');
        $file_lama      = $this->request->getPost('file_lama');
        $deskripsi         = $this->request->getPost('deskripsi');
        $url            = $this->request->getPost('url');

        if (empty($idevent)) { // ini kondisi jika tambah data 

            if ($file->isValid()) {
                $newName = $file->getRandomName();
                // thumnail foto_ktp path
                $thumbnail_path = FCPATH . 'uploads/event/thumbnails';

                $path = FCPATH . 'uploads/event';
                if ($file->move($path, $newName)) {
                    // resizing newName
                    $this->image->withFile($path . '/' . $newName)
                        // ->resize(1012, 1012, true, 'auto') // maintain ratio, auto dimensi
                        ->save($thumbnail_path . '/' . $newName, 80); 

                    if (file_exists('./uploads/event/' . $newName)) {
                        unlink('./uploads/event/' . $newName);
                    };
                }
            }

            $data = array(
                'nama_event'    => $nama_event,
                'deskripsi'     => $deskripsi,
                'file'          => $newName,
                'url'           => $url,
            );
            $simpan = $this->event_model->simpan($data);
        } else { // ini kondisi jika edit data



            if ($file->isValid()) {
                $newName = $file->getRandomName();
                // thumnail foto_ktp path
                $thumbnail_path = FCPATH . 'uploads/event/thumbnails';

                $path = FCPATH . 'uploads/event';
                if ($file->move($path, $newName)) {
                    // resizing newName
                    $this->image->withFile($path . '/' . $newName)
                        // ->resize(1012, 1012, true, 'auto') // maintain ratio, auto dimensi
                        ->save($thumbnail_path . '/' . $newName, 80);

                    if (file_exists('./uploads/event/thumbnails/' . $file_lama)) {
                        unlink('./uploads/event/thumbnails/' . $file_lama);
                    };
                    if (file_exists('./uploads/event/' . $newName)) {
                        unlink('./uploads/event/' . $newName);
                    };
                }
            } else {
                $newName = $file_lama;
            }

            $data = array(
                'nama_event'    => $nama_event,
                'deskripsi'     => $deskripsi,
                'file'          => $newName,
                'url'           => $url,
            );

            $simpan = $this->event_model->updateWhere($data, $idevent);
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
        return redirect()->to('event');
    }

    public function get_edit_data()
    {
        $idevent = $this->request->getPost('idevent');
        $RsData = $this->event_model->get_by_id($idevent)->getRow();

        $data = array(
            'idevent' =>  $RsData->idevent,
            'nama_event' =>  $RsData->nama_event,
            'file' =>  $RsData->file,
            'url' =>  $RsData->url,
            'deskripsi' => $RsData->deskripsi,
        );
        echo (json_encode($data));
    }

    public function upload_foto($file, $nama, $new_name)
    {

        if (!empty($file[$nama]['name'])) {
            $saved_file = $this->request->getFile($nama);
            $newName = $saved_file->getRandomName();
            $upload_path = FCPATH . 'uploads/event/thumbnails';
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
                if (file_exists('./uploads/event/thumbnails/' . $file_lama)) {
                    unlink('./uploads/event/thumbnails/' . $file_lama);
                };
            }

            $saved_file = $this->request->getFile($nama);
            $newName = $saved_file->getRandomName();
            $upload_path = FCPATH . 'uploads/event/thumbnails';
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

    function resize_foto($data)
    {
        print_r($data);
        exit;
        $filePath = '';
        $saveto = './uploads/event/thumbnails/' . $data['file_name'];
        $image = \Config\Services::image();
        $image->withFile($filePath)->resize(128, 128, true)->save($saveto, 80);
    }
}
