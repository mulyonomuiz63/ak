<?= $this->extend('template/admin') ?>
<?= $this->section('css'); ?>
<style>
    /* Container switch */
    .switch-btn {
      position: relative;
      display: inline-block;
      width: 70px;   /* lebar lebih panjang biar muat teks */
      height: 40px;
    }

    .switch-btn input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    /* Background switch */
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 10px; /* radius persegi */
      font-size: 14px;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 12px;
      color: white;
    }

    /* Tulisan ON di kiri, OFF di kanan */
    .slider::before {
      content: "ON";
    }
    .slider::after {
      content: "OFF";
    }

    /* Tombol bulat */
    .circle {
      position: absolute;
      height: 32px;
      width: 32px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      border-radius: 8px; /* sedikit tumpul biar matching */
      transition: .4s;
    }

    /* Aktif (ON) */
    input:checked + .slider {
      background-color: hotpink;
    }
    input:checked + .slider .circle {
      transform: translateX(30px);
    }
  </style>
<?= $this->endSection(); ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->

<div class="container-fluid">



  <div class="h-100">
    <div class="row h-100 justify-content-center">

      <div class="col-md-12">

        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">LAPORAN KOREKSI FISKAL</h6>
          </div>
          <div class="card-body">

            <?php
            $pesan = session()->getFlashdata('pesan');
            if (!empty($pesan)) {
              echo $pesan;
            }
            ?>


            <form action="<?php echo (site_url('laporan/lapkoreksifiskal')) ?>" class="" method="post" id="myForm">
                <?php if(session()->get('idpengguna') === '8888888888'): ?>
                      <div class="form-group row">
                        <label for="" class="col-md-2 col-form-label text-right">Nama Perusahaan : </label>
                        <div class="col-md-10">
                          <input type="text" id="namaperusahaan" name="namaperusahaan" class="form-control" placeholder="Pilih nama perusahaan" value="<?= $namaperusahaan ?>">
                          <input type="hidden" id="idperusahaan" name="idperusahaan" value="<?= $idperusahaan ?>">
                        </div>
                      </div>
                <?php else: ?>
                    <input type="hidden" id="idperusahaan" name="idperusahaan" value="<?= session()->get('idperusahaan') ?>">
                <?php endif; ?>

              <div class="form-group row">
                <label for="" class="col-md-2 col-form-label text-right">Periode Laporan : </label>
                <div class="col-md-2 pr-0 pl-0">
                  <input type="date" id="tglawal" name="tglawal" class="form-control" value="<?php echo ($tglawal) ?>">
                </div>
                <label for="" class="col-md-1 col-form-label text-center">S/D</label>
                <div class="col-md-2 pl-0 pr-0 mb-2">
                  <input type="date" id="tglakhir" name="tglakhir" class="form-control" value="<?php echo ($tglakhir) ?>">
                </div>
                <div class="col-md-5 d-flex justify-content-start ">
                  <div>
                    <button type="submit" id="searchLaporan" class="btn btn-info tooltips" data-toggle="tooltip" data-placement="left" title="Cari Laporan"><i class="fa fa-search"></i></button>
                    <a href="#" class="btn btn-secondary mr-sm-0 tooltips" data-toggle="modal" data-placement="left" title="Cetak Pdf" id="cetak" data-target="#modalcetakpdf"><i class="fa fa-print"></i></a>
                    <?php if (session()->get('hitLanggana')) { ?>
                      <a href="javascript: void(0)" class="btn btn-success tooltips" data-toggle="tooltip" data-placement="left" title="Export Excel (filter)" target="_blank" id="cetakexcel"><i class="fa fa-file-excel"></i></a>
                    <?php } else { ?>
                      <a href="<?php echo (site_url('Histori')) ?>" class="btn btn-success tooltips" data-toggle="tooltip" data-placement="left" title="Export Excel (filter)"><i class="fa fa-file-excel"></i></a>
                    <?php } ?>
                  </div>
                  <label class="switch-btn ml-1 tooltips" data-toggle="tooltip" data-placement="top" title="Cetak Ringkas">
                    <input type="checkbox" class="checkbox" value="1" name="akunlevel3" id="akunlevel3" <?php echo ($akunlevel3 == '1') ? 'checked' : '' ?>>
                    <span class="slider">
                      <span class="circle"></span>
                    </span>
                  </label>
                </div>
              </div>




              <div class="row mt-0">

                <div class="col-md-12">
                    <hr>
                    <?php if(!empty($idperusahaan)): ?>
                      <div class="table-responsive">
                        <table border="1" cellpadding="5" width="100%" style="border-color:#e3e6f0;">
                          <thead>
                            <tr style="background-color:#055F93; color: white;">
                              <th width="10%" style="font-size:13px; font-weight:bold; text-align:center; vertical-align:middle;">Kode Akun</th>
                              <th width="30%" style="font-size:13px; font-weight:bold; text-align:center; vertical-align:middle;">Nama Akun</th>
                              <th width="15%" style="font-size:13px; font-weight:bold; text-align:center; vertical-align:middle;">Jumlah (Rp)</th>
                              <th width="15%" style="font-size:13px; font-weight:bold; text-align:center; vertical-align:middle;">Korfis Positif (Rp)</th>
                              <th width="15%" style="font-size:13px; font-weight:bold; text-align:center; vertical-align:middle;">Korfis Negatif (Rp)</th>
                              <th width="15%" style="font-size:13px; font-weight:bold; text-align:center; vertical-align:middle;">Fiskal (Rp)</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
    
                            $no = 1;
                            $gt = 0;
                            $total1 = 0;
                            $totalaset = 0;
                            $totalhutangdanmodal = 0;
                            $kdakun_old = '';
    
                            $totaldesc = '';
                            $totaldesc2 = '';
                            $totalrupiah = 0;
                            $totalrupiah2 = 0;
    
                            $totalpendapatan = 0;
                            $totalpengeluaran = 0;
                            $totalfiskal = 0;
                            $totalkorpositif = 0;
                            $totalkornegatif = 0;
    
                            foreach ($rsData->getResult() as $data) {
                              $total1 += $data->jumlah;
                              $font = '';
    
                              if ((substr($kdakun_old, 0, 1) != substr($data->kdakun, 0, 1)) && $kdakun_old != '' && substr($kdakun_old, 0, 1) != '7') {
                                echo '
                                  <tr height="25">
                                    <td style="border-right-color: #e6f5fe;background-color: #e6f5fe;"></td>
                                    <td style="background-color: #e6f5fe; font-size: 13px; font-weight: bold; text-align:left;">' . $totaldesc . '</td>
                                    <td style="background-color: #e6f5fe; font-size: 13px; font-weight: bold; text-align:right;">' . ($totalrupiah >= 0 ? number_format($totalrupiah, 0, ",", ".") : "(" . number_format($totalrupiah * -1, 0, ",", ".") . ")") . '</td>
                                    <td style="background-color: #e6f5fe; font-size: 13px; font-weight: bold; text-align:right;">' . ($totalkorpositif >= 0 ? number_format($totalkorpositif, 0, ",", ".") : "(" . number_format($totalkorpositif * -1, 0, ",", ".") . ")") . '</td>
                                    <td style="background-color: #e6f5fe; font-size: 13px; font-weight: bold; text-align:right;">' . ($totalkornegatif >= 0 ? number_format($totalkornegatif, 0, ",", ".") : "(" . number_format($totalkornegatif * -1, 0, ",", ".") . ")") . '</td>
                                    <td style="background-color: #e6f5fe; font-size: 13px; font-weight: bold; text-align:right;">' . ($totalfiskal >= 0 ? number_format($totalfiskal, 0, ",", ".") : "(" . number_format($totalfiskal * -1, 0, ",", ".") . ")") . '</td>
                                  </tr>';
                                if ($totaldesc == "TOTAL PENDAPATAN") {
                                  $total_pendapatan = $totalrupiah;
                                }
    
                                if ($totaldesc == "TOTAL HARGA POKOK PENJUALAN") {
                                  $total_harga_pokok_penjualan = $totalrupiah;
                                  $totallabarugi = $total_pendapatan - $total_harga_pokok_penjualan;
                                  echo '
                                  <tr height="25" >
                                    <td style="border-right-color: #e6f5fe;background-color: #e6f5fe;"></td>
                                    <td style="background-color: #e6f5fe; font-size: 13px; font-weight: bold; text-align:left;">TOTAL LABA RUGI KOTOR</td>
                                    <td style="background-color: #e6f5fe; font-size: 13px; font-weight: bold; text-align:right;">' . ($totallabarugi >= 0 ? number_format($totallabarugi, 0, ",", ".") : "(" . number_format($totallabarugi * -1, 0, ",", ".") . ")") . '</td>
                                    <td style="background-color: #e6f5fe;" colspan="3"></td>
                                  </tr>';
                                }
    
    
    
                                $totalrupiah = 0;
                                $totalfiskal = 0;
                                $totalkorpositif = 0;
                                $totalkornegatif = 0;
                              }
    
                              //khusus PENDAPATAN DAN BEBAN LAINNYA
                              if ((substr($kdakun_old, 0, 2) != substr($data->kdakun, 0, 2)) && $kdakun_old != '' && substr($kdakun_old, 0, 1) == '7' && $kdakun_old != '70000') {
                                if ($totaldesc2 != "TOTAL PENDAPATAN LAINNYA") {
                                  echo '';
                                }
                                $totalrupiah = 0;
                              }

                              if ((substr($data->kdakun, 0, 1) == '4' || substr($data->kdakun, 0, 2) == '71') && $data->level == '4') {
                                $totalpendapatan += $data->jumlah;
                              } elseif ((substr($data->kdakun, 0, 1) == '5' || substr($data->kdakun, 0, 1) == '6' || substr($data->kdakun, 0, 2) == '72') && $data->level == '4') {
                                $totalpengeluaran += $data->jumlah;
                              } else {
                                $totalpengeluaran += 0;
                              }


                              if ((substr($data->kdakun, 0, 1) == '4' || substr($data->kdakun, 0, 2) == '71')){
                                // Mengambil dan menghitung nilai Korfis & Fiskal
                                $korfis_pos = isset($data->koreksi_positif) ? (float)$data->koreksi_positif : 0;
                                $korfis_neg = isset($data->koreksi_negatif) ? (float)$data->koreksi_negatif : 0;
                                $fiskal_val = (float)$data->jumlah + $korfis_pos - $korfis_neg;
                              } elseif ((substr($data->kdakun, 0, 1) == '5' || substr($data->kdakun, 0, 1) == '6' || substr($data->kdakun, 0, 2) == '72')) {
                                // Mengambil dan menghitung nilai Korfis & Fiskal
                                $korfis_pos = isset($data->koreksi_positif) ? (float)$data->koreksi_positif : 0;
                                $korfis_neg = isset($data->koreksi_negatif) ? (float)$data->koreksi_negatif : 0;
                                $fiskal_val = (float)$data->jumlah - $korfis_pos + $korfis_neg;
                              }else{
                                $totalpengeluaran += 0;
                                // Mengambil dan menghitung nilai Korfis & Fiskal
                                $korfis_pos = 0;
                                $korfis_neg = 0;
                                $fiskal_val = (float)$data->jumlah + $korfis_pos - $korfis_neg;
                              }
                              
    
                              switch ($data->level) {
                                case '1':
                                  $font = 'font-size: 13px; font-weight: bold;';
                                  $spasi = '';
                                  $totaldesc = 'TOTAL ' . strtoupper($data->nmakun);
    
                                  //$totalrupiah += $data->jumlah;
                                  break;
                                case '2':
                                  $font = 'font-size: 13px;';
                                  $spasi = str_repeat('&nbsp;', 4);
                                  $totaldesc2 = 'TOTAL ' . strtoupper($data->nmakun);
    
                                  //edit di sini
    
                                  break;
                                case '3':
                                  $font = 'font-size: 13px;';
                                  $spasi = str_repeat('&nbsp;', 8);
                                  break;
                                case '4':
                                  $font = 'font-size: 13px;';
                                  $spasi = str_repeat('&nbsp;', 12);
                                  $totalrupiah += $data->jumlah;
                                  $totalfiskal += $fiskal_val;
                                  $totalkorpositif += $korfis_pos;
                                  $totalkornegatif += $korfis_neg;
                                  break;
                                default:
                                  $font = '';
                                  break;
                              }
        
    
                              if ($akunlevel3 == 1) {
                                if ($data->level != 4) {
                                  echo '
                                  <tr> 
                                    <td style="' . $font . ' text-align:center;">' . $data->kdakun . '</td>
                                    <td style="' . $font . ' text-align:left;">' . $spasi . $data->nmakun . '</td>
                                    <td style="' . $font . ' text-align:right;">' . ((($data->kdakun != '70000') || ($data->kdakun != '700000')) && $data->level != 1  ? ($data->jumlah >= 0 ? number_format($data->jumlah, 0, ",", ".") : "(" . number_format($data->jumlah * -1, 0, ",", ".") . ")") : "") . '</td>
                                    <td style="' . $font . ' text-align:right;">' . ((($data->kdakun != '70000') || ($data->kdakun != '700000')) && $data->level != 1  ? ($korfis_pos >= 0 ? number_format($korfis_pos, 0, ",", ".") : "(" . number_format($korfis_pos * -1, 0, ",", ".") . ")") : "") . '</td>
                                    <td style="' . $font . ' text-align:right;">' . ((($data->kdakun != '70000') || ($data->kdakun != '700000')) && $data->level != 1  ? ($korfis_neg >= 0 ? number_format($korfis_neg, 0, ",", ".") : "(" . number_format($korfis_neg * -1, 0, ",", ".") . ")") : "") . '</td>
                                    <td style="' . $font . ' text-align:right;">' . ((($data->kdakun != '70000') || ($data->kdakun != '700000')) && $data->level != 1  ? ($fiskal_val >= 0 ? number_format($fiskal_val, 0, ",", ".") : "(" . number_format($fiskal_val * -1, 0, ",", ".") . ")") : "") . '</td>
                                  </tr>';
                                }
                              } else {
                                if ($data->kdakun != "730000" && $data->kdakun != "73000") {
                                  echo '
                                  <tr>
                                    <td style="' . $font . ' text-align:center;">' . $data->kdakun . '</td>
                                    <td style="' . $font . ' text-align:left;">' . $spasi . $data->nmakun . '</td>
                                    <td style="' . $font . ' text-align:right;">' . ((($data->kdakun != '70000') || ($data->kdakun != '700000')) && $data->level != 1 ? ($data->jumlah >= 0 ? number_format($data->jumlah, 0, ",", ".") : "(" . number_format($data->jumlah * -1, 0, ",", ".") . ")") : "") . '</td>
                                    <td style="' . $font . ' text-align:right;">' . ((($data->kdakun != '70000') || ($data->kdakun != '700000')) && $data->level != 1  ? ($korfis_pos >= 0 ? number_format($korfis_pos, 0, ",", ".") : "(" . number_format($korfis_pos * -1, 0, ",", ".") . ")") : "") . '</td>
                                    <td style="' . $font . ' text-align:right;">' . ((($data->kdakun != '70000') || ($data->kdakun != '700000')) && $data->level != 1  ? ($korfis_neg >= 0 ? number_format($korfis_neg, 0, ",", ".") : "(" . number_format($korfis_neg * -1, 0, ",", ".") . ")") : "") . '</td>
                                    <td style="' . $font . ' text-align:right;">' . ((($data->kdakun != '70000') || ($data->kdakun != '700000')) && $data->level != 1  ? ($fiskal_val >= 0 ? number_format($fiskal_val, 0, ",", ".") : "(" . number_format($fiskal_val * -1, 0, ",", ".") . ")") : "") . '</td>
                                  </tr>';
                                }
                              }
    
    
                              $kdakun_old = $data->kdakun;
                            }
    
                            if ($totaldesc2 != "TOTAL BEBAN LAINNYA") {
                              echo '
                                  <tr height="25">
                                    <td style="border-right-color:#e6f5fe;background-color: #e6f5fe;"></td>
                                    <td style="background-color: #e6f5fe; font-size: 13px; font-weight: bold; text-align:left;">' . $totaldesc2 . '</td>
                                    <td style="background-color: #e6f5fe; font-size: 13px; font-weight: bold; text-align:right;">' . ($totalrupiah >= 0 ? number_format($totalrupiah, 0, ",", ".") : "(" . number_format($totalrupiah * -1, 0, ",", ".") . ")") . '</td>
                                    <td style="background-color: #e6f5fe;" colspan="3"></td>
                                  </tr>';
                            }
                            $totalsebelumpajak = $totalpendapatan - $totalpengeluaran;
                            echo '
                                  <tr height="25" style="background-color: #e6f5fe;">
                                    <td style="border-right-color:#fff;"></td>
                                    <td style="font-size: 13px; font-weight: bold; text-align:left;">LABA (RUGI) SEBELUM PAJAK PENGHASILAN</td>
                                    <td style="font-size: 13px; font-weight: bold; text-align:right;">' . ($totalsebelumpajak >= 0 ? number_format($totalsebelumpajak, 0, ",", ".") : "(" . number_format($totalsebelumpajak * -1, 0, ",", ".") . ")") . '</td>
                                    <td style="background-color: #e6f5fe;" colspan="3"></td>
                                  </tr>';
    
                            $bebanpajakpenghasilan = 0;
                            foreach ($rsData->getResult() as $data) {
                              if ($data->kdakun == "730000" || $data->kdakun == "73000") {
                                $bebanpajakpenghasilan = $data->jumlah;
                                $korfis_pos = isset($data->koreksi_positif) ? (float)$data->koreksi_positif : 0;
                                $korfis_neg = isset($data->koreksi_negatif) ? (float)$data->koreksi_negatif : 0;
                                $fiskal_val = (float)$data->jumlah + $korfis_pos - $korfis_neg;

                                echo '
                                  <tr height="25" style="">
                                    <td style="' . $font . ' text-align:center;">' . $data->kdakun . '</td>
                                    <td style="' . $font . ' text-align:left;">' . $spasi . 'Beban Pajak Penghasilan</td>
                                    <td style="font-size: 13px; text-align:right;">' . ($data->jumlah >= 0 ? number_format($data->jumlah, 0, ",", ".") : "(" . number_format($data->jumlah * -1, 0, ",", ".") . ")") . '</td>
                                    <td style="font-size: 13px; text-align:right;">' . ($korfis_pos >= 0 ? number_format($korfis_pos, 0, ",", ".") : "(" . number_format($korfis_pos * -1, 0, ",", ".") . ")") . '</td>
                                    <td style="font-size: 13px; text-align:right;">' . ($korfis_neg >= 0 ? number_format($korfis_neg, 0, ",", ".") : "(" . number_format($korfis_neg * -1, 0, ",", ".") . ")") . '</td>
                                    <td style="font-size: 13px; text-align:right;">' . ($fiskal_val >= 0 ? number_format($fiskal_val, 0, ",", ".") : "(" . number_format($fiskal_val * -1, 0, ",", ".") . ")") . '</td>
                                  </tr>';
                              }
                            }
                            $totallabasetelahpajak = ($totalpendapatan - $totalpengeluaran) - $bebanpajakpenghasilan;
                            echo '
                                  <tr height="25" style="background-color: #e6f5fe;">
                                    <td style="border-right-color:#fff;"></td>
                                    <td style="font-size: 13px; font-weight: bold; text-align:left;">LABA (RUGI) SETELAH PAJAK PENGHASILAN</td>
                                    <td style="font-size: 13px; font-weight: bold; text-align:right;">' . ($totallabasetelahpajak >= 0 ? number_format($totallabasetelahpajak, 0, ",", ".") : "(" . number_format($totallabasetelahpajak * -1, 0, ",", ".") . ")") . '</td>
                                    <td style="background-color: #e6f5fe;" colspan="3"></td>
                                  </tr>';
                            echo ' </tbody>
                                </table>';
                            ?>
    
    
    
                      </div>
                    <?php endif; ?>

                </div>
              </div>
            </form>


          </div>
        </div>


      </div>


    </div>
  </div>


