<?php

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Libraries\Pdf;

class LapJurnalController extends BaseController
{

	var $namaperusahaan = 'PT. XYZ';

	public function index()
	{
		$tglawal = $this->request->getPost('tglawal');
		$tglakhir = $this->request->getPost('tglakhir');

		if (session()->get('idpengguna') == '8888888888') {
			$idperusahaan = $this->request->getPost('idperusahaan');
		} else {
			$idperusahaan = $this->request->getPost('idperusahaan');
		}

		if (!empty($idperusahaan)) {
			$namaperusahaan = $this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow()->namaperusahaan;
		} else {
			$namaperusahaan = '';
		}


		if (empty($tglawal)) {
			$tglawal = date('Y-m-01');
			$tglakhir = date('Y-m-d');
		}
		$level = session()->get('level');



		$data['level'] = $level;
		$data['idperusahaan'] = $idperusahaan;
		$data['namaperusahaan'] = $namaperusahaan;
		$data['tglawal'] = $tglawal;
		$data['tglakhir'] = $tglakhir;
		$data['menuaktif'] = 'lapjurnal';
		$data['rsData'] = $this->laporan_model->get_jurnal($tglawal, $tglakhir, $idperusahaan, 'desc');
		return view('laporan/lapjurnal/index', $data);
	}

