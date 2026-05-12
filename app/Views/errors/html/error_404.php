<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Akuntanmu.com | Pusat Software Akuntansi Online dan Learning Center</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="<?php echo (base_url('assets/assetLanding/img/favicon.png')) ?>" rel="icon">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Source+Sans+Pro:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo (base_url('assets/assetLanding/vendor/bootstrap/css/bootstrap.min.css')) ?>" rel="stylesheet">
  <link href="<?php echo (base_url('assets/assetLanding/vendor/bootstrap-icons/bootstrap-icons.css')) ?>" rel="stylesheet">
  <link href="<?php echo (base_url('assets/assetLanding/vendor/aos/aos.css')) ?>" rel="stylesheet">
  <link href="<?php echo (base_url('assets/assetLanding/vendor/glightbox/css/glightbox.min.css')) ?>" rel="stylesheet">
  <link href="<?php echo (base_url('assets/assetLanding/vendor/swiper/swiper-bundle.min.css')) ?>" rel="stylesheet">

  <!-- Variables CSS Files. Uncomment your preferred color scheme -->
  <link href="<?php echo (base_url('assets/assetLanding/css/variables.css')) ?>" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?php echo (base_url('assets/assetLanding/css/main.css')) ?>" rel="stylesheet">

</head>

<body>

  <!-- ======= Header ======= -->
 

  <section id="hero-animated" class="hero-animated d-flex align-items-center">
    <div class="container d-flex flex-column justify-content-center align-items-center text-center position-relative" data-aos="zoom-out">
      <img style="height:200px" src="<?php echo (base_url('assets/assetLanding/img/hero-carousel/404.svg')) ?>" class="img-fluid animated">
      <h4>Halaman yang anda cari tidak ditemukan</h4>
      <div class="d-flex">
        <a href="<?php echo base_url('/') ?>" class="btn-get-started scrollto">Kembali ke halaman sebelumnya</a>
      </div>
    </div>
  </section>

 

  <a href="https://wa.me/6282180744966" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-whatsapp"></i></a>

  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="<?php echo (base_url('assets/assetLanding/vendor/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>
  <script src="<?php echo (base_url('assets/assetLanding/vendor/aos/aos.js')) ?>"></script>
  <script src="<?php echo (base_url('assets/assetLanding/vendor/glightbox/js/glightbox.min.js')) ?>"></script>
  <script src="<?php echo (base_url('assets/assetLanding/vendor/isotope-layout/isotope.pkgd.min.js')) ?>"></script>
  <script src="<?php echo (base_url('assets/assetLanding/vendor/swiper/swiper-bundle.min.js')) ?>"></script>
  <script src="<?php echo (base_url('assets/assetLanding/vendor/php-email-form/validate.js')) ?>"></script>

  <!-- Template Main JS File -->
  <script src="<?php echo (base_url('assets/assetLanding/js/main.js')) ?>"></script>

  <script>
    const scriptURL = 'https://script.google.com/macros/s/AKfycbwkxETP-8S43SrJWA2LKsXiIYiJkM6aV4F5-EqeDW0ZTTvTT9i8tcuKhmEHtc9gQmoA/exec'
    const form = document.forms['contact-me-nurtiyas'];
    const btnSend = document.querySelector('.btn-send');
    const btnLoading = document.querySelector('.btn-loading');
    const btnMyalert = document.querySelector('.my-alert');
  
    form.addEventListener('submit', e => {
      e.preventDefault()
      //Ketika submit, tampilkan tombol loading dan hilangkan tombol send
      btnLoading.classList.toggle('d-none');
      btnSend.classList.toggle('d-none');
      
      fetch(scriptURL, { method: 'POST', body: new FormData(form)})
    .then(response => {
        //hilangkan tombol loading dan tampilkan tombol send
        btnLoading.classList.toggle('d-none');
        btnSend.classList.toggle('d-none');
        //Tampilkan alert
        btnMyalert.classList.toggle('d-none');
        //Reset form
        form.reset();
        
        console.log('Success!', response)
    })
        .catch(error => console.error('Error!', error.message))
    })
  </script>

</body>

</html>