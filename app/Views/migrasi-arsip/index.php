<?= $this->extend('template/admin') ?>
<?= $this->section('css') ?>
<style>
  .dataTable tbody tr td {
    padding: 8px 10px !important;
  }

  .loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    z-index: 9999;
    text-align: center;
    padding-top: 200px;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="loading-overlay" id="loader" style="display: none;">
  <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
  <h4 class="mt-3 font-weight-bold text-primary">Sedang Memigrasi File ke Google Drive...</h4>
  <p id="progress-text" class="font-weight-bold">Memproses 0 dari 0 file...</p>

  <div class="progress mx-auto mb-3" style="width: 50%; height: 25px;">
    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
  </div>

  <div class="d-flex justify-content-center">
    <span class="badge badge-success px-3 py-2 mr-2" style="font-size: 14px;" id="progress-success">Berhasil: 0</span>
    <span class="badge badge-danger px-3 py-2" style="font-size: 14px;" id="progress-fail">Gagal: 0</span>
  </div>
</div>

<div class="container-fluid">
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">Migrasi File Server ke Google Drive</h6>
      <button type="button" class="btn btn-success btn-sm" id="btn-migrasi">
        <i class="fas fa-cloud-upload-alt"></i> Migrasi File Terpilih
      </button>
    </div>
    <div class="card-body">
      <!-- Filter Section -->
      <div class="row mb-4">
        <div class="col-md-4 mb-3">
          <label class="font-weight-bold">Perusahaan</label>
          <input type="hidden" id="idperusahaan" name="idperusahaan">
          <input type="text" id="tampilperusahaan" class="form-control" placeholder="Cari Perusahaan...">
        </div>

        <div class="col-md-3 mb-3">
          <label class="font-weight-bold">Status</label>
          <select class="form-control" id="filter_status">
            <option value="">Pilih Status</option>
            <option value="null">Belum Migrasi</option>
            <option value="true">Sudah Migrasi</option>
          </select>
        </div>
      </div>

      <?php if (session()->getFlashData('pesan')) echo session()->getFlashData('pesan'); ?>

      <form id="form-migrasi">
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="table" style="width: 100%;">
            <thead>
              <tr style="background-color:#055F93; color: white;">
                <th style="width: 5%; text-align: center;"><input type="checkbox" id="check-all"></th>
                <th style="text-align: center;">Nama File</th>
                <th style="text-align: center;">Ukuran</th>
                <th style="text-align: center;">Status</th>
              </tr>
            </thead>
          </table>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="modalcetakpdf" tabindex="-1" role="dialog" aria-labelledby="modalCetakLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalCetakLabel"><i class="fas fa-file-alt mr-2"></i> Viewer Dokumen Jurnal</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-0">
        <iframe id="iframe-viewer" src="" width="100%" height="600px" style="border: none;"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
