<?php

namespace App\Controllers;

use Config\Services;

class MigrasiController extends BaseController
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
        return view('migrasi/index', $data);
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

        $builder = $this->db->table('jurnalfile')
            ->select('jurnalfile.*, v_jurnal.tgljurnal, v_jurnal.idjurnal, v_jurnal.idperusahaan')
            ->join('v_jurnal', 'v_jurnal.idjurnal = jurnalfile.idjurnal')
            ->where('jurnalfile.kode_file', null); // Berjaga-jaga jika string kosong

        if (!empty($idperusahaan)) {
            $builder->where('v_jurnal.idperusahaan', $idperusahaan);
        }

        if (!empty($tahun)) {
            $builder->where("YEAR(v_jurnal.tgljurnal)", $tahun);
        }

        if (!empty($bulan)) {
            $builder->where("MONTH(v_jurnal.tgljurnal)", $bulan);
        }

        // Logika Server-Side Datatables (Simple version)
        $i = 0;
        $RsData = $builder->get();
        $data = [];

        foreach ($RsData->getResult() as $rowdata) {
            $path = FCPATH . 'uploads/jurnal/thumbnails/' . $rowdata->file;
            $size = file_exists($path) ? round(filesize($path) / 1024, 2) . ' KB' : '<span class="text-danger">File Hilang</span>';

            $row = [];
            $row[] = '<input type="checkbox" class="check-item" name="id[]" data-idperusahaan="' . $rowdata->idperusahaan . '" data-tgljurnal="' . $rowdata->tgljurnal . '"value="' . $rowdata->id . '">';
            $row[] = date('d-m-Y', strtotime($rowdata->tgljurnal));
            $row[] = $rowdata->idjurnal;
            $row[] = $rowdata->nama_file ?? $rowdata->file;
            $row[] = $size;
            $data[] = $row;
        }

        $output = [
            "draw" => $request->getPost('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data,
        ];

        return $this->response->setJSON($output);
    }

    /**
     * Proses Migrasi File
     */
    public function prosesUpload()
    {
        // Tangkap data array dari AJAX
        $file_migrasi = $this->request->getPost('file_migrasi');

        if (empty($file_migrasi)) {
            return $this->response->setJSON(['status' => false, 'msg' => 'Tidak ada file dipilih.']);
        }

        $successCount = 0;
        $failCount = 0;

        // Looping data yang dikirim dari Javascript
        foreach ($file_migrasi as $row) {
            $id           = $row['id'];
            $idperusahaan = $row['idperusahaan'];
            $tgljurnal    = $row['tgljurnal'];

            // Format TahunBulan (Misal: 202605)
            $tahunBulan = date('Ym', strtotime($tgljurnal));

            // --- Mulai proses ambil database dan upload seperti biasa ---
            $fileData = $this->db->table('jurnalfile')->where('id', $id)->get()->getRowArray();

            if (!$fileData) {
                $failCount++;
                continue;
            }

            $filePath = FCPATH . 'uploads/jurnal/thumbnails/' . $fileData['file'];

            if (file_exists($filePath)) {
                try {
                    // Upload ke GDrive menggunakan nama folder yang spesifik
                    $gdriveId = $this->_uploadToGoogleDrive($filePath, $fileData['file'], $idperusahaan, $tahunBulan);

                    if ($gdriveId) {
                        $this->db->table('jurnalfile')
                            ->where('id', $id)
                            ->update(['kode_file' => $gdriveId]);
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $failCount++;
                }
            } else {
                $failCount++;
            }
        }

        return $this->response->setJSON([
            'status' => true,
            'msg' => "Migrasi Selesai! Berhasil: $successCount, Gagal: $failCount."
        ]);
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
}
