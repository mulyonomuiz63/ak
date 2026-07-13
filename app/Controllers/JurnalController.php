<?php



namespace App\Controllers;



class JurnalController extends BaseController

{



	public $menuaktif = 'Jurnal';


	public function notif()
	{
		$session = session();
		$session->set('status_approve', 'all');
		return redirect()->to('jurnal');
	}

	public function index()

	{
		cek_jurnal();

		$data['cari'] = "";

		if ($this->request->getPost('cari') != "") {

			$data['cari'] = $this->request->getPost('cari');
		}

		$data['jurnal'] = $this->jurnal_model->get_jurnal()->getResult();
		$data['jurnal_bulan'] = $this->jurnal_model->get_jurnal_bulan()->getRowObject();

		$data['menuaktif'] = $this->menuaktif;

		return view('jurnal/index', $data);
	}



	public function tambah($kondisi = null)

	{

		if (!(session()->get('databaseHitJurnal') <= session()->get('hitJurnal'))) {

			return redirect()->to('jurnal');
		}

		$data['idjurnal'] = "";

		$data['menuaktif'] = $this->menuaktif;

		$data['kondisi'] = $kondisi;

		return view('jurnal/inputdata', $data);
	}



	public function edit($idjurnal, $kondisi = null, $kdakun = null)
	{



		if ($this->jurnal_model->get_by_id($idjurnal)->getNumRows() < 1) {

			$pesan = '<div>

						<div class="alert alert-danger alert-dismissable">

			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>

			                <strong>Ilegal!</strong> Data tidak ditemukan 

					    </div>

					</div>';

			$this->session->setFlashdata('pesan', $pesan);

			return redirect()->to('jurnal');

			exit();
		};

		$data['jurnalfile'] = $this->jurnal_model->getJurnalFile($idjurnal)->getResult();

		$data['jurnal'] = $this->jurnal_model->getJurnal($idjurnal)->getRow();

		$data['idjurnal'] = $idjurnal;

		$data['menuaktif'] = $this->menuaktif;

		$data['kondisi'] = $kondisi;

		$data['kdakun'] = $kdakun;

		return view('jurnal/inputdata', $data);
	}



	public function datatablesource()
	{
		$RsData = $this->jurnal_model->get_datatables();
		$no = $this->request->getPost('start');

		// 1. SIAPKAN 2 ARRAY TERPISAH
		$dataBalance = array();
		$dataTidakBalance = array();

		// Panggil koneksi database sekali di luar loop untuk efisiensi
		$db = \Config\Database::connect();

		if ($RsData->getNumRows() > 0) {
			foreach ($RsData->getResult() as $rowdata) {

				// ========================================================================
				// 1. CEK STATUS BALANCE, OBJEK PAJAK, DAN FISKAL UNTUK BARIS INI
				// ========================================================================
				// Penambahan parameter false di akhir select() agar CodeIgniter tidak merusak fungsi CASE WHEN
				$cekDetail = $db->table('jurnaldetail')
					->select('SUM(debet) as t_debet, SUM(kredit) as t_kredit, SUM(CASE WHEN objek != "0" THEN 1 ELSE 0 END) as jml_objek, SUM(CASE WHEN fiskal != "0" THEN 1 ELSE 0 END) as jml_fiskal', false)
					->where('idjurnal', $rowdata->idjurnal)
					->get()
					->getRow();

				// Tentukan apakah balance atau tidak (anggap balance jika debet == kredit)
				$isBalance = ($cekDetail->t_debet == $cekDetail->t_kredit);
				// ========================================================================

				if (session()->get('level_super') != 3) {
					if ($rowdata->approve == 1) {
						$approve = '<a href="#" class="btn btn-sm btn-success btn-circle tooltips" data-toggle="tooltip" data-placement="left" title="Telah Disetujui"><i class="bi bi-check-circle-fill"></i></a>';
					} elseif ($rowdata->approve == 2) {
						$approve = '<a href="#" class="btn btn-sm btn-danger btn-circle tooltips" data-toggle="tooltip" data-placement="left" title="Jurnal Perlu Perbaikan"><i class="bi bi-x-circle-fill"></i></a>';
					} else {
						$approve = '<a href="#" class="btn btn-sm btn-info btn-circle tooltips" data-toggle="tooltip" data-placement="left" title="Sedang di proses PIC"><i class="bi bi-check-circle-fill"></i></a>';
					}

					$opsi = ' <a href="javascript:void(0)" data-cetak_pdf="' . site_url('jurnal/lihat/' . encrypt($rowdata->idjurnal)) . '" class="btn btn-sm btn-secondary btn-circle tooltips" data-toggle="modal" data-placement="left" data-target="#modalcetakpdf" id="cetak-pdf" title="Cetak jurnal ke pdf"><i class="fa fa-print"></i></a> <a href="' . site_url('jurnal/edit/' . encrypt($rowdata->idjurnal)) . '" class="btn btn-sm btn-warning btn-circle tooltips" data-toggle="tooltip" data-placement="left" title="Ubah data jurnal"><i class="fa fa-edit"></i></a>
                          <a href="' . site_url('jurnal/delete/' . encrypt($rowdata->idjurnal)) . '" class="btn btn-sm btn-danger btn-circle" id="hapus"><i class="fa fa-trash tooltips" data-toggle="tooltip" data-placement="left" title="Hapus data jurnal"></i></a>';
					$app = $approve;
				} else {
					if ($rowdata->approve == 0 || $rowdata->id_pic == null) {
						$approve = '<a href="' . site_url('jurnal/edit/' . encrypt($rowdata->idjurnal)) . '" class="btn btn-sm btn-info btn-circle tooltips" data-toggle="tooltip" data-placement="left" title="Menunggu Persetujuan"><i class="bi bi-check-circle-fill"></i></a>';
					} elseif ($rowdata->approve == 1 || $rowdata->id_pic == null) {
						$approve = '<a href="' . site_url('jurnal/edit/' . encrypt($rowdata->idjurnal)) . '" class="btn btn-sm btn-success btn-circle tooltips" data-toggle="tooltip" data-placement="left" title="Telah Disetujui"><i class="bi bi-check-circle-fill"></i></a>';
					} else {
						$approve = '<a href="' . site_url('jurnal/edit/' . encrypt($rowdata->idjurnal)) . '" class="btn btn-sm btn-danger btn-circle tooltips" data-toggle="tooltip" data-placement="left" title="Jurnal Perlu Perbaikan"><i class="bi bi-x-circle-fill"></i></a>';
					}
					$app = $approve;
					$opsi = $approve;
				}

				if (session()->get('level_super') != 3) {
					$option = '<div class="dropdown custom-dropdown dropleft mr-2">
                                <a class="badge btn-info" href="#" role="button" id="dropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bi bi-three-dots"></i>
                                </a>
                                <div class="dropdown-menu" >
                                    <div class="mx-2">
                                    ' . $opsi . '
                                    </div>
                                </div>
                            </div>';
				} else {
					$option = "";
				}

				$no++;
				$row = array();

				$row[] = '<input type="checkbox" class="check-item" name="idjurnal[]" value="' . encrypt($rowdata->idjurnal) . '" >';
				$row[] = date('d-m-Y', strtotime($rowdata->tgljurnal));

				// ========================================================================
				// 2. KUMPULKAN SEMUA BADGE (BALANCE, OBJEK, FISKAL)
				// ========================================================================
				$badgeHTML = '<br>'; // Mulai dari baris baru di bawah ID Jurnal

				if ($isBalance) {
					$badgeHTML .= '<span class="badge badge-success mt-1 mr-1" style="font-size:10px;"><i class="fa fa-check-circle"></i> BALANCE</span>';
					$row['DT_RowClass'] = 'row-balance';
				} else {
					$badgeHTML .= '<span class="badge badge-danger mt-1 mr-1" style="font-size:10px;"><i class="fa fa-exclamation-triangle"></i> TIDAK BALANCE</span>';
					$row['DT_RowClass'] = 'row-tidak-balance';
				}

				// Tambahkan Badge Objek Pajak jika nilai jml_objek > 0
				if ($cekDetail->jml_objek > 0) {
					$badgeHTML .= '<span class="badge badge-info mt-1 mr-1" style="font-size:10px;"><i class="fas fa-file-invoice-dollar"></i> PPh</span>';
				}

				// Tambahkan Badge Fiskal jika nilai jml_fiskal > 0
				if ($cekDetail->jml_fiskal > 0) {
					$badgeHTML .= '<span class="badge badge-warning mt-1" style="font-size:10px; color:#000;"><i class="fas fa-balance-scale"></i> FISKAL</span>';
				}
				// ========================================================================

				// Masukkan gabungan ID Jurnal, Tombol Copy, dan Badge ke dalam array datatable
				$copyBtn = ' <a href="javascript:void(0);" class="text-secondary ml-1 btn-copy-idjurnal" data-id="' . $rowdata->idjurnal . '" title="Copy ID Jurnal"><i class="fa fa-copy"></i></a>';
				$row[] = '<span class="d-flex align-items-center">' . $rowdata->idjurnal . $copyBtn . '</span>' . $badgeHTML;

				$row[] = $rowdata->referensi == "" ? "-" : $rowdata->referensi;

				$teks = ringkas_teks($rowdata->keterangan, 5);
				if ($teks['full'] == '') {
					$row[] = '<span>' . $teks['short'] . '</span>';
				} else {
					$row[] = '
                    <span class="text-short">' . $teks['short'] . '...</span>
                    <span class="text-full d-none">' . $teks['full'] . '</span>
                    <a href="javascript:void(0)" class="toggle-text text-primary ml-1">Lihat semua</a>
                ';
				}

				$row[] = $rowdata->namapengguna;
				$row[] = '<span class="font-weight-bold">' . number_format($rowdata->jumlah) . '</span>';

				$lamp = '-';
				if ($rowdata->totalfile != 0) {
					$lamp = '<a href="#" class="btn btn-sm btn-secondary btn-circle tooltips" id="lihatFile" data-id="' . $rowdata->idjurnal . '" data-toggle="tooltip" data-placement="left" title="Untuk melihat lampiran file"><i class="bi bi-file-earmark"></i></a> ';
				} else if ($rowdata->filelampiran != null || $rowdata->filelampiran != '') {
					$lamp = '<a href="#" class="btn btn-sm btn-secondary btn-circle tooltips" id="lihatFile" data-id="' . $rowdata->idjurnal . '" data-toggle="tooltip" data-placement="left" title="Untuk melihat lampiran file"><i class="bi bi-file-earmark"></i></a> ';
				}
				$row[] = $lamp;

				$row[] = '<div class="d-flex justify-content-center align-items-center">
                        ' . $option . '
                        ' . $app . '
                      </div>';

				$row['DT_RowData'] = array(
					'idjurnal' => $rowdata->idjurnal
				);

				// ========================================================================
				// 3. MASUKKAN KE ARRAY YANG SESUAI (PISAHKAN BALANCE & TIDAK BALANCE)
				// ========================================================================
				if (!$isBalance) {
					$dataTidakBalance[] = $row;
				} else {
					$dataBalance[] = $row;
				}
			}
		}

		// ========================================================================
		// 4. GABUNGKAN ARRAY: YANG TIDAK BALANCE SELALU DI ATAS
		// ========================================================================
		$dataFinal = array_merge($dataTidakBalance, $dataBalance);

		$output = array(
			"draw" => $this->request->getPost('draw'),
			"recordsTotal" => $this->jurnal_model->count_all(),
			"recordsFiltered" => $this->jurnal_model->count_filtered(),
			"data" => $dataFinal,
		);

		return $this->response->setJSON($output);
	}

