<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta property="og:title" content="Yuk Berlangganan di Akuntanmu!!!" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?= base_url() . 'Login/registrasi/' ?>" />
    <meta property="og:image" content="https://akuntanmu.com/uploads/langganan/langganan.png" />
    <meta property="og:description" content="Nikmati Langganan di aplikasi akuntanmu dengan diskon yang cukup menghemat kantong." />
    <meta name="author" content="Akuntanmu">
    <title>Akuntanmu.com | Software Akuntansi Online Berstandar</title>
    <link rel="shortcut icon" href="<?= base_url("favicon.ico") ?>" />
    <!-- Bootstrap 5.3.3 -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo (base_url('assets/jquery-ui/themes/base/jquery-ui.css')) ?>">
    <script src='https://www.google.com/recaptcha/api.js?hl=id'></script>
    <?php
        $db = Config\Database::connect();
        $backgrounds = $db->query("SELECT file FROM iklan where status = 'background' LIMIT 1 ");
        $background = $backgrounds->getRowObject();
        if(!empty($background)){
            $background_desktop = base_url('uploads/iklan/thumbnails/'.$background->file);
        }else{
            $background_desktop = base_url('uploads/nav-sidebar/background.jpg?v=1');
        }
        
        $background_ms = $db->query("SELECT file FROM iklan where status = 'background-mobile' LIMIT 1 ");
        $background_m = $background_ms->getRowObject();
        if(!empty($background_m)){
            $background_mobile = base_url('uploads/iklan/thumbnails/'.$background_m->file);
        }else{
            $background_mobile = base_url('uploads/nav-sidebar/background-mobile.jpg?v=1');
        }
    ?>
    <style>
        body {
            background: url('<?= $background_desktop ?>') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            /*background: rgba(0, 0, 0, 0.4);*/
            z-index: 0;
        }
    
        /* Untuk HP */
        @media (max-width: 768px) {
            body {
                background: url('<?= $background_mobile ?>') no-repeat center center fixed;
                background-size: cover;
            }
            .desktop-illustration {
                display: none;
            }
            .card-box{
                width:100%
            }
        }
        @media (min-width: 768px) {
            .card-box{
                width:70%
            }
        }
        .card-box {
            position: relative;
            z-index: 1;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            padding: 30px;
        }
        .btn-gradient {
            background: radial-gradient(circle, #5dade2 10%, #005572 100%, #005572 80%);
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 8px;
        }
        .welcome-text {
            color: white;
            font-size: 2rem;
            font-weight: bold;
            text-shadow: 0 2px 5px rgba(0,0,0,0.5);
        }
        .brand-logo {
            max-width: 60px;
        }
        .form-control {
            border-radius: 8px;
        }
        .input-group-text {
            cursor: pointer;
            background-color: transparent;
            border-left: none;
        }
        .input-group .form-control {
            border-right: none;
        }
        .carousel {
            max-width: 65%;
            margin: auto;
        }
        .has-error .help-block {
          color: red;
        }
        .error {
            color: red;        /* Warna teks merah untuk pesan kesalahan */
          }
        
          .success {
            color: green;      /* Warna teks hijau untuk pesan sukses */
          }
          .slider-shadow {
            filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.2))
            drop-shadow(0 0 40px rgba(255, 255, 255, 0.1))
            drop-shadow(0 0 60px rgba(255, 255, 255, 0.1));
            border: 1px solid white;
            /* atau khusus warna saja */
            border-color: white;
          }
    </style>
</head>
<body>
<div class="container position-relative z-1">
    <div class="row align-items-center">
        
        <!-- Bagian kiri -->
        <div class="col-md-6 text-center text-md-left desktop-illustration">
            <div id="iklanCarousel" class="carousel slide" data-bs-ride="carousel">
              <!-- Gambar Slider -->
              <div class="carousel-inner rounded-4 shadow slider-shadow">
                <?php $no=0; foreach($banner as $rows): ?>
                    <div class="carousel-item <?= $no == '0'? 'active':'' ?>">
                        <a href="<?= $rows->url ?>">
                            <?= img_lazy('uploads/iklan/thumbnails/'. $rows->file,"$rows->nama_iklan", ['class' => 'd-block w-100 rounded shadow']) ?>
                        </a>
                    </div>
                    <?php $no++; ?>
                <?php endforeach; ?>
              </div>
            </div>
        </div>

        <!-- Bagian Form -->
        <div class="col-md-6 d-flex justify-content-center">
            <div class="card-box m-4 ">
                <div class="text-center mb-3">
                    <a href="<?= base_url('/') ?>">
                        <?= img_lazy('images/logo-akuntanmu.png',"loading", ['class' => 'mb-2', 'width'=>'70%', 'height'=>'10%']) ?>
                    </a>
                </div>
                <?= $this->renderSection('content'); ?>
            </div>
        </div>

    </div>
</div>

<!-- Script -->
<script>
function showForm(form) {
    document.getElementById('login-form').style.display = (form === 'login') ? 'block' : 'none';
    document.getElementById('forgot-form').style.display = (form === 'forgot') ? 'block' : 'none';
    document.getElementById('register-form').style.display = (form === 'register') ? 'block' : 'none';

    const title = {
        login: 'Log in',
        forgot: 'Lupa Password',
        register: 'Registrasi'
    };
    document.getElementById('form-title').textContent = title[form];
}

function togglePassword(fieldId, el) {
    let field = document.getElementById(fieldId);
    if (field.type === "password") {
        field.type = "text";
        el.textContent = "🙈";
    } else {
        field.type = "password";
        el.textContent = "👁";
    }
}
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
  let lazyImages = document.querySelectorAll("img.lazy");

  if ("IntersectionObserver" in window) {
    // ✅ Browser support IntersectionObserver
    let observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          let img = entry.target;
          img.src = img.dataset.src;
          img.removeAttribute("data-src");
          img.classList.remove("lazy");
          obs.unobserve(img);
        }
      });
    });

    lazyImages.forEach(img => observer.observe(img));

  } else {
    // ⚠️ Fallback kalau browser tidak support
    lazyImages.forEach(img => {
      img.src = img.dataset.src;
      img.removeAttribute("data-src");
      img.setAttribute("loading", "lazy");
      img.classList.remove("lazy");
    });
  }
});
</script>
</body>
</html>