	public function lapJurnalCetak($tglawal, $tglakhir, $idperusahaan)
    {
        // Bypass batas waktu eksekusi dan memori untuk data masif
        ini_set('memory_limit', '-1'); 
        set_time_limit(0);             

        $rsDataPerusahaan = $this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow();
        if (!empty($idperusahaan)) {
            $namaperusahaan = $rsDataPerusahaan->namaperusahaan;
        } else {
            $namaperusahaan = '';
        }
        
        if ($tglawal == $tglakhir) {
            $periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $periode = $this->laporan_model->tglindonesialengkap($tglawal) . ' s/d ' . $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        $rsData = $this->laporan_model->get_jurnal($tglawal, $tglakhir, $idperusahaan, 'asc');

        $pdf = new \App\Libraries\Pdf('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, $namaperusahaan, 'LAPORAN JURNAL UMUM', $periode);
        
        // Atur margin default sebelum AddPage (Kiri 15, Atas 35, Kanan 15)
        $pdf->SetMargins(15, 35, 15);
        $pdf->setPrintHeader(true);
        $pdf->AddPage();

        // Mengatur Base Font untuk Konten Tabel
        $pdf->SetFont('times', '', 10);

        // [PERBAIKAN 1]: Hapus <br><br> dan Samakan persentase width dengan <tbody>
        $tableHeader = '
            <table border="0" cellpadding="2" cellspacing="0" width="100%">
                <thead>
                    <tr style="background-color:#055F93; color:#ffffff;">
                        <th width="5%" style="font-weight:bold; text-align:center; font-size:11pt;">No</th>
                        <th width="12%" style="font-weight:bold; text-align:center; font-size:11pt;">Tanggal</th>
                        <th width="15%" style="font-weight:bold; text-align:center; font-size:11pt;">No Jurnal</th>
                        <th width="40%" style="font-weight:bold; text-align:center; font-size:11pt;">Nama Akun</th>
                        <th width="14%" style="font-weight:bold; text-align:center; font-size:11pt;">Debet</th>
                        <th width="14%" style="font-weight:bold; text-align:center; font-size:11pt;">Kredit</th>
                    </tr>
                </thead>
                <tbody>';
        
        $tableFooter = '</tbody></table>';

        $total1 = 0;
        $total2 = 0;
        $idjurnal_lama = '';
        $no = 1;

        $html = $tableHeader;
        $rowCount = 0;
        $chunkSize = 500; 

        while ($data = $rsData->getUnbufferedRow()) {
            $total1 = $total1 + $data->debet;
            $total2 = $total2 + $data->kredit;

            // Styling baris data murni tanpa border
            $tdStyle = 'font-size: 10pt; line-height: 1.3;';

            // [PERBAIKAN 2]: Pastikan persentase width di <td> sama persis dengan <th> di atas
            $html .= '
                    <tr nobr="true">
                        <td width="5%"  style="text-align:center; ' . $tdStyle . '">' . ($data->idjurnal == $idjurnal_lama ? "" : $no++) . '</td>
                        <td width="12%" style="text-align:center; ' . $tdStyle . '">' . ($data->idjurnal == $idjurnal_lama ? "" : date('d-m-Y', strtotime($data->tgljurnal))) . '</td>
                        <td width="15%" style="text-align:center; ' . $tdStyle . '">' . ($data->idjurnal == $idjurnal_lama ? "" : $data->idjurnal) . '</td>
                        <td width="40%" style="text-align:left; ' . $tdStyle . '">' . ($data->debet == 0 ? str_repeat("&nbsp;", 6) : "") . $data->nmakun . '</td>
                        <td width="14%" style="text-align:right; ' . $tdStyle . '">' . ($data->debet == 0 ? "-" : number_format($data->debet, 0, ",", ".")) . '</td>
                        <td width="14%" style="text-align:right; ' . $tdStyle . '">' . ($data->kredit == 0 ? "-" : number_format($data->kredit, 0, ",", ".")) . '</td>            
                    </tr>';

            $idjurnal_lama = $data->idjurnal;
            $rowCount++;

            if ($rowCount % $chunkSize == 0) {
                $html .= $tableFooter;
                $pdf->writeHTML($html, true, false, false, false, '');
                $html = $tableHeader; 
            }
        }

        // Baris Total tanpa garis, hanya dibedakan dengan warna background tipis dan tulisan tebal
        $tdTotalStyle = 'font-size: 10pt; padding: 8px 0;';
        
        $html .= '
                    <tr nobr="true" style="background-color: #f2f2f2;">
                        <td width="72%" style="text-align:right; ' . $tdTotalStyle . '" colspan="4"><strong>TOTAL &nbsp;&nbsp;&nbsp;</strong></td>
                        <td width="14%" style="text-align:right; ' . $tdTotalStyle . '"><strong>' . number_format($total1, 0, ",", ".") . '</strong></td>
                        <td width="14%" style="text-align:right; ' . $tdTotalStyle . '"><strong>' . number_format($total2, 0, ",", ".") . '</strong></td>
                    </tr>';
        
        $html .= $tableFooter;

        $pdf->writeHTML($html, true, false, false, false, '');

        $bulantahun = bulan_tahun($tglawal) . ' - ' . bulan_tahun($tglakhir);
        $namaPerusahaan = ucwords(strtolower($namaperusahaan));
        $namaPerusahaan = preg_replace(['/\bPt\b/', '/\bCv\b/'], ['PT', 'CV'], $namaPerusahaan);
        $namaFile = 'Laporan Jurnal ' . $namaPerusahaan . ' ' . $bulantahun . '.pdf';
        
        $pdf->Output($namaFile, 'I');
        exit;
    }

	public function lapJurnalCetakExcel($tglawal, $tglakhir, $idperusahaan)
    {
        // [1. BYPASS MEMORY] 
        ini_set('memory_limit', '-1'); 
        set_time_limit(0);             

        // [2. AMBIL DATA PERUSAHAAN]
        $rsDataPerusahaan = $this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow();
        $namaperusahaan = !empty($idperusahaan) ? $rsDataPerusahaan->namaperusahaan : '';

        // [3. PERIODE]
        if ($tglawal == $tglakhir) {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal) . ' s/d ' . $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        // [4. SETUP HEADER HTTP UNTUK EXCEL (HTML BASED)]
        $bulantahun     = bulan_tahun($tglawal) . ' - ' . bulan_tahun($tglakhir);
        $namaPerusahaan = ucwords(strtolower($namaperusahaan));
        $namaPerusahaan = preg_replace(['/\bPt\b/', '/\bCv\b/'], ['PT', 'CV'], $namaPerusahaan);
        $namaFile = 'Laporan Jurnal ' . $namaPerusahaan . ' ' . $bulantahun . '.xls';
        
        header("Content-Disposition: attachment; filename=\"" . $namaFile . "\"");
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Cache-Control: max-age=0");

        // [5. TULIS STRUKTUR HTML & CSS (Untuk Styling Lebar Kolom dll)]
        echo "<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">";
        echo "<head>";
        echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
        echo "<style>";
        echo "table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }";
        echo "th, td { border: 1px solid #000000; padding: 5px; }";
        echo "th { background-color: #D9D9D9; font-weight: bold; text-align: center; }";
        // Mengatur persentase lebar (responsive) atau lebar pasti
        echo ".col-no { width: 5%; text-align: center; }";
        echo ".col-tgl { width: 12%; text-align: center; }";
        echo ".col-nojurnal { width: 18%; text-align: center; }";
        echo ".col-akun { width: 35%; text-align: left; }";
        echo ".col-uang { width: 15%; text-align: right; }";
        echo "</style>";
        echo "</head>";
        echo "<body>";

        // [6. TULIS KOP LAPORAN]
        echo "<table>";
        echo "<tr><th colspan='6' style='border:none; background:none; font-size: 16px;'>{$namaperusahaan}</th></tr>";
        echo "<tr><th colspan='6' style='border:none; background:none; font-size: 14px;'>LAPORAN JURNAL UMUM</th></tr>";
        echo "<tr><th colspan='6' style='border:none; background:none; font-size: 12px;'>Periode {$Periode}</th></tr>";
        echo "<tr><td colspan='6' style='border:none;'></td></tr>"; // Baris Kosong Pemisah

        // [7. TULIS HEADER TABEL]
        echo "<tr>";
        echo "<th class='col-no'>No</th>";
        echo "<th class='col-tgl'>Tanggal</th>";
        echo "<th class='col-nojurnal'>No Jurnal</th>";
        echo "<th class='col-akun'>Nama Akun</th>";
        echo "<th class='col-uang'>Debet</th>";
        echo "<th class='col-uang'>Kredit</th>";
        echo "</tr>";

        // [8. AMBIL DATA DARI DATABASE]
        $rsData = $this->laporan_model->get_jurnal($tglawal, $tglakhir, $idperusahaan, 'asc');

        $total1 = 0;
        $total2 = 0;
        $idjurnal_lama = '';
        $no = 1;

        // [9. LOOPING DATA & FLUSH UNTUK HEMAT RAM]
        while ($data = $rsData->getUnbufferedRow()) {
            $total1 += $data->debet;
            $total2 += $data->kredit;

            $tampil_no = ($data->idjurnal == $idjurnal_lama) ? "" : $no++;
            $tampil_tgl = ($data->idjurnal == $idjurnal_lama) ? "" : date('d-m-Y', strtotime($data->tgljurnal));
            $tampil_nojurnal = ($data->idjurnal == $idjurnal_lama) ? "" : $data->idjurnal;

            // Indentasi spasi HTML (&nbsp;) untuk akun kredit
            $nama_akun = ($data->debet == 0 ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : "") . htmlspecialchars($data->nmakun);

            echo "<tr>";
            echo "<td class='col-no'>{$tampil_no}</td>";
            echo "<td class='col-tgl'>{$tampil_tgl}</td>";
            echo "<td class='col-nojurnal'>{$tampil_nojurnal}</td>";
            echo "<td class='col-akun'>{$nama_akun}</td>";
            
            // PENTING: mso-number-format "\#\,\#\#0" akan memaksa Excel 
            // menampilkan separator ribuan, TAPI nilainya tetap angka murni (bisa di SUM)
            echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$data->debet}</td>";
            echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$data->kredit}</td>";
            echo "</tr>";

            $idjurnal_lama = $data->idjurnal;

            // Membuang output ke browser secara bertahap agar RAM server tidak penuh
            if (ob_get_level() > 0) ob_flush();
            flush();
        }

        // [10. TULIS TOTAL]
        echo "<tr>";
        echo "<td colspan='4' style='text-align: right; font-weight: bold;'>TOTAL</td>";
        echo "<td class='col-uang' style='font-weight: bold; mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$total1}</td>";
        echo "<td class='col-uang' style='font-weight: bold; mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$total2}</td>";
        echo "</tr>";

        // [11. TUTUP TAG HTML]
        echo "</table>";
        echo "</body>";
        echo "</html>";
        exit;
    }

	function fetch()
	{
		$tglawal = $this->request->getPost('tglawal');
		$tglakhir = $this->request->getPost('tglakhir');
		$idperusahaan = $this->request->getPost('idperusahaan');
		$limit = $this->request->getPost('limit');
		$start = $this->request->getPost('start');
		$output = '';
		$rsData = $this->laporan_model->get_fetch($tglawal, $tglakhir, $idperusahaan, $limit, $start);

		$total1 = 0;
		$total2 = 0;
		$idjurnal_lama = '';

		$no = 1;
		foreach ($rsData->getResult() as $key => $data) {
			$no++;
			$total1 = $total1 + $data->debet;
			$total2 = $total2 + $data->kredit;

			// <td style="text-align:center;">' . ($data->idjurnal == $idjurnal_lama ? "" : $no++) . '</td>
			$output .= '
                                  <tr>
                                    <td style="text-align:center;">' . ($data->idjurnal == $idjurnal_lama ? "" : date('d-m-Y', strtotime($data->tgljurnal))) . '</td>
                                    <td style="text-align:left;">' . ($data->debet == 0 ? str_repeat("&nbsp;", 5) : "") . $data->nmakun . '</td>
                                    <td style="text-align:center;">' . $data->kdakun . '</td>
                                    <td style="text-align:right;">' . ($data->debet == 0 ? "" : number_format($data->debet, 0, ",", ".")) . '</td>
                                    <td style="text-align:right;">' . ($data->kredit == 0 ? "" : number_format($data->kredit, 0, ",", ".")) . '</td>     
                                  </tr>';

			$idjurnal_lama = $data->idjurnal;
		}
		$total = $no;
		if ($no <= $limit) {
			$total = '';
		}
		$dt = array(
			'output' => $output,
			'total' => $total,
		);
		return $this->response->setJSON($dt);
	}
	function totalFetch()
	{
		$tglawal = $this->request->getPost('tglawal');
		$tglakhir = $this->request->getPost('tglakhir');
		$idperusahaan = $this->request->getPost('idperusahaan');

		$rsData = $this->laporan_model->get_totalFetch($tglawal, $tglakhir, $idperusahaan);
		return $this->response->setJSON($rsData->getResult());
	}
}
