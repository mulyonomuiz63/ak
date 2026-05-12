<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">



  <div class="h-100">
    <div class="row h-100 justify-content-center">

      <div class="col-md-12">

        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">LAPORAN PERUBAHAN EKUITAS</h6>
          </div>
          <div class="card-body">

            <?php
            $pesan = session()->getFlashdata('pesan');
            if (!empty($pesan)) {
              echo $pesan;
            }
            ?>


            <form action="<?php echo (site_url('laporan/lapperubahanekuitas')) ?>" class="" method="post">
              <?php if (session()->get('idpengguna') === '8888888888'): ?>
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
                <label for="" class="col-md-2 col-form-label text-right mt-2">Periode Laporan : </label>
                <div class="col-md-3 mt-2">
                  <input type="date" id="tglawal" name="tglawal" class="form-control" value="<?php echo ($tglawal) ?>" readonly>
                </div>
                <label for="" class="col-md-1 col-form-label text-center mt-2">S/D</label>
                <div class="col-md-3 mt-2">
                  <input type="date" id="tglakhir" name="tglakhir" class="form-control" value="<?php echo ($tglakhir) ?>">
                </div>
                <div class="col-md-3 mt-2">
                  <button type="submit" id="searchLaporan" class="btn btn-info tooltips" data-toggle="tooltip" data-placement="left" title="Cari laporan"><i class="fa fa-search"></i></button>
                  <a href="#" class="btn btn-secondary mr-sm-2 tooltips" data-toggle="modal" data-placement="left" title="Cetak pdf" id="cetak" data-target="#modalcetakpdf"><i class="fa fa-print"></i></a>

                </div>
              </div>

            </form>

            <div class="row mt-4">

              <div class="col-md-12">
                <hr>
                <?php if (!empty($idperusahaan)): ?>
                  <div class="table-responsive">




                    <table border="0" cellpadding="2" width="100%">
                      <h5 class="mb-3 font-weight-bold text-center">PERUBAHAN EKUITAS</h5>
                      <?php
                      $this->db = \Config\Database::connect();
                      $totalaset = 0;
                      $totalhutangdanmodal = 0;
                      $totalasetBerjalan = 0;
                      $totalhutangdanmodalBerjalan = 0;

                      foreach ($rsDataBerjalan->getResult() as $dataBerjalan) {
                        if (substr($dataBerjalan->kdakun, 0, 1) == '1' && $dataBerjalan->level == '1') {
                          $totalasetBerjalan += $dataBerjalan->jumlah;
                        }

                        if ((substr($dataBerjalan->kdakun, 0, 1) == '2' || substr($dataBerjalan->kdakun, 0, 1) == '3') && $dataBerjalan->level == '4') {
                          $totalhutangdanmodalBerjalan += $dataBerjalan->jumlah;
                        }
                      }


                      foreach ($rsData->getResult() as $data) {
                        if (substr($data->kdakun, 0, 1) == '1' && $data->level == '1') {
                          $totalaset += $data->jumlah;
                        }
                        if ((substr($data->kdakun, 0, 1) == '2' || substr($data->kdakun, 0, 1) == '3') && $data->level == '4') {
                          $totalhutangdanmodal += $data->jumlah;
                        }
                      }
                      $totallabaditahan = ($totalaset - $totalhutangdanmodal) - ($totalasetBerjalan - $totalhutangdanmodalBerjalan);
                      $totallabarugiberjalan = $totalasetBerjalan - $totalhutangdanmodalBerjalan;


                      echo ' 
                                <thead>       
                                </thead>
                                <tbody>';

                      // MOdal awal

                      $modalawal = $this->db->query('
                        SELECT SUM(kredit)-SUM(debet) as modalawal FROM v_jurnaldetail 
                        WHERE idperusahaan ="' . $idperusahaan . '" and  LEFT(kdakun,3) IN ("311") and tgljurnal between "' . $tglawal . '" and "' . $tglakhir . '";
                        ')->getRow()->modalawal;

                      $modalawal = ($modalawal == '') ? 0 : $modalawal;

                      echo '<tr>
                                  <th width= "75%" style="font-size:14px; font-weight:bold; text-align:left;" colspan="3">Modal Awal</th>
                                  <th width= "10%" style="font-size:14px; font-weight:bold; text-align:left;">Rp.</th>
                                  <th width= "15%" style="font-size:14px; font-weight:bold; text-align:right;">' . ($modalawal >= 0 ? number_format($modalawal, 0, ",", ".") : "(" . number_format($modalawal * -1, 0, ",", ".") . ")") . '</th>
                                 </tr>
                                 ';

                      echo '<tr><td colspan="5"></td></tr>';





                      //Penambahan Investasi
                      $penambahaninvestasi = $this->db->query('
                                              SELECT SUM(kredit)-SUM(debet) as penambahaninvestasi FROM v_jurnaldetail 
                                              WHERE idperusahaan ="' . $idperusahaan . '" and  LEFT(kdakun,3) IN ("312") and v_jurnaldetail.tgljurnal between "' . $tglawal . '" and "' . $tglakhir . '";
                                              ')->getRow()->penambahaninvestasi;
                      $penambahaninvestasi = ($penambahaninvestasi == '') ? 0 : $penambahaninvestasi;

                      echo '<tr>
                                  <th width= "50%" style="font-size:14px; text-align:left;">' . str_repeat('&nbsp;', 10) . 'Tambahan Modal</th>
                                  <th width= "10%" style="font-size:14px; text-align:left;">Rp.</th>
                                  <th width= "15%" style="font-size:14px; text-align:right;">' . ($penambahaninvestasi >= 0 ? number_format($penambahaninvestasi, 0, ",", ".") : "(" . number_format($penambahaninvestasi * -1, 0, ",", ".") . ")") . '</th>
                                  <th width= "10%" style="font-size:14px; text-align:left;"></th>
                                  <th width= "15%" style="font-size:14px; text-align:left;"></th>
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
                                              WHERE idperusahaan ="' . $idperusahaan . '" and  LEFT(kdakun,5) IN ("32111")  and tgljurnal between "' . $tglawal . '" and "' . $tglakhir . '";
                                              ')->getRow()->labarugisebelumnya;
                      $labarugisebelumnya = ($labarugisebelumnya == '') ? 0 : $labarugisebelumnya;


                      //Laba Bersih
                      $lb_penerimaan = $this->db->query('
                                              SELECT SUM(kredit)-SUM(debet) as lb_penerimaan FROM v_jurnaldetail 
                                              WHERE idperusahaan ="' . $idperusahaan . '" and  (LEFT(kdakun,2) IN ("71") or LEFT(kdakun,1) IN ("4") ) and tgljurnal between "' . $tglawal . '" and "' . $tglakhir . '";
                                              ')->getRow()->lb_penerimaan;
                      $lb_penerimaan = ($lb_penerimaan == '') ? 0 : $lb_penerimaan;

                      $lb_pengeluaran = $this->db->query('
                                              SELECT SUM(debet)-SUM(kredit) as lb_pengeluaran FROM v_jurnaldetail 
                                              WHERE idperusahaan ="' . $idperusahaan . '" and  (LEFT(kdakun,2) IN ("72") or LEFT(kdakun,1) IN ("5","6") ) and tgljurnal between "' . $tglawal . '" and "' . $tglakhir . '"
                                              ')->getRow()->lb_pengeluaran;
                      $lb_pengeluaran = ($lb_pengeluaran == '') ? 0 : $lb_pengeluaran;


                      $lababersih = $lb_penerimaan - $lb_pengeluaran + $labarugisebelumnya;

                      echo '<tr>
                                  <th width= "50%" style="font-size:14px; text-align:left;">' . str_repeat('&nbsp;', 10) . 'Laba (Rugi) Usaha</th>
                                  <th width= "10%" style="font-size:14px; text-align:left;">Rp.</th>
                                  <th width= "15%" style="font-size:14px; border-bottom:1pt solid black; text-align:right;">' . ($totallabarugiberjalan >= 0 ? number_format($totallabarugiberjalan, 0, ",", ".") : "(" . number_format($totallabarugiberjalan * -1, 0, ",", ".") . ")") . '</th>
                                  <th width= "10%" style="font-size:14px; text-align:left;"></th>
                                  <th width= "15%" style="font-size:14px; text-align:left;"></th>
                                 </tr>
                                 ';

                      //laba rugi 
                      //  <th width= "15%" style="font-size:14px; border-bottom:1pt solid black; text-align:right;">' . ($lababersih >= 0 ? number_format($lababersih, 0, ",", ".") : "(" . number_format($lababersih * -1, 0, ",", ".") . ")") . '</th>
                      $totalinvestasi = $penambahaninvestasi + $totallabarugiberjalan;
                      echo '<tr>
                                  <th width= "50%" style="font-size:14px; text-align:left;"></th>
                                  <th width= "10%" style="font-size:14px; text-align:left;">Rp.</th>
                                  <th width= "15%" style="font-size:14px; text-align:right;">' . ($totalinvestasi >= 0 ? number_format($totalinvestasi, 0, ",", ".") : "(" . number_format($totalinvestasi * -1, 0, ",", ".") . ")") . '</th>
                                  <th width= "10%" style="font-size:14px; text-align:left;"></th>
                                  <th width= "15%" style="font-size:14px; text-align:left;"></th>
                                 </tr>
                                 ';
                      echo '<tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                ';

                      //dividen/prive
                      echo '<tr>
                                  <th width= "50%" style="font-size:14px; text-align:left;">' . str_repeat('&nbsp;', 10) . 'Dividen/Prive</th>
                                  <th width= "10%" style="font-size:14px; text-align:left;">Rp.</th>
                                  <th width= "15%" style="font-size:14px; text-align:right;">' . ($dividenprive >= 0 ? number_format($dividenprive, 0, ",", ".") : "(" . number_format($dividenprive * -1, 0, ",", ".") . ")") . '</th>
                                  <th width= "10%" style="font-size:14px; text-align:left;"></th>
                                  <th width= "15%" style="font-size:14px; text-align:left;"></th>
                                 </tr>
                                 ';

                      //penambah pengurang ekuitas
                      echo '<tr>
                                  <th width= "50%" style="font-size:14px; text-align:left;">' . str_repeat('&nbsp;', 10) . 'Penambahan/Pengurangan Ekuitas</th>
                                  <th width= "10%" style="font-size:14px; text-align:left;">Rp.</th>
                                  <th width= "15%" style="font-size:14px; text-align:right;">' . ($penambahpengurangekuitas >= 0 ? number_format($penambahpengurangekuitas, 0, ",", ".") : "(" . number_format($penambahpengurangekuitas * -1, 0, ",", ".") . ")") . '</th>
                                  <th width= "10%" style="font-size:14px; text-align:left;"></th>
                                  <th width= "15%" style="font-size:14px; text-align:left;"></th>
                                 </tr>
                                 ';


                      // Penarikan / Deviden
                      $deviden = $this->db->query('
                                              SELECT SUM(kredit)-SUM(debet) as deviden FROM v_jurnaldetail 
                                              WHERE idperusahaan ="' . $idperusahaan . '" and  LEFT(kdakun,5) IN ("32110") and tgljurnal between "' . $tglawal . '" and "' . $tglakhir . '";
                                              ')->getRow()->deviden;
                      $deviden = ($deviden == '') ? 0 : $deviden;

                      echo '<tr>
                                  <th width= "50%" style="font-size:14px; text-align:left;">' . str_repeat('&nbsp;', 10) . 'Laba Ditahan</th>
                                  <th width= "10%" style="font-size:14px; text-align:left;">Rp.</th>
                                  <th width= "15%" style="font-size:14px; border-bottom:1pt solid black; text-align:right;">' . ($totallabaditahan >= 0 ? number_format($totallabaditahan, 0, ",", ".") : "(" . number_format($totallabaditahan * -1, 0, ",", ".") . ")") . '</th>
                                  <th width= "10%" style="font-size:14px; text-align:left;"></th>
                                  <th width= "15%" style="font-size:14px; text-align:left;"></th>
                                 </tr>
                                 ';

                      $totallabaditahanakhir = $totallabaditahan + $dividenprive + $penambahpengurangekuitas;
                      echo '<tr>
                                  <th width= "50%" style="font-size:14px; text-align:left;">' . str_repeat('&nbsp;', 10) . 'Laba Ditahan Akhir</th>
                                  <th width= "10%" style="font-size:14px; text-align:left;">Rp.</th>
                                  <th width= "15%" style="font-size:14px; text-align:right;">' . ($totallabaditahanakhir >= 0 ? number_format($totallabaditahanakhir, 0, ",", ".") : "(" . number_format($totallabaditahanakhir * -1, 0, ",", ".") . ")") . '</th>
                                  <th width= "10%" style="font-size:14px; text-align:left;"></th>
                                  <th width= "15%" style="font-size:14px; text-align:left;"></th>
                                 </tr>
                                 ';
                      //  laba ditahan
                      // <th width= "15%" style="font-size:14px; border-bottom:1pt solid black; text-align:right;">' . ($deviden >= 0 ? number_format($deviden, 0, ",", ".") : "(" . number_format($deviden * -1, 0, ",", ".") . ")") . '</th>

                      // Keanaikan ekuitas pemilik
                      $kenaikanekuitaspemilik = ($totalinvestasi + $totallabaditahan) + $dividenprive + $penambahpengurangekuitas;
                      echo '<tr>
                                  <th width= "50%" style="font-size:14px; text-align:left;">Kenaikan (Penurunan) Pada Ekuitas</th>
                                  <th width= "10%" style="font-size:14px; text-align:left;"></th>
                                  <th width= "15%" style="font-size:14px; text-align:right;"></th>
                                  <th width= "10%" style="font-size:14px; text-align:left;">Rp.</th>
                                  <th width= "15%" style="font-size:14px; border-bottom:1pt solid black; text-align:right;">' . ($kenaikanekuitaspemilik >= 0 ? number_format($kenaikanekuitaspemilik, 0, ",", ".") : "(" . number_format($kenaikanekuitaspemilik * -1, 0, ",", ".") . ")") . '</th>
                                 </tr>
                                 ';

                      echo '<tr><td colspan="5"></td></tr>';
                      $totalmodal = $modalawal + $kenaikanekuitaspemilik;
                      echo '<tr>
                                  <th width= "75%" style="font-size:14px; font-weight:bold; text-align:left;" colspan="3">Modal Akhir Per ' . $tglakhir_indo . '</th>
                                  <th width= "10%" style="font-size:14px; font-weight:bold; text-align:left;">Rp.</th>
                                  <th width= "15%" style="font-size:14px; font-weight:bold; text-align:right;">' . ($totalmodal >= 0 ? number_format($totalmodal, 0, ",", ".") : "(" . number_format($totalmodal * -1, 0, ",", ".") . ")") . '</th>
                                 </tr>
                                 ';

                      echo ' </tbody>
                                </table> ';
                      ?>



                  </div>
                <?php endif; ?>

              </div>
            </div>



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

    var url = "<?php echo site_url('laporan/lapperubahanekuitas-cetak/') ?>" + tglawal + "/" + tglakhir + "/" + idperusahaan;
    $(".isiKonten").html(`
        <div class="modal-header">
            <h5 class="modal-title">Buku Besar</h5>
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


  $("#namaperusahaan").autocomplete({
      minLength: 0,
      source: function(request, response) {
        $.ajax({
          type: "POST",
          url: "<?php echo site_url('Perusahaan/autocomplate'); ?>",
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
<?= $this->endSection() ?>