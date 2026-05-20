<?php

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Libraries\Pdf;

class LapRasioController extends BaseController
{

	var $namaperusahaan = 'PT. XYZ';

	public function index()
	{
		$bulan = $this->request->getPost('bulan');
		$tahun = $this->request->getPost('tahun') == null ? date('Y') : $this->request->getPost('tahun');



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
		if (!empty($tahun)) {
			if (!empty($bulan)) {
				$mon = $bulan;
				$day = date('d');
				$year = $tahun;
				$tglakhir1 = "$year-$mon-$day";
				$tglakhir = date("Y-m-t", strtotime($tglakhir1));

				$tglawal = date('Y-m-01', strtotime($tglakhir));
			} else {
				$mon = date('12');
				$day = date('d');
				$year = $tahun;
				$tglakhir1 = "$year-$mon-$day";
				$tglakhir = date("Y-m-t", strtotime($tglakhir1));

				$tglawal = date('Y-01-01', strtotime($tglakhir));
			}
		} else {
			$tglakhir = date('Y-m-d');
			$tglawal = date('Y-01-01', strtotime($tglakhir));
		}




		$tglawalPerusahaan = date('Y-m-d', strtotime($this->perusahaan_model->get_by_id(encrypt(session()->get('idperusahaan')))->getRow()->tglmulaiusaha));

		$data['tglawal'] = $tglawalPerusahaan;
		$data['tahun'] = $tahun;
		$data['bulan'] = $bulan;
		$data['idperusahaan'] = $idperusahaan;
        $data['namaperusahaan'] = $namaperusahaan;


		$rsData = $this->laporan_model->get_laprasio($tglawalPerusahaan, $tglakhir, $idperusahaan);
		$rsDatas = $this->laporan_model->get_laprasio($tglawal, $tglakhir, $idperusahaan);
		$totalkasdanbank = 0;
		$totalpersediaan = 0;
		$totalasetlancar = 0;
		$totalaset = 0;
		$totalliabilitasjangkapendek = 0;
		$totalliabilitasjangkapanjang = 0;
		$totalliabilitas = 0;
		$totalekuitas = 0;
		$totalpenjualan = 0;
		$totalpokokpenjualan = 0;
		$totallabakotor = 0;
		$totalbebanusaha = 0;
		$totallabasebelumpajak = 0;
		$totalbebanpajakpenghasilan = 0;
		$totallabasetelahpajak = 0;
		$totalpendapatandanbebanlainnya = 0;

		foreach ($rsData->getResult() as $rows) {
			// kas dan bank 111000	112000
			if (($rows->kdakun == "111000") || $rows->kdakun == "112000") {
				$totalkasdanbank += $rows->jumlah;
			}

			// kas dan bank 11100	11200
			if (($rows->kdakun == "11100") || $rows->kdakun == "11200") {
				$totalkasdanbank += $rows->jumlah;
			}


			//persediaan 117000 118000
			if (($rows->kdakun == "117000")) {
				$totalpersediaan += $rows->jumlah;
			}
			if (($rows->kdakun == "11700")) {
				$totalpersediaan += $rows->jumlah;
			}

			//total aset lancar
			if (($rows->kdakun == "110000")) {
				$totalasetlancar += $rows->jumlah;
			}
			if (($rows->kdakun == "11000")) {
				$totalasetlancar += $rows->jumlah;
			}

			//total aset
			if (($rows->kdakun == "100000")) {
				$totalaset += $rows->jumlah;
			}
			if (($rows->kdakun == "10000")) {
				$totalaset += $rows->jumlah;
			}
			//Liabilitas Jangka Pendek
			if (($rows->kdakun == "210000")) {
				$totalliabilitasjangkapendek += $rows->jumlah;
			}
			if (($rows->kdakun == "21000")) {
				$totalliabilitasjangkapendek += $rows->jumlah;
			}


			//Liabilitas Jangka Panjang
			if (($rows->kdakun == "220000")) {
				$totalliabilitasjangkapanjang += $rows->jumlah;
			}
			if (($rows->kdakun == "22000")) {
				$totalliabilitasjangkapanjang += $rows->jumlah;
			}

			//total Liabilitas
			if (($rows->kdakun == "200000")) {
				$totalliabilitas += $rows->jumlah;
			}
			if (($rows->kdakun == "20000")) {
				$totalliabilitas += $rows->jumlah;
			}

			// Total Ekuitas
		}

		$totalekuitas = $totalaset - $totalliabilitas;

		//perulangan untuk fillter tahun dan bulan
		foreach ($rsDatas->getResult() as $rows) {
			//total penjualan
			if (($rows->kdakun == "400000")) {
				$totalpenjualan += $rows->jumlah;
			}
			if (($rows->kdakun == "40000")) {
				$totalpenjualan += $rows->jumlah;
			}

			// Harga Pokok Penjualan
			if (($rows->kdakun == "500000")) {
				$totalpokokpenjualan += $rows->jumlah;
			}
			if (($rows->kdakun == "50000")) {
				$totalpokokpenjualan += $rows->jumlah;
			}


			// Beban Usaha
			if (($rows->kdakun == "600000")) {
				$totalbebanusaha += $rows->jumlah;
			}
			if (($rows->kdakun == "60000")) {
				$totalbebanusaha += $rows->jumlah;
			}

			//pendapatan dan beban lainnya
			if ($rows->kdakun == "700000") {
				$totalpendapatandanbebanlainnya += $rows->jumlah;
			}
			if (($rows->kdakun == "70000")) {
				$totalpendapatandanbebanlainnya += $rows->jumlah;
			}

			//beban pajak penghasilan
			if (($rows->kdakun == "730000")) {
				$totalbebanpajakpenghasilan += $rows->jumlah;
			}
			if (($rows->kdakun == "73000")) {
				$totalbebanpajakpenghasilan += $rows->jumlah;
			}
		}
		// Laba Kotor
		$totallabakotor = $totalpenjualan - $totalpokokpenjualan;

		//Laba Sebelum Pajak (EBT)
		$totallabasebelumpajak = $totallabakotor - $totalbebanusaha;

		//Laba Setelah Pajak (EAT)
		$totallabasetelahpajak = $totallabasebelumpajak + ($totalpendapatandanbebanlainnya - $totalbebanpajakpenghasilan);

		$data["data"] = array(
			"total_1" => $totalpenjualan != 0 ? round((($totallabakotor / $totalpenjualan) * 100), 2) : 0,
			"total_2" => $totalpenjualan != 0 ? round((($totallabasebelumpajak / $totalpenjualan) * 100), 2) : 0,
			"total_3" => $totalpenjualan != 0 ? round((($totallabasetelahpajak / $totalpenjualan) * 100), 2) : 0,
			"total_4" => $totalaset != 0 ? round((($totallabasebelumpajak / $totalaset) * 100), 2) : 0,
			"total_5" => $totalekuitas != 0 ? round((($totallabasetelahpajak / $totalekuitas) * 100), 2) : 0,
			"total_6" => $totalaset != 0 ? round((($totallabasetelahpajak / $totalaset) * 100), 2) : 0,
			"total_7" => $totalliabilitasjangkapendek != 0 ? round((($totalasetlancar / $totalliabilitasjangkapendek) * 100), 2) : 0,
			"total_8" => ($totalliabilitasjangkapendek) != 0 ? round(((($totalasetlancar - $totalpersediaan) / $totalliabilitasjangkapendek) * 100), 2) : 0,
			"total_9" => $totalliabilitasjangkapendek != 0 ? round(((($totalkasdanbank) / $totalliabilitasjangkapendek) * 100), 2) : 0,
			"total_10" => $totalaset != 0 ? round(((($totalliabilitas) / $totalaset) * 100), 2) : 0,
			"total_11" => $totalekuitas != 0 ? round(((($totalliabilitas) / $totalekuitas) * 100), 2) : 0,
			"total_12" => $totalaset != 0 ? round(((($totalpenjualan) / $totalaset) * 100), 2) : 0,
			"total_13" => $totallabasebelumpajak != 0 ? round(((($totalbebanpajakpenghasilan) / $totallabasebelumpajak) * 100), 2) : 0,
			"total_14" => $totallabasebelumpajak != 0 ? round(22 - ((($totalbebanpajakpenghasilan) / $totallabasebelumpajak) * 100), 2) : 22,

		);
		// var_dump($totallabasebelumpajak, $totallabasetelahpajak, $totalpendapatandanbebanlainnya);
		return view('laporan/laprasio/index', $data);
	}

