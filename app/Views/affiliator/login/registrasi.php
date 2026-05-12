<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Mudah bikin akun tanpa ribet!">
  <meta name="keywords" content="">
  <meta name="author" content="Akuntanmu">

  <title>Registrasi | Software Akuntansi Online Berstandar</title>

  <!-- Custom fonts for this template-->
  <link href="<?php echo (base_url('assets/sb-admin-2/vendor/fontawesome-free/css/all.min.css')) ?>" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="<?php echo (base_url('assets/sb-admin-2/css/sb-admin-2.min.css')) ?>" rel="stylesheet">
  <style>
    .has-error .help-block {
      color: red;
    }
  </style>
</head>

<body class="bg-gradient">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-md-5">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-12">
                <div class="p-5">
                  <div class="text-center mb-4">
                    <img src="../../images/logo-marketer.png" width="220">
                    <h6>REGISTRASI MARKETER</h6>
                  </div>
                  <form action=""></form>
                  <form action="<?php echo (site_url('Affiliator/Login/simpanregistrasi')) ?>" class="user" method="post" id="form">
                    <?php
                    $pesan = session()->getFlashdata('pesan');
                    if (!empty($pesan)) {
                      echo $pesan;
                    }
                    ?>

                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="email" name="email" onkeyup='lihatEmail()' aria-describedby="emailHelp" placeholder="Email" autocomplete="off">
                      <div class="ml-4">
                        <span id='pesan_email'></span>
                      </div>
                    </div>
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="username" name="username" onkeyup='lihatUsername()' aria-describedby="emailHelp" placeholder="Username" autocomplete="off">
                      <div class="ml-4">
                        <span id='pesan_username'></span>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="input-group" id="password">
                        <input type="password" name="password" class=" form-control form-control-user" placeholder="Password">
                        <div class="input-group-append">
                          <a href="" class="input-group-text text-decoration-none">
                            <i class="far fa-eye-slash"></i>
                          </a>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="input-group" id="password_conf">
                        <input type="password" name="password2" class=" form-control form-control-user" placeholder="Ulangi Password">
                        <div class="input-group-append">
                          <a href="" class="input-group-text text-decoration-none">
                            <i class="far fa-eye-slash"></i>
                          </a>
                        </div>
                      </div>
                    </div>

                    <button type="submit" class="btn btn-info btn-user btn-block">Registrasi</button>

                  </form>
                  <br>
                  <div class="text-center">
                    <a class="small" href="<?php echo (site_url('Affiliator/Login')) ?>">Sudah punya akun? Login disini</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>

  <script type="text/javascript">
    function lihatEmail() {
      $("#pesan_email").hide();
      // ambil value email dari form
      var email = $("#email").val();
      // proses pengecekan email tersedia atau tidak.
      $.ajax({
        url: "<?php echo site_url() . 'Affiliator/Login/cekEmail'; ?>",
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

    function lihatUsername() {
      $("#pesan_username").hide();
      // ambil value username dari form
      var user = $("#username").val();
      // proses pengecekan username tersedia atau tidak.
      $.ajax({
        url: "<?php echo site_url() . 'Affiliator/Login/cekUsername'; ?>",
        data: 'username=' + user,
        type: "POST",
        success: function(msg) {
          if (msg == 1) {
            $("#pesan_username").css("color", "#fc5d32");
            $("#pesan_username").html("Maaf username sudah digunakan.");
            $('#username').val('');

          } else if (msg == 2) {
            $("#pesan_username").css("color", "#ced4da");
            $("#pesan_username").html("");

          } else {
            $("#pesan_username").css("color", "#fc5d32");
            $("#pesan_username").html("Username tidak valid");
            $('#username').val('');

          }
          $("#pesan_username").fadeIn(1000);
        }
      });

    }
  </script>
  <!-- Bootstrap core JavaScript
    -->
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery/jquery.min.js')) ?>"></script>
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery-easing/jquery.easing.min.js')) ?>"></script>

  <!-- Custom scripts for all pages-->
  <script src="<?php echo (base_url('assets/sb-admin-2/js/sb-admin-2.min.js')) ?>"></script>

  <!-- Bootstrap validator -->
  <script src="<?php echo (base_url('assets/bootstrap-validator/js/bootstrapValidator.js')) ?>"></script>

  <!-- jquery-mask -->
  <script type="text/javascript" src="<?php echo base_url('assets/jquery_mask/jquery.mask.js') ?>"></script>

  <script type="text/javascript">
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
                message: 'Password tidak boleh kosong'
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
                message: 'Ulangi Password tidak boleh kosong'
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
    }); //end (document).ready
  </script>
</body>

</html>