<?= $this->extend('login/template') ?>
<?= $this->section('content') ?>
<!-- Logo -->
<!-- Form Login -->
<form action="<?php echo (site_url('cek-login')) ?>" class="user" id="FormId" method="post">
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
        <input type="password" name="password" class="form-control form-control-user" placeholder="Kata Sandi">
        <div class="input-group-append input-group-text d-flex align-items-center">
          <a href="" class=" text-decoration-none">
            <i class="far fa-eye-slash"></i>
          </a>
        </div>
      </div>
    </div>
     <input type="hidden" name="recaptcha_token" id="recaptcha_token">
    <div class="mb-3 text-end">
        <a href="<?php echo (site_url('lupapassword')) ?>" class="text-decoration-none">Lupa kata sandi?</a>
    </div>
    <button type="button" class="btn btn-gradient w-100" onclick="submitForm()">Masuk</button>
    <div class="mt-3 text-center">
        <small>Belum punya akun? <a href="<?php echo (site_url('registrasi')) ?>" class="text-decoration-none">Registrasi</a></small>
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
<script src="https://www.google.com/recaptcha/api.js?render=<?= getenv('RECAPTCHA_SITE_KEY') ?>"></script>

<script>
    function submitForm() {
        if (typeof grecaptcha === 'undefined') {
            alert('reCAPTCHA gagal dimuat');
            return;
        }
    
        grecaptcha.ready(function () {
            grecaptcha.execute('<?= getenv('RECAPTCHA_SITE_KEY') ?>', {
                action: 'login'
            }).then(function (token) {
                document.getElementById('recaptcha_token').value = token;
                document.getElementById('FormId').submit();
            });
        });
    }
</script>
<?= $this->endSection() ?>