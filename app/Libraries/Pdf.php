<?php

namespace App\Libraries;

use TCPDF;

class Pdf extends TCPDF
{
    function __construct()
    {
        parent::__construct();
    }

    public function Header()
    {

        $this->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        $this->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));


        // set margins
        //$this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
    }

    // Page footer
    public function Footer()
    {
        // [PERBAIKAN UTAMA]: Atur posisi Y dari bawah halaman.
        // -15 berarti ditarik naik sejauh 15mm dari ujung paling bawah kertas.
        // Jika masih kurang naik, Anda bisa ubah menjadi -20 atau -25.
        $this->SetY(-10);

        // Disarankan ukuran font footer sedikit lebih kecil agar rapi
        $this->SetFont('times', '', 10);

        $nomor =  $this->getAliasNumPage() . '/' . $this->getAliasNbPages();
        $tgl = date('d/m/Y H:i:s' );
        $nama = session()->get('namapengguna') ?? 'System'; // Mencegah error jika session kosong

        // [PERBAIKAN HTML]: Hapus \\" yang salah, dan tambahkan width="100%" 
        // agar tabelnya benar-benar membentang dari margin kiri ke margin kanan.
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

/* End of file Pdf.php */
/* Location: ./application/libraries/Pdf.php */