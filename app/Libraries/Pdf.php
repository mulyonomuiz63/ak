<?php

namespace App\Libraries;

use TCPDF;

class Pdf extends TCPDF
{
    // Tambahkan properti untuk menampung data dinamis
    protected $namaPerusahaan;
    protected $judulLaporan;
    protected $periode;

    // Ambil data dinamis lewat constructor parameter (diberi nilai default kosong agar tidak error di laporan lain)
    function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $namaPerusahaan = '', $judulLaporan = '', $periode = '')
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
        
        $this->namaPerusahaan = $namaPerusahaan;
        $this->judulLaporan = $judulLaporan;
        $this->periode = $periode;
    }

    public function Header()
    {
        // Cek apakah ini halaman pertama
        if ($this->getPage() == 1) {
            
            $this->SetY(12); 
            $this->SetFont('times', '', 14);

            $title = '
                <table border="0" width="100%" cellpadding="0">
                    <tr>
                        <td style="text-align:center; text-transform:uppercase; font-size:17px; font-weight:bold;">' . $this->namaPerusahaan . '</td>
                    </tr>
                    <tr>
                        <td style="text-align:center; font-size:14px; font-weight:bold; padding-top:5px;">' . $this->judulLaporan . '</td>
                    </tr>';
            
            if (!empty($this->periode)) {
                // Tambahkan padding-bottom agar tulisan periode tidak menempel ke garis
                $title .= '
                    <tr>
                        <td style="text-align:center; font-size:12px; padding-top:5px; padding-bottom:10px;">Periode ' . $this->periode . '</td>
                    </tr>';
            }

            // [PERBAIKAN]: Ganti <hr> dengan baris tabel kosong yang memiliki border bawah
            $title .= '
                    <tr>
                        <td style="border-bottom: 2px solid black; font-size: 1px;">&nbsp;</td>
                    </tr>
                </table>';

            $this->writeHTML($title, true, false, false, false, '');

            // Set Margin Atas agar tabel konten turun dan sejajar
            // Pastikan angka 15 (kiri) dan 15 (kanan) sama persis dengan yang ada di Controller
            $this->SetMargins(15, 35, 15);

        } else {
            // Margin untuk Halaman 2 dan seterusnya
            $this->SetMargins(15, 15, 15);
        }
    }

    // Page footer
    public function Footer()
    {
        $this->SetY(-10);
        $this->SetFont('times', '', 10);

        $nomor = $this->getAliasNumPage() . '/' . $this->getAliasNbPages();
        $tgl = date('d/m/Y H:i:s');
        $nama = session()->get('namapengguna') ?? 'System';

        $foot = '
        <table border="0" width="100%" cellpadding="0">
            <tr>
                <td width="80%">User: ' . $nama . ' | Dicetak Melalui Akuntanmu.com | '.$tgl.'</td>
                <td width="20%" style="text-align:right;">Page ' . $nomor . '</td>
            </tr>
        </table>';

        $this->writeHTML($foot, true, false, false, false, '');
    }
}