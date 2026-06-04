<?php

namespace App\Controllers;

class ExportDataController extends BaseController
{
    // 1. INIT: Hitung total data & buat folder sementara
    public function export_init()
    {
        try {
            $idperusahaan_enc = $this->request->getPost('idperusahaan');
            $tables = $this->request->getPost('tables');
            
            if (empty($idperusahaan_enc) || empty($tables)) {
                return $this->response->setJSON([
                    'status' => false, 
                    'msg' => 'Data perusahaan atau tabel belum dipilih.'
                ]);
            }

            $idperusahaan = [];
            foreach ($idperusahaan_enc as $enc) {
                $idperusahaan[] = decrypt($enc); 
            }

            $db = \Config\Database::connect();
            $totalRows = 0;

            foreach ($tables as $table) {
                $builder = $db->table($table);
                if ($table == 'jurnal' || $table == 'jurnaldetail' || $table == 'jurnalfile') {
                    if($table == 'jurnaldetail') $builder->join('jurnal', 'jurnal.idjurnal = jurnaldetail.idjurnal');
                    if($table == 'jurnalfile') $builder->join('jurnal', 'jurnal.idjurnal = jurnalfile.idjurnal');
                    $builder->join('pengguna', 'pengguna.idpengguna = jurnal.idpengguna');
                    $builder->whereIn('md5(pengguna.idperusahaan)', $idperusahaan);
                } else {
                    $builder->whereIn('md5(idperusahaan)', $idperusahaan);
                }
                
                $totalRows += $builder->countAllResults();
            }

            $sessionExportId = 'export_' . time() . '_' . rand(1000,9999);
            if (!is_dir(WRITEPATH . 'uploads/' . $sessionExportId)) {
                mkdir(WRITEPATH . 'uploads/' . $sessionExportId, 0777, true);
            }

            return $this->response->setJSON([
                'status' => true,
                'total_rows' => $totalRows,
                'session_export_id' => $sessionExportId,
                'tables' => $tables,
                'msg' => 'Inisialisasi berhasil. Total data yang akan diekspor: ' . $totalRows
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false, 
                'msg' => 'Terjadi kesalahan pada Database saat init: ' . $e->getMessage()
            ]);
        }
    }

