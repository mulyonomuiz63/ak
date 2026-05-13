<?php

namespace App\Controllers;

class Maintenance extends BaseController
{
    public function index()
    {
        // Pengaman ekstra: Jika maintenance OFF tapi user iseng akses /maintenance, kembalikan ke home
        if (env('app.maintenanceMode', false) === false) {
            return redirect()->to('/');
        }

        // Tampilkan halaman
        $this->response->setStatusCode(503);
        return view('errors/html/maintenance_view');
    }
}