	public function lapRasioCetak($bulan, $tahun)
    {
        // [OPTIMASI]: Bebaskan limit memori dan batas waktu eksekusi
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        if (session()->get('idpengguna') == '8888888888') {
            $idperusahaan = $this->request->getPost('idperusahaan');
        } else {
            $idperusahaan = session()->get('idperusahaan');
        }

        if (!empty($idperusahaan)) {
            $namaperusahaan = $this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow()->namaperusahaan;
        } else {
            $namaperusahaan = '';
        }

        if (!empty($tahun)) {
            if (!empty($bulan)) {
                $mon = $bulan;
                $day = date('d');
                $year = $tahun;
                $tglakhir1 = "$year-$mon-$day";
                $tglakhir = date("Y-m-t", strtotime($tglakhir1));
                $tglawal = date('Y-m-01', strtotime($tglakhir));
            } else {
                $mon = date('12');
                $day = date('d');
                $year = $tahun;
                $tglakhir1 = "$year-$mon-$day";
                $tglakhir = date("Y-m-t", strtotime($tglakhir1));
                $tglawal = date('Y-01-01', strtotime($tglakhir));
            }
        } else {
            $tglakhir = date('Y-m-d');
            $tglawal = date('Y-01-01', strtotime($tglakhir));
        }

        $tglawalPerusahaan = date('Y-m-d', strtotime($this->perusahaan_model->get_by_id(encrypt(session()->get('idperusahaan')))->getRow()->tglmulaiusaha));

        // Penarikan Data (Pastikan get_laprasio mengembalikan Object Builder / bukan Array)
        $rsData = $this->laporan_model->get_laprasio($tglawalPerusahaan, $tglakhir, $idperusahaan);
        $rsDatas = $this->laporan_model->get_laprasio($tglawal, $tglakhir, $idperusahaan);
        
        $totalkasdanbank = 0;
        $totalpersediaan = 0;
        $totalasetlancar = 0;
        $totalaset = 0;
        $totalliabilitasjangkapendek = 0;
        $totalliabilitasjangkapanjang = 0;
        $totalliabilitas = 0;
        $totalekuitas = 0;
        $totalpenjualan = 0;
        $totalpokokpenjualan = 0;
        $totallabakotor = 0;
        $totalbebanusaha = 0;
        $totallabasebelumpajak = 0;
        $totalbebanpajakpenghasilan = 0;
        $totallabasetelahpajak = 0;
        $totalpendapatandanbebanlainnya = 0;

        // [OPTIMASI]: Iterasi Unbuffered 1 (Data Neraca)
        while ($rows = $rsData->getUnbufferedRow()) {
            if ($rows->kdakun == "111000" || $rows->kdakun == "112000" || $rows->kdakun == "11100" || $rows->kdakun == "11200") {
                $totalkasdanbank += $rows->jumlah;
            }
            if ($rows->kdakun == "117000" || $rows->kdakun == "11700") {
                $totalpersediaan += $rows->jumlah;
            }
            if ($rows->kdakun == "110000" || $rows->kdakun == "11000") {
                $totalasetlancar += $rows->jumlah;
            }
            if ($rows->kdakun == "100000" || $rows->kdakun == "10000") {
                $totalaset += $rows->jumlah;
            }
            if ($rows->kdakun == "210000" || $rows->kdakun == "21000") {
                $totalliabilitasjangkapendek += $rows->jumlah;
            }
            if ($rows->kdakun == "220000" || $rows->kdakun == "22000") {
                $totalliabilitasjangkapanjang += $rows->jumlah;
            }
            if ($rows->kdakun == "200000" || $rows->kdakun == "20000") {
                $totalliabilitas += $rows->jumlah;
            }
        }

        $totalekuitas = $totalaset - $totalliabilitas;

        // [OPTIMASI]: Iterasi Unbuffered 2 (Data Laba Rugi)
        while ($rows = $rsDatas->getUnbufferedRow()) {
            if ($rows->kdakun == "400000" || $rows->kdakun == "40000") {
                $totalpenjualan += $rows->jumlah;
            }
            if ($rows->kdakun == "500000" || $rows->kdakun == "50000") {
                $totalpokokpenjualan += $rows->jumlah;
            }
            if ($rows->kdakun == "600000" || $rows->kdakun == "60000") {
                $totalbebanusaha += $rows->jumlah;
            }
            if ($rows->kdakun == "700000" || $rows->kdakun == "70000") {
                $totalpendapatandanbebanlainnya += $rows->jumlah;
            }
            if ($rows->kdakun == "730000" || $rows->kdakun == "73000") {
                $totalbebanpajakpenghasilan += $rows->jumlah;
            }
        }

        // Kalkulasi Rasio
        $totallabakotor = $totalpenjualan - $totalpokokpenjualan;
        $totallabasebelumpajak = $totallabakotor - $totalbebanusaha;
        $totallabasetelahpajak = $totallabasebelumpajak + ($totalpendapatandanbebanlainnya - $totalbebanpajakpenghasilan);
        
        $spasi = str_repeat('&nbsp;', 4);

        // --- Render PDF ---
        $pdf = new \App\Libraries\Pdf('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetMargins(20, 20, 10);
        $pdf->setPrintHeader(false);
        $pdf->AddPage();

        $title = '
			<span style="text-align:center; text-transform:uppercase; font-weight:bold; padding-top:10px;">' . $namaperusahaan . '</span><br>	
			<span style="text-align:center; font-weight:bold; padding-top:10px;">LAPORAN RASIO KEUANGAN</span>	
		';
        $pdf->SetFont('times', '', 14);
        $pdf->writeHTML($title, true, false, false, false, '');
        $pdf->SetTopMargin(15);

        if ($tglawal == $tglakhir) {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal) . ' s/d ' . $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        $title_periode = '<div style="text-align:center; padding-top:10px;"> Periode ' . ($Periode) . '</div><br>';
        $pdf->SetFont('times', '', 14);
        $pdf->writeHTML($title_periode, true, false, false, false, '');

        $table  = '<br><br><table border="0" cellpadding="3">
                    <thead>
                        <tr style="background-color:#055F93; color:#ffffff;">
                            <th width="30%" style="font-weight:bold; text-align:center;">ANALISIS RASIO KEUANGAN</th>
                            <th width="60%" style="font-weight:bold; text-align:center;">PENJELASAN</th>
                            <th width="10%" style="font-weight:bold; text-align:center;">HASIL</th>
                        </tr>
                    </thead>
                    <tbody>';

        // RASIO PROFITABILITAS
        $table .= '<tr><td colspan="3" style="font-weight:bold">RASIO PROFITABILITAS</td></tr>';
        
        $total_1 = $totalpenjualan != 0 ? round((($totallabakotor / $totalpenjualan) * 100), 2) : 0;
        $total_1 = $total_1 != "-0" ? $total_1 : 0;
        $table .= '<tr>
                    <td width="30%" style="font-style: italic;padding-left:30px">' . $spasi . 'Gross Profit Margin</td>
                    <td width="60%">Mengukur efisiensi perusahaan dalam menghasilkan laba kotor dari penjualan.</td>
                    <td width="10%" style="text-align:center">' . $total_1 . '%</td>
                </tr>';

        $total_2 = $totalpenjualan != 0 ? round((($totallabasebelumpajak / $totalpenjualan) * 100), 2) : 0;
        $total_2 = $total_2 != "-0" ? $total_2 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">' . $spasi . 'Operating Profit Margin</td>
                    <td>Menunjukkan kemampuan perusahaan menghasilkan laba dari operasi.</td>
                    <td style="text-align:center">' . $total_2  . '%</td>
                </tr>';

        $total_3 = $totalpenjualan != 0 ? round((($totallabasetelahpajak / $totalpenjualan) * 100), 2) : 0;
        $total_3 = $total_3 != "-0" ? $total_3 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">' . $spasi . 'Net Profit Margin</td>
                    <td>Mengukur persentase laba bersih dari setiap penjualan.</td>
                    <td style="text-align:center">' . $total_3  . '%</td>
                </tr>';

        $total_4 = $totalaset != 0 ? round((($totallabasebelumpajak / $totalaset) * 100), 2) : 0;
        $total_4 = $total_4 != "-0" ? $total_4 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">' . $spasi . 'Return On Asset (ROA)</td>
                    <td>Menilai efektivitas perusahaan dalam menghasilkan laba dari asetnya.</td>
                    <td style="text-align:center">' . $total_4 . '%</td>
                </tr>';

        $total_5 = $totalekuitas != 0 ? round((($totallabasetelahpajak / $totalekuitas) * 100), 2) : 0;
        $total_5 = $total_5 != "-0" ? $total_5 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">' . $spasi . 'Return On Equity (ROE)</td>
                    <td>Mengukur tingkat pengembalian investasi bagi pemegang saham.</td>
                    <td style="text-align:center">' . $total_5 . '%</td>
                </tr>';

        $total_6 = $totalaset != 0 ? round((($totallabasetelahpajak / $totalaset) * 100), 2) : 0;
        $total_6 = $total_6 != "-0" ? $total_6 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">' . $spasi . 'Return On Investment (ROI)</td>
                    <td>Mengevaluasi efisiensi investasi dalam menghasilkan keuntungan.</td>
                    <td style="text-align:center">' . $total_6 . '%</td>
                </tr>';

        // RASIO LIKUIDITAS
        $table .= '<tr><td colspan="3" style="font-weight:bold">RASIO LIKUIDITAS</td></tr>';
        
        $total_7 = $totalliabilitasjangkapendek != 0 ? round((($totalasetlancar / $totalliabilitasjangkapendek) * 100), 2) : 0;
        $total_7 = $total_7 != "-0" ? $total_7 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">' . $spasi . 'Current Ratio</td>
                    <td>Mengukur kemampuan perusahaan membayar kewajiban jangka pendek.</td>
                    <td style="text-align:center">' . $total_7 . '%</td>
                </tr>';

        $total_9 = $totalliabilitasjangkapendek != 0 ? round(((($totalkasdanbank) / $totalliabilitasjangkapendek) * 100), 2) : 0;
        $total_9 = $total_9 != "-0" ? $total_9 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">' . $spasi . 'Cash Ratio</td>
                    <td>Mengukur kemampuan kas bank untuk melunasi utang jangka pendek.</td>
                    <td style="text-align:center">' . $total_9  . '%</td>
                </tr>';

        // RASIO LEVERAGE
        $table .= '<tr><td colspan="3" style="font-weight:bold">RASIO LAVERAGE</td></tr>';
        
        $total_10 = $totalaset != 0 ? round(((($totalliabilitas) / $totalaset) * 100), 2) : 0;
        $total_10 = $total_10 != "-0" ? $total_10 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">' . $spasi . 'Total Debt Asset Ratio (DAR)</td>
                    <td>Mengukur proporsi aset yang dibiayai oleh utang.</td>
                    <td style="text-align:center">' . $total_10 . '%</td>
                </tr>';

        $total_11 = $totalekuitas != 0 ? round(((($totalliabilitas) / $totalekuitas) * 100), 2) : 0;
        $total_11 = $total_11 != "-0" ? $total_11 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">' . $spasi . 'Total Debt to Total Equity (DER)</td>
                    <td>Menunjukkan perbandingan utang dengan ekuitas pemegang saham.</td>
                    <td style="text-align:center">' . $total_11 . '%</td>
                </tr>';

        // RASIO AKTIVITAS
        $table .= '<tr><td colspan="3" style="font-weight:bold">RASIO AKTIVITAS</td></tr>';
        
        $total_12 = $totalaset != 0 ? round(((($totalpenjualan) / $totalaset) * 100), 2) : 0;
        $total_12 = $total_12 != "-0" ? $total_12 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">' . $spasi . 'Total Asset Turnover</td>
                    <td>Mengukur efisiensi penggunaan seluruh aset dalam menghasilkan penjualan.</td>
                    <td style="text-align:center">' . $total_12 . '%</td>
                </tr>';

        $table .= '</tbody></table>';

        $pdf->SetTopMargin(35);
        $pdf->SetFont('times', '', 10);
        $pdf->writeHTML($table, true, false, false, false, '');
        $bulantahun = bulan_tahun($tglawal) . ' - ' . bulan_tahun($tglakhir);
        $namaPerusahaan = ucwords(strtolower($namaperusahaan));
        $namaFile = 'Laporan Rasio Keuangan ' . $namaPerusahaan . ' ' . $bulantahun . '.pdf';
        $pdf->Output($namaFile, 'I');
        exit;
    }

	public function lapRasioExcel($bulan, $tahun)
    {
        // [OPTIMASI]: Bebaskan limit memori dan batas waktu eksekusi
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        if (session()->get('idpengguna') == '8888888888') {
            $idperusahaan = $this->request->getPost('idperusahaan');
        } else {
            $idperusahaan = session()->get('idperusahaan');
        }

        if (!empty($idperusahaan)) {
            $namaperusahaan = $this->perusahaan_model->get_by_id(encrypt($idperusahaan))->getRow()->namaperusahaan;
        } else {
            $namaperusahaan = '';
        }

        if (!empty($tahun)) {
            if (!empty($bulan)) {
                $mon = $bulan;
                $day = date('d');
                $year = $tahun;
                $tglakhir1 = "$year-$mon-$day";
                $tglakhir = date("Y-m-t", strtotime($tglakhir1));
                $tglawal = date('Y-m-01', strtotime($tglakhir));
            } else {
                $mon = date('12');
                $day = date('d');
                $year = $tahun;
                $tglakhir1 = "$year-$mon-$day";
                $tglakhir = date("Y-m-t", strtotime($tglakhir1));
                $tglawal = date('Y-01-01', strtotime($tglakhir));
            }
        } else {
            $tglakhir = date('Y-m-d');
            $tglawal = date('Y-01-01', strtotime($tglakhir));
        }

        $tglawalPerusahaan = date('Y-m-d', strtotime($this->perusahaan_model->get_by_id(encrypt(session()->get('idperusahaan')))->getRow()->tglmulaiusaha));

        $rsData = $this->laporan_model->get_laprasio($tglawalPerusahaan, $tglakhir, $idperusahaan);
        $rsDatas = $this->laporan_model->get_laprasio($tglawal, $tglakhir, $idperusahaan);
        
        $totalkasdanbank = 0;
        $totalpersediaan = 0;
        $totalasetlancar = 0;
        $totalaset = 0;
        $totalliabilitasjangkapendek = 0;
        $totalliabilitasjangkapanjang = 0;
        $totalliabilitas = 0;
        $totalekuitas = 0;
        $totalpenjualan = 0;
        $totalpokokpenjualan = 0;
        $totallabakotor = 0;
        $totalbebanusaha = 0;
        $totallabasebelumpajak = 0;
        $totalbebanpajakpenghasilan = 0;
        $totallabasetelahpajak = 0;
        $totalpendapatandanbebanlainnya = 0;

        // [OPTIMASI]: Iterasi Unbuffered 1 (Data Neraca)
        while ($rows = $rsData->getUnbufferedRow()) {
            if ($rows->kdakun == "111000" || $rows->kdakun == "112000" || $rows->kdakun == "11100" || $rows->kdakun == "11200") {
                $totalkasdanbank += $rows->jumlah;
            }
            if ($rows->kdakun == "117000" || $rows->kdakun == "11700") {
                $totalpersediaan += $rows->jumlah;
            }
            if ($rows->kdakun == "110000" || $rows->kdakun == "11000") {
                $totalasetlancar += $rows->jumlah;
            }
            if ($rows->kdakun == "100000" || $rows->kdakun == "10000") {
                $totalaset += $rows->jumlah;
            }
            if ($rows->kdakun == "210000" || $rows->kdakun == "21000") {
                $totalliabilitasjangkapendek += $rows->jumlah;
            }
            if ($rows->kdakun == "220000" || $rows->kdakun == "22000") {
                $totalliabilitasjangkapanjang += $rows->jumlah;
            }
            if ($rows->kdakun == "200000" || $rows->kdakun == "20000") {
                $totalliabilitas += $rows->jumlah;
            }
        }

        $totalekuitas = $totalaset - $totalliabilitas;

        // [OPTIMASI]: Iterasi Unbuffered 2 (Data Laba Rugi)
        while ($rows = $rsDatas->getUnbufferedRow()) {
            if ($rows->kdakun == "400000" || $rows->kdakun == "40000") {
                $totalpenjualan += $rows->jumlah;
            }
            if ($rows->kdakun == "500000" || $rows->kdakun == "50000") {
                $totalpokokpenjualan += $rows->jumlah;
            }
            if ($rows->kdakun == "600000" || $rows->kdakun == "60000") {
                $totalbebanusaha += $rows->jumlah;
            }
            if ($rows->kdakun == "700000" || $rows->kdakun == "70000") {
                $totalpendapatandanbebanlainnya += $rows->jumlah;
            }
            if ($rows->kdakun == "730000" || $rows->kdakun == "73000") {
                $totalbebanpajakpenghasilan += $rows->jumlah;
            }
        }

        // Kalkulasi Rasio
        $totallabakotor = $totalpenjualan - $totalpokokpenjualan;
        $totallabasebelumpajak = $totallabakotor - $totalbebanusaha;
        $totallabasetelahpajak = $totallabasebelumpajak + ($totalpendapatandanbebanlainnya - $totalbebanpajakpenghasilan);

        // --- Render Excel ---
        $bulantahun     = bulan_tahun($tglawal) . ' - ' . bulan_tahun($tglakhir);
        $namaPerusahaan = ucwords(strtolower($namaperusahaan));
        $namaFile = 'Laporan Rasio Keuangan ' . $namaPerusahaan . ' ' . $bulantahun . '.xls';
        header("Content-Disposition: attachment; filename=\"" . $namaFile . "\"");
        header("Content-Type: application/vnd.ms-excel");
        header("Cache-Control: max-age=0");

        $table = '
            <div style="text-align:center; font-weight:bold; padding-top:10px;">' . $namaperusahaan . '</div>
            <div style="text-align:center; font-weight:bold; padding-top:10px;">LAPORAN RASIO KEUANGAN</div>  
        ';

        if ($tglawal == $tglakhir) {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal);
        } else {
            $Periode = $this->laporan_model->tglindonesialengkap($tglawal) . ' s/d ' . $this->laporan_model->tglindonesialengkap($tglakhir);
        }

        $table .= '<div style="text-align:center; padding-top:10px;"> Periode ' . ($Periode) . '</div>';

        $table  .= '<br><br><table border="1" cellpadding="5">
                    <thead>
                        <tr style="background-color:#ccc;">
                            <th width="30%" style="font-weight:bold; text-align:center;">ANALISIS RASIO KEUANGAN</th>
                            <th width="60%" style="font-weight:bold; text-align:center;">PENJELASAN</th>
                            <th width="10%" style="font-weight:bold; text-align:center;">HASIL</th>
                        </tr>
                    </thead>
                    <tbody>';

        // RASIO PROFITABILITAS
        $table .= '<tr><td colspan="3" style="font-weight:bold">RASIO PROFITABILITAS</td></tr>';
        
        $total_1 = $totalpenjualan != 0 ? round((($totallabakotor / $totalpenjualan) * 100), 2) : 0;
        $total_1 = $total_1 != "-0" ? $total_1 : 0;
        $table .= '<tr>
                    <td width="30%" style="font-style: italic;padding-left:30px">Gross Profit Margin</td>
                    <td width="60%">Mengukur efisiensi perusahaan dalam menghasilkan laba kotor dari penjualan.</td>
                    <td width="10%" style="text-align:center">' . $total_1 . '%</td>
                </tr>';

        $total_2 = $totalpenjualan != 0 ? round((($totallabasebelumpajak / $totalpenjualan) * 100), 2) : 0;
        $total_2 = $total_2 != "-0" ? $total_2 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">Operating Profit Margin</td>
                    <td>Menunjukkan kemampuan perusahaan menghasilkan laba dari operasi.</td>
                    <td style="text-align:center">' . $total_2  . '%</td>
                </tr>';

        $total_3 = $totalpenjualan != 0 ? round((($totallabasetelahpajak / $totalpenjualan) * 100), 2) : 0;
        $total_3 = $total_3 != "-0" ? $total_3 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">Net Profit Margin</td>
                    <td>Mengukur persentase laba bersih dari setiap penjualan.</td>
                    <td style="text-align:center">' . $total_3 . '%</td>
                </tr>';

        $total_4 = $totalaset != 0 ? round((($totallabasebelumpajak / $totalaset) * 100), 2) : 0;
        $total_4 = $total_4 != "-0" ? $total_4 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">Return On Asset (ROA)</td>
                    <td>Menilai efektivitas perusahaan dalam menghasilkan laba dari asetnya.</td>
                    <td style="text-align:center">' . $total_4 . '%</td>
                </tr>';

        $total_5 = $totalekuitas != 0 ? round((($totallabasetelahpajak / $totalekuitas) * 100), 2) : 0;
        $total_5 = $total_5 != "-0" ? $total_5 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">Return On Equity (ROE)</td>
                    <td>Mengukur tingkat pengembalian investasi bagi pemegang saham.</td>
                    <td style="text-align:center">' . $total_5 . '%</td>
                </tr>';

        $total_6 = $totalaset != 0 ? round((($totallabasetelahpajak / $totalaset) * 100), 2) : 0;
        $total_6 = $total_6 != "-0" ? $total_6 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">Return On Investment (ROI)</td>
                    <td>Mengevaluasi efisiensi investasi dalam menghasilkan keuntungan.</td>
                    <td style="text-align:center">' . $total_6 . '%</td>
                </tr>';

        // RASIO LIKUIDITAS
        $table .= '<tr><td colspan="3" style="font-weight:bold">RASIO LIKUIDITAS</td></tr>';
        
        $total_7 = $totalliabilitasjangkapendek != 0 ? round((($totalasetlancar / $totalliabilitasjangkapendek) * 100), 2) : 0;
        $total_7 = $total_7 != "-0" ? $total_7 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">Current Ratio</td>
                    <td>Mengukur kemampuan perusahaan membayar kewajiban jangka pendek.</td>
                    <td style="text-align:center">' . $total_7 . '%</td>
                </tr>';

        $total_9 = $totalliabilitasjangkapendek != 0 ? round(((($totalkasdanbank) / $totalliabilitasjangkapendek) * 100), 2) : 0;
        $total_9 = $total_9 != "-0" ? $total_9 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">Cash Ratio</td>
                    <td>Mengukur kemampuan kas bank untuk melunasi utang jangka pendek.</td>
                    <td style="text-align:center">' . $total_9 . '%</td>
                </tr>';

        // RASIO LEVERAGE
        $table .= '<tr><td colspan="3" style="font-weight:bold">RASIO LAVERAGE</td></tr>';
        
        $total_10 = $totalaset != 0 ? round(((($totalliabilitas) / $totalaset) * 100), 2) : 0;
        $total_10 = $total_10 != "-0" ? $total_10 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">Total Debt Asset Ratio (DAR)</td>
                    <td>Mengukur proporsi aset yang dibiayai oleh utang.</td>
                    <td style="text-align:center">' . $total_10 . '%</td>
                </tr>';

        $total_11 = $totalekuitas != 0 ? round(((($totalliabilitas) / $totalekuitas) * 100), 2) : 0;
        $total_11 = $total_11 != "-0" ? $total_11 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">Total Debt to Total Equity (DER)</td>
                    <td>Menunjukkan perbandingan utang dengan ekuitas pemegang saham.</td>
                    <td style="text-align:center">' . $total_11 . '%</td>
                </tr>';

        // RASIO AKTIVITAS
        $table .= '<tr><td colspan="3" style="font-weight:bold">RASIO AKTIVITAS</td></tr>';
        
        $total_12 = $totalaset != 0 ? round(((($totalpenjualan) / $totalaset) * 100), 2) : 0;
        $total_12 = $total_12 != "-0" ? $total_12 : 0;
        $table .= '<tr>
                    <td style="font-style: italic;padding-left:30px">Total Asset Turnover</td>
                    <td>Mengukur efisiensi penggunaan seluruh aset dalam menghasilkan penjualan.</td>
                    <td style="text-align:center">' . $total_12 . '%</td>
                </tr>';

        $table .= '</tbody></table>';

        echo $table;
    }
}