    // 2. PROCESS: Ambil data dicicil, dipisah per file CSV sesuai tabelnya masing-masing
    public function export_process()
    {
        try {
            $sessionExportId = $this->request->getPost('session_export_id');
            $table = $this->request->getPost('table');
            $limit = (int) $this->request->getPost('limit');
            $offset = (int) $this->request->getPost('offset');
            
            $idperusahaan_enc = $this->request->getPost('idperusahaan');
            $idperusahaan = [];
            foreach ($idperusahaan_enc as $enc) {
                $idperusahaan[] = decrypt($enc); 
            }

            $db = \Config\Database::connect();
            $builder = $db->table($table);
            
            if ($table == 'jurnal' || $table == 'jurnaldetail' || $table == 'jurnalfile') {
                $builder->select($table.'.*');
                if($table == 'jurnaldetail') $builder->join('jurnal', 'jurnal.idjurnal = jurnaldetail.idjurnal');
                if($table == 'jurnalfile') $builder->join('jurnal', 'jurnal.idjurnal = jurnalfile.idjurnal');
                $builder->join('pengguna', 'pengguna.idpengguna = jurnal.idpengguna');
                $builder->whereIn('md5(pengguna.idperusahaan)', $idperusahaan);
            } else {
                $builder->whereIn('md5(idperusahaan)', $idperusahaan);
            }

            $builder->limit($limit, $offset);
            $result = $builder->get()->getResultArray();

            // Membuka file fisik CSV terpisah sesuai nama tabelnya di dalam folder session sementara
            $csvFilePath = WRITEPATH . 'uploads/' . $sessionExportId . '/' . $table . '.csv';
            $file = fopen($csvFilePath, 'a');

            // Cetak Header Kolom di baris pertama (Offset 0)
            if ($offset == 0) {
                if ($table == 'perusahaan') {
                    fputcsv($file, ['idperusahaan', 'namaperusahaan', 'alamat', 'tglmulaiusaha', 'email', 'foto', 'notelp', 'statusaktif']);
                } elseif ($table == 'pengguna') {
                    fputcsv($file, ['idpengguna', 'idperusahaan', 'namapengguna', 'username', 'password']);
                } elseif ($table == 'akun') {
                    fputcsv($file, ['keyakun', 'idperusahaan', 'kdakun', 'nmakun', 'level', 'saldonormal', 'status']);
                } elseif ($table == 'jurnal') {
                    fputcsv($file, ['idjurnal', 'idpengguna', 'tgljurnal', 'keterangan', 'jumlah', 'tglinsert', 'tglupdate', 'referensi']);
                } elseif ($table == 'jurnaldetail') {
                    fputcsv($file, ['keyakun', 'idjurnal', 'debet', 'kredit', 'nourut']);
                } elseif ($table == 'jurnalfile') {
                    fputcsv($file, ['id', 'idjurnal', 'nama_file', 'kode_file', 'created_at']);
                }
            }

            // Tulis baris data dengan pemetaan array key yang kaku (Menjamin kolom tidak tertukar)
            foreach ($result as $row) {
                $csvData = [];
                if ($table == 'perusahaan') {
                    $csvData = ["'" .$row['idperusahaan'], $row['namaperusahaan'], $row['alamat'], $row['tglmulaiusaha'], $row['email'], $row['foto'], $row['notelp'], $row['statusaktif']];
                } elseif ($table == 'pengguna') {
                    $csvData = ["'" .$row['idpengguna'], "'" .$row['idperusahaan'], $row['namapengguna'], $row['username'], $row['password']];
                } elseif ($table == 'akun') {
                    $csvData = ["'" .$row['keyakun'], "'" .$row['idperusahaan'], $row['kdakun'], $row['nmakun'], $row['level'], $row['saldonormal'], $row['status']];
                } elseif ($table == 'jurnal') { 
                    $csvData = [$row['idjurnal'], "'" .$row['idpengguna'], $row['tgljurnal'], $row['keterangan'],"'" . $row['jumlah'], $row['tglinsert'], $row['tglupdate'], $row['referensi']];
                } elseif ($table == 'jurnaldetail') {
                    $csvData = ["'" .$row['keyakun'], $row['idjurnal'],"'" . $row['debet'],"'" . $row['kredit'], $row['nourut']];
                } elseif ($table == 'jurnalfile') {
                    $csvData = ["'" .$row['id'], $row['idjurnal'], $row['nama_file'], $row['kode_file'], $row['created_at']];
                }
                fputcsv($file, $csvData); 
            }
            fclose($file);

            return $this->response->setJSON([
                'status' => true,
                'rows_processed' => count($result),
                'is_done' => (count($result) < $limit)
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false, 
                'msg' => 'Error pada tabel ' . $table . ': ' . $e->getMessage()
            ]);
        }
    }

    // 3. FINALIZE: Kemas file-file CSV terpisah tadi menjadi satu berkas ZIP
    public function export_finalize()
    {
        try {
            $sessionExportId = $this->request->getPost('session_export_id');
            $folderPath = WRITEPATH . 'uploads/' . $sessionExportId;
            
            $zipFileName = 'Backup_Migrasi_' . date('Ymd_His') . '.zip';
            $zipFilePath = WRITEPATH . 'uploads/' . $zipFileName;

            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                $files = glob($folderPath . '/*.csv');
                if ($files !== false) {
                    foreach ($files as $file) {
                        // Memasukkan file CSV terpisah ke dalam zip dengan nama asli tabelnya (contoh: perusahaan.csv)
                        $zip->addFile($file, basename($file));
                    }
                }
                $zip->close();
            }

            // Bersihkan folder sementara
            if (is_dir($folderPath)) {
                array_map('unlink', glob("$folderPath/*.*"));
                rmdir($folderPath);
            }

            return $this->response->setJSON([
                'status' => true,
                'download_url' => site_url('perusahaan/download-zip/' . $zipFileName)
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'msg' => 'Gagal mengemas ZIP: ' . $e->getMessage()
            ]);
        }
    }

    // 4. DOWNLOAD ENDPOINT
    public function download_zip($filename)
    {
        $path = WRITEPATH . 'uploads/' . $filename;
        
        if (file_exists($path)) {
            // Kita hapus file tersebut setelah browser selesai mengunduhnya.
            // Register shutdown function akan menjalankan unlink saat script PHP selesai sepenuhnya.
            register_shutdown_function(function() use ($path) {
                @unlink($path);
            });

            // Kirim file ke user
            return $this->response->download($path, null);
        }
        
        die("File tidak ditemukan atau sudah kadaluarsa.");
    }
}