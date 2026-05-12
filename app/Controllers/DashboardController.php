<?php

namespace App\Controllers;

class DashboardController extends BaseController
{

	public $menuaktif = 'Dashboard';

	
	public function index()
	{
		$idperusahaan = session()->idperusahaan;

		//Laba Rugi
		$rslabarugi = $this->db->query("SELECT 
											SUM(CASE WHEN LEFT(kdakun,1)='4' OR LEFT(kdakun,2) ='71' THEN  kredit - debet ELSE 0 END ) AS jlhpenerimaan ,
											SUM(CASE WHEN LEFT(kdakun,1)='5' OR LEFT(kdakun,1)='6' OR LEFT(kdakun,2) ='72'  OR LEFT(kdakun,2) ='73'  THEN  debet - kredit ELSE 0 END) AS jlhpengeluaran,
											SUM(CASE WHEN LEFT(kdakun,2) ='73'  THEN  debet - kredit ELSE 0 END) AS jlhbebanpajak  
											FROM v_jurnaldetail WHERE YEAR(tgljurnal) = YEAR(NOW()) and idperusahaan='" . $idperusahaan . "'")->getRow();
		$jlhlabarugi = ($rslabarugi->jlhpenerimaan - $rslabarugi->jlhpengeluaran);
		//Penjualan
		$jlhpenjualan = $this->db->query("SELECT SUM(kredit) - SUM(debet) as jlhpenjualan FROM v_jurnaldetail where left(kdakun,1)='4' and YEAR(tgljurnal)=YEAR(NOW()) and idperusahaan='" . $idperusahaan . "'")->getRow()->jlhpenjualan;

		//Piutang All
		$jlhpiutang = $this->db->query("SELECT SUM(debet) - SUM(kredit) AS jlhpiutang FROM v_jurnaldetail 
												WHERE LEFT(kdakun,3) IN ('113', '114') and idperusahaan='" . $idperusahaan . "'")->getRow()->jlhpiutang;

		//hutang all
		$jlhhutang = $this->db->query("SELECT  SUM(kredit) - SUM(debet) AS jlhhutang FROM v_jurnaldetail 
											WHERE LEFT(kdakun,1) = '2' and idperusahaan='" . $idperusahaan . "'")->getRow()->jlhhutang;

		$data['jlhlabarugi'] = ($jlhlabarugi == '') ? 0 : $jlhlabarugi;
		$data['jlhpiutang'] = ($jlhpiutang == '') ? 0 : $jlhpiutang;
		$data['jlhpenjualan'] = ($jlhpenjualan == '') ? 0 : $jlhpenjualan;
		$data['jlhhutang'] = ($jlhhutang == '') ? 0 : $jlhhutang;
		$data['menuaktif'] = $this->menuaktif;
		
		//banner
		$builder = $this->db->table('iklan')->where('status !=','depan');
		$data['banner'] =  $builder->get()->getResultObject();
		//event
		$builderevent = $this->db->table('event')->where('status','event');
		$data['event'] =  $builderevent->get()->getResultObject();
        // var_dump($data['banner']);
		return view('dashboard', $data); 
	}

	public function get_grafik_penjualan()
	{
		$idperusahaan = $this->session->get('idperusahaan');
		$mingguan = $this->request->getPost('mingguan');
		$bulanan = $this->request->getPost('bulanan');
		$tahunan = $this->request->getPost('tahunan');

		if ($mingguan != '0' && $bulanan == '0') {
			return $this->response->setJSON(array('msg' => "Pilih Bulan"));
			exit();
		}

		// ------------------------------------------------ > Kondisi Tahunan
		if ($tahunan != '0' && $bulanan == '0' && $mingguan == '0') {
			$query = "
					SELECT 
					SUM(CASE WHEN MONTH(tgljurnal) = 1 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln1,
					SUM(CASE WHEN MONTH(tgljurnal) = 2 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln2,
					SUM(CASE WHEN MONTH(tgljurnal) = 3 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln3,
					SUM(CASE WHEN MONTH(tgljurnal) = 4 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln4,
					SUM(CASE WHEN MONTH(tgljurnal) = 5 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln5,
					SUM(CASE WHEN MONTH(tgljurnal) = 6 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln6,
					SUM(CASE WHEN MONTH(tgljurnal) = 7 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln7,
					SUM(CASE WHEN MONTH(tgljurnal) = 8 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln8,
					SUM(CASE WHEN MONTH(tgljurnal) = 9 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln9,
					SUM(CASE WHEN MONTH(tgljurnal) = 10 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln10,
					SUM(CASE WHEN MONTH(tgljurnal) = 11 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln11, 
					SUM(CASE WHEN MONTH(tgljurnal) = 12 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln12  
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and year(tgljurnal)= " . $tahunan . " ";

			$labels = array('Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des');

			$where = " and LEFT(kdakun,1)='4' ";
			$row = $this->db->query($query . $where)->getRow();
			$data_lsm = array(
				$row->bln1,
				$row->bln2,
				$row->bln3,
				$row->bln4,
				$row->bln5,
				$row->bln6,
				$row->bln7,
				$row->bln8,
				$row->bln9,
				$row->bln10,
				$row->bln11,
				$row->bln12,
			);
		}

		// ------------------------------------------------ > Kondisi Bulanan
		if ($bulanan != '0' && $mingguan == '0') {
			$query = "
					SELECT 
					SUM(CASE WHEN DAY(tgljurnal) IN (1,2,3) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl3,
					SUM(CASE WHEN DAY(tgljurnal) IN (4,5,6) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl6,
					SUM(CASE WHEN DAY(tgljurnal) IN (7,8,9) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl9,
					SUM(CASE WHEN DAY(tgljurnal) IN (10,11,12) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl12,
					SUM(CASE WHEN DAY(tgljurnal) IN (13,14,15) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl15,
					SUM(CASE WHEN DAY(tgljurnal) IN (16,17,18) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl18,
					SUM(CASE WHEN DAY(tgljurnal) IN (19,20,21) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl21,
					SUM(CASE WHEN DAY(tgljurnal) IN (22,23,24) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl24,
					SUM(CASE WHEN DAY(tgljurnal) IN (25,26,27) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl27,
					SUM(CASE WHEN DAY(tgljurnal) IN (28,29,30) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl30,
					SUM(CASE WHEN DAY(tgljurnal) IN (31) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl31 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";
			$labels = array('Tgl 3', 'Tgl 6', 'Tgl 9', 'Tgl 12', 'Tgl 15', 'Tgl 18', 'Tgl 21', 'Tgl 24', 'Tgl 27', 'Tgl 30', 'Tgl 31');

			$where = " and LEFT(kdakun,1)='4' ";
			$row = $this->db->query($query . $where)->getRow();
			$data_lsm = array($row->tgl3, $row->tgl6, $row->tgl9, $row->tgl12, $row->tgl15, $row->tgl18, $row->tgl21, $row->tgl24, $row->tgl27, $row->tgl30, $row->tgl31);
		}


		// ------------------------------------------------ > Kondisi Mingguan
		switch ($mingguan) {
			case '1':
				$query = "SELECT 
					SUM(CASE WHEN DAY(tgljurnal) = 1 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl1,
					SUM(CASE WHEN DAY(tgljurnal) = 2 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl2,
					SUM(CASE WHEN DAY(tgljurnal) = 3 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl3,
					SUM(CASE WHEN DAY(tgljurnal) = 4 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl4,
					SUM(CASE WHEN DAY(tgljurnal) = 5 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl5,
					SUM(CASE WHEN DAY(tgljurnal) = 6 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl6,
					SUM(CASE WHEN DAY(tgljurnal) = 7 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl7 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and tgljurnal is not null and day(tgljurnal) between 1 and 7 and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";

				$labels = array('Tgl 1', 'Tgl 2', 'Tgl 3', 'Tgl 4', 'Tgl 5', 'Tgl 6', 'Tgl 7');

				$where = " and LEFT(kdakun,1)='4' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_lsm = array(
					$row->tgl1,
					$row->tgl2,
					$row->tgl3,
					$row->tgl4,
					$row->tgl5,
					$row->tgl6,
					$row->tgl7
				);

				break;
			case '2':
				$query = "SELECT 
					SUM(CASE WHEN DAY(tgljurnal) = 8 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl8,
					SUM(CASE WHEN DAY(tgljurnal) = 9 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl9,
					SUM(CASE WHEN DAY(tgljurnal) = 10 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl10,
					SUM(CASE WHEN DAY(tgljurnal) = 11 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl11,
					SUM(CASE WHEN DAY(tgljurnal) = 12 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl12,
					SUM(CASE WHEN DAY(tgljurnal) = 13 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl13,
					SUM(CASE WHEN DAY(tgljurnal) = 14 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl14 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and tgljurnal is not null and day(tgljurnal) between 8 and 14 and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";
				$labels = array('Tgl 8', 'Tgl 9', 'Tgl 10', 'Tgl 11', 'Tgl 12', 'Tgl 13', 'Tgl 14');

				$where = " and LEFT(kdakun,1)='4' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_lsm = array(
					$row->tgl8,
					$row->tgl9,
					$row->tgl10,
					$row->tgl11,
					$row->tgl12,
					$row->tgl13,
					$row->tgl14
				);


				break;
			case '3':
				$query = "SELECT 
					SUM(CASE WHEN DAY(tgljurnal) = 15 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl15,
					SUM(CASE WHEN DAY(tgljurnal) = 16 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl16,
					SUM(CASE WHEN DAY(tgljurnal) = 17 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl17,
					SUM(CASE WHEN DAY(tgljurnal) = 18 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl18,
					SUM(CASE WHEN DAY(tgljurnal) = 19 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl19,
					SUM(CASE WHEN DAY(tgljurnal) = 20 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl20,
					SUM(CASE WHEN DAY(tgljurnal) = 21 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl21 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and tgljurnal is not null and day(tgljurnal) between 15 and 21 and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";

				$labels = array('Tgl 15', 'Tgl 16', 'Tgl 17', 'Tgl 18', 'Tgl 19', 'Tgl 20', 'Tgl 21');

				$where = " and LEFT(kdakun,1)='4' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_lsm = array(
					$row->tgl15,
					$row->tgl16,
					$row->tgl17,
					$row->tgl18,
					$row->tgl19,
					$row->tgl20,
					$row->tgl21
				);


				break;
			case '4':
				$query = "SELECT 
					SUM(CASE WHEN DAY(tgljurnal) = 22 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl22,
					SUM(CASE WHEN DAY(tgljurnal) = 23 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl23,
					SUM(CASE WHEN DAY(tgljurnal) = 24 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl24,
					SUM(CASE WHEN DAY(tgljurnal) = 25 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl25,
					SUM(CASE WHEN DAY(tgljurnal) = 26 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl26,
					SUM(CASE WHEN DAY(tgljurnal) = 27 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl27,
					SUM(CASE WHEN DAY(tgljurnal) = 28 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl28 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and tgljurnal is not null and day(tgljurnal) between 22 and 28 and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";

				$labels = array('Tgl 22', 'Tgl 23', 'Tgl 24', 'Tgl 25', 'Tgl 26', 'Tgl 27', 'Tgl 28');

				$where = " and LEFT(kdakun,1)='4' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_lsm = array(
					$row->tgl22,
					$row->tgl23,
					$row->tgl24,
					$row->tgl25,
					$row->tgl26,
					$row->tgl27,
					$row->tgl28
				);


				break;
			case '5':
				$query = "SELECT 
					SUM(CASE WHEN DAY(tgljurnal) = 29 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl29,
					SUM(CASE WHEN DAY(tgljurnal) = 30 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl30,
					SUM(CASE WHEN DAY(tgljurnal) = 31 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl31 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and tgljurnal is not null  and day(tgljurnal) between 29 and 31 and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";

				$labels = array('Tgl 29', 'Tgl 30', 'Tgl 31');

				$where = " and LEFT(kdakun,1)='4' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_lsm = array(
					$row->tgl29,
					$row->tgl30,
					$row->tgl31
				);
				break;
		}


		// echo json_encode(array('data' => $data));
		echo json_encode(array('success' => true, 'data_lsm' => $data_lsm, 'labels' => $labels));
	}


	public function get_grafik_penjualan_asli()
	{
		$idperusahaan = $this->session->get('idperusahaan');
		$mingguan = $this->request->getPost('mingguan');
		$bulanan = $this->request->getPost('bulanan');
		$tahunan = $this->request->getPost('tahunan');

		if ($mingguan != '0' && $bulanan == '0') {
			echo json_encode(array('msg' => "Pilih Bulan"));
			exit();
		}

		// ------------------------------------------------ > Kondisi Tahunan
		if ($tahunan != '0' && $bulanan == '0' && $mingguan == '0') {
			$query = "
					SELECT 
					SUM(CASE WHEN MONTH(tgljurnal) = 1 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln1,
					SUM(CASE WHEN MONTH(tgljurnal) = 2 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln2,
					SUM(CASE WHEN MONTH(tgljurnal) = 3 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln3,
					SUM(CASE WHEN MONTH(tgljurnal) = 4 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln4,
					SUM(CASE WHEN MONTH(tgljurnal) = 5 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln5,
					SUM(CASE WHEN MONTH(tgljurnal) = 6 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln6,
					SUM(CASE WHEN MONTH(tgljurnal) = 7 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln7,
					SUM(CASE WHEN MONTH(tgljurnal) = 8 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln8,
					SUM(CASE WHEN MONTH(tgljurnal) = 9 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln9,
					SUM(CASE WHEN MONTH(tgljurnal) = 10 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln10,
					SUM(CASE WHEN MONTH(tgljurnal) = 11 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln11, 
					SUM(CASE WHEN MONTH(tgljurnal) = 12 THEN (kredit-debet) / 1000000 ELSE 0 END) AS bln12  
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and year(tgljurnal)= " . $tahunan . " ";

			$labels = array('Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des');

			$where = " and LEFT(kdakun,1)='411' ";
			$row = $this->db->query($query . $where)->getRow();
			$data_lsm = array(
				$row->bln1,
				$row->bln2,
				$row->bln3,
				$row->bln4,
				$row->bln5,
				$row->bln6,
				$row->bln7,
				$row->bln8,
				$row->bln9,
				$row->bln10,
				$row->bln11,
				$row->bln12,
			);

			$where = " and LEFT(kdakun,3)='412' ";
			$row = $this->db->query($query . $where)->getRow();
			$data_ehtml = array(
				$row->bln1,
				$row->bln2,
				$row->bln3,
				$row->bln4,
				$row->bln5,
				$row->bln6,
				$row->bln7,
				$row->bln8,
				$row->bln9,
				$row->bln10,
				$row->bln11,
				$row->bln12,
			);

			$where = " and LEFT(kdakun,3)='413' ";
			$row = $this->db->query($query . $where)->getRow();
			$data_eflash = array(
				$row->bln1,
				$row->bln2,
				$row->bln3,
				$row->bln4,
				$row->bln5,
				$row->bln6,
				$row->bln7,
				$row->bln8,
				$row->bln9,
				$row->bln10,
				$row->bln11,
				$row->bln12,
			);

			$where = " and LEFT(kdakun,3)='414' ";
			$row = $this->db->query($query . $where)->getRow();
			$data_video = array(
				$row->bln1,
				$row->bln2,
				$row->bln3,
				$row->bln4,
				$row->bln5,
				$row->bln6,
				$row->bln7,
				$row->bln8,
				$row->bln9,
				$row->bln10,
				$row->bln11,
				$row->bln12,
			);
		}

		// ------------------------------------------------ > Kondisi Bulanan
		if ($bulanan != '0' && $mingguan == '0') {
			$query = "
					SELECT 
					SUM(CASE WHEN DAY(tgljurnal) IN (1,2,3) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl3,
					SUM(CASE WHEN DAY(tgljurnal) IN (4,5,6) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl6,
					SUM(CASE WHEN DAY(tgljurnal) IN (7,8,9) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl9,
					SUM(CASE WHEN DAY(tgljurnal) IN (10,11,12) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl12,
					SUM(CASE WHEN DAY(tgljurnal) IN (13,14,15) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl15,
					SUM(CASE WHEN DAY(tgljurnal) IN (16,17,18) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl18,
					SUM(CASE WHEN DAY(tgljurnal) IN (19,20,21) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl21,
					SUM(CASE WHEN DAY(tgljurnal) IN (22,23,24) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl24,
					SUM(CASE WHEN DAY(tgljurnal) IN (25,26,27) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl27,
					SUM(CASE WHEN DAY(tgljurnal) IN (28,29,30) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl30,
					SUM(CASE WHEN DAY(tgljurnal) IN (31) THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl31 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";
			$labels = array('Tgl 3', 'Tgl 6', 'Tgl 9', 'Tgl 12', 'Tgl 15', 'Tgl 18', 'Tgl 21', 'Tgl 24', 'Tgl 27', 'Tgl 30', 'Tgl 31');

			$where = " and LEFT(kdakun,1)='411' ";
			$row = $this->db->query($query . $where)->getRow();
			$data_lsm = array($row->tgl3, $row->tgl6, $row->tgl9, $row->tgl12, $row->tgl15, $row->tgl18, $row->tgl21, $row->tgl24, $row->tgl27, $row->tgl30, $row->tgl31);

			$where = " and LEFT(kdakun,3)='412' ";
			$row = $this->db->query($query . $where)->getRow();
			$data_ehtml = array($row->tgl3, $row->tgl6, $row->tgl9, $row->tgl12, $row->tgl15, $row->tgl18, $row->tgl21, $row->tgl24, $row->tgl27, $row->tgl30, $row->tgl31);

			$where = " and LEFT(kdakun,3)='413' ";
			$row = $this->db->query($query . $where)->getRow();
			$data_eflash = array($row->tgl3, $row->tgl6, $row->tgl9, $row->tgl12, $row->tgl15, $row->tgl18, $row->tgl21, $row->tgl24, $row->tgl27, $row->tgl30, $row->tgl31);

			$where = " and LEFT(kdakun,3)='414' ";
			$row = $this->db->query($query . $where)->getRow();
			$data_video = array($row->tgl3, $row->tgl6, $row->tgl9, $row->tgl12, $row->tgl15, $row->tgl18, $row->tgl21, $row->tgl24, $row->tgl27, $row->tgl30, $row->tgl31);
		}


		// ------------------------------------------------ > Kondisi Mingguan
		switch ($mingguan) {
			case '1':
				$query = "SELECT 
					SUM(CASE WHEN DAY(tgljurnal) = 1 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl1,
					SUM(CASE WHEN DAY(tgljurnal) = 2 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl2,
					SUM(CASE WHEN DAY(tgljurnal) = 3 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl3,
					SUM(CASE WHEN DAY(tgljurnal) = 4 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl4,
					SUM(CASE WHEN DAY(tgljurnal) = 5 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl5,
					SUM(CASE WHEN DAY(tgljurnal) = 6 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl6,
					SUM(CASE WHEN DAY(tgljurnal) = 7 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl7 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and tgljurnal is not null and day(tgljurnal) between 1 and 7 and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";

				$labels = array('Tgl 1', 'Tgl 2', 'Tgl 3', 'Tgl 4', 'Tgl 5', 'Tgl 6', 'Tgl 7');

				$where = " and LEFT(kdakun,1)='411' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_lsm = array(
					$row->tgl1,
					$row->tgl2,
					$row->tgl3,
					$row->tgl4,
					$row->tgl5,
					$row->tgl6,
					$row->tgl7
				);

				$where = " and LEFT(kdakun,3)='412' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_ehtml = array(
					$row->tgl1,
					$row->tgl2,
					$row->tgl3,
					$row->tgl4,
					$row->tgl5,
					$row->tgl6,
					$row->tgl7
				);

				$where = " and LEFT(kdakun,3)='413' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_eflash = array(
					$row->tgl1,
					$row->tgl2,
					$row->tgl3,
					$row->tgl4,
					$row->tgl5,
					$row->tgl6,
					$row->tgl7
				);

				$where = " and LEFT(kdakun,3)='414' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_video = array(
					$row->tgl1,
					$row->tgl2,
					$row->tgl3,
					$row->tgl4,
					$row->tgl5,
					$row->tgl6,
					$row->tgl7
				);
				break;
			case '2':
				$query = "SELECT 
					SUM(CASE WHEN DAY(tgljurnal) = 8 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl8,
					SUM(CASE WHEN DAY(tgljurnal) = 9 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl9,
					SUM(CASE WHEN DAY(tgljurnal) = 10 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl10,
					SUM(CASE WHEN DAY(tgljurnal) = 11 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl11,
					SUM(CASE WHEN DAY(tgljurnal) = 12 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl12,
					SUM(CASE WHEN DAY(tgljurnal) = 13 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl13,
					SUM(CASE WHEN DAY(tgljurnal) = 14 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl14 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and tgljurnal is not null and day(tgljurnal) between 8 and 14 and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";
				$labels = array('Tgl 8', 'Tgl 9', 'Tgl 10', 'Tgl 11', 'Tgl 12', 'Tgl 13', 'Tgl 14');

				$where = " and LEFT(kdakun,1)='411' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_lsm = array(
					$row->tgl8,
					$row->tgl9,
					$row->tgl10,
					$row->tgl11,
					$row->tgl12,
					$row->tgl13,
					$row->tgl14
				);

				$where = " and LEFT(kdakun,3)='412' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_ehtml = array(
					$row->tgl8,
					$row->tgl9,
					$row->tgl10,
					$row->tgl11,
					$row->tgl12,
					$row->tgl13,
					$row->tgl14
				);

				$where = " and LEFT(kdakun,3)='413' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_eflash = array(
					$row->tgl8,
					$row->tgl9,
					$row->tgl10,
					$row->tgl11,
					$row->tgl12,
					$row->tgl13,
					$row->tgl14
				);

				$where = " and LEFT(kdakun,3)='414' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_video = array(
					$row->tgl8,
					$row->tgl9,
					$row->tgl10,
					$row->tgl11,
					$row->tgl12,
					$row->tgl13,
					$row->tgl14
				);

				break;
			case '3':
				$query = "SELECT 
					SUM(CASE WHEN DAY(tgljurnal) = 15 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl15,
					SUM(CASE WHEN DAY(tgljurnal) = 16 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl16,
					SUM(CASE WHEN DAY(tgljurnal) = 17 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl17,
					SUM(CASE WHEN DAY(tgljurnal) = 18 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl18,
					SUM(CASE WHEN DAY(tgljurnal) = 19 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl19,
					SUM(CASE WHEN DAY(tgljurnal) = 20 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl20,
					SUM(CASE WHEN DAY(tgljurnal) = 21 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl21 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and tgljurnal is not null and day(tgljurnal) between 15 and 21 and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";

				$labels = array('Tgl 15', 'Tgl 16', 'Tgl 17', 'Tgl 18', 'Tgl 19', 'Tgl 20', 'Tgl 21');

				$where = " and LEFT(kdakun,1)='411' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_lsm = array(
					$row->tgl15,
					$row->tgl16,
					$row->tgl17,
					$row->tgl18,
					$row->tgl19,
					$row->tgl20,
					$row->tgl21
				);

				$where = " and LEFT(kdakun,3)='412' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_ehtml = array(
					$row->tgl15,
					$row->tgl16,
					$row->tgl17,
					$row->tgl18,
					$row->tgl19,
					$row->tgl20,
					$row->tgl21
				);

				$where = " and LEFT(kdakun,3)='413' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_eflash = array(
					$row->tgl15,
					$row->tgl16,
					$row->tgl17,
					$row->tgl18,
					$row->tgl19,
					$row->tgl20,
					$row->tgl21
				);

				$where = " and LEFT(kdakun,3)='414' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_video = array(
					$row->tgl15,
					$row->tgl16,
					$row->tgl17,
					$row->tgl18,
					$row->tgl19,
					$row->tgl20,
					$row->tgl21
				);

				break;
			case '4':
				$query = "SELECT 
					SUM(CASE WHEN DAY(tgljurnal) = 22 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl22,
					SUM(CASE WHEN DAY(tgljurnal) = 23 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl23,
					SUM(CASE WHEN DAY(tgljurnal) = 24 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl24,
					SUM(CASE WHEN DAY(tgljurnal) = 25 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl25,
					SUM(CASE WHEN DAY(tgljurnal) = 26 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl26,
					SUM(CASE WHEN DAY(tgljurnal) = 27 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl27,
					SUM(CASE WHEN DAY(tgljurnal) = 28 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl28 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and tgljurnal is not null and day(tgljurnal) between 22 and 28 and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";

				$labels = array('Tgl 22', 'Tgl 23', 'Tgl 24', 'Tgl 25', 'Tgl 26', 'Tgl 27', 'Tgl 28');

				$where = " and LEFT(kdakun,1)='411' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_lsm = array(
					$row->tgl22,
					$row->tgl23,
					$row->tgl24,
					$row->tgl25,
					$row->tgl26,
					$row->tgl27,
					$row->tgl28
				);

				$where = " and LEFT(kdakun,3)='412' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_ehtml = array(
					$row->tgl22,
					$row->tgl23,
					$row->tgl24,
					$row->tgl25,
					$row->tgl26,
					$row->tgl27,
					$row->tgl28
				);

				$where = " and LEFT(kdakun,3)='413' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_eflash = array(
					$row->tgl22,
					$row->tgl23,
					$row->tgl24,
					$row->tgl25,
					$row->tgl26,
					$row->tgl27,
					$row->tgl28
				);

				$where = " and LEFT(kdakun,3)='414' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_video = array(
					$row->tgl22,
					$row->tgl23,
					$row->tgl24,
					$row->tgl25,
					$row->tgl26,
					$row->tgl27,
					$row->tgl28
				);

				break;
			case '5':
				$query = "SELECT 
					SUM(CASE WHEN DAY(tgljurnal) = 29 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl29,
					SUM(CASE WHEN DAY(tgljurnal) = 30 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl30,
					SUM(CASE WHEN DAY(tgljurnal) = 31 THEN (kredit-debet) / 1000000 ELSE 0 END) AS tgl31 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and tgljurnal is not null  and day(tgljurnal) between 29 and 31 and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";

				$labels = array('Tgl 29', 'Tgl 30', 'Tgl 31');

				$where = " and LEFT(kdakun,1)='411' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_lsm = array(
					$row->tgl29,
					$row->tgl30,
					$row->tgl31
				);

				$where = " and LEFT(kdakun,3)='412' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_ehtml = array(
					$row->tgl29,
					$row->tgl30,
					$row->tgl31
				);

				$where = " and LEFT(kdakun,3)='413' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_eflash = array(
					$row->tgl29,
					$row->tgl30,
					$row->tgl31
				);

				$where = " and LEFT(kdakun,3)='414' ";
				$row = $this->db->query($query . $where)->getRow();
				$data_video = array(
					$row->tgl29,
					$row->tgl30,
					$row->tgl31
				);

				break;
		}


		// echo json_encode(array('data' => $data));
		echo json_encode(array('success' => true, 'data_lsm' => $data_lsm, 'labels' => $labels, 'data_ehtml' => $data_ehtml, 'data_eflash' => $data_eflash, 'data_video' => $data_video));
	}



	public function get_grafik_biaya()
	{
		$idperusahaan = $this->session->get('idperusahaan');
		$mingguan = $this->request->getPost('mingguan');
		$bulanan = $this->request->getPost('bulanan');
		$tahunan = $this->request->getPost('tahunan');

		if ($mingguan != '0' && $bulanan == '0') {
			echo json_encode(array('msg' => "Pilih Bulan"));
			exit();
		}

		// ------------------------------------------------ > Kondisi Tahunan
		if ($tahunan != '0' && $bulanan == '0' && $mingguan == '0') {
			$query = "
					SELECT 
					SUM(CASE WHEN MONTH(tgljurnal) = 1 THEN (debet-kredit) / 1000000 ELSE 0 END) AS bln1,
					SUM(CASE WHEN MONTH(tgljurnal) = 2 THEN (debet-kredit) / 1000000 ELSE 0 END) AS bln2,
					SUM(CASE WHEN MONTH(tgljurnal) = 3 THEN (debet-kredit) / 1000000 ELSE 0 END) AS bln3,
					SUM(CASE WHEN MONTH(tgljurnal) = 4 THEN (debet-kredit) / 1000000 ELSE 0 END) AS bln4,
					SUM(CASE WHEN MONTH(tgljurnal) = 5 THEN (debet-kredit) / 1000000 ELSE 0 END) AS bln5,
					SUM(CASE WHEN MONTH(tgljurnal) = 6 THEN (debet-kredit) / 1000000 ELSE 0 END) AS bln6,
					SUM(CASE WHEN MONTH(tgljurnal) = 7 THEN (debet-kredit) / 1000000 ELSE 0 END) AS bln7,
					SUM(CASE WHEN MONTH(tgljurnal) = 8 THEN (debet-kredit) / 1000000 ELSE 0 END) AS bln8,
					SUM(CASE WHEN MONTH(tgljurnal) = 9 THEN (debet-kredit) / 1000000 ELSE 0 END) AS bln9,
					SUM(CASE WHEN MONTH(tgljurnal) = 10 THEN (debet-kredit) / 1000000 ELSE 0 END) AS bln10,
					SUM(CASE WHEN MONTH(tgljurnal) = 11 THEN (debet-kredit) / 1000000 ELSE 0 END) AS bln11, 
					SUM(CASE WHEN MONTH(tgljurnal) = 12 THEN (debet-kredit) / 1000000 ELSE 0 END) AS bln12  
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and year(tgljurnal)= " . $tahunan . " ";

			$labels = array('Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des');

			$where = " and ( LEFT(kdakun,1)='5' or LEFT(kdakun,1)='6' or LEFT(kdakun,2)='72' )";
			$row = $this->db->query($query . $where)->getRow();
			$data_biaya = array(
				$row->bln1,
				$row->bln2,
				$row->bln3,
				$row->bln4,
				$row->bln5,
				$row->bln6,
				$row->bln7,
				$row->bln8,
				$row->bln9,
				$row->bln10,
				$row->bln11,
				$row->bln12,
			);
		}

		// ------------------------------------------------ > Kondisi Bulanan
		if ($bulanan != '0' && $mingguan == '0') {
			$query = "
					SELECT 
					SUM(CASE WHEN DAY(tgljurnal) IN (1,2,3) THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl3,
					SUM(CASE WHEN DAY(tgljurnal) IN (4,5,6) THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl6,
					SUM(CASE WHEN DAY(tgljurnal) IN (7,8,9) THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl9,
					SUM(CASE WHEN DAY(tgljurnal) IN (10,11,12) THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl12,
					SUM(CASE WHEN DAY(tgljurnal) IN (13,14,15) THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl15,
					SUM(CASE WHEN DAY(tgljurnal) IN (16,17,18) THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl18,
					SUM(CASE WHEN DAY(tgljurnal) IN (19,20,21) THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl21,
					SUM(CASE WHEN DAY(tgljurnal) IN (22,23,24) THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl24,
					SUM(CASE WHEN DAY(tgljurnal) IN (25,26,27) THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl27,
					SUM(CASE WHEN DAY(tgljurnal) IN (28,29,30) THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl30,
					SUM(CASE WHEN DAY(tgljurnal) IN (31) THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl31 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";

			$labels = array('Tgl 3', 'Tgl 6', 'Tgl 9', 'Tgl 12', 'Tgl 15', 'Tgl 18', 'Tgl 21', 'Tgl 24', 'Tgl 27', 'Tgl 30', 'Tgl 31');

			$where = " and ( LEFT(kdakun,1)='5' or LEFT(kdakun,1)='6' or LEFT(kdakun,2)='72' )";
			$row = $this->db->query($query . $where)->getRow();
			$data_biaya = array(
				$row->tgl3,
				$row->tgl6,
				$row->tgl9,
				$row->tgl12,
				$row->tgl15,
				$row->tgl18,
				$row->tgl21,
				$row->tgl24,
				$row->tgl27,
				$row->tgl30,
				$row->tgl31
			);
		}


		// ------------------------------------------------ > Kondisi Mingguan
		switch ($mingguan) {
			case '1':
				$query = "SELECT 
					SUM(CASE WHEN DAY(tgljurnal) = 1 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl1,
					SUM(CASE WHEN DAY(tgljurnal) = 2 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl2,
					SUM(CASE WHEN DAY(tgljurnal) = 3 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl3,
					SUM(CASE WHEN DAY(tgljurnal) = 4 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl4,
					SUM(CASE WHEN DAY(tgljurnal) = 5 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl5,
					SUM(CASE WHEN DAY(tgljurnal) = 6 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl6,
					SUM(CASE WHEN DAY(tgljurnal) = 7 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl7 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and tgljurnal is not null and day(tgljurnal) between 1 and 7 and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";

				$labels = array('Tgl 1', 'Tgl 2', 'Tgl 3', 'Tgl 4', 'Tgl 5', 'Tgl 6', 'Tgl 7');

				$where = " and ( LEFT(kdakun,1)='5' or LEFT(kdakun,1)='6' or LEFT(kdakun,2)='72' )";
				$row = $this->db->query($query . $where)->getRow();
				$data_biaya = array(
					$row->tgl1,
					$row->tgl2,
					$row->tgl3,
					$row->tgl4,
					$row->tgl5,
					$row->tgl6,
					$row->tgl7
				);

				break;
			case '2':
				$query = "SELECT 
					SUM(CASE WHEN DAY(tgljurnal) = 8 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl8,
					SUM(CASE WHEN DAY(tgljurnal) = 9 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl9,
					SUM(CASE WHEN DAY(tgljurnal) = 10 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl10,
					SUM(CASE WHEN DAY(tgljurnal) = 11 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl11,
					SUM(CASE WHEN DAY(tgljurnal) = 12 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl12,
					SUM(CASE WHEN DAY(tgljurnal) = 13 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl13,
					SUM(CASE WHEN DAY(tgljurnal) = 14 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl14 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and tgljurnal is not null and day(tgljurnal) between 8 and 14 and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";
				$labels = array('Tgl 8', 'Tgl 9', 'Tgl 10', 'Tgl 11', 'Tgl 12', 'Tgl 13', 'Tgl 14');

				$where = " and ( LEFT(kdakun,1)='5' or LEFT(kdakun,1)='6' or LEFT(kdakun,2)='72' )";
				$row = $this->db->query($query . $where)->getRow();
				$data_biaya = array(
					$row->tgl8,
					$row->tgl9,
					$row->tgl10,
					$row->tgl11,
					$row->tgl12,
					$row->tgl13,
					$row->tgl14
				);


				break;
			case '3':
				$query = "SELECT 
					SUM(CASE WHEN DAY(tgljurnal) = 15 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl15,
					SUM(CASE WHEN DAY(tgljurnal) = 16 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl16,
					SUM(CASE WHEN DAY(tgljurnal) = 17 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl17,
					SUM(CASE WHEN DAY(tgljurnal) = 18 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl18,
					SUM(CASE WHEN DAY(tgljurnal) = 19 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl19,
					SUM(CASE WHEN DAY(tgljurnal) = 20 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl20,
					SUM(CASE WHEN DAY(tgljurnal) = 21 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl21 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and tgljurnal is not null and day(tgljurnal) between 15 and 21 and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";

				$labels = array('Tgl 15', 'Tgl 16', 'Tgl 17', 'Tgl 18', 'Tgl 19', 'Tgl 20', 'Tgl 21');

				$where = " and ( LEFT(kdakun,1)='5' or LEFT(kdakun,1)='6' or LEFT(kdakun,2)='72' )";
				$row = $this->db->query($query . $where)->getRow();
				$data_biaya = array(
					$row->tgl15,
					$row->tgl16,
					$row->tgl17,
					$row->tgl18,
					$row->tgl19,
					$row->tgl20,
					$row->tgl21
				);


				break;
			case '4':
				$query = "SELECT 
					SUM(CASE WHEN DAY(tgljurnal) = 22 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl22,
					SUM(CASE WHEN DAY(tgljurnal) = 23 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl23,
					SUM(CASE WHEN DAY(tgljurnal) = 24 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl24,
					SUM(CASE WHEN DAY(tgljurnal) = 25 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl25,
					SUM(CASE WHEN DAY(tgljurnal) = 26 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl26,
					SUM(CASE WHEN DAY(tgljurnal) = 27 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl27,
					SUM(CASE WHEN DAY(tgljurnal) = 28 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl28 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and tgljurnal is not null and day(tgljurnal) between 22 and 28 and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";

				$labels = array('Tgl 22', 'Tgl 23', 'Tgl 24', 'Tgl 25', 'Tgl 26', 'Tgl 27', 'Tgl 28');

				$where = " and ( LEFT(kdakun,1)='5' or LEFT(kdakun,1)='6' or LEFT(kdakun,2)='72' )";
				$row = $this->db->query($query . $where)->getRow();
				$data_biaya = array(
					$row->tgl22,
					$row->tgl23,
					$row->tgl24,
					$row->tgl25,
					$row->tgl26,
					$row->tgl27,
					$row->tgl28
				);


				break;
			case '5':
				$query = "SELECT 
					SUM(CASE WHEN DAY(tgljurnal) = 29 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl29,
					SUM(CASE WHEN DAY(tgljurnal) = 30 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl30,
					SUM(CASE WHEN DAY(tgljurnal) = 31 THEN (debet-kredit) / 1000000 ELSE 0 END) AS tgl31 
					FROM v_jurnaldetail 
					WHERE idperusahaan='" . $idperusahaan . "' and tgljurnal is not null  and day(tgljurnal) between 29 and 31 and month(tgljurnal)= " . $bulanan . " and year(tgljurnal) = " . $tahunan . " ";

				$labels = array('Tgl 29', 'Tgl 30', 'Tgl 31');

				$where = " and ( LEFT(kdakun,1)='5' or LEFT(kdakun,1)='6' or LEFT(kdakun,2)='72' )";
				$row = $this->db->query($query . $where)->getRow();
				$data_biaya = array(
					$row->tgl29,
					$row->tgl30,
					$row->tgl31
				);

				break;
		}


		// echo json_encode(array('data' => $data));
		echo json_encode(array('success' => true, 'data_biaya' => $data_biaya, 'labels' => $labels));
	}
}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */