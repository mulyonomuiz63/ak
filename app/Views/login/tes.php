<?= $this->extend('login/template') ?>
<?= $this->section('content') ?>
<!-- Logo -->
<div class="text-center mb-3">
     <img src="../../images/logo-akuntanmu.png" width="220" class="mb-2" alt="Logo">
</div>

<!-- Form Login -->
<form action="<?php echo (site_url('Login/cek_login')) ?>" class="user" method="post">
    <?php
    $pesan = session()->getFlashdata('pesan');
    if (!empty($pesan)) {
      echo $pesan;
    }
    ?>
    <div class="mb-3">
        <input type="text" id="username" name="username" class="form-control" placeholder="Username atau Email">
    </div>
    <div class="form-group mb-2">
      <div class="input-group" id="password">
        <input type="password" name="password" class="form-control form-control-user" placeholder="Password">
        <div class="input-group-append input-group-text d-flex align-items-center">
          <a href="" class=" text-decoration-none">
            <i class="far fa-eye-slash"></i>
          </a>
        </div>
      </div>
    </div>
    <div class="mb-3 text-end">
        <a href="<?php echo (site_url('lupapassword')) ?>" class="text-decoration-none">Lupa Password?</a>
    </div>
    <button type="submit" class="btn btn-gradient w-100">Log in</button>
    <div class="mt-3 text-center">
        <small>Belum punya akun? <a href="<?php echo (site_url('registrasi')) ?>" class="text-decoration-none">Registrasi di sini</a></small>
    </div>
</form>

<script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery/jquery.min.js')) ?>"></script>
<script>
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
    });
</script>
<?= $this->endSection() ?>