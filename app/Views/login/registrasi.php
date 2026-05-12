<?= $this->extend('login/template') ?>
<?= $this->section('content') ?>
<!-- Logo -->
<!-- Form Registrasi -->
<form action="<?php echo (site_url('Login/simpanregistrasi')) ?>" class="user" method="post" id="form">
    <?php
    $pesan = session()->getFlashdata('pesan');
    if (!empty($pesan)) {
      echo $pesan;
    }
    ?>
    <div class="form-group mb-2">
      <input type="text" class="form-control form-control-user" id="namaperusahaan" name="namaperusahaan" value="<?= old("namaperusahaan") ?>" placeholder="Nama Perusahaan">
    </div>
    <div class="form-group mb-2">
      <input type="text" class="form-control form-control-user" id="pengguna" name="pengguna" value="<?= old("pengguna") ?>" placeholder="Nama Pengguna">
    </div>
    <div class="form-group mb-2">
      <input type="text" class="form-control form-control-user" id="tglmulaiusaha" name="tglmulaiusaha" value="<?= old("tglmulaiusaha") ?>" placeholder="Mulai Pembukuan" autocomplete="off">
    </div>
    <div class="form-group mb-2">
      <input type="text" class="form-control form-control-user" id="email" name="email" value="<?= old("email") ?>" aria-describedby="emailHelp" placeholder="Email" autocomplete="off">
      <div class="ml-4">
        <span id='pesan_email'></span>
      </div>
    </div>
    <div class="form-group mb-2">
      <input type="text" class="form-control form-control-user" id="username" name="username" value="<?= old("username") ?>" placeholder="Username" autocomplete="off">
      <div class="ml-4">
        <span id='pesan_username'></span>
      </div>
    </div>

    <div class="form-group mb-2">
      <div class="input-group" id="password">
        <input type="password" name="password" class="form-control form-control-user" placeholder="Kata Sandi">
        <div class="input-group-append input-group-text d-flex align-items-center">
          <a href="" class=" text-decoration-none">
            <i class="far fa-eye-slash"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="form-group mb-2">
      <div class="input-group" id="password_conf">
        <input type="password" name="password2" class=" form-control form-control-user" placeholder="Ulangi Kata Sandi">
        <div class="input-group-append input-group-text d-flex align-items-center">
          <a href="" class="text-decoration-none">
            <i class="far fa-eye-slash"></i>
          </a>
        </div>
      </div>
    </div>
    <input type="hidden" name="recaptcha_token" id="recaptcha_token">
    <hr>
    <div class="form-group mb-2">
      <input type="text" id="kode_referal_tampil" class="form-control form-control-user" name="kode_referal" <?= $kode_referal == null ? '' : 'readonly' ?> value="<?= $kode_referal; ?>" placeholder="Kode Referal (Opsional)">
      <div id="suggestion-box"></div>
    </div>

    <button type="button" class="btn btn-gradient w-100" onclick="submitForm()">Registrasi</button>

</form>
<div class="mt-3 text-center">
    <a href="<?php echo (site_url('login')) ?>" class="text-decoration-none">Sudah punya akun? Masuk</a>
</div>
 <script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery/jquery.min.js')) ?>"></script>
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery-easing/jquery.easing.min.js')) ?>"></script>

  <!-- Custom scripts for all pages-->
  <script src="<?php echo (base_url('assets/sb-admin-2/js/sb-admin-2.min.js')) ?>"></script>

  <!-- Bootstrap validator -->
  <script src="<?php echo (base_url('assets/bootstrap-validator/js/bootstrapValidator.js')) ?>"></script>

  <!-- jquery-mask -->
  <script src="<?php echo (base_url('assets/jquery-ui/jquery-ui-2.js')) ?>"></script>
