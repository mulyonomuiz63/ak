<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Page Heading -->
  <div class="card shadow mb-4">
    <form action="<?php echo site_url('perusahaan/delete-all') ?>" id="form-delete" method="post" enctype="multipart/form-data">
      <div class="card-header d-sm-flex align-items-center justify-content-between py-3 ">
        <h6 class="m-0 font-weight-bold text-info pr-2">Data Perusahaan</h6>
        <div>
          <!-- Tambahan Tombol Export CSV -->
          <button type="button" class="btn btn-sm btn-primary tooltips mr-1" id="btn-export-modal" data-toggle="tooltip" data-placement="left" title="Export data ke CSV"><i class="fas fa-file-csv"></i> Export CSV</button>

          <?php if (session()->get('idpengguna') == '8888888888'): ?>
            <button type="button" class="btn btn-sm btn-danger tooltips" id="btn-delete" data-toggle="tooltip" data-placement="left" title="Hapus data perusahaan"><i class="fa fa-trash"></i></button>
            <a href="<?php echo ('perusahaan/tambah') ?>" class="btn btn-sm btn-success shadow-sm tooltips" data-toggle="tooltip" data-placement="left" title="Tambah data perusahaan"><i class="fas fa-plus fa-lg"></i></a>
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
                <th style="width: 5%; text-align: center;"><input type="checkbox" id="check-all"></th>
                <!--<th style="text-align: center;">Id Perusahaan</th>-->
                <th style="text-align: center;">Nama Perusahaan</th>
                <th style="text-align: center;">Tgl Pembukuan</th>
                <th style="text-align: center;">Tgl Daftar</th>
                <th style="text-align: center;">Tgl Berakhir</th>
                <th style="text-align: center;">Status</th>
                <th style="text-align: center; width: 15%;">Opsi</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
    </form>

  </div>
</div>

</div>
<!-- /.container-fluid -->

<!-- MODAL EXPORT CSV -->
<div class="modal fade" id="modalExportCSV" tabindex="-1" role="dialog" aria-labelledby="modalExportLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!-- Form mengarah ke controller export Anda -->
      <form action="<?php echo site_url('perusahaan/export-csv') ?>" method="post" id="form-export-csv">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalExportLabel"><i class="fas fa-file-csv mr-2"></i> Export Data ke CSV</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p class="mb-3">Pilih tabel data yang ingin Anda export untuk perusahaan yang dipilih:</p>

          <!-- Tempat untuk menampung ID Perusahaan yang diceklis dari tabel utama -->
          <div id="hidden-selected-perusahaan"></div>

          <div class="form-group">
            <div class="custom-control custom-checkbox mb-2">
              <input type="checkbox" class="custom-control-input" id="chk_perusahaan" checked disabled>
              <!-- Hidden input agar 'perusahaan' tetap terkirim di $_POST meski checkbox-nya disabled -->
              <input type="hidden" name="tables[]" value="perusahaan">
              <label class="custom-control-label font-weight-bold" for="chk_perusahaan">Tabel Perusahaan (Wajib)</label>
            </div>
            <div class="custom-control custom-checkbox mb-2">
              <input type="checkbox" class="custom-control-input" name="tables[]" value="pengguna" id="chk_pengguna">
              <label class="custom-control-label" for="chk_pengguna">Tabel Pengguna</label>
            </div>
            <div class="custom-control custom-checkbox mb-2">
              <input type="checkbox" class="custom-control-input" name="tables[]" value="akun" id="chk_akun">
              <label class="custom-control-label" for="chk_akun">Tabel Akun</label>
            </div>
            <div class="custom-control custom-checkbox mb-2">
              <input type="checkbox" class="custom-control-input" name="tables[]" value="jurnal" id="chk_jurnal">
              <label class="custom-control-label" for="chk_jurnal">Tabel Jurnal</label>
            </div>
            <div class="custom-control custom-checkbox mb-2">
              <input type="checkbox" class="custom-control-input" name="tables[]" value="jurnaldetail" id="chk_jurnaldetail">
              <label class="custom-control-label" for="chk_jurnaldetail">Tabel Jurnal Detail</label>
            </div>
            <div class="custom-control custom-checkbox mb-2">
              <input type="checkbox" class="custom-control-input" name="tables[]" value="jurnalfile" id="chk_jurnalfile">
              <label class="custom-control-label" for="chk_jurnalfile">Tabel Jurnal File</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div id="export-progress-area" style="display: none;" class="mt-4">
            <h6 class="font-weight-bold text-primary" id="export-status-text">Memulai proses ekspor...</h6>
            <div class="progress mb-2" style="height: 25px;">
              <div id="export-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
            <small class="text-muted" id="export-detail-text">0 / 0 baris diproses</small>
          </div>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-download"></i> Download CSV</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- /MODAL EXPORT CSV -->

