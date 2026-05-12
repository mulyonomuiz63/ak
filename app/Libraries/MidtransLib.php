<?php

namespace App\Libraries;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransLib
{
    public function __construct()
    {
        // Mengambil data dari .env
        Config::$serverKey = env('midtrans.serverKey');
        Config::$isProduction = (bool) env('midtrans.isProduction');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function getSnapToken($params)
    {
        return Snap::getSnapToken($params);
    }
}