<script type="text/javascript">
    $("#kode_referal_tampil").autocomplete({
      appendTo: "#suggestion-box",
      source: function(request, response) {
        $.ajax({
          type: "POST",
          url: "<?php echo site_url('Login/autocomplate'); ?>",
          dataType: "json",
          data: {
            term: request.term,
          },
          success: function(data) {
            var results = $.map(data, function(v, i) {
              v = $.extend(v, {
                label: v.kode_referal + ' - ' + v.nama,
                value: v.kode_referal
              });
              return v;
            });
            response(results);

          }
        }) //ajax
      }, //source
      select: function(event, ui) {
        $('[name="label"]').val(ui.item.kode_referal);
      }
    });
    $(document).ready(function() {
      $("#password a").on('click', function(event) {
        event.preventDefault();
        if ($('#password input').attr("type") == "text") {
          $('#password input').attr('type', 'password');
          $('#password i').addClass("far fa-eye-slash");
          $('#password i').removeClass("fa fa-solid fa-eye");
        } else if ($('#password input').attr("type") == "password") {
          $('#password input').attr('type', 'text');
          $('#password i').removeClass("far fa-eye-slash");
          $('#password i').addClass("fa fa-solid fa-eye");
        }
      });

      $("#password_conf a").on('click', function(event) {
        event.preventDefault();
        if ($('#password_conf input').attr("type") == "text") {
          $('#password_conf input').attr('type', 'password');
          $('#password_conf i').addClass("far fa-eye-slash");
          $('#password_conf i').removeClass("fa fa-solid fa-eye");
        } else if ($('#password_conf input').attr("type") == "password") {
          $('#password_conf input').attr('type', 'text');
          $('#password_conf i').removeClass("far fa-eye-slash");
          $('#password_conf i').addClass("fa fa-solid fa-eye");
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
                message: 'Nama Perusahaan tidak boleh kosong'
              },
              stringLength: {
                max: 100,
                message: 'Panjang Karakter maksimal 100'
              },
            }
          },
          pengguna: {
            validators: {
              notEmpty: {
                message: 'Nama Pengguna tidak boleh kosong'
              },
              stringLength: {
                max: 100,
                message: 'Panjang Karakter maksimal 100'
              },
            }
          },
          tglmulaiusaha: {
            validators: {
              notEmpty: {
                message: 'Mulai pembukaan tidak boleh kosong'
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
              regexp: {
                regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                message: 'Harus format email @ yang valid!'
              },

            }
          },
          username: {
            validators: {
              notEmpty: {
                message: 'Username tidak boleh kosong'
              },
              stringLength: {
                max: 30,
                message: 'Panjang Karakter maksimal 50'
              },
            }
          },
          password: {
            validators: {
              notEmpty: {
                message: 'Kata sandi tidak boleh kosong'
              },
              stringLength: {
                max: 25,
                message: 'Panjang Karakter maksimal 25'
              },
            }
          },
          password2: {
            validators: {
              notEmpty: {
                message: 'Ulangi kata sandi tidak boleh kosong'
              },
              stringLength: {
                max: 25,
                message: 'Panjang Karakter maksimal 25'
              },
            }
          },
        }
      });
      //------------------------------------------------------------------------> END VALIDASI DAN SIMPAN


      // $("form").attr('autocomplete', 'off');
      // $('#tglmulaiusaha').mask('00-00-0000');

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
</script>
<script src="https://www.google.com/recaptcha/api.js?render=<?= getenv('RECAPTCHA_SITE_KEY') ?>"></script>

<script>
    function submitForm() {
        if (typeof grecaptcha === 'undefined') {
            alert('reCAPTCHA gagal dimuat');
            return;
        }
    
        grecaptcha.ready(function () {
            grecaptcha.execute('<?= getenv('RECAPTCHA_SITE_KEY') ?>', {
                action: 'registrasi'
            }).then(function (token) {
                document.getElementById('recaptcha_token').value = token;
                document.getElementById('form').submit();
            });
        });
    }
</script>
<?= $this->endSection() ?>