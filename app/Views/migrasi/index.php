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
<div class="loading-overlay" id="loader">
  <div class="spinner-border text-primary" role="status"></div>
  <h5 class="mt-3">Sedang Memigrasi File ke Google Drive...</h5>
  <p id="progress-text">Mohon jangan tutup halaman ini.</p>
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
        <div class="col-md-4">
          <label>Perusahaan</label>
          <input type="hidden" id="idperusahaan" name="idperusahaan">
          <input type="text" id="tampilperusahaan" class="form-control" placeholder="Cari Perusahaan...">
        </div>
        <div class="col-md-3">
          <label>Tahun</label>
          <select class="form-control" id="filter_tahun">
            <?php for ($i = date('Y'); $i >= 2020; $i--) echo "<option value='$i'>$i</option>"; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label>Bulan</label>
          <select class="form-control" id="filter_bulan">
            <option value="">Semua Bulan</option>
            <?php
            $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            foreach ($bulan as $k => $v) echo "<option value='$k'>$v</option>";
            ?>
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
                <th>Tanggal Jurnal</th>
                <th>No. Jurnal</th>
                <th>Nama File</th>
                <th style="text-align: center;">Ukuran</th>
              </tr>
            </thead>
          </table>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="<?php echo (base_url('assets/jquery-ui/jquery-ui-2.js')) ?>"></script>
<script type="text/javascript">
  var t;

  $(document).ready(function() {
    t = $('#table').DataTable({
      "processing": true,
      "serverSide": true,
      "pageLength": 50,
      "ajax": {
        "url": "<?php echo site_url('migrasi/datatablesource') ?>",
        "type": "POST",
        "data": function(d) {
          d.idperusahaan = $('#idperusahaan').val();
          d.tahun = $('#filter_tahun').val();
          d.bulan = $('#filter_bulan').val();
        },
      },
      "columnDefs": [{
          "targets": [0],
          "orderable": false,
          "className": 'text-center'
        },
        {
          "targets": [1, 2, 4],
          "className": 'text-center'
        }
      ]
    });

    // Trigger Filter
    $('#filter_tahun, #filter_bulan').on('change', function() {
      t.draw();
    });

    // Check All
    $("#check-all").click(function() {
      $(".check-item").prop("checked", $(this).is(":checked"));
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
            // Map data agar memiliki properti label dan value
            response($.map(data, function(item) {
              return {
                label: item.namaperusahaan,
                value: item.namaperusahaan,
                idperusahaan: item.idperusahaan // data tambahan
              };
            }));
          }
        });
      },
      select: function(event, ui) {
        // Menggunakan ui.item hasil map di atas
        $('#idperusahaan').val(ui.item.idperusahaan);
        $('#tampilperusahaan').val(ui.item.label);

        // Pastikan fungsi reload() sudah didefinisikan
        if (typeof reload === "function") {
          reload();
        }
        return false;
      }
    }).autocomplete("instance")._renderItem = function(ul, item) {
      return $("<li>")
        .append("<div><b>" + item.label + "</b></div>")
        .appendTo(ul);
    };

    // Proses Migrasi Ajax
    $('#btn-migrasi').click(function() {
      var $checkedItems = $(".check-item:checked");
      var selected = $checkedItems.length;

      if (selected === 0) {
        alert("Pilih minimal satu file untuk dimigrasi!");
        return;
      }

      if (confirm("Migrasi " + selected + " file ke Google Drive? File di server akan dihapus setelah sukses.")) {
        $('#loader').show();

        // 1. Buat array kosong untuk menampung data
        var dataMigrasi = [];

        // 2. Looping setiap checkbox yang ter-ceklis
        $checkedItems.each(function() {
          dataMigrasi.push({
            id: $(this).val(), // Mengambil value (ID)
            idperusahaan: $(this).data('idperusahaan'), // Mengambil data-idperusahaan
            tgljurnal: $(this).data('tgljurnal') // Mengambil data-tgljurnal
          });
        });

        $.ajax({
          url: "<?php echo site_url('migrasi/proses-upload') ?>",
          type: "POST",
          // 3. Kirim array data tersebut dengan parameter 'file_migrasi'
          data: {
            file_migrasi: dataMigrasi
          },
          dataType: "JSON",
          success: function(data) {
            $('#loader').hide();
            alert(data.msg);
            t.draw();
          },
          error: function(xhr, status, error) {
            $('#loader').hide();
            console.log(xhr.responseText); // Untuk mengecek error di console jika gagal
            alert("Terjadi kesalahan sistem.");
          }
        });
      }
    });
  });
</script>
<?= $this->endSection() ?>