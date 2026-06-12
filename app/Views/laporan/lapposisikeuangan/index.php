<?= $this->extend('template/admin') ?>
<?= $this->section('css') ?>
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
<?= $this->endSection() ?>

<!--//untuk content-->
<?= $this->section('content') ?>
<!-- Begin Page Content -->

<div class="container-fluid">



  <div class="h-100">
    <div class="row h-100 justify-content-center">

      <div class="col-md-12">

        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">LAPORAN POSISI KEUANGAN</h6>
          </div>
          <div class="card-body">

            <?php
            $pesan = session()->getFlashdata('pesan');
            if (!empty($pesan)) {
              echo $pesan;
            }
            ?>


            <form action="<?php echo (site_url('laporan/lapposisikeuangan')) ?>" class="" method="post" id="myForm">
              
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
                <label for="" class="col-md-2 col-form-label text-right mt-2">Periode Laporan : </label>
                <div class="col-md-2 mt-2">
                  <input type="date" id="tglawal" name="tglawal" class="form-control" value="<?php echo ($tglawal) ?>" readonly>
                </div>
                <label for="" class="col-md-1 col-form-label text-center mt-2">S/D</label>
                <div class="col-md-2 mt-2">
                  <input type="date" id="tglakhir" name="tglakhir" class="form-control" value="<?php echo ($tglakhir) ?>">
                </div>
                <div class="col-md-5  d-flex justify-content-start mt-2">
                  <div>
                    <button type="submit" id="searchLaporan" class="btn btn-info tooltips" data-toggle="tooltip" data-placement="left" title="Cari Laporan"><i class="fa fa-search"></i></button>
                    <a href="#" class="btn btn-secondary mr-sm-0 tooltips" data-toggle="modal" data-placement="left" title="Cetak Pdf" id="cetak" data-target="#modalcetakpdf"><i class="fa fa-print"></i></a>
                    <?php if (session()->get('hitLanggana')) { ?>
                      <a href="javascript: void(0)" class="btn btn-success tooltips" data-toggle="tooltip" data-placement="left" title="Export Excel (filter)" target="_blank" id="cetakexcel"><i class="fa fa-file-excel"></i></a>
                    <?php } else { ?>
                      <a href="<?php echo (site_url('Histori')) ?>" class="btn btn-success tooltips" data-toggle="tooltip" data-placement="left" title="Export Excel (Filter)"><i class="fa fa-file-excel"></i></a>
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
    
                        <table border="1" cellpadding="7" width="100%" style="border-color:#e3e6f0;">
                          <thead>
                            <tr style="background-color:#055F93; color: white;">
                              <th width="15%" style="font-size:16px; font-weight:bold; text-align:center;">Kode Akun</th>
                              <th width="65%" style="font-size:16px; font-weight:bold; text-align:center;">Nama Akun</th>
                              <th width="20%" style="font-size:16px; font-weight:bold; text-align:center;">Jumlah (Dalam Rp.)</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $no = 1;
                            $gt = 0;
                            $total1 = 0;
                            $totalaset = 0;
                            $totalhutangdanmodal = 0;
                            $totalasetBerjalan = 0;
                            $totalhutangdanmodalBerjalan = 0;
                            $kdakun_old = '';
                            
    
                            //total berjalan
                            foreach ($rsDataBerjalan->getResult() as $dataBerjalan) {
                                if (substr($dataBerjalan->kdakun, 0, 1) == '1' && $dataBerjalan->level == '1') {
                                    $totalasetBerjalan += $dataBerjalan->jumlah;
                                }
    
                             
                                if ((substr($dataBerjalan->kdakun, 0, 1) == '2' || substr($dataBerjalan->kdakun, 0, 1) == '3') && $dataBerjalan->level == '1') {
                                  $totalhutangdanmodalBerjalan += $dataBerjalan->jumlah;
                                }
                              
                            }
                            $spasi = '';
                            $font = '';
    
                            foreach ($rsData->getResult() as $data) {
                              $total1 += $data->jumlah;
    
    
                              switch ($data->level) {
                                case '1':
                                  $font = 'font-size: 15px; font-weight: bold;';
                                  $spasi = '';
                                  break;
                                case '2':
                                  $font = 'font-size: 15px;';
                                  $spasi = str_repeat('&nbsp;', 4);
                                  break;
                                case '3':
                                  $font = 'font-size: 15px;';
                                  $spasi = str_repeat('&nbsp;', 8);
                                  break;
                                case '4':
                                  $font = 'font-size: 15px;';
                                  $spasi = str_repeat('&nbsp;', 12);
                                  break;
                                default:
                                  $font = '';
                                  $spasi = '';
                                  break;
                              }
    
                              if (substr($data->kdakun, 0, 1) == '1' && $data->level == '1') {
                                $totalaset += $data->jumlah;
                              }
    
                             
                                if ((substr($data->kdakun, 0, 1) == '2' || substr($data->kdakun, 0, 1) == '3') && $data->level == '1') {
                                  $totalhutangdanmodal += $data->jumlah;
                                }
                             
    
                              if ((substr($kdakun_old, 0, 1) != substr($data->kdakun, 0, 1)) && $kdakun_old != '' && substr($kdakun_old, 0, 1) == '1') {
                                echo '
                                <tr height="30">
                                  <td style="border-right-color: #e6f5fe;background-color: #e6f5fe;"></td>
                                  <td style="background-color: #e6f5fe; font-size: 15px; font-weight: bold; text-align:left;">TOTAL ASET</td>
                                  <td style="background-color: #e6f5fe; font-size: 15px; font-weight: bold; text-align:right;">' . ($totalaset >= 0 ? number_format($totalaset, 0, ",", ".") : "(" . number_format($totalaset * -1, 0, ",", ".") . ")") . '</td>
                                </tr>';
                              }
    
                              if ($data->nmakun != 'LABA DITAHAN') {
                                echo '
                                <tr>
                                  <td style="' . $font . ' text-align:center;">' . $data->kdakun . '</td>
                                  <td style="' . $font . ' text-align:left;">' . $spasi . $data->nmakun . '</td>
                                  <td style="' . $font . ' text-align:right;">' . ($data->level != 1 ? ($data->jumlah >= 0 ? number_format($data->jumlah, 0, ",", ".") : "(" . number_format($data->jumlah * -1, 0, ",", ".") . ")") : '') . '</td>
                                </tr>';
                              }
                              $kdakun_old = $data->kdakun;
                            }
    
    
    
                            $totallabaditahan = ($totalaset - $totalhutangdanmodal) - ($totalasetBerjalan - $totalhutangdanmodalBerjalan);
                            echo '
                                <tr>
                                  <td style="border-right-color: #fff;background-color: #fff;"></td>
                                  <td style="font-size: 15px; font-weight: bold; text-align:left;">' . $spasi . 'LABA DITAHAN</td>
                                  <td style="font-size: 15px; font-weight: bold; text-align:right;">' . ($totallabaditahan >= 0 ? number_format($totallabaditahan, 0, ",", ".") : "(" . number_format($totallabaditahan * -1, 0, ",", ".") . ")") . '</td>
                                  
                                  </tr>';
                            $totallabarugiberjalan = $totalasetBerjalan - $totalhutangdanmodalBerjalan;
                            echo '
                                <tr style="border-right-color: #fff;background-color: #e6f5fe;">
                                  <td ></td>
                                  <td style="font-size: 15px; font-weight: bold; text-align:left;">' . $spasi . 'LABA (RUGI) PERIODE BERJALAN</td>
                                  <td style="font-size: 15px; font-weight: bold; text-align:right;">' . ($totallabarugiberjalan >= 0 ? number_format($totallabarugiberjalan, 0, ",", ".") : "(" . number_format($totallabarugiberjalan * -1, 0, ",", ".") . ")") . '</td>
                                  
                                  </tr>';
    
                            $totalliabilitasdanekuitas = $totalhutangdanmodal + $totalaset - $totalhutangdanmodal;
    
                            echo '
                                <tr>
                                  <td style="border-right-color: #e6f5fe;background-color: #e6f5fe;"></td>
                                  <td style="background-color: #e6f5fe; font-size: 15px; font-weight: bold; text-align:left;">TOTAL LIABILITAS DAN EKUITAS</td>
                                  <td style="background-color: #e6f5fe; font-size: 15px; font-weight: bold; text-align:right;">' . ($totalliabilitasdanekuitas >= 0 ? number_format($totalliabilitasdanekuitas, 0, ",", ".") : "(" . number_format($totalliabilitasdanekuitas * -1, 0, ",", ".") . ")") . '</td>
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
    var url = "<?php echo site_url('laporan/lapposisikeuangan-cetak/') ?>" + tglawal + "/" + tglakhir + "/" + akunlevel3 + "/" + idperusahaan;
    $(".isiKonten").html(`
        <div class="modal-header">
            <h5 class="modal-title">Posisi Keuangan</h5>
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

    window.open("<?php echo site_url('laporan/lapposisikeuangan-excel/') ?>" + tglawal + "/" + tglakhir + "/" + akunlevel3 + "/" + idperusahaan);
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
        $('#tglawal').val(ui.item.tglmulaiusaha);
        return false;
      },
      select: function(event, ui) {
        $('#idperusahaan').val(ui.item.idperusahaan);
        $('#namaperusahaan').val(ui.item.namaperusahaan);
        $('#tglawal').val(ui.item.tglmulaiusaha);
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