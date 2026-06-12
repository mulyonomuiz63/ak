<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">


  <div class="h-100">
    <div class="row h-100 justify-content-center">

      <div class="col-md-12">

        <div class="card shadow mb-4">


          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">LAPORAN JURNAL</h6>
          </div>
          <div class="card-body ">

            <?php
            $pesan = session()->getFlashdata('pesan');
            if (!empty($pesan)) {
              echo $pesan;
            }
            ?>


            <form action="<?php echo (site_url('laporan/lapjurnal')) ?>" class="" method="post">
                <?php if(session()->get('idpengguna') === '8888888888'): ?>
                  <div class="form-group row">
                    <label for="" class="col-md-2 col-form-label mt-2">Nama Perusahaan</label>
                    <div class="col-md-10">
                      <input type="text" id="namaperusahaan" name="namaperusahaan" class="form-control" placeholder="Pilih nama perusahaan" value="<?php echo ($namaperusahaan) ?>">
                      <input type="hidden" id="idperusahaan" name="idperusahaan" value="<?php echo ($idperusahaan) ?>">
                    </div>
                  </div>
                <?php else: ?>
                    <input type="hidden" id="idperusahaan" name="idperusahaan" value="<?= session()->get('idperusahaan') ?>">
                <?php endif; ?>

              <div class="form-group row">
                <label for="" class="col-md-2 col-form-label">Periode Laporan </label>
                <div class="col-md-3 mt-2">
                  <input type="date" id="tglawal" name="tglawal" class="form-control" value="<?php echo ($tglawal) ?>">
                </div>
                <label for="" class="col-md-1 col-form-label text-center mt-2">S/D</label>
                <div class="col-md-3 mt-2">
                  <input type="date" id="tglakhir" name="tglakhir" class="form-control" value="<?php echo ($tglakhir) ?>">
                </div>
                <div class="col-md-3 mt-2">
                  <button type="submit" id="searchLaporan" class="btn btn-info tooltips" data-toggle="tooltip" data-placement="left" title="Cari jurnal"><i class="fa fa-search"></i></button>
                  <a href="#" class="btn btn-secondary  tooltips" data-toggle="modal" data-placement="left" title="Cetak pdf" id="cetak" data-target="#modalcetakpdf"><i class="fa fa-print"></i></a>
                  <a href="#" class="btn btn-success  tooltips" data-toggle="tooltip" data-placement="left" title="Cetak Excel" id="cetak_excel"><i class="fa fa-file-excel"></i></a>

                </div>
              </div>

            </form>

            <div class="row mt-4">

              <div class="col-md-12">
                <hr>
                <div class="table-responsive">

                  <table border="1" cellpadding="7" width="100%" style="border-color:#e3e6f0;" id="table">
                    <thead>
                      <tr style="background-color:#055F93; color: white;">
                        <!-- <th width="5%" style="font-size:16px; font-weight:bold; text-align:center;">NO</th> -->
                        <th width="16%" style="font-size:16px; font-weight:bold; text-align:center;">Tanggal</th>
                        <th width="44%" style="font-size:16px; font-weight:bold; text-align:center;">Nama Akun</th>
                        <th width="10%" style="font-size:16px; font-weight:bold; text-align:center;">Akun</th>
                        <th width="20%" style="font-size:16px; font-weight:bold; text-align:center;">Debit</th>
                        <th width="20%" style="font-size:16px; font-weight:bold; text-align:center;">Kredit</th>
                      </tr>

                    </thead>
                    <tbody></tbody>
                  </table>
                  <div class="d-flex justify-content-center">
                    <div id="loading"></div>
                  </div>
                </div>

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
  var level = '<?php echo $level ?>';
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

    var url = "<?php echo site_url('laporan/lapjurnal-cetak/') ?>" + tglawal + "/" + tglakhir + "/" + idperusahaan;
    $(".isiKonten").html(`
        <div class="modal-header">
            <h5 class="modal-title">Jurnal Umum</h5>
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

  $('#cetak_excel').click(function() {
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

    window.open("<?php echo site_url('laporan/lapjurnal-cetak-excel/') ?>" + tglawal + "/" + tglakhir + "/" + idperusahaan);
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
  $(document).ready(function() {

    var tglawal = $("#tglawal").val();
    var tglakhir = $("#tglakhir").val();
    var idperusahaan = $("#idperusahaan").val();
    var limit = 20;
    var start = 0;
    var action = 'inactive';

    function load_data(tglawal, tglakhir, idperusahaan, limit, start) {
      $.ajax({
        url: "<?php echo base_url(); ?>/laporan/fetch",
        method: "POST",
        data: {
          tglawal,
          tglakhir,
          idperusahaan,
          limit,
          start
        },
        cache: false,
        success: function(data) {
          if (data.output != '') {
            $('#table  > tbody').append(data.output);
            $('#load_data_lap').html("");
            action = 'inactive';
            $('#loading').html('<div class="loader text-center"></div>');
          }
          if (data.output == '' || data.total == '') {
            $.ajax({
              url: "<?php echo base_url(); ?>/laporan/total-fetch",
              method: "POST",
              data: {
                tglawal,
                tglakhir,
                idperusahaan,
              },
              success: function(hasil) {
                // var $obj = $.parseJSON(hasil);
                $('#table > tbody').append(`<tr>
                                <td style="text-align:right;" colspan="3"><B>TOTAL</B></td>
                                <td style="text-align:right;"><B>${number_format(hasil[0].debet, 0, ",", ".")}</B></td>
                                <td style="text-align:right;"><B>${number_format(hasil[0].kredit, 0, ",", ".")}</B></td>
                              </tr>';
                      }`)

                $('#loading').html('');

              }

            });

            // $('#load_data_lap').html('<h6 class="text-center mt-4">Tidak ada berita lagi yang di tampilkan</h6>');
            action = 'active';
          }


        }
      })
    }

    if (action == 'inactive') {
      action = 'active';
      load_data(tglawal, tglakhir, idperusahaan, limit, start);
    }

    $("div").scroll(function() {
      if ($(this).scrollTop() + $("div").height() > $("#table").height() && action == 'inactive') {
        action = 'active';
        start = start + limit;
        setTimeout(function() {
          load_data(tglawal, tglakhir, idperusahaan, limit, start);
        }, 1000);
      }
    });


  });
</script>
<?= $this->endSection() ?>