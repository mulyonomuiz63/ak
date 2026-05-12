<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Perusahaan</h1>
    <!--<a href="<?php echo site_url('Perusahaan') ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-chevron-left fa-sm text-white-50"></i> Kembali</a>-->
  </div>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-info" id="lbljudul"></h6>
    </div>
    <div class="card-body">


      <form action="<?php echo (site_url('Perusahaan/simpan')) ?>" id="form" method="post" enctype="multipart/form-data">
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
              <textarea name="alamat" id="alamat" class="form-control" rows="2" placeholder="Alamat Perusahaan"></textarea>
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
              <label for="">Tanggal Mulai Pembukuan</label>
              <input type="text" id="tglmulaiusaha" name="tglmulaiusaha" class="form-control" placeholder="Tanggal Mulai Pembukuan">
            </div>
          </div>

          <div class="col-6">
            <div class="form-group required">
              <label for="">Email</label>
              <input type="email" id="email" name="email" onkeyup='lihatEmail()' class="form-control" placeholder="Email">
              <div>
                <span id='pesan_email'></span>
              </div>
            </div>
          </div>

          <div class="col-6">
            <div class="form-group required">
              <label for="">Username</label>
              <input type="text" id="username" name="username" class="form-control" placeholder="Username">
            </div>
          </div>

          <div class="col-6">
            <div class="form-group required">
              <label for="">Password</label>
              <input type="password" id="password" name="password" class="form-control" placeholder="***********">
            </div>
          </div>

          <div class="col-6">
            <div class="form-group required">
              <label for="">Status Aktif</label>
              <select name="statusaktif" id="statusaktif" class="form-control">
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
              </select>
            </div>
          </div>


        </div>
        <hr>
        <div class="clearfix"></div>
        <div class="text-right">
          <a href="<?php echo (site_url('Perusahaan')) ?>" class="btn btn-danger">Kembali</a>
          <button type="submit" id="simpan" class="btn btn-success">Simpan</button>

        </div>
      </form>

    </div>
  </div>


</div>
<!-- /.container-fluid -->

<script type="text/javascript">
  //var idperusahaan = "<?php //echo ($idperusahaan) 
                        ?>";

  $(document).ready(function() {


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
              message: 'Level akses belum dipilih'
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
              message: 'Tanggal mulai tidak boleh kosong'
            },
          }
        },
        email: {
          validators: {
            notEmpty: {
              message: 'Email tidak boleh kosong'
            },
            stringLength: {
              max: 50,
              message: 'Panjang Karakter maksimal 50'
            },
          }
        },
        username: {
          validators: {
            notEmpty: {
              message: 'Username tidak boleh kosong'
            },
            stringLength: {
              min: 5,
              max: 50,
              message: 'Panjang Karakter Minimal 5 maksimal 50'
            },
          }
        },
        password: {
          validators: {
            notEmpty: {
              message: 'Password tidak boleh kosong'
            },
            stringLength: {
              min: 6,
              max: 25,
              message: 'Panjang Karakter minimal 6 maksimal 25'
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