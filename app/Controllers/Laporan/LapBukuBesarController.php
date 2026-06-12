<?php

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Libraries\Pdf;

class LapBukuBesarController extends BaseController
{

	var $namaperusahaan = 'PT. XYZ';


	public function index()
	{
		if ($this->request->getPost('idperusahaan') == null) {

			$tglawal = session()->get('_tglawal');
			$tglakhir = session()->get('_tglakhir');
			$kdakun	 = session()->get('_kdakun');
			$keyakun = session()->get('_keyakun');
			$nmakun = session()->get('_nmakun');
		} else {
			$tglawal = $this->request->getPost('tglawal');
			$tglakhir = $this->request->getPost('tglakhir');
			$kdakun	 = $this->request->getPost('kdakun');
			$keyakun = $this->request->getPost('keyakun');
			$nmakun = $this->request->getPost('nmakun');
		}

		$data = array(
			'_tglawal' => $tglawal,
			'_tglakhir' => $tglakhir,
			'_kdakun' => $kdakun,
			'_keyakun' => $keyakun,
			'_nmakun' => $nmakun,
		);

		session()->set($data);


		$tanggalakhir = session()->get('_tglakhir') == null ? date('Y-m-d') : session()->get('_tglakhir');
		$DefaultTglEnd = $tanggalakhir;


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

		// 		if (empty($tglawal)) {
		// $tglawals = date('Y-m-d', strtotime($this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow()->tglmulaiusaha));
		$tglawals = date("Y-m-01");
		$tglakhir = $DefaultTglEnd;
		// 		}
		if (empty($kdakun)) {
			$kdakun = '';
			$keyakun = '';
			$nmakun = '';
		}

		$data['tglawals'] = $tglawals;
		$data['tglawal'] = $tglawal == null ? $tglawals : $tglawal;
		$data['tglakhir'] = $tglakhir;
		$data['kdakun'] = $kdakun;
		$data['keyakun'] = $keyakun;
		$data['nmakun'] = $nmakun;
		$data['idperusahaan'] = $idperusahaan;
		$data['namaperusahaan'] = $namaperusahaan;
		$data['rsData'] = $this->laporan_model->get_bukubesar($tglawal, $tglakhir, $kdakun, encrypt($idperusahaan), 'desc');
		$data['akunData'] = $this->akun_model->get_by_id(encrypt($keyakun), encrypt($idperusahaan))->getRow();
		return view('laporan/lapbukubesar/index', $data);
	}

