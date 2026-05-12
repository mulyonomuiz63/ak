<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">
  <!-- Page Heading -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <!-- <h6 class="m-0 font-weight-bold text-info" id="lbljudul"></h6> -->
      <div class="row">
        <div class="col-6">
            <h6 class="m-0 font-weight-bold text-info">Profile</h6>
        </div>
      </div>
    </div>
    <div class="card-body">


      <form action="<?php echo (site_url('perusahaan/store')) ?>" id="form" method="post" enctype="multipart/form-data">
        <div class="row">
          <input type="hidden" id="idperusahaan" name="idperusahaan" class="form-control" readonly="">

          <div class="col-6">
            <div class="form-group required">
              <label for="">Nama Perusahaan</label>
              <input type="text" id="namaperusahaan" name="namaperusahaan" class="form-control" placeholder="Nama Perusahaan" autofocus="">
            </div>
          </div>

          <div class="col-6">
            <div class="form-group required">
              <label for="">Alamat Perusahaan</label>
              <textarea name="alamat" id="alamat" class="form-control" rows="1" placeholder="Alamat Perusahaan"></textarea>
            </div>
          </div>

          <div class="col-6">
            <div class="form-group">
              <label for="">Nomor Telepon</label>
              <input type="text" id="notelp" name="notelp" class="form-control" placeholder="Nomor Telepon">
            </div>
          </div>

          <div class="col-6">
            <div class="form-group required">
              <label for="">Mulai Pembukuan</label>
              <input type="text" id="tglmulaiusaha" name="tglmulaiusaha" class="form-control" placeholder="Tanggal Mulai Pembukuan">
            </div>
          </div>

          <div class="col-6">
            <div class="form-group required">
              <label for="">Email <small>untuk login</small></label>
              <input type="email" id="email" name="email" onkeyup='lihatEmail()' readonly class="form-control" placeholder="Email">
              <div>
                <span id='pesan_email'></span>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="form-group required">
              <label for="">Username <small>untuk login</small></label>
              <input type="text" readonly class="form-control" id="username">
            </div>
          </div>
          <hr>
          <div class="clearfix"></div>
          <div class="col-6">
            <div class="form-group required">
              <label for="">Email Header</label> <input type="checkbox" id="checkbox"><small> Ceklist apa bila ingin menggunakan email yang sudah ada</small>
              <input type="text" id="email_pengguna" name="email_pengguna" class="form-control" placeholder="Email perusahaan">
            </div>
          </div>
          <div class="col-6">
            <div class="form-group">
              <label for="">ID Perusahaan</label>
              <a href="#" class="text-decoration-none text-dark" onclick="copyToClipboard('#idperusahaan_view')"><i class="ml-2 fas fa-solid fa-copy"></i></a>
              <div class="form-control">
                  <small id="idperusahaan_view"></small>
              </div>
            </div>
          </div>
          

          <input type="hidden" id="pengguna" name="pengguna" class="form-control" placeholder="Nama pengguna">





          <?php if (session()->get('level') == 9) : ?>
            <div class="col-6">
              <div class="form-group required">
                <label for="">Status Aktif</label>
                <select name="statusaktif" id="statusaktif" class="form-control">
                  <option value="1">Aktif</option>
                  <option value="0">Tidak Aktif</option>
                </select>
                <input type="hidden" id="tglberakhir" name="tglberakhir" class="form-control" placeholder="Tanggal Berakhir">
              </div>
            </div>




          <?php endif; ?>


        </div>
        <hr>
        <div class="clearfix"></div>
        <div class="text-right">
          <?php
          if (session()->get('idpengguna') == '8888888888') { ?>
            <a href="<?php echo (site_url('Perusahaan')) ?>" class="btn btn-danger">Kembali</a>
          <?php  } else { ?>
            <a href="<?php echo (site_url('dashboard')) ?>" class="btn btn-danger">Kembali</a>
          <?php  } ?>
          <button type="submit" id="simpan" class="btn btn-success">Simpan</button>

        </div>
      </form>

    </div>
  </div>


