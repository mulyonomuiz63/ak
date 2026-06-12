<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="h-100">
        <div class="row justify-content-center">

            <div class="col-md-12">

                <div class="card shadow mb-4">


                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-info">LAPORAN RASIO KEUANGAN</h6>
                    </div>
                    <div class="card-body ">

                        <?php
                        $pesan = session()->getFlashdata('pesan');
                        if (!empty($pesan)) {
                            echo $pesan;
                        }
                        ?>


                        <form action="<?php echo (site_url('laporan/laprasio')) ?>" class="" method="post">
                            <?php if(session()->get('idpengguna') === '8888888888'): ?>
                                <div class="form-group row">
                                    <label for="" class="col-md-2 col-form-label">Nama Perusahaan</label>
                                    <div class="col-md-10">
                                      <input type="text" id="namaperusahaan" name="namaperusahaan" class="form-control" placeholder="Pilih nama perusahaan" value="<?= $namaperusahaan ?>">
                                      <input type="hidden" id="idperusahaan" name="idperusahaan" value="<?= $idperusahaan ?>">
                                    </div>
                                </div>
                            <?php else: ?>
                                <input type="hidden" id="idperusahaan" name="idperusahaan" value="<?= session()->get('idperusahaan') ?>">
                            <?php endif; ?>

                            <div class="form-group row">
                                <label for="" class="col-md-2 col-form-label mt-2">Periode Laporan </label>
                                <div class="col-md-3 mt-2">
                                    <select name="bulan" id="bulan" class="form-control">
                                        <option value="">Pilih bulan</option>
                                        <option value="01" <?= $bulan == "01" ? "selected" : ''; ?>>Januari</option>
                                        <option value="02" <?= $bulan == "02" ? "selected" : ''; ?>>Februari</option>
                                        <option value="03" <?= $bulan == "03" ? "selected" : ''; ?>>Maret</option>
                                        <option value="04" <?= $bulan == "04" ? "selected" : ''; ?>>April</option>
                                        <option value="05" <?= $bulan == "05" ? "selected" : ''; ?>>Mei</option>
                                        <option value="06" <?= $bulan == "06" ? "selected" : ''; ?>>Juni</option>
                                        <option value="07" <?= $bulan == "07" ? "selected" : ''; ?>>Juli</option>
                                        <option value="08" <?= $bulan == "08" ? "selected" : ''; ?>>Agustus</option>
                                        <option value="09" <?= $bulan == "09" ? "selected" : ''; ?>>September</option>
                                        <option value="10" <?= $bulan == "10" ? "selected" : ''; ?>>Oktober</option>
                                        <option value="11" <?= $bulan == "11" ? "selected" : ''; ?>>November</option>
                                        <option value="12" <?= $bulan == "12" ? "selected" : ''; ?>>Desember</option>
                                    </select>
                                </div>
                                <div class="col-md-3  mt-2 mr-2">
                                    <select name="tahun" id="tahun" class="form-control">
                                        <option value="">Pilih tahun</option>
                                        <?php
                                        $tglawalTahun = date('Y', strtotime($tglawal));
                                        $now = date('Y');

                                        for ($a = $tglawalTahun; $a <= $now; $a++) { ?>
                                            <option value="<?= $a; ?>" <?= $tahun == $a ? "selected" : ''; ?>><?= $a; ?></option>
                                        <?php }
                                        ?>
                                    </select>
                                    <!-- <input type="date" id="tglawal" name="tglawal" class="form-control" value="<?php echo ($tglawal) ?>" readonly> -->
                                </div>

                                <div class="col-md-3 mt-2">
                                    <button type="submit" id="searchLaporan" class="btn btn-info tooltips" data-toggle="tooltip" data-placement="left" title="Cari laporan"><i class="fa fa-search"></i></button>
                                    <a href="#" class="btn btn-secondary tooltips" data-toggle="modal" data-placement="left" title="Cetak pdf" id="cetak" data-target="#modalcetakpdf"><i class="fa fa-print"></i></a>
                                    <?php if (session()->get('hitLanggana')) { ?>
                                        <a href="javascript: void(0)" class="btn btn-success tooltips" data-toggle="tooltip" data-placement="left" title="Export excel (filter)" target="_blank" id="cetakexcel"><i class="fa fa-file-excel"></i></a>
                                    <?php } else { ?>
                                        <a href="<?php echo (site_url('Histori')) ?>" class="btn btn-success tooltips" data-toggle="tooltip" data-placement="left" title="Export excel (filter)"><i class="fa fa-file-excel"></i></a>
                                    <?php } ?>

                                </div>
                            </div>

                        </form>

                        <div class="row mt-4">

                            <div class="col-md-12">
                                <hr>
                                <?php if(!empty($idperusahaan)): ?>
                                    <div class="table-responsive">
    
                                        <table border="1" cellpadding="7" width="100%" style="border-color:#e3e6f0;" id="table">
                                            <thead>
                                                <tr style="background-color:#055F93; color: white;">
                                                    <th width="30%" style="font-size:17px; font-weight:bold; text-align:center;">Analisis Rasio Keuangan</th>
                                                    <th width="60%" style="font-size:17px; font-weight:bold; text-align:center;">Penjelasan</th>
                                                    <th width="10%" style="font-size:17px; font-weight:bold; text-align:center;">Hasil</th>
                                                </tr>
    
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">RASIO PROFITABILITAS</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-italic" style="padding-left:30px">Gross Profit Margin</td>
                                                    <td>Mengukur efisiensi perusahaan dalam menghasilkan laba kotor dari penjualan.</td>
                                                    <td class="text-center"><?= $data["total_1"] != "-0" ? $data["total_1"] : 0; ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-italic" style="padding-left:30px">Operating Profit Margin</td>
                                                    <td>Menunjukkan kemampuan perusahaan menghasilkan laba dari operasi.</td>
                                                    <td class="text-center"><?= $data["total_2"] != "-0" ? $data["total_2"] : 0; ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-italic" style="padding-left:30px">Net Profit Margin</td>
                                                    <td>Mengukur persentase laba bersih dari setiap penjualan.</td>
                                                    <td class="text-center"><?= $data["total_3"] != "-0" ? $data["total_3"] : 0; ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-italic" style="padding-left:30px">Return On Asset (ROA)</td>
                                                    <td>Menilai efektivitas perusahaan dalam menghasilkan laba dari asetnya.</td>
                                                    <td class="text-center"><?= $data["total_4"] != "-0" ? $data["total_4"] : 0; ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-italic" style="padding-left:30px">Return On Equity (ROE)</td>
                                                    <td>Mengukur tingkat pengembalian investasi bagi pemegang saham.</td>
                                                    <td class="text-center"><?= $data["total_5"] != "-0" ? $data["total_5"] : 0; ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-italic" style="padding-left:30px">Return On Investment (ROI)</td>
                                                    <td>Mengevaluasi efisiensi investasi dalam menghasilkan keuntungan.</td>
                                                    <td class="text-center"><?= $data["total_6"] != "-0" ? $data["total_6"] : 0; ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">RASIO LIKUIDITAS</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-italic" style="padding-left:30px">Current Ratio</td>
                                                    <td>Mengukur kemampuan perusahaan membayar kewajiban jangka pendek.</td>
                                                    <td class="text-center"><?= $data["total_7"] != "-0" ? $data["total_7"] : 0; ?>%</td>
                                                </tr>
                                                <!--<tr>-->
                                                <!--    <td class="font-italic" style="padding-left:30px">Quick Ratio</td>-->
                                                <!--    <td>Menilai likuiditas perusahaan tanpa mengandalkan persediaan.</td>-->
                                                <!--    <td class="text-center"><?= $data["total_8"] != "-0" ? $data["total_8"] : 0; ?>%</td>-->
                                                <!--</tr>-->
                                                <tr>
                                                    <td class="font-italic" style="padding-left:30px">Cash Ratio</td>
                                                    <td>Mengukur kemampuan kas bank untuk melunasi utang jangka pendek.</td>
                                                    <td class="text-center"><?= $data["total_9"] != "-0" ? $data["total_9"] : 0; ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">RASIO LEVERAGE</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-italic" style="padding-left:30px">Total Debt Asset Ratio (DAR)</td>
                                                    <td>Mengukur proporsi aset yang dibiayai oleh utang.</td>
                                                    <td class="text-center"><?= $data["total_10"] != "-0" ? $data["total_10"] : 0;  ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-italic" style="padding-left:30px">Total Debt to Total Equity (DER)</td>
                                                    <td>Menunjukkan perbandingan utang dengan ekuitas pemegang saham.</td>
                                                    <td class="text-center"><?= $data["total_11"] != "-0" ? $data["total_11"] : 0; ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="font-weight-bold">RASIO AKTIVITAS</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-italic" style="padding-left:30px">Total Asset Turnover</td>
                                                    <td>Mengukur efisiensi penggunaan seluruh aset dalam menghasilkan penjualan.</td>
                                                    <td class="text-center"><?= $data["total_12"] != "-0" ? $data["total_12"] : 0; ?>%</td>
                                                </tr>
                                                <!-- <tr>
                                                    <td colspan="3" class="font-weight-bold">RASIO MANAJEMEN PAJAK</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-italic" style="padding-left:30px">ETR</td>
                                                    <td>Mengukur beban pajak yang sebenarnya dibayar oleh perusahaan dibandingkan dengan laba sebelum pajak</td>
                                                    <td class="text-center"><?= $data["total_13"] != "-0" ? $data["total_13"] : 0; ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-italic" style="padding-left:30px">STR - ETR</td>
                                                    <td>Menilai efesiensi perusahaan dalam mengelola pajak</td>
                                                    <td class="text-center"><?= $data["total_14"] != "-0" ? $data["total_14"] : 0; ?>%</td>
                                                </tr> -->
                                            </tbody>
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
    $('#cetak').click(function() {
        var bulan = $('#bulan').val() == "" ? "all" : $('#bulan').val();
        var tahun = $('#tahun').val() == "" ? "all" : $('#tahun').val();
        var url = "<?php echo site_url('laporan/laprasio-cetak/') ?>" + bulan + "/" + tahun;
            $(".isiKonten").html(`
            <div class="modal-header">
                <h5 class="modal-title">Rasio Keuangan</h5>
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
        var bulan = $('#bulan').val() == "" ? "all" : $('#bulan').val();
        var tahun = $('#tahun').val() == "" ? "all" : $('#tahun').val();

        window.open("<?php echo site_url('laporan/laprasio-excel/') ?>" + bulan + "/" + tahun);
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
<?= $this->endSection() ?>