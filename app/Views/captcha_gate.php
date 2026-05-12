<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Verifikasi</title>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
      /* Background gradasi dengan #047da7 */
      background: linear-gradient(135deg, #047da7 0%, #04c4a7 100%);
    }

    .card {
      background: #fff;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.25);
      text-align: center;
      min-width: 320px;
    }

    h3 {
      margin-bottom: 20px;
      color: #333;
    }
  </style>
</head>
<body style="display:flex;align-items:center;justify-content:center;height:100vh;">

  <form id="captchaForm" method="post" action="<?= site_url('captcha/verify') ?>" class="card">
    <?= csrf_field() ?>
    <input type="hidden" name="next" value="<?= esc($next) ?>">

    <h3>Sedang Verifikasi</h3>

    <!-- Turnstile widget -->
    <div class="cf-turnstile"
         data-sitekey="<?= esc($siteKey) ?>"
         data-callback="onCaptchaSuccess"
         data-theme="auto"></div>

    <?php if (session('error')): ?>
      <p style="color:red;"><?= esc(session('error')) ?></p>
    <?php endif; ?>

    <!-- Tombol fallback (tidak dipakai karena auto submit) -->
    <button type="submit" style="display:none;">Lanjutkan</button>
  </form>

  <script>
    function onCaptchaSuccess(token) {
      // Otomatis submit setelah verifikasi sukses
      document.getElementById('captchaForm').submit();
    }
  </script>

</body>
</html>
