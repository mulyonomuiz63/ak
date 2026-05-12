<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">


  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="author" content="Akuntanmu">

  <title>Akuntanmu.com | Software Akuntansi Online Berstandar</title>

  <!-- Custom fonts for this template-->
  <link href="<?php echo (base_url('assets/sb-admin-2/vendor/fontawesome-free/css/all.min.css')) ?>" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="<?php echo (base_url('assets/sb-admin-2/css/sb-admin-2.min.css')) ?>" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
  <script type="text/javascript" src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

</head>

<body class="bg-gradient">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-lg-5">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-12">
                <div class="p-5">
                  <a href="<?php echo base_url('/') ?>">
                    <div class="text-center mb-2">
                      <img src="../../images/logo-marketer.png" width="220">
                    </div>
                  </a>
                  <form action=""></form>
                  <form action="<?php echo (site_url('Affiliator/Login/cek_login')) ?>" class="user" method="post">
                    <?php
                    $pesan = session()->getFlashdata('pesan');
                    if (!empty($pesan)) {
                      echo $pesan;
                    }
                    ?>
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="username" name="username" aria-describedby="emailHelp" placeholder="Username atau Email" autofocus>
                      <div class="ml-4">
                        <span style="color: red; font-size:12px" id="username_error" class="error"><span>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="input-group" id="password">
                        <input type="password" class="form-control form-control-user" name="password" placeholder=" Password">
                        <div class="input-group-append">
                          <a href="" class="input-group-text text-decoration-none">
                            <i class="far fa-eye-slash"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="custom-control custom-checkbox small">
                        <input type="checkbox" class="custom-control-input" id="customCheck">
                        <label class="custom-control-label" for="customCheck">Remember Me</label>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-info btn-user btn-block" id="login" disabled>Login</button>
                  </form>
                  <br>
                  <div class="text-center">
                    <a class="small" href="<?php echo (site_url('Affiliator/lupapassword')) ?>">Lupa Password?</a>
                  </div>
                  <div class="text-center">
                    <a class="small" href="<?php echo (site_url('Affiliator/registrasi')) ?>">Belum punya akun? Registrasi disini</a>
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
    $(document).ready(function() {
      $('#username').on('keyup', function(e) {
        e.preventDefault();
        var username = {
          username: $("#username").val()
        };
        $.ajax({
          type: "POST",
          url: "<?php echo base_url(); ?>/Affiliator/Login/validasi_username_login",
          data: username,
          success: function(msg) {
            $('#username_error').html(msg);
            if (!$.trim(msg)) {
              //alert("What follows is blank: " + msg);
              $("#login").attr("disabled", false);

            } else {
              //alert("What follows is not blank: " + msg);
              $('#username').focus();
              $('#username').val('');
              $("#login").attr("disabled", true);

            }
          },
        });
      });

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
    });
  </script>
  <!-- Bootstrap core JavaScript-->
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery/jquery.min.js')) ?>"></script>
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery/jquery.min.js')) ?>"></script>
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery-easing/jquery.easing.min.js')) ?>"></script>

  <!-- Custom scripts for all pages-->
  <script src="<?php echo (base_url('assets/sb-admin-2/js/sb-admin-2.min.js')) ?>"></script>


</body>

</html>