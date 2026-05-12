<?php

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Libraries\Pdf;

class LapPerubahanEkuitasController extends BaseController
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
			$tglawal = date('Y-m-d', strtotime($this->perusahaan_model->get_by_id(encrypt(session()->get('idperusahaan')))->getRow()->tglmulaiusaha));
			$tglakhir = date('Y-m-d');
		}
		
        $tglakhirberjalan = date('Y-01-01', strtotime($tglakhir));
        
        //perbaikan untuk laba ditahan 
		$data['rsData'] = $this->laporan_model->get_lapposisikeuangan($tglawal, $tglakhir, $idperusahaan);
		$data['rsDataBerjalan'] = $this->laporan_model->get_lapposisikeuangan($tglakhirberjalan, $tglakhir, $idperusahaan);


		$data['tglawal'] = $tglawal;
		$data['tglakhir'] = $tglakhir;
		$data['idperusahaan'] = $idperusahaan;
		$data['namaperusahaan'] = $namaperusahaan;
		$data['tglakhir_indo'] = $this->laporan_model->tglindonesialengkap($tglakhir);
		return view('laporan/lapperubahanekuitas/index', $data);
	}

	public function lapPerubahanEkuitasCetak($tglawal, $tglakhir, $idperusahaan)
    {
        // [OPTIMASI 1]: Bebaskan limit memori dan batas waktu eksekusi PHP
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $tglakhirberjalan = date('Y-01-01', strtotime($tglakhir));

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

        // Penarikan Data
        $rsData = $this->laporan_model->get_lapposisikeuangan($tglawal, $tglakhir, $idperusahaan);
        $rsDataBerjalan = $this->laporan_model->get_lapposisikeuangan($tglakhirberjalan, $tglakhir, $idperusahaan);
        
        $totalaset = 0;
        $totalhutangdanmodal = 0;
        $totalasetBerjalan = 0;
        $totalhutangdanmodalBerjalan = 0;
        
        // [OPTIMASI 2]: Gunakan UnbufferedRow agar jutaan data tidak ditarik sekaligus ke RAM
        while ($dataBerjalan = $rsDataBerjalan->getUnbufferedRow()) {
            if (substr($dataBerjalan->kdakun, 0, 1) == '1' && $dataBerjalan->level == '1') {
                $totalasetBerjalan += $dataBerjalan->jumlah;
            }

            if ((substr($dataBerjalan->kdakun, 0, 1) == '2' || substr($dataBerjalan->kdakun, 0, 1) == '3') && $dataBerjalan->level == '4') {
                $totalhutangdanmodalBerjalan += $dataBerjalan->jumlah;
            }
        }
       
        // [OPTIMASI 3]: Gunakan UnbufferedRow untuk Data Utama
        while ($data = $rsData->getUnbufferedRow()) {
            if (substr($data->kdakun, 0, 1) == '1' && $data->level == '1') {
                $totalaset += $data->jumlah;
            }
            if ((substr($data->kdakun, 0, 1) == '2' || substr($data->kdakun, 0, 1) == '3') && $data->level == '4') {
                $totalhutangdanmodal += $data->jumlah;
            }
        }

        $totallabaditahan = ($totalaset - $totalhutangdanmodal) - ($totalasetBerjalan - $totalhutangdanmodalBerjalan);
        $totallabarugiberjalan = $totalasetBerjalan - $totalhutangdanmodalBerjalan;

        // --- SETUP PDF ---
        $pdf = new \App\Libraries\Pdf('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetMargins(20, 20, 10);
        $pdf->setPrintHeader(false);
        $pdf->AddPage();

        $title = '
			<span style="text-align:center; text-transform:uppercase; font-weight:bold; padding-top:10px;">' . $namaperusahaan . '</span><br>	
			<span style="text-align:center; font-weight:bold; padding-top:10px;">PERUBAHAN EKUITAS</span>	
		';
        $pdf->SetFont('times', '', 16);
        $pdf->writeHTML($title, true, false, false, false, '');
        $pdf->SetTopMargin(15);

        if ($tglawal == $tglakhir) {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $Periode = $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        $title_periode = '<div style="text-align:center; padding-top:10px;"> Periode Berakhir s/d ' . ($Periode) . '</div><br><br>';
        $pdf->SetFont('times', '', 12);
        $pdf->writeHTML($title_periode, true, false, false, false, '');

        $table  = '<br><br><br><br><table border="0" cellpadding="2">';
        $table .= ' 
                    <thead>             
                    </thead>
                    <tbody>';

        // --- KUERI AGREGASI SQL (Otomatis aman dari Memory Leak karena mereturn 1 baris) ---

        $modalawal = $this->db->query('
                                                SELECT SUM(kredit)-SUM(debet) as modalawal FROM v_jurnaldetail 
                                                WHERE idperusahaan ="' . $idperusahaan . '" and LEFT(kdakun,3) IN ("311") and tgljurnal between "' . $tglawal . '" and "' . $tglakhir . '";
                                                ')->getRow()->modalawal;
        $modalawal = ($modalawal == '') ? 0 : $modalawal;

        $table .= '<tr>
                        <th width= "75%" style="font-weight:bold; text-align:left;" colspan="3">Modal Awal</th>
                        <th width= "10%" style="font-weight:bold; text-align:left;">Rp.</th>
                        <th width= "15%" style="font-weight:bold; text-align:right;">' . ($modalawal >= 0 ? number_format($modalawal, 0, ",", ".") : "(" . number_format($modalawal * -1, 0, ",", ".") . ")") . '</th>
                </tr>
                ';

        $table .= '<tr><td colspan="5"></td></tr>';

        //Penambahan Investasi
        $penambahaninvestasi = $this->db->query('
                                                SELECT SUM(kredit)-SUM(debet) as penambahaninvestasi FROM v_jurnaldetail 
                                                WHERE idperusahaan ="' . $idperusahaan . '" and LEFT(kdakun,3) IN ("312") and tgljurnal between "' . $tglawal . '" and "' . $tglakhir . '";
                                                ')->getRow()->penambahaninvestasi;
        $penambahaninvestasi = ($penambahaninvestasi == '') ? 0 : $penambahaninvestasi;

        $table .= '<tr>
                        <th width= "50%" style="text-align:left;">' . str_repeat('&nbsp;', 10) . 'Tambahan Modal</th>
                        <th width= "10%" style="text-align:left;">Rp.</th>
                        <th width= "15%" style="text-align:right;">' . ($penambahaninvestasi >= 0 ? number_format($penambahaninvestasi, 0, ",", ".") : "(" . number_format($penambahaninvestasi * -1, 0, ",", ".") . ")") . '</th>
                        <th width= "10%" style="text-align:left;"></th>
                        <th width= "15%" style="text-align:left;"></th>
                </tr>
                ';

        //dividenprive 321000 s/d 321999
        $dividenprives = $this->db->query('
                                SELECT SUM(kredit) - SUM(debet) AS labarugisebelumnya
                                FROM v_jurnaldetail
                                WHERE idperusahaan = "' . $idperusahaan . '"
                                  AND kdakun >= 321001
                                  AND kdakun <= 321999
                                  AND tgljurnal BETWEEN "' . $tglawal . '" AND "' . $tglakhir . '"
                              ')->getRow()->labarugisebelumnya;
        $dividenprive = ($dividenprives == '') ? 0 : $dividenprives;
        
        //penambag pengurang ekuitas 322000 s/d 399999
        $ppekuitas = $this->db->query('
                            SELECT SUM(kredit) - SUM(debet) AS labarugisebelumnya
                            FROM v_jurnaldetail
                            WHERE idperusahaan = "' . $idperusahaan . '"
                              AND kdakun >= 322001
                              AND kdakun <= 399999
                              AND tgljurnal BETWEEN "' . $tglawal . '" AND "' . $tglakhir . '"
                        ')->getRow()->labarugisebelumnya;

        $penambahpengurangekuitas = ($ppekuitas == '') ? 0 : $ppekuitas;
        
        //Laba Rugi Sebelumnya
        $labarugisebelumnya = $this->db->query('
                                                SELECT SUM(kredit)-SUM(debet) as labarugisebelumnya FROM v_jurnaldetail 
                                                WHERE idperusahaan ="' . $idperusahaan . '" and LEFT(kdakun,5) IN ("32111")  and tgljurnal between "' . $tglawal . '" and "' . $tglakhir . '";
                                                ')->getRow()->labarugisebelumnya;
        $labarugisebelumnya = ($labarugisebelumnya == '') ? 0 : $labarugisebelumnya;

        //Laba Bersih
        $lb_penerimaan = $this->db->query('
                                                SELECT SUM(kredit)-SUM(debet) as lb_penerimaan FROM v_jurnaldetail 
                                                WHERE idperusahaan ="' . $idperusahaan . '" and (LEFT(kdakun,2) IN ("71") or LEFT(kdakun,1) IN ("4") ) and tgljurnal between "' . $tglawal . '" and "' . $tglakhir . '";
                                                ')->getRow()->lb_penerimaan;
        $lb_penerimaan = ($lb_penerimaan == '') ? 0 : $lb_penerimaan;

        $lb_pengeluaran = $this->db->query('
                                                SELECT SUM(debet)-SUM(kredit) as lb_pengeluaran FROM v_jurnaldetail 
                                                WHERE idperusahaan ="' . $idperusahaan . '" and (LEFT(kdakun,2) IN ("72") or LEFT(kdakun,1) IN ("5","6") ) and tgljurnal between "' . $tglawal . '" and "' . $tglakhir . '"
                                                ')->getRow()->lb_pengeluaran;
        $lb_pengeluaran = ($lb_pengeluaran == '') ? 0 : $lb_pengeluaran;

        $lababersih = $lb_penerimaan - $lb_pengeluaran + $labarugisebelumnya;

        $table .= '<tr>
                        <th width= "50%" style="text-align:left;">' . str_repeat('&nbsp;', 10) . 'Laba (Rugi) Usaha</th>
                        <th width= "10%" style="text-align:left;">Rp.</th>
                        <th width= "15%" style="border-bottom:1pt solid black; text-align:right;">' . ($totallabarugiberjalan >= 0 ? number_format($totallabarugiberjalan, 0, ",", ".") : "(" . number_format($totallabarugiberjalan * -1, 0, ",", ".") . ")") . '</th>
                        <th width= "10%" style="text-align:left;"></th>
                        <th width= "15%" style="text-align:left;"></th>
                </tr>
                ';
        $totalinvestasi = $penambahaninvestasi + $totallabarugiberjalan;
        $table .= '<tr>
                        <th width= "50%" style="text-align:left;"></th>
                        <th width= "10%" style="text-align:left;">Rp.</th>
                        <th width= "15%" style="text-align:right;">' . ($totalinvestasi >= 0 ? number_format($totalinvestasi, 0, ",", ".") : "(" . number_format($totalinvestasi * -1, 0, ",", ".") . ")") . '</th>
                        <th width= "10%" style="text-align:left;"></th>
                        <th width= "15%" style="text-align:left;"></th>
                </tr>
                ';

        $table .= '<tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                ';

        //dividen atau prive
        $table .= '<tr>
                  <th width= "50%" style="text-align:left;">' . str_repeat('&nbsp;', 10) . 'Dividen/Prive</th>
                  <th width= "10%" style="text-align:left;">Rp.</th>
                  <th width= "15%" style="text-align:right;">' . ($dividenprive >= 0 ? number_format($dividenprive, 0, ",", ".") : "(" . number_format($dividenprive * -1, 0, ",", ".") . ")") . '</th>
                  <th width= "10%" style="text-align:left;"></th>
                  <th width= "15%" style="text-align:left;"></th>
                 </tr>
                 ';
                 
        //penambahan pengurang ekuitas
        $table .= '<tr>
                  <th width= "50%" style="text-align:left;">' . str_repeat('&nbsp;', 10) . 'Penambahan/Pengurangan Ekuitas</th>
                  <th width= "10%" style="text-align:left;">Rp.</th>
                  <th width= "15%" style="text-align:right;">' . ($penambahpengurangekuitas >= 0 ? number_format($penambahpengurangekuitas, 0, ",", ".") : "(" . number_format($penambahpengurangekuitas * -1, 0, ",", ".") . ")") . '</th>
                  <th width= "10%" style="text-align:left;"></th>
                  <th width= "15%" style="text-align:left;"></th>
                 </tr>
                 ';
                 
        // Penarikan / Deviden
        $deviden = $this->db->query('
                                SELECT SUM(kredit)-SUM(debet) as deviden FROM v_jurnaldetail 
                                WHERE idperusahaan ="' . $idperusahaan . '" and LEFT(kdakun,5) IN ("32110") and tgljurnal between "' . $tglawal . '" and "' . $tglakhir . '";
                                ')->getRow()->deviden;
                                
        // [Catatan]: Baris ini dibiarkan sama persis dengan kode Anda, walaupun kueri Deviden di atas tertimpa oleh nilai $totallabaditahan.
        $deviden = ($totallabaditahan == '') ? 0 : $totallabaditahan;
        
        $table .= '<tr>
                        <th width= "50%" style="text-align:left;">' . str_repeat('&nbsp;', 10) . 'Laba Ditahan</th>
                        <th width= "10%" style="text-align:left;">Rp.</th>
                        <th width= "15%" style="border-bottom:1pt solid black; text-align:right;">' . ($deviden >= 0 ? number_format($deviden, 0, ",", ".") : "(" . number_format($deviden * -1, 0, ",", ".") . ")") . '</th>
                        <th width= "10%" style="text-align:left;"></th>
                        <th width= "15%" style="text-align:left;"></th>
                </tr>
                ';
        $totallabaditahanakhir = $totallabaditahan + $dividenprive + $penambahpengurangekuitas;
        $table .= '<tr>
                        <th width= "50%" style="text-align:left;">' . str_repeat('&nbsp;', 10) . 'Laba Ditahan Akhir</th>
                        <th width= "10%" style="text-align:left;">Rp.</th>
                        <th width= "15%" style="text-align:right;">' . ($totallabaditahanakhir >= 0 ? number_format($totallabaditahanakhir, 0, ",", ".") : "(" . number_format($totallabaditahanakhir * -1, 0, ",", ".") . ")") . '</th>
                        <th width= "10%" style="text-align:left;"></th>
                        <th width= "15%" style="text-align:left;"></th>
                </tr>
                ';

        // Keanaikan ekuitas pemilik
        $kenaikanekuitaspemilik = ($totalinvestasi + $deviden)+ $dividenprive + $penambahpengurangekuitas;
        $table .= '<tr>
                        <th width= "50%" style="text-align:left;">Kenaikan (Penurunan) Pada Ekuitas</th>
                        <th width= "10%" style="text-align:left;"></th>
                        <th width= "15%" style="text-align:right;"></th>
                        <th width= "10%" style="text-align:left;">Rp.</th>
                        <th width= "15%" style="border-bottom:1pt solid black; text-align:right;">' . ($kenaikanekuitaspemilik >= 0 ? number_format($kenaikanekuitaspemilik, 0, ",", ".") : "(" . number_format($kenaikanekuitaspemilik * -1, 0, ",", ".") . ")") . '</th>
                </tr>
                ';

        $table .= '<tr><td colspan="5"></td></tr>';
        $totalmodalawal = $modalawal + $kenaikanekuitaspemilik;
        $table .= '<tr>
                        <th width= "75%" style="font-weight:bold; text-align:left;" colspan="3">Modal Akhir Per ' . $this->laporan_model->tglindonesialengkap($tglakhir) . '</th>
                        <th width= "10%" style="font-weight:bold; text-align:left;">Rp.</th>
                        <th width= "15%" style="font-weight:bold; text-align:right;">' . ($totalmodalawal >= 0 ? number_format($totalmodalawal, 0, ",", ".") : "(" . number_format($totalmodalawal * -1, 0, ",", ".") . ")") . '</th>
                </tr>
                ';

        $table .= ' </tbody>
                    </table> ';

        $pdf->SetTopMargin(35);
        $pdf->SetFont('times', '', 10);
        $pdf->writeHTML($table, true, false, false, false, '');
        $pdf->Output('Laporan Perubahan Ekuitas.pdf', 'I');
        exit;
    }
}