	public function get_detail_jurnal()
	{
		$idjurnal = $this->request->getPost('idjurnal');
		$db = \Config\Database::connect();

		$detail = $db->table('jurnaldetail')
			->select('jurnaldetail.iddetailjurnal, akun.kdakun, akun.nmakun, jurnaldetail.debet, jurnaldetail.kredit, jurnaldetail.objek, jurnaldetail.objek_pajak, jurnaldetail.fiskal, jurnaldetail.koreksi_positif, jurnaldetail.koreksi_negatif, jurnaldetail.keterangan')
			->join('akun', 'akun.keyakun = jurnaldetail.keyakun', 'left')
			->where('jurnaldetail.idjurnal', $idjurnal)
			->get()
			->getResult();

		// Wrapper utama (diberi overflow: hidden agar tidak bocor scroll-nya ke luar)
		$html = '<div class="p-3 my-2 rounded" style="background-color: #d4e5f5; border: 1px solid #e2e8f0; border-left: 4px solid #20c997; overflow: hidden; max-width: 100%;">';

		$html .= '  <div class="d-flex align-items-center mb-3 pb-2" style="border-bottom: 1px dashed #cbd5e1;">';
		$html .= '      <i class="fas fa-sitemap text-secondary mr-2"></i>';
		$html .= '      <h6 class="m-0 font-weight-bold text-secondary" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Rincian Jurnal Akuntansi</h6>';
		$html .= '  </div>';

		// Wrapper tabel khusus untuk scroll horizontal
		$html .= '  <div class="bg-white rounded shadow-sm" style="border: 1px solid #e2e8f0; width: 100%; overflow-x: auto;">';
		$html .= '      <table class="table table-sm table-hover table-bordered mb-0" id="tabel-detail-jurnal" style="width: 100%; font-size: 12px; white-space: nowrap;">';
		$html .= '          <thead style="background-color: #f1f5f9; color: #475569;">
                            <tr>
                                <th class="py-2 px-2 text-center">Aksi</th>
                                <th class="py-2 px-2">Akun</th>
                                <th class="py-2 px-2 text-right">Debet</th>
                                <th class="py-2 px-2 text-right">Kredit</th>
                                <th class="py-2 px-2">Objek Pajak</th>
                                <th class="py-2 px-2 text-right">Nominal Objek</th>
                                <th class="py-2 px-2">Fiskal</th>
                                <th class="py-2 px-2 text-right">Kor. Positif</th>
                                <th class="py-2 px-2 text-right">Kor. Negatif</th>
                                <th class="py-2 px-2">Keterangan</th>
                            </tr>
                        </thead>';
		$html .= '          <tbody>';

		$tDebet = 0;
		$tKredit = 0;

		$mapObjek = ['0' => '-', '1' => 'PPh Psl 21', '2' => 'PPh Psl 23', '3' => 'PPh Psl 4(2)'];
		$mapFiskal = ['0' => 'Tidak', '1' => 'Ya'];

		if (count($detail) > 0) {
			foreach ($detail as $d) {
				$valObjek = $d->objek ?? '0';
				$valFiskal = $d->fiskal ?? '0';
				$txtObjek = $mapObjek[$valObjek] ?? '-';
				$txtFiskal = $mapFiskal[$valFiskal] ?? 'Tidak';

				$valObjekPajak = $d->objek_pajak ?? 0;
				$valKorPos = $d->koreksi_positif ?? 0;
				$valKorNeg = $d->koreksi_negatif ?? 0;
				$valKet = $d->keterangan ?? '';

				$html .= '          <tr id="row-' . $d->iddetailjurnal . '" 
                                    data-id="' . $d->iddetailjurnal . '" 
                                    data-objek="' . $valObjek . '" 
                                    data-objekpajak="' . $valObjekPajak . '" 
                                    data-fiskal="' . $valFiskal . '" 
                                    data-korpos="' . $valKorPos . '" 
                                    data-korneg="' . $valKorNeg . '" 
                                    data-ket="' . $valKet . '">';

				// Penyesuaian ukuran tombol Edit: ditambah style="padding: 2px 6px; font-size: 10px;"
				$html .= '              <td class="py-2 px-2 text-center align-middle">
                                        <button type="button" class="btn btn-warning btn-edit-inline" style="padding: 2px 6px; font-size: 10px;" title=""><i class="fas fa-edit"></i></button>
                                    </td>';

				$html .= '              <td class="py-2 px-2 align-middle"><b>' . $d->kdakun . '</b> - ' . $d->nmakun . '</td>';
				$html .= '              <td class="py-2 px-2 text-right text-success align-middle">' . number_format($d->debet, 0, ',', '.') . '</td>';
				$html .= '              <td class="py-2 px-2 text-right text-danger align-middle">' . number_format($d->kredit, 0, ',', '.') . '</td>';

				$html .= '              <td class="py-2 px-2 td-objek align-middle">' . $txtObjek . '</td>';
				$html .= '              <td class="py-2 px-2 text-right td-objekpajak align-middle">' . number_format($valObjekPajak, 0, ',', '.') . '</td>';
				$html .= '              <td class="py-2 px-2 td-fiskal align-middle">' . $txtFiskal . '</td>';
				$html .= '              <td class="py-2 px-2 text-right td-korpos align-middle">' . number_format($valKorPos, 0, ',', '.') . '</td>';
				$html .= '              <td class="py-2 px-2 text-right td-korneg align-middle">' . number_format($valKorNeg, 0, ',', '.') . '</td>';
				// Potong teks keterangan jika lebih dari 10 karakter
				$shortKet = (strlen($valKet) > 10) ? substr($valKet, 0, 10) . '...' : $valKet;
				$html .= '              <td class="py-2 px-2 td-ket align-middle btn-edit-inline" style="cursor:pointer;" title="' . htmlspecialchars($valKet) . '">' . htmlspecialchars($shortKet) . '</td>';
				$html .= '          </tr>';

				$tDebet += $d->debet;
				$tKredit += $d->kredit;
			}
		} else {
			$html .= '              <tr><td colspan="10" class="py-3 text-center text-muted font-italic">Tidak ada detail data jurnal</td></tr>';
		}

		$html .= '          </tbody>';
		$html .= '          <tfoot style="background-color: #f8fafc; border-top: 2px solid #e2e8f0;">';
		$html .= '              <tr>';
		$html .= '                  <td colspan="2" class="text-right font-weight-bold py-2 px-3 text-secondary">TOTAL :</td>';
		$html .= '                  <td class="text-right font-weight-bold py-2 px-3 text-dark">' . number_format($tDebet, 0, ',', '.') . '</td>';
		$html .= '                  <td class="text-right font-weight-bold py-2 px-3 text-dark">' . number_format($tKredit, 0, ',', '.') . '</td>';
		$html .= '                  <td colspan="6"></td>';
		$html .= '              </tr>';
		$html .= '          </tfoot>';
		$html .= '      </table>';
		$html .= '  </div>'; // Tutup wrapper scroll tabel

		$status_badge = ($tDebet == $tKredit)
			? '<span class="badge badge-success px-3 py-2"><i class="fas fa-check-circle mr-1"></i> BALANCE</span>'
			: '<span class="badge badge-danger px-3 py-2"><i class="fas fa-times-circle mr-1"></i> TIDAK BALANCE</span>';

		$html .= '  <div class="mt-3 text-right"><span class="font-weight-bold text-secondary mr-2">STATUS JURNAL:</span> ' . $status_badge . '</div>';
		$html .= '</div>';

		echo $html;
	}