<script src="<?php echo (base_url('assets/jquery-ui/jquery-ui-2.js')) ?>"></script>
<script type="text/javascript">
  var t;
  $(document).ready(function() {
    t = $('#table').DataTable({
      "select": true,
      "processing": true,
      "serverSide": true,
      "order": [],
      "pageLength": 10000,
      "ajax": {
        "url": "<?php echo site_url('migrasi-arsip/datatablesource') ?>",
        "type": "POST",
        "data": function(d) {
          d.idperusahaan = $('#idperusahaan').val();
          d.status = $('#filter_status').val(); 
        },
        // PERBAIKAN: Tambahan penangkap error DataTables
        "error": function(xhr, error, thrown) {
          console.error("DataTables Error: ", xhr.responseText);
        }
      },
      "columnDefs": [{
          "targets": [0],
          "orderable": false,
          "className": 'text-center'
        },
      ]
    });

    // 2. Trigger Reload Datatables jika Filter Diubah
    $('#filter_status').on('change', function() {
      t.draw();
    });


    // 3. Script untuk Modal Viewer
    $('#modalcetakpdf').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var urlFile = button.data('cetak_pdf');
      var modal = $(this);
      modal.find('#iframe-viewer').attr('src', urlFile);
    });

    $('#modalcetakpdf').on('hidden.bs.modal', function() {
      $(this).find('#iframe-viewer').attr('src', '');
    });

    // Check All
    $("#check-all").click(function() {
      var isChecked = $(this).is(":checked");
      $('.check-item', t.rows({ page: 'current' }).nodes()).prop('checked', isChecked);
    });

    t.on('draw.dt', function() {
      $('#check-all').prop('checked', false);
    });

    // Autocomplete Perusahaan
    $("#tampilperusahaan").autocomplete({
      minLength: 0,
      source: function(request, response) {
        $.ajax({
          type: "POST",
          url: "<?php echo site_url('migrasi-arsip/autocomplatePerusahaan'); ?>",
          dataType: "json",
          data: { term: request.term },
          success: function(data) {
            response($.map(data, function(item) {
              return {
                label: item.namaperusahaan,
                value: item.namaperusahaan,
                idperusahaan: item.idperusahaan
              };
            }));
          }
        });
      },
      select: function(event, ui) {
        $('#idperusahaan').val(ui.item.idperusahaan);
        $('#tampilperusahaan').val(ui.item.label);
        if ($.fn.DataTable.isDataTable('#table')) {
          t.draw();
        }
        return false;
      }
    }).autocomplete("instance")._renderItem = function(ul, item) {
      return $("<li>")
        .append("<div><b>" + item.label + "</b></div>")
        .appendTo(ul);
    };

    $('#tampilperusahaan').on('keyup', function() {
      if ($(this).val() === "") {
        $('#idperusahaan').val("");
        t.draw();
      }
    });

    // Proses Migrasi Ajax
    $('#btn-migrasi').click(function() {
      var $checkedItems = $(".check-item:checked");
      var totalFiles = $checkedItems.length;

      if (totalFiles === 0) {
        bootbox.alert({
          title: "Perhatian!",
          message: "Pilih minimal satu file untuk dimigrasi!",
          backdrop: true,
          centerVertical: true,
          callback: function() {
            $('body').focus(); // PERBAIKAN: Bersihkan fokus dari modal
          }
        });
        return;
      }

      bootbox.confirm({
        closeButton: false,
        message: `
        <div class="text-center p-2">
            <i class="fas fa-cloud-upload-alt text-primary mb-3" style="font-size: 65px;"></i>
            <h4 class="font-weight-bold mb-3">Konfirmasi Migrasi</h4>
            <p style="font-size: 16px; margin-bottom: 20px;">
                Anda akan memigrasi <span class="badge badge-primary px-2 py-1" style="font-size: 15px;">` + totalFiles + ` file</span> ke Google Drive.
            </p>
            <div class="alert alert-info text-left mb-0" style="border-left: 4px solid #17a2b8;">
                <i class="fas fa-info-circle mr-1"></i> 
                <small>Sistem akan memproses upload <b>satu per satu</b> untuk mencegah kegagalan jaringan atau server (Timeout).</small>
            </div>
        </div>`,
        centerVertical: true,
        buttons: {
          cancel: {
            label: '<i class="fas fa-times"></i> Batal',
            className: 'btn-outline-secondary px-4'
          },
          confirm: {
            label: '<i class="fas fa-paper-plane"></i> Ya, Migrasi Sekarang',
            className: 'btn-primary px-4'
          }
        },
        callback: function(result) {
          if (result) {
            // PERBAIKAN: Beri jeda 300ms agar bootbox hilang dari DOM sebelum mengeksekusi
            // Ini untuk mengatasi error "Blocked aria-hidden on an element"
            setTimeout(function() {
              $('body').focus(); 
              $('#loader').show();
              
              var currentIndex = 0;
              var successCount = 0;
              var failCount = 0;

              function processNextFile() {
                var percent = Math.round((currentIndex / totalFiles) * 100);
                $('#progress-text').text("Memproses " + currentIndex + " dari " + totalFiles + " file...");
                $('#progress-bar').css('width', percent + '%').text(percent + '%').attr('aria-valuenow', percent);
                $('#progress-success').text("Berhasil: " + successCount);
                $('#progress-fail').text("Gagal: " + failCount);

                if (currentIndex >= totalFiles) {
                  $('#loader').hide();

                  var pesanHasil = `
                  <div class="text-center mb-4">
                      <i class="fas fa-check-circle text-success mb-2" style="font-size: 50px;"></i>
                      <h5 class="font-weight-bold">Proses Migrasi Selesai!</h5>
                      <p class="text-muted text-sm">Berikut adalah rincian status upload file Anda:</p>
                  </div>
                  <ul class="list-group shadow-sm">
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                          <span><i class="fas fa-cloud-upload-alt text-success mr-2"></i> Berhasil Diupload</span>
                          <span class="badge badge-success badge-pill px-3 py-2" style="font-size: 14px;">` + successCount + ` File</span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                          <span><i class="fas fa-exclamation-triangle text-danger mr-2"></i> Gagal / Error</span>
                          <span class="badge badge-danger badge-pill px-3 py-2" style="font-size: 14px;">` + failCount + ` File</span>
                      </li>
                  </ul>`;

                  bootbox.alert({
                    message: pesanHasil,
                    centerVertical: true,
                    closeButton: false,
                    buttons: {
                      ok: {
                        label: '<i class="fas fa-check"></i> Tutup & Refresh Tabel',
                        className: 'btn-primary btn-block'
                      }
                    },
                    callback: function() {
                      // PERBAIKAN: Beri jeda lagi agar bootbox hilang sempurna sebelum mereload DataTables
                      setTimeout(function() {
                        $('body').focus();
                        t.draw(false); // False = refresh tapi tetap di halaman pagination saat ini
                        $("#check-all").prop("checked", false);
                      }, 300);
                    }
                  });
                  return;
                }

                var currentElement = $($checkedItems[currentIndex]);
                var dataKirim = {
                  id: currentElement.val(),
                  idperusahaan: currentElement.data('idperusahaan'),
                };

                $.ajax({
                  url: "<?php echo site_url('migrasi-arsip/proses-upload') ?>",
                  type: "POST",
                  data: dataKirim,
                  dataType: "JSON",
                  success: function(response) {
                    if (response.status) {
                      successCount++;
                    } else {
                      failCount++;
                    }
                    currentIndex++;
                    processNextFile(); // PERBAIKAN: Uncomment baris ini agar lanjut ke file berikutnya
                  },
                  error: function(xhr) {
                    console.log("Error pada file ID " + dataKirim.id + ":", xhr.responseText); // PERBAIKAN: ubah dataKirim.id jadi dataKirim.id
                    failCount++;
                    currentIndex++;
                    processNextFile(); // PERBAIKAN: Uncomment baris ini agar lanjut jika terjadi error
                  }
                });
              }

              // Mulai antrean
              processNextFile();

            }, 300); // Akhir dari setTimeout
          } else {
            $('body').focus(); // Jika batal diklik, lepaskan fokus
          }
        }
      });
    });
  });
</script>
<?= $this->endSection() ?>