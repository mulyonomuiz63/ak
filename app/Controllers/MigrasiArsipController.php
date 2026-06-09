<?php

namespace App\Controllers;

use Config\Services;

class MigrasiArsipController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->image = Services::image();
    }

    public function index()
    {
        $data = [
            'title' => 'Migrasi File ke Google Drive',
        ];
        return view('migrasi-arsip/index', $data);
    }

    /**
     * Source Data untuk DataTable
     */
    public function datatablesource()
    {
        $request = Services::request();
        $idperusahaan = $request->getPost('idperusahaan');
        $status = $request->getPost('status');
        $searchValue = $request->getPost('search')['value'];

        $builder = $this->db->table('arsip');

        // 1. Terapkan Filter Tambahan (idperusahaan & status)
        if (!empty($idperusahaan)) {
            $builder->where('arsip.idperusahaan', $idperusahaan);
        }

        if (!empty($status)) {
            if ($status === 'null') {
                $builder->groupStart()
                    ->where('arsip.kode_file IS NULL')
                    ->orWhere('arsip.kode_file', '')
                    ->groupEnd();
            } elseif ($status === 'true') {
                $builder->groupStart()
                    ->where('arsip.kode_file IS NOT NULL')
                    ->where('arsip.kode_file !=', '')
                    ->groupEnd();
            }
        }

        // 2. Terapkan Filter Pencarian Utama (Search Datatables)
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('arsip.nama_pengirim', $searchValue)
                ->orLike('arsip.file', $searchValue) // Gunakan orLike untuk kondisi ATAU
                ->groupEnd();
        }

        // 3. Hitung Data Setelah Difilter (Penting: Parameter 'false' agar query tidak kereset)
        $totalFiltered = $builder->countAllResults(false);

        // 4. Hitung Total Keseluruhan (Opsional tapi disarankan untuk recordsTotal)
        // $totalData = $this->db->table('arsip')->countAllResults(); 
        // Untuk efisiensi sementara, kita bisa gunakan $totalFiltered saja jika tidak perlu menunjuk total absolut.

        // 5. Terapkan Limit dan Offset untuk Pagination
        $limit = $request->getPost('length');
        $start = $request->getPost('start');

        if ($limit != -1) {
            $builder->limit($limit, $start);
        }

        $RsData = $builder->get();
        $data = [];

        foreach ($RsData->getResult() as $rowdata) {
            // [Logika foreach Anda tetap sama persis seperti sebelumnya]
            if (empty($rowdata->file)) {
                $size = '<span class="text-muted">Tidak Ada File</span>';
                $path = null;
            } else {
                $path_arsip  = FCPATH . 'uploads/arsip/thumbnails/' . $rowdata->file;
                if (file_exists($path_arsip)) {
                    $size = round(filesize($path_arsip) / 1024, 2) . ' KB';
                } else {
                    $size = '<span class="text-danger">File Hilang</span>';
                }
            }

            if (!empty($rowdata->kode_file)) {
                $linkViewer  = "https://drive.google.com/file/d/" . $rowdata->kode_file . "/preview";
                $statusBadge = '<span class="badge badge-success px-2 py-1"><i class="fab fa-google-drive mr-1"></i> Sudah Terupload</span>';
                $iconFile    = 'fa-google text-success';
            } else {
                $linkViewer  = site_url('uploads/arsip/thumbnails/' . $rowdata->file);
                $statusBadge = '<span class="badge badge-warning px-2 py-1"><i class="fas fa-server mr-1"></i> Belum Terupload</span>';
                $iconFile    = 'fa-file-alt text-info';
            }

            $row = [];

            if (!empty($rowdata->kode_file)) {
                $row[] = '<input type="checkbox" disabled title="File sudah dimigrasi ke Google Drive">';
            } elseif (empty($rowdata->file) || !is_file($path_arsip)) {
                $row[] = '<input type="checkbox" disabled title="File fisik tidak ditemukan di server lokal">';
            } else {
                $row[] = '<input type="checkbox" class="check-item" name="id[]" data-idperusahaan="' . $rowdata->idperusahaan . '"  value="' . $rowdata->id . '">';
            }

            $row[] = '<a id="cetak-pdf" 
                 data-cetak_pdf="' . $linkViewer . '" 
                 data-toggle="modal" 
                 data-target="#modalcetakpdf" 
                 href="javascript:void(0)"  
                 class="font-weight-bold text-wrap">
                 <i class="fa ' . $iconFile . ' mr-1"></i> ' . $rowdata->file . '
              </a>';

            $row[] = $size;
            $row[] = $statusBadge;

            $data[] = $row;
        }

        $output = [
            "draw"            => intval($request->getPost('draw')),
            "recordsTotal"    => $totalFiltered, // Ubah jika Anda menghitung $totalData secara terpisah
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        ];

        return $this->response->setJSON($output);
    }

    public function autocomplatePerusahaan()
    {
        $cari = $this->request->getPost('term');
        $query = "SELECT * FROM perusahaan WHERE namaperusahaan like '%" . $cari . "%' order by namaperusahaan asc limit 10";
        $res = $this->db->query($query);
        $result = array();
        foreach ($res->getResult() as $row) {
            array_push($result, array(
                'idperusahaan' => $row->idperusahaan,
                'namaperusahaan' => $row->namaperusahaan,
            ));
        }
        return $this->response->setJSON($result);
    }

    /**
     * Proses Migrasi File
     */
    public function prosesUpload()
    {
        $id           = $this->request->getPost('id');
        $idperusahaan = $this->request->getPost('idperusahaan');

        if (empty($id)) {
            return $this->response->setJSON(['status' => false, 'msg' => 'Data tidak valid.']);
        }


        // Ambil data satu file dari database
        $fileData = $this->db->table('arsip')->where('id', $id)->get()->getRowArray();

        if (!$fileData) {
            return $this->response->setJSON(['status' => false, 'msg' => 'File tidak ditemukan di database.']);
        }

        $path_arsip  = FCPATH . 'uploads/arsip/thumbnails/' . $fileData['file'];

        // 1. Tentukan path mana yang valid (ada filenya)
        $validPath = false;
        if (file_exists($path_arsip)) {
            $validPath = $path_arsip;
        }

        // 2. Jika file ditemukan di salah satu folder, lanjutkan proses GDrive dan DB
        if ($validPath) {
            try {
                // Hapus file lama jika ada (tidak perlu rollback jika ini gagal)
                if (!empty($fileData['kode_file'])) {
                    try {
                        $this->_deleteFromGoogleDrive($fileData['kode_file']);
                    } catch (\Exception $e) {
                        // Abaikan jika gagal hapus file lama
                    }
                }

                // Upload file baru dari path yang valid ke GDrive
                $gdriveId = $this->_uploadToGoogleDrive($validPath, $fileData['file'], $idperusahaan);

                if ($gdriveId) {
                    // Mulai Transaksi Database
                    $this->db->transBegin();

                    try {
                        // Update tabel arsip
                        $this->db->table('arsip')
                            ->where('id', $id)
                            ->update(['kode_file' => $gdriveId]);
                        // Cek apakah ada error database selama proses
                        if ($this->db->transStatus() === false) {
                            throw new \Exception("Gagal menyimpan ke database.");
                        }

                        $this->db->transCommit();
                        return $this->response->setJSON(['status' => true, 'msg' => 'Sukses']);
                    } catch (\Exception $e) {
                        $this->db->transRollback();

                        // --- ANTISIPASI PENTING ---
                        // Jika database gagal, hapus file yang sudah terlanjur terupload di GDrive
                        $this->_deleteFromGoogleDrive($gdriveId);

                        throw new \Exception("Database error: " . $e->getMessage());
                    }
                } else {
                    return $this->response->setJSON(['status' => false, 'msg' => 'Gagal mengupload file ke Google Drive.']);
                }
            } catch (\Exception $e) {
                return $this->response->setJSON(['status' => false, 'msg' => $e->getMessage()]);
            }
        }

        // 3. Kondisi jika file tidak ditemukan di kedua folder lokal
        return $this->response->setJSON(['status' => false, 'msg' => 'File fisik tidak ditemukan di folder arsip maupun Arsip.']);
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
