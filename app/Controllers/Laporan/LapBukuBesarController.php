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
            $alamat = $rsPerusahaan->alamat;
            $notelp = $rsPerusahaan->notelp;
        } else {
            $namaperusahaan = '';
            $alamat = '';
            $notelp = '';
        }

        $kdakun = $rsAkun->kdakun;
        $nmakun = $rsAkun->nmakun;
        
        $rsData = $this->laporan_model->get_bukubesar($tglawal, $tglakhir, $kdakun, encrypt($idperusahaan), 'asc');

        // Gunakan library custom Anda
        $pdf = new Pdf('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintHeader(false); // Memastikan garis header default hilang
        $pdf->SetMargins(15, 20, 10);
        $pdf->AddPage();

        // --- CETAK KOP LAPORAN (Langsung dirender untuk menghemat memori) ---
        $title = '
			<span style="text-align:center; text-transform:uppercase; font-size:16px; font-weight:bold; padding-top:10px;">' . $namaperusahaan . '</span><br>	
			<span style="text-align:center; font-size:16px; font-weight:bold; padding-top:10px;">LAPORAN BUKU BESAR</span>	
		';
        $pdf->SetFont('times', '', 14);
        $pdf->writeHTML($title, true, false, false, false, '');
        $pdf->SetTopMargin(15);

        if ($tglawal == $tglakhir) {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal) . ' s/d ' . $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        $title_periode = '<div style="text-align:center; padding-top:10px;"> Periode ' . ($Periode) . '</div>';
        $pdf->SetFont('times', '', 14);
        $pdf->writeHTML($title_periode, true, false, false, false, '');

        if (!empty($kdakun)) {
            $title_akun = '<br><br><div style="text-align:left; font-weight:bold; padding-top:10px;">Akun: ' . $kdakun . ' - ' . $nmakun . '</div>';
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
                            <td style="text-align:center;" colspan="4"><B>TOTAL       </B></td>
                            <td style="text-align:right;"><B>' . ($total1 >= 0 ? number_format($total1, 0, ",", ".") : "(" . number_format($total1 * -1, 0, ",", ".") . ")") . '</B></td>
                            <td style="text-align:right;"><B>' . ($total2 >= 0 ? number_format($total2, 0, ",", ".") : "(" . number_format($total2 * -1, 0, ",", ".") . ")") . '</B></td>
                            <td style="text-align:right;"><B></B></td>
                        </tr>';
        $htmlChunk .= $tableFooter;

        // Cetak sisa tabel yang belum dieksekusi oleh modulo (chunk)
        $pdf->SetFont('times', '', 10);
        $pdf->writeHTML($htmlChunk, true, false, false, false, '');

        $pdf->Output("Laporan Buku Besar $nmakun.pdf", 'I');
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
        $pdf = new \App\Libraries\Pdf('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->AddPage();

        // --- CETAK KOP UTAMA LAPORAN ---
        $title = '
			<span style="text-align:center; text-transform:uppercase; font-size:17px; font-weight:bold; padding-top:10px;">' . $namaperusahaan . '</span><br>	
			<span style="text-align:center; font-size:17px; font-weight:bold; padding-top:10px;">LAPORAN BUKU BESAR</span>	
		';
        $pdf->SetFont('times', '', 14);
        $pdf->writeHTML($title, true, false, false, false, '');
        $pdf->SetTopMargin(15);

        $title_periode = '<div style="text-align:center; padding-top:10px;"> Periode ' . ($periode) . '</div>';
        $pdf->SetFont('times', '', 12);
        $pdf->writeHTML($title_periode, true, false, false, false, '');

        // --- LOOPING SETIAP AKUN ---
        foreach ($akun->getResult() as $r) {
            
            // Cetak Judul Akun terlebih dahulu agar langsung dieksekusi oleh PDF
            $title_akun = '<br><div style="text-align:left; font-weight:bold; padding-top:10px;">Akun: ' . $r->kdakun . ' - ' . $r->nmakun . '</div>';
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
                            <td style="text-align:center;" colspan="4"><B>TOTAL       </B></td>
                            <td style="text-align:right;"><B>' . ($total1 >= 0 ? number_format($total1, 0, ",", ".") : "(" . number_format($total1 * -1, 0, ",", ".") . ")") . '</B></td>
                            <td style="text-align:right;"><B>' . ($total2 >= 0 ? number_format($total2, 0, ",", ".") : "(" . number_format($total2 * -1, 0, ",", ".") . ")") . '</B></td>
                            <td style="text-align:right;"><B></B></td>
                        </tr>';
            $htmlChunk .= $tableFooter . '<br><br>';
			$pdf->SetFont('times', '', 10);
            $pdf->writeHTML($htmlChunk, true, false, false, false, '');
        }

        $pdf->Output('Laporan Buku Besar All.pdf', 'I');
        exit;
    }

	public function lapBukuBesarExcel($tglawal, $tglakhir, $keyakun, $idperusahaan, $nmakun)
    {
        // Bebaskan limit memori dan waktu eksekusi
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

        if (!empty($idperusahaan)) {
            $namaperusahaan = $rsPerusahaan->namaperusahaan;
        } else {
            $namaperusahaan = '';
        }

        if ($tglawal == $tglakhir) {
            $periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $periode = $this->laporan_model->tglindonesialengkap($tglawal) . ' s/d ' . $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        $builder = $this->db->table('v_akun');
        $akun = $builder->getWhere(array('idperusahaan' => $idperusahaan, 'keyakun' => $keyakun));

        // Setup Header untuk Download CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="Laporan Buku Besar ' . $nmakun . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // BOM untuk Excel

        foreach ($akun->getResult() as $r) {
            
            // Tulis Kop Laporan
            fputcsv($output, [$namaperusahaan]);
            fputcsv($output, ['LAPORAN BUKU BESAR']);
            fputcsv($output, ['Periode ' . $periode]);
            fputcsv($output, []); // Baris kosong
            
            fputcsv($output, ['Akun: ' . $r->kdakun . ' - ' . $r->nmakun]);
            fputcsv($output, ['Tanggal', 'No Jurnal', 'Referensi', 'Keterangan', 'Debet', 'Kredit', 'Saldo']);

            $total1 = 0;
            $total2 = 0;
            $nsaldo = 0;

            $saldonormal = $this->akun_model->get_by_id(encrypt($keyakun), encrypt($idperusahaan))->getRow()->saldonormal;
            $rsData = $this->laporan_model->get_bukubesar($tglawal, $tglakhir, $kdakun, encrypt($idperusahaan), 'asc');
            
            $isFirstRow = true;

            // [PERBAIKAN]: Gunakan getUnbufferedRow() dan proses Saldo Awal di dalam
            while ($data = $rsData->getUnbufferedRow()) {
                
                // Cek Saldo Awal HANYA pada putaran pertama (Mencegah fungsi getRow() merusak data)
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

                $total1 = $total1 + $data->debet;
                $total2 = $total2 + $data->kredit;

                if ($saldonormal == 'D') {
                    $nsaldo -= $data->kredit - $data->debet;
                } else {
                    $nsaldo -= $data->debet - $data->kredit;
                }
                
                $referensi_aman = htmlspecialchars($data->referensi, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $keterangan_aman = htmlspecialchars($data->keterangan, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                
                $tgl_jurnal = '="' . date('d-m-Y', strtotime($data->tgljurnal)) . '"';

                // Tulis langsung ke file
                fputcsv($output, [
                    $tgl_jurnal, // Tanggal rata kiri
                    $data->idjurnal,
                    $referensi_aman,
                    $keterangan_aman,
                    $data->debet,
                    $data->kredit,
                    ($nsaldo >= 0 ? $nsaldo : $nsaldo * -1)
                ]);
            }
            
            // Baris Total Akhir
            fputcsv($output, [
                '', '', '', 'TOTAL', 
                ($total1 >= 0 ? $total1 : $total1 * -1), 
                ($total2 >= 0 ? $total2 : $total2 * -1), 
                ''
            ]);
            fputcsv($output, []); // Baris kosong penutup akun
        }

        fclose($output);
        exit;
    }

	public function lapBukuBesarExcelSemua($tglawal, $tglakhir, $idperusahaan)
    {
        // [OPTIMASI]: Bebaskan limit memori dan waktu eksekusi
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

        // [UBAH KE CSV]: Set Header HTTP
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="Laporan Buku Besar Semua Akun.csv"');
        
        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // Tambahkan BOM agar Excel membaca UTF-8

        // Tulis Kop Utama
        fputcsv($output, [$namaperusahaan]);
        fputcsv($output, ['LAPORAN BUKU BESAR']);
        fputcsv($output, ['Periode ' . $periode]);
        fputcsv($output, []); // Baris kosong

        foreach ($akun->getResult() as $r) {
            // Tulis Judul per Akun
            fputcsv($output, ['Akun: ' . $r->kdakun . ' - ' . $r->nmakun]);
            fputcsv($output, ['Tanggal', 'No Jurnal', 'Referensi', 'Keterangan', 'Debet', 'Kredit', 'Saldo']);

            $total1 = 0;
            $total2 = 0;
            $nsaldo = 0;
            $isFirstRow = true;

            $saldonormal = $this->akun_model->get_by_id(encrypt($r->keyakun), encrypt($idperusahaan))->getRow()->saldonormal;
            
            // Ambil data buku besar (Pastikan model me-return object query, bukan result array)
            $rsData = $this->laporan_model->get_bukubesar($tglawal, $tglakhir, $r->kdakun, encrypt($idperusahaan), 'asc');
            $rsDataSaldo = $this->laporan_model->get_bukubesar_saldoawal($tglawal, $tglakhir, $r->kdakun, $idperusahaan, 'asc');

            // [OPTIMASI]: Loop Unbuffered
            while ($data = $rsData->getUnbufferedRow()) {
                
                // [KUNCI]: Hitung Saldo Awal hanya pada baris pertama agar tidak merusak stream data
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

                // Kalkulasi Saldo
                $total1 += $data->debet;
                $total2 += $data->kredit;

                if ($saldonormal == 'D') {
                    $nsaldo -= $data->kredit - $data->debet;
                } else {
                    $nsaldo -= $data->debet - $data->kredit;
                }

                // [FORMAT TANGGAL]: Trik ="..." agar di Excel otomatis Rata Kiri
                $tgl_jurnal = '="' . date('d-m-Y', strtotime($data->tgljurnal)) . '"';
                
                $referensi_aman = htmlspecialchars($data->referensi, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $keterangan_aman = htmlspecialchars($data->keterangan, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                // Tulis Baris Data ke CSV
                fputcsv($output, [
                    $tgl_jurnal,
                    $data->idjurnal,
                    $referensi_aman,
                    $keterangan_aman,
                    $data->debet,
                    $data->kredit,
                    ($nsaldo >= 0 ? $nsaldo : $nsaldo * -1)
                ]);
            }
            
            // Tulis Baris Total Akun
            fputcsv($output, [
                '', '', '', 'TOTAL', 
                ($total1 >= 0 ? $total1 : $total1 * -1), 
                ($total2 >= 0 ? $total2 : $total2 * -1), 
                ''
            ]);
            fputcsv($output, []); // Jarak antar akun
        }

        fclose($output);
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
