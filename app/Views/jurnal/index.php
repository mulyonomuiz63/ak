<?= $this->extend('template/admin') ?>
<style>
  /* Memaksa semua kolom (td) pada baris yang error menjadi merah muda */
table.dataTable tbody tr.row-tidak-balance td {
    background-color: red !important; /* Warna merah muda pucat */
}

/* Membedakan warna sedikit saat kursor melewati baris tersebut (Hover) */
table.dataTable tbody tr.row-tidak-balance:hover td {
    background-color: red !important; 
}

/* Agar teks status badge tetap terlihat rapi */
table.dataTable tbody tr.row-tidak-balance td .badge {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Page Heading -->
  <div class="card shadow mb-4">
    <form action="<?php echo site_url('jurnal/delete-all') ?>" id="form-delete" method="post" enctype="multipart/form-data">
      <div class="card-header d-sm-flex align-items-center justify-content-between py-3">
        <div class="row">
          <?php if (session()->get('idpengguna') != '8888888888') { ?>
            <!--<h6 class="m-0 font-weight-bold text-info">Data Jurnal</h6>-->
            <div class="col-4">
              <select class="form-control" name="bulan" id="bulan">
                <option value="">Semua Bulan</option>
                <option value="1" <?= $jurnal_bulan->bulan == '1' ? 'selected' : '' ?>>Januari</option>
                <option value="2" <?= $jurnal_bulan->bulan == '2' ? 'selected' : '' ?>>Februari</option>
                <option value="3" <?= $jurnal_bulan->bulan == '3' ? 'selected' : '' ?>>Maret</option>
                <option value="4" <?= $jurnal_bulan->bulan == '4' ? 'selected' : '' ?>>April</option>
                <option value="5" <?= $jurnal_bulan->bulan == '5' ? 'selected' : '' ?>>Mei</option>
                <option value="6" <?= $jurnal_bulan->bulan == '6' ? 'selected' : '' ?>>Juni</option>
                <option value="7" <?= $jurnal_bulan->bulan == '7' ? 'selected' : '' ?>>Juli</option>
                <option value="8" <?= $jurnal_bulan->bulan == '8' ? 'selected' : '' ?>>Agustus</option>
                <option value="9" <?= $jurnal_bulan->bulan == '9' ? 'selected' : '' ?>>September</option>
                <option value="10" <?= $jurnal_bulan->bulan == '10' ? 'selected' : '' ?>>Oktober</option>
                <option value="11" <?= $jurnal_bulan->bulan == '11' ? 'selected' : '' ?>>November</option>
                <option value="12" <?= $jurnal_bulan->bulan == '12' ? 'selected' : '' ?>>Desember</option>
              </select>
            </div>
            <div class="col-3">
              <select class="form-control" name="tahun" id="tahun">
                <?php foreach ($jurnal as $rows): ?>
                  <option value="<?= $rows->tgljurnal ?>" <?= $jurnal_bulan->tahun == $rows->tgljurnal ? 'selected' : '' ?>><?= $rows->tgljurnal ?></option>
                <?php endforeach; ?>
                <option value="null">Kosong</option>
              </select>
            </div>

            <div class="col-5">
              <select class="form-control" name="status_approve" id="status_approve">
                <option class="bg-warning text-light" value="0" <?= session('status_approve') == '0' ? 'selected' : '' ?>>Menunggu</option>
                <option class="bg-success text-light" value="1" <?= session('status_approve') == '1' ? 'selected' : '' ?>>Disetujui</option>
                <option class="bg-danger text-light" value="all" <?= session('status_approve') == 'all' ? 'selected' : '' ?>>Perbaikan</option>
                <!--<option class="bg-danger text-light" value="2" <?= session('status_approve') == '2' ? 'selected' : '' ?>>Perbaikan</option>-->
              </select>
            </div>
          <?php } else { ?>
            <input type="hidden" id="idperusahaan" name="idperusahaan">
            <div class="col-4">
              <input type="text" id="tampilperusahaan" class="form-control" value="" style="width: 100%;" placeholder="Cari nama perusahaan..">
            </div>
            <div class="col-3">
              <select class="form-control" name="bulan" id="bulan">
                <option value="">Semua Bulan</option>
                <option value="1" <?= $jurnal_bulan->bulan == '1' ? 'selected' : '' ?>>Januari</option>
                <option value="2" <?= $jurnal_bulan->bulan == '2' ? 'selected' : '' ?>>Februari</option>
                <option value="3" <?= $jurnal_bulan->bulan == '3' ? 'selected' : '' ?>>Maret</option>
                <option value="4" <?= $jurnal_bulan->bulan == '4' ? 'selected' : '' ?>>April</option>
                <option value="5" <?= $jurnal_bulan->bulan == '5' ? 'selected' : '' ?>>Mei</option>
                <option value="6" <?= $jurnal_bulan->bulan == '6' ? 'selected' : '' ?>>Juni</option>
                <option value="7" <?= $jurnal_bulan->bulan == '7' ? 'selected' : '' ?>>Juli</option>
                <option value="8" <?= $jurnal_bulan->bulan == '8' ? 'selected' : '' ?>>Agustus</option>
                <option value="9" <?= $jurnal_bulan->bulan == '9' ? 'selected' : '' ?>>September</option>
                <option value="10" <?= $jurnal_bulan->bulan == '10' ? 'selected' : '' ?>>Oktober</option>
                <option value="11" <?= $jurnal_bulan->bulan == '11' ? 'selected' : '' ?>>November</option>
                <option value="12" <?= $jurnal_bulan->bulan == '12' ? 'selected' : '' ?>>Desember</option>
              </select>
            </div>
            <div class="col-2">
              <select class="form-control" name="tahun" id="tahun">
                <?php foreach ($jurnal as $rows): ?>
                  <option value="<?= $rows->tgljurnal ?>" <?= $jurnal_bulan->tahun == $rows->tgljurnal ? 'selected' : '' ?>><?= $rows->tgljurnal ?></option>
                <?php endforeach; ?>
                <option value="null">Kosong</option>
              </select>
            </div>
            <div class="col-3">
              <select class="form-control" name="status_approve" id="status_approve">
                <option value="0" <?= session('status_approve') == '0' ? 'selected' : '' ?>>Menunggu Persetujuan</option>
                <option value="1" <?= session('status_approve') == '1' ? 'selected' : '' ?>>Telah Disetujui</option>
                <option value="2" <?= session('status_approve') == '2' ? 'selected' : '' ?>>Perlu Perbaikan</option>
              </select>
            </div>
          <?php } ?>
        </div>
        <div>
          <?php if (session()->get('level_nama') != 'Supervisor'): ?>
            <?php if (session()->get('databaseHitJurnal') <= session()->get('hitJurnal')) { ?>
              <button type="button" class="btn btn-sm btn-danger tooltips" id="btn-delete" data-toggle="tooltip" data-placement="left" title="Hapus data jurnal"><i class="fa fa-trash"></i></button>
              <a href="<?php echo ('jurnal/tambah') ?>" class="btn btn-sm btn-success shadow-sm tooltips" data-toggle="tooltip" data-placement="left" title="Tambah Data Jurnal"><i class="fas fa-plus fa-lg"></i></a>
            <?php } else { ?>
              <a href="<?php echo (site_url('histori')) ?>" class="btn btn-sm btn-danger tooltips" data-toggle="tooltip" data-placement="left" title="Hapus data jurnal"><i class="fa fa-trash"></i></a>
              <a href="<?php echo (site_url('histori')) ?>" class="btn btn-sm btn-success shadow-sm tooltips" data-toggle="tooltip" data-placement="left" title="Tambah Data Jurnal"><i class="fas fa-plus fa-lg"></i></a>
            <?php } ?>
          <?php endif; ?>
        </div>
      </div>

      <div class="card-body">

        <?php
        $pesan = session()->get('pesan');
        if (!empty($pesan)) {
          echo $pesan;
        }
        ?>

        <!-- datatable -->
        <div class="table-responsive">

          <table class="table table-bordered table-striped table-condesed" id="table">
            <thead>
              <tr class="success" style="background-color:#055F93; color: white;">
                <th style="width: 3%; text-align: center;"><input type="checkbox" id="check-all"></th>
                <th style="width: 13%; text-align: center;">Tgl Jurnal</th>
                <th style="width: 13%; text-align: center;">No. Jurnal</th>
                <th style="width: 13%; text-align: center;">Referensi</th>
                <th style="text-align: center;">Keterangan</th>
                <th style="width: 10%; text-align: center;">Pembuat</th>
                <th style="width: 14%; text-align: center;">Jumlah (Rp.)</th>
                <th style="width: 5%; text-align: center;">Lamp</th>
                <th style="width: 5%; text-align: center;">Opsi</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
    </form>


  </div>
</div>

<div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">


      <table id="tampiltbody" style="width: 100%; ">

        <thead class="text-light" width="100%" style="background-color:#055F93;">

          <tr>
            <th class="text-left">Lampiran file <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button></th>

          </tr>

        </thead>

        <tbody>
        </tbody>

      </table>
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
<script src="<?php echo (base_url('assets/jquery-ui/jquery-ui-2.js')) ?>"></script>

<script type="text/javascript">
  var table;

  $(document).ready(function() {

      // 1. INISIALISASI DATATABLES (Script asli Anda)
      var t = $('#table').DataTable({
        "select": true,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
          "url": "<?php echo site_url('jurnal/datatablesource') ?>",
          "type": "POST",
          data: function(d) {
            d.cari = $('input[name=cari]').val();
            d.idperusahaan = $('input[name=idperusahaan]').val();
            d.tahun = $('#tahun').val();
            d.bulan = $('#bulan').val();
            d.status_approve = $('#status_approve').val();
          }
        },
        "columnDefs": [{
            "targets": [0],
            "orderable": false,
            "className": 'dt-body-center'
          },
          {
            "targets": [1],
            "className": 'dt-body-center'
          },
          {
            "targets": [2],
            "className": 'dt-body-center'
          },
          {
            "targets": [5],
            "className": 'dt-body-right'
          },
          {
            "targets": [6],
            "orderable": false,
            "className": 'dt-body-right'
          },
          {
            "targets": [7],
            "orderable": false,
            "className": 'dt-body-center'
          },
        ],
        "language": {
          "infoFiltered": ""
        },
        
        // =========================================================
        // TAMBAHKAN FITUR INI UNTUK MEWARNAI BARIS SECARA PAKSA
        // =========================================================
        // "rowCallback": function(row, data, displayNum, displayIndex, dataIndex) {
            
        //     // Jika baris memiliki class TIDAK BALANCE (MERAH)
        //     if ($(row).hasClass('row-tidak-balance')) {
        //         $('td', row).each(function() {
        //             this.style.setProperty('border-color', '#f5c6cb', 'important');
        //         });
        //     } 
            
        //     // Jika baris memiliki class BALANCE (HIJAU)
        //     else if ($(row).hasClass('row-balance')) {
        //         $('td', row).each(function() {
        //             this.style.setProperty('background-color', '#d4edda', 'important'); // Hijau muda
        //             this.style.setProperty('color', '#155724', 'important');            // Teks hijau gelap
        //             this.style.setProperty('border-color', '#c3e6cb', 'important');
        //         });
        //     }
            
        // }

      });

      // 2. TAMBAHKAN EVENT LISTENER KLIK BARIS DI SINI
      $('#table tbody').on('click', 'td:not(:first-child):not(:last-child)', function() {
        var tr = $(this).closest('tr');
        var row = t.row(tr);

        if (row.child.isShown()) {
          // Jika detail sudah terbuka, tutup detailnya
          row.child.hide();
          tr.removeClass('shown bg-light');
        } else {
          // Ambil idjurnal dari meta data (Pastikan Langkah 1 di PHP sudah Anda terapkan)
          var idjurnal = row.data().DT_RowData.idjurnal;

          // Tampilkan animasi loading sementara menunggu data dari server
          row.child('<div class="text-center p-3"><i class="fas fa-spinner fa-spin text-primary"></i> Mengambil detail data...</div>').show();
          tr.addClass('shown bg-light');

          // Ambil data detail menggunakan AJAX
          $.ajax({
            url: "<?php echo site_url('jurnal/get_detail_jurnal') ?>", // Pastikan nama controller sesuai
            type: "POST",
            data: {
              idjurnal: idjurnal
            },
            success: function(response) {
              // Masukkan tabel HTML yang di-return oleh PHP ke dalam child row
              row.child(response).show();
            },
            error: function() {
              row.child('<div class="p-3 text-danger">Gagal mengambil data detail, periksa koneksi.</div>').show();
            }
          });
        }
      });

    //untuk lihat semua pada kolom Keterangan
    $('#table').on('click', '.toggle-text', function() {
      let $cell = $(this).closest('td');

      let shortText = $cell.find('.text-short');
      let fullText = $cell.find('.text-full');

      if (fullText.hasClass('d-none')) {
        shortText.addClass('d-none');
        fullText.removeClass('d-none');
        $(this).text('ringkas');
      } else {
        fullText.addClass('d-none');
        shortText.removeClass('d-none');
        $(this).text('lihat semua');
      }
    });

    $('#search-form').on('keyup', function(e) {
      t.draw();
      e.preventDefault();
    });
    //end (document).ready

    $('#tahun').on('change', function() {
      t.draw();
      e.preventDefault();
    })

    $('#bulan').on('change', function() {
      t.draw();
      e.preventDefault();
    })

    $('#status_approve').on('change', function() {
      t.draw();
      e.preventDefault();
    })

    $(document).on("click", "#hapus", function(e) {
      var link = $(this).attr("href");
      e.preventDefault();
      bootbox.confirm("Anda yakin ingin menghapus data ini ?", function(result) {
        if (result) {
          document.location.href = link;
        }
      });
    });


    $(document).ready(function() {
      $("#check-all").click(function() {
        if ($(this).is(":checked"))
          $(".check-item").prop("checked", true);
        else
          $(".check-item").prop("checked", false);
      });

      $("#btn-delete").click(function(e) {
        e.preventDefault();
        if ($("#check-all").is(":checked")) {
          bootbox.confirm("Anda yakin ingin menghapus data ini ?", function(result) {
            if (result) {
              $("#form-delete").submit();
            }
          });

        } else if ($(".check-item").is(":checked")) {
          bootbox.confirm("Anda yakin ingin menghapus data ini ?", function(result) {
            if (result) {
              $("#form-delete").submit();
            }
          });
        } else {
          bootbox.confirm("silahkan pilih data yang akan di hapus?", function(result) {

          });

        }
      });
    });


    $("#tampilperusahaan").autocomplete({

      minLength: 0,

      source: function(request, response) {

        $.ajax({

          type: "POST",

          url: "<?php echo site_url('jurnal/autocomplatePerusahaan'); ?>",

          dataType: "json",

          data: {

            term: request.term

          },

          success: function(data) {
            response(data);

          }

        });

      },

      select: function(event, ui) {
        $('#idperusahaan').val(ui.item.idperusahaan)
        $('#tampilperusahaan').val(ui.item.namaperusahaan)
        reload();
        return false;

      }

    }).autocomplete("instance")._renderItem = function(ul, item) {
      return $("<li>")
        .append("<div><b>" + item.namaperusahaan + " </b></div>")

        .appendTo(ul);

    };

    function reload() {
      t.draw();
    }
  });
  $(document).on("click", "#lihatFile", function(e) {
    e.preventDefault();
    let idjurnal = $(this).data('id');
    $('#fileModal').modal('show');
    $('#tampiltbody').DataTable({
      "destroy": true,
      "select": false,
      "processing": false,
      "serverSide": false,
      "searching": false,
      "paging": false,
      "info": false,
      "order": [],
      "ajax": {
        "url": "<?php echo site_url('jurnal/get-file'); ?>",
        "type": "POST",
        data: {
          idjurnal: idjurnal
        },
      },
      "columnDefs": [{
        "targets": [0],
        "orderable": false,
        "className": 'dt-body-left'
      }, ],
      "language": {
        "infoFiltered": ""
      }

    });
  });

  $(document).on("click", "#cetak-pdf, [data-cetak_pdf]", function(e) {
    const url = $(this).data('cetak_pdf');
    $(".isiKonten").html(`
            <div class="modal-header">
                <h5 class="modal-title">Tampilan laporan</h5>
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
</script>
<?= $this->endSection() ?>