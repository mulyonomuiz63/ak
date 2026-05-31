<?php

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Libraries\Pdf;

class LapLabaRugiController extends BaseController
{

	var $namaperusahaan = 'PT. XYZ';

	public function index()
	{
		$tglawal = $this->request->getPost('tglawal');
		$tglakhir = $this->request->getPost('tglakhir');
		$akunlevel3 = $this->request->getPost('akunlevel3');
		
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
			$tglawal = date('Y-01-01');
			$tglakhir = date('Y-m-d');
		}

		$data['tglawal'] = $tglawal;
		$data['tglakhir'] = $tglakhir;
		$data['akunlevel3'] = $akunlevel3;
		$data['idperusahaan'] = $idperusahaan;
		$data['namaperusahaan'] = $namaperusahaan;
		$data['rsData'] = $this->laporan_model->get_laplabarugi($tglawal, $tglakhir, $idperusahaan);

		return view('laporan/laplabarugi/index', $data);
	}

	public function lapLabaRugiCetak($tglawal, $tglakhir, $akunlevel3, $idperusahaan)
    {
        // [OPTIMASI]: Bebaskan limit memori dan waktu eksekusi
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

        if ($akunlevel3 == '1') {
            $namaCetak = " Ringkas";
        }else{
            $namaCetak = '';
        }

        $rsData = $this->laporan_model->get_laplabarugi($tglawal, $tglakhir, $idperusahaan);

        $pdf = new \App\Libraries\Pdf('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetMargins(20, 20, 10);
        $pdf->setPrintHeader(false);
        $pdf->AddPage();

        $title = '
			<span style="text-align:center; text-transform:uppercase; font-weight:bold; padding-top:10px;">' . $namaperusahaan . '</span><br>	
			<span style="text-align:center; font-weight:bold; padding-top:10px;">LAPORAN LABA RUGI</span>	
		';
        $pdf->SetFont('times', '', 16);
        $pdf->writeHTML($title, true, false, false, false, '');
        $pdf->SetTopMargin(15);

        if ($tglawal == $tglakhir) {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal) . ' s/d ' . $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        $title_periode = '<div style="text-align:center; font-size:14px; padding-top:10px;"> Periode ' . ($Periode) . '</div><br>';
        $pdf->SetFont('times', '', 12);
        $pdf->writeHTML($title_periode, true, false, false, false, '');

        // Setup Variabel Chunking HTML
        $tableHeader = '<table border="0" cellpadding="3"><tbody>';
        $tableFooter = '</tbody></table>';
        $htmlChunk = $tableHeader;
        $rowCount = 0;
        $chunkSize = 400; // Eksekusi ke PDF per 400 baris

        $total1 = 0;
        $kdakun_old = '';

        $totaldesc = '';
        $totaldesc2 = '';
        $totalrupiah = 0;

        $totalpendapatan = 0;
        $totalpengeluaran = 0;
        
        // Setup Variabel Tangkapan Pajak (Pengganti Looping Kedua)
        $bebanpajakpenghasilan = 0;
        $pajakHtmlPdf = '';

        // [OPTIMASI]: Gunakan Unbuffered Query
        while ($data = $rsData->getUnbufferedRow()) {
            $total1 += $data->jumlah;
            $font = '';

            // TANGKAP PAJAK (Berbarengan di loop pertama)
            if ($data->kdakun == "730000" || $data->kdakun == "73000") {
                $bebanpajakpenghasilan = $data->jumlah;
                // Kita akan gunakan $spasi berdasarkan levelnya nanti di bawah, jadi kita simpan dulu nilainya.
            }

            if ((substr($kdakun_old, 0, 1) != substr($data->kdakun, 0, 1)) && $kdakun_old != '' && substr($kdakun_old, 0, 1) != '7') {
                $htmlChunk .= '
                        <tr height="50" style="background-color: #E5E4E2;">
                            <td width="78%"  style=" font-weight: bold; text-align:left;">' . $totaldesc . '</td>
                            <td width="22%" style=" font-weight: bold; text-align:right;">' . ($totalrupiah >= 0 ? number_format($totalrupiah, 0, ",", ".") : "(" . number_format($totalrupiah * -1, 0, ",", ".") . ")") . '</td>
                        </tr>';

                if ($totaldesc == "TOTAL PENDAPATAN") {
                    $total_pendapatan = $totalrupiah;
                }

                if ($totaldesc == "TOTAL HARGA POKOK PENJUALAN") {
                    $total_harga_pokok_penjualan = $totalrupiah;
                    $totallabarugikotor = $total_pendapatan - $total_harga_pokok_penjualan;
                    $htmlChunk .= '
                        <tr height="50" style="background-color: #E5E4E2;">
                            <td width="78%"  style=" font-weight: bold; text-align:left;">TOTAL LABA RUGI KOTOR</td>
                            <td width="22%" style=" font-weight: bold; text-align:right;">' . ($totallabarugikotor >= 0 ? number_format($totallabarugikotor, 0, ",", ".") : "(" . number_format($totallabarugikotor * -1, 0, ",", ".") . ")") . '</td>
                        </tr>';
                }
                $totalrupiah = 0;
            }

            //khusus PENDAPATAN DAN BEBAN LAINNYA
            if ((substr($kdakun_old, 0, 2) != substr($data->kdakun, 0, 2)) && $kdakun_old != '' && substr($kdakun_old, 0, 1) == '7' && $kdakun_old != '70000') {
                $totalrupiah = 0;
            }

            switch ($data->level) {
                case '1':
                    $font = ' font-weight: bold;';
                    $spasi = '';
                    $totaldesc = 'TOTAL ' . strtoupper($data->nmakun);
                    break;
                case '2':
                    $font = '';
                    $spasi = str_repeat('&nbsp;', 4);
                    $totaldesc2 = 'TOTAL ' . strtoupper($data->nmakun);
                    break;
                case '3':
                    $font = '';
                    $spasi = str_repeat('&nbsp;', 8);
                    break;
                case '4':
                    $font = '';
                    $spasi = str_repeat('&nbsp;', 12);
                    $totalrupiah += $data->jumlah;
                    break;
                default:
                    $font = '';
                    break;
            }

            // Lanjutan Tangkap Pajak dengan Format HTML
            if ($data->kdakun == "730000" || $data->kdakun == "73000") {
                $pajakHtmlPdf = '
                        <tr style="color:#000000;" height="50" >
                            <td width="78%" style="' . $font . ' text-align:left;">' . $spasi . $data->kdakun . ' - ' . $data->nmakun . '</td>
                            <td width="22%" style=" text-align:right;">' . ($data->jumlah >= 0 ? number_format($data->jumlah, 0, ",", ".") : "(" . number_format($data->jumlah * -1, 0, ",", ".") . ")") . '</td>
                        </tr>';
            }

            if ((substr($data->kdakun, 0, 1) == '4' || substr($data->kdakun, 0, 2) == '71') && $data->level == '4') {
                $totalpendapatan += $data->jumlah;
            } elseif ((substr($data->kdakun, 0, 1) == '5' || substr($data->kdakun, 0, 1) == '6' || substr($data->kdakun, 0, 2) == '72') && $data->level == '4') {
                $totalpengeluaran += $data->jumlah;
            } else {
                $totalpengeluaran += 0;
            }

            if ((substr($kdakun_old, 0, 1) != substr($data->kdakun, 0, 1)) and $kdakun_old != '') {
                $htmlChunk .= '
                        <tr height="50">
                            <td width="78%" style=" font-weight: bold; text-align:left;"></td>
                            <td width="22%" style=" font-weight: bold; text-align:right;"></td>
                        </tr>';
            }

            if ($akunlevel3 == 1) {
                if ($data->level != 4) {
                    $htmlChunk .= '
                        <tr>
                            <td width="78%" style="' . $font . ' text-align:left;">' . $spasi . $data->kdakun . ' - ' . $data->nmakun . '</td>
                            <td width="22%" style="' . $font . ' text-align:right;">' . ($data->kdakun != '70000' && $data->level != 1 ? ($data->jumlah >= 0 ? number_format($data->jumlah, 0, ",", ".") : "(" . number_format($data->jumlah * -1, 0, ",", ".") . ")") : "") . '</td>
                        </tr>';
                }
            } else {
                if ($data->kdakun != "730000" && $data->kdakun != "73000") {
                    $htmlChunk .= '
                    <tr>
                        <td width="78%" style="' . $font . ' text-align:left;">' . $spasi . $data->kdakun . ' - ' . $data->nmakun . '</td>
                        <td width="22%" style="' . $font . ' text-align:right;">' . ($data->kdakun != '70000' && $data->level == 4 ? ($data->jumlah >= 0 ? number_format($data->jumlah, 0, ",", ".") : "(" . number_format($data->jumlah * -1, 0, ",", ".") . ")") : "") . '</td>
                    </tr>';
                }
            }

            $kdakun_old = $data->kdakun;
            $rowCount++;

            // [OPTIMASI]: Chunk render HTML
            if ($rowCount % $chunkSize == 0) {
                $htmlChunk .= $tableFooter;
                $pdf->SetFont('times', '', 10);
                $pdf->writeHTML($htmlChunk, true, false, false, false, '');
                $htmlChunk = $tableHeader; // Buka tabel baru
            }
        }

        // Tulis sisa akhir laporan
        if ($totaldesc2 != "TOTAL BEBAN LAINNYA") {
            $htmlChunk .= '
                        <tr height="50" style="background-color: #E5E4E2;">
                            <td width="78%" style=" font-weight: bold; text-align:left;">' . $totaldesc2 . '</td>
                            <td width="22%" style=" font-weight: bold; text-align:right;">' . ($totalrupiah >= 0 ? number_format($totalrupiah, 0, ",", ".") : "(" . number_format($totalrupiah * -1, 0, ",", ".") . ")") . '</td>
                        </tr>';
        }

        $totallabasebelumpajak = $totalpendapatan - $totalpengeluaran;
        $htmlChunk .= '
                        <tr style="background-color:#E5E4E2; color:#000000;" height="50" >
                            <td width="78%" style=" font-weight: bold; text-align:left;">LABA (RUGI) SEBELUM PAJAK PENGHASILAN</td>
                            <td width="22%" style=" font-weight: bold; text-align:right;">' . ($totallabasebelumpajak >= 0 ? number_format($totallabasebelumpajak, 0, ",", ".") : "(" . number_format($totallabasebelumpajak * -1, 0, ",", ".") . ")") . '</td>
                        </tr>';

        // Masukkan HTML Pajak yang sudah kita tangkap tadi
        $htmlChunk .= $pajakHtmlPdf;

        $totallabasetelahpajak = ($totalpendapatan - $totalpengeluaran) - $bebanpajakpenghasilan;
        $htmlChunk .= '
                        <tr style="background-color:#055F93; color:#ffffff;" height="50" >
                            <td width="78%" style=" font-weight: bold; text-align:left;">LABA (RUGI) SETELAH PAJAK PENGHASILAN</td>
                            <td width="22%" style=" font-weight: bold; text-align:right;">' . ($totallabasetelahpajak >= 0 ? number_format($totallabasetelahpajak, 0, ",", ".") : "(" . number_format($totallabasetelahpajak * -1, 0, ",", ".") . ")") . '</td>
                        </tr>';

        $htmlChunk .= $tableFooter;

        // Cetak sisa tabel terakhir ke PDF
        $pdf->SetTopMargin(35);
        $pdf->SetFont('times', '', 10);
        $pdf->writeHTML($htmlChunk, true, false, false, false, '');
        $bulantahun = bulan_tahun($tglawal) . ' - ' . bulan_tahun($tglakhir);
        $namaPerusahaan = ucwords(strtolower($namaperusahaan));
        $namaPerusahaan = preg_replace(['/\bPt\b/', '/\bCv\b/'], ['PT', 'CV'], $namaPerusahaan);
        $namaFile = 'Laporan Laba Rugi'.$namaCetak.' ' . $namaPerusahaan . ' ' . $bulantahun . '.pdf';
        $pdf->Output($namaFile, 'I');
        exit;
    }


	public function lapLabaRugiugiExcel($tglawal, $tglakhir, $akunlevel3, $idperusahaan)
    {
        $menuaktif = 'laplabarugi';

        // [1] BYPASS MEMORY: Bebaskan memori dan waktu eksekusi untuk data skala besar
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
        
        if ($akunlevel3 == '1') {
            $namaCetak = " Ringkas";
        } else {
            $namaCetak = '';
        }

        $rsData = $this->laporan_model->get_laplabarugi($tglawal, $tglakhir, $idperusahaan);

        // [2] SETUP HEADER HTTP: Menggunakan .xls berbasis HTML Stream
        $bulantahun     = bulan_tahun($tglawal) . ' - ' . bulan_tahun($tglakhir);
        $namaPerusahaan = ucwords(strtolower($namaperusahaan));
        $namaPerusahaan = preg_replace(['/\bPt\b/', '/\bCv\b/'], ['PT', 'CV'], $namaPerusahaan);
        $namaFile = 'Laporan Laba Rugi' . $namaCetak . ' ' . $namaPerusahaan . ' ' . $bulantahun . '.xls';
        
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
        
        // Style untuk baris penegas / total (bold)
        echo ".row-bold { font-weight: bold; background-color: #f5f5f5; }";
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
        echo "<tr class='kop-laporan'><th colspan='3' style='font-size: 14px; font-weight:bold;'>LAPORAN LABA RUGI{$namaCetak}</th></tr>";
        echo "<tr class='kop-laporan'><th colspan='3' style='font-size: 12px;'>Periode {$Periode}</th></tr>";
        echo "<tr><td colspan='3' style='border:none;'></td></tr>"; // Baris kosong pemisah
        
        // Header Utama Tabel
        echo "<tr class='header-tabel'>";
        echo "<th class='col-kode'>KODE AKUN</th>";
        echo "<th class='col-akun'>NAMA AKUN</th>";
        echo "<th class='col-uang'>NILAI (Dalam Rp.)</th>";
        echo "</tr>";

        $total1 = 0;
        $kdakun_old = '';

        $totaldesc = '';
        $totaldesc2 = '';
        $totalrupiah = 0;

        $totalpendapatan = 0;
        $totalpengeluaran = 0;

        // Tangkapan baris Pajak
        $bebanpajakpenghasilan = 0;
        $pajakRowHtml = ""; 

        // [4] LOOP UNBUFFERED ROW: Cetak data utama secara hemat memori
        while ($data = $rsData->getUnbufferedRow()) {
            $total1 += $data->jumlah;
            $spasi = '';

            // TANGKAP PAJAK (Logika Asli Tetap)
            if ($data->kdakun == "730000" || $data->kdakun == "73000") {
                $bebanpajakpenghasilan = $data->jumlah;
            }

            // Sisipan Baris Sub-total Kelompok Utama (Logika Perhitungan Asli Tetap)
            if ((substr($kdakun_old, 0, 1) != substr($data->kdakun, 0, 1)) && $kdakun_old != '' && substr($kdakun_old, 0, 1) != '7') {
                echo "<tr class='row-bold'>";
                echo "<td></td>";
                echo "<td>{$totaldesc}</td>";
                echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totalrupiah}</td>";
                echo "</tr>";
                
                if ($totaldesc == "TOTAL PENDAPATAN") {
                    $total_pendapatan = $totalrupiah;
                }

                if ($totaldesc == "TOTAL HARGA POKOK PENJUALAN") {
                    $total_harga_pokok_penjualan = $totalrupiah;
                    $totallabarugikotor = $total_pendapatan - $total_harga_pokok_penjualan;
                    
                    echo "<tr class='row-bold' style='background-color: #eaf2ff;'>";
                    echo "<td></td>";
                    echo "<td>TOTAL LABA RUGI KOTOR</td>";
                    echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totallabarugikotor}</td>";
                    echo "</tr>";
                }
                $totalrupiah = 0;
            }

            if ((substr($kdakun_old, 0, 2) != substr($data->kdakun, 0, 2)) && $kdakun_old != '' && substr($kdakun_old, 0, 1) == '7' && $kdakun_old != '70000') {
                $totalrupiah = 0;
            }

            // Konversi spasi indentasi level menggunakan Entitas HTML agar terbaca sempurna di Excel
            switch ($data->level) {
                case '1':
                    $spasi = '';
                    $totaldesc = 'TOTAL ' . strtoupper($data->nmakun);
                    break;
                case '2':
                    $spasi = str_repeat('&nbsp;', 4);
                    $totaldesc2 = 'TOTAL ' . strtoupper($data->nmakun);
                    break;
                case '3':
                    $spasi = str_repeat('&nbsp;', 8);
                    break;
                case '4':
                    $spasi = str_repeat('&nbsp;', 12);
                    $totalrupiah += $data->jumlah;
                    break;
                default:
                    $spasi = '';
                    break;
            }

            // TANGKAP STRUKTUR HTML PAJAK UNTUK DICETAK DI AKHIR (Logika Asli Tetap)
            if ($data->kdakun == "730000" || $data->kdakun == "73000") {
                $nama_pajak_aman = htmlspecialchars($data->nmakun, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $pajakRowHtml = "<tr>";
                $pajakRowHtml .= "<td class='col-kode'>{$data->kdakun}</td>";
                $pajakRowHtml .= "<td class='col-akun'>{$spasi}{$nama_pajak_aman}</td>";
                $pajakRowHtml .= "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$data->jumlah}</td>";
                $pajakRowHtml .= "</tr>";
            }

            // Akumulasi Finansial (Logika Asli Tetap)
            if ((substr($data->kdakun, 0, 1) == '4' || substr($data->kdakun, 0, 2) == '71') && $data->level == '4') {
                $totalpendapatan += $data->jumlah;
            } elseif ((substr($data->kdakun, 0, 1) == '5' || substr($data->kdakun, 0, 1) == '6' || substr($data->kdakun, 0, 2) == '72') && $data->level == '4') {
                $totalpengeluaran += $data->jumlah;
            } else {
                $totalpengeluaran += 0;
            }

            if ((substr($kdakun_old, 0, 1) != substr($data->kdakun, 0, 1)) && $kdakun_old != '') {
                echo "<tr><td colspan='3' style='border:none;'></td></tr>"; // Baris pemisah kosong asli
            }

            // Cetak Baris Akun Individu ke File Excel Berdasarkan Tingkat Ringkasan Level (Logika Asli)
            $nama_akun_aman = htmlspecialchars($data->nmakun, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $style_induk = ($data->level == '1') ? "style='font-weight: bold; background-color: #fcfcfc;'" : "";

            if ($akunlevel3 == 1) {
                if ($data->level != '4') {
                    $nilai = ($data->kdakun != '70000' && $data->level != '1') ? $data->jumlah : '';
                    
                    echo "<tr {$style_induk}>";
                    echo "<td class='col-kode'>{$data->kdakun}</td>";
                    echo "<td class='col-akun'>{$spasi}{$nama_akun_aman}</td>";
                    if ($nilai !== '') {
                        echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$nilai}</td>";
                    } else {
                        echo "<td></td>";
                    }
                    echo "</tr>";
                }
            } else {
                if ($data->kdakun != "730000" && $data->kdakun != "73000") {
                    $nilai = ($data->kdakun != '70000' && $data->level == 4) ? $data->jumlah : '';
                    
                    echo "<tr {$style_induk}>";
                    echo "<td class='col-kode'>{$data->kdakun}</td>";
                    echo "<td class='col-akun'>{$spasi}{$nama_akun_aman}</td>";
                    if ($nilai !== '') {
                        echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$nilai}</td>";
                    } else {
                        echo "<td></td>";
                    }
                    echo "</tr>";
                }
            }

            $kdakun_old = $data->kdakun;

            // Pengosongan RAM server bertahap (sangat krusial untuk jutaan data)
            if (ob_get_level() > 0) ob_flush();
            flush();
        }

        // [5] Sisa Baris Akhir Penutup Kelompok Akun (Logika Asli)
        if ($totaldesc2 != "TOTAL BEBAN LAINNYA") {
            echo "<tr class='row-bold'>";
            echo "<td></td>";
            echo "<td>{$totaldesc2}</td>";
            echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totalrupiah}</td>";
            echo "</tr>";
        }

        // Hitung Hasil Akhir Finansial (Laba Rugi Sebelum & Setelah Pajak)
        $totallabasebelumpajak = $totalpendapatan - $totalpengeluaran;
        
        echo "<tr><td colspan='3' style='border:none;'></td></tr>"; // Baris Jeda Kosong
        echo "<tr class='row-bold' style='background-color: #e0f0d9;'>";
        echo "<td></td>";
        echo "<td>LABA (RUGI) SEBELUM PAJAK PENGHASILAN</td>";
        echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totallabasebelumpajak}</td>";
        echo "</tr>";
        
        // Keluarkan baris pajak yang ditangkap di dalam loop (jika ada)
        if (!empty($pajakRowHtml)) {
            echo $pajakRowHtml;
        }

        $totallabasetelahpajak = ($totalpendapatan - $totalpengeluaran) - $bebanpajakpenghasilan;
        
        echo "<tr class='row-bold' style='background-color: #d0ebd0;'>";
        echo "<td></td>";
        echo "<td>LABA (RUGI) SETELAH PAJAK PENGHASILAN</td>";
        echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totallabasetelahpajak}</td>";
        echo "</tr>";

        // [6] PENUTUP DOKUMEN HTML
        echo "</table>";
        echo "</body>";
        echo "</html>";
        exit;
    }
}