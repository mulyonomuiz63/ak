<?php

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Libraries\Pdf;

class LapPosisiKeuanganController extends BaseController
{

	var $namaperusahaan = 'PT. XYZ';

	public function index()
	{
		$tglawal = $this->request->getPost('tglawal');
		$tglakhir = $this->request->getPost('tglakhir');
		
		if($this->request->getPost('akunlevel3') != null){
		    $akunlevel3 = $this->request->getPost('akunlevel3');
		}else{
		    $akunlevel3 = '1';
		}


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
			$tglawal = date('Y-m-d', strtotime($this->perusahaan_model->get_by_id(encrypt(session()->get('idperusahaan')))->getRow()->tglmulaiusaha));
			$tglakhir = date('Y-m-d');
		}
		$tglakhirberjalan = date('Y-01-01', strtotime($tglakhir));

		$data['tglawal'] = $tglawal;
		$data['tglakhir'] = $tglakhir;
		$data['akunlevel3'] = $akunlevel3;
		$data['idperusahaan'] = $idperusahaan;
		$data['namaperusahaan'] = $namaperusahaan;

		//untuk menampilkan ringkasan laporan level 1 sampai level 3 saja
		if ($akunlevel3 == '1') {
    		$level = "AND a.level IN (1,2,3)";
		}else{
		    $level = "";
		}
		
		$data['rsData'] = $this->laporan_model->get_lapposisikeuangan($tglawal, $tglakhir, $idperusahaan, $level);
		$data['rsDataBerjalan'] = $this->laporan_model->get_lapposisikeuangan($tglakhirberjalan, $tglakhir, $idperusahaan, $level);

