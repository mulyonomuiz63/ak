<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class CheckReload extends Controller
{
    public function index()
    {
        $data = $this->request->getJSON();
        $reload = isset($data->reload) && $data->reload == 1;

        if ($reload) {
            return $this->response->setJSON([
                'status'  => 'reload',
                'message' => 'Halaman ini di-reload.'
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'normal',
                'message' => 'Halaman ini dibuka secara normal.'
            ]);
        }
    }
}