	public function delete($id)
	{
		// 1. Cek apakah data jurnal ada di database
		$rsjurnal = $this->jurnal_model->get_by_id($id);

		if ($rsjurnal->getNumRows() < 1) {
			$pesan = '<div>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                        <strong>Ilegal!</strong> Data tidak ditemukan 
                    </div>
                </div>';
			$this->session->setFlashdata('pesan', $pesan);
			return redirect()->to('jurnal');
		}

		// 2. Ambil informasi file sebelum database dihapus
		$rowJurnal     = $rsjurnal->getRow();
		$filelampiran  = $rowJurnal->filelampiran;
		// Berjaga-jaga jika tabel utama 'jurnal' juga memiliki field 'kode_file'
		$kodeFileUtama = isset($rowJurnal->kode_file) ? $rowJurnal->kode_file : null;

		// Ambil semua data file arsip (tabel jurnalfile)
		$jurnalfile    = $this->jurnal_model->getJurnalFile($id)->getResult();

		// 3. Eksekusi Hapus Database
		$hapus = $this->jurnal_model->hapus($id);

		// 4. Jika database berhasil dihapus, bersihkan file-filenya
		if ($hapus) {

			// --- A. Hapus Arsip / Multi File (Tabel jurnalfile) ---
			foreach ($jurnalfile as $rows) {

				// Hapus dari Google Drive (Jika memiliki kode_file)
				if (!empty($rows->kode_file)) {
					$this->_deleteFromGoogleDrive($rows->kode_file);
				}

				// Hapus dari Server Lokal (Untuk file-file lama yang belum migrasi)
				if (!empty($rows->file)) {
					$pathThumb = FCPATH . 'uploads/jurnal/thumbnails/' . $rows->file;
					if (file_exists($pathThumb)) {
						unlink($pathThumb);
					}
				}
			}

			// --- B. Hapus Lampiran Utama (Tabel jurnal) ---
			// Hapus dari Google Drive
			if (!empty($kodeFileUtama)) {
				$this->_deleteFromGoogleDrive($kodeFileUtama);
			}

			// Hapus dari Server Lokal
			if (!empty($filelampiran)) {
				$pathUtama = FCPATH . 'uploads/jurnal/' . $filelampiran;
				if (file_exists($pathUtama)) {
					unlink($pathUtama);
				}
			}

			$pesan = '<div>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                        <strong>Berhasil.</strong> Data beserta lampirannya telah dihapus
                    </div>
                </div>';
		} else {
			// Jika gagal hapus database (misal karena relasi tabel), file aman tidak ikut terhapus
			$eror = $this->db->error();
			$pesan = '<div>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                        <strong>Maaf,</strong> Data gagal dihapus karena sudah digunakan di transaksi lain. <br>
                    </div>
                </div>';
		}

		$this->session->setFlashdata('pesan', $pesan);
		return redirect()->to('jurnal');
	}



	public function deleteAll()

	{

		$dataJurnal = $this->request->getPost('idjurnal');

		$dataBerhasil = 0;

		$dataGagal = 0;

		foreach ($dataJurnal as $id) {

			$rsjurnal = $this->jurnal_model->get_by_id($id);

			$filelampiran = $rsjurnal->getRow()->filelampiran;



			$jurnalfile = $this->jurnal_model->getJurnalFile($id)->getResult();


			$hapus = $this->jurnal_model->hapus($id);

			if ($hapus) {

				// --- A. Hapus Arsip / Multi File (Tabel jurnalfile) ---
				foreach ($jurnalfile as $rows) {

					// Hapus dari Google Drive (Jika memiliki kode_file)
					if (!empty($rows->kode_file)) {
						$this->_deleteFromGoogleDrive($rows->kode_file);
					}

					// Hapus dari Server Lokal (Untuk file-file lama yang belum migrasi)
					if (!empty($rows->file)) {
						$pathThumb = FCPATH . 'uploads/jurnal/thumbnails/' . $rows->file;
						if (file_exists($pathThumb)) {
							unlink($pathThumb);
						}
					}
				}

				// --- B. Hapus Lampiran Utama (Tabel jurnal) ---
				// Hapus dari Google Drive
				if (!empty($kodeFileUtama)) {
					$this->_deleteFromGoogleDrive($kodeFileUtama);
				}

				// Hapus dari Server Lokal
				if (!empty($filelampiran)) {
					$pathUtama = FCPATH . 'uploads/jurnal/' . $filelampiran;
					if (file_exists($pathUtama)) {
						unlink($pathUtama);
					}
				}

				$dataBerhasil +=  1;
			} else {
				// Jika gagal hapus database (misal karena relasi tabel), file aman tidak ikut terhapus
				$dataGagal += 1;
			}
		}

		$pesan = '<div>

						<div class="alert alert-success alert-dismissable">

			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>

			                <strong>Berhasil.</strong> Data telah berhasil dihapus ' . $dataBerhasil . ' dan data gagal dihapus ' . $dataGagal . '

					    </div>

					</div>';

		$this->session->setFlashdata('pesan', $pesan);

		return redirect()->to('jurnal');
	}



