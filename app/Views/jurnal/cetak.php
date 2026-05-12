<?php
// 1. Tambahkan backslash (\) agar PHP tahu TCPDF berasal dari namespace global Composer
class MYPDF extends \TCPDF
{
    //Page header
    public function Header()
    {
        $this->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        $this->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set margins
        $this->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
    }

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// create new PDF document
$pdf = new MYPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->AddPage();

$title = '
    <div style="text-align:center; font-size:20px; color:#2f3031; font-weight:bold; padding-top:10px; border-bottom:1px solid black">' . trim($namaperusahaan) . '</div>
    <span style="text-align:center; font-size:18px; color:#2f3031; font-weight:bold; ">JURNAL UMUM</span>   
    <hr>
';
$pdf->SetFont('times', '', 16);
$pdf->writeHTML($title, true, false, false, false, '');
$pdf->SetTopMargin(15);

// 2. PERBAIKAN: Ubah .= menjadi = pada deklarasi awal agar tidak kena error "Undefined variable $table"
$table = '<table cellpadding="5">';

$table .= '<tr>';
$table .= '<td width="20%" style="font-size:16px">No. Jurnal</td>';
$table .= '<td style="font-size:16px">: ' . $idjurnal  . '</td>';
$table .= '<td style="font-size:16px">Keterangan Transaksi:</td>';
$table .= '</tr>';

$table .= '<tr>';
$table .= '<td style="font-size:16px">Tanggal</td>';
// 3. PERBAIKAN CI4: Ubah ->row() menjadi ->getRow()
$table .= '<td style="font-size:16px">: ' . date('d-m-Y', strtotime($rsData->getRow()->tgljurnal)) . '</td>';
$table .= '<td style="height:60px;width:300px;border:1px solid gray">' . $rsData->getRow()->keterangan . '</td>';
$table .= '</tr>';

$table .= '</table>';

$table .= '<br><br><table border="1" cellpadding="4">';
$table .= ' 
            <thead>
                <tr>
                    <th width="50%" style="font-size:16px; font-weight:bold; text-align:center;">Akun</th>
                    <th width="14%" style="font-size:16px; font-weight:bold; text-align:center;">No. Akun</th>
                    <th width="18%" style="font-size:16px; font-weight:bold; text-align:center;">Debit</th>
                    <th width="18%" style="font-size:16px; font-weight:bold; text-align:center;">Kredit</th>
                </tr>
            </thead>
            <tbody>';
            
$no = 1;
$gt = 0;
$total1 = 0;
$total2 = 0;
$idjurnal_lama = '';

// 4. PERBAIKAN CI4: Ubah ->result() menjadi ->getResult()
foreach ($rsData->getResult() as $data) {
    $total1 += $data->debet;
    $total2 += $data->kredit;

    $table .= '
                <tr>
                    <td width="50%" style="font-size:14px; text-align:left;">' . ($data->debet == 0 ? str_repeat("&nbsp;", 5) : "") . $data->nmakun . '</td>
                    <td width="14%" style="font-size:14px; text-align:center;">' . $data->kdakun . '</td>
                    <td width="18%" style="font-size:14px; text-align:right;">' . ($data->debet == 0 ? "" : number_format($data->debet)) . '</td>
                    <td width="18%" style="font-size:14px; text-align:right;">' . ($data->kredit == 0 ? "" : number_format($data->kredit)) . '</td>        
                </tr>';

    $idjurnal_lama = $data->idjurnal;
}

$table .= '
                <tr>
                    <td width="64%" style="font-size:14px; text-align:right;" colspan="2"><B>TOTAL       </B></td>
                    <td width="18%" style="font-size:14px; text-align:right;"><B>' . number_format($total1, 0, "", '.') . '</B></td>
                    <td width="18%" style="font-size:14px; text-align:right;"><B>' . number_format($total2, 0, "", '.') . '</B></td>
                </tr>';
$table .= ' </tbody>
            </table>';

$table .= '<br><br><table cellpadding="5">';
$table .= '<tr>';
$table .= '<td width="50%"></td>';
$table .= '<td width="25%" style="font-size:14px;border:1px solid gray;text-align:center">Dibuat Oleh:</td>';
$table .= '<td width="25%" style="font-size:14px;border:1px solid gray;text-align:center">Diperiksa Oleh:</td>';
$table .= '</tr>';

$table .= '<tr>';
$table .= '<td></td>';
$table .= '<td style="height:75px;border:1px solid gray"></td>';
$table .= '<td style="height:75px;border:1px solid gray"></td>';
$table .= '</tr>';

$table .= '<tr>';
$table .= '<td></td>';
$table .= '<td style="border:1px solid gray;text-align:center"></td>';
$table .= '<td style="border:1px solid gray"></td>';
$table .= '</tr>';
$table .= '</table>';

$table .= '<br>';
$table .= '<br>';

$table .= '<table>';
$table .= '<tr>';
$table .= '<td style="height:20px;border:1px solid gray;text-align:center"><b>Lampiran Dokumen / Bukti Transfer</b></td>';
$table .= '</tr>';
$table .= '</table>';

$table .= '<table cellpadding="5">';
$table .= '<tr>';
$table .= '<td style="height:350px; border:1px solid gray;text-align:center">';

// 5. PERBAIKAN GAMBAR: Menggunakan FCPATH alih-alih base_url() untuk TCPDF
// TCPDF lebih optimal membaca langsung dari path server lokal ketimbang melalui URL HTTP
$table .= $lampiran == '' ? '' :  '<img src="' . FCPATH . 'uploads/' . $lampiran  . '" height="350">';

$table .= '</td>';
$table .= '</tr>';
$table .= '</table>';

$pdf->SetTopMargin(35);
$pdf->SetFont('times', '', 10);
$pdf->writeHTML($table, true, false, false, false, '');

$tglcetak = date('d-m-Y');

// 6. PERBAIKAN OUTPUT CI4: Menghentikan script setelah output agar output buffer CI4 tidak merusak file PDF
$pdf->Output('Jurnal_Umum.pdf', 'I');
exit;
?>