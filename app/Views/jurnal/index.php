<?= $this->extend('template/admin') ?>
<style>
  /* Memaksa semua kolom (td) pada baris yang error menjadi merah muda */
  table.dataTable tbody tr.row-tidak-balance td {
    background-color: red !important;
    /* Warna merah muda pucat */
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
                    <option value="<?= $val ?>" <?= $jurnal_bulan->bulan == $val ? 'selected' : '' ?>><?= $nama ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="<?= $isAdmin ? 'col-md-2' : 'col-md-3' ?> col-sm-6 mb-2 mb-md-0">
                <select class="form-control" name="tahun" id="tahun">
                  <?php foreach ($jurnal as $rows): ?>
                    <option value="<?= $rows->tgljurnal ?>" <?= $jurnal_bulan->tahun == $rows->tgljurnal ? 'selected' : '' ?>><?= $rows->tgljurnal ?></option>
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
    $('#fiskal_objek').on('change', function() {
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Pastikan Anda sudah meload library SweetAlert2 di header/footer Anda

  $(document).ready(function() {

    // Helper 1: Format angka ke Rupiah (Ribuan)
    function formatRupiah(angka) {
      if (!angka || angka == 0) return '0';
      // Format menggunakan locale id-ID agar otomatis pakai pemisah titik
      return parseFloat(angka).toLocaleString('id-ID');
    }

    // Helper 2: Mapping Enum ke Text biasa untuk tampilan
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

    // Helper 3: Konfigurasi Toast SweetAlert2
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

    // Fungsi untuk mengecek logika disable/enable inputan
    function applyLogic(tr) {
      let valObjek = tr.find('.inp-objek').val();
      let inpObjekPajak = tr.find('.inp-objekpajak');

      if (valObjek === '0') {
        inpObjekPajak.prop('readonly', true).val('0');
      } else {
        inpObjekPajak.prop('readonly', false);
      }

      let valFiskal = tr.find('.inp-fiskal').val();
      let inpKorPos = tr.find('.inp-korpos');
      let inpKorNeg = tr.find('.inp-korneg');

      if (valFiskal === '0') {
        inpKorPos.prop('readonly', true).val('0');
        inpKorNeg.prop('readonly', true).val('0');
      } else {
        inpKorPos.prop('readonly', false);
        inpKorNeg.prop('readonly', false);
      }
    }

    // Aksi ketika tombol Edit diklik
    $(document).on('click', '.btn-edit-inline', function() {
      let tr = $(this).closest('tr');

      if (tr.hasClass('is-editing')) return;
      tr.addClass('is-editing');

      let id = tr.data('id');
      let objek = tr.data('objek');
      let objekPajak = tr.data('objekpajak');
      let fiskal = tr.data('fiskal');
      let korPos = tr.data('korpos');
      let korNeg = tr.data('korneg');
      let ket = tr.data('ket');

      tr.find('td:eq(0)').html(`<button type="button" class="btn btn-success btn-save-inline" style="padding: 2px 6px; font-size: 10px; line-height: 1.2;" title=""><i class="fas fa-check"></i></button>`);

      tr.find('.td-objek').html(`
            <select class="form-control form-control-sm inp-objek" style="width: 80px;">
                <option value="0" ${objek == '0' ? 'selected' : ''}>Pilih</option>
                <option value="1" ${objek == '1' ? 'selected' : ''}>PPh Psl 21</option>
                <option value="2" ${objek == '2' ? 'selected' : ''}>PPh Psl 23</option>
                <option value="3" ${objek == '3' ? 'selected' : ''}>PPh Psl 4 ayat 2</option>
            </select>
        `);

      tr.find('.td-fiskal').html(`
            <select class="form-control form-control-sm inp-fiskal" style="width: 60px;">
                <option value="0" ${fiskal == '0' ? 'selected' : ''}>Tidak</option>
                <option value="1" ${fiskal == '1' ? 'selected' : ''}>Ya</option>
            </select>
        `);

      tr.find('.td-objekpajak').html(`<input type="number" class="form-control form-control-sm text-right inp-objekpajak" value="${objekPajak}" style="width: 80px;">`);
      tr.find('.td-korpos').html(`<input type="number" class="form-control form-control-sm text-right inp-korpos" value="${korPos}" style="width: 80px;">`);
      tr.find('.td-korneg').html(`<input type="number" class="form-control form-control-sm text-right inp-korneg" value="${korNeg}" style="width: 80px;">`);
      tr.find('.td-ket').html(`<input type="text" class="form-control form-control-sm inp-ket" value="${ket}" style="width: 100px;">`);

      applyLogic(tr);
    });

    $(document).on('change', '.inp-objek, .inp-fiskal', function() {
      let tr = $(this).closest('tr');
      applyLogic(tr);
    });

    // Aksi ketika tombol Simpan diklik
    $(document).on('click', '.btn-save-inline', function() {
      let tr = $(this).closest('tr');
      let iddetailjurnal = tr.data('id');

      // Tangkap nilai-nilai baru dari inputan
      let valObjek = tr.find('.inp-objek').val();
      let valObjekPajak = tr.find('.inp-objekpajak').val() || 0;
      let valFiskal = tr.find('.inp-fiskal').val();
      let valKorPos = tr.find('.inp-korpos').val() || 0;
      let valKorNeg = tr.find('.inp-korneg').val() || 0;
      let valKet = tr.find('.inp-ket').val();

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
          // Gunakan SweetAlert jika ada, kalau tidak fallback ke alert biasa
          if (typeof Swal !== 'undefined') {
            Swal.fire('Perhatian', 'Jika menggunakan Fiskal, minimal salah satu koreksi (Positif/Negatif) harus diisi!', 'warning');
          } else {
            alert('Jika menggunakan Fiskal, minimal salah satu koreksi (Positif/Negatif) harus diisi!');
          }
          return;
        }
      }

      // Tampilkan indikator loading pada tombol
      $(this).html('<i class="fas fa-spinner fa-spin"></i>');

      $.ajax({
        url: '<?= base_url("jurnal/simpan-fiskal") ?>',
        type: 'POST',
        data: dataToSave,
        dataType: 'json',
        success: function(response) {
          if (response.status) {

            // 1. Tampilkan Toast Sukses
            if (typeof Swal !== 'undefined') {
              Toast.fire({
                icon: 'success',
                title: 'Data berhasil diperbarui'
              });
            } else {
              alert('Data berhasil disimpan!'); // Fallback jika Swal tidak ada
            }

            // 2. Update data-* atribut di baris <tr> agar kalau diedit lagi, datanya sudah yang terbaru
            tr.data('objek', valObjek);
            tr.data('objekpajak', valObjekPajak);
            tr.data('fiskal', valFiskal);
            tr.data('korpos', valKorPos);
            tr.data('korneg', valKorNeg);
            tr.data('ket', valKet);

            // 3. Kembalikan inputan menjadi teks biasa + format angka jadi Rupiah
            tr.find('.td-objek').html(textObjek[valObjek]);
            tr.find('.td-objekpajak').html(formatRupiah(valObjekPajak));
            tr.find('.td-fiskal').html(textFiskal[valFiskal]);
            tr.find('.td-korpos').html(formatRupiah(valKorPos));
            tr.find('.td-korneg').html(formatRupiah(valKorNeg));
            tr.find('.td-ket').text(valKet);

            // 4. Kembalikan tombol Simpan menjadi tombol Edit (Kuning, ukuran kecil)
            tr.find('td:eq(0)').html(`<button type="button" class="btn btn-warning btn-edit-inline" style="padding: 2px 6px; font-size: 10px; line-height: 1.2;" title=""><i class="fas fa-edit"></i></button>`);

            // 5. Lepas status editing dari baris tersebut
            tr.removeClass('is-editing');

          } else {
            if (typeof Swal !== 'undefined') Toast.fire({
              icon: 'error',
              title: 'Gagal menyimpan data'
            });
            tr.find('.btn-save-inline').html('<i class="fas fa-check"></i>');
          }
        },
        error: function() {
          if (typeof Swal !== 'undefined') Toast.fire({
            icon: 'error',
            title: 'Terjadi kesalahan server'
          });
          tr.find('.btn-save-inline').html('<i class="fas fa-check"></i>');
        }
      });
    });
  });

  $(document).ready(function() {
    // Event delegation untuk tombol copy (karena elemen diload dari datatable/ajax)
    $(document).on('click', '.btn-copy-idjurnal', function(e) {
      e.preventDefault();

      // Ambil nilai ID Jurnal dari atribut data-id
      var idToCopy = $(this).data('id');

      // Proses copy ke clipboard menggunakan API navigator terbaru (jika didukung) atau execCommand (fallback)
      var tempInput = $("<input>");
      $("body").append(tempInput);
      tempInput.val(idToCopy).select();
      document.execCommand("copy");
      tempInput.remove();

      // Tampilkan notifikasi Toast (menggunakan SweetAlert2 jika ada)
      if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end', // Pojok kanan atas
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        });

        Toast.fire({
          icon: 'success',
          title: 'ID Jurnal ' + idToCopy + ' disalin!'
        });
      } else {
        // Fallback alert biasa jika SweetAlert2 tidak ditemukan di template Anda
        alert('ID Jurnal ' + idToCopy + ' berhasil disalin!');
      }
    });
  });
</script>
<?= $this->endSection() ?>