<?php

namespace App\Controllers;


use CodeIgniter\Controller;
use ZipArchive;

class Download extends BaseController
{

   
    public function index()
    {
       $urls = [];

        for ($i = 1; $i <= 385; $i++) {
            // $urls[] = "https://web.iaiglobal.or.id/assets/materi/Sertifikasi/CA/modul/abm/files/mobile/{$i}.jpg";
            // $urls[] = "https://web.iaiglobal.or.id/assets/materi/Sertifikasi/CA/modul/asi/files/mobile/{$i}.jpg";
            // $urls[] = "https://web.iaiglobal.or.id/assets/materi/Sertifikasi/CA/modul/mkd/files/mobile/{$i}.jpg";
            // $urls[] = "https://web.iaiglobal.or.id/assets/materi/Sertifikasi/CA/modul/ak/files/mobile/{$i}.jpg";
            // $urls[] = "https://web.iaiglobal.or.id/assets/materi/Sertifikasi/CA/modul/bde/files/mobile/{$i}.jpg";
            // $urls[] = "https://web.iaiglobal.or.id/assets/materi/Sertifikasi/CA/modul/hbp/files/mobile/{$i}.jpg";
            // $urls[] = "https://web.iaiglobal.or.id/assets/materi/Sertifikasi/CA/modul/aml/files/mobile/{$i}.jpg";
            // $urls[] = "https://web.iaiglobal.or.id/assets/materi/Sertifikasi/CA/modul/mkl/files/mobile/{$i}.jpg";
            // $urls[] = "https://web.iaiglobal.or.id/assets/materi/Sertifikasi/CA/modul/sipi/files/mobile/{$i}.jpg";
            // $urls[] = "https://web.iaiglobal.or.id/assets/materi/Sertifikasi/CA/modul/mp/files/mobile/{$i}.jpg";
            // $urls[] = "https://web.iaiglobal.or.id/assets/materi/Sertifikasi/CA/modul/aa/files/mobile/{$i}.jpg";
            // $urls[] = "https://web.iaiglobal.or.id/assets/materi/Sertifikasi/CA/modul/msk/files/mobile/{$i}.jpg";
            $urls[] = "https://web.iaiglobal.or.id/assets/materi/Sertifikasi/CA/modul/pk_19/files/mobile/{$i}.jpg";
        }

        // buat nama file zip sementara
        $zipFile = WRITEPATH . 'uploads/downloads/images_' . time() . '.zip';

        $zip = new ZipArchive;
        if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
            return "Tidak bisa membuat file ZIP";
        }

        foreach ($urls as $url) {
            $imgContent = @file_get_contents($url);
            if ($imgContent !== false) {
                $filename = basename(parse_url($url, PHP_URL_PATH));
                $zip->addFromString($filename, $imgContent);
            }
        }

        $zip->close();

        // langsung trigger download ke browser
        return $this->response->download($zipFile, null)->setFileName("materi.zip");

    }
}