</div>
<!-- /.container-fluid -->

<script type="text/javascript">
  var idperusahaan = "<?php echo ($idperusahaan) ?>";

  function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
    alert("ID perusahaan telah tersalin.")
  }


  $(document).ready(function() {


    //---------------------------------------------------------> JIKA EDIT DATA
    if (idperusahaan != "") {
      //console.log(idperusahaan);
      $.ajax({
          type: 'POST',
          url: '<?php echo site_url("perusahaan/get-edit") ?>',
          data: {
            idperusahaan: idperusahaan
          },
          dataType: 'json',
          encode: true
        })
        .done(function(result) {
          $('#idperusahaan').val(result.idperusahaan);
          $('#idperusahaan_view').html(result.idperusahaan);
          $('#namaperusahaan').val(result.namaperusahaan);
          $('#username').val(result.username);
          $('#alamat').val(result.alamat);
          $('#notelp').val(result.notelp);
          $('#email').val(result.email);
          $('#tglmulaiusaha').val(result.tglmulaiusaha);
          $('#statusaktif').val(result.statusaktif);
          $('#tglberakhir').val(result.tglberakhir);
          $('#pengguna').val(result.pengguna);
          $('#email_pengguna').val(result.email_pengguna);
        }); // end ajax.done

      $('#lbljudul').html('Edit Data Perusahaan');
    } else {
      $('#lbljudul').html('Tambah Data Perusahaan');
    } //end !ltambah   



    $("#checkbox").change(function() {
      if ($(this).prop('checked')) {
        var email = $('#email').val();
        $("#email_pengguna").val(email);
      } else {
        $("#email_pengguna").val("");
      }
    });

    //----------------------------------------------------------------- > validasi dan proses simpan data disini
    $('#form').bootstrapValidator({
      feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
      },
      fields: {
        namaperusahaan: {
          validators: {
            notEmpty: {
              message: 'Nama tidak boleh kosong'
            },
            stringLength: {
              max: 100,
              message: 'Panjang Karakter maksimal 100'
            },
          }
        },
        alamat: {
          validators: {
            notEmpty: {
              message: 'Alamat akses belum dipilih'
            },
            stringLength: {
              max: 255,
              message: 'Panjang Karakter maksimal 255'
            },
          }
        },
        tglmulaiusaha: {
          validators: {
            notEmpty: {
              message: 'Tanggal mulai usaha tidak boleh kosong'
            },
          }
        },
        statusaktif: {
          validators: {
            notEmpty: {
              message: 'Status aktif belum dipilih'
            },
          }
        },
      }
    });
    //------------------------------------------------------------------------> END VALIDASI DAN SIMPAN


    $("form").attr('autocomplete', 'off');
    $('#tglmulaiusaha').datepicker({
      maxDate: new Date(),
      changeMonth: true,
      changeYear: true,
      yearRange: "-90:+00",
      maxDate: -1,
      dateFormat: "dd-mm-yy",
      autoclose: true,
      disableTouchKeyboard: true,
      Readonly: false,
    }).on('change', function(e) {
      // Revalidate the date field
      $('#form').bootstrapValidator('revalidateField', 'tglmulaiusaha');
    });
  }); //end (document).ready

  function lihatEmail() {
    $("#pesan_email").hide();
    // ambil value email dari form
    var email = $("#email").val();
    // proses pengecekan email tersedia atau tidak.
    $.ajax({
      url: "<?php echo site_url() . 'Login/cekEmail'; ?>",
      data: 'email=' + email,
      type: "POST",
      success: function(msg) {
        if (msg == 1) {
          $("#pesan_email").css("color", "#fc5d32");
          $("#pesan_email").html("Maaf Email sudah digunakan.");
          $('#email').val('');

        } else {
          $("#pesan_email").html("");

        }
        $("#pesan_email").fadeIn(1000);
      }
    });

  }
</script>
<?= $this->endSection() ?>