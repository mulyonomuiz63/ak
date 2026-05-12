<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">



  <div class="h-100">
    <div class="row h-100 justify-content-center">

      <div class="col-md-12">

        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">BUKU BESAR</h6>
          </div>
          <div class="card-body">

            <?php
            $pesan = session()->getFlashdata('pesan');
            if (!empty($pesan)) {
              echo $pesan;
            }
            ?>


            <form action="<?php echo (site_url('laporan/lapbukubesar')) ?>" class="" method="post">
              <?php if (session()->get('idpengguna') === '8888888888'): ?>
                <div class="form-group row">
                  <label for="" class="col-md-2 col-form-label text-right">Nama Perusahaan : </label>
                  <div class="col-md-10">
                    <input type="text" id="namaperusahaan" name="namaperusahaan" class="form-control" placeholder="Pilih nama perusahaan" value="<?php echo ($namaperusahaan) ?>">
                    <input type="hidden" id="idperusahaan" name="idperusahaan" value="<?php echo ($idperusahaan) ?>">
                  </div>
                </div>
              <?php else: ?>
                <input type="hidden" id="idperusahaan" name="idperusahaan" value="<?= session()->get('idperusahaan') ?>">
              <?php endif; ?>

              <div class="row form-group">
                <label for="" class="col-md-2 col-form-label text-right">Pilih Akun : </label>
                <div class="col-md-10">
                  <input type="text" id="nmakun" name="nmakun" class="form-control mr-sm-2" placeholder="Ketik Nama Akun" value="<?php echo ($nmakun) ?>">
                  <input type="hidden" id="kdakun" name="kdakun" value="<?php echo ($kdakun) ?>">
                  <input type="hidden" id="keyakun" name="keyakun" value="<?php echo ($keyakun) ?>">
                </div>
              </div>

              <div class="row form-group">
                <label for="" class="col-md-2 col-form-label text-right mt-2">Periode Laporan : </label>
                <div class="col-md-10">
                  <div class="form-inline">
                    <input type="date" id="tglawal" name="tglawal" class="form-control mr-sm-2 mt-2" value="<?php echo ($tglawal) ?>">

                    <label for="" class="mr-sm-2 mt-2">S/D</label>
                    <input type="date" id="tglakhir" name="tglakhir" class="form-control mr-sm-2 mt-2" value="<?php echo ($tglakhir) ?>">
                    <button type="submit" id="searchLaporan" class="btn btn-info mr-sm-2 mt-2  tooltips" data-toggle="tooltip" data-placement="left" title="Cari akun"><i class="fa fa-search"></i></button>
                    <a href="javascript: void(0)" class="btn btn-secondary mr-sm-2 mt-2 tooltips" data-toggle="modal" data-placement="left" title="Cetak Pdf" id="cetak" data-target="#modalcetakpdf"><i class="fa fa-print"></i></a>
                    <a href="javascript: void(0)" class="btn btn-danger mr-sm-2 mt-2 tooltips" data-toggle="modal" data-placement="left" title="Cetak Pdf (All)" id="cetak_semua" data-target="#modalcetakpdf"><i class="fa fa-print"></i></a>
                    <?php if (session()->get('hitLanggana')) { ?>
                      <a href="javascript: void(0)" class="btn btn-success mr-sm-2 mt-2 tooltips" data-toggle="tooltip" data-placement="left" title="Export Excel (Filter)" target="_blank" id="cetakexcel" title="Export"><i class="fa fa-file-excel"></i></a>
                      <a href="javascript: void(0)" class="btn btn-danger mt-2 tooltips" data-toggle="tooltip" data-placement="left" title="Export Excel (All)" target="_blank" id="cetakexcel-all" title="Export Semua"><i class="fa fa-file-excel"></i></a>
                    <?php } else { ?>
                      <a href="<?php echo (site_url('Histori')) ?>" class="btn btn-success mr-sm-2 mt-2" title="Export"><i class="fa fa-file-excel"></i> </a>
                      <a href="<?php echo (site_url('Histori')) ?>" class="btn btn-danger mt-2" title="Export Semua"><i class="fa fa-file-excel"></i></a>
                    <?php } ?>
                  </div>

                </div>

              </div>

            </form>

            <div class="row mt-4">
              <div class="col-md-12">
                <hr>
                <?php if (!empty($idperusahaan)): ?>
                  <?php
                  echo '<div style="text-align:left; font-size:16px; font-weight:bold;">Akun: ' . $kdakun . ' - ' . $nmakun . '</div>';
                  ?>

                  <div class="table-responsive">
                    <table border="1" width="100%" class="" cellpadding="7" style="border-color:#e3e6f0;" id="table">
                      <thead class="">
                        <tr class=" text-light" style="background-color:#055F93;">
                          <!-- <th width="5%" style="font-size:16px; font-weight:bold; text-align:center;">NO</th> -->
                          <th width="10%" style="font-size:14px; font-weight:bold; text-align:center;">Tanggal</th>
                          <th width="10%" style="font-size:14px; font-weight:bold; text-align:center;">No Jurnal</th>
                          <th width="10%" style="font-size:14px; font-weight:bold; text-align:center;">Referensi</th>
                          <th width="25%" style="font-size:14px; font-weight:bold; text-align:center;">Keterangan</th>
                          <th width="11%" style="font-size:14px; font-weight:bold; text-align:center;">Debet</th>
                          <th width="11%" style="font-size:14px; font-weight:bold; text-align:center;">Kredit</th>
                          <th width="11%" style="font-size:14px; font-weight:bold; text-align:center;">Saldo</th>
                          <th width="9%" style="font-size:14px; font-weight:bold; text-align:center;">Status</th>
                          <th width="3%" style="font-size:14px; font-weight:bold; text-align:center;">Opsi</th>
                        </tr>
                      </thead>

                      <tbody></tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                      <div id="loading"></div>
                    </div>

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
    var kdakun = $('#kdakun').val();
    var keyakun = $('#keyakun').val();
    var nmakun = $('#nmakun').val();
    var idperusahaan = $('#idperusahaan').val();

    if (keyakun === '' || keyakun === 'Tidak Ada') {
      bootbox.alert('Pilih nama akun!');
      return;
    }

    if (tglawal === '' || tglakhir === '') {
      bootbox.alert('Pilih Periode!');
      return;
    }

    if (idpengguna == '8888888888' && idperusahaan === '') {
      bootbox.alert('Pilih nama perusahaan!');
      return;
    }

    var url = "<?php echo site_url('laporan/lapbukubesar-cetak/') ?>" + tglawal + "/" + tglakhir + "/" + keyakun + "/" + idperusahaan + "/" + nmakun;
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

  $('#cetak_semua').click(function() {
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

    var url = "<?php echo site_url('laporan/lapbukubesar-cetak-semua/') ?>" + tglawal + "/" + tglakhir + "/" + idperusahaan;

    // 1. Masukkan struktur HTML (termasuk spinner loading dan iframe)
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


  $('#cetakexcel').click(function() {
    var tglawal = $('#tglawal').val();
    var tglakhir = $('#tglakhir').val();
    var kdakun = $('#kdakun').val();
    var keyakun = $('#keyakun').val();
    var idperusahaan = $('#idperusahaan').val();
    var nmakun = $('#nmakun').val();

    if (keyakun === '' || keyakun === 'Tidak Ada') {
      bootbox.alert('Pilih nama akun!');
      return;
    }

    if (tglawal === '' || tglakhir === '') {
      bootbox.alert('Pilih Periode!');
      return;
    }

    if (idpengguna == '8888888888' && idperusahaan === '') {
      bootbox.alert('Pilih nama perusahaan!');
      return;
    }

    window.open("<?php echo site_url('laporan/lapbukubesar-excel/') ?>" + tglawal + "/" + tglakhir + "/" + keyakun + "/" + idperusahaan + "/" + nmakun);
    location.reload();
  });

  $('#cetakexcel-all').click(function() {
    var tglawal = $('#tglawal').val();
    var tglakhir = $('#tglakhir').val();
    //var kdakun      = $('#kdakun').val();
    //var keyakun      = $('#keyakun').val();
    var idperusahaan = $('#idperusahaan').val();


    if (tglawal === '' || tglakhir === '') {
      bootbox.alert('Pilih Periode!');
      return;
    }

    if (idpengguna == '8888888888' && idperusahaan === '') {
      bootbox.alert('Pilih nama perusahaan!');
      return;
    }

    window.open("<?php echo site_url('laporan/lapbukubesar-excel-semua/') ?>" + tglawal + "/" + tglakhir + "/" + idperusahaan);
    location.reload();
  });


  $("#nmakun").autocomplete({
      minLength: 0,
      source: function(request, response) {
        var idperusahaan = $('#idperusahaan').val();
        $.ajax({
          type: "POST",
          url: "<?php echo site_url('akun/autocomplate'); ?>",
          dataType: "json",
          data: {
            term: request.term,
            idperusahaan
          },
          success: function(data) {
            response(data);
          }
        });
      },
      focus: function(event, ui) {
        $('#keyakun').val(ui.item.keyakun);
        $('#kdakun').val(ui.item.kdakun);
        $('#nmakun').val(ui.item.nmakun);
        return false;
      },
      select: function(event, ui) {
        $('#keyakun').val(ui.item.keyakun);
        $('#kdakun').val(ui.item.kdakun);
        $('#nmakun').val(ui.item.nmakun);
        return false;
      }
    })
    .autocomplete("instance")._renderItem = function(ul, item) {
      return $("<li>")
        .append("<div><b>" + item.kdakun + " " + item.nmakun + "</b></div>")
        .appendTo(ul);
    };


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
  $(document).ready(function() {



    function load_data(tglawal, tglakhir, kdakun, keyakun, nmakun, idperusahaan, namaperusahaan, limit, start) {

      $.ajax({
        url: "<?php echo base_url(); ?>/laporan/fetchLapbukuBesar",
        method: "POST",
        data: {
          tglawal,
          tglakhir,
          idperusahaan,
          namaperusahaan,
          kdakun,
          keyakun,
          nmakun,
          limit,
          start
        },
        cache: false,
        success: function(data) {
          if (data.output != '') {
            $('#table  > tbody').append(data.output);
            $('#load_data_lap').html("");
            $('#loading').html('<div class="loader text-center"></div>');
            action = 'inactive';
          }
          if (data.output == '' || data.total == '') {
            $.ajax({
              url: "<?php echo base_url(); ?>/laporan/totalFetchBukubesar",
              method: "POST",
              data: {
                tglawal,
                tglakhir,
                idperusahaan,
                kdakun
              },
              success: function(hasil) {
                $('#table > tbody').append(`<tr>
                                  <td style="font-size:12px;text-align:right;" colspan="4"><B>TOTAL</B></td>
                                 <td style="font-size:12px; text-align:right;"><B>${number_format(hasil[0].debet, 0, ",", ".")}</B></td>
                                  <td style="font-size:12px; text-align:right;"><B>${number_format(hasil[0].kredit , 0, ",", ".")}</B></td>
                                  <td></td>
                                </tr>';
                        }`)
                $('#loading').html('');
              }


            });
            action = 'active';
          }
        }
      })
    }

    var tglawal = $("#tglawal").val();
    var tglakhir = $("#tglakhir").val();
    var idperusahaan = $("#idperusahaan").val();
    var namaperusahaan = $("#namaperusahaan").val();
    var kdakun = $("#kdakun").val();
    var keyakun = $("#keyakun").val();
    var nmakun = $("#nmakun").val();
    var limit = 200;
    var start = 0;
    var action = 'inactive';
    if (action == 'inactive') {
      action = 'active';
      load_data(tglawal, tglakhir, kdakun, keyakun, nmakun, idperusahaan, namaperusahaan, limit, start);
    }

    $("div").scroll(function() {
      if ($(this).scrollTop() + $("div").height() > $("#table").height() && action == 'inactive') {
        action = 'active';
        start = start + limit;
        setTimeout(function() {
          load_data(tglawal, tglakhir, kdakun, keyakun, nmakun, idperusahaan, namaperusahaan, limit, start);
        }, 1000);
      }
    });


  });

  $(document).on("click", "#cetak-pdf", function(e) {
    const url = $(this).data('cetak_pdf');
    $(".isiKonten").html(`
            <div class="modal-header">
                <h5 class="modal-title">Jurnal Umum</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    x
                </button>
            </div>
                <iframe src="${url}" width="100%" height="600vh"></iframe>
            `);
  });
</script>
<?= $this->endSection() ?>