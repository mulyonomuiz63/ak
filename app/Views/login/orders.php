<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Berlangganan | Software Akuntansi Online Berstandar</title>

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
                    <img src="../../images/logo-akuntanmu.png" width="220">
                    <h6>PERPANJANG BERLANGGANAN</h6>
                  </div>
                  <form action=""></form>
                  <form action="<?php echo (site_url('')) ?>" class="user" method="post" id="form">
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="email" name="email" aria-describedby="emailHelp" placeholder="Email">
                    </div>
                    
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="durasi" name="durasi" aria-describedby="emailHelp" placeholder="Durasi Berlangganan">
                    </div>

                    <button type="submit" class="btn btn-info btn-user btn-block">Bayar Langganan</button> <br>

                  </form>
                 
                  <div class="text-center">
                    <a class="small" href="<?php echo (site_url('login')) ?>">Sudah berlangganan? Kembali disini</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>

  <!-- Bootstrap core JavaScript-->
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
</body>

</html>