	public function lapBukuBesarCetak($tglawal, $tglakhir, $keyakun, $idperusahaan, $nmakun)
    {
        // [PERBAIKAN 1]: Bypass batas memori dan waktu eksekusi agar server tidak timeout/crash
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $rsAkun = $this->akun_model->get_by_id(encrypt($keyakun), encrypt($idperusahaan))->getRow();
        $rsPerusahaan = $this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow();
        $tglawals = date('Y-m-d', strtotime($rsPerusahaan->tglmulaiusaha));

        if (!empty($idperusahaan)) {
            $namaperusahaan = $rsPerusahaan->namaperusahaan;
        } else {
            $namaperusahaan = '';
        }

        $kdakun = $rsAkun->kdakun;
        $nmakun = $rsAkun->nmakun;
        
        $rsData = $this->laporan_model->get_bukubesar($tglawal, $tglakhir, $kdakun, encrypt($idperusahaan), 'asc');
        if ($tglawal == $tglakhir) {
            $periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $periode = $this->laporan_model->tglindonesialengkap($tglawal) . ' s/d ' . $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        // Gunakan library custom Anda
        $pdf = new \App\Libraries\Pdf('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, $namaperusahaan, 'LAPORAN BUKU BESAR', $periode);
        $pdf->setPrintHeader(true); // Memastikan garis header default hilang
        $pdf->SetMargins(15, 20, 10);
        $pdf->AddPage();


        if (!empty($kdakun)) {
            $title_akun = '<div style="text-align:left; font-weight:bold; padding-top:10px;">Akun: ' . $kdakun . ' - ' . $nmakun . '</div>';
            $pdf->SetFont('times', '', 12);
            $pdf->writeHTML($title_akun, true, false, false, false, '');
        }

        // [PERBAIKAN 2]: Memisahkan Tag Header dan Footer Tabel agar bisa dipotong (Chunk)
        $tableHeader = '<table border="0" cellpadding="3">
                    <thead>
                        <tr style="background-color:#055F93; color:#ffffff;">
                            <th width="14%" style="font-weight:bold; text-align:center;">Tanggal</th>
                            <th width="15%" style="font-weight:bold; text-align:center;">No Jurnal</th>
                            <th width="15%" style="font-weight:bold; text-align:center;">Referensi</th>
                            <th width="20%" style="font-weight:bold; text-align:center;">Keterangan</th>            
                            <th width="12%" style="font-weight:bold; text-align:center;">Debet</th>
                            <th width="12%" style="font-weight:bold; text-align:center;">Kredit</th>
                            <th width="12%" style="font-weight:bold; text-align:center;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        $tableFooter = '</tbody></table>';

        $total1 = 0;
        $total2 = 0;
        $nsaldo = 0;
        $saldonormal = $this->akun_model->get_by_id(encrypt($keyakun), encrypt($idperusahaan))->getRow()->saldonormal;
        
        // Inisialisasi variabel Chunking
        $htmlChunk = $tableHeader;
        $rowCount = 0;
        $chunkSize = 500; // Proses render per 500 baris ke PDF
        $isFirstRow = true;

        // [PERBAIKAN 3]: Gunakan getUnbufferedRow() untuk menarik data satu-per-satu tanpa menumpuk di RAM
        while ($data = $rsData->getUnbufferedRow()) {
            
            // [PERBAIKAN 4]: Pindahkan logika Query Saldo Awal ke dalam loop untuk putaran pertama saja.
            // Ini mencegah pemanggilan getRow() sebelumnya yang dapat merusak mode Unbuffered.
            if ($isFirstRow) {
                $tglJurnalMinusSatu = date('Y-m-d', strtotime('-1 days', strtotime($data->tgljurnal)));
                
                if ($saldonormal == 'D') {
                    $query = "select sum(debet)-sum(kredit) as saldoakhir from v_jurnaldetail where idperusahaan='" . $idperusahaan . "' and kdakun='" . $kdakun . "' and tgljurnal between '" . $tglawals . "' and '" . $tglJurnalMinusSatu . "'";
                } else {
                    $query = "select sum(kredit)-sum(debet) as saldoakhir from v_jurnaldetail where idperusahaan='" . $idperusahaan . "' and kdakun='" . $kdakun . "' and tgljurnal between '" . $tglawals . "' and '" . $tglJurnalMinusSatu . "'";
                }
                
                $saldoakhir = $this->db->query($query)->getRow()->saldoakhir;
                $nsaldo = ($saldoakhir == '') ? 0 : $saldoakhir;
                $isFirstRow = false; // Kunci agar tidak dieksekusi lagi di baris berikutnya
            }

            // Logika Perhitungan Anda (Tetap Sama)
            $total1 = $total1 + $data->debet;
            $total2 = $total2 + $data->kredit;

            if ($saldonormal == 'D') {
                $nsaldo -= $data->kredit - $data->debet;
            } else {
                $nsaldo -= $data->debet - $data->kredit;
            }

            $referensi_aman = htmlspecialchars($data->referensi, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $keterangan_aman = htmlspecialchars($data->keterangan, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
            $htmlChunk .= '
                        <tr>
                            <td width="14%" style="text-align:center;">' . date('d-m-Y', strtotime($data->tgljurnal)) . '</td>
                            <td width="17%" style="text-align:center;">' . $data->idjurnal . '</td>
                            <td width="15%" style="text-align:center;">' . $referensi_aman . '</td>
                            <td width="18%" style="text-align:left;">' . $keterangan_aman . '</td>                  
                            <td width="12%" style="text-align:right;">' . ($data->debet > 0 ? number_format($data->debet, 0, ",", ".") : "") . '</td>
                            <td width="12%" style="text-align:right;">' . ($data->kredit > 0 ? number_format($data->kredit, 0, ",", ".") : "") . '</td>
                            <td width="12%" style="text-align:right;">' . ($nsaldo >= 0 ? number_format($nsaldo, 0, ",", ".") : "(" . number_format($nsaldo * -1, 0, ",", ".") . ")") . '</td>
                        </tr>';

            $rowCount++;

            // [PERBAIKAN 5]: Trigger cetak jika sudah mencapai batas 500 baris
            if ($rowCount % $chunkSize == 0) {
                $htmlChunk .= $tableFooter; // Tutup tabel
                $pdf->SetFont('times', '', 10);
                $pdf->writeHTML($htmlChunk, true, false, false, false, ''); // Tulis ke PDF
                
                // Kosongkan variabel RAM dan mulai tabel baru (Tanpa spasi <br> tambahan)
                $tableHeaderLanjutan = '<table border="0" cellpadding="3"><tbody>'; 
                $htmlChunk = $tableHeaderLanjutan;
            }
        }

        // --- CETAK TOTAL DIAKHIR & SISA BARIS ---
        $htmlChunk .= '
                        <tr style="background-color: #E5E4E2;">
                            <td style="text-align:right;font-size: 9pt" colspan="4"><B>TOTAL       </B></td>
                            <td style="text-align:right; font-size: 9pt"><B>' . ($total1 >= 0 ? number_format($total1, 0, ",", ".") : "(" . number_format($total1 * -1, 0, ",", ".") . ")") . '</B></td>
                            <td style="text-align:right; font-size: 9pt"><B>' . ($total2 >= 0 ? number_format($total2, 0, ",", ".") : "(" . number_format($total2 * -1, 0, ",", ".") . ")") . '</B></td>
                            <td style="text-align:right;"><B></B></td>
                        </tr>';
        $htmlChunk .= $tableFooter;

        // Cetak sisa tabel yang belum dieksekusi oleh modulo (chunk)
        $pdf->SetFont('times', '', 10);
        $pdf->writeHTML($htmlChunk, true, false, false, false, '');

        $bulantahun = bulan_tahun($tglawal) . ' - ' . bulan_tahun($tglakhir);
        $namaPerusahaan = ucwords(strtolower($namaperusahaan));
        $namaPerusahaan = preg_replace(['/\bPt\b/', '/\bCv\b/'], ['PT', 'CV'], $namaPerusahaan);
        $namaFile = 'Laporan Buku Besar ' . $namaPerusahaan . ' '.$nmakun.' ' . $bulantahun . '.pdf';
        $pdf->Output($namaFile, 'I');
        exit;
    }

	public function lapBukuBesarCetakSemua($tglawal, $tglakhir, $idperusahaan)
    {
        // [PERBAIKAN 1]: Bebaskan limit memori dan waktu eksekusi server
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $tglawal = $this->uri->getSegment(3);
        $tglakhir = $this->uri->getSegment(4);
        $idperusahaan = $this->uri->getSegment(5);

        $keyakunq = $this->akun_model->get_by_id(encrypt($idperusahaan))->getRow();

        if (session()->get('idpengguna') == '8888888888') {
            $idperusahaan = $this->uri->getSegment(5);
        } else {
            $idperusahaan = session()->get('idperusahaan');
        }

        $rsPerusahaan = $this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow();
        $tglawals = date('Y-m-d', strtotime($rsPerusahaan->tglmulaiusaha));

        if (!empty($idperusahaan)) {
            $namaperusahaan = $rsPerusahaan->namaperusahaan;
        } else {
            $namaperusahaan = '';
        }

        $tglawal = $tglawal;
        $tglakhir = $tglakhir;
        if ($tglawal == $tglakhir) {
            $periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $periode = $this->laporan_model->tglindonesialengkap($tglawal) . ' s/d ' . $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        $builder = $this->db->table('v_akun');
        $akun = $builder->getWhere(array('idperusahaan' => $idperusahaan, 'level' => 4));
        $idperusahaan = $idperusahaan;
        $namaperusahaan = $namaperusahaan;

        // Gunakan library PDF yang sudah kita rapikan sebelumnya
        $pdf = new \App\Libraries\Pdf('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, $namaperusahaan, 'LAPORAN BUKU BESAR', $periode);
        $pdf->setPrintHeader(true);
        $pdf->SetMargins(15, 20, 10);
        $pdf->AddPage();

        

        // --- LOOPING SETIAP AKUN ---
        foreach ($akun->getResult() as $r) {
            
            // Cetak Judul Akun terlebih dahulu agar langsung dieksekusi oleh PDF
            $title_akun = '<div style="text-align:left; font-weight:bold; padding-top:10px;">Akun: ' . $r->kdakun . ' - ' . $r->nmakun . '</div>';
            $pdf->writeHTML($title_akun, true, false, false, false, '');

            // [PERBAIKAN 2]: Pisahkan Header dan Footer Tabel untuk sistem Chunking
            $tableHeader = '<table border="0">
                    <thead>
                        <tr style="background-color:#055F93; color:#ffffff;">
                        <th width="10%" style="font-weight:bold; text-align:center;">Tanggal</th>
                            <th width="15%" style="font-weight:bold; text-align:center;">No Jurnal</th>
                            <th width="15%" style="font-weight:bold; text-align:center;">Referensi</th>
                            <th width="24%" style="font-weight:bold; text-align:center;">Keterangan</th>            
                            <th width="12%" style="font-weight:bold; text-align:center;">Debet</th>
                            <th width="12%" style="font-weight:bold; text-align:center;">Kredit</th>
                            <th width="12%" style="font-weight:bold; text-align:center;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            $tableFooter = '</tbody></table>';

            $no = 1;
            $total1 = 0;
            $total2 = 0;
            $nsaldo = 0;

            $saldonormal = $this->akun_model->get_by_id(encrypt($r->keyakun), encrypt($idperusahaan))->getRow()->saldonormal;
            $rsData = $this->laporan_model->get_bukubesar($tglawal, $tglakhir, $r->kdakun, encrypt($idperusahaan), 'asc');
            $rsDataSaldo = $this->laporan_model->get_bukubesar_saldoawal($tglawal, $tglakhir, $r->kdakun, $idperusahaan, 'asc');
            
            // Setup variabel chunking
            $htmlChunk = $tableHeader;
            $rowCount = 0;
            $chunkSize = 500;
            $isFirstRow = true;

            // [PERBAIKAN 3]: Loop data per baris tanpa membebani RAM (Unbuffered)
            while ($data = $rsData->getUnbufferedRow()) {
                
                // [PERBAIKAN 4]: Pindahkan logika getRow() tgljurnal ke dalam iterasi pertama agar kueri unbuffered tidak rusak
                if ($isFirstRow) {
                    if ($rsDataSaldo->getRow() != null) {
                        $tgl_minus_satu = date('Y-m-d', strtotime('-1 days', strtotime($data->tgljurnal)));

                        if ($saldonormal == 'D') {
                            $query = "select sum(debet)-sum(kredit) as saldoakhir from v_jurnaldetail where idperusahaan='" . $idperusahaan . "' and kdakun='" . $r->kdakun . "' and tgljurnal between '" . $tglawals . "' and '" . $tgl_minus_satu . "'";
                        } else {
                            $query = "select sum(kredit)-sum(debet) as saldoakhir from v_jurnaldetail where idperusahaan='" . $idperusahaan . "' and kdakun='" . $r->kdakun . "' and tgljurnal between '" . $tglawals . "' and '" . $tgl_minus_satu . "'";
                        }
                        
                        $saldoakhir = $this->db->query($query)->getRow()->saldoakhir;
                        if ($saldoakhir == '') {
                            $saldoakhir = 0;
                        }
                    } else {
                        $saldoakhir = 0;
                    }
                    $nsaldo = $saldoakhir;
                    $isFirstRow = false;
                }

                $total1 = $total1 + $data->debet;
                $total2 = $total2 + $data->kredit;

                if ($saldonormal == 'D') {
                    $nsaldo -= $data->kredit - $data->debet;
                } else {
                    $nsaldo -= $data->debet - $data->kredit;
                }

                $referensi_aman = htmlspecialchars($data->referensi, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $keterangan_aman = htmlspecialchars($data->keterangan, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                
                $htmlChunk .= '
                        <tr>
                            <td width="10%" style="text-align:center;">' . date('d-m-Y', strtotime($data->tgljurnal)) . '</td>
                            <td width="15%" style="text-align:center;">' . $data->idjurnal . '</td>
                            <td width="15%" style="text-align:center;">' . $referensi_aman . '</td>
                            <td width="24%" style="text-align:left;">' . $keterangan_aman . '</td>                  
                            <td width="12%" style="text-align:right;">' . ($data->debet > 0 ? number_format($data->debet, 0, ",", ".") : "") . '</td>
                            <td width="12%" style="text-align:right;">' . ($data->kredit > 0 ? number_format($data->kredit, 0, ",", ".") : "") . '</td>
                            <td width="12%" style="text-align:right;">' . ($nsaldo >= 0 ? number_format($nsaldo, 0, ",", ".") : "(" . number_format($nsaldo * -1, 0, ",", ".") . ")") . '</td>
                        </tr>';

                $rowCount++;

                // [PERBAIKAN 5]: Cetak ke PDF setiap kelipatan 500 lalu kosongkan memori variabel
                if ($rowCount % $chunkSize == 0) {
                    $htmlChunk .= $tableFooter;
					$pdf->SetFont('times', '', 10);
                    $pdf->writeHTML($htmlChunk, true, false, false, false, '');
                    
                    // Kembalikan ke format header agar tabel selanjutnya memiliki lebar kolom (width) yang konsisten presisi
                    $htmlChunk = $tableHeader;
                }
            }

            // Setelah semua data di akun ini selesai, cetak sisa tabel dan baris TOTAL
            $htmlChunk .= '
                        <tr style="background-color: #E5E4E2;">
                            <td style="text-align:right;font-size: 9pt" colspan="4"><B>TOTAL       </B></td>
                            <td style="text-align:right;font-size: 9pt"><B>' . ($total1 >= 0 ? number_format($total1, 0, ",", ".") : "(" . number_format($total1 * -1, 0, ",", ".") . ")") . '</B></td>
                            <td style="text-align:right;font-size: 9pt"><B>' . ($total2 >= 0 ? number_format($total2, 0, ",", ".") : "(" . number_format($total2 * -1, 0, ",", ".") . ")") . '</B></td>
                            <td style="text-align:right;font-size: 9pt"><B></B></td>
                        </tr>';
            $htmlChunk .= $tableFooter . '<br><br>';
			$pdf->SetFont('times', '', 10);
            $pdf->writeHTML($htmlChunk, true, false, false, false, '');
        }

        $bulantahun = bulan_tahun($tglawal) . ' - ' . bulan_tahun($tglakhir);
        $namaPerusahaan = ucwords(strtolower($namaperusahaan));
        $namaPerusahaan = preg_replace(['/\bPt\b/', '/\bCv\b/'], ['PT', 'CV'], $namaPerusahaan);
        $namaFile = 'Laporan Buku Besar ' . $namaPerusahaan . ' ' . $bulantahun . '.pdf';
        $pdf->Output($namaFile, 'I');
        exit;
    }

	public function lapBukuBesarExcel($tglawal, $tglakhir, $keyakun, $idperusahaan, $nmakun)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $tglawal = $this->uri->getSegment(3);
        $tglakhir = $this->uri->getSegment(4);
        $keyakun = $this->uri->getSegment(5);
        $idperusahaan = $this->uri->getSegment(6);
        $nmakun = $this->uri->getSegment(7);
        $kdakun = $this->akun_model->get_by_id(encrypt($keyakun), encrypt($idperusahaan))->getRow()->kdakun;

        $rsPerusahaan = $this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow();
        $tglawals = date('Y-m-d', strtotime($rsPerusahaan->tglmulaiusaha));
        $namaperusahaan = (!empty($idperusahaan)) ? $rsPerusahaan->namaperusahaan : '';

        if ($tglawal == $tglakhir) {
            $periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $periode = $this->laporan_model->tglindonesialengkap($tglawal) . ' s/d ' . $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        $builder = $this->db->table('v_akun');
        $akun = $builder->getWhere(array('idperusahaan' => $idperusahaan, 'keyakun' => $keyakun));

        $bulantahun     = bulan_tahun($tglawal) . ' - ' . bulan_tahun($tglakhir);
        $namaPerusahaan = ucwords(strtolower($namaperusahaan));
        $namaPerusahaan = preg_replace(['/\bPt\b/', '/\bCv\b/'], ['PT', 'CV'], $namaPerusahaan);
        $namaFile = 'Laporan Buku Besar ' . $namaPerusahaan . ' ' . $nmakun . ' ' . $bulantahun . '.xls';
        
        header("Content-Disposition: attachment; filename=\"" . $namaFile . "\"");
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Cache-Control: max-age=0");
        
        echo "<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">";
        echo "<head>";
        echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
        echo "<style>";
        echo "table { border-collapse: collapse; font-family: Arial, sans-serif; margin-bottom: 20px; }";
        echo "th, td { border: 1px solid #000000; padding: 5px; vertical-align: top; }";
        echo ".kop-laporan th { border: none; background: none; text-align: center; }"; 
        echo ".header-tabel th { background-color: #f2f2f2; font-weight: bold; text-align: center; }";
        echo ".col-tgl { width: 100px; text-align: center; }";
        echo ".col-nojurnal { width: 160px; text-align: center; }";
        echo ".col-ref { width: 120px; text-align: left; }";
        echo ".col-ket { width: 350px; text-align: left; }";
        echo ".col-uang { width: 130px; text-align: right; }";
        echo "</style>";
        echo "</head>";
        echo "<body>";

        foreach ($akun->getResult() as $r) {
            echo "<table>";
            echo "<tr class='kop-laporan'><th colspan='7' style='font-size: 16px; font-weight:bold;'>{$namaperusahaan}</th></tr>";
            echo "<tr class='kop-laporan'><th colspan='7' style='font-size: 14px; font-weight:bold;'>LAPORAN BUKU BESAR</th></tr>";
            echo "<tr class='kop-laporan'><th colspan='7' style='font-size: 12px;'>Periode {$periode}</th></tr>";
            echo "<tr class='kop-laporan'><th colspan='7' style='font-size: 12px; text-align: left;'>Akun: {$r->kdakun} - {$r->nmakun}</th></tr>";
            echo "<tr><td colspan='7' style='border:none;'></td></tr>";
            
            echo "<tr class='header-tabel'>";
            echo "<th class='col-tgl'>Tanggal</th>";
            echo "<th class='col-nojurnal'>No Jurnal</th>";
            echo "<th class='col-ref'>Referensi</th>";
            echo "<th class='col-ket'>Keterangan</th>";
            echo "<th class='col-uang'>Debet</th>";
            echo "<th class='col-uang'>Kredit</th>";
            echo "<th class='col-uang'>Saldo</th>";
            echo "</tr>";

            $total1 = 0; $total2 = 0; $nsaldo = 0;
            $saldonormal = $this->akun_model->get_by_id(encrypt($keyakun), encrypt($idperusahaan))->getRow()->saldonormal;
            $rsData = $this->laporan_model->get_bukubesar($tglawal, $tglakhir, $kdakun, encrypt($idperusahaan), 'asc');
            $isFirstRow = true;

            while ($data = $rsData->getUnbufferedRow()) {
                if ($isFirstRow) {
                    $tgl_minus_satu = date('Y-m-d', strtotime('-1 days', strtotime($data->tgljurnal)));
                    if ($saldonormal == 'D') {
                        $query = "select sum(debet)-sum(kredit) as saldoakhir from v_jurnaldetail where idperusahaan='" . $idperusahaan . "' and kdakun='" . $kdakun . "' and tgljurnal between '" . $tglawals . "' and '" . $tgl_minus_satu . "'";
                    } else {
                        $query = "select sum(kredit)-sum(debet) as saldoakhir from v_jurnaldetail where idperusahaan='" . $idperusahaan . "' and kdakun='" . $kdakun . "' and tgljurnal between '" . $tglawals . "' and '" . $tgl_minus_satu . "'";
                    }
                    $saldoakhir = $this->db->query($query)->getRow()->saldoakhir;
                    $nsaldo = ($saldoakhir == '') ? 0 : $saldoakhir;
                    $isFirstRow = false;
                }

                $total1 += $data->debet;
                $total2 += $data->kredit;

                if ($saldonormal == 'D') {
                    $nsaldo -= $data->kredit - $data->debet;
                } else {
                    $nsaldo -= $data->debet - $data->kredit;
                }
                
                $referensi_aman = htmlspecialchars($data->referensi, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $keterangan_aman = htmlspecialchars($data->keterangan, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $tampil_tgl = date('d-m-Y', strtotime($data->tgljurnal));

                echo "<tr>";
                echo "<td class='col-tgl'>{$tampil_tgl}</td>";
                echo "<td class='col-nojurnal'>{$data->idjurnal}</td>";
                echo "<td class='col-ref'>{$referensi_aman}</td>";
                echo "<td class='col-ket'>{$keterangan_aman}</td>";
                // [PERBAIKAN]: Menggunakan format tanda kurung untuk negatif \(\#\,\#\#0\)
                echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$data->debet}</td>";
                echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$data->kredit}</td>";
                echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$nsaldo}</td>"; // Biarkan nilai aslinya (jangan di * -1)
                echo "</tr>";

                if (ob_get_level() > 0) ob_flush();
                flush();
            }
            
            echo "<tr>";
            echo "<td colspan='4' style='text-align: right; font-weight: bold;'>TOTAL</td>";
            // [PERBAIKAN]: Hilangkan pengkondisian * -1 agar format akuntansi bekerja sempurna
            echo "<td class='col-uang' style='font-weight: bold; mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$total1}</td>";
            echo "<td class='col-uang' style='font-weight: bold; mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$total2}</td>";
            echo "<td></td>"; 
            echo "</tr>";
            echo "</table><br>"; 
        }

        echo "</body>";
        echo "</html>";
        exit;
    }

	public function lapBukuBesarExcelSemua($tglawal, $tglakhir, $idperusahaan)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        
        if (session()->get('idpengguna') == '8888888888') {
            $idperusahaan = $this->uri->getSegment(5);
        } else {
            $idperusahaan = session()->get('idperusahaan');
        }

        $rsPerusahaan = $this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow();
        $tglawals = date('Y-m-d', strtotime($rsPerusahaan->tglmulaiusaha));
        $namaperusahaan = !empty($idperusahaan) ? $rsPerusahaan->namaperusahaan : '';

        if ($tglawal == $tglakhir) {
            $periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $periode = $this->laporan_model->tglindonesialengkap($tglawal) . ' s/d ' . $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        $builder = $this->db->table('v_akun');
        $akun = $builder->getWhere(array('idperusahaan' => $idperusahaan, 'level' => 4));

        $bulantahun     = bulan_tahun($tglawal) . ' - ' . bulan_tahun($tglakhir);
        $namaPerusahaan = ucwords(strtolower($namaperusahaan));
        $namaPerusahaan = preg_replace(['/\bPt\b/', '/\bCv\b/'], ['PT', 'CV'], $namaPerusahaan);
        $namaFile = 'Laporan Buku Besar Semua Akun ' . $namaPerusahaan . ' ' . $bulantahun . '.xls';
        
        header("Content-Disposition: attachment; filename=\"" . $namaFile . "\"");
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Cache-Control: max-age=0");
        
        echo "<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">";
        echo "<head>";
        echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
        echo "<style>";
        echo "table { border-collapse: collapse; font-family: Arial, sans-serif; margin-bottom: 30px; }";
        echo "th, td { border: 1px solid #000000; padding: 5px; vertical-align: top; }";
        echo ".kop-laporan th { border: none; background: none; text-align: center; }";
        echo ".header-tabel th { background-color: #f2f2f2; font-weight: bold; text-align: center; }";
        echo ".col-tgl { width: 100px; text-align: center; }";
        echo ".col-nojurnal { width: 160px; text-align: center; }";
        echo ".col-ref { width: 120px; text-align: left; }";
        echo ".col-ket { width: 350px; text-align: left; }"; 
        echo ".col-uang { width: 130px; text-align: right; }";
        echo "</style>";
        echo "</head>";
        echo "<body>";

        echo "<table>";
        echo "<tr class='kop-laporan'><th colspan='7' style='font-size: 16px; font-weight:bold;'>{$namaperusahaan}</th></tr>";
        echo "<tr class='kop-laporan'><th colspan='7' style='font-size: 14px; font-weight:bold;'>LAPORAN BUKU BESAR ALL ACCOUNT</th></tr>";
        echo "<tr class='kop-laporan'><th colspan='7' style='font-size: 12px;'>Periode {$periode}</th></tr>";
        echo "</table>";

        foreach ($akun->getResult() as $r) {
            echo "<table>";
            echo "<tr class='kop-laporan'><th colspan='7' style='font-size: 13px; font-weight:bold; text-align:left; background-color:#e6e6e6; border:1px solid #000000; padding:6px;'>Akun: {$r->kdakun} - {$r->nmakun}</th></tr>";
            
            echo "<tr class='header-tabel'>";
            echo "<th class='col-tgl'>Tanggal</th>";
            echo "<th class='col-nojurnal'>No Jurnal</th>";
            echo "<th class='col-ref'>Referensi</th>";
            echo "<th class='col-ket'>Keterangan</th>";
            echo "<th class='col-uang'>Debet</th>";
            echo "<th class='col-uang'>Kredit</th>";
            echo "<th class='col-uang'>Saldo</th>";
            echo "</tr>";

            $total1 = 0; $total2 = 0; $nsaldo = 0; $isFirstRow = true;
            $saldonormal = $this->akun_model->get_by_id(encrypt($r->keyakun), encrypt($idperusahaan))->getRow()->saldonormal;
            
            $rsData = $this->laporan_model->get_bukubesar($tglawal, $tglakhir, $r->kdakun, encrypt($idperusahaan), 'asc');
            $rsDataSaldo = $this->laporan_model->get_bukubesar_saldoawal($tglawal, $tglakhir, $r->kdakun, $idperusahaan, 'asc');

            while ($data = $rsData->getUnbufferedRow()) {
                if ($isFirstRow) {
                    if ($rsDataSaldo->getRow() != null) {
                        $tgl_minus_satu = date('Y-m-d', strtotime('-1 days', strtotime($data->tgljurnal)));
                        if ($saldonormal == 'D') {
                            $query = "select sum(debet)-sum(kredit) as saldoakhir from v_jurnaldetail where idperusahaan='" . $idperusahaan . "' and kdakun='" . $r->kdakun . "' and tgljurnal between '" . $tglawals . "' and '" . $tgl_minus_satu . "'";
                        } else {
                            $query = "select sum(kredit)-sum(debet) as saldoakhir from v_jurnaldetail where idperusahaan='" . $idperusahaan . "' and kdakun='" . $r->kdakun . "' and tgljurnal between '" . $tglawals . "' and '" . $tgl_minus_satu . "'";
                        }
                        $saldoakhir_raw = $this->db->query($query)->getRow()->saldoakhir;
                        $saldoakhir = ($saldoakhir_raw == '') ? 0 : $saldoakhir_raw;
                    } else {
                        $saldoakhir = 0;
                    }
                    $nsaldo = $saldoakhir;
                    $isFirstRow = false;
                }

                $total1 += $data->debet;
                $total2 += $data->kredit;

                if ($saldonormal == 'D') {
                    $nsaldo -= $data->kredit - $data->debet;
                } else {
                    $nsaldo -= $data->debet - $data->kredit;
                }

                $tampil_tgl = date('d-m-Y', strtotime($data->tgljurnal));
                $referensi_aman = htmlspecialchars($data->referensi, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $keterangan_aman = htmlspecialchars($data->keterangan, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                echo "<tr>";
                echo "<td class='col-tgl'>{$tampil_tgl}</td>";
                echo "<td class='col-nojurnal'>{$data->idjurnal}</td>";
                echo "<td class='col-ref'>{$referensi_aman}</td>";
                echo "<td class='col-ket'>{$keterangan_aman}</td>";
                // [PERBAIKAN]: Tanda kurung di-set di mso-number-format dan tampilkan variabel asli $nsaldo
                echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$data->debet}</td>";
                echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$data->kredit}</td>";
                echo "<td class='col-uang' style='mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$nsaldo}</td>";
                echo "</tr>";

                if (ob_get_level() > 0) ob_flush();
                flush();
            }
            
            echo "<tr>";
            echo "<td colspan='4' style='text-align: right; font-weight: bold;'>TOTAL</td>";
            // [PERBAIKAN]: Mengembalikan ke nilai asli tanpa perkalian * -1
            echo "<td class='col-uang' style='font-weight: bold; mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$total1}</td>";
            echo "<td class='col-uang' style='font-weight: bold; mso-number-format:\"\#\,\#\#0;\(\#\,\#\#0\)\";'>{$total2}</td>";
            echo "<td></td>"; 
            echo "</tr>";
            echo "</table>"; 
        }

        echo "</body>";
        echo "</html>";
        exit;
    }

	function fetchLapbukuBesar()
	{
		$limit = $this->request->getPost('limit');
		$start = $this->request->getPost('start');
		if ($this->request->getPost('namaperusahaan') == null) {

			$tglawal = session()->get('_tglawal');
			$tglakhir = session()->get('_tglakhir');
			$kdakun	 = session()->get('_kdakun');
			$keyakun = session()->get('_keyakun');
			$nmakun = session()->get('_nmakun');
		} else {
			$tglawal = $this->request->getPost('tglawal');
			$tglakhir = $this->request->getPost('tglakhir');
			$kdakun	 = $this->request->getPost('kdakun');
			$keyakun = $this->request->getPost('keyakun');
			$nmakun = $this->request->getPost('nmakun');
		}

		$data = array(
			'_tglawal' => $tglawal,
			'_tglakhir' => $tglakhir,
			'_kdakun' => $kdakun,
			'_keyakun' => $keyakun,
			'_nmakun' => $nmakun,
		);

		session()->set($data);


		$tanggalakhir = session()->get('_tglakhir') == null ? date('Y-m-d') : session()->get('_tglakhir');
		$DefaultTglEnd = $tanggalakhir;


		if (session()->get('idpengguna') == '8888888888') {
			if ($this->request->getPost('idperusahaan') == "") {
				$idperusahaan = "9999999999";
			} else {
				$idperusahaan = $this->request->getPost('idperusahaan');
			}
		} else {
			$idperusahaan = session()->get('idperusahaan');
		}

		$tglawals = date('Y-m-d', strtotime($this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow()->tglmulaiusaha));
		$tglakhir = $DefaultTglEnd;

		if (empty($kdakun)) {
			$kdakun = '10000';
			$keyakun = '10000' . $idperusahaan;
			$nmakun = 'ASET';
		}

		$rsData = $this->laporan_model->get_fetchBukubesar($tglawal, $tglakhir, $kdakun, encrypt($idperusahaan), 'desc', $limit, $start);
		$akunData = $this->akun_model->get_by_id(encrypt($keyakun), encrypt($idperusahaan))->getRow();

		$total1 = 0;
		$total2 = 0;
		$nsaldo = 0;


		$output = '';

		if ($akunData) {

			$saldonormal = $akunData->saldonormal;
			if (!empty($rsData->getRow()->tgljurnal)) {
				$tglakhirs = $rsData->getRow()->tgljurnal;
			} else {
				$tglakhirs = null;
			}
			if ($saldonormal == 'D') {
				$query = "select sum(debet)-sum(kredit) as saldoakhir from v_jurnaldetail where idperusahaan='" . $idperusahaan . "' and kdakun='" . $kdakun . "' and tgljurnal between '" . $tglawals . "' and '" . $tglakhirs . "'";
			} else {
				$query = "select sum(kredit)-sum(debet) as saldoakhir from v_jurnaldetail where idperusahaan='" . $idperusahaan . "' and kdakun='" . $kdakun . "' and tgljurnal between '" . $tglawals . "' and '" . $tglakhirs . "'";
			}
			$this->db = \Config\Database::connect();
			$saldoakhir = $this->db->query($query)->getRow()->saldoakhir;
			if ($saldoakhir == '') {
				$saldoakhir = 0;
			}

			if ($start < 10) {
				$nsaldo = $saldoakhir;
			} else {
				$nsaldo = session()->get('_saldoakhir');
			}
			$no = 1;
			foreach ($rsData->getResult() as $key => $data) {
				$no++;
				$total1 = $total1 + $data->debet;
				$total2 = $total2 + $data->kredit;
				// <td width="5%" style="text-align:center;">' . $no++ . '</td>
				if($data->approve == '1'){
				    $status ='<span class="badge badge-pill badge-success">Disetujui</span>';
				    
				}elseif($data->approve == '2'){
				    $status ='<span class="badge badge-pill badge-danger">Perbaikan</span>';
				}else{
				     $status ='<span class="badge badge-pill badge-warning">Menunggu</span>';
				}

                $referensi_aman = htmlspecialchars($data->referensi, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $keterangan_aman = htmlspecialchars($data->keterangan, ENT_QUOTES | ENT_HTML5, 'UTF-8');
				$output .= '<tr>
    				<td width="10%" style="text-align:center;">'. date('d-m-Y', strtotime($data->tgljurnal)) . '</td>
					<td width="10%" style="text-align:center;">' . $data->idjurnal . '</td>
					<td width="10%" style="text-align:left;">' . $referensi_aman . '</td>         
					<td width="25%" style="text-align:left;">' . $keterangan_aman . '</td>         
					<td width="11%" style="text-align:right;">' . ($data->debet > 0 ? number_format($data->debet, 0, ",", ".") : "") . '</td>
					<td width="11%" style="text-align:right;">' . ($data->kredit > 0 ? number_format($data->kredit, 0, ",", ".") : "") . '</td>
					<td width="11%" style="text-align:right;">' . ($nsaldo >= 0 ? number_format($nsaldo, 0, ",", ".") : "(" . number_format($nsaldo * -1, 0, ",", ".") . ")") . '</td>
					<td width="6]9%">'.$status.'</td>
					<td width="9%" style="text-align:right">
					    <div class="d-flex justify-content-center align-items-center">
        					<div class="dropdown custom-dropdown dropleft mr-2">
        						<a class="badge btn-info" href="#" role="button" id="dropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        							<i class="bi bi-three-dots"></i>
        						</a>
        						<div class="dropdown-menu" >
            						<div class="mx-2">
                						<a href="javascript:void(0)" data-cetak_pdf="' . site_url('jurnal/lihat/' . encrypt($data->idjurnal)) . '" class="btn btn-sm btn-secondary btn-circle tooltips" data-toggle="modal" data-placement="left" data-target="#modalcetakpdf" id="cetak-pdf" title="Cetak jurnal ke pdf"><i class="fa fa-print"></i></a>
                						
                						<a href="' . site_url('jurnal/edit/' . encrypt($data->idjurnal). '/lab') . '" class="btn btn-sm btn-warning btn-circle tooltips" data-toggle="tooltip" data-placement="left" title="Ubah data jurnal"><i class="fa fa-edit"></i></a>
            						</div>
        						</div>
        					</div>
        				</div>
					</td>
				</tr>';


				if ($saldonormal == 'D') {
					$nsaldo += $data->kredit - $data->debet;
				} else {
					$nsaldo += $data->debet - $data->kredit;
				}
				$datas = array(
					'_saldoakhir' => $nsaldo,
				);

				session()->set($datas);
			}
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

	function totalFetchBukubesar()
	{
		$tglawal = $this->request->getPost('tglawal');
		$tglakhir = $this->request->getPost('tglakhir');
		$kdakun	 = $this->request->getPost('kdakun');
		if ($kdakun == "") {
			$idperusahaan = "";
		} else {
			$idperusahaan = $this->request->getPost('idperusahaan');
		}

		$rsData = $this->laporan_model->get_totalFetchBukubesar($tglawal, $tglakhir, $kdakun, $idperusahaan);
		return $this->response->setJSON($rsData->getResult());
	}
}
