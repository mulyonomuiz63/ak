<?php

namespace App\Controllers;

use Config\Services;

class MigrasiFileUtamaController extends BaseController
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
        return view('migrasi/fileUtama/index', $data);
    }

    /**
     * Source Data untuk DataTable
     */
    public function datatablesource()
    {
        $request = Services::request();
        $idperusahaan = $request->getPost('idperusahaan');
        $tahun = $request->getPost('tahun');
        $bulan = $request->getPost('bulan');
        $status = $request->getPost('status');

        $builder = $this->db->table('v_jurnal');

        if (!empty($idperusahaan)) {
            $builder->where('v_jurnal.idperusahaan', $idperusahaan);
        }

        if (!empty($tahun)) {
            $builder->where("YEAR(v_jurnal.tgljurnal)", $tahun);
        }

        if (!empty($bulan)) {
            $builder->where("MONTH(v_jurnal.tgljurnal)", $bulan);
        }

        if (!empty($status)) {
            if ($status === 'null') {
                // BELUM MIGRASI: 
                // Syarat 1: File lokalnya HARUS ADA (filelampiran tidak kosong)
                // Syarat 2: Belum masuk ke GDrive (kode_file masih kosong)
                $builder->groupStart()
                    ->where('v_jurnal.filelampiran IS NOT NULL')
                    ->where('v_jurnal.filelampiran !=', '')

                    ->groupStart()
                    ->where('v_jurnal.kode_file IS NULL')
                    ->orWhere('v_jurnal.kode_file', '')
                    ->groupEnd()

                    ->groupEnd();
            } elseif ($status === 'true') {
                // SUDAH MIGRASI: kode_file HARUS ADA
                $builder->groupStart()
                    ->where('v_jurnal.kode_file IS NOT NULL')
                    ->where('v_jurnal.kode_file !=', '')
                    ->groupEnd();
            }
        }

        // Logika Server-Side Datatables (Simple version)
        $searchValue = $request->getPost('search')['value'];
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('v_jurnal.idjurnal', $searchValue)
                ->orLike('v_jurnal.kode_file', $searchValue)
                ->groupEnd();
        }
        $totalFiltered = $builder->countAllResults(false);
        $limit = $request->getPost('length'); // Mengambil angka 10 dari pageLength JS
        $start = $request->getPost('start');  // Mengambil posisi halaman saat ini (offset)

        if ($limit != -1) { // -1 artinya jika user memilih "Tampilkan Semua (All)"
            $builder->limit($limit, $start);
        }
        $RsData = $builder->get();
        $data = [];

        foreach ($RsData->getResult() as $rowdata) {
            // Cek dulu apakah nama file di database kosong atau null
            if (empty($rowdata->filelampiran)) {
                $size = '<span class="text-muted">Tidak Ada File</span>';
                $path = null; // Tidak ada file fisik untuk dicek
            } else {
                $path = FCPATH . 'uploads/jurnal/' . $rowdata->filelampiran;

                // is_file() memastikan bahwa path tersebut adalah file sungguhan, bukan folder
                $size = is_file($path) ? round(filesize($path) / 1024, 2) . ' KB' : '<span class="text-danger">File Hilang</span>';
            }

            // 1. Logika Pengecekan Google Drive (Link Viewer & Status)
            if (!empty($rowdata->kode_file)) {
                // Jika kode_file ada isinya -> Tampilkan dari Drive
                $linkViewer  = "https://drive.google.com/file/d/" . $rowdata->kode_file . "/preview";
                $statusBadge = '<span class="badge badge-success px-2 py-1"><i class="fab fa-google-drive mr-1"></i> Sudah Terupload</span>';
                $iconFile    = 'fa-google text-success';
            } else {
                // Jika kode_file kosong -> Tampilkan dari Server Lokal
                $linkViewer  = site_url('uploads/jurnal/' . $rowdata->filelampiran);
                $statusBadge = '<span class="badge badge-warning px-2 py-1"><i class="fas fa-server mr-1"></i> Belum Terupload</span>';
                $iconFile    = 'fa-file-alt text-info';
            }


            $row = [];

            if (!empty($rowdata->kode_file)) {
                $row[] = '<input type="checkbox" disabled title="File sudah dimigrasi ke Google Drive">';
            }elseif (empty($rowdata->filelampiran) || !is_file($path)) {
                $row[] = '<input type="checkbox" disabled title="File fisik tidak ditemukan di server lokal">';
            }else {
                $row[] = '<input type="checkbox" class="check-item" name="idjurnal[]" data-idperusahaan="' . $rowdata->idperusahaan . '" data-tgljurnal="' . $rowdata->tgljurnal . '" value="' . $rowdata->idjurnal . '">';
            }

            // [1] Tanggal
            $row[] = date('d-m-Y', strtotime($rowdata->tgljurnal));

            // [2] ID / No Jurnal
            $row[] = $rowdata->idjurnal;

            // [3] Nama File (Clickable memanggil modal viewer)
            $row[] = '<a id="cetak-pdf" 
                 data-cetak_pdf="' . $linkViewer . '" 
                 data-toggle="modal" 
                 data-target="#modalcetakpdf" 
                 href="javascript:void(0)" 
                 class="font-weight-bold text-wrap">
                 <i class="fa ' . $iconFile . ' mr-1"></i> ' . $rowdata->filelampiran . '
              </a>';

            // [4] Ukuran File
            $row[] = $size;

            // [5] Status (Kolom Baru)
            $row[] = $statusBadge;

            $data[] = $row;
        }

        $output = [
            "draw"            => intval($request->getPost('draw')),
            "recordsTotal"    => $totalFiltered, // Beri tahu DataTables total aslinya
            "recordsFiltered" => $totalFiltered, // Beri tahu DataTables total aslinya
            "data"            => $data           // Data 10 barisnya
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
        $id           = $this->request->getPost('idjurnal');
        $idperusahaan = $this->request->getPost('idperusahaan');
        $tgljurnal    = $this->request->getPost('tgljurnal');

        if (empty($id)) {
            return $this->response->setJSON(['status' => false, 'msg' => 'Data tidak valid.']);
        }

        $tahunBulan = date('Ym', strtotime($tgljurnal));

        // Ambil data satu file dari database
        $fileData = $this->db->table('jurnal')->where('idjurnal', $id)->get()->getRowArray();

        if (!$fileData) {
            return $this->response->setJSON(['status' => false, 'msg' => 'File tidak ditemukan di database.']);
        }

        $filePath = FCPATH . 'uploads/jurnal/' . $fileData['filelampiran']; // Path file di server lokal

        if (file_exists($filePath)) {
            try {
                // 1. Hapus file lama jika ada (tidak perlu rollback jika ini gagal)
                if (!empty($fileData['kode_file'])) {
                    try {
                        $this->_deleteFromGoogleDrive($fileData['kode_file']);
                    } catch (\Exception $e) {
                    }
                }

                // 2. Upload file baru ke GDrive
                $gdriveId = $this->_uploadToGoogleDrive($filePath, $fileData['filelampiran'], $idperusahaan, $tahunBulan);

                if ($gdriveId) {
                    // Mulai Transaksi Database
                    $this->db->transBegin();

                    try {
                        // Update tabel jurnal
                        $this->db->table('jurnal')
                            ->where('idjurnal', $id)
                            ->update(['kode_file' => $gdriveId]);

                        // Simpan ke tabel pendukung

                        $this->jurnal_model->simpanfile([
                            'idjurnal'  => $id,
                            'nama_file' => 'Lampiran Utama',
                            'kode_file' => $gdriveId
                        ]);

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
                }
            } catch (\Exception $e) {
                return $this->response->setJSON(['status' => false, 'msg' => $e->getMessage()]);
            }
        }

        return $this->response->setJSON(['status' => false, 'msg' => 'File fisik sudah tidak ada di server.']);
    }

    /**
     * Pastikan fungsi _uploadToGoogleDrive ada di sini 
     * atau dipanggil dari helper/BaseController
     */
    private function _uploadToGoogleDrive($filePath, $fileName, $folderPerusahaan, $folderBulan)
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
        // Logika Pembuatan Folder Bertingkat & Dukungan Shared Drive
        // ==============================================================

        // ID Folder Utama (Root) tempat menampung seluruh folder perusahaan
        $parentFolderId     = env('IDJURNAL');
        $perusahaanFolderId = null; // Penampung ID folder Level 1
        $bulanFolderId      = null; // Penampung ID folder Level 2

        // -------------------------------------------------------------
        // TAHAP 1: Cari atau Buat Folder PERUSAHAAN (Level 1)
        // -------------------------------------------------------------
        $query1 = "mimeType='application/vnd.google-apps.folder' and name='{$folderPerusahaan}' and '{$parentFolderId}' in parents and trashed=false";
        $searchParams1 = [
            'q' => $query1,
            'fields' => 'files(id, name)',
            'supportsAllDrives' => true,
            'includeItemsFromAllDrives' => true
        ];
        $results1 = $driveService->files->listFiles($searchParams1);

        if (count($results1->getFiles()) == 0) {
            $folderMetadata1 = new \Google\Service\Drive\DriveFile([
                'name'     => $folderPerusahaan,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents'  => [$parentFolderId]
            ]);
            $folder1 = $driveService->files->create($folderMetadata1, ['fields' => 'id', 'supportsAllDrives' => true]);
            $perusahaanFolderId = $folder1->id;
        } else {
            $perusahaanFolderId = $results1->getFiles()[0]->id;
        }

        // -------------------------------------------------------------
        // TAHAP 2: Cari atau Buat Folder TAHUN-BULAN (Level 2)
        // -------------------------------------------------------------
        // Mencari di dalam $perusahaanFolderId, bukan lagi di $parentFolderId
        $query2 = "mimeType='application/vnd.google-apps.folder' and name='{$folderBulan}' and '{$perusahaanFolderId}' in parents and trashed=false";
        $searchParams2 = [
            'q' => $query2,
            'fields' => 'files(id, name)',
            'supportsAllDrives' => true,
            'includeItemsFromAllDrives' => true
        ];
        $results2 = $driveService->files->listFiles($searchParams2);

        if (count($results2->getFiles()) == 0) {
            $folderMetadata2 = new \Google\Service\Drive\DriveFile([
                'name'     => $folderBulan,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents'  => [$perusahaanFolderId]
            ]);
            $folder2 = $driveService->files->create($folderMetadata2, ['fields' => 'id', 'supportsAllDrives' => true]);
            $bulanFolderId = $folder2->id;
        } else {
            $bulanFolderId = $results2->getFiles()[0]->id;
        }

        // -------------------------------------------------------------
        // TAHAP 3: Upload File ke dalam Folder TAHUN-BULAN
        // -------------------------------------------------------------
        $fileMetadata = new \Google\Service\Drive\DriveFile([
            'name'    => $fileName,
            'parents' => [$bulanFolderId] // File dimasukkan ke folder Level 2
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