		return view('laporan/lapposisikeuangan/index', $data);
	}

	public function lapPosisiKeuanganCetak($tglawal, $tglakhir, $akunlevel3, $idperusahaan)
    {
        // [OPTIMASI]: Bebaskan limit memori dan waktu eksekusi server
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        if (session()->get('idpengguna') == '8888888888') {
            $idperusahaan = $idperusahaan;
        } else {
            $idperusahaan = session()->get('idperusahaan');
        }

        if (!empty($idperusahaan)) {
            $namaperusahaan = $this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow()->namaperusahaan;
        } else {
            $namaperusahaan = '';
        }

        if (!empty($idperusahaan)) {
            $alamat = $this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow()->alamat;
        } else {
            $alamat = '';
        }

        if (!empty($idperusahaan)) {
            $notelp = $this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow()->notelp;
        } else {
            $notelp = '';
        }

        $tglakhirberjalan = date('Y-01-01', strtotime($tglakhir));
        $akunlevel3 = $akunlevel3;
        $idperusahaan = $idperusahaan;
        $namaperusahaan = $namaperusahaan;
        $alamat = $alamat;
        $notelp = $notelp;
        
        //untuk menampilkan ringkasan laporan level 1 sampai level 3 saja
        if ($akunlevel3 == '1') {
            $level = "AND a.level IN (1,2,3)";
            $namaCetak = ' Ringkas';
        }else{
            $level = "";
            $namaCetak = '';
        }
        
        $rsData = $this->laporan_model->get_lapposisikeuangan($tglawal, $tglakhir, $idperusahaan, $level);
        $rsDataBerjalan = $this->laporan_model->get_lapposisikeuangan($tglakhirberjalan, $tglakhir, $idperusahaan, $level);
        
        $pdf = new \App\Libraries\Pdf('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetMargins(20, 20, 10);
        $pdf->setPrintHeader(false); // Opsional, hilangkan header default
        $pdf->AddPage();

        // Cetak Header ke PDF
        $title = '
			<span style="text-align:center; text-transform:uppercase; font-weight:bold; padding-top:10px;">' . $namaperusahaan . '</span><br>	
			<span style="text-align:center; font-weight:bold; padding-top:10px;">LAPORAN POSISI KEUANGAN</span>	
		';
        $pdf->SetFont('times', '', 16);
        $pdf->writeHTML($title, true, false, false, false, '');
        $pdf->SetTopMargin(15);

        if ($tglawal == $tglakhir) {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $Periode = $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        $title_periode = '<div style="text-align:center; padding-top:10px;"> Periode Berakhir s/d ' . ($Periode) . '</div><br>';
        $pdf->SetFont('times', '', 12);
        $pdf->writeHTML($title_periode, true, false, false, false, '');

        $total1 = 0;
        $totalaset = 0;
        $totalhutangdanmodal = 0;
        $totalasetBerjalan = 0;
        $totalhutangdanmodalBerjalan = 0;
        $kdakun_old = '';

        // [OPTIMASI 1]: Looping Unbuffered untuk Periode Berjalan
        while ($dataBerjalan = $rsDataBerjalan->getUnbufferedRow()) {
            if (substr($dataBerjalan->kdakun, 0, 1) == '1' && $dataBerjalan->level == '1') {
                $totalasetBerjalan += $dataBerjalan->jumlah;
            }

            if ((substr($dataBerjalan->kdakun, 0, 1) == '2' || substr($dataBerjalan->kdakun, 0, 1) == '3') && $dataBerjalan->level == '1') {
                $totalhutangdanmodalBerjalan += $dataBerjalan->jumlah;
            }
        }

        // [OPTIMASI 2]: Siapkan Chunking PDF
        $tableHeader = '<table border="0" cellpadding="3"><tbody>';
        $tableFooter = '</tbody></table>';
        $htmlChunk = $tableHeader;
        $rowCount = 0;
        $chunkSize = 400; // Kirim ke PDF per 400 baris

        // [OPTIMASI 3]: Looping Unbuffered untuk Data Utama
        while ($data = $rsData->getUnbufferedRow()) {
            $total1 += $data->jumlah;
            $font = '';

            switch ($data->level) {
                case '1':
                    $font = ' font-weight: bold;';
                    $spasi = '';
                    break;
                case '2':
                    $font = '';
                    $spasi = str_repeat('&nbsp;', 4);
                    break;
                case '3':
                    $font = '';
                    $spasi = str_repeat('&nbsp;', 8);
                    break;
                case '4':
                    $font = '';
                    $spasi = str_repeat('&nbsp;', 12);
                    break;
                default:
                    $font = '';
                    break;
            }

            if (substr($data->kdakun, 0, 1) == '1' && $data->level == '1') {
                $totalaset += $data->jumlah;
            }

            if ((substr($data->kdakun, 0, 1) == '2' || substr($data->kdakun, 0, 1) == '3') && $data->level == '1') {
                $totalhutangdanmodal += $data->jumlah;
            }

            // Logika Pembatas TOTAL ASET
            if ((substr($kdakun_old, 0, 1) != substr($data->kdakun, 0, 1)) && $kdakun_old != '' && substr($kdakun_old, 0, 1) == '1') {
                $htmlChunk .= '
                        <tr style="background-color:#055F93; color:#ffffff;">
                            <td width="78%" style=" font-weight: bold; text-align:left;">TOTAL ASET</td>
                            <td width="22%" style=" font-weight: bold; text-align:right;">' . ($totalaset >= 0 ? number_format($totalaset, 0, ",", ".") : "(" . number_format($totalaset * -1, 0, ",", ".") . ")") . '</td>
                        </tr>';
                $rowCount++;
            }

            // Logika Render Baris Akun
            if ($akunlevel3 == 1) {
                if ($data->level != 4) {
                    $htmlChunk .= '
                        <tr>
                            <td width="78%" style="' . $font . ' text-align:left;">' .  $spasi . $data->kdakun . ' - ' . $data->nmakun . '</td>
                            <td width="22%" style="' . $font . ' text-align:right;">' . ($data->level != 1 ? ($data->jumlah >= 0 ? number_format($data->jumlah, 0, ",", ".") : "(" . number_format($data->jumlah * -1, 0, ",", ".") . ")") : '') . '</td>
                        </tr>';
                }
            } else {
                $htmlChunk .= '
                        <tr>
                            <td width="78%" style="' . $font . ' text-align:left;">' .  $spasi . $data->kdakun . ' - ' . $data->nmakun . '</td>
                            <td width="22%" style="' . $font . ' text-align:right;">' . ($data->level == 4 ? ($data->jumlah >= 0 ? number_format($data->jumlah, 0, ",", ".") : "(" . number_format($data->jumlah * -1, 0, ",", ".") . ")") : '') . '</td>
                        </tr>';
            }

            $kdakun_old = $data->kdakun;
            $rowCount++;

            // [OPTIMASI 4]: Chunk render PDF jika sudah mencapai 400 baris
            if ($rowCount % $chunkSize == 0) {
                $htmlChunk .= $tableFooter;
                $pdf->SetFont('times', '', 10);
                $pdf->writeHTML($htmlChunk, true, false, false, false, '');
                
                // Buka tabel baru
                $htmlChunk = $tableHeader;
            }
        }

        // Kalkulasi Total Akhir
        $totallabaditahan = ($totalaset - $totalhutangdanmodal) - ($totalasetBerjalan - $totalhutangdanmodalBerjalan);
        $totallabaperiodeberjalan = $totalasetBerjalan - $totalhutangdanmodalBerjalan;
        $totalliabilitandanekuitas = $totalhutangdanmodal + $totalaset - $totalhutangdanmodal;

        // Render Baris Akhir
        $htmlChunk .= '
                        <tr style="background-color: #E5E4E2;">
                            <td width="78%" style=" font-weight: bold; text-align:left;">' . str_repeat('&nbsp;', 4) . 'Laba Ditahan</td>
                            <td width="22%" style=" font-weight: bold; text-align:right;">' . ($totallabaditahan >= 0 ? number_format($totallabaditahan, 0, ",", ".") : "(" . number_format($totallabaditahan * -1, 0, ",", ".") . ")") . '</td>
                        </tr>';

        $htmlChunk .= '
                         <tr style="border-right-color: #fff; background-color: #E5E4E2;" >
                            <td width="78%" style=" font-weight: bold; text-align:left;">' . str_repeat('&nbsp;', 4) . 'Laba (Rugi) Periode Berjalan</td>
                            <td width="22%" style=" font-weight: bold; text-align:right;">' . ($totallabaperiodeberjalan >= 0 ? number_format($totallabaperiodeberjalan, 0, ",", ".") : "(" . number_format($totallabaperiodeberjalan * -1, 0, ",", ".") . ")") . '</td>
                        </tr>';

        $htmlChunk .= '
                        <tr style="background-color:#055F93; color:#ffffff;">
                            <td width="78%" style=" font-weight: bold; text-align:left;">TOTAL LIABILITAS DAN EKUITAS</td>
                            <td width="22%" style=" font-weight: bold; text-align:right;">' . ($totalliabilitandanekuitas >= 0 ? number_format($totalliabilitandanekuitas, 0, ",", ".") : "(" . number_format($totalliabilitandanekuitas * -1, 0, ",", ".") . ")") . '</td>
                        </tr>';

        $htmlChunk .= $tableFooter;

        // Cetak sisa tabel ke PDF
        $pdf->SetTopMargin(35);
        $pdf->SetFont('times', '', 10);
        $pdf->writeHTML($htmlChunk, true, false, false, false, '');

        $bulantahun = bulan_tahun($tglawal) . ' - ' . bulan_tahun($tglakhir);
        $namaPerusahaan = ucwords(strtolower($namaperusahaan));
        $namaPerusahaan = preg_replace(['/\bPt\b/', '/\bCv\b/'], ['PT', 'CV'], $namaPerusahaan);
        $namaFile = 'Laporan Posisi Keuangan'.$namaCetak.' ' . $namaPerusahaan . ' ' . $bulantahun . '.pdf';
        $pdf->Output($namaFile, 'I');
        exit;
    }


	public function lapPosisiKeuanganExcel($tglawal, $tglakhir, $akunlevel3, $idperusahaan)
    {
        // [1] BYPASS MEMORY: Bebaskan limit memori dan waktu eksekusi untuk jutaan data
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        if (!empty($idperusahaan)) {
            $namaperusahaan = $this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow()->namaperusahaan;
        } else {
            $namaperusahaan = '';
        }

        $tglakhirberjalan = date('Y-01-01', strtotime($tglakhir));
        $idperusahaan = $idperusahaan;
        $namaperusahaan = $namaperusahaan;
        
        // Logika Ringkasan Laporan Level 1 sampai Level 3 (Logika Asli Tetap)
        if ($akunlevel3 == '1') {
            $level = "AND a.level IN (1,2,3)";
            $namaCetak = " Ringkas";
        } else {
            $level = "";
            $namaCetak = '';
        }
        
        $rsData = $this->laporan_model->get_lapposisikeuangan($tglawal, $tglakhir, $idperusahaan, $level);
        $rsDataBerjalan = $this->laporan_model->get_lapposisikeuangan($tglakhirberjalan, $tglakhir, $idperusahaan, $level);
        
        // [2] SETUP HEADER HTTP: Menggunakan .xls berbasis HTML Stream
        $bulantahun = bulan_tahun($tglawal) . ' - ' . bulan_tahun($tglakhir);
        $namaPerusahaan = ucwords(strtolower($namaperusahaan));
        $namaPerusahaan = preg_replace(['/\bPt\b/', '/\bCv\b/'], ['PT', 'CV'], $namaPerusahaan);
        $namaFile = 'Laporan Posisi Keuangan' . $namaCetak . ' ' . $namaPerusahaan . ' ' . $bulantahun . '.xls';
        
        header("Content-Disposition: attachment; filename=\"" . $namaFile . "\"");
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Cache-Control: max-age=0");

        // [3] STRUKTUR CSS: Mengatur Lebar Kolom (Width) & Posisi Judul di Tengah
        echo "<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">";
        echo "<head>";
        echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
        echo "<style>";
        echo "table { border-collapse: collapse; font-family: Arial, sans-serif; }";
        echo "th, td { border: 1px solid #000000; padding: 6px; vertical-align: top; }";
        
        // KOP LAPORAN DI TENGAH (Membagi rata berdasarkan colspan='3')
        echo ".kop-laporan th { border: none; background: none; text-align: center; }";
        
        echo ".header-tabel th { background-color: #f2f2f2; font-weight: bold; text-align: center; }";
        
        // RESPONSIVE WIDTH: Mengunci ukuran kolom agar lapang dan tidak berdempetan
        echo ".col-kode { width: 140px; text-align: center; }";
        echo ".col-akun { width: 420px; text-align: left; }"; 
        echo ".col-uang { width: 170px; text-align: right; }";
        
        // Style untuk baris penegas (bold)
        echo ".row-bold { font-weight: bold; background-color: #f9f9f9; }";
        echo "</style>";
        echo "</head>";
        echo "<body>";

        if ($tglawal == $tglakhir) {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal) . ' s/d ' . $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        // Cetak Kop Utama (Otomatis ke tengah karena CSS .kop-laporan th)
        echo "<table>";
        echo "<tr class='kop-laporan'><th colspan='3' style='font-size: 16px; font-weight:bold;'>{$namaperusahaan}</th></tr>";
        echo "<tr class='kop-laporan'><th colspan='3' style='font-size: 14px; font-weight:bold;'>LAPORAN POSISI KEUANGAN{$namaCetak}</th></tr>";
        echo "<tr class='kop-laporan'><th colspan='3' style='font-size: 12px;'>Periode {$Periode}</th></tr>";
        echo "<tr><td colspan='3' style='border:none;'></td></tr>"; // Baris kosong pemisah
        
        // Header Utama Tabel
        echo "<tr class='header-tabel'>";
        echo "<th class='col-kode'>KODE AKUN</th>";
        echo "<th class='col-akun'>NAMA AKUN</th>";
        echo "<th class='col-uang'>NILAI (Dalam Rp.)</th>";
        echo "</tr>";

        $total1 = 0;
        $totalaset = 0;
        $totalhutangdanmodal = 0;
        $totalasetBerjalan = 0;
        $totalhutangdanmodalBerjalan = 0;
        $kdakun_old = '';

        // [4] LOOP UNBUFFERED ROW: Hitung akumulasi data berjalan (Logika Asli)
        while ($dataBerjalan = $rsDataBerjalan->getUnbufferedRow()) {
            if (substr($dataBerjalan->kdakun, 0, 1) == '1' && $dataBerjalan->level == '1') {
                $totalasetBerjalan += $dataBerjalan->jumlah;
            }

            if ((substr($dataBerjalan->kdakun, 0, 1) == '2' || substr($dataBerjalan->kdakun, 0, 1) == '3') && $dataBerjalan->level == '1') {
                $totalhutangdanmodalBerjalan += $dataBerjalan->jumlah;
            }
        }

        // [5] LOOP UNBUFFERED ROW: Cetak data utama
        while ($data = $rsData->getUnbufferedRow()) {
            $total1 += $data->jumlah;
            
            // Konversi spasi indentasi level menggunakan Entitas HTML agar terbaca sempurna di Excel
            switch ($data->level) {
                case '1': $spasi = ''; break;
                case '2': $spasi = str_repeat('&nbsp;', 4); break;
                case '3': $spasi = str_repeat('&nbsp;', 8); break;
                case '4': $spasi = str_repeat('&nbsp;', 12); break;
                default:  $spasi = ''; break;
            }

            if (substr($data->kdakun, 0, 1) == '1' && $data->level == '1') {
                $totalaset += $data->jumlah;
            }

            if ((substr($data->kdakun, 0, 1) == '2' || substr($data->kdakun, 0, 1) == '3') && $data->level == '1') {
                $totalhutangdanmodal += $data->jumlah;
            }

            // SISIPAN SUB-TOTAL: TOTAL ASET (Mendeteksi perubahan kelompok kepala akun 1)
            if ((substr($kdakun_old, 0, 1) != substr($data->kdakun, 0, 1)) && $kdakun_old != '' && substr($kdakun_old, 0, 1) == '1') {
                echo "<tr class='row-bold'>";
                echo "<td></td>";
                echo "<td>TOTAL ASET</td>";
                // Menggunakan mso-number-format khusus akuntansi untuk penulisan tanda kurung jika minus
                echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totalaset}</td>";
                echo "</tr>";
            }

            // Penentuan cetak baris berdasarkan tingkat ringkasan level (Logika Asli)
            $isRowRendered = false;
            $nilai_bersih = '';

            if ($akunlevel3 == 1) {
                if ($data->level != 4) {
                    $nilai_bersih = ($data->level != 1) ? $data->jumlah : '';
                    $isRowRendered = true;
                }
            } else {
                $nilai_bersih = ($data->level == 4) ? $data->jumlah : '';
                $isRowRendered = true;
            }

            if ($isRowRendered) {
                // Tulis Baris Data Akun ke File Excel
                $style_induk = ($data->level == '1') ? "style='font-weight: bold; background-color: #fcfcfc;'" : "";
                $nama_akun_aman = htmlspecialchars($data->nmakun, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                
                echo "<tr {$style_induk}>";
                echo "<td class='col-kode'>{$data->kdakun}</td>";
                echo "<td class='col-akun'>{$spasi}{$nama_akun_aman}</td>";
                
                if ($nilai_bersih !== '') {
                    // mso-number-format: "\#\,\#\#0;\(\#\,\#\#0\)" mengatur positif biasa, negatif otomatis bertanda kurung ()
                    echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$nilai_bersih}</td>";
                } else {
                    echo "<td></td>";
                }
                echo "</tr>";
            }

            $kdakun_old = $data->kdakun;
            
            // Pengosongan RAM server bertahap (sangat krusial untuk jutaan data)
            if (ob_get_level() > 0) ob_flush();
            flush();
        }

        // [6] KALKULASI TOTAL AKHIR (Logika Finansial Asli Tetap Sama)
        $totallabaditahan = ($totalaset - $totalhutangdanmodal) - ($totalasetBerjalan - $totalhutangdanmodalBerjalan);
        $totallabaperiodeberjalan = $totalasetBerjalan - $totalhutangdanmodalBerjalan;
        $totalliabilitandanekuitas = $totalhutangdanmodal + $totalaset - $totalhutangdanmodal;

        // Cetak Komponen Finansial Tambahan Pasiva & Total Akhir Neraca
        $spasi_sub = str_repeat('&nbsp;', 4);
        
        echo "<tr>";
        echo "<td></td>";
        echo "<td>{$spasi_sub}Laba Ditahan</td>";
        echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totallabaditahan}</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td></td>";
        echo "<td>{$spasi_sub}Laba (Rugi) Periode Berjalan</td>";
        echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totallabaperiodeberjalan}</td>";
        echo "</tr>";

        echo "<tr class='row-bold' style='background-color: #f2f2f2;'>";
        echo "<td></td>";
        echo "<td>TOTAL LIABILITAS DAN EKUITAS</td>";
        echo "<td class='col-uang' style='font-weight: bold; mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totalliabilitandanekuitas}</td>";
        echo "</tr>";

        // [7] PENUTUP DOKUMEN HTML
        echo "</table>";
        echo "</body>";
        echo "</html>";
        exit;
    }
}
