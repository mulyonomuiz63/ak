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
        // [PERBAIKAN 1]: Bypass batas waktu eksekusi dan memori untuk data masif
        ini_set('memory_limit', '-1'); // Gunakan memori server tanpa batas
        set_time_limit(0);             // Jangan hentikan proses meskipun memakan waktu berjam-jam

        $rsDataPerusahaan = $this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow();
        if (!empty($idperusahaan)) {
            $namaperusahaan = $rsDataPerusahaan->namaperusahaan;
            $alamat = $rsDataPerusahaan->alamat;
            $notelp = $rsDataPerusahaan->notelp;
        } else {
            $namaperusahaan = '';
            $alamat = '';
            $notelp = '';
        }

        $rsData = $this->laporan_model->get_jurnal($tglawal, $tglakhir, $idperusahaan, 'asc');

        $pdf = new Pdf('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetMargins(20, 20, 10);
        $pdf->setPrintHeader(false);
        $pdf->AddPage();

        $title = '
			<span style="text-align:center; text-transform:uppercase; font-size:16px; font-weight:bold; padding-top:10px;">' . $namaperusahaan . '</span><br>	
			<span style="text-align:center; font-size:16px; font-weight:bold; padding-top:10px;">JURNAL UMUM</span>	
		';
        $pdf->SetFont('times', '', 14);
        $pdf->writeHTML($title, true, false, false, false, '');
        $pdf->SetTopMargin(15);

        if ($tglawal == $tglakhir) {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal) . ' s/d ' . $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        $title_periode = '<div style="text-align:center; font-size:14px; padding-top:10px;"> Periode ' . ($Periode) . '</div><br><br>';
        $pdf->SetFont('times', '', 10);
        $pdf->writeHTML($title_periode, true, false, false, false, '');

        // [PERBAIKAN 2]: Pisahkan Header dan Footer Tabel agar bisa di-render secara bertahap (Chunking)
        $tableHeader = '
            <table border="0" cellpadding="5">
                <thead>
                    <tr style="background-color:#055F93; color:#ffffff;">
                        <th width="5%" style="font-size:12px; font-weight:bold; text-align:center;">No</th>
                        <th width="12%" style="font-size:12px; font-weight:bold; text-align:center;">Tanggal</th>
                        <th width="15%" style="font-size:12px; font-weight:bold; text-align:center;">No Jurnal</th>
                        <th width="40%" style="font-size:12px; font-weight:bold; text-align:center;">Nama Akun</th>
                        <th width="14%" style="font-size:12px; font-weight:bold; text-align:center;">Debet</th>
                        <th width="14%" style="font-size:12px; font-weight:bold; text-align:center;">Kredit</th>
                    </tr>
                </thead>
                <tbody>';
        
        $tableFooter = '</tbody></table>';

        $total1 = 0;
        $total2 = 0;
        $idjurnal_lama = '';
        $no = 1;

        // Inisialisasi chunk render
        $html = $tableHeader;
        $rowCount = 0;
        $chunkSize = 500; // Render ke PDF dan bersihkan RAM setiap 500 baris

        // [PERBAIKAN 3]: Ganti getResult() menjadi getUnbufferedRow()
        // Ini akan memanggil data 1 per 1 dari database, sehingga RAM sangat hemat!
        while ($data = $rsData->getUnbufferedRow()) {
            $total1 = $total1 + $data->debet;
            $total2 = $total2 + $data->kredit;

            $html .= '
                    <tr nobr="true">
                        <td width="5%"  style="text-align:center;">' . ($data->idjurnal == $idjurnal_lama ? "" : $no++) . '</td>
                        <td width="12%" style="text-align:center;">' . ($data->idjurnal == $idjurnal_lama ? "" : date('d-m-Y', strtotime($data->tgljurnal))) . '</td>
                        <td width="15%" style="text-align:center;">' . ($data->idjurnal == $idjurnal_lama ? "" : $data->idjurnal) . '</td>
                        <td width="40%" style="text-align:left;">' . ($data->debet == 0 ? str_repeat("&nbsp;", 5) : "") . $data->nmakun . '</td>
                        <td width="14%" style="text-align:right;">' . ($data->debet == 0 ? "" : number_format($data->debet, 0, ",", ".")) . '</td>
                        <td width="14%" style="text-align:right;">' . ($data->kredit == 0 ? "" : number_format($data->kredit, 0, ",", ".")) . '</td>            
                    </tr>';

            $idjurnal_lama = $data->idjurnal;
            $rowCount++;

            // [PERBAIKAN 4]: Cetak sebagian ke PDF lalu bersihkan variabel (Mencegah Crash TCPDF DOM)
            if ($rowCount % $chunkSize == 0) {
                $html .= $tableFooter;
                $pdf->writeHTML($html, true, false, false, false, '');
                
                // Mulai string tabel baru untuk batch selanjutnya
                $html = $tableHeader; 
            }
        }

        // [PERBAIKAN 5]: Perbaiki logika Colspan (Total Header ada 4 kolom sisa: 5+12+15+40 = 72%)
        $html .= '
                    <tr nobr="true" style="background-color: #E5E4E2;">
                        <td width="72%" style="text-align:right;" colspan="4"><B>TOTAL       </B></td>
                        <td width="14%" style="text-align:right;"><B>' . number_format($total1, 0, "", '.') . '</B></td>
                        <td width="14%" style="text-align:right;"><B>' . number_format($total2, 0, "", '.') . '</B></td>
                    </tr>';
        
        $html .= $tableFooter;

        // Cetak sisa sisa baris yang belum mencapai 500 (atau kelipatannya)
        $pdf->writeHTML($html, true, false, false, false, '');

        $pdf->Output('Laporan Jurnal.pdf', 'I');
        exit;
    }

	public function lapJurnalCetakExcel($tglawal, $tglakhir, $idperusahaan)
    {
        // [1. BYPASS MEMORY] 
        // Sangat krusial untuk jutaan data: hilangkan batas waktu dan batas memori
        ini_set('memory_limit', '-1'); 
        set_time_limit(0);             

        // [2. AMBIL DATA PERUSAHAAN]
        $rsDataPerusahaan = $this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow();
        if (!empty($idperusahaan)) {
            $namaperusahaan = $rsDataPerusahaan->namaperusahaan;
        } else {
            $namaperusahaan = '';
        }

        // [3. PERIODE]
        if ($tglawal == $tglakhir) {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal) . ' s/d ' . $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        // [4. SETUP HEADER HTTP UNTUK DOWNLOAD CSV]
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="Laporan_Jurnal_Umum.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // [5. BUKA JALUR STREAM LANGSUNG KE BROWSER]
        // "php://output" akan langsung mengirim data yang di-echo ke file yang di-download user, 
        // sehingga RAM server Anda tetap kosong (hemat memori).
        $output = fopen('php://output', 'w');

        // Tambahkan BOM (Byte Order Mark) agar aplikasi seperti MS Excel otomatis membaca UTF-8 dengan benar
        fputs($output, "\xEF\xBB\xBF");

        // [6. TULIS KOP LAPORAN KE CSV]
        // Di CSV tidak ada format tebal (bold) atau tengah (center), jadi kita tulis sebagai baris pertama
        fputcsv($output, [$namaperusahaan]);
        fputcsv($output, ['LAPORAN JURNAL UMUM']);
        fputcsv($output, ['Periode ' . $Periode]);
        fputcsv($output, []); // Baris kosong pemisah

        // [7. TULIS HEADER TABEL]
        // Gunakan delimiter titik koma (;) jika target user Anda menggunakan Excel format Indonesia, 
        // atau biarkan default koma (,) untuk format global. Di sini menggunakan default fputcsv (,).
        fputcsv($output, ['No', 'Tanggal', 'No Jurnal', 'Nama Akun', 'Debet', 'Kredit']);

        // [8. AMBIL DATA DARI DATABASE]
        $rsData = $this->laporan_model->get_jurnal($tglawal, $tglakhir, $idperusahaan, 'asc');

        $total1 = 0;
        $total2 = 0;
        $idjurnal_lama = '';
        $no = 1;

        // [9. LOOPING DATA MENGGUNAKAN UNBUFFERED ROW]
        // SAMA SEPERTI PDF: Wajib gunakan getUnbufferedRow() agar data dipanggil 1 per 1, bukan ditarik sekaligus.
        while ($data = $rsData->getUnbufferedRow()) {
            $total1 = $total1 + $data->debet;
            $total2 = $total2 + $data->kredit;

            // Logika untuk mengosongkan kolom jika ID Jurnalnya sama dengan baris sebelumnya
            $tampil_no = ($data->idjurnal == $idjurnal_lama) ? "" : $no++;
            $tampil_tgl = ($data->idjurnal == $idjurnal_lama) ? "" : date('d-m-Y', strtotime($data->tgljurnal));
            $tampil_nojurnal = ($data->idjurnal == $idjurnal_lama) ? "" : $data->idjurnal;

            // Indentasi spasi untuk akun kredit (di Excel spasi akan terbaca)
            $nama_akun = ($data->debet == 0 ? "     " : "") . $data->nmakun;

            // Tulis baris ini langsung ke file download
            fputcsv($output, [
                $tampil_no,
                $tampil_tgl,
                $tampil_nojurnal,
                $nama_akun,
                // PENTING: Untuk Excel/CSV, jangan gunakan number_format() di sini.
                // Biarkan nilainya berupa angka murni (misal: 100000) agar user bisa melakukan 
                // operasi rumus SUM() di Excel. Jika pakai number_format, Excel akan menganggapnya Teks.
                $data->debet, 
                $data->kredit
            ]);

            $idjurnal_lama = $data->idjurnal;
        }

        // [10. TULIS TOTAL]
        fputcsv($output, [
            '', '', '', 'TOTAL', 
            $total1, 
            $total2
        ]);

        // [11. TUTUP STREAM]
        fclose($output);
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