</div>

<!--modal cetak pdf-->
<div class="modal fade" id="modalcetakpdf" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="isiKonten"></div>
        </div>
    </div>
</div>
<!--modal cetak pdf-->

<!-- /.container-fluid -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js" integrity="sha256-0YPKAwZP7Mp3ALMRVB2i8GXeEndvCq3eSl/WsAl1Ryk=" crossorigin="anonymous"></script>
<script type="text/javascript">
  var idpengguna = '<?php echo (session()->get('idpengguna')) ?>';

  $('#cetak').click(function() {
    var tglawal = $('#tglawal').val();
    var tglakhir = $('#tglakhir').val();
    var idperusahaan = $('#idperusahaan').val();

    if (tglawal === '' || tglakhir === '') {
      bootbox.alert('Pilih Periode!');
      return;
    }

    if (idpengguna == '8888888888' && idperusahaan === '') {
      bootbox.alert('Pilih nama perusahaan!');
      return;
    }


    if ($("#akunlevel3").is(':checked')) {
      var akunlevel3 = '1';

    } else {
      var akunlevel3 = '0';

    }
        var url = "<?php echo site_url('laporan/lapkoreksifiskal-cetak/') ?>" + tglawal + "/" + tglakhir + "/" + akunlevel3 + "/" + idperusahaan;
        $(".isiKonten").html(`
            <div class="modal-header">
                <h5 class="modal-title">Laporan Koreksi Fiskal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- Gunakan tinggi tetap misal 75vh agar modal tidak menembus layar -->
            <div class="modal-body p-0" style="position: relative; height: 75vh; overflow: hidden;">
                
                <!-- Indikator Loading (Tampil di tengah) -->
                <div id="loadingIframe" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; z-index: 10;">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="mt-3 font-weight-bold text-muted">Sedang menarik banyak data...<br>Mohon tunggu sebentar.</div>
                </div>

                <!-- Iframe (Awalnya disembunyikan menggunakan opacity) -->
                <iframe id="frameBukuBesar" src="${url}" width="100%" height="100%" style="border: none; opacity: 0; transition: opacity 0.5s; position: relative; z-index: 5;"></iframe>
                
            </div>
        `);

    // 2. Deteksi kapan Iframe selesai memuat SELURUH data dari server
    $("#frameBukuBesar").on("load", function() {
      // Sembunyikan loading
      $("#loadingIframe").fadeOut();

      // Tampilkan iframe secara halus
      $(this).css("opacity", "1");
    });
  });


  $('#cetakexcel').click(function() {
    var tglawal = $('#tglawal').val();
    var tglakhir = $('#tglakhir').val();
    var idperusahaan = $('#idperusahaan').val();

    if (tglawal === '' || tglakhir === '') {
      bootbox.alert('Pilih Periode!');
      return;
    }

    if (idpengguna == '8888888888' && idperusahaan === '') {
      bootbox.alert('Pilih nama perusahaan!');
      return;
    }


    if ($("#akunlevel3").is(':checked')) {
      var akunlevel3 = '1';

    } else {
      var akunlevel3 = '0';

    }

    window.open("<?php echo site_url('laporan/lapkoreksifiskal-excel/') ?>" + tglawal + "/" + tglakhir + "/" + akunlevel3 + "/" + idperusahaan);
    location.reload();
  });

  $("#namaperusahaan").autocomplete({
      minLength: 0,
      source: function(request, response) {
        $.ajax({
          type: "POST",
          url: "<?php echo site_url('perusahaan/autocomplate'); ?>",
          dataType: "json",
          data: {
            term: request.term
          },
          success: function(data) {
            response(data);
          }
        });
      },
      focus: function(event, ui) {
        $('#idperusahaan').val(ui.item.idperusahaan);
        $('#namaperusahaan').val(ui.item.namaperusahaan);
        return false;
      },
      select: function(event, ui) {
        $('#idperusahaan').val(ui.item.idperusahaan);
        $('#namaperusahaan').val(ui.item.namaperusahaan);
        return false;
      }
    })
    .autocomplete("instance")._renderItem = function(ul, item) {
      return $("<li>")
        .append("<div><b>" + item.idperusahaan + " " + item.namaperusahaan + "</b></div>")
        .appendTo(ul);
    };
</script>
<script>
const toggleSwitch = document.getElementById("akunlevel3");
const formSwitch = document.getElementById("myForm");

toggleSwitch.addEventListener("change", function() {
    const cb = document.getElementById('akunlevel3');
  
  // Kalau checkbox tidak dicentang
  if (!cb.checked) {
    // Buat input hidden sementara
    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = cb.name;
    hidden.value = 0;
    this.appendChild(hidden);
  }
    formSwitch.submit();
});

document.getElementById('myForm').addEventListener('submit', function() {
  const cb = document.getElementById('akunlevel3');
  
  // Kalau checkbox tidak dicentang
  if (!cb.checked) {
    // Buat input hidden sementara
    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = cb.name;
    hidden.value = 0;
    this.appendChild(hidden);
  }
});
</script>
<?= $this->endSection() ?>