<?= $this->extend('login/template') ?>
<?= $this->section('content') ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<!-- Logo -->
<form action="<?php echo (site_url('Login/update_password')) ?>" class="user" method="post" onsubmit="return validatePassword()">
    <?php
    $pesan = session()->getFlashdata('pesan');
    if (!empty($pesan)) {
      echo $pesan;
    }
    ?>

    <!-- Password input -->
    <!-- <div class="form-outline mb-3"> -->
    <div class="form-group mb-4">
    <input type="hidden" name="token" value="<?= $token ?>">
      <div class="input-group" id="eye-password">
        <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Password">
        <div class="input-group-append input-group-text d-flex align-items-center">
          <a href="" class=" text-decoration-none" >
            <i class="far fa-eye-slash"></i>
          </a>
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="input-group" id="eye-confirm">
        <input type="password" name="confirm" id="confirm" class="form-control form-control-lg" placeholder="Ulangi Password">
        <div class="input-group-append input-group-text d-flex align-items-center">
          <a href="" class=" text-decoration-none" >
            <i class="far fa-eye-slash"></i>
          </a>
        </div>
      </div>
    </div>
    <span id="message" class="error text-danger"></span>
    <!-- </div> -->
    <div class="text-center text-lg-start mt-4 pt-2">
      <button type="submit" class="btn btn-gradient w-100">Ubah Password</button>
      <p class="small fw-bold mt-2 pt-1 mb-0 ">Kembali kehalaman depan <a class="small text-decoration-none" href="<?php echo (site_url('/')) ?>">Kembali</a></p>
    </div>
</form>

<script type="text/javascript">
$(document).ready(function() {
  

  $("#eye-password a").on('click', function(event) {
    event.preventDefault();
    if ($('#eye-password input').attr("type") == "text") {
      $('#eye-password input').attr('type', 'password');
      $('#eye-password i').addClass("far fa-eye-slash");
      $('#eye-password i').removeClass("fa fa-solid fa-eye");
    } else if ($('#eye-password input').attr("type") == "password") {
      $('#eye-password input').attr('type', 'text');
      $('#eye-password i').removeClass("far fa-eye-slash");
      $('#eye-password i').addClass("fa fa-solid fa-eye");
    }
  });
  
  $("#eye-confirm a").on('click', function(event) {
    event.preventDefault();
    if ($('#eye-confirm input').attr("type") == "text") {
      $('#eye-confirm input').attr('type', 'password');
      $('#eye-confirm i').addClass("far fa-eye-slash");
      $('#eye-confirm i').removeClass("fa fa-solid fa-eye");
    } else if ($('#eye-confirm input').attr("type") == "password") {
      $('#eye-confirm input').attr('type', 'text');
      $('#eye-confirm i').removeClass("far fa-eye-slash");
      $('#eye-confirm i').addClass("fa fa-solid fa-eye");
    }
  });

});
</script>
<script>
    function validatePassword() {
      const password = document.getElementById("password").value;
      const confirm = document.getElementById("confirm").value;
      const message = document.getElementById("message");

      // Regex untuk validasi
      const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/;

      if (!regex.test(password)) {
        message.textContent = "Password harus minimal 6 karakter, mengandung huruf kecil, huruf kapital, dan angka.";
        message.className = "error";
        return false;
      }

      if (password !== confirm) {
        message.textContent = "Konfirmasi password tidak cocok.";
        message.className = "error";
        return false;
      }

      message.textContent = "Password valid dan cocok!";
      message.className = "success";
      return true;
    }
  </script>
  <!-- Bootstrap core JavaScript-->
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery/jquery.min.js')) ?>"></script>
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery-easing/jquery.easing.min.js')) ?>"></script>

  <!-- Custom scripts for all pages-->
  <script src="<?php echo (base_url('assets/sb-admin-2/js/sb-admin-2.min.js')) ?>"></script>
<?= $this->endSection() ?>