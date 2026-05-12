<?= $this->extend('login/template') ?>
<?= $this->section('content') ?>
<!-- Logo -->
<form action="<?php echo (site_url('kirim-reset-password')) ?>" class="user" method="post" id="form">
    <?php
    $pesan = session()->getFlashdata('pesan');
    if (!empty($pesan)) {
      echo $pesan;
    }
    ?>

    <div class="form-group">
      <input type="email" class="form-control form-control-user" id="email" name="email" aria-describedby="emailHelp" placeholder="Email" autofocus="" required>
    </div>  
    <input type="hidden" name="recaptcha_token" id="recaptcha_token">

    <button type="button" class="btn btn-gradient w-100 mt-4" onclick="submitForm()">Reset Kata Sandi</button>

    <div class="mt-3 text-center">
        <a href="<?php echo (site_url('login')) ?>" class="text-decoration-none">Kembali Masuk</a>
    </div>
</form>

<script src="https://www.google.com/recaptcha/api.js?render=<?= getenv('RECAPTCHA_SITE_KEY') ?>"></script>

<script>
    function submitForm() {
        if (typeof grecaptcha === 'undefined') {
            alert('reCAPTCHA gagal dimuat');
            return;
        }
    
        grecaptcha.ready(function () {
            grecaptcha.execute('<?= getenv('RECAPTCHA_SITE_KEY') ?>', {
                action: 'lupapassword'
            }).then(function (token) {
                document.getElementById('recaptcha_token').value = token;
                document.getElementById('form').submit();
            });
        });
    }
</script>
<?= $this->endSection() ?>