<script type="text/javascript">
  var table;

  $(document).ready(function() {

    //defenisi datatable
    table = $('#table').DataTable({
      "select": true,
      "processing": true,
      "serverSide": true,
      "order": [],
      "ajax": {
        "url": "<?php echo site_url('perusahaan/datatablesource') ?>",
        "type": "POST"
      },
      "columnDefs": [{
          "targets": [0],
          "orderable": false,
          "className": 'dt-body-center'
        },
        {
          "targets": [1]
        },
        {
          "targets": [3],
          "className": 'dt-body-center'
        },
        {
          "targets": [4],
          "className": 'dt-body-center'
        },
        {
          "targets": [5],
          "className": 'dt-body-center'
        },
        {
          "targets": [6],
          "orderable": false,
          "className": 'dt-body-center'
        },
      ],
      "language": {
        "infoFiltered": ""
      }
    });

    // Handle Hapus Satuan
    $(document).on("click", "#hapus", function(e) {
      var link = $(this).attr("href");
      e.preventDefault();
      bootbox.confirm("Anda yakin ingin menghapus data ini ?", function(result) {
        if (result) {
          document.location.href = link;
        }
      });
    });

    // Handle Check All
    $("#check-all").click(function() {
      if ($(this).is(":checked"))
        $(".check-item").prop("checked", true);
      else
        $(".check-item").prop("checked", false);
    });

    // Handle Tombol Delete All (Multiple)
    $("#btn-delete").click(function(e) {
      e.preventDefault();
      if ($(".check-item:checked").length > 0) {
        bootbox.confirm("Anda yakin ingin menghapus data ini ?", function(result) {
          if (result) {
            $("#form-delete").submit();
          }
        });
      } else {
        bootbox.alert("Silahkan pilih data yang akan di hapus terlebih dahulu!");
      }
    });

    // --- SCRIPT BARU UNTUK EXPORT CSV ---
    $("#btn-export-modal").click(function(e) {
      e.preventDefault();

      var $checkedItems = $(".check-item:checked");

      if ($checkedItems.length === 0) {
        bootbox.alert({
          title: "Perhatian",
          message: "Silahkan pilih (ceklis) minimal satu perusahaan dari tabel untuk di-export datanya.",
          centerVertical: true
        });
        return;
      }

      // RESET UI MODAL AGAR KEMBALI SEPERTI SEMULA SAAT DIBUKA ULANG
      $(".form-group").show();
      $("#export-progress-area").hide();
      $("#export-progress-bar").css('width', '0%').text('0%');
      $("#export-detail-text").text('0 / 0 baris diproses');
      $("#form-export-csv button[type=submit]").prop('disabled', false);
      $("#export-status-text").text("Memulai proses ekspor...");

      $("#hidden-selected-perusahaan").empty();

      $checkedItems.each(function() {
        var idPerusahaan = $(this).val();
        $("#hidden-selected-perusahaan").append('<input type="hidden" name="idperusahaan[]" value="' + idPerusahaan + '">');
      });

      $("#modalExportCSV").modal('show');
    });
    // -------------------------------------

    $("#form-export-csv").submit(function(e) {
      e.preventDefault();

      $(".form-group").slideUp();
      $("#export-progress-area").slideDown();
      $("#form-export-csv button[type=submit]").prop('disabled', true);

      var formData = $(this).serialize();
      var limit = 5000;

      // 1. TAHAP INIT
      $.ajax({
        url: "<?php echo site_url('perusahaan/export-init') ?>",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function(resInit) {
          if (!resInit.status) {
            bootbox.alert("<h5 class='text-danger'>Error Init</h5>" + resInit.msg);
            resetUI();
            return;
          }

          var totalRows = resInit.total_rows;
          var sessionExportId = resInit.session_export_id;
          var tablesToProcess = resInit.tables;
          var totalProcessed = 0;

          // JIKA DATA KOSONG, BERHENTI DI SINI, JANGAN DOWNLOAD
          if (totalRows === 0) {
            bootbox.alert({
              title: "Data Kosong",
              message: resInit.msg,
              callback: function() {
                resetUI();
              }
            });
            return;
          }

          // 2. TAHAP PROSES (Fungsi Rekursif)
          function processChunk(tableIndex, offset) {
            if (tableIndex >= tablesToProcess.length) {
              finalizeExport(sessionExportId);
              return;
            }

            var currentTable = tablesToProcess[tableIndex];
            $('#export-status-text').text("Memproses tabel: " + currentTable.toUpperCase() + "...");

            $.ajax({
              url: "<?php echo site_url('perusahaan/export-process') ?>",
              type: "POST",
              data: formData + "&session_export_id=" + sessionExportId + "&table=" + currentTable + "&limit=" + limit + "&offset=" + offset,
              dataType: "json",
              success: function(resProcess) {
                // TANGKAP ERROR DARI TRY-CATCH PHP
                if (!resProcess.status) {
                  bootbox.alert("<h5 class='text-danger'>Error Tabel " + currentTable + "</h5>" + resProcess.msg);
                  resetUI();
                  return;
                }

                totalProcessed += resProcess.rows_processed;
                var percent = Math.round((totalProcessed / totalRows) * 100);
                $('#export-progress-bar').css('width', percent + '%').text(percent + '%');
                $('#export-detail-text').text(totalProcessed + " / " + totalRows + " baris disalin");

                if (resProcess.is_done) {
                  processChunk(tableIndex + 1, 0);
                } else {
                  processChunk(tableIndex, offset + limit);
                }
              },
              error: function(xhr) {
                // TANGKAP ERROR HTTP (misal 500 Internal Server Error)
                console.error(xhr.responseText);
                bootbox.alert("<h5 class='text-danger'>Error Server (Process)</h5>Gagal mengeksekusi data. Silakan cek console browser (F12).");
                resetUI();
              }
            });
          }

          processChunk(0, 0);
        },
        error: function(xhr) {
          // TANGKAP ERROR HTTP SAAT INIT
          console.error(xhr.responseText);
          bootbox.alert("<h5 class='text-danger'>Error Server (Init)</h5>Terjadi masalah koneksi atau query saat menghitung data. Cek console (F12).");
          resetUI();
        }
      });

      // 3. TAHAP FINALIZE
      function finalizeExport(sessionExportId) {
        $('#export-status-text').text("Mengemas file ke dalam ZIP...");

        $.ajax({
          url: "<?php echo site_url('perusahaan/export-finalize') ?>",
          type: "POST",
          data: {
            session_export_id: sessionExportId
          },
          dataType: "json",
          success: function(resFinal) {
            $('#export-status-text').text("Selesai!");
            $("#modalExportCSV").modal('hide');
            window.location.href = resFinal.download_url;
            resetUI();
          },
          error: function(xhr) {
            console.error(xhr.responseText);
            bootbox.alert("<h5 class='text-danger'>Error Server (Finalize)</h5>Gagal mengemas ZIP.");
            resetUI();
          }
        });
      }

      // Fungsi untuk mengembalikan tampilan modal jika terjadi error
      function resetUI() {
        $(".form-group").slideDown();
        $("#export-progress-area").slideUp();
        $("#form-export-csv button[type=submit]").prop('disabled', false);
      }
    });
  }); //end (document).ready
</script>
<?= $this->endSection() ?>