	public function store()
	{
		$request     = $this->request;
		$totaldebet  = $request->getPost('totaldebet');
		$totalkredit = $request->getPost('totalkredit');
		$kondisi     = $request->getPost('kondisi');

		// 1. Guard Clause: Validasi Total Debet & Kredit
		if ($totaldebet !== $totalkredit) {
			$pesanError = $this->_alertMessage('Maaf, Data gagal disimpan, Total Debet dan Total Kredit tidak sama.', 'danger');
			$this->session->setFlashdata('pesan', $pesanError);
			return redirect()->to($kondisi == null ? 'jurnal' : 'laporan/lapbukubesar');
		}

		// 2. Inisialisasi Variabel
		$idjurnal   = $request->getPost('idjurnal');
		$isUpdate   = ($idjurnal != '');
		$tgljurnal  = date('Y-m-d', strtotime($request->getPost('tgljurnal')));
		$jumlah     = str_replace(',', '', $totaldebet);

		if (!$isUpdate) {
			$tgljurnalId = date('ymd', strtotime($tgljurnal));
			$idjurnal    = "JU{$tgljurnalId}" . rand(00000, 99999);
		}

		$idperusahaan = session()->get('idperusahaan');
		$tahunBulan = date('Ym', strtotime($tgljurnal));
		$fileif           = '0';

		if (session()->get('hitLanggana')) {

			// ====================================================
			// 3A. PROSES FILE BARU (Langsung ke GDrive, 0% di Server)
			// ====================================================
			$files = $request->getFileMultiple('file2');
			if ($files) {
				foreach ($files as $file) {
					if ($file->isValid()) {
						$newName  = $file->getRandomName();
						// getTempName() adalah folder temporari bawaan PHP (/tmp)
						$tempPath = $file->getTempName();
						$isImage  = ($file->guessExtension() !== 'pdf');
						$pathToUpload = $tempPath;
						$resizedPath  = '';

						// Jika gambar, kompres dan simpan ke WRITEPATH sementara waktu
						if ($isImage) {
							$resizedPath = WRITEPATH . 'uploads/' . $newName;
							$this->image->withFile($tempPath)
								->resize(1012, 1012, true, 'auto')
								->save($resizedPath, 80);
							$pathToUpload = $resizedPath;
						}

						// Lempar ke Google Drive API
						$gdriveFileId = $this->_uploadToGoogleDrive($pathToUpload, $newName, $idperusahaan, $tahunBulan);

						// PENTING: Hapus file kompresi dari server lokal seketika itu juga!
						if ($isImage && file_exists($resizedPath)) {
							unlink($resizedPath);
						}

						// Simpan ID Drive-nya ke DB
						$this->jurnal_model->simpanfile([
							'idjurnal'  => $idjurnal,
							'file'      => $newName,
							'nama_file' => $file->getClientName(),
							'kode_file' => $gdriveFileId
						]);
						$fileif = '1';
					}
				}
			}

			// ====================================================
			// 3B. PROSES ARSIP & MIGRASI
			// ====================================================
			// ====================================================
			// 3B. PROSES ARSIP (file3) & COPY GOOGLE DRIVE
			// ====================================================
			$files3      = $request->getPost('file3');
			$namaFile3   = $request->getPost('nama_file');
			$kodeFile3   = $request->getPost('kode_file');

			if ($files3) {
				foreach ($files3 as $key => $fileValue) {
					if ($fileValue != '') {
						$gdriveId = $kodeFile3[$key] ?? '';

						if (empty($gdriveId)) {
							// KONDISI 1: Kode File Kosong (Masih file fisik di server)
							// Migrasikan dengan cara upload ke GDrive
							$arsipPath = FCPATH . 'uploads/arsip/thumbnails/' . $fileValue;
							if (file_exists($arsipPath)) {
								$gdriveId = $this->_uploadToGoogleDrive($arsipPath, $fileValue, $idperusahaan, $tahunBulan);
							}
						} else {
							// KONDISI 2: Kode File Terisi (Sudah ada di GDrive Arsip)
							// Duplikasi (Copy) file tersebut ke folder Perusahaan Jurnal
							$gdriveId = $this->_copyFileInGoogleDrive($gdriveId, $idperusahaan, $tahunBulan);
						}

						$this->jurnal_model->simpanfile([
							'idjurnal'  => $idjurnal,
							'file'      => $fileValue,
							'nama_file' => $namaFile3[$key] ?? 'Arsip',
							'kode_file' => $gdriveId // Gdrive ID yang baru hasil duplikasi/upload
						]);
						$fileif = '1';
					}
				}
			}
		}

		// ====================================================
		// 4. PROSES FILE LAMA SAAT UPDATE (Migrasi Otomatis)
		// ====================================================
		$file2_lama     = $request->getPost('file2_lama');
		$kode_file_lama = $request->getPost('kode_file_lama');

		if ($isUpdate && !empty($file2_lama)) {
			$gdriveIdLama = $kode_file_lama;

			// Migrasi ke Drive jika sebelumnya masih ada di lokal
			if (empty($gdriveIdLama)) {
				$pathLama = FCPATH . 'uploads/jurnal/' . $file2_lama;
				if (file_exists($pathLama)) {
					$gdriveIdLama = $this->_uploadToGoogleDrive($pathLama, $file2_lama, $idperusahaan, $tahunBulan);
				}
			}

			// Jika user menambahkan file baru, pindahkan file lama ini ke tabel jurnalfile agar seragam
			if ($fileif == '1') {
				$this->jurnal_model->simpanfile([
					'idjurnal'  => $idjurnal,
					'file'      => $file2_lama,
					'nama_file' => 'Lampiran Utama',
					'kode_file' => $gdriveIdLama
				]);
			}
		}

		// ====================================================
		// 5. SUSUN ARRAY DETAIL AKUN (DEBET & KREDIT)
		// ====================================================
		$arrUpdate = []; // Menampung detail lama yang di-edit
		$arrInsert = []; // Menampung detail baru (baik saat Create Jurnal maupun Tambah Detail saat Edit)

		// Ambil data post berupa array
		$iddetailjurnal   = $request->getPost('iddetailjurnal'); // Pastikan di view form edit, input ini berupa array name="iddetailjurnal[]"
		$keyakun   = $request->getPost('keyakun');
		$debet     = $request->getPost('debet');
		$kredit    = $request->getPost('kredit');
		$urut      = 1;

		if ($keyakun) {
			foreach ($keyakun as $key => $value) {
				if (!$value) continue;

				// Cek apakah baris ini memiliki ID Detail (artinya data lama yang diedit)
				$idDetailSpesifik = isset($iddetailjurnal[$key]) && $iddetailjurnal[$key] != '' ? $iddetailjurnal[$key] : null;

				$rowData = [
					'idjurnal'  => $idjurnal,
					'keyakun'   => $value,
					'deskripsi' => "",
					'debet'     => str_replace(',', '', $debet[$key]),
					'kredit'    => str_replace(',', '', $kredit[$key]),
					'nourut'    => $urut++,
				];

				// Jika ada ID detail, masukkan ke array Update
				if (!empty($idDetailSpesifik)) {
					$rowData['iddetailjurnal'] = $idDetailSpesifik;
					$arrUpdate[] = $rowData;
				}
				// Jika TIDAK ADA ID detail, masukkan ke array Insert (Baris Baru)
				else {
					$arrInsert[] = $rowData;
				}
			}
		}

		// ====================================================
		// 6. SIMPAN DATA UTAMA (CREATE ATAU UPDATE)
		// ====================================================
		$dataUtama = [
			'tgljurnal'  => $tgljurnal,
			'tag'        => '',
			'keterangan' => $request->getPost('keterangan'),
			'jumlah'     => $jumlah,
			'tglupdate'  => date('Y-m-d H:i:s'),
			'idpengguna' => $request->getPost('idpengguna'),
			'referensi'  => $request->getPost('referensi'),
		];

		if (!$isUpdate) { // CREATE
			$dataUtama['idjurnal']     = $idjurnal;
			$dataUtama['tglinsert']    = $dataUtama['tglupdate'];
			$dataUtama['filelampiran'] = null;

			// Saat CREATE, semua detail pasti masuk ke $arrInsert
			$simpan = $this->jurnal_model->simpan($dataUtama, $arrInsert, $idjurnal);
		} else { // UPDATE
			$dataUtama['filelampiran'] = ($fileif == '1') ? null : $file2_lama;
			$dataUtama['approve']      = '0';

			// Saat UPDATE, kita kirimkan KEDUA array (Update dan Insert) ke Model
			$simpan = $this->jurnal_model->updateWhere($dataUtama, $arrUpdate, $arrInsert, $idjurnal);
		}

		// ====================================================
		// 7. PENANGANAN RESPON KE USER
		// ====================================================
		if ($simpan) {
			$msg = $this->_alertMessage('<strong>Berhasil.</strong> Data telah disimpan', 'success');
		} else {
			$eror = $this->db->error();
			$msg  = $this->_alertMessage("<strong>Maaf,</strong> Data gagal disimpan <br> Pesan Error : {$eror['code']} {$eror['message']}", 'danger');
		}

		$this->session->setFlashdata('pesan', $msg);
		return redirect()->to($kondisi == null ? 'jurnal' : 'laporan/lapbukubesar');
	}



	public function getEdit()
	{
		$idjurnal = $this->request->getPost('idjurnal');
		$RsData = $this->jurnal_model->get_by_id($idjurnal)->getRow();



		$RsDataDetail = $this->jurnal_model->get_detail_by_id($idjurnal)->getResultArray();



		$data = array(

			'idjurnal' =>  $RsData->idjurnal,

			'idpengguna' =>  session()->get('idpengguna') == '8888888888' ? $RsData->idpengguna : session()->get('idpengguna'),

			'tgljurnal' =>  date('Y-m-d', strtotime($RsData->tgljurnal)),

			'tag' =>  $RsData->tag,

			'keterangan' =>  $RsData->keterangan,

			'filelampiran' =>  $RsData->filelampiran,

			'referensi' 	=> $RsData->referensi,
			'namapengguna' 	=> $RsData->namapengguna,
			'approve' 	    => $RsData->approve,
			'keterangan_approve' 	=> $RsData->keterangan_approve,

			'RsDataDetail' => $RsDataDetail,

		);



		return $this->response->setJSON($data);
	}





	public function upload_foto($file, $nama)

	{



		if (!empty($file[$nama]['name'])) {

			$saved_file = $this->request->getFile($nama);

			$newName = $saved_file->getRandomName();

			$upload_path = FCPATH . 'uploads/jurnal';

			$moved = $saved_file->move($upload_path, $newName);

			if ($moved) {

				$gambar = $newName;
			} else {

				$gambar = "";
			}
		} else {

			$gambar = "";
		}



		return $gambar;
	}



	public function update_upload_foto($file, $nama, $file_lama, $file_lama_hapus)

