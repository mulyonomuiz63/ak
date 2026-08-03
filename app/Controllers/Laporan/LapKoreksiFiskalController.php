<?php

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;

class LapKoreksiFiskalController extends BaseController
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
		$data['rsData'] = $this->laporan_model->get_lapkoreksifiskal($tglawal, $tglakhir, $idperusahaan);

		return view('laporan/lapkoreksifiskal/index', $data);
	}

	public function lapKoreksiFiskalCetak($tglawal, $tglakhir, $akunlevel3, $idperusahaan)
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
        if ($tglawal == $tglakhir) {
            $periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $periode = $this->laporan_model->tglindonesialengkap($tglawal) . ' s/d ' . $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        $rsData = $this->laporan_model->get_lapkoreksifiskal($tglawal, $tglakhir, $idperusahaan);

        $pdf = new \App\Libraries\Pdf('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, $namaperusahaan, 'LAPORAN KOREKSI FISKAL', $periode);
        $pdf->setPrintHeader(true);
        $pdf->AddPage();

        // Setup Header Tabel dengan 5 Kolom
        $tableHeader = '
        <table border="1" cellpadding="4">
            <thead>
                <tr style="background-color:#055F93; color: white; font-weight: bold; text-align: center;">
                    <th width="40%">Kode & Nama Akun</th>
                    <th width="15%">Jumlah (Rp)</th>
                    <th width="15%">Korfis Positif (Rp)</th>
                    <th width="15%">Korfis Negatif (Rp)</th>
                    <th width="15%">Fiskal (Rp)</th>
                </tr>
            </thead>
            <tbody>';
        $tableFooter = '</tbody></table>';
        
        $htmlChunk = $tableHeader;
        $rowCount = 0;
        $chunkSize = 400; // Eksekusi ke PDF per 400 baris

        $total1 = 0;
        $kdakun_old = '';

        $totaldesc = '';
        $totaldesc2 = '';
        $totalrupiah = 0;
        $totalfiskal = 0; // Tambahan variabel total fiskal

        $totalpendapatan = 0;
        $totalpengeluaran = 0;
        
        // Setup Variabel Tangkapan Pajak
        $bebanpajakpenghasilan = 0;
        $pajakHtmlPdf = '';

        // [OPTIMASI]: Gunakan Unbuffered Query
        while ($data = $rsData->getUnbufferedRow()) {
            $total1 += $data->jumlah;
            $font = '';

            // Hitung Korfis & Fiskal sesuai logika VIEW
            $korfis_pos = isset($data->koreksi_positif) ? (float)$data->koreksi_positif : 0;
            $korfis_neg = isset($data->koreksi_negatif) ? (float)$data->koreksi_negatif : 0;
            
            if (substr($data->kdakun, 0, 1) == '4' || substr($data->kdakun, 0, 2) == '71') {
                $fiskal_val = (float)$data->jumlah + $korfis_pos - $korfis_neg;
            } elseif (substr($data->kdakun, 0, 1) == '5' || substr($data->kdakun, 0, 1) == '6' || substr($data->kdakun, 0, 2) == '72') {
                $fiskal_val = (float)$data->jumlah - $korfis_pos + $korfis_neg;
            } else {
                $fiskal_val = 0;
            }

            // Pengecekan Grup Akun untuk mencetak Baris Subtotal
            if ((substr($kdakun_old, 0, 1) != substr($data->kdakun, 0, 1)) && $kdakun_old != '' && substr($kdakun_old, 0, 1) != '7') {
                $htmlChunk .= '
                        <tr style="background-color: #E5E4E2;">
                            <td width="40%" style="font-weight: bold; text-align:left;">' . $totaldesc . '</td>
                            <td width="15%" style="font-weight: bold; text-align:right;">' . ($totalrupiah >= 0 ? number_format($totalrupiah, 0, ",", ".") : "(" . number_format($totalrupiah * -1, 0, ",", ".") . ")") . '</td>
                            <td width="30%" colspan="2"></td>
                            <td width="15%" style="font-weight: bold; text-align:right;">' . ($totalfiskal >= 0 ? number_format($totalfiskal, 0, ",", ".") : "(" . number_format($totalfiskal * -1, 0, ",", ".") . ")") . '</td>
                        </tr>';

                if ($totaldesc == "TOTAL PENDAPATAN") {
                    $total_pendapatan = $totalrupiah;
                }

                if ($totaldesc == "TOTAL HARGA POKOK PENJUALAN") {
                    $total_harga_pokok_penjualan = $totalrupiah;
                    $totallabarugikotor = $total_pendapatan - $total_harga_pokok_penjualan;
                    $htmlChunk .= '
                        <tr style="background-color: #E5E4E2;">
                            <td width="40%" style="font-weight: bold; text-align:left;">TOTAL LABA RUGI KOTOR</td>
                            <td width="15%" style="font-weight: bold; text-align:right;">' . ($totallabarugikotor >= 0 ? number_format($totallabarugikotor, 0, ",", ".") : "(" . number_format($totallabarugikotor * -1, 0, ",", ".") . ")") . '</td>
                            <td width="45%" colspan="3"></td>
                        </tr>';
                }
                $totalrupiah = 0;
                $totalfiskal = 0;
            }

            // khusus PENDAPATAN DAN BEBAN LAINNYA
            if ((substr($kdakun_old, 0, 2) != substr($data->kdakun, 0, 2)) && $kdakun_old != '' && substr($kdakun_old, 0, 1) == '7' && $kdakun_old != '70000') {
                $totalrupiah = 0;
                $totalfiskal = 0;
            }

            // Hitung Pendapatan & Pengeluaran Utama
            if ((substr($data->kdakun, 0, 1) == '4' || substr($data->kdakun, 0, 2) == '71') && $data->level == '4') {
                $totalpendapatan += $data->jumlah;
            } elseif ((substr($data->kdakun, 0, 1) == '5' || substr($data->kdakun, 0, 1) == '6' || substr($data->kdakun, 0, 2) == '72') && $data->level == '4') {
                $totalpengeluaran += $data->jumlah;
            }

            // Format Huruf dan Spasi Leveling
            switch ($data->level) {
                case '1':
                    $font = 'font-weight: bold;';
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
                    $totalfiskal += $fiskal_val;
                    break;
                default:
                    $font = '';
                    break;
            }

            // TANGKAP PAJAK (Disimpan ke variabel agar dirender paling bawah)
            if ($data->kdakun == "730000" || $data->kdakun == "73000") {
                $bebanpajakpenghasilan = $data->jumlah;
                $fiskal_pajak = (float)$data->jumlah + $korfis_pos - $korfis_neg; // Rumus pajak sesuai view
                $pajakHtmlPdf = '
                        <tr style="color:#000000;">
                            <td width="40%" style="' . $font . ' text-align:left;">' . $spasi . $data->kdakun . ' - Beban Pajak Penghasilan</td>
                            <td width="15%" style="text-align:right;">' . ($data->jumlah >= 0 ? number_format($data->jumlah, 0, ",", ".") : "(" . number_format($data->jumlah * -1, 0, ",", ".") . ")") . '</td>
                            <td width="15%" style="text-align:right;">' . ($korfis_pos >= 0 ? number_format($korfis_pos, 0, ",", ".") : "(" . number_format($korfis_pos * -1, 0, ",", ".") . ")") . '</td>
                            <td width="15%" style="text-align:right;">' . ($korfis_neg >= 0 ? number_format($korfis_neg, 0, ",", ".") : "(" . number_format($korfis_neg * -1, 0, ",", ".") . ")") . '</td>
                            <td width="15%" style="text-align:right;">' . ($fiskal_pajak >= 0 ? number_format($fiskal_pajak, 0, ",", ".") : "(" . number_format($fiskal_pajak * -1, 0, ",", ".") . ")") . '</td>
                        </tr>';
            }

            // Konten Baris (Cetak Ringkas / Detail)
            if ($data->kdakun != "730000" && $data->kdakun != "73000") {
                $renderBaris = false;
                
                if ($akunlevel3 == 1) {
                    if ($data->level != 4) { $renderBaris = true; }
                } else {
                    $renderBaris = true;
                }

                if ($renderBaris) {
                    // Logika menampilkan angka (sesuai kondisi ternary view)
                    $tampilAngka = ($data->kdakun != '70000' && $data->kdakun != '700000' && $data->level != 1);

                    $v_jumlah = $tampilAngka ? ($data->jumlah >= 0 ? number_format($data->jumlah, 0, ",", ".") : "(" . number_format($data->jumlah * -1, 0, ",", ".") . ")") : "";
                    $v_pos    = $tampilAngka ? ($korfis_pos >= 0 ? number_format($korfis_pos, 0, ",", ".") : "(" . number_format($korfis_pos * -1, 0, ",", ".") . ")") : "";
                    $v_neg    = $tampilAngka ? ($korfis_neg >= 0 ? number_format($korfis_neg, 0, ",", ".") : "(" . number_format($korfis_neg * -1, 0, ",", ".") . ")") : "";
                    $v_fisk   = $tampilAngka ? ($fiskal_val >= 0 ? number_format($fiskal_val, 0, ",", ".") : "(" . number_format($fiskal_val * -1, 0, ",", ".") . ")") : "";

                    $htmlChunk .= '
                    <tr>
                        <td width="40%" style="' . $font . ' text-align:left;">' . $spasi . $data->kdakun . ' - ' . $data->nmakun . '</td>
                        <td width="15%" style="' . $font . ' text-align:right;">' . $v_jumlah . '</td>
                        <td width="15%" style="' . $font . ' text-align:right;">' . $v_pos . '</td>
                        <td width="15%" style="' . $font . ' text-align:right;">' . $v_neg . '</td>
                        <td width="15%" style="' . $font . ' text-align:right;">' . $v_fisk . '</td>
                    </tr>';
                }
            }

            $kdakun_old = $data->kdakun;
            $rowCount++;

            // [OPTIMASI]: Chunk render HTML
            if ($rowCount % $chunkSize == 0) {
                $htmlChunk .= $tableFooter;
                $pdf->SetFont('times', '', 9); 
                $pdf->writeHTML($htmlChunk, true, false, false, false, '');
                $htmlChunk = $tableHeader; // Buka tabel baru
            }
        }

        // Tulis sisa akhir laporan (Subtotal Terakhir)
        if ($totaldesc2 != "TOTAL BEBAN LAINNYA") {
            $htmlChunk .= '
                        <tr style="background-color: #E5E4E2;">
                            <td width="40%" style="font-weight: bold; text-align:left;">' . $totaldesc2 . '</td>
                            <td width="15%" style="font-weight: bold; text-align:right;">' . ($totalrupiah >= 0 ? number_format($totalrupiah, 0, ",", ".") : "(" . number_format($totalrupiah * -1, 0, ",", ".") . ")") . '</td>
                            <td width="30%" colspan="2"></td>
                            <td width="15%" style="font-weight: bold; text-align:right;">' . ($totalfiskal >= 0 ? number_format($totalfiskal, 0, ",", ".") : "(" . number_format($totalfiskal * -1, 0, ",", ".") . ")") . '</td>
                        </tr>';
        }

        // Laba/Rugi Sebelum Pajak
        $totallabasebelumpajak = $totalpendapatan - $totalpengeluaran;
        $htmlChunk .= '
                        <tr style="background-color:#E5E4E2; color:#000000;">
                            <td width="40%" style="font-weight: bold; text-align:left;">LABA (RUGI) SEBELUM PAJAK PENGHASILAN</td>
                            <td width="15%" style="font-weight: bold; text-align:right;">' . ($totallabasebelumpajak >= 0 ? number_format($totallabasebelumpajak, 0, ",", ".") : "(" . number_format($totallabasebelumpajak * -1, 0, ",", ".") . ")") . '</td>
                            <td width="45%" colspan="3"></td>
                        </tr>';

        // Masukkan HTML Pajak yang sudah ditangkap di atas
        $htmlChunk .= $pajakHtmlPdf;

        // Laba/Rugi Setelah Pajak
        $totallabasetelahpajak = ($totalpendapatan - $totalpengeluaran) - $bebanpajakpenghasilan;
        $htmlChunk .= '
                        <tr style="background-color:#055F93; color:#ffffff;">
                            <td width="40%" style="font-weight: bold; text-align:left;">LABA (RUGI) SETELAH PAJAK PENGHASILAN</td>
                            <td width="15%" style="font-weight: bold; text-align:right;">' . ($totallabasetelahpajak >= 0 ? number_format($totallabasetelahpajak, 0, ",", ".") : "(" . number_format($totallabasetelahpajak * -1, 0, ",", ".") . ")") . '</td>
                            <td width="45%" colspan="3"></td>
                        </tr>';

        $htmlChunk .= $tableFooter;

        // Cetak sisa tabel terakhir ke PDF
        $pdf->SetTopMargin(35);
        $pdf->SetFont('times', '', 9);
        $pdf->writeHTML($htmlChunk, true, false, false, false, '');
        $bulantahun = bulan_tahun($tglawal) . ' - ' . bulan_tahun($tglakhir);
        $namaPerusahaan = ucwords(strtolower($namaperusahaan));
        $namaPerusahaan = preg_replace(['/\bPt\b/', '/\bCv\b/'], ['PT', 'CV'], $namaPerusahaan);
        $namaFile = 'Laporan Koreksi Fiskal'.$namaCetak.' ' . $namaPerusahaan . ' ' . $bulantahun . '.pdf';
        $pdf->Output($namaFile, 'I');
        exit;
    }


	public function lapKoreksiFiskalExcel($tglawal, $tglakhir, $akunlevel3, $idperusahaan)
    {
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

        $rsData = $this->laporan_model->get_lapkoreksifiskal($tglawal, $tglakhir, $idperusahaan);

        // [2] SETUP HEADER HTTP: Menggunakan .xls berbasis HTML Stream
        $bulantahun     = bulan_tahun($tglawal) . ' - ' . bulan_tahun($tglakhir);
        $namaPerusahaan = ucwords(strtolower($namaperusahaan));
        $namaPerusahaan = preg_replace(['/\bPt\b/', '/\bCv\b/'], ['PT', 'CV'], $namaPerusahaan);
        $namaFile = 'Laporan Koreksi Fiskal' . $namaCetak . ' ' . $namaPerusahaan . ' ' . $bulantahun . '.xls';
        
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
        
        // KOP LAPORAN DI TENGAH (Membagi rata berdasarkan colspan='6')
        echo ".kop-laporan th { border: none; background: none; text-align: center; }";
        
        echo ".header-tabel th { background-color: #f2f2f2; font-weight: bold; text-align: center; vertical-align: middle; }";
        
        // RESPONSIVE WIDTH: Mengunci ukuran kolom agar lapang dan tidak berdempetan
        echo ".col-kode { width: 120px; text-align: center; }";
        echo ".col-akun { width: 350px; text-align: left; }"; 
        echo ".col-num { width: 140px; text-align: right; }"; // Untuk 4 kolom angka
        
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
        echo "<tr class='kop-laporan'><th colspan='6' style='font-size: 16px; font-weight:bold;'>{$namaperusahaan}</th></tr>";
        echo "<tr class='kop-laporan'><th colspan='6' style='font-size: 14px; font-weight:bold;'>LAPORAN KOREKSI FISKAL{$namaCetak}</th></tr>";
        echo "<tr class='kop-laporan'><th colspan='6' style='font-size: 12px;'>Periode {$Periode}</th></tr>";
        echo "<tr><td colspan='6' style='border:none;'></td></tr>"; // Baris kosong pemisah
        
        // Header Utama Tabel (Disesuaikan menjadi 6 kolom)
        echo "<tr class='header-tabel'>";
        echo "<th class='col-kode'>KODE AKUN</th>";
        echo "<th class='col-akun'>NAMA AKUN</th>";
        echo "<th class='col-num'>JUMLAH (Rp)</th>";
        echo "<th class='col-num'>KORFIS POSITIF (Rp)</th>";
        echo "<th class='col-num'>KORFIS NEGATIF (Rp)</th>";
        echo "<th class='col-num'>FISKAL (Rp)</th>";
        echo "</tr>";

        $total1 = 0;
        $kdakun_old = '';

        $totaldesc = '';
        $totaldesc2 = '';
        $totalrupiah = 0;
        $totalfiskal = 0; // Tambahan variabel Total Fiskal

        $totalpendapatan = 0;
        $totalpengeluaran = 0;

        // Tangkapan baris Pajak
        $bebanpajakpenghasilan = 0;
        $pajakRowHtml = ""; 

        // [4] LOOP UNBUFFERED ROW: Cetak data utama secara hemat memori
        while ($data = $rsData->getUnbufferedRow()) {
            $total1 += $data->jumlah;
            $spasi = '';

            // Hitung Nilai Korfis & Fiskal sesuai logika VIEW
            $korfis_pos = isset($data->koreksi_positif) ? (float)$data->koreksi_positif : 0;
            $korfis_neg = isset($data->koreksi_negatif) ? (float)$data->koreksi_negatif : 0;
            
            if (substr($data->kdakun, 0, 1) == '4' || substr($data->kdakun, 0, 2) == '71') {
                $fiskal_val = (float)$data->jumlah + $korfis_pos - $korfis_neg;
            } elseif (substr($data->kdakun, 0, 1) == '5' || substr($data->kdakun, 0, 1) == '6' || substr($data->kdakun, 0, 2) == '72') {
                $fiskal_val = (float)$data->jumlah - $korfis_pos + $korfis_neg;
            } else {
                $fiskal_val = 0;
            }

            // Sisipan Baris Sub-total Kelompok Utama
            if ((substr($kdakun_old, 0, 1) != substr($data->kdakun, 0, 1)) && $kdakun_old != '' && substr($kdakun_old, 0, 1) != '7') {
                echo "<tr class='row-bold'>";
                echo "<td></td>";
                echo "<td>{$totaldesc}</td>";
                echo "<td class='col-num' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totalrupiah}</td>";
                echo "<td colspan='2'></td>"; // 2 Kolom kosong agar selaras dengan header
                echo "<td class='col-num' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totalfiskal}</td>";
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
                    echo "<td class='col-num' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totallabarugikotor}</td>";
                    echo "<td colspan='3'></td>";
                    echo "</tr>";
                }
                $totalrupiah = 0;
                $totalfiskal = 0;
            }

            if ((substr($kdakun_old, 0, 2) != substr($data->kdakun, 0, 2)) && $kdakun_old != '' && substr($kdakun_old, 0, 1) == '7' && $kdakun_old != '70000') {
                $totalrupiah = 0;
                $totalfiskal = 0;
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
                    $totalfiskal += $fiskal_val;
                    break;
                default:
                    $spasi = '';
                    break;
            }

            // TANGKAP STRUKTUR HTML PAJAK UNTUK DICETAK DI AKHIR (Sesuai 6 Kolom)
            if ($data->kdakun == "730000" || $data->kdakun == "73000") {
                $bebanpajakpenghasilan = $data->jumlah;
                $fiskal_pajak = (float)$data->jumlah + $korfis_pos - $korfis_neg; // Rumus pajak

                $pajakRowHtml = "<tr>";
                $pajakRowHtml .= "<td class='col-kode'>{$data->kdakun}</td>";
                $pajakRowHtml .= "<td class='col-akun'>{$spasi}Beban Pajak Penghasilan</td>";
                $pajakRowHtml .= "<td class='col-num' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$data->jumlah}</td>";
                $pajakRowHtml .= "<td class='col-num' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$korfis_pos}</td>";
                $pajakRowHtml .= "<td class='col-num' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$korfis_neg}</td>";
                $pajakRowHtml .= "<td class='col-num' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$fiskal_pajak}</td>";
                $pajakRowHtml .= "</tr>";
            }

            // Akumulasi Finansial
            if ((substr($data->kdakun, 0, 1) == '4' || substr($data->kdakun, 0, 2) == '71') && $data->level == '4') {
                $totalpendapatan += $data->jumlah;
            } elseif ((substr($data->kdakun, 0, 1) == '5' || substr($data->kdakun, 0, 1) == '6' || substr($data->kdakun, 0, 2) == '72') && $data->level == '4') {
                $totalpengeluaran += $data->jumlah;
            }

            // Jeda Baris Antar Kategori Besar
            if ((substr($kdakun_old, 0, 1) != substr($data->kdakun, 0, 1)) && $kdakun_old != '') {
                echo "<tr><td colspan='6' style='border:none;'></td></tr>"; 
            }

            // Cetak Baris Akun Individu (Jika bukan Akun Pajak)
            if ($data->kdakun != "730000" && $data->kdakun != "73000") {
                $renderBaris = false;
                
                if ($akunlevel3 == 1) {
                    if ($data->level != '4') { $renderBaris = true; }
                } else {
                    $renderBaris = true;
                }

                if ($renderBaris) {
                    $nama_akun_aman = htmlspecialchars($data->nmakun, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $style_induk = ($data->level == '1') ? "style='font-weight: bold; background-color: #fcfcfc;'" : "";
                    
                    // Logika menentukan apakah baris ini boleh menampilkan angka/nominal atau hanya header
                    $tampilAngka = ($data->kdakun != '70000' && $data->kdakun != '700000' && $data->level != 1);

                    echo "<tr {$style_induk}>";
                    echo "<td class='col-kode'>{$data->kdakun}</td>";
                    echo "<td class='col-akun'>{$spasi}{$nama_akun_aman}</td>";

                    if ($tampilAngka) {
                        echo "<td class='col-num' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$data->jumlah}</td>";
                        echo "<td class='col-num' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$korfis_pos}</td>";
                        echo "<td class='col-num' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$korfis_neg}</td>";
                        echo "<td class='col-num' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$fiskal_val}</td>";
                    } else {
                        // Kosongkan kolom jika tidak memenuhi kriteria TampilAngka
                        echo "<td></td><td></td><td></td><td></td>";
                    }
                    echo "</tr>";
                }
            }

            $kdakun_old = $data->kdakun;

            // Pengosongan RAM server bertahap (sangat krusial untuk jutaan data)
            if (ob_get_level() > 0) ob_flush();
            flush();
        }

        // [5] Sisa Baris Akhir Penutup Kelompok Akun
        if ($totaldesc2 != "TOTAL BEBAN LAINNYA") {
            echo "<tr class='row-bold'>";
            echo "<td></td>";
            echo "<td>{$totaldesc2}</td>";
            echo "<td class='col-num' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totalrupiah}</td>";
            echo "<td colspan='2'></td>";
            echo "<td class='col-num' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totalfiskal}</td>";
            echo "</tr>";
        }

        // Hitung Hasil Akhir Finansial (Laba Rugi Sebelum & Setelah Pajak)
        $totallabasebelumpajak = $totalpendapatan - $totalpengeluaran;
        
        echo "<tr><td colspan='6' style='border:none;'></td></tr>"; // Baris Jeda Kosong
        echo "<tr class='row-bold' style='background-color: #e0f0d9;'>";
        echo "<td></td>";
        echo "<td>LABA (RUGI) SEBELUM PAJAK PENGHASILAN</td>";
        echo "<td class='col-num' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totallabasebelumpajak}</td>";
        echo "<td colspan='3'></td>";
        echo "</tr>";
        
        // Keluarkan baris pajak yang ditangkap di dalam loop (jika ada)
        if (!empty($pajakRowHtml)) {
            echo $pajakRowHtml;
        }

        $totallabasetelahpajak = ($totalpendapatan - $totalpengeluaran) - $bebanpajakpenghasilan;
        
        echo "<tr class='row-bold' style='background-color: #d0ebd0;'>";
        echo "<td></td>";
        echo "<td>LABA (RUGI) SETELAH PAJAK PENGHASILAN</td>";
        echo "<td class='col-num' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$totallabasetelahpajak}</td>";
        echo "<td colspan='3'></td>";
        echo "</tr>";

        // [6] PENUTUP DOKUMEN HTML
        echo "</table>";
        echo "</body>";
        echo "</html>";
        echo "exit";
    }
}