<?= $this->extend('template/admin') ?>
<?= $this->section('css') ?>
<style>
  .ui-autocomplete {
      z-index: 9999 !important; /* Pastikan di atas modal atau elemen tabel */
      max-height: 250px;
      overflow-y: auto;
      overflow-x: hidden;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
  }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>

<?php
// Variabel helper agar tidak menulis kondisi panjang berulang kali
$isDisabled = session()->get('level_super') == '3' ? 'disabled' : '';
$levelSuper = session()->get('level_super');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between">
      <h6 class="m-0 font-weight-bold text-info" id="lbljudul">Data Jurnal</h6>
      <div>
        <?php if ($idjurnal != '') : ?>
          <a href="javascript:void(0)" data-cetak_pdf="<?= site_url('jurnal/lihat/' . $idjurnal) ?>" class="btn btn-sm btn-secondary btn-circle tooltips" data-toggle="modal" data-placement="left" title="Cetak jurnal ke PDF" data-target="#modalcetakpdf" id="cetak-pdf">
            <i class="fa fa-print"></i>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <div class="card-body">
      <form action="<?= site_url('jurnal/store') ?>" id="form" method="post" enctype="multipart/form-data" autocomplete="off">
        <div class="row">
          <div class="col-md-12">
            <div class="card bg-light">
              <div class="card-body p-2">
                <div class="row">
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Tanggal</label>
                      <input type="text" id="tgljurnal" name="tgljurnal" class="form-control form-control-sm" <?= $isDisabled ?>>
                      <input type="hidden" id="idjurnal" name="idjurnal" readonly>
                      <input type="hidden" id="idpengguna" name="idpengguna">
                      <input type="hidden" name="kondisi" value="<?= $kondisi; ?>">
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Referensi</label>
                      <input type="text" id="referensi" name="referensi" class="form-control form-control-sm" <?= $isDisabled ?>>
                    </div>
                  </div>
                  <div class="col-md-2"></div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Keterangan</label>
                      <textarea name="keterangan" id="keterangan" class="form-control form-control-sm" rows="1" autofocus <?= $isDisabled ?>></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-12 mt-3">
          <div class="table-responsive">
            <table id="table" class="display" style="width:100%;">
              <thead class="text-light" style="background-color:#055F93;">
                <tr>
                  <th class="text-center">Akun</th>
                  <th class="text-center" style="width: 20%;">Debet (Rp.)</th>
                  <th class="text-center" style="width: 20%;">Kredit (Rp.)</th>
                  <th class="text-center" style="width: 5%;">#</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>

        <div class="col-md-12">
          <hr>
          <div class="row">
            <div class="col-md-4">
              <?php if ($levelSuper != 3) : ?>
                <span class="btn btn-sm btn-success" id="addrow"><i class="fa fa-plus"></i> Tambah Baris (F2)</span>
              <?php endif; ?>
            </div>
            <div class="col-md-8">
              <div class="row">
                <div class="col-md-6">
                  <div class="input-group input-group-sm">
                    <div class="input-group-prepend font-weight-bold">
                      <span class="input-group-text text-light" style="background-color:#055F93;">Total Debet</span>
                    </div>
                    <input type="text" class="form-control text-right font-weight-bold" name="totaldebet" id="totaldebet" readonly>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group input-group-sm">
                    <div class="input-group-prepend font-weight-bold">
                      <span class="input-group-text text-light" style="background-color:#055F93;">Total Kredit</span>
                    </div>
                    <input type="text" class="form-control text-right font-weight-bold" name="totalkredit" id="totalkredit" readonly>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 mt-4">
          <?php if (!empty(session()->get('hitFileJurnal')) && $levelSuper != 3) : ?>
            <span class="btn btn-sm btn-success" id="addrowfile"><i class="fa fa-plus"></i> Tambah Upload File</span>
          <?php endif; ?>
        </div>

        <div class="col-md-12 mt-3">
          <div class="table-responsive">
            <table id="dynamic_field" class="display" style="width:100%;">
              <tbody></tbody>
            </table>
          </div>
        </div>

        <!-- DAFTAR LAMPIRAN FILE (EDIT MODE) -->
        <?php if ($idjurnal != '') : ?>
          <div class="row">
            <?php if (empty($jurnal->filelampiran)) : ?>

              <?php if (isset($jurnalfile)) : foreach ($jurnalfile as $rows) :
                  $gdriveId = $rows->kode_file;
                  $isPdf    = (strtolower(substr($rows->file, -3)) == 'pdf');

                  // 1. JIKA MENGGUNAKAN GOOGLE DRIVE (KODE_FILE TERISI)
                  if (!empty($gdriveId)) {
                    $targetViewer = "https://drive.google.com/file/d/{$gdriveId}/preview";
              ?>
                    <div class="col-md-3 mt-2">
                      <div style="display: flex; justify-content: right;">
                        <a href="javascript:void(0)" data-cetak_pdf="<?= $targetViewer ?>" class="tooltips cetak-file-pdf" data-toggle="modal" data-placement="left" data-target="#modalcetakpdf" title="Lihat Dokumen Google Drive">
                          <div class="text-center p-3" style="width: 200px; height: 160px; border: 2px dashed #0F9D58; border-radius:10%; background-color:#f8f9fc;">
                            <i class="fab fa-google-drive text-success" style="font-size: 55px; margin-top:10px;"></i>
                            <p class="mt-3 text-dark font-weight-bold" style="font-size:12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= $rows->nama_file ?? 'Dokumen Drive' ?></p>
                          </div>
                        </a>
                        <a style="position:absolute" href="<?= site_url("jurnal/delete-file-new/{$rows->id}/{$idjurnal}/{$rows->file}") ?>" class="btn btn-danger btn_remove btn-sm mr-2 mt-2" onclick="return confirm('Yakin ingin menghapus lampiran ini?');"><i class="fa fa-trash"></i></a>
                      </div>
                    </div>

                  <?php
                    // 2. JIKA MASIH DI SERVER LOKAL (KODE_FILE KOSONG)
                  } else {
                    $targetViewer = base_url('uploads/jurnal/thumbnails/' . $rows->file);
                    $thumbImg     = $isPdf ? 'uploads/pdf.png' : 'uploads/jurnal/thumbnails/' . $rows->file;
                  ?>
                    <div class="col-md-3 mt-2">
                      <div style="display: flex; justify-content: right;">
                        <a href="javascript:void(0)" data-cetak_pdf="<?= $targetViewer ?>" class="tooltips cetak-file-pdf" data-toggle="modal" data-placement="left" data-target="#modalcetakpdf" title="Lihat Dokumen Server">
                          <div style="width: 200px; height: 160px; border: 2px solid grey; border-radius:10%; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                            <?= img_lazy($thumbImg, 'img loading', ['class' => 'rounded shadow', 'style' => 'max-width:100%; max-height:100%; object-fit:cover;']) ?>
                          </div>
                        </a>
                        <a style="position:absolute" href="<?= site_url("jurnal/delete-file-new/{$rows->id}/{$idjurnal}/{$rows->file}") ?>" class="btn btn-danger btn_remove btn-sm mr-2 mt-2" onclick="return confirm('Yakin ingin menghapus lampiran ini?');"><i class="fa fa-trash"></i></a>
                      </div>
                    </div>
                  <?php } ?>

              <?php endforeach;
              endif; ?>

              <!-- BAGIAN FILE LAMPIRAN UTAMA (FILE LAMA) -->
            <?php else : ?>

              <?php
              $gdriveIdLama = $jurnal->kode_file ?? '';

              // 1. FILE LAMA NAMUN SUDAH DI MIGRASI KE DRIVE
              if (!empty($gdriveIdLama)) {
                $targetViewerLama = "https://drive.google.com/file/d/{$gdriveIdLama}/preview";
              ?>
                <div class="col-md-3 mt-2">
                  <div style="display: flex; justify-content: right;">
                    <input type="hidden" name="file2_lama" id="file2_lama">
                    <input type="hidden" name="kode_file_lama" id="kode_file_lama">

                    <a href="javascript:void(0)" data-cetak_pdf="<?= $targetViewerLama ?>" class="tooltips cetak-file-pdf" data-toggle="modal" data-placement="left" data-target="#modalcetakpdf" title="Lihat Dokumen Google Drive">
                      <div class="text-center p-3" style="width: 200px; height: 160px; border: 2px dashed #0F9D58; border-radius:10%; background-color:#f8f9fc;">
                        <i class="fab fa-google-drive text-success" style="font-size: 55px; margin-top:10px;"></i>
                        <p class="mt-3 text-dark font-weight-bold" style="font-size:12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Lampiran Utama (Drive)</p>
                      </div>
                    </a>
                    <a style="position:absolute" href="<?= site_url("jurnal/delete-file/{$idjurnal}/{$jurnal->filelampiran}") ?>" class="btn btn-danger btn_remove btn-sm mr-2 mt-2" onclick="return confirm('Yakin ingin menghapus lampiran ini?');"><i class="fa fa-trash"></i></a>
                  </div>
                </div>

              <?php
                // 2. FILE LAMA MASIH ADA DI SERVER
              } else {
                $targetViewerLama = base_url('uploads/jurnal/' . $jurnal->filelampiran);
              ?>
                <div class="col-md-3 mt-2">
                  <div style="display: flex; justify-content: right;">
                    <input type="hidden" name="file2_lama" id="file2_lama">
                    <input type="hidden" name="kode_file_lama" id="kode_file_lama">

                    <a href="javascript:void(0)" data-cetak_pdf="<?= $targetViewerLama ?>" class="tooltips cetak-file-pdf" data-toggle="modal" data-placement="left" data-target="#modalcetakpdf" title="Lihat Dokumen Server">
                      <div style="width: 200px; height: 160px; border: 2px solid grey; border-radius:10%; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                        <?= img_lazy('uploads/jurnal/' . $jurnal->filelampiran, 'img loading', ['class' => 'rounded shadow', 'style' => 'max-width:100%; max-height:100%; object-fit:cover;']) ?>
                      </div>
                    </a>
                    <a style="position:absolute" href="<?= site_url("jurnal/delete-file/{$idjurnal}/{$jurnal->filelampiran}") ?>" class="btn btn-danger btn_remove btn-sm mr-2 mt-2" onclick="return confirm('Yakin ingin menghapus lampiran ini?');"><i class="fa fa-trash"></i></a>
                  </div>
                </div>
              <?php } ?>

            <?php endif; ?>
          </div>
        <?php endif; ?>
        <!-- END LAMPIRAN -->

        <hr>
        <div class="row">
          <div class="col-md-6 text-left">
            <span id="namapengguna"></span>
          </div>
          <div class="col-md-6 text-right">
            <?php if ($levelSuper != 3) : ?>
              <a href="javascript:window.history.go(-1);" class="btn btn-secondary">Kembali</a>
              <button type="submit" id="simpan" class="btn btn-success">Simpan</button>
            <?php endif; ?>
          </div>
        </div>
      </form>

      <!-- FORM APPROVAL STAFF (LEVEL SUPER = 3) -->
      <?php if ($levelSuper == 3) : ?>
        <form action="<?= site_url('jurnal/simpanApprove') ?>" method="post">
          <div class="mt-4 card bg-light">
            <div class="card-body p-2">
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan_approve" id="keterangan_approve" class="form-control form-control-sm" rows="3"></textarea>
                    <input type="hidden" name="idpengguna" value="<?= session()->get('idpengguna') ?>">
                    <input type="hidden" name="idjurnal" value="<?= $idjurnal ?>">
                    <input type="hidden" name="kondisi" value="<?= $kondisi ?>">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Status Approve</label>
                    <select name="status_approve" id="status_approve" class="form-control form-control-sm" required>
                      <option value="0">Pilih</option>
                      <option value="all">Disetujui & Komentar</option>
                      <option value="1">Disetujui</option>
                      <option value="2">Perbaikan</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-2 text-right">
            <div class="col-md-12">
              <a href="javascript:window.history.go(-1);" class="btn btn-secondary">Kembali</a>
              <button type="submit" class="btn btn-success">Simpan</button>
            </div>
          </div>
        </form>
      <?php else : ?>
        <span id="keterangan_approve_staff"></span>
      <?php endif; ?>

    </div>
  </div>