	{

		if (!empty($file[$nama]['name'])) {

			if ($file_lama != '' && $file_lama != null) {

				//hapus file lama

				if (file_exists('./uploads/jurnal/' . $file_lama)) {

					unlink('./uploads/jurnal/' . $file_lama);
				};
			}

			$saved_file = $this->request->getFile($nama);

			$newName = $saved_file->getRandomName();

			$upload_path = FCPATH . 'uploads/jurnal';

			$moved = $saved_file->move($upload_path, $newName);

			if ($moved) {

				$gambar = $newName;
			} else {

				$gambar = $file_lama;
			}
		} else {

			if ($file_lama_hapus != '' && $file_lama_hapus != null) {

				$gambar = $file_lama;
			} else {

				if ($file_lama != '' && $file_lama != null) {

					//hapus file lama

					if (file_exists('./uploads/jurnal/' . $file_lama)) {

						unlink('./uploads/jurnal/' . $file_lama);
					};
				}

				$gambar = "";
			}
		}



		return $gambar;
	}





	public function lihat($idjurnal)
	{
		// Tambahkan batas waktu eksekusi
		ini_set('max_execution_time', 300);

		$rsDataJurnal = $this->jurnal_model->get_by_id($idjurnal)->getRow();

		$idperusahaan = encrypt($rsDataJurnal->idperusahaan);
		$idpengguna = encrypt($rsDataJurnal->idpengguna);

		$namaperusahaan = $this->perusahaan_model->get_by_id($idperusahaan)->getRow()->namaperusahaan;
		$alamat = $this->perusahaan_model->get_by_id($idperusahaan)->getRow()->alamat;
		$hp = $this->perusahaan_model->get_by_id($idperusahaan)->getRow()->notelp;
		$email = $this->perusahaan_model->get_by_id($idperusahaan)->getRow()->email_pengguna;

		$pics = $this->pengguna_model->get_by_id_id_pic($rsDataJurnal->id_pic)->getRow();
		$pic_ttds = $this->pengguna_model->get_by_id_id_pic($rsDataJurnal->id_pic)->getRow();

		$pic = ($pics != null) ? $pics->namapengguna : '';
		$pic_ttd = ($pics != null) ? $pic_ttds->pic_ttd : '';

		$namapengguna = $this->pengguna_model->get_by_id($idpengguna)->getRow()->namapengguna;
		$file_ttd = $this->pengguna_model->get_by_id($idpengguna)->getRow()->file_ttd;

		// Path fisik TTD di server
		$path_ttd_pembuat = FCPATH . 'uploads/ttd/' . $file_ttd;
		$path_ttd_pemeriksa = FCPATH . 'uploads/ttd/' . $pic_ttd;

		$file_pembuat = (file_exists($path_ttd_pembuat) && $file_ttd != '') ? '<img src="' . $path_ttd_pembuat . '" height="80" />' : '';
		$file_pemeriksa = (file_exists($path_ttd_pemeriksa) && $pic_ttd != '') ? '<img src="' . $path_ttd_pemeriksa . '" height="80" />' : '';

		$rsData = $this->jurnal_model->get_jurnal_cetak($idjurnal);
		$builder = $this->db->table('jurnal');

		$dataJurnal = $builder->getWhere(array('md5(idjurnal)' => decrypt($idjurnal)))->getRow();
		$lampiran = $dataJurnal->filelampiran;
		$kode_file_lampiran = isset($dataJurnal->kode_file) ? $dataJurnal->kode_file : '';

		$pdf = new \TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->setPrintHeader(false);
		$pdf->SetMargins(20, 20, 10);
		$pdf->AddPage();
		$pdf->SetCreator("akuntanmu.com");
		$pdf->SetAuthor(trim($namaperusahaan));
		$pdf->SetTitle('Nomor Jurnal ' . $rsDataJurnal->idjurnal);

		$hp_tamp = ($hp != '' && $hp != '-' && $hp != null) ? "No. Telp :  $hp, " : 'No. Telp : - , ';
		$email_tamp = ($email != '' && $email != '-' && $email != null) ? "E-mail :  $email " : 'E-mail : - ';

		$title = '
            <table width="100%">
                <tr>
                    <th width="100%" align="center" style="text-align:center; font-size:16px; color:#2f3031; font-weight:bold; padding-top:10px;">' . trim($namaperusahaan) . '</th>
                </tr>';

		if ($alamat != '' && $alamat != '-' && $alamat != null) {
			$title .= '
                <tr>
                    <th width="100%" height="15px" align="center" valign="center" style="font-size:10px; color:#2f3031;">' . $alamat . '</th>
                </tr>
                <tr>
                    <th width="100%" height="15px" align="center" valign="center" style="font-size:10px; color:#2f3031;">' . $hp_tamp . ' ' . $email_tamp . '</th>
                </tr>';
		}

		$title .= '
                <tr>
                    <th width="100%" align="center" style="text-align:center; font-size:12px; color:#2f3031; font-weight:bold; ">JURNAL UMUM</th>
                </tr>
            </table>
            <hr>
        ';

		$pdf->SetFont('times', '', 16);
		$pdf->writeHTML($title, true, false, false, false, '');
		$pdf->SetTopMargin(15);

		// =========================================================================
		// DATA JURNAL UMUM
		// =========================================================================
		$table = '<table><tr><th><table cellpadding="5"><tr>';
		$table .= '<th width="40%" height="20px" style="font-size:12px">No. Jurnal</th>';
		$table .= '<th width="60%" style="font-size:12px">: ' . $rsDataJurnal->idjurnal  . '</th></tr><tr>';
		$table .= '<th height="20px" style="font-size:12px">Referensi</th>';
		$table .= '<th style="font-size:12px">: ' . ($rsData->getRow()->referensi == '' ? '-' : $rsData->getRow()->referensi) . '</th></tr><tr>';
		$table .= '<th height="20px" style="font-size:12px">Tanggal</th>';
		$table .= '<th style="font-size:12px">: ' . date('d-m-Y', strtotime($rsData->getRow()->tgljurnal)) . '</th></tr></table></th>';

		$table .= '<th><table cellpadding="5"><tr><th height="20px" style="font-size:12px">Keterangan Transaksi:</th></tr><tr>';
		$keterangan_aman = htmlspecialchars($rsData->getRow()->keterangan, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$table .= '<td style="font-size:12px;border:1px solid gray;height:60px;" >' . $keterangan_aman . '</td>';
		$table .= '</tr></table></th></tr></table>';

		$table .= '<br><br><table border="0.5" style="border:1px solid gray;" cellpadding="4"> 
                    <thead>
                        <tr>
                            <th width="50%" style="font-size:12px; font-weight:bold; text-align:center;border:1px solid gray;">Akun</th>
                            <th width="14%" style="font-size:12px; font-weight:bold; text-align:center;border:1px solid gray;">No. Akun</th>
                            <th width="18%" style="font-size:12px; font-weight:bold; text-align:center;border:1px solid gray;">Debit</th>
                            <th width="18%" style="font-size:12px; font-weight:bold; text-align:center;border:1px solid gray;">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>';

		$writer = new \Endroid\QrCode\Writer\PngWriter();
		$qrCode = \Endroid\QrCode\QrCode::create(site_url('validasi-jurnal/' . $idjurnal));
		$logo = \Endroid\QrCode\Logo\Logo::create(FCPATH . 'uploads/icon/iconqr.png')->setResizeToWidth(100);
		$result = $writer->write($qrCode, $logo, null);
		$qrCodes = $result->getDataUri();

		$total1 = 0;
		$total2 = 0;
		foreach ($rsData->getResult() as $data) {
			$total1 += $data->debet;
			$total2 += $data->kredit;
			$table .= '
                        <tr>
                            <td width="50%" style="font-size:12px; text-align:left;border:1px solid gray;">' . ($data->debet == 0 ? str_repeat("&nbsp;", 5) : "") . $data->nmakun . '</td>
                            <td width="14%" style="font-size:12px; text-align:center;border:1px solid gray;">' . $data->kdakun . '</td>
                            <td width="18%" style="font-size:12px; text-align:right;border:1px solid gray;">' . ($data->debet == 0 ? "" : number_format($data->debet)) . '</td>
                            <td width="18%" style="font-size:12px; text-align:right;border:1px solid gray;">' . ($data->kredit == 0 ? "" : number_format($data->kredit)) . '</td>          
                        </tr>';
		}

		$table .= '
                        <tr>
                            <td width="64%" style="font-size:12px; text-align:right;border:1px solid gray;" colspan="2"><B>TOTAL       </B></td>
                            <td width="18%" style="font-size:12px; text-align:right;border:1px solid gray;"><B>' . number_format($total1, 0, "", '.') . '</B></td>
                            <td width="18%" style="font-size:12px; text-align:right;border:1px solid gray;"><B>' . number_format($total2, 0, "", '.') . '</B></td>
                        </tr> </tbody></table>';

		$table .= '<br><br><table cellpadding="5"><tr><td width="50%"></td>';
		$table .= '<td width="25%" align="center" style="font-size:12px;border:1px solid gray;text-align:center">Dibuat Oleh:</td>';
		$table .= '<td width="25%" align="center" style="font-size:12px;border:1px solid gray;text-align:center">Disetujui Oleh:</td></tr>';

		$table .= '<tr><td width="50%" style="text-align:center;"><br><br><img src="' . $qrCodes . '" width="70" height="70" /><br></td>';
		$table .= '<td width="25%" style="border:1px solid gray; text-align:center; vertical-align:middle; height:80px;"><br><br>' . $file_pembuat . '<br></td>';
		$table .= '<td width="25%" style="border:1px solid gray; text-align:center; vertical-align:middle; height:80px;"><br><br>' . ($rsData->getRow()->approve == 1 ? $file_pemeriksa : '') . '<br></td></tr>';

		$table .= '<tr><td width="50%"></td>';
		$table .= '<td width="25%" align="center" style="border:1px solid gray;text-align:center">' . $namapengguna . '</td>';
		$table .= '<td width="25%" align="center" style="border:1px solid gray;text-align:center">' . $pic . '</td></tr></table>';

		$pdf->SetTopMargin(35);
		$pdf->SetFont('times', '', 10);
		$pdf->writeHTML($table, true, false, false, false, '');


		// =========================================================================
		// LAMPIRAN
		// =========================================================================
		if ($lampiran == '' || $lampiran == null) {
			$jurnalfile = $this->jurnal_model->getJurnalFile($idjurnal)->getResult();

			if (!empty($jurnalfile)) {
				$headerLampiran = '<table><tr><td><img src="' . FCPATH . 'uploads/jurnal/potong.png" ></td></tr></table>';
				$headerLampiran .= '<table><tr><td style="height:20px;border:1px solid gray;text-align:center;font-weight:bold;">Lampiran Dokumen / Bukti Transfer</td></tr></table><br>';
				$pdf->writeHTML($headerLampiran, true, false, false, false, '');

				$chunks = array_chunk($jurnalfile, 4);

				foreach ($chunks as $index => $chunk) {

					if ($index > 0) {
						$pdf->AddPage();
					}

					// TABEL PEMBUNGKUS
					$tabelLampiran = '<table cellpadding="0">';
					$tabelLampiran .= '<tr nobr="true"><td style="border:1px solid gray;">';

					// TABEL ISI
					$tabelLampiran .= '<table border="0" cellpadding="10">';

					$rows = array_chunk($chunk, 2);

					foreach ($rows as $row) {
						$tabelLampiran .= '<tr>';

						foreach ($row as $item) {
							$kode_gdrive = isset($item->kode_file) ? $item->kode_file : '';
							// PENGECEKAN PDF DITAMBAHKAN DI SINI
							$nama_file = isset($item->file) ? strtolower($item->file) : '';
							$is_pdf = (pathinfo($nama_file, PATHINFO_EXTENSION) === 'pdf');

							// JIKA KODE GDRIVE ADA DAN BUKAN PDF, MAKA TAMPILKAN
							if ($kode_gdrive != '' && !$is_pdf) {
								$gdrive_url = 'https://drive.google.com/uc?export=download&id=' . $kode_gdrive;
								$img_data = @file_get_contents($gdrive_url);
								if ($img_data !== false) {
									$base64 = base64_encode($img_data);
									$tabelLampiran .= '<td width="50%" style="text-align:center;"><br><img src="@' . $base64 . '" width="260"><br></td>';
								} else {
									$tabelLampiran .= '<td width="50%" style="text-align:center;color:red;"><br><span style="font-size:10px;">[Gagal Memuat]</span><br></td>';
								}
							} else {
								// JIKA KOSONG ATAU MERUPAKAN PDF, KOSONGKAN KOLOMNYA AGAR TIDAK ERROR
								$tabelLampiran .= '<td width="50%"></td>';
							}
						}

						// Isi sel kosong jika jumlah gambar ganjil
						if (count($row) == 1) {
							$tabelLampiran .= '<td width="50%"></td>';
						}
						$tabelLampiran .= '</tr>';
					}

					$tabelLampiran .= '</table>'; // Tutup tabel dalam
					$tabelLampiran .= '</td></tr></table><br>'; // Tutup tabel luar

					$pdf->writeHTML($tabelLampiran, true, false, false, false, '');
				}
			}
		} else {
			// Skema file tunggal
			$headerLampiran = '<table><tr><td><img src="' . FCPATH . 'uploads/jurnal/potong.png" ></td></tr></table>';
			$headerLampiran .= '<table><tr><td style="height:20px;border:1px solid gray;text-align:center;font-weight:bold;">Lampiran Dokumen / Bukti Transfer</td></tr></table><br>';
			$pdf->writeHTML($headerLampiran, true, false, false, false, '');

			$tabelLampiran = '<table cellpadding="0"><tr nobr="true"><td style="border:1px solid gray;">';
			$tabelLampiran .= '<table border="0" cellpadding="10"><tr><td style="text-align:center;">';

			// PENGECEKAN PDF DITAMBAHKAN DI SINI UNTUK FILE TUNGGAL
			$is_pdf_single = (pathinfo(strtolower($lampiran), PATHINFO_EXTENSION) === 'pdf');

			// HANYA RENDER JIKA BUKAN PDF
			if ($kode_file_lampiran != '' && !$is_pdf_single) {
				$gdrive_url_single = 'https://drive.google.com/uc?export=download&id=' . $kode_file_lampiran;
				$img_data_single = @file_get_contents($gdrive_url_single);
				if ($img_data_single !== false) {
					$base64_single = base64_encode($img_data_single);
					$tabelLampiran .= '<br><img src="@' . $base64_single . '" width="400"><br>';
				} else {
					$tabelLampiran .= '<br><span style="font-size:10px;">[Gagal Memuat]</span><br>';
				}
			}
			$tabelLampiran .= '</td></tr></table></td></tr></table>';

			$pdf->writeHTML($tabelLampiran, true, false, false, false, '');
		}

		if (ob_get_length()) {
			ob_clean();
		}

		$pdf->Output('Laporan Jurnal.pdf', 'I');
		exit;
	}


	function viewfile($id, $fil)

	{

		$data['menuaktif'] = $this->menuaktif;

		$data['file'] = $id;

		$data['fil'] = $fil;

		return view("jurnal/viewfile", $data);
	}



	public function deleteFile($idjurnal, $nama_file = null)
	{
		$db = \Config\Database::connect();

		// 1. Ambil data jurnal
		$jurnal = $db->table('jurnal')->where('idjurnal', $idjurnal)->get()->getRow();

		if ($jurnal) {
			// Asumsi: Jika tabel jurnal Anda sudah memiliki kolom kode_file, hapus GDrive-nya
			// Jika tidak ada kolom kode_file di tabel utama, kita cari dari tabel jurnalfile menggunakan nama file
			$fileGdrive = $db->table('jurnalfile')->where('idjurnal', $idjurnal)->where('file', $nama_file)->get()->getRow();

			if ($fileGdrive && !empty($fileGdrive->kode_file)) {
				$this->_deleteFromGoogleDrive($fileGdrive->kode_file);
				$db->table('jurnalfile')->where('id', $fileGdrive->id)->delete();
			} else {
				// Hapus file fisik lokal jika masih berupa file lama
				$pathFile = FCPATH . 'uploads/jurnal/' . $nama_file;
				if (file_exists($pathFile)) unlink($pathFile);
			}

			// 2. Kosongkan field filelampiran di tabel jurnal utama
			$db->table('jurnal')->where('idjurnal', $idjurnal)->update(['filelampiran' => null]);
		}

		$pesan = $this->_alertMessage('<strong>Berhasil!</strong> Lampiran utama berhasil dihapus.', 'success');
		$this->session->setFlashdata('pesan', $pesan);

		// Kembalikan user ke halaman edit
		return redirect()->to('jurnal/edit/' . $idjurnal);
	}



	public function deleteFileNew($id_jurnalfile, $idjurnal, $nama_file = null)
	{
		// 1. Ambil data spesifik dari tabel jurnalfile untuk mendapatkan kode_file (ID GDrive)
		$db = \Config\Database::connect();
		$fileData = $db->table('jurnalfile')->where('id', $id_jurnalfile)->get()->getRow();

		if ($fileData) {
			$gdriveId = $fileData->kode_file;

			// 2. Jika punya ID GDrive, hapus dari Google Drive
			if (!empty($gdriveId)) {
				$this->_deleteFromGoogleDrive($gdriveId);
			} else {
				// [Opsional] Jika file tersebut adalah file lama yang belum di-Drive-kan, hapus fisik lokalnya
				$pathFile = FCPATH . 'uploads/jurnal/' . $fileData->file;
				$pathThumb = FCPATH . 'uploads/jurnal/thumbnails/' . $fileData->file;
				if (file_exists($pathFile)) unlink($pathFile);
				if (file_exists($pathThumb)) unlink($pathThumb);
			}

			// 3. Hapus data dari tabel database
			$db->table('jurnalfile')->where('id', $id_jurnalfile)->delete();
		}

		$pesan = $this->_alertMessage('<strong>Berhasil!</strong> Lampiran berhasil dihapus.', 'success');
		$this->session->setFlashdata('pesan', $pesan);

		// Kembalikan user ke halaman edit
		return redirect()->to('jurnal/edit/' . $idjurnal);
	}



	public function autocomplate()

	{
		$cari = $this->request->getPost('term');

		$idperusahaan = $this->session->get('idperusahaan');

		if ($idperusahaan == "9999999999") {

			$tampil =  $this->request->getPost('idperusahaan');
		} else {

			$tampil =  $idperusahaan;
		}
		$query = "SELECT * FROM arsip WHERE idperusahaan = '$tampil' and

        		( nama_file like '%" . $cari . "%' or nama_pengirim like '%" . $cari . "%' ) order by nama_file asc limit 10";

		$res = $this->db->query($query);

		$result = array();

		foreach ($res->getResult() as $row) {

			array_push($result, array(

				'id' => $row->id,

				'nama_file' => $row->nama_file,

				'nama_pengirim' => $row->nama_pengirim,

				'file' => $row->file,
				'kode_file' => $row->kode_file,
			));
		}

		return $this->response->setJSON($result);
	}

	public function autocomplatePerusahaan()

	{
		$cari = $this->request->getPost('term');
		$query = "SELECT * FROM perusahaan WHERE namaperusahaan like '%" . $cari . "%' order by namaperusahaan asc limit 10";
		$res = $this->db->query($query);
		$result = array();
		foreach ($res->getResult() as $row) {
			array_push($result, array(
				'idperusahaan' => $row->idperusahaan,
				'namaperusahaan' => $row->namaperusahaan,
			));
		}
		return $this->response->setJSON($result);
	}

	public function getFile()
	{
		$idjurnal = $this->request->getPost('idjurnal');
		$query = "SELECT * FROM jurnalfile WHERE idjurnal = '$idjurnal'";
		$res = $this->db->query($query);
		$data = array();
		foreach ($res->getResult() as $row) {

			// array_push($result, array(

			// 	'file' => $row->file,
			// 	'nama_file' => $row->nama_file,
			// ));
			$rows = array();
			// 1. Tentukan sumber file (Google Drive atau Server Lokal)
			if (!empty($row->kode_file)) {
				// Jika kode_file ada, gunakan link Google Drive
				$linkViewer = "https://drive.google.com/file/d/" . $row->kode_file . "/preview";
			} else {
				// Jika kosong, gunakan file lokal yang ada di server
				$linkViewer = base_url('uploads/jurnal/thumbnails/' . $row->file);
			}

			// 2. Tentukan nama yang akan ditampilkan (nama_file atau nama fisik file)
			$teksTampil = ($row->nama_file == null) ? $row->file : $row->nama_file;

			// 3. Masukkan ke dalam array baris tabel
			// Catatan: Saya mengubah id="cetak-pdf" menjadi class "cetak-file-pdf" karena di dalam tabel (looping), ID tidak boleh ganda/duplikat agar modal Javascript tidak error.
			$rows[] = '<tr>
				<td class="text-left"> 
				<a href="javascript:void(0)" data-cetak_pdf="' . $linkViewer . '" class="tooltips cetak-file-pdf" data-toggle="modal" data-placement="left" data-target="#modalcetakpdf" title="Lihat dokumen">
					<i class="fa ' . (!empty($row->kode_file) ? 'fa-google' : 'fa-file') . ' mr-1"></i> ' . $teksTampil . '
				</a> 
				</td>
			</tr>';
			$data[] = $rows;
		}

		$output = array(
			"data" => $data,

		);

		return $this->response->setJSON($output);
	}


	public function simpanApprove()
	{
		$kondisi = $this->request->getPost('kondisi');
		$idpengguna = $this->request->getPost('idpengguna');
		$idjurnal = $this->request->getPost('idjurnal');

		if ($this->request->getPost('status_approve') != 'all'):
			$status_approve = $this->request->getPost('status_approve');

			if ($this->request->getPost('status_approve') == '1') {
				$keterangan_approve = '';
			} else {
				$keterangan_approve = $this->request->getPost('keterangan_approve');
			}
		else:
			$status_approve = '1';
			$keterangan_approve = $this->request->getPost('keterangan_approve');
		endif;

		$data = array(
			'approve' 	            => $status_approve,
			'keterangan_approve' 	=> $keterangan_approve,
			'id_pic' 	            => $idpengguna,
		);
		$simpan = $this->jurnal_model->updateApprove($idjurnal, $data);
		if ($simpan) {

			$pesan = '<div>

						<div class="alert alert-success alert-dismissable">

			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>

			                <strong>Berhasil.</strong> Jurnal telah disetujui

					    </div>

					</div>';
		} else {

			$eror = $this->db->error();

			$pesan = '<div>

						<div class="alert alert-danger alert-dismissable">

			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>

			                <strong>Maaf,</strong> Data gagal diapprove <br>

			                Pesan Error : ' . $eror['code'] . ' ' . $eror['message'] . '

					    </div>

					</div>';
		}


		$this->session->setFlashdata('pesan', $pesan);


		if ($kondisi == null) {

			return redirect()->to('jurnal');
		} else {

			return redirect()->to('Laporan/lapbukubesar');
		}
	}

	public function simpanFiskal()
	{
		// Pastikan request datang dari AJAX
		if ($this->request->isAJAX()) {
			$iddetailjurnal = $this->request->getPost('iddetailjurnal');

			// Tangkap data enum dari request
			$objek = $this->request->getPost('objek');
			$fiskal = $this->request->getPost('fiskal');

			// Validasi ketat untuk memastikan tipe data Enum masuk ke database
			// Memastikan nilainya berwujud string yang valid sesuai opsi, jika tidak default ke '0'
			$objek_enum = in_array($objek, ['0', '1', '2', '3']) ? (string)$objek : '0';
			$fiskal_enum = in_array($fiskal, ['0', '1']) ? (string)$fiskal : '0';

			// Susun data untuk diupdate
			$data = [
				'objek'           => $objek_enum,
				'objek_pajak'     => empty($this->request->getPost('objek_pajak')) ? 0 : $this->request->getPost('objek_pajak'),
				'fiskal'          => $fiskal_enum,
				'koreksi_positif' => empty($this->request->getPost('koreksi_positif')) ? 0 : $this->request->getPost('koreksi_positif'),
				'koreksi_negatif' => empty($this->request->getPost('koreksi_negatif')) ? 0 : $this->request->getPost('koreksi_negatif'),
				'keterangan'      => $this->request->getPost('keterangan')
			];

			$db = \Config\Database::connect();

			// Lakukan proses update berdasarkan iddetailjurnal
			$update = $db->table('jurnaldetail')
				->where('iddetailjurnal', $iddetailjurnal)
				->update($data);

			if ($update) {
				// Berikan balasan JSON sukses ke jQuery
				return $this->response->setJSON([
					'status' => true,
					'message' => 'Data berhasil disimpan'
				]);
			} else {
				// Berikan balasan JSON gagal ke jQuery
				return $this->response->setJSON([
					'status' => false,
					'message' => 'Gagal menyimpan ke database'
				]);
			}
		} else {
			// Jika bukan request AJAX, tolak aksesnya
			return $this->response->setStatusCode(403)->setBody('Direct access not allowed');
		}
	}


	/**
	 * Fungsi untuk mengupload file ke Google Drive berdasarkan Folder ID Perusahaan
	 */
	private function _uploadToGoogleDrive($filePath, $fileName, $folderPerusahaan, $folderBulan)
	{
		$client = new \Google\Client();

		// 1. Kredensial & Scope Akses
		$client->setAuthConfig(APPPATH . 'ThirdParty/oauth-credentials.json');
		$client->addScope(\Google\Service\Drive::DRIVE);
		$client->setAccessType('offline');
		$client->setPrompt('select_account consent');

		// 2. Load Token JSON
		$tokenPath = WRITEPATH . 'google-token-admin.json';
		if (file_exists($tokenPath)) {
			$accessToken = json_decode(file_get_contents($tokenPath), true);
			$client->setAccessToken($accessToken);
		} else {
			throw new \Exception("File token.json tidak ditemukan. Harap generate ulang token.");
		}

		// 3. Refresh Token Otomatis jika Expired
		if ($client->isAccessTokenExpired()) {
			if ($client->getRefreshToken()) {
				$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
				file_put_contents($tokenPath, json_encode($client->getAccessToken()));
			} else {
				throw new \Exception("Token expired dan Refresh Token tidak tersedia. Harap hapus token.json dan otorisasi ulang.");
			}
		}

		$driveService = new \Google\Service\Drive($client);

		// ==============================================================
		// Logika Pembuatan Folder Bertingkat & Dukungan Shared Drive
		// ==============================================================

		// ID Folder Utama (Root) tempat menampung seluruh folder perusahaan
		$parentFolderId     = env('IDJURNAL');
		$perusahaanFolderId = null; // Penampung ID folder Level 1
		$bulanFolderId      = null; // Penampung ID folder Level 2

		// -------------------------------------------------------------
		// TAHAP 1: Cari atau Buat Folder PERUSAHAAN (Level 1)
		// -------------------------------------------------------------
		$query1 = "mimeType='application/vnd.google-apps.folder' and name='{$folderPerusahaan}' and '{$parentFolderId}' in parents and trashed=false";
		$searchParams1 = [
			'q' => $query1,
			'fields' => 'files(id, name)',
			'supportsAllDrives' => true,
			'includeItemsFromAllDrives' => true
		];
		$results1 = $driveService->files->listFiles($searchParams1);

		if (count($results1->getFiles()) == 0) {
			$folderMetadata1 = new \Google\Service\Drive\DriveFile([
				'name'     => $folderPerusahaan,
				'mimeType' => 'application/vnd.google-apps.folder',
				'parents'  => [$parentFolderId]
			]);
			$folder1 = $driveService->files->create($folderMetadata1, ['fields' => 'id', 'supportsAllDrives' => true]);
			$perusahaanFolderId = $folder1->id;
		} else {
			$perusahaanFolderId = $results1->getFiles()[0]->id;
		}

		// -------------------------------------------------------------
		// TAHAP 2: Cari atau Buat Folder TAHUN-BULAN (Level 2)
		// -------------------------------------------------------------
		// Mencari di dalam $perusahaanFolderId, bukan lagi di $parentFolderId
		$query2 = "mimeType='application/vnd.google-apps.folder' and name='{$folderBulan}' and '{$perusahaanFolderId}' in parents and trashed=false";
		$searchParams2 = [
			'q' => $query2,
			'fields' => 'files(id, name)',
			'supportsAllDrives' => true,
			'includeItemsFromAllDrives' => true
		];
		$results2 = $driveService->files->listFiles($searchParams2);

		if (count($results2->getFiles()) == 0) {
			$folderMetadata2 = new \Google\Service\Drive\DriveFile([
				'name'     => $folderBulan,
				'mimeType' => 'application/vnd.google-apps.folder',
				'parents'  => [$perusahaanFolderId]
			]);
			$folder2 = $driveService->files->create($folderMetadata2, ['fields' => 'id', 'supportsAllDrives' => true]);
			$bulanFolderId = $folder2->id;
		} else {
			$bulanFolderId = $results2->getFiles()[0]->id;
		}

		// -------------------------------------------------------------
		// TAHAP 3: Upload File ke dalam Folder TAHUN-BULAN
		// -------------------------------------------------------------
		$fileMetadata = new \Google\Service\Drive\DriveFile([
			'name'    => $fileName,
			'parents' => [$bulanFolderId] // File dimasukkan ke folder Level 2
		]);

		$content = file_get_contents($filePath);
		$uploadedFile = $driveService->files->create($fileMetadata, [
			'data'       => $content,
			'mimeType'   => mime_content_type($filePath),
			'uploadType' => 'multipart',
			'fields'     => 'id',
			'supportsAllDrives' => true // Memastikan bisa upload ke folder Shared Drive
		]);

		return $uploadedFile->id;
	}
	/**
	 * Fungsi untuk menghapus file dari Google Drive berdasarkan ID File
	 */
	private function _deleteFromGoogleDrive($fileId)
	{
		try {
			$client = new \Google\Client();
			$client->setAuthConfig(APPPATH . 'ThirdParty/oauth-credentials.json'); // Gunakan file Kredensial OAuth Anda
			$client->addScope(\Google\Service\Drive::DRIVE_FILE);

			// Load Token OAuth
			$tokenPath = WRITEPATH . 'google-token-admin.json';
			if (file_exists($tokenPath)) {
				$accessToken = json_decode(file_get_contents($tokenPath), true);
				$client->setAccessToken($accessToken);
			}

			// Auto Refresh Token jika expired
			if ($client->isAccessTokenExpired() && $client->getRefreshToken()) {
				$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
				file_put_contents($tokenPath, json_encode($client->getAccessToken()));
			}

			$driveService = new \Google\Service\Drive($client);

			// Perintah untuk menghapus/memindahkan file ke Trash Google Drive
			$driveService->files->delete($fileId);

			return true;
		} catch (\Exception $e) {
			// Jika file sudah tidak ada di Drive (atau error lain), biarkan berlalu agar proses DB tetap jalan
			return false;
		}
	}

	/**
	 * Fungsi untuk menduplikasi (Copy) file yang sudah ada di GDrive ke Folder Target
	 */
	private function _copyFileInGoogleDrive($sourceFileId, $folderPerusahaan, $folderBulan)
	{
		$client = new \Google\Client();
		$client->setAuthConfig(APPPATH . 'ThirdParty/oauth-credentials.json');
		$client->addScope(\Google\Service\Drive::DRIVE);
		$client->setAccessType('offline');
		$client->setPrompt('select_account consent');

		$tokenPath = WRITEPATH . 'google-token-admin.json';
		if (file_exists($tokenPath)) {
			$client->setAccessToken(json_decode(file_get_contents($tokenPath), true));
		}

		if ($client->isAccessTokenExpired() && $client->getRefreshToken()) {
			$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			file_put_contents($tokenPath, json_encode($client->getAccessToken()));
		}

		$driveService = new \Google\Service\Drive($client);

		// ==============================================================
		// Logika Folder Bertingkat untuk COPY File
		// ==============================================================

		$parentFolderId     = env('IDJURNAL');
		$perusahaanFolderId = null;
		$bulanFolderId      = null;

		// -------------------------------------------------------------
		// TAHAP 1: Cari atau Buat Folder PERUSAHAAN (Level 1)
		// -------------------------------------------------------------
		$query1 = "mimeType='application/vnd.google-apps.folder' and name='{$folderPerusahaan}' and '{$parentFolderId}' in parents and trashed=false";
		$results1 = $driveService->files->listFiles([
			'q' => $query1,
			'fields' => 'files(id, name)',
			'supportsAllDrives' => true,
			'includeItemsFromAllDrives' => true
		]);

		if (count($results1->getFiles()) == 0) {
			$folderMetadata1 = new \Google\Service\Drive\DriveFile([
				'name'     => $folderPerusahaan,
				'mimeType' => 'application/vnd.google-apps.folder',
				'parents'  => [$parentFolderId]
			]);
			$folder1 = $driveService->files->create($folderMetadata1, ['fields' => 'id', 'supportsAllDrives' => true]);
			$perusahaanFolderId = $folder1->id;
		} else {
			$perusahaanFolderId = $results1->getFiles()[0]->id;
		}

		// -------------------------------------------------------------
		// TAHAP 2: Cari atau Buat Folder TAHUN-BULAN (Level 2)
		// -------------------------------------------------------------
		$query2 = "mimeType='application/vnd.google-apps.folder' and name='{$folderBulan}' and '{$perusahaanFolderId}' in parents and trashed=false";
		$results2 = $driveService->files->listFiles([
			'q' => $query2,
			'fields' => 'files(id, name)',
			'supportsAllDrives' => true,
			'includeItemsFromAllDrives' => true
		]);

		if (count($results2->getFiles()) == 0) {
			$folderMetadata2 = new \Google\Service\Drive\DriveFile([
				'name'     => $folderBulan,
				'mimeType' => 'application/vnd.google-apps.folder',
				'parents'  => [$perusahaanFolderId]
			]);
			$folder2 = $driveService->files->create($folderMetadata2, ['fields' => 'id', 'supportsAllDrives' => true]);
			$bulanFolderId = $folder2->id;
		} else {
			$bulanFolderId = $results2->getFiles()[0]->id;
		}

		// -------------------------------------------------------------
		// TAHAP 3: Duplikasi (Copy) File ke Folder TAHUN-BULAN
		// -------------------------------------------------------------
		$copiedFileMetadata = new \Google\Service\Drive\DriveFile([
			'parents' => [$bulanFolderId]
		]);

		try {
			$copiedFile = $driveService->files->copy($sourceFileId, $copiedFileMetadata, [
				'supportsAllDrives' => true,
				'fields' => 'id'
			]);

			// Kembalikan ID File hasil kopian (ID Baru)
			return $copiedFile->id;
		} catch (\Exception $e) {
			// Jika gagal (misal file asli di arsip terhapus manual), kembalikan ID lama sebagai fallback
			return $sourceFileId;
		}
	}

	/**
	 * Fungsi helper untuk merapikan penulisan HTML Alert
	 */
	private function _alertMessage($text, $type)
	{
		return '<div>
                <div class="alert alert-' . $type . ' alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                    ' . $text . '
                </div>
            </div>';
	}
}



/* End of file Jurnal.php */

/* Location: ./application/controllers/Jurnal.php */