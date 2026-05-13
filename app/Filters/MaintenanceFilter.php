<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MaintenanceFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (env('app.maintenanceMode', false)) {

            $session = \Config\Services::session();

            // 1. CEK KUNCI RAHASIA DI URL
            // Jika Anda mengakses: domainanda.com/?bypass=buka_pintu
            if ($request->getGet('bypass') === 'buka_pintu') {
                // Simpan tiket masuk di session
                $session->set('bebas_maintenance', true);
                // Redirect ke halaman depan agar URL kembali bersih
                return redirect()->to('/');
            }

            // 2. JIKA PUNYA TIKET MASUK, IZINKAN LEWAT
            if ($session->get('bebas_maintenance') === true) {
                return; // Lolos dari filter!
            }

            // --- Logika redirect maintenance lama Anda tetap di bawah sini ---
            helper('url');
            $current_url = current_url();
            $target_url  = site_url('maintenance');

            if ($current_url !== $target_url) {
                return redirect()->to($target_url);
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi
    }
}
