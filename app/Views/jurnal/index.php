<?= $this->extend('template/admin') ?>
<style>
  /* Memaksa semua kolom (td) pada baris yang error menjadi merah muda */
  table.dataTable tbody tr.row-tidak-balance td {
    background-color: red !important;
  }

  /* Membedakan warna sedikit saat kursor melewati baris tersebut (Hover) */
  table.dataTable tbody tr.row-tidak-balance:hover td {
    background-color: red !important;
  }

  /* Agar teks status badge tetap terlihat rapi */
  table.dataTable tbody tr.row-tidak-balance td .badge {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
</style>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<?php
// 1. Persiapan Variabel & Data untuk meringkas logika HTML
$isAdmin      = session()->get('idpengguna') == '8888888888';
$isSupervisor = session()->get('level_nama') == 'Supervisor';
$allowEdit    = session()->get('databaseHitJurnal') <= session()->get('hitJurnal');

$list_bulan = [
  1 => 'Januari',
  2 => 'Februari',
  3 => 'Maret',
  4 => 'April',
  5 => 'Mei',
  6 => 'Juni',
  7 => 'Juli',
  8 => 'Agustus',
  9 => 'September',
  10 => 'Oktober',
  11 => 'November',
  12 => 'Desember'
];
?>

<div class="container-fluid">
  <div class="card shadow mb-4">
    <form action="<?= site_url('jurnal/delete-all') ?>" id="form-delete" method="post" enctype="multipart/form-data">

      <div class="card-header bg-white py-3">
        <div class="row align-items-center">

          <div class="col-md-10">
            <div class="row">

              <?php if ($isAdmin): ?>
                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                  <input type="hidden" id="idperusahaan" name="idperusahaan">
                  <input type="text" id="tampilperusahaan" class="form-control" placeholder="Cari nama perusahaan..">
                </div>
              <?php endif; ?>

              <div class="<?= $isAdmin ? 'col-md-2' : 'col-md-3' ?> col-sm-6 mb-2 mb-md-0">
                <select class="form-control" name="bulan" id="bulan">
                  <option value="">Semua Bulan</option>
                  <?php foreach ($list_bulan as $val => $nama): ?>
                    <option value="<?= $val ?>" <?= (isset($jurnal_bulan) && $jurnal_bulan->bulan == $val) ? 'selected' : '' ?>><?= $nama ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="<?= $isAdmin ? 'col-md-2' : 'col-md-3' ?> col-sm-6 mb-2 mb-md-0">
                <select class="form-control" name="tahun" id="tahun">
                  <?php foreach ($jurnal as $rows): ?>
                    <option value="<?= $rows->tgljurnal ?>" <?= (isset($jurnal_bulan) && $jurnal_bulan->tahun == $rows->tgljurnal) ? 'selected' : '' ?>><?= $rows->tgljurnal ?></option>
                  <?php endforeach; ?>
                  <option value="null">Kosong</option>
                </select>
              </div>

              <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                <select class="form-control" name="status_approve" id="status_approve">
                  <option class="bg-warning text-dark" value="0" <?= session('status_approve') == '0' ? 'selected' : '' ?>>Menunggu Persetujuan</option>
                  <option class="bg-success text-white" value="1" <?= session('status_approve') == '1' ? 'selected' : '' ?>>Telah Disetujui</option>
                  <option class="bg-danger text-white" value="all" <?= session('status_approve') == 'all' ? 'selected' : '' ?>>Perlu Perbaikan</option>
                </select>
              </div>

              <div class="<?= $isAdmin ? 'col-md-2' : 'col-md-3' ?> col-sm-6 mb-2 mb-md-0">
                <select class="form-control" name="fiskal_objek" id="fiskal_objek">
                  <option value="">Pilih Objek Fiskal</option>
                  <option value="objek">Khusus Objek & Fiskal</option>
                </select>
              </div>

            </div>
          </div>

          <div class="col-md-2 text-md-right text-left mt-3 mt-md-0">
            <?php if (!$isSupervisor): ?>
              <?php
              // Tentukan link berdasarkan limit hitJurnal
              $link_delete = $allowEdit ? 'button' : site_url('histori');
              $link_add    = $allowEdit ? site_url('jurnal/tambah') : site_url('histori');
              ?>

              <?php if ($allowEdit): ?>
                <button type="button" class="btn btn-sm btn-danger shadow-sm tooltips" id="btn-delete" data-toggle="tooltip" data-placement="top" title="Hapus Data Jurnal">
                  <i class="fa fa-trash"></i>
                </button>
              <?php else: ?>
                <a href="<?= $link_delete ?>" class="btn btn-sm btn-danger shadow-sm tooltips" data-toggle="tooltip" data-placement="top" title="Hapus Data Jurnal">
                  <i class="fa fa-trash"></i>
                </a>
              <?php endif; ?>

              <a href="<?= $link_add ?>" class="btn btn-sm btn-success shadow-sm tooltips" data-toggle="tooltip" data-placement="top" title="Tambah Data Jurnal">
                <i class="fas fa-plus fa-lg"></i>
              </a>
            <?php endif; ?>
          </div>

        </div>
      </div>

      <div class="card-body">

        <?php if (session()->get('pesan')): ?>
          <?= session()->get('pesan') ?>
        <?php endif; ?>

        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover" id="table">
            <thead style="background-color: #055F93; color: white;">
              <tr>
                <th class="text-center" style="width: 3%;"><input type="checkbox" id="check-all"></th>
                <th class="text-center" style="width: 13%;">Tgl Jurnal</th>
                <th class="text-center" style="width: 13%;">No. Jurnal</th>
                <th class="text-center" style="width: 13%;">Referensi</th>
                <th class="text-center">Keterangan</th>
                <th class="text-center" style="width: 10%;">Pembuat</th>
                <th class="text-center" style="width: 14%;">Jumlah (Rp.)</th>
                <th class="text-center" style="width: 5%;">Lamp</th>
                <th class="text-center" style="width: 5%;">Opsi</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>

      </div>
    </form>
  </div>
</div>

<!-- Modal File Lampiran -->
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

<!--modal cetak pdf-->
<div class="modal fade" id="modalcetakpdf" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="isiKonten"></div>
    </div>
  </div>
</div>
<!--modal cetak pdf-->

<!-- Modal Form Edit Fiskal & Objek Pajak -->
<div class="modal fade" id="modalEditFiskal" tabindex="-1" aria-labelledby="modalEditFiskalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #055F93;">
        <h5 class="modal-title" id="modalEditFiskalLabel"><i class="fas fa-edit"></i> Edit Rincian Jurnal</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit_iddetailjurnal">

        <!-- INFORMASI AKUN, DEBET, KREDIT -->
        <div class="p-3 mb-4 rounded border" style="background-color: #f8f9fa;">
          <div class="row text-sm">
            <div class="col-12 mb-2 border-bottom pb-2">
              <span class="text-muted font-weight-bold"><i class="fas fa-book"></i> Akun:</span><br>
              <strong class="text-dark" id="info_akun" style="font-size: 1.1em;">-</strong>
            </div>
            <div class="col-6">
              <span class="text-muted font-weight-bold">Debet:</span><br>
              <strong class="text-success" id="info_debet" style="font-size: 1.1em;">-</strong>
            </div>
            <div class="col-6">
              <span class="text-muted font-weight-bold">Kredit:</span><br>
              <strong class="text-danger" id="info_kredit" style="font-size: 1.1em;">-</strong>
            </div>
          </div>
        </div>
        <!-- END INFORMASI -->

        <div class="form-group row align-items-center">
          <label class="col-sm-4 col-form-label font-weight-bold">Objek Pajak</label>
          <div class="col-sm-8">
            <select class="form-control" id="edit_objek">
              <option value="0">Pilih</option>
              <option value="1">PPh Psl 21</option>
              <option value="2">PPh Psl 23</option>
              <option value="3">PPh Psl 4 ayat 2</option>
            </select>
          </div>
        </div>
        <div class="form-group row align-items-center">
          <label class="col-sm-4 col-form-label font-weight-bold">Nominal Objek</label>
          <div class="col-sm-8">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="number" class="form-control text-right" id="edit_objekpajak">
            </div>
          </div>
        </div>
        <hr>
        <div class="form-group row align-items-center">
          <label class="col-sm-4 col-form-label font-weight-bold">Fiskal</label>
          <div class="col-sm-8">
            <select class="form-control" id="edit_fiskal">
              <option value="0">Tidak</option>
              <option value="1">Ya</option>
            </select>
          </div>
        </div>
        <div class="form-group row align-items-center">
          <label class="col-sm-4 col-form-label font-weight-bold">Kor. Positif</label>
          <div class="col-sm-8">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="number" class="form-control text-right" id="edit_korpos">
            </div>
          </div>
        </div>
        <div class="form-group row align-items-center">
          <label class="col-sm-4 col-form-label font-weight-bold">Kor. Negatif</label>
          <div class="col-sm-8">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="number" class="form-control text-right" id="edit_korneg">
            </div>
          </div>
        </div>
        <hr>
        <div class="form-group row align-items-center">
          <label class="col-sm-4 col-form-label font-weight-bold">Keterangan</label>
          <div class="col-sm-8">
            <textarea class="form-control" id="edit_ket" rows="2" placeholder="Masukkan keterangan tambahan..."></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
        <button type="button" class="btn btn-success" id="btnSaveModalFiskal"><i class="fas fa-save"></i> Simpan Data</button>
      </div>
    </div>
  </div>
</div>
<!-- End Modal Edit Fiskal -->

<!-- /.container-fluid -->
<script src="<?php echo (base_url('assets/jquery-ui/jquery-ui-2.js')) ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
  var table;

  $(document).ready(function() {

    // 1. INISIALISASI DATATABLES
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
          d.fiskal_objek = $('#fiskal_objek').val();
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
    });

    // 2. EVENT LISTENER KLIK BARIS (CHILD ROW JURNAL)
    $('#table tbody').on('click', 'td:not(:first-child):not(:last-child)', function() {
      var tr = $(this).closest('tr');
      var row = t.row(tr);

      if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown bg-light');
      } else {
        var idjurnal = row.data().DT_RowData.idjurnal;

        row.child('<div class="text-center p-3"><i class="fas fa-spinner fa-spin text-primary"></i> Mengambil detail data...</div>').show();
        tr.addClass('shown bg-light');

        $.ajax({
          url: "<?php echo site_url('jurnal/get_detail_jurnal') ?>",
          type: "POST",
          data: {
            idjurnal: idjurnal
          },
          success: function(response) {
            row.child(response).show();
          },
          error: function() {
            row.child('<div class="p-3 text-danger">Gagal mengambil data detail, periksa koneksi.</div>').show();
          }
        });
      }
    });

    // Toggle Keterangan (lihat semua/ringkas)
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

    // Event Filter DataTables
    $('#search-form').on('keyup', function(e) {
      t.draw();
      e.preventDefault();
    });

    $('#tahun, #bulan, #status_approve, #fiskal_objek').on('change', function(e) {
      t.draw();
      e.preventDefault();
    });

    // Konfirmasi hapus satuan
    $(document).on("click", "#hapus", function(e) {
      var link = $(this).attr("href");
      e.preventDefault();
      bootbox.confirm("Anda yakin ingin menghapus data ini ?", function(result) {
        if (result) {
          document.location.href = link;
        }
      });
    });

    // Hapus massal & Check all
    $("#check-all").click(function() {
      if ($(this).is(":checked"))
        $(".check-item").prop("checked", true);
      else
        $(".check-item").prop("checked", false);
    });

    $("#btn-delete").click(function(e) {
      e.preventDefault();
      if ($("#check-all").is(":checked") || $(".check-item").is(":checked")) {
        bootbox.confirm("Anda yakin ingin menghapus data ini ?", function(result) {
          if (result) {
            $("#form-delete").submit();
          }
        });
      } else {
        bootbox.confirm("Silahkan pilih data yang akan di hapus?", function(result) {});
      }
    });

    // Autocomplete Nama Perusahaan
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

  // Event modal File Lampiran
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

  // Event cetak PDF Iframe
  $(document).on("click", "#cetak-pdf, [data-cetak_pdf]", function(e) {
    const url = $(this).data('cetak_pdf');
    $(".isiKonten").html(`
            <div class="modal-header">
                <h5 class="modal-title">Tampilan laporan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body p-0" style="position: relative; height: 75vh; overflow: hidden;">
                
                <div id="loadingIframe" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; z-index: 10;">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="mt-3 font-weight-bold text-muted">Sedang menarik banyak data...<br>Mohon tunggu sebentar.</div>
                </div>

                <iframe id="frameBukuBesar" src="${url}" width="100%" height="100%" style="border: none; opacity: 0; transition: opacity 0.5s; position: relative; z-index: 5;"></iframe>
                
            </div>
        `);

    $("#frameBukuBesar").on("load", function() {
      $("#loadingIframe").fadeOut();
      $(this).css("opacity", "1");
    });
  });


  // ==============================================================
  // LOGIKA MODAL EDIT FISKAL DAN OBJEK PAJAK BESERTA INFO SUMMARY
  // ==============================================================
  $(document).ready(function() {

    function formatRupiah(angka) {
      if (!angka || angka == 0) return '0';
      return parseFloat(angka).toLocaleString('id-ID');
    }

    const textObjek = {
      '0': '-',
      '1': 'PPh Psl 21',
      '2': 'PPh Psl 23',
      '3': 'PPh Psl 4(2)'
    };
    const textFiskal = {
      '0': 'Tidak',
      '1': 'Ya'
    };

    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    });

    let activeRow = null;

    function applyLogicModal() {
      let valObjek = $('#edit_objek').val();
      let inpObjekPajak = $('#edit_objekpajak');

      if (valObjek === '0') {
        inpObjekPajak.prop('readonly', true).val('0');
      } else {
        inpObjekPajak.prop('readonly', false);
      }

      let valFiskal = $('#edit_fiskal').val();
      let inpKorPos = $('#edit_korpos');
      let inpKorNeg = $('#edit_korneg');

      if (valFiskal === '0') {
        inpKorPos.prop('readonly', true).val('0');
        inpKorNeg.prop('readonly', true).val('0');
      } else {
        inpKorPos.prop('readonly', false);
        inpKorNeg.prop('readonly', false);
      }
    }

    // Aksi ketika tombol Edit diklik (MEMBUKA MODAL & MENAMPILKAN SUMMARY INFO)
    $(document).on('click', '.btn-edit-inline', function() {
      let tr = $(this).closest('tr');
      activeRow = tr;

      // ======================================================
      // 1. Ekstrak data Akun, Debet, Kredit dari baris tabel
      // Sesuai HTML controller yang di-generate:
      // td:eq(0) = Button Edit
      // td:eq(1) = Akun
      // td:eq(2) = Debet
      // td:eq(3) = Kredit
      // ======================================================
      let namaAkun = tr.find('td:eq(1)').text().trim();
      let debet = tr.find('td:eq(2)').text().trim();
      let kredit = tr.find('td:eq(3)').text().trim();

      $('#info_akun').text(namaAkun || '-');
      $('#info_debet').text(debet || '0');
      $('#info_kredit').text(kredit || '0');

      // 2. Set value form modal dengan data-*
      $('#edit_iddetailjurnal').val(tr.data('id'));
      $('#edit_objek').val(tr.data('objek'));
      $('#edit_objekpajak').val(tr.data('objekpajak'));
      $('#edit_fiskal').val(tr.data('fiskal'));
      $('#edit_korpos').val(tr.data('korpos'));
      $('#edit_korneg').val(tr.data('korneg'));
      $('#edit_ket').val(tr.data('ket'));

      applyLogicModal();

      $('#modalEditFiskal').modal('show');
    });

    $(document).on('change', '#edit_objek, #edit_fiskal', function() {
      applyLogicModal();
    });

    // Aksi ketika tombol SIMPAN pada Modal diklik
    $(document).on('click', '#btnSaveModalFiskal', function() {

      let iddetailjurnal = $('#edit_iddetailjurnal').val();
      let valObjek = $('#edit_objek').val();
      let valObjekPajak = $('#edit_objekpajak').val() || 0;
      let valFiskal = $('#edit_fiskal').val();
      let valKorPos = $('#edit_korpos').val() || 0;
      let valKorNeg = $('#edit_korneg').val() || 0;
      let valKet = $('#edit_ket').val();

      let dataToSave = {
        iddetailjurnal: iddetailjurnal,
        objek: valObjek,
        objek_pajak: valObjekPajak,
        fiskal: valFiskal,
        koreksi_positif: valKorPos,
        koreksi_negatif: valKorNeg,
        keterangan: valKet
      };

      if (dataToSave.fiskal === '1') {
        if (parseFloat(dataToSave.koreksi_positif) === 0 && parseFloat(dataToSave.koreksi_negatif) === 0) {
          if (typeof Swal !== 'undefined') {
            Swal.fire('Perhatian', 'Jika menggunakan Fiskal, minimal salah satu koreksi (Positif/Negatif) harus diisi!', 'warning');
          } else {
            alert('Jika menggunakan Fiskal, minimal salah satu koreksi (Positif/Negatif) harus diisi!');
          }
          return;
        }
      }

      let btn = $(this);
      let originalText = btn.html();
      btn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
      btn.prop('disabled', true);

      $.ajax({
        url: '<?= base_url("jurnal/simpan-fiskal") ?>',
        type: 'POST',
        data: dataToSave,
        dataType: 'json',
        success: function(response) {
          btn.html(originalText);
          btn.prop('disabled', false);

          if (response.status) {

            if (typeof Swal !== 'undefined') {
              Toast.fire({
                icon: 'success',
                title: 'Data berhasil diperbarui'
              });
            } else {
              alert('Data berhasil disimpan!');
            }

            activeRow.data('objek', valObjek);
            activeRow.data('objekpajak', valObjekPajak);
            activeRow.data('fiskal', valFiskal);
            activeRow.data('korpos', valKorPos);
            activeRow.data('korneg', valKorNeg);
            activeRow.data('ket', valKet);

            activeRow.find('.td-objek').html(textObjek[valObjek]);
            activeRow.find('.td-objekpajak').html(formatRupiah(valObjekPajak));
            activeRow.find('.td-fiskal').html(textFiskal[valFiskal]);
            activeRow.find('.td-korpos').html(formatRupiah(valKorPos));
            activeRow.find('.td-korneg').html(formatRupiah(valKorNeg));
            // Potong string menjadi maksimal 10 karakter jika lebih dari 10
            let shortKet = valKet.length > 10 ? valKet.substring(0, 10) + '...' : valKet;
            activeRow.find('.td-ket').text(shortKet);

            $('#modalEditFiskal').modal('hide');

          } else {
            if (typeof Swal !== 'undefined') Toast.fire({
              icon: 'error',
              title: 'Gagal menyimpan data'
            });
          }
        },
        error: function() {
          btn.html(originalText);
          btn.prop('disabled', false);
          if (typeof Swal !== 'undefined') Toast.fire({
            icon: 'error',
            title: 'Terjadi kesalahan server'
          });
        }
      });
    });

    // Copy ID Jurnal ke Clipboard
    $(document).on('click', '.btn-copy-idjurnal', function(e) {
      e.preventDefault();
      var idToCopy = $(this).data('id');

      var tempInput = $("<input>");
      $("body").append(tempInput);
      tempInput.val(idToCopy).select();
      document.execCommand("copy");
      tempInput.remove();

      if (typeof Swal !== 'undefined') {
        Toast.fire({
          icon: 'success',
          title: 'ID Jurnal ' + idToCopy + ' disalin!'
        });
      } else {
        alert('ID Jurnal ' + idToCopy + ' berhasil disalin!');
      }
    });

  });
</script>
<?= $this->endSection() ?>