</div>

<!-- Modal PDF Viewer -->
<div class="modal fade" id="modalcetakpdf" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="isiKonten"></div>
    </div>
  </div>
</div>

<!-- Scripts -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>

<script type="text/javascript">
  const idjurnal = "<?= $idjurnal ?>";
  const isDisabledJs = "<?= $isDisabled ?>";

  $(document).ready(function() {
    $('#hapusFile').hide();

    $('input[type="file"]').on('change', function() {
      $(this).css('color', '#333');
    });

    // ----------------------------------------------------
    // LOGIKA EDIT DATA (AJAX Load)
    // ----------------------------------------------------
    if (idjurnal !== "") {
      $.ajax({
        type: 'POST',
        url: '<?= site_url("jurnal/get-edit") ?>',
        data: {
          idjurnal: idjurnal
        },
        dataType: 'json',
        encode: true
      }).done(function(result) {
        let isifile = (!result.filelampiran || result.filelampiran === '') ? null : result.filelampiran;

        $('#idjurnal').val(result.idjurnal);
        $('#keterangan').val(result.keterangan);
        $('#tgljurnal').val($.datepicker.formatDate("dd-mm-yy", new Date(result.tgljurnal)));

        // Set File Lama & GDrive ID
        $('#file2_lama').val(isifile);
        if (result.kode_file !== undefined) $('#kode_file_lama').val(result.kode_file);

        $('#namapengguna').html('<span class="font-weight">Dibuat Oleh: ' + result.namapengguna + '</span>');
        $('#referensi').val(result.referensi);
        $('#idpengguna').val(result.idpengguna);
        $('#status_approve').val(result.approve);
        $('#keterangan_approve').val(result.keterangan_approve);

        // Render Info Approval jika ada perbaikan
        if ((result.approve == '2' || result.approve == '1') && result.keterangan_approve != '') {
          $('#keterangan_approve_staff').html(`
                        <div class="card border-danger shadow-sm mt-4">
                            <div class="card-body">
                                <div class="media">
                                    <div class="align-self-start mr-3">
                                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:45px;height:45px;">
                                            <i class="fas fa-info"></i>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <h6 class="font-weight-bold text-danger mb-1">Informasi Penting</h6>
                                        <p class="mb-0 text-secondary">${result.keterangan_approve}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
        }

        if (isifile) {
          $('#hapusFile').show();
          $('#lblfilelama').html(`File terlampir: <a href="<?= base_url('jurnal/viewfile') ?>/${isifile}" target="_blank">${isifile}</a>`);
        }

        tambahrowfile();

        let counter = 1;
        $.each(result.RsDataDetail, function(key, value) {
          tambahrow();
          $('#keyakun' + counter).val(value['keyakun']);
          $('#nmakun' + counter).val(value['nmakun']);
          $('#debet' + counter).val(numberWithCommas(value['debet']));
          $('#kredit' + counter).val(numberWithCommas(value['kredit']));
          counter++;
        });
        hitungtotal();
      });
      $('#lbljudul').html('Edit Data Jurnal');

    } else {
      // LOGIKA TAMBAH DATA (CREATE)
      $('#lbljudul').html('Tambah Data Jurnal');
      tambahrow();
      tambahrow();
      tambahrowfile();
      $('#idpengguna').val("<?= session()->get('idpengguna') ?>");
    }

    // ----------------------------------------------------
    // VALIDASI BOOTSTRAP
    // ----------------------------------------------------
    $('#form').bootstrapValidator({
      feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
      },
      fields: {
        tgljurnal: {
          validators: {
            notEmpty: {
              message: 'Tidak boleh kosong'
            }
          }
        }
      }
    }).on('success.form.bv', function(e) {
      let td = $('#totaldebet').val();
      let tk = $('#totalkredit').val();

      if (td === '' || tk === '') {
        alert('Total debet dan kredit tidak boleh kosong!');
        e.preventDefault();
        $('#simpan').prop('disabled', false);
        return false;
      }

      if ($('#table tbody tr').length === 0) {
        alert('Tabel jurnal tidak boleh kosong!');
        e.preventDefault();
        $('#simpan').prop('disabled', false);
        return false;
      }

      if (td !== tk) {
        alert('Total debet dan Total kredit harus sama!');
        e.preventDefault();
        $('#simpan').prop('disabled', false);
        return false;
      }
    });

    // ----------------------------------------------------
    // EVENTS
    // ----------------------------------------------------
    $('#addrow').click(function() {
      tambahrow();
    });
    $('#addrowfile').click(function() {
      tambahrowfile();
    });

    $(document).on('click', '#removerow, #removerowfile', function(e) {
      e.preventDefault();
      $(this).closest('tr').remove();
      hitungtotal();
    });

    // Shortcut F2
    $(document).on('keydown', function(e) {
      if ((e.which || e.keyCode) === 113) {
        tambahrow();
        tambahrowfile();
        return false;
      }
    });

    $('#tgljurnal').datepicker({
      maxDate: new Date(),
      changeMonth: true,
      changeYear: true,
      yearRange: "-90:+00",
      dateFormat: "dd-mm-yy",
      autoclose: true,
    }).on('change', function(e) {
      $('#form').bootstrapValidator('revalidateField', 'tgljurnal');
    });

  }); // end $(document).ready

  // ----------------------------------------------------
  // FUNGSI TAMBAH BARIS AKUN (Menggunakan Backticks)
  // ----------------------------------------------------
  function tambahrow() {
    let counter = $('#table tbody tr').length + 1;
    let html = `
            <tr>
                <td>
                    <input type="text" name="nmakun[]" id="nmakun${counter}" class="form-control form-control-sm akunautocomplate" ${isDisabledJs}>
                    <input type="hidden" name="keyakun[]" id="keyakun${counter}">
                </td>
                <td><input type="text" name="debet[]" id="debet${counter}" class="form-control form-control-sm text-right" onchange="hitungtotal()" ${isDisabledJs}></td>
                <td><input type="text" name="kredit[]" id="kredit${counter}" class="form-control form-control-sm text-right" onchange="hitungtotal()" ${isDisabledJs}></td>
                <td style="text-align: center;"><a href="#" id="removerow"><i class="fa fa-trash"></i></a></td>
            </tr>
        `;

    $('#table tbody').append(html);

    // Init Autocomplete
    $(`#nmakun${counter}`).autocomplete({
      minLength: 0,
      source: function(request, response) {
        $.ajax({
          type: "POST",
          url: "<?= site_url('akun/autocomplate') ?>",
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
        $(`#keyakun${counter}`).val(ui.item.keyakun);
        $(`#nmakun${counter}`).val(ui.item.nmakun);
        return false;
      },
      select: function(event, ui) {
        let keyakun = ui.item.keyakun;
        for (let i = 1; i <= $('#table tbody tr').length; i++) {
          if ($(`#keyakun${i}`).val() !== '' && keyakun === $(`#keyakun${i}`).val() && i !== counter) {
            alert('Maaf, Akun ini sudah ada');
            $(`#keyakun${counter}`).val('');
            $(`#nmakun${counter}`).val('');
            return false;
          }
        }
        $(`#keyakun${counter}`).val(ui.item.keyakun);
        $(`#nmakun${counter}`).val(ui.item.nmakun);
        return false;
      }
    }).autocomplete("instance")._renderItem = function(ul, item) {
      return $("<li>").append(`<div><b>${item.kdakun} ${item.nmakun}</b><br>Saldo Normal : ${item.saldonormal2}</div>`).appendTo(ul);
    };

    $(`#debet${counter}, #kredit${counter}`).mask('000,000,000,000', {
      reverse: true
    });

    if (counter > 2) $(`#nmakun${counter}`).focus();
  }

  // ----------------------------------------------------
  // FUNGSI TAMBAH BARIS FILE (Menggunakan Backticks & Penambahan kode_file)
  // ----------------------------------------------------
  function tambahrowfile() {
    let hitfile = "<?= session()->get('hitFileJurnal') ?>";
    let typeFile = hitfile ? "file" : "hidden";
    let typetext = hitfile ? "text" : "hidden";
    let hapus = hitfile ? "" : "hidden";
    let counter = $('#dynamic_field tbody tr').length + 1;

    let html = `
            <tr>
                <td width="55%">
                    <input type="${typeFile}" name="file2[]" id="file2${counter}" accept="image/jpeg, image/jpg, image/png, application/pdf" class="form-control border border-0">
                </td>
                <td width="40%">
                    <input type="${typetext}" name="nama_file[]" id="nama_file${counter}" class="form-control form-control-sm akunautocomplate" placeholder="Pilih dari Arsip...">
                    <input type="hidden" name="file3[]" id="file3${counter}">
                    <input type="hidden" name="kode_file[]" id="kode_file${counter}">
                </td>
                <td style="text-align: center;"><a href="#" id="removerowfile" ${hapus}><i class="fa fa-trash"></i></a></td>
            </tr>
        `;

    $('#dynamic_field tbody').append(html);

    $(`#nama_file${counter}`).autocomplete({
      minLength: 0,
      // Tambahkan appendTo agar dropdown tidak terpengaruh overflow tabel
      appendTo: "body",
      // Logika posisi agar otomatis "Dropup" (ke atas) jika ruang di bawah sempit
      position: {
        my: "left bottom",
        at: "left top",
        collision: "flip" // Ini otomatis membalikkan posisi ke atas jika mentok di bawah
      },
      source: function(request, response) {
        $.ajax({
          type: "POST",
          url: "<?= site_url('jurnal/autocomplate') ?>",
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
        $(`#nama_file${counter}`).val(ui.item.nama_file);
        $(`#file3${counter}`).val(ui.item.file);
        return false;
      },
      select: function(event, ui) {
        $(`#nama_file${counter}`).val(ui.item.nama_file);
        $(`#file3${counter}`).val(ui.item.file);

        if (ui.item.kode_file !== undefined) {
          $(`#kode_file${counter}`).val(ui.item.kode_file);
        }
        return false;
      }
    }).autocomplete("instance")._renderItem = function(ul, item) {
      // Menambahkan class agar styling lebih mudah dikontrol jika perlu
      return $("<li>")
        .append(`<div><b>${item.nama_file} - ${item.nama_pengirim}</b></div>`)
        .appendTo(ul);
    };

    if (counter > 2) $(`#file3${counter}`).focus();
  }

  // ----------------------------------------------------
  // FUNGSI HITUNG TOTAL
  // ----------------------------------------------------
  function hitungtotal() {
    let totaldebet = 0;
    let totalkredit = 0;

    for (let i = 1; i <= $('#table tbody tr').length; i++) {
      let valDebet = untitik($(`#debet${i}`).val());
      let valKredit = untitik($(`#kredit${i}`).val());

      let debet = valDebet ? parseInt(valDebet) : 0;
      let kredit = valKredit ? parseInt(valKredit) : 0;

      totaldebet += debet;
      totalkredit += kredit;
    }

    $('#totaldebet').val(numberWithCommas(totaldebet));
    $('#totalkredit').val(numberWithCommas(totalkredit));
  }
</script>

<script>
  // ----------------------------------------------------
  // MODAL PDF & IMAGE VIEWER
  // ----------------------------------------------------
  $(document).on("click", "#cetak-pdf, .cetak-file-pdf, .cetak-jpg-pdf", function(e) {
    e.preventDefault();
    const url = $(this).data('cetak_pdf');

    $(".isiKonten").html(`
            <div class="modal-header">
                <h5 class="modal-title">Viewer Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="position: relative; height: 75vh; overflow: hidden;">
                <div id="loadingIframe" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; z-index: 10;">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="mt-3 font-weight-bold text-muted">Sedang menarik data...<br>Mohon tunggu sebentar.</div>
                </div>
                <iframe id="frameBukuBesar" src="${url}" width="100%" height="100%" style="border: none; opacity: 0; transition: opacity 0.5s; position: relative; z-index: 5;"></iframe>
            </div>
        `);

    $("#frameBukuBesar").on("load", function() {
      $("#loadingIframe").fadeOut();
      $(this).css("opacity", "1");
    });
  });
</script>

<?= $this->endSection() ?>