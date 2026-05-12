<?php

namespace App\Controllers;

class DokumenMasuk extends BaseController
{

    public $menuaktif = 'Dokumen Masuk';

    public function index()
    {
        $data['menuaktif'] = $this->menuaktif;
        return view('dokumenmasuk/index', $data);
    }

    public function tambah()
    {
        $data['ltambah'] = "1";
        $data['id'] = "";
        $data['menuaktif'] = $this->menuaktif;
        return view('dokumenmasuk/inputdata', $data);
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
            return redirect()->to('dokumen-masuk');
            exit();
        };

        $data['ltambah'] = "0";
        $data['id'] = $id;
        $data['menuaktif'] = $this->menuaktif;
        return view('dokumenmasuk/inputdata', $data);
    }

    public function datatablesource()
    {
        $RsData = $this->dokumen_model->get_datatables('masuk');
        $no = $this->request->getPost('start');
        $data = array();

        if ($RsData->getNumRows() > 0) {
            foreach ($RsData->getResult() as $rowdata) {

                $no++;
                $row = array();

                // 1. Tentukan sumber URL File (Google Drive vs Server Lokal)
                if (!empty($rowdata->kode_file)) {
                    // Jika ada kode_file, gunakan link preview Google Drive
                    $urlFile = "https://drive.google.com/file/d/" . $rowdata->kode_file . "/preview";
                } else {
                    // Jika kosong, gunakan path server lokal
                    $urlFile = site_url('uploads/arsip/thumbnails/' . $rowdata->file);
                }

                // Checkbox
                $row[] = '<input type="checkbox" class="check-item" name="id[]" value="' . encrypt($rowdata->id) . '">';

                // Tanggal
                $row[] = date('d-m-Y', strtotime($rowdata->created_at));

                // Bagian Nama File / Link Viewer
                // Menggunakan id="cetak-pdf" agar sinkron dengan script Javascript viewer Anda
                $row[] = '<a id="cetak-pdf" 
                     data-cetak_pdf="' . $urlFile . '" 
                     data-toggle="modal" 
                     data-target="#modalcetakpdf" 
                     href="javascript:void(0)" 
                     class="font-weight-bold">
                     <i class="fa ' . (!empty($rowdata->kode_file) ? 'fa-google text-success' : 'fa-file-alt text-info') . ' mr-1"></i> ' . $rowdata->nama_file . '
                  </a>';

                // Nama Pengirim
                $row[] = $rowdata->nama_pengirim;

                // Dropdown Opsi
                $row[] =
                    '<div class="dropdown custom-dropdown dropleft">
                <a class="badge btn-info" href="#" role="button" id="dropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-three-dots"></i>
                </a>
                <div class="dropdown-menu" >
                    <a class="dropdown-item ml-2" href="' . site_url('dokumen-masuk/edit/' . encrypt($rowdata->id)) . '" >Edit</a>
                    <a class="dropdown-item ml-2" href="' . site_url('dokumen-masuk/delete/' . encrypt($rowdata->id)) . '" id="hapus">Delete</a>
                </div>
            </div>';

                $data[] = $row;
            }
        }

        $output = array(
            "draw" => $this->request->getPost('draw'),
            "recordsTotal" => $this->dokumen_model->count_all(),
            "recordsFiltered" => $this->dokumen_model->count_filtered('masuk'),
            "data" => $data,
        );

        //output to json format
        return $this->response->setJSON($output);
    }

    public function delete($id)
    {
        // $id = decrypt($id); // Gunakan jika ID dikirim dalam bentuk terenkripsi
        $rsData = $this->dokumen_model->get_by_id($id);

        if ($rsData->getNumRows() < 1) {
            $pesan = '<div><div class="alert alert-danger"><strong>Ilegal!</strong> Data tidak ditemukan!</div></div>';
            $this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('dokumen-masuk');
        }

        $row = $rsData->getRow();
        $kode_file = $row->kode_file;
        $nama_fisik = $row->file;

        // Eksekusi hapus di database dulu (untuk keamanan integritas data)
        $hapus = $this->dokumen_model->hapus($id);

        if ($hapus) {
            // --- LOGIKA HAPUS FILE ---
            if (!empty($kode_file)) {
                // Jika ada kode_file, hapus dari Google Drive
                $this->_deleteFromGoogleDrive($kode_file);
            } else {
                // Jika kosong, hapus dari server lokal
                $pathLokal = FCPATH . 'uploads/arsip/thumbnails/' . $nama_fisik;
                if (file_exists($pathLokal) && !empty($nama_fisik)) {
                    unlink($pathLokal);
                }
            }

            $pesan = '<div><div class="alert alert-success"><strong>Berhasil.</strong> Data dan file telah dihapus</div></div>';
        } else {
            $pesan = '<div><div class="alert alert-danger"><strong>Gagal.</strong> Dokumen sudah digunakan pada jurnal/transaksi lain.</div></div>';
        }

        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('dokumen-masuk');
    }

    public function deleteAll()
    {
        $ids = $this->request->getPost('id');
        $dataBerhasil = 0;
        $dataGagal = 0;

        if ($ids) {
            foreach ($ids as $idEncrypted) {
                $id = decrypt($idEncrypted); // Sesuaikan dengan cara Anda mengirim ID dari view

                $rsData = $this->dokumen_model->get_by_id($id);
                if ($rsData->getNumRows() > 0) {
                    $row = $rsData->getRow();
                    $kode_file = $row->kode_file;
                    $nama_fisik = $row->file;

                    // Coba hapus database
                    if ($this->dokumen_model->hapus($id)) {
                        // Jika database berhasil dihapus, baru hapus filenya
                        if (!empty($kode_file)) {
                            $this->_deleteFromGoogleDrive($kode_file);
                        } else {
                            $pathLokal = FCPATH . 'uploads/arsip/thumbnails/' . $nama_fisik;
                            if (file_exists($pathLokal) && !empty($nama_fisik)) {
                                unlink($pathLokal);
                            }
                        }
                        $dataBerhasil++;
                    } else {
                        $dataGagal++;
                    }
                }
            }
        }

        $pesan = '<div><div class="alert alert-success"><strong>Berhasil.</strong> Data dihapus: ' . $dataBerhasil . ', Gagal: ' . $dataGagal . '</div></div>';
        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('dokumen-masuk');
    }

    public function simpan()
    {
        $request      = $this->request;
        $id           = $request->getPost('id');
        $idperusahaan = $request->getPost('idperusahaan');
        $ltambah      = $request->getPost('ltambah');
        $fileUpload   = $request->getFile('file');

        $file_lama      = $request->getPost('file_lama');
        $kode_file_lama = $request->getPost('kode_file_lama'); // ID Drive lama

        $simpan = false;
        $newName = $file_lama;
        $newKodeDrive = $kode_file_lama;

        // 1. PROSES UPLOAD (Jika ada file baru yang diunggah)
        if ($fileUpload && $fileUpload->isValid() && !$fileUpload->hasMoved()) {

            $newName  = $fileUpload->getRandomName();
            $tempPath = $fileUpload->getTempName();
            $isImage  = ($fileUpload->guessExtension() !== 'pdf');

            try {
                // Logika Resizing jika Gambar (seperti permintaan sebelumnya)
                if ($isImage) {
                    $resizedPath = WRITEPATH . 'uploads/' . $newName;
                    $this->image->withFile($tempPath)
                        ->resize(1012, 1012, true, 'auto')
                        ->save($resizedPath, 80);
                    $tempPath = $resizedPath;
                }

                // A. Upload file baru ke Google Drive
                $newKodeDrive = $this->_uploadToGoogleDrive($tempPath, $newName, $idperusahaan);

                // B. Hapus file lama jika ini adalah proses UPDATE
                if ($ltambah != '1') {
                    // Hapus file di Google Drive
                    if (!empty($kode_file_lama)) {
                        $this->_deleteFromGoogleDrive($kode_file_lama);
                    }
                    // Hapus file fisik di server (jika masih ada sisa file lama)
                    if (!empty($file_lama)) {
                        $pathLokal = FCPATH . 'uploads/arsip/thumbnails/' . $file_lama;
                        if (file_exists($pathLokal)) unlink($pathLokal);
                    }
                }

                // Bersihkan file temporary
                if ($isImage && file_exists($tempPath)) unlink($tempPath);
            } catch (\Exception $e) {
                $this->session->setFlashdata('pesan', '<div class="alert alert-danger">Gagal ke Cloud: ' . $e->getMessage() . '</div>');
                return redirect()->to('dokumen-masuk');
            }
        }

        // 2. PERSIAPAN DATA DATABASE
        $data = [
            'idperusahaan'  => $idperusahaan,
            'nama_pengirim' => $request->getPost('nama_pengirim'),
            'nama_file'     => $request->getPost('nama_file'),
            'file'          => $newName,
            'kode_file'     => $newKodeDrive, // ID Drive Baru/Lama
            'status'        => $request->getPost('status'),
        ];

        // 3. EKSEKUSI DATABASE
        if ($ltambah == '1') {
            $simpan = $this->dokumen_model->simpan($data);
        } else {
            $simpan = $this->dokumen_model->updateWhere($data, encrypt($id));
        }

        // 4. RESPONS ALERT
        if ($simpan) {
            $pesan = '<div><div class="alert alert-success alert-dismissable"><strong>Berhasil.</strong> Dokumen telah tersimpan di Cloud Storage.</div></div>';
        } else {
            $eror = $this->db->error();
            $pesan = '<div><div class="alert alert-danger alert-dismissable"><strong>Gagal!</strong> Database error: ' . $eror['message'] . '</div></div>';
        }

        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('dokumen-masuk');
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
            'kode_file' =>  $RsData->kode_file,
            'status' =>  $RsData->status,
            'nama_pengirim'         => $RsData->nama_pengirim,

        );
        return $this->response->setJSON($data);
    }

    public function viewfile($id = null)
    {
        echo '<iframe style="height:100%;width:100%;" src="' . base_url('uploads/arsip/thumbnails/' . $id) . '" title="description"></iframe>';
    }

    public function viewfilejurnal($id = null)
    {
        echo '<iframe style="height:100%;width:100%;" src="' . base_url('uploads/jurnal/thumbnails/' . $id) . '" title="description"></iframe>';
    }

    public function tambahArsip($id)
    {
        $datas = $this->dokumen_model->get_by_idperusahaan($id);
        if ($datas->getNumRows() < 1) {
            $pesan = '<div>
        				<div class="alert alert-danger alert-dismissable">
        	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        	                <strong>Ilegal!</strong> Data tidak ditemukan! 
        			    </div>
        			</div>';
            $this->session->setFlashdata('pesan', $pesan);
            return redirect()->to('/');
            exit();
        };
        $data['idperusahaan'] = $datas->getRow()->idperusahaan;
        $data['namaperusahaan'] = $datas->getRow()->namaperusahaan;
        return view('dokumenmasuk/inputdataarsip', $data);
    }

    public function simpanArsip()
    {
        $request      = $this->request;
        $idperusahaan = $request->getPost('idperusahaan');
        $ltambah      = $request->getPost('ltambah');
        $fileUpload   = $request->getFile('file');

        // Inisialisasi variabel simpan
        $simpan = false;

        if ($ltambah == '1') { // Kondisi tambah data
            if ($fileUpload && $fileUpload->isValid() && !$fileUpload->hasMoved()) {

                $newName  = $fileUpload->getRandomName();
                $tempPath = $fileUpload->getTempName(); // Path sementara di server
                $isImage  = ($fileUpload->guessExtension() !== 'pdf');

                try {
                    // 1. Logika Resizing jika file adalah Gambar (seperti kode lama Anda)
                    if ($isImage) {
                        // Simpan sementara di folder writable CI4 untuk di-resize
                        $resizedPath = WRITEPATH . 'uploads/' . $newName;
                        $this->image->withFile($tempPath)
                            ->resize(1012, 1012, true, 'auto')
                            ->save($resizedPath, 80);

                        $tempPath = $resizedPath; // Gunakan file yang sudah diperkecil untuk upload ke Drive
                    }

                    // 2. Upload ke Google Drive
                    // Parameter: Path file, Nama file di Drive, Nama Folder (ID Perusahaan)
                    $gdriveFileId = $this->_uploadToGoogleDrive($tempPath, $newName, $idperusahaan);

                    // 3. Bersihkan file lokal segera setelah upload ke Drive berhasil
                    if ($isImage && file_exists($tempPath)) {
                        unlink($tempPath);
                    }

                    // 4. Siapkan data untuk Database
                    $data = [
                        'idperusahaan'  => $idperusahaan,
                        'nama_pengirim' => $request->getPost('nama_pengirim'),
                        'nama_file'     => $request->getPost('nama_file'),
                        'file'          => $newName,      // Nama file asli (fisik)
                        'kode_file'     => $gdriveFileId, // ID unik dari Google Drive
                        'status'        => $request->getPost('status'),
                    ];

                    $simpan = $this->dokumen_model->simpan($data);
                } catch (\Exception $e) {
                    // Tangkap error jika API Google Drive bermasalah
                    $this->session->setFlashdata('pesan', '<div class="alert alert-danger">Error Google Drive: ' . $e->getMessage() . '</div>');
                    return redirect()->to('dokumen-arsip/' . encrypt($idperusahaan));
                }
            }
        }

        // Penanganan Respon Alert
        if ($simpan) {
            $pesan = '<div>
                    <div class="alert alert-success alert-dismissable">
                        <strong>Berhasil.</strong> Data telah disimpan di Google Drive.
                    </div>
                </div>';
        } else {
            $eror  = $this->db->error();
            $pesan = '<div>
                    <div class="alert alert-danger alert-dismissable">
                        <strong>Gagal!</strong> Data gagal disimpan! <br>
                        Pesan Error : ' . $eror['code'] . ' ' . $eror['message'] . '
                    </div>
                </div>';
        }

        $this->session->setFlashdata('pesan', $pesan);
        return redirect()->to('dokumen-arsip/' . encrypt($idperusahaan));
    }
    private function _uploadToGoogleDrive($filePath, $fileName, $folderName)
    {
        $client = new \Google\Client();

        // 1. Kredensial & Scope Akses
        $client->setAuthConfig(APPPATH . 'ThirdParty/oauth-credentials.json');
        $client->addScope(\Google\Service\Drive::DRIVE);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // 2. Load Token JSON
        $tokenPath = WRITEPATH . 'google-token-admin.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        } else {
            throw new \Exception("File token.json tidak ditemukan. Harap generate ulang token.");
        }

        // 3. Refresh Token Otomatis jika Expired
        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            } else {
                throw new \Exception("Token expired dan Refresh Token tidak tersedia. Harap hapus token.json dan otorisasi ulang.");
            }
        }

        $driveService = new \Google\Service\Drive($client);

        // ==============================================================
        // Logika Upload, Pembuatan Folder, & Dukungan Shared Drive
        // ==============================================================

        // ID Folder Utama tempat menampung seluruh folder perusahaan
        $parentFolderId = env('IDARSIP'); // Ganti dengan ID Folder Utama Anda
        $childFolderId  = null; // ID folder perusahaan (anak)

        // Cek apakah folder perusahaan sudah ada di dalam Folder Utama
        $query = "mimeType='application/vnd.google-apps.folder' and name='{$folderName}' and '{$parentFolderId}' in parents and trashed=false";

        // Parameter pencarian (Ditambah parameter Shared Drive untuk menghindari error 404)
        $searchParams = [
            'q' => $query,
            'fields' => 'files(id, name)',
            'supportsAllDrives' => true,
            'includeItemsFromAllDrives' => true
        ];
        $results = $driveService->files->listFiles($searchParams);

        if (count($results->getFiles()) == 0) {
            // JIKA BELUM ADA: Buat folder perusahaan baru di dalam Folder Utama
            $folderMetadata = new \Google\Service\Drive\DriveFile([
                'name'     => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents'  => [$parentFolderId]
            ]);

            $folder = $driveService->files->create($folderMetadata, [
                'fields' => 'id',
                'supportsAllDrives' => true
            ]);
            $childFolderId = $folder->id;
        } else {
            // JIKA SUDAH ADA: Ambil ID folder perusahaan tersebut
            $childFolderId = $results->getFiles()[0]->id;
        }

        // Upload file ke dalam folder perusahaan yang bersangkutan
        $fileMetadata = new \Google\Service\Drive\DriveFile([
            'name'    => $fileName,
            'parents' => [$childFolderId]
        ]);

        $content = file_get_contents($filePath);
        $uploadedFile = $driveService->files->create($fileMetadata, [
            'data'       => $content,
            'mimeType'   => mime_content_type($filePath),
            'uploadType' => 'multipart',
            'fields'     => 'id',
            'supportsAllDrives' => true // Memastikan bisa upload ke folder Shared Drive
        ]);

        return $uploadedFile->id;
    }

    private function _deleteFromGoogleDrive($fileId)
    {
        try {
            $client = new \Google\Client();
            $client->setAuthConfig(APPPATH . 'ThirdParty/oauth-credentials.json'); // Gunakan file Kredensial OAuth Anda
            $client->addScope(\Google\Service\Drive::DRIVE_FILE);

            // Load Token OAuth
            $tokenPath = WRITEPATH . 'google-token-admin.json';
            if (file_exists($tokenPath)) {
                $accessToken = json_decode(file_get_contents($tokenPath), true);
                $client->setAccessToken($accessToken);
            }

            // Auto Refresh Token jika expired
            if ($client->isAccessTokenExpired() && $client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            }

            $driveService = new \Google\Service\Drive($client);

            // Perintah untuk menghapus/memindahkan file ke Trash Google Drive
            $driveService->files->delete($fileId);

            return true;
        } catch (\Exception $e) {
            // Jika file sudah tidak ada di Drive (atau error lain), biarkan berlalu agar proses DB tetap jalan
            return false;
        }
    }
}
