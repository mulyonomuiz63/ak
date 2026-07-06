<!DOCTYPE html>

<html lang="en">



<head>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <meta property="og:title" content="Yuk Berlangganan di Akuntanmu!!!" />

  <meta property="og:type" content="article" />

  <meta property="og:url" content="<?= base_url() ?>" />

  <meta property="og:image" content="https://akuntanmu.com/uploads/langganan/langganan.png" />

  <meta property="og:description" content="Nikmati Langganan di aplikasi akuntanmu dengan diskon yang cukup menghemat kantong." />

  <meta name="author" content="">



  <title>Akuntanmu.com | Software Akuntansi Online UMKM dan Learning Center</title>

  <!-- Favicons -->

  <link rel="shortcut icon" href="<?= base_url("favicon.ico") ?>" />



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
  <link href="https://kelasbrevet.com/assets/app-assets/template/cbt-malela/plugins/sweetalerts/sweetalert.css" rel="stylesheet" type="text/css">
  




  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.12/dist/css/splide.min.css">







  <!-- Variables CSS Files. Uncomment your preferred color scheme -->

  <link href="<?php echo (base_url('assets/assetLanding/css/variables.css')) ?>" rel="stylesheet">



  <!-- Template Main CSS File -->

  <link href="<?php echo (base_url('assets/assetLanding/css/main.css')) ?>" rel="stylesheet">



  <style>
    :root {
      --main-color: #055F93;
      --accent-color: #00C6FF;
      --light-color: #E8F7FF;
      --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      --gradient-main: linear-gradient(135deg, #055F93 0%, #00C6FF 100%);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      overflow-x: hidden;
    }
    #header{
          background: rgba(5, 95, 147, 0.8);
    }
    nav.scrolled {
      background: white;
      box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    }

    nav .navbar-brand {
      font-weight: 700;
      color: white;
      font-size: 1.4rem;
      transition: color 0.3s;
    }

    nav.scrolled .navbar-brand {
      color: var(--main-color);
    }

    nav .nav-link {
      color: #fff !important;
      margin-right: 15px;
      font-weight: 500;
      padding: 8px 16px;
      border-radius: 20px;
      transition: all 0.3s ease;
    }

    nav .nav-link:hover {
      background: white;
      color: var(--main-color) !important;
    }

    nav.scrolled .nav-link {
      color: var(--main-color) !important;
    }

    nav.scrolled .nav-link:hover {
      background: var(--light-color);
      color: var(--main-color) !important;
    }

    .hero {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, var(--main-color) 0%, var(--accent-color) 100%);
      overflow-x: hidden;
      color: white;
      scroll-behavior: smooth;
    }


    /* ==== HERO SECTION - MODERN DESIGN ==== */
    .hero {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      padding: 140px 0 60px;
      overflow: hidden;
      background: linear-gradient(135deg, #055F93 0%, #00C6FF 50%, #764ba2 100%);
      background-size: 200% 200%;
      animation: gradientShift 15s ease infinite;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Animated Background Particles */
    .hero::before {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      background-image: 
        radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
      animation: particleMove 20s ease-in-out infinite;
    }

    @keyframes particleMove {
      0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.5; }
      33% { transform: translate(30px, -30px) scale(1.1); opacity: 0.7; }
      66% { transform: translate(-20px, 20px) scale(0.9); opacity: 0.6; }
    }

    /* Glassmorphism Effect */
    .hero::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }

    .hero-content {
      z-index: 2;
      position: relative;
      animation: fadeInUp 1.5s ease forwards;
    }

    /* Floating Shapes Animation */
    .floating-shape {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      animation: floatShape 15s infinite ease-in-out;
    }

    .shape-1 {
      width: 200px;
      height: 200px;
      top: 10%;
      left: 10%;
      animation-delay: 0s;
    }

    .shape-2 {
      width: 150px;
      height: 150px;
      bottom: 20%;
      right: 15%;
      animation-delay: 2s;
    }

    .shape-3 {
      width: 100px;
      height: 100px;
      top: 60%;
      right: 30%;
      animation-delay: 4s;
    }

    @keyframes floatShape {
      0%, 100% { transform: translate(0, 0) rotate(0deg); }
      33% { transform: translate(30px, -50px) rotate(120deg); }
      66% { transform: translate(-30px, 30px) rotate(240deg); }
    }

    .hero h1 {
      font-size: 3.5rem;
      font-weight: 800;
      line-height: 1.2;
      text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
      letter-spacing: -1px;
    }

    .hero h1 .highlight {
      background: linear-gradient(135deg, #FFD700, #FFA500, #FF6B6B);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      display: inline-block;
      animation: shimmer 3s infinite;
      background-size: 200% auto;
    }

    @keyframes shimmer {
      0%, 100% { 
        filter: brightness(1);
        background-position: 0% 50%;
      }
      50% { 
        filter: brightness(1.5);
        background-position: 100% 50%;
      }
    }

    /* STATS SECTION */
    .stats-section {
      padding: 80px 0;
    }

    .stat-card {
      padding: 40px 20px;
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(5, 95, 147, 0.1);
      border: 2px solid rgba(255, 255, 255, 0.8);
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      height: 100%;
    }

    .stat-card:hover {
      transform: translateY(-10px) scale(1.05);
      box-shadow: 0 20px 60px rgba(5, 95, 147, 0.2);
    }

    .stat-icon {
      width: 80px;
      height: 80px;
      margin: 0 auto 20px;
      background: linear-gradient(135deg, #055F93, #00C6FF);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 10px 30px rgba(5, 95, 147, 0.3);
      transition: all 0.4s;
    }

    .stat-card:hover .stat-icon {
      transform: rotate(360deg) scale(1.1);
      box-shadow: 0 15px 40px rgba(5, 95, 147, 0.4);
    }

    .stat-icon i {
      font-size: 36px;
      color: white;
    }

    .stat-number {
      font-size: 3rem;
      font-weight: 800;
      color: #055F93;
      margin-bottom: 10px;
      font-family: 'Poppins', sans-serif;
    }

    .stat-label {
      font-size: 1.1rem;
      color: #6c757d;
      font-weight: 500;
      margin: 0;
    }

    .hero p {
      font-size: 1.1rem;
      max-width: 550px;
      color: rgba(255,255,255,0.9);
      margin-bottom: 30px;
    }

    .btn-join {
      background: linear-gradient(135deg, #ffffff, #f0f0f0);
      color: var(--main-color);
      font-weight: 700;
      border-radius: 50px;
      padding: 15px 35px;
      transition: all .4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      position: relative;
      overflow: hidden;
      border: none;
      font-size: 1.1rem;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .btn-join::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.5);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }

    .btn-join:hover::before {
      width: 300px;
      height: 300px;
    }

    .btn-join:hover {
      background: linear-gradient(135deg, #00C6FF, #055F93);
      color: white;
      transform: translateY(-3px) scale(1.05);
      box-shadow: 0 15px 40px rgba(0, 198, 255, 0.4);
    }

    .btn-join span {
      position: relative;
      z-index: 1;
    }

    /* ==== DECORATIVE SHAPES ==== */
    .wave-bg {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      z-index: 1;
    }

    .photo-wrapper {
      position: relative;
      width: 350px;
      height: 350px;
      border-radius: 30px;
      overflow: hidden;
      border: 8px solid rgba(255, 255, 255, 0.3);
      margin: auto;
      box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.3),
        inset 0 0 50px rgba(255, 255, 255, 0.1);
      animation: float3D 6s ease-in-out infinite;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      transform-style: preserve-3d;
    }

    @keyframes float3D {
      0%, 100% { 
        transform: translateY(0px) rotateX(0deg) rotateY(0deg); 
      }
      25% { 
        transform: translateY(-20px) rotateX(5deg) rotateY(5deg); 
      }
      50% { 
        transform: translateY(-10px) rotateX(0deg) rotateY(-5deg); 
      }
      75% { 
        transform: translateY(-15px) rotateX(-5deg) rotateY(0deg); 
      }
    }

    .photo-wrapper:hover {
      transform: scale(1.05);
      box-shadow: 0 0 30px rgba(0,198,255,0.6);
    }

    .photo-wrapper img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .dots {
      position: absolute;
      width: 150px;
      height: 150px;
      background-image: radial-gradient(white 2px, transparent 2px);
      background-size: 12px 12px;
      top: 60%;
      left: -60px;
      opacity: 0.25;
      animation: float 8s ease-in-out infinite;
    }

    /* ==== ANIMATIONS ==== */
    @keyframes fadeInUp {
      0% {opacity: 0; transform: translateY(40px);}
      100% {opacity: 1; transform: translateY(0);}
    }

    @keyframes fadeIn {
      0% {opacity: 0;}
      100% {opacity: 1;}
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-15px); }
    }

    @keyframes floatReverse {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(15px); }
    }

    @media (max-width: 768px) {
      .hero {
        text-align: center;
      }
      .hero h1 {
        font-size: 2.3rem;
      }
      .photo-wrapper {
        width: 220px;
        height: 220px;
      }
    }

    /* ==== FEATURED SERVICES ==== */
    .featured-services {
      padding: 80px 0;
      background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
    }

    .featured-services .service-item {
      padding: 50px 35px;
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      height: 100%;
      border-radius: 25px;
      box-shadow: 
        0 15px 35px rgba(5, 95, 147, 0.1),
        inset 0 -1px 0 rgba(255, 255, 255, 0.6);
      border: 2px solid rgba(255, 255, 255, 0.8);
      position: relative;
      overflow: hidden;
      transform-style: preserve-3d;
    }

    .featured-services .service-item::after {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(5, 95, 147, 0.1) 0%, transparent 70%);
      opacity: 0;
      transition: opacity 0.5s;
    }

    .featured-services .service-item:hover::after {
      opacity: 1;
    }

    .featured-services .service-item::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
      transition: left 0.5s;
    }

    .featured-services .service-item:hover::before {
      left: 100%;
    }

    .featured-services .service-item .icon {
      margin-bottom: 20px;
      position: relative;
      display: inline-block;
    }

    .featured-services .service-item .icon::after {
      content: '';
      position: absolute;
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, rgba(5, 95, 147, 0.1), rgba(0, 198, 255, 0.1));
      border-radius: 50%;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0);
      transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      z-index: -1;
    }

    .featured-services .service-item:hover .icon::after {
      transform: translate(-50%, -50%) scale(1.5);
    }

    .featured-services .service-item .icon i {
      color: #055F93;
      font-size: 48px;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      display: inline-block;
      position: relative;
      z-index: 1;
    }

    .featured-services .service-item:hover .icon i {
      color: #00C6FF;
      transform: scale(1.2) rotateY(360deg);
    }

    .featured-services .service-item h4 {
      font-weight: 700;
      margin-bottom: 15px;
      font-size: 22px;
      transition: all 0.3s;
    }

    .featured-services .service-item h4 a {
      color: var(--color-secondary);
      transition: ease-in-out 0.3s;
      text-decoration: none;
    }

    .featured-services .service-item p {
      line-height: 26px;
      font-size: 15px;
      margin-bottom: 0;
      color: #6c757d;
      transition: color 0.3s;
    }

    .featured-services .service-item:hover {
      transform: translateY(-20px) scale(1.03) rotateX(5deg);
      box-shadow: 
        0 25px 70px rgba(5, 95, 147, 0.3),
        0 0 0 1px rgba(0, 198, 255, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
      background: rgba(255, 255, 255, 0.95);
      border-color: rgba(0, 198, 255, 0.5);
    }

    .featured-services .service-item:hover h4 a {
      color: #055F93;
    }

    .featured-services .service-item:hover p {
      color: #495057;
    }

    /* ==== SERVICES SECTION CARD ANIMATION ==== */
    .services .service-item {
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .services .service-item:hover {
      transform: translateY(-10px);
    }

    .services .service-item .details {
      transition: all 0.4s ease;
    }

    .services .service-item:hover .details {
      box-shadow: 0px 10px 40px rgba(5, 95, 147, 0.15);
      transform: translateY(-5px);
    }

    /* ==== PRICING CARD ANIMATION ==== */
    .pricing .pricing-item {
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .pricing .pricing-item:hover {
      transform: translateY(-10px) scale(1.02);
      box-shadow: 0 15px 50px rgba(5, 95, 147, 0.2);
    }

    .pricing .pricing-item.featured {
      border: 4px solid #055F93;
      position: relative;
      overflow: hidden;
    }

    .pricing .pricing-item.featured::before {
      content: 'POPULER';
      position: absolute;
      top: 20px;
      right: -35px;
      background: linear-gradient(135deg, #055F93, #00C6FF);
      color: white;
      padding: 5px 40px;
      font-size: 12px;
      font-weight: 700;
      transform: rotate(45deg);
      z-index: 10;
      box-shadow: 0 2px 10px rgba(5, 95, 147, 0.3);
    }

    /* ==== PULSE ANIMATION FOR ICONS ==== */
    @keyframes pulse {
      0% {
        box-shadow: 0 0 0 0 rgba(5, 95, 147, 0.7);
      }
      70% {
        box-shadow: 0 0 0 10px rgba(5, 95, 147, 0);
      }
      100% {
        box-shadow: 0 0 0 0 rgba(5, 95, 147, 0);
      }
    }

    .featured-services .service-item .icon i {
      animation: pulse 2s infinite;
    }

    .featured-services .service-item:hover .icon i {
      animation: none;
    }

    /* ==== RIPPLE EFFECT ==== */
    .btn-join, .buy-btn {
      position: relative;
      overflow: hidden;
    }

    .ripple {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.6);
      transform: scale(0);
      animation: ripple-animation 0.6s ease-out;
      pointer-events: none;
    }

    @keyframes ripple-animation {
      to {
        transform: scale(4);
        opacity: 0;
      }
    }

    /* ==== ENHANCED SECTION HEADERS ==== */
    .section-header h2 {
      position: relative;
      display: inline-block;
      padding-bottom: 15px;
    }

    .section-header h2::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 4px;
      background: linear-gradient(90deg, #055F93, #00C6FF);
      border-radius: 2px;
      animation: expandLine 0.8s ease-out;
    }

    @keyframes expandLine {
      from {
        width: 0;
      }
      to {
        width: 60px;
      }
    }

    /* ==== SMOOTH SCROLL BEHAVIOR ==== */
    html {
      scroll-behavior: smooth;
    }

    /* ==== ENHANCED CARD SHADOWS ==== */
    .pricing-item, .service-item {
      transition: box-shadow 0.3s ease;
    }

    .pricing-item:hover, .service-item:hover {
      box-shadow: 0 15px 50px rgba(5, 95, 147, 0.25) !important;
    }

    /* ==== FLOATING ANIMATION FOR HERO IMAGE ==== */
    @keyframes float {
      0%, 100% { 
        transform: translateY(0px) rotate(0deg); 
      }
      33% { 
        transform: translateY(-20px) rotate(1deg); 
      }
      66% { 
        transform: translateY(-10px) rotate(-1deg); 
      }
    }

    .photo-wrapper {
      animation: float 6s ease-in-out infinite;
    }

    /* ==== GRADIENT TEXT ANIMATION ==== */
    @keyframes gradient-shift {
      0%, 100% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
    }

    /* ==== PRICING CARDS ENHANCEMENT ==== */
    .pricing-item {
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 2px solid rgba(255, 255, 255, 0.8) !important;
    }

    .pricing-item.featured {
      background: linear-gradient(135deg, rgba(5, 95, 147, 0.05), rgba(0, 198, 255, 0.05)) !important;
      border: 3px solid #055F93 !important;
      transform: scale(1.05);
    }

    /* ==== SERVICES CARD ENHANCEMENT ==== */
    .services .service-item {
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .services .service-item:hover {
      transform: translateY(-15px) scale(1.03);
    }

    .services .details {
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
    }

    /* ==== RESPONSIVE ENHANCEMENTS ==== */
    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2.5rem;
      }
      
      .stat-number {
        font-size: 2.5rem;
      }
      
      .photo-wrapper {
        width: 280px;
        height: 280px;
      }

      .btn-join {
        padding: 12px 25px;
        font-size: 1rem;
      }
    }
  </style>
    
    <style>
        /* Tambahkan CSS ini di bagian style Anda agar tampilan rapi */
        .main-footer {
            background-color: #00233a;
            color: #ffffff;
            padding-top: 50px;
        }
        .collapse-content {
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 30px;
        }
        .content-body {
            max-height: 450px; /* Batas tinggi box agar ada scroll */
            overflow-y: auto;
            padding: 30px;
            font-size: 0.9rem;
            color: #e0e0e0;
            line-height: 1.6;
        }
        .content-body h5 { color: #0dcaf0; margin-top: 20px; border-bottom: 1px solid #333; padding-bottom: 5px; }
        .footer-link {
            color: #a0a0a0;
            text-decoration: none;
            display: block;
            margin-bottom: 8px;
            cursor: pointer;
            border: none;
            background: none;
            padding: 0;
            text-align: left;
        }
        .footer-link:hover { color: #ffffff; }
        .bottom-footer {
            background-color: #001d31;
            padding: 20px 0;
            margin-top: 50px;
            border-top: 1px dashed rgba(255,255,255,0.1);
        }
    </style>


</head>



<body>
  <!-- NAVBAR -->
  <header id="header" class="header fixed-top" data-scrollto-offset="0">

    <div class="container-fluid d-flex align-items-center justify-content-between">
      <a href="<?php echo base_url("/") ?>" class=" d-flex align-items-center scrollto me-auto me-lg-0">
        <?= img_lazy('images/logo-akuntanmu-putih.png',"loading", ['width' => '200px', 'height'=>'50px']) ?>
      </a>



      <nav id="navbar" class="navbar">
        <ul>
          <li class="nav-item"><a class="nav-link scrollto" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link scrollto" href="index.html#about">Tentang Kami</a></li>
          <li class="nav-item"><a class="nav-link scrollto" href="index.html#services">Layanan</a></li>
          <li class="nav-item"><a class="nav-link scrollto" href="index.html#pricing">Harga</a></li>
          <li class="nav-item"><a class="nav-link scrollto" href="https://kelasbrevet.com">KelasBrevet</a></li>
          <li class="nav-item"><a class="nav-link scrollto" href="https://elearning.akuntanmu.com">Lembaga Pelatihan</a></li>
          <li class="nav-item"><a class="nav-link scrollto" href="index.html#contact">Kontak</a></li>
          <li class="nav-item"><a class="nav-link scrollto" href="<?php echo base_url('login') ?>">Masuk</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle d-none text-light"></i>
      </nav><!-- .navbar -->



      



    </div>

  </header><!-- End Header -->
  <!-- HERO -->
  <section class="hero">
    <!-- Floating Shapes -->
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>
    
    <div class="container hero-content text-center text-md-start">
      <div class="row align-items-center">
        <div class="col-md-8 text-start">
          <h1>Selesaikan segera masalah<br><span class="highlight">pembukuan usaha anda</span></h1>
          <p style="font-size: 1.2rem; line-height: 1.8; margin-bottom: 35px;">Kami bersama para konsultan profesional akan membantu anda membukukan transaksi bisnis sesuai standar akuntansi keuangan</p>
          <a href="https://api.whatsapp.com/send?phone=6282180744966&text=Halo Akuntanmu,%20saya%20ingin%20bertanya..." class="btn btn-join me-3"><span>Hubungi Kami</span></a>
        </div>
        <div class="col-md-4 mt-5 mt-md-0 text-center position-relative">
          <div class="photo-wrapper">
            <?= img_lazy('assets/assetLanding/img/cta3.jpg',"loading", ['class' => 'img-fluid']) ?>
          </div>
          <div class="dots"></div>
        </div>
      </div>
    </div>

    <!-- Wave Background (Layered) -->
    <svg class="wave-bg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
      <path fill="#ffffff" fill-opacity="0.15" d="M0,192L60,186.7C120,181,240,171,360,154.7C480,139,600,117,720,128C840,139,960,181,1080,186.7C1200,192,1320,160,1380,144L1440,128L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path>
    </svg>
  </section>

  <!-- STATS SECTION -->
  <section class="stats-section" style="padding: 60px 0; background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); margin-top: -50px; position: relative; z-index: 10;">
    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="0">
          <div class="stat-card text-center">
            <div class="stat-icon">
              <i class="bi bi-people-fill"></i>
            </div>
            <h3 class="stat-number" data-target="1000" data-suffix="+">0</h3>
            <p class="stat-label">Pengguna Aktif</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
          <div class="stat-card text-center">
            <div class="stat-icon">
              <i class="bi bi-building"></i>
            </div>
            <h3 class="stat-number" data-target="100" data-suffix="+">0</h3>
            <p class="stat-label">Perusahaan</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
          <div class="stat-card text-center">
            <div class="stat-icon">
              <i class="bi bi-award-fill"></i>
            </div>
            <h3 class="stat-number" data-target="98" data-suffix="%">0</h3>
            <p class="stat-label">Kepuasan</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
          <div class="stat-card text-center">
            <div class="stat-icon">
              <i class="bi bi-clock-history"></i>
            </div>
            <h3 class="stat-number" data-target="24" data-suffix="">0</h3>
            <p class="stat-label">Jam Support</p>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- ======= About Section ======= -->

  <section id="about" class="about">

    <div class="container" data-aos="fade-up">



      <div class="section-header">

        <h2>Tentang Kami</h2>

        <p>Akuntanmu merupakan platform pembukuan online yang di desain khusus untuk membantu perkembangan UMKM.
          <br>Proses sederhana namun sangat mengasah kemampuan jurnal seorang akuntan.
          <br>Akuntanmu Learning Center merupakan Lembaga Resmi dibawah PT.Legalyn Konsultan Indonesia.

        </p>

      </div>

    </div>

  </section><!-- End About Section -->



  <main id="main">



    <!-- ======= Featured Services Section ======= -->

    <section id="featured-services" class="featured-services">

      <div class="container">



        <div class="row gy-4">



          <div class="col-xl-3 col-md-6 d-flex" data-aos="zoom-out" data-aos-duration="800">

            <div class="service-item position-relative">

              <div class="icon">
                <i class="bi bi-check-circle-fill"></i>
              </div>

              <h4><a href="" class="stretched-link">Mudah Berstandar</a></h4>

              <p>Nikmati kemudahan dalam pembukuan dengan basis standar akuntansi keuangan</p>

            </div>

          </div><!-- End Service Item -->



          <div class="col-xl-3 col-md-6 d-flex" data-aos="zoom-out" data-aos-delay="200" data-aos-duration="800">

            <div class="service-item position-relative">

              <div class="icon">
                <i class="bi bi-shield-lock-fill"></i>
              </div>

              <h4><a href="" class="stretched-link">Dokumentasi Aman</a></h4>

              <p>Setiap transaksi dan dokumen anda akan terjaga keamanannya oleh sistem akuntanmu</p>

            </div>

          </div><!-- End Service Item -->



          <div class="col-xl-3 col-md-6 d-flex" data-aos="zoom-out" data-aos-delay="400" data-aos-duration="800">

            <div class="service-item position-relative">

              <div class="icon">
                <i class="bi bi-calendar-check-fill"></i>
              </div>

              <h4><a href="" class="stretched-link">Terjadwal</a></h4>

              <p>Ikuti pelatihan pengembangan keahlian di bidang akuntansi dan perpajakan</p>

            </div>

          </div><!-- End Service Item -->



          <div class="col-xl-3 col-md-6 d-flex" data-aos="zoom-out" data-aos-delay="600" data-aos-duration="800">

            <div class="service-item position-relative">

              <div class="icon">
                <i class="bi bi-cloud-check-fill"></i>
              </div>

              <h4><a href="" class="stretched-link">Online 24/7</a></h4>

              <p>Sistem akuntanmu dapat di akses kapan saja dan dimana saja 24 jam tanpa batas</p>

            </div>

          </div><!-- End Service Item -->



        </div>



      </div>

    </section><!-- End Featured Services Section -->







    <!-- ======= On Focus Section ======= -->

    <section id="onfocus" class="onfocus">

      <div class="container-fluid p-0" data-aos="fade-up">



        <div class="row g-0">

          <div class="col-lg-6 video-play position-relative">

            <a href="https://www.youtube.com/watch?v=X2tm0WzBKbk" class="glightbox play-btn"></a>

          </div>

          <div class="col-lg-6">

            <div class="content d-flex flex-column justify-content-center h-100">

              <h3>Tingkatkan kompetensi anda bersama kami di Akuntanmu Learning Center</h3>

              <p class="fst-italic">

                Belajar mudah dan terstruktur dengan modul materi premium.

              </p>

              <ul>

                <li><i class="bi bi-check-circle"></i> Dipandu oleh konsultan dan praktisi berpengalaman</li>

                <li><i class="bi bi-check-circle"></i> Mendapatkan akses video dan modul pembelajaran lengkap</li>

                <li><i class="bi bi-check-circle"></i> Mendapatkan e-certificate dan pengakuan kompetensi dari Akuntanmu Learning Center bagi yang lolos assessment</li>

              </ul>

              <a href="https://wa.me/6282180744966" class="read-more align-self-start"><span>Gabung Sekarang</span><i class="bi bi-arrow-right"></i></a>

            </div>

          </div>

        </div>



      </div>

    </section><!-- End On Focus Section -->



    <!-- ======= Services Section ======= -->

    <section id="services" class="services">

      <div class="container" data-aos="fade-up">



        <div class="section-header">

          <h2>Layanan</h2>

          <p>Anda dapat memilih setiap layanan yang telah kami sediakan. Layanan tersebut diantaranya adalah:</p>

        </div>



        <div class="row gy-5">



          <div class="col-xl-4 col-md-6" data-aos="zoom-in" data-aos-delay="200" data-aos-duration="800">

            <div class="service-item">

              <div class="img" style="overflow: hidden; border-radius: 8px;">

                <?= img_lazy('assets/assetLanding/img/services-1.jpg',"loading", ['class' => 'img-fluid']) ?>

              </div>

              <div class="details position-relative">

                <div class="icon" style="transition: all 0.4s ease;">

                  <i class="bi bi-calculator-fill"></i>

                </div>

                <a href="#" class="stretched-link">

                  <h3>Aplikasi Akuntansi</h3>

                </a>

                <p>Kami menyediakan software akuntansi berbasis web yang bisa diakses kapan saja dan dimana saja.</p>

              </div>

            </div>

          </div><!-- End Service Item -->



          <div class="col-xl-4 col-md-6" data-aos="zoom-in" data-aos-delay="500" data-aos-duration="800">

            <div class="service-item">

              <div class="img" style="overflow: hidden; border-radius: 8px;">

                <?= img_lazy('assets/assetLanding/img/services-4.jpg',"loading", ['class' => 'img-fluid']) ?>

              </div>

              <div class="details position-relative">

                <div class="icon" style="transition: all 0.4s ease;">

                  <i class="bi bi-mortarboard-fill"></i>

                </div>

                <a href="#" class="stretched-link">

                  <h3>Pelatihan Kerja</h3>

                </a>

                <p>Kami menyediakan layanan pelatihan SKKNI untuk teknisi akuntansi dan Teknisi perpajakan bergelar Non-Akademik.</p>

                <a href="#" class="stretched-link"></a>

              </div>

            </div>

          </div><!-- End Service Item -->



          <div class="col-xl-4 col-md-6" data-aos="zoom-in" data-aos-delay="600" data-aos-duration="800">

            <div class="service-item">

              <div class="img" style="overflow: hidden; border-radius: 8px;">

                <?= img_lazy('assets/assetLanding/img/services-5.jpg',"loading", ['class' => 'img-fluid']) ?>

              </div>

              <div class="details position-relative">

                <div class="icon" style="transition: all 0.4s ease;">

                  <i class="bi bi-file-earmark-text-fill"></i>

                </div>

                <a href="#" class="stretched-link">

                  <h3>Brevet Pajak AB</h3>

                </a>

                <p>Kami memiliki LKP Akuntanmu Lerning Center sebagai pelaksana pelatihan brevet pajak terpadu..</p>

                <a href="#" class="stretched-link"></a>

              </div>

            </div>

          </div><!-- End Service Item -->


        </div>



      </div>

    </section><!-- End Services Section -->



    <!-- ======= Pricing Section ======= -->

    <section id="pricing" class="pricing">

      <div class="container" data-aos="fade-up">



        <div class="section-header">

          <h2>Berlangganan Akuntanmu</h2>

          <p>Anda dapat melakukan percobaan di sistem akuntanmu dengan biaya mulai dari 0,- rupiah. Kami benar-benar ingin anda merasakan pengalaman baik bersama kami dalam menentukan perubahan di masa mendatang</p>

        </div>



        <div class="row gy-4">



          <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="200">

            <div class="pricing-item">



              <div class="pricing-header">

                <h3>Gratis</h3>

                <h4><sup>Rp</sup>0<span> / bulan</span></h4>

              </div>



              <ul>

                <li><i class="bi bi-dot"></i> <span>Pengguna Maksimal 3</span></li>

                <li><i class="bi bi-dot"></i> <span>Daftar Akun Maksimal 500</span></li>

                <li><i class="bi bi-dot"></i> <span>Jurnal Entri Maksimal 500</span></li>

                <li><i class="bi bi-dot"></i> <span>Laporan Jurnal</span></li>

                <li><i class="bi bi-dot"></i> <span>Laporan Buku Besar</span></li>

                <li><i class="bi bi-dot"></i> <span>Laporan Posisi Keuangan</span></li>

                <li><i class="bi bi-dot"></i> <span>Laporan Laba Rugi</span></li>

                <li><i class="bi bi-dot"></i> <span>Laporan Perubahan Ekuitas</span></li>

                <li><i class="bi bi-dot"></i> <span>Cetak PDF-Laporan Jurnal</span></li>

                <li><i class="bi bi-dot"></i> <span>Cetak PDF-Laporan Buku Besar</span></li>

                <li><i class="bi bi-dot"></i> <span>Cetak PDF-Laporan Posisi Keuangan</span></li>

                <li><i class="bi bi-dot"></i> <span>Cetak PDF-Laporan Laba Rugi</span></li>

                <li><i class="bi bi-dot"></i> <span>Cetak PDF-Laporan Perubahan Ekuitas</span></li>

                <li class="na"><i class="bi bi-x"></i> <span>Export Excel-Laporan Buku Besar</span></li>

                <li class="na"><i class="bi bi-x"></i> <span>Export Excel-Laporan Posisi Keuangan</span></li>

                <li class="na"><i class="bi bi-x"></i> <span>Export Excel-Laporan Laba Rugi</span></li>

                <li class="na"><i class="bi bi-x"></i> <span>Upload Lampiran Bukti Transaksi</span></li>

                <li class="na"><i class="bi bi-x"></i> <span>Import Daftar Akun</span></li>

                <li><i class="bi bi-dot"></i> <span>Dashboard</span></li>

                <li><i class="bi bi-dot"></i> <span>Promosi/Iklan</span></li>

              </ul>



              <div class="text-center mt-auto">

                <a disabled class="buy-btn">Berlangganan</a>

              </div>



            </div>

          </div><!-- End Pricing Item -->



          <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="400">

            <div class="pricing-item featured">



              <div class="pricing-header">

                <h3>Bayar Tahunan</h3>

                <h4><sup>Rp</sup>75.000<span> / bulan</span></h4>

              </div>



              <ul>

                <li><i class="bi bi-dot"></i> <span>Pengguna Unlimited</span></li>

                <li><i class="bi bi-dot"></i> <span>Daftar Akun Unlimited</span></li>

                <li><i class="bi bi-dot"></i> <span>Jurnal Entri Unlimited</span></li>

                <li><i class="bi bi-dot"></i> <span>Laporan Jurnal</span></li>

                <li><i class="bi bi-dot"></i> <span>Laporan Buku Besar</span></li>

                <li><i class="bi bi-dot"></i> <span>Laporan Posisi Keuangan</span></li>

                <li><i class="bi bi-dot"></i> <span>Laporan Laba Rugi</span></li>

                <li><i class="bi bi-dot"></i> <span>Laporan Perubahan Ekuitas</span></li>

                <li><i class="bi bi-dot"></i> <span>Cetak PDF-Laporan Jurnal</span></li>

                <li><i class="bi bi-dot"></i> <span>Cetak PDF-Laporan Buku Besar</span></li>

                <li><i class="bi bi-dot"></i> <span>Cetak PDF-Laporan Posisi Keuangan</span></li>

                <li><i class="bi bi-dot"></i> <span>Cetak PDF-Laporan Laba Rugi</span></li>

                <li><i class="bi bi-dot"></i> <span>Cetak PDF-Laporan Perubahan Ekuitas</span></li>

                <li><i class="bi bi-dot"></i> <span>Export Excel-Laporan Buku Besar</span></li>

                <li><i class="bi bi-dot"></i> <span>Export Excel-Laporan Posisi Keuangan</span></li>

                <li><i class="bi bi-dot"></i> <span>Export Excel-Laporan Laba Rugi</span></li>

                <li><i class="bi bi-dot"></i> <span>Upload Lampiran Bukti Transaksi</span></li>

                <li><i class="bi bi-dot"></i> <span>Import Daftar Akun</span></li>

                <li><i class="bi bi-dot"></i> <span>Dashboard</span></li>

                <li><i class="bi bi-dot"></i> <span>Promosi/Iklan</span></li>

              </ul>

              <div class="text-center mt-auto">

                <a href="<?php echo base_url('Histori') ?>" class="buy-btn">Berlangganan</a>

              </div>



            </div>

          </div><!-- End Pricing Item -->



          <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="600">

            <div class="pricing-item">



              <div class="pricing-header">

                <h3>Bayar Bulanan</h3>

                <h4><sup>Rp</sup>150.000<span> / bulan</span></h4>

              </div>



              <ul>

                <li><i class="bi bi-dot"></i> <span>Pengguna Unlimited</span></li>

                <li><i class="bi bi-dot"></i> <span>Daftar Akun Unlimited</span></li>

                <li><i class="bi bi-dot"></i> <span>Jurnal Entri Unlimited</span></li>

                <li><i class="bi bi-dot"></i> <span>Laporan Jurnal</span></li>

                <li><i class="bi bi-dot"></i> <span>Laporan Buku Besar</span></li>

                <li><i class="bi bi-dot"></i> <span>Laporan Posisi Keuangan</span></li>

                <li><i class="bi bi-dot"></i> <span>Laporan Laba Rugi</span></li>

                <li><i class="bi bi-dot"></i> <span>Laporan Perubahan Ekuitas</span></li>

                <li><i class="bi bi-dot"></i> <span>Cetak PDF-Laporan Jurnal</span></li>

                <li><i class="bi bi-dot"></i> <span>Cetak PDF-Laporan Buku Besar</span></li>

                <li><i class="bi bi-dot"></i> <span>Cetak PDF-Laporan Posisi Keuangan</span></li>

                <li><i class="bi bi-dot"></i> <span>Cetak PDF-Laporan Laba Rugi</span></li>

                <li><i class="bi bi-dot"></i> <span>Cetak PDF-Laporan Perubahan Ekuitas</span></li>

                <li><i class="bi bi-dot"></i> <span>Export Excel-Laporan Buku Besar</span></li>

                <li><i class="bi bi-dot"></i> <span>Export Excel-Laporan Posisi Keuangan</span></li>

                <li><i class="bi bi-dot"></i> <span>Export Excel-Laporan Laba Rugi</span></li>

                <li><i class="bi bi-dot"></i> <span>Upload Lampiran Bukti Transaksi</span></li>

                <li><i class="bi bi-dot"></i> <span>Import Daftar Akun</span></li>

                <li><i class="bi bi-dot"></i> <span>Dashboard</span></li>

                <li><i class="bi bi-dot"></i> <span>Promosi/Iklan</span></li>

              </ul>



              <div class="text-center mt-auto">

                <a href="<?php echo base_url('Histori') ?>" class="buy-btn">Berlangganan</a>

              </div>



            </div>

          </div><!-- End Pricing Item -->



        </div>



      </div>

    </section><!-- End Pricing Section -->







    <!-- ======= F.A.Q Section ======= -->

    <section id="faq" class="faq">

      <div class="container-fluid" data-aos="fade-up">



        <div class="row gy-4">



          <div class="col-lg-7 d-flex flex-column justify-content-center align-items-stretch  order-2 order-lg-1">



            <div class="content px-xl-5">

              <h3>Seputar <strong>Pertanyaan</strong></h3>

              <p>

                Dibawah ini adalah pertanyaan-pertanyaan yang sering diajukan oleh pengguna.

              </p>

            </div>



            <div class="accordion accordion-flush px-xl-5" id="faqlist">



              <div class="accordion-item" data-aos="fade-up" data-aos-delay="200">

                <h3 class="accordion-header">

                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-content-1">

                    <i class="bi bi-question-circle question-icon"></i>

                    Apakah akuntanmu.com semua berbayar?

                  </button>

                </h3>

                <div id="faq-content-1" class="accordion-collapse collapse" data-bs-parent="#faqlist">

                  <div class="accordion-body">

                    Anda dapat menggunakan layanan sistem akuntanmu.com berbasis website dengan biaya Rp 0,- atau GRATIS, namun kami membatasi beberapa fitur dan akses.

                  </div>

                </div>

              </div><!-- # Faq item-->



              <div class="accordion-item" data-aos="fade-up" data-aos-delay="300">

                <h3 class="accordion-header">

                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-content-2">

                    <i class="bi bi-question-circle question-icon"></i>

                    Apakah harga layanan akuntanmu.com bisa di nego?

                  </button>

                </h3>

                <div id="faq-content-2" class="accordion-collapse collapse" data-bs-parent="#faqlist">

                  <div class="accordion-body">

                    Untuk seluruh harga layanan yang tertera dalam website merupakan harga tetap. Untuk negosiasi harga anda dapat mengajukan layanan jangka waktu tertentu.

                  </div>

                </div>

              </div><!-- # Faq item-->



              <div class="accordion-item" data-aos="fade-up" data-aos-delay="400">

                <h3 class="accordion-header">

                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-content-3">

                    <i class="bi bi-question-circle question-icon"></i>

                    Apakah ada panduan untuk mengoperasikan website akuntanmu.com?

                  </button>

                </h3>

                <div id="faq-content-3" class="accordion-collapse collapse" data-bs-parent="#faqlist">

                  <div class="accordion-body">

                    Kami telah menyediakan banyak video tentang tata cara penggunaan website akuntanmu.com melalui chanel youtube Akuntanmu Official.

                  </div>

                </div>

              </div><!-- # Faq item-->



              <div class="accordion-item" data-aos="fade-up" data-aos-delay="500">

                <h3 class="accordion-header">

                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-content-4">

                    <i class="bi bi-question-circle question-icon"></i>

                    Apakah akuntanmu.com memberikan komisi untuk rekomendasi?

                  </button>

                </h3>

                <div id="faq-content-4" class="accordion-collapse collapse" data-bs-parent="#faqlist">

                  <div class="accordion-body">

                    <i class="bi bi-question-circle question-icon"></i>

                    Kami akan memberikan komisi sebesar 10%-25% bagi anda yang terdaftar sebagai marketer dan berhasil membawa pengguna berbayar.

                  </div>

                </div>

              </div><!-- # Faq item-->



              <div class="accordion-item" data-aos="fade-up" data-aos-delay="600">

                <h3 class="accordion-header">

                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-content-5">

                    <i class="bi bi-question-circle question-icon"></i>

                    Apakah akuntanmu.com terdaftar resmi dengan badan hukum?

                  </button>

                </h3>

                <div id="faq-content-5" class="accordion-collapse collapse" data-bs-parent="#faqlist">

                  <div class="accordion-body">

                    Akuntanmu.com merupakan program dari divisi pendidikan dan riset PT Legalyn Konsultan Indonesia sehingga telah memiliki badan hukum resmi.

                  </div>

                </div>

              </div><!-- # Faq item-->



            </div>



          </div>



          <div class="col-lg-5 align-items-stretch order-1 order-lg-2 img" style="background-image: url('<?php echo base_url(); ?>/assets/assetLanding/img/faq.jpg')">&nbsp;</div>

        </div>



      </div>

    </section><!-- End F.A.Q Section -->



    <!-- ======= Team Section ======= -->

    <section id="team" class="team">

      <div class="container" data-aos="fade-up">



        <div class="section-header">

          <h2>Bersama Kami</h2>

          <p>Tim profesional kami akan membuat anda lebih lega dalam menjalankan bisnis serta menjaga aktifitas transaksi bisnis anda dengan basis standar peraturan perundang-undangan yang berlaku di indonesia.</p>

        </div>



        <div class="row gy-5">

          <?php

          $data = [

            [

              'nama'      => 'Nurtiyas',

              'gambar'    => 'tiyas.jpg',

              'posisi'    => 'Founder & CEO',

            ],

            [

              'nama'      => 'Zahra Ramadhani Syahada',

              'gambar'    => 'zahra.jpg',

              'posisi'    => 'Teknisi Akuntansi Pajak',

            ],
            [

              'nama'      => 'Deta Melania A',

              'gambar'    => 'deta.jpg',

              'posisi'    => 'Finance & Accounting',

            ],
            [

              'nama'      => 'Nikhrotin Nafilah',

              'gambar'    => 'nikhrotin.jpg',

              'posisi'    => 'Finance & Accounting',

            ],
           
            [

              'nama'      => 'Rio Ramadan',

              'gambar'    => 'rio.jpg',

              'posisi'    => 'Teknisi Akuntansi Pajak',

            ],
            [

              'nama'      => 'Mulyono',

              'gambar'    => 'mulyono.jpg',

              'posisi'    => 'IT Developer',

            ],
             [

              'nama'      => 'Fatwa Rahma Dian Elisza',

              'gambar'    => 'fatwa.jpg',

              'posisi'    => 'HR & Program',

            ],
            [

              'nama'      => 'Ricky Subagya',

              'gambar'    => 'ricky.jpg',

              'posisi'    => 'Media & Publikasi',

            ],
           


          ];

          ?>





          <div class="splide">

            <div class="splide__track">

              <div class="splide__list">

                <!--<div class="col-sm-4 col-6 splide__slide m-2">-->

                <?php foreach ($data as $rows) : ?>

                  <div class="col-xl-4 col-md-6 d-flex splide__slide m-2" data-aos="zoom-in" data-aos-delay="200">

                    <div class="team-member">

                      <div class="member-img">

                        <?= img_lazy('uploads/team/'. $rows['gambar'],"loading", ['class' => 'img-fluid']) ?>

                      </div>

                      <div class="member-info">

                        <div class="social">

                          <a href=""><i class="bi bi-twitter"></i></a>

                          <a href=""><i class="bi bi-facebook"></i></a>

                          <a href=""><i class="bi bi-instagram"></i></a>

                          <a href=""><i class="bi bi-linkedin"></i></a>

                        </div>

                        <h6><?= $rows['nama'] ?></h6>

                        <small><?= $rows['posisi'] ?></small>

                      </div>

                    </div>

                  </div>

                <?php endforeach; ?>

                <!--</div>-->

              </div>

            </div>

          </div>

        </div>



      </div>

    </section><!-- End Team Section -->



    <!-- ======= Contact Section ======= -->

    <section id="contact" class="contact">

      <div class="container">



        <div class="section-header">

          <h2>Hubungi Kami</h2>

          <p>Anda dapat menghubungi kapan saja dan kami akan menyambut anda dengan senang hati</p>

        </div>



      </div>



      <div class="map">

        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15890.160997219624!2d105.2722062!3d-5.334154!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e40c5e209a82e23%3A0x8e84be3bfe4f3bb!2sK-BIG%20OFFICE!5e0!3m2!1sid!2sid!4v1691587931782!5m2!1sid!2sid" frameborder="0" allowfullscreen></iframe>

      </div><!-- End Google Maps -->



      <div class="container">



        <div class="row gy-5 gx-lg-5">



          <div class="col-lg-4">



            <div class="info">

              <h3>Akuntanmu.com</h3>

              <p>Pengembang software akuntansi online umkm dan learning center di bidang akuntansi perpajakan</p>



              <div class="info-item d-flex">

                <i class="bi bi-geo-alt flex-shrink-0"></i>

                <div>

                  <h4>Kantor:</h4>

                  <p>KBIG OFFICE - Jl. Sawo Raya - Lampung</p>

                </div>

              </div><!-- End Info Item -->



              <div class="info-item d-flex">

                <i class="bi bi-envelope flex-shrink-0"></i>

                <div>

                  <h4>Email:</h4>

                  <p>support@akuntanmu.com</p>

                </div>

              </div><!-- End Info Item -->



              <div class="info-item d-flex">

                <i class="bi bi-phone flex-shrink-0"></i>

                <div>

                  <h4>Telephone:</h4>

                  <p>0821-8074-4966</p>

                </div>

              </div><!-- End Info Item -->



            </div>



          </div>



          <div class="col-lg-8">

            <form name="contact-me-nurtiyas" class="php-email-form">

              <div class="row">

                <div class="col-md-6 form-group">

                  <input type="text" name="name" class="form-control" id="name" placeholder="Nama Anda" required>

                </div>

                <div class="col-md-6 form-group mt-3 mt-md-0">

                  <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>

                </div>

              </div>

              <div class="form-group mt-3">

                <input type="text" class="form-control" name="subject" id="subject" placeholder="Judul" required>

              </div>

              <div class="form-group mt-3">

                <textarea class="form-control" name="message" placeholder="Tuliskan pesan anda disini..." required></textarea>

              </div>

              <button type="submit" class="btn btn-primary btn-send">Kirim Pesan</button>



              <button class="btn btn-primary btn-loading d-none" type="button" disabled>

                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...

              </button>

              <br>

              <br>

              <div class="alert alert-success alert-dismissible fade show d-none my-alert" role="alert">

                <strong>Berhasil.</strong> Kami telah menerima pesan anda

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

              </div>

            </form>

          </div><!-- End Contact Form -->



        </div>



      </div>

    </section><!-- End Contact Section -->



  </main><!-- End #main -->



  <!--//iklan-->
<?php if ($dataIklan->getNumRows() > 0): ?>
  <div class="modal fade" id="iklanDepan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">

    <div class="modal-dialog modal-dialog-centered">

      <div class="modal-content">

        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">

          <div class="carousel-indicators">
            <?php $no = 0;
            foreach ($dataIklan->getResult() as $rows) : ?>
              <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?= $no + 1; ?>" class="<?= $no == 0 ? "active" : ""; ?> " aria-current="true" aria-label="Slide <?= $no + 1; ?>"></button>
            <?php endforeach; ?>
          </div>

          <div class="carousel-inner">
            <?php foreach ($dataIklan->getResult() as $key=>$rows) : ?>
                <a href="<?= $rows->url != null ? $rows->url : '' ?>" target="_blank">
                  <div class="carousel-item <?= $key == '0'? 'active':'' ?>">
                    <?= img_lazy('uploads/iklan/thumbnails/'. $rows->file,"loading", ['class' => 'img-fluid img-thumbnail']) ?>
                  </div>
                </a>
            <?php endforeach; ?>
          </div>

          <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">

            <span class="carousel-control-prev-icon" aria-hidden="true"></span>

            <span class="visually-hidden">Previous</span>

          </button>

          <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">

            <span class="carousel-control-next-icon" aria-hidden="true"></span>

            <span class="visually-hidden">Next</span>

          </button>

        </div>

      </div>

    </div>

  </div>
<?php endif; ?>
  <a href="https://api.whatsapp.com/send?phone=6282180744966&text=Halo Akuntanmu,%20saya%20ingin%20bertanya..." class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-whatsapp"></i></a>



  <div id="preloader"></div>


<footer class="main-footer">
    <div class="container">
        
        <div class="collapse collapse-content" id="termsContent" data-bs-parent=".main-footer">
            <div class="content-body shadow-lg">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 mb-0 text-info">Syarat dan Ketentuan</h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-toggle="collapse" data-bs-target="#termsContent"></button>
                </div>
                <h5>A. PENDAHULUAN</h5>
                <p>Selamat datang di Akuntanmu.com ("kami", "kita", atau "website"). Kami menghargai privasi Anda dan berkomitmen untuk melindungi data pribadi Anda. Kebijakan privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi Anda saat Anda menggunakan layanan kami.</p>
                
                <h5>B. INFORMASI YANG KAMI KUMPULKAN</h5>
                <b>Informasi Pribadi:</b>
                <ul>
                    <li>Nama lengkap, Alamat email, Nomor Telepon, Alamat</li>
                    <li>Informasi pembayaran, Data pendidikan dan profesional</li>
                </ul>
                <b>Informasi Otomatis:</b>
                <ul>
                    <li>Alamat IP, Data browser, Informasi Perangkat</li>
                    <li>Cookie dan teknologi pelacakan serupa, Data penggunaan website</li>
                </ul>
                <h5>C. PENGGUNA INFORMASI</h5>
                <ul>
                    <li>Menyediakan dan mengelola layanan pendidikan</li>
                    <li>Memproses pembayaran dan mengirim pembaruan layanan</li>
                    <li>Meningkatkan pengalaman pengguna & materi pemasaran</li>
                    <li>Mematuhi kewajiban hukum</li>
                </ul>
                <h5>D. PENYIMPANAN DAN KEAMANAN DATA</h5>
                <p>Kami mengimplementasikan langkah-langkah keamanan yang sesuai untuk melindungi data Anda.</p>
                <h5>E. PEMBAGIAN INFORMASI</h5>
                <ul>
                    <li>Penyedia layanan pihak ketiga yang membantu operasional website</li>
                    <li>Mitra bisnis yang terkait dengan layanan kami</li>
                    <li>Otoritas hukum jika diwajibkan oleh hukum</li>
                </ul>
                <h5>F. HAK PENGGUNA</h5>
                <ul>
                    <li>Mengakses data pribadi Anda & Meminta koreksi data</li>
                    <li>Meminta penghapusan data & Membatasi penggunaan data</li>
                </ul>
                <h5>G. COOKIE</h5>
                <p>Website kami menggunakan cookie untuk meningkatkan pengalaman pengguna.</p>
                <h5>H. PERUBAHAN KEBIJAKAN PRIVASI</h5>
                <p>Kami berhak mengubah kebijakan privasi ini sewaktu-waktu.</p>
                <h5>I. HUKUM YANG BERLAKU</h5>
                <p>Kebijakan privasi ini tunduk pada hukum Republik Indonesia.</p>
                
                <div class="text-center mt-3">
                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="collapse" data-bs-target="#termsContent">Tutup</button>
                </div>
            </div>
        </div>

        <div class="collapse collapse-content" id="privacyContent" data-bs-parent=".main-footer">
            <div class="content-body shadow-lg">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 mb-0 text-info">Privacy Policy</h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-toggle="collapse" data-bs-target="#privacyContent"></button>
                </div>

                <h5>A. PENDAHULUAN</h5>
                <p>Selamat datang di Akuntabnmu.com ("Website"), yang dioperasikan oleh Legalyn Konsultan Indonesia. Dengan mengakses dan menggunakan website ini, Anda menyetujui untuk terikat oleh syarat dan ketentuan ini.</p>

                <h5>B. DEFINISI</h5>
                <ul>
                    <li>"Pengguna" atau "Anda" merujuk pada setiap individu yang mengakses website ini</li>
                    <li>"Layanan" merujuk pada semua produk, konten, dan jasa</li>
                </ul>

                <h5>C. PENGGUNAAN LAYANAN</h5>
                <p>Anda harus berusia minimal 18 tahun atau di bawah pengawasan orang tua/wali. Anda dilarang menggunakan website untuk tujuan ilegal atau menyalin materi tanpa izin.</p>

                <h5>D. PEMBAYARAN DAN PENGAMBILAN DANA</h5>
                <p>Semua pembayaran dianggap final kecuali ditentukan lain. Pengembalian dana diproses sesuai kebijakan yang berlaku.</p>

                <h5>E. HAK KEKAYAAN INTELEKTUAL</h5>
                <p>Seluruh konten di website dilindungi hak cipta. Penggunaan materi di luar keperluan pribadi dilarang.</p>

                <h5>F. BATAS TANGGUNG JAWAB</h5>
                <p>Website disediakan "sebagaimana adanya" tanpa jaminan apapun.</p>

                <h5>G. KEBIJAKAN PRIVASI</h5>
                <p>Penggunaan data pribadi Anda diatur dalam Kebijakan Privasi kami.</p>

                <h5>H. PERUBAHAN KETENTUAN</h5>
                <p>Kami berhak mengubah syarat dan ketentuan ini sewaktu-waktu.</p>

                <h5>I. PENYELESAIAN SENGKETA</h5>
                <p>Setiap sengketa akan diselesaikan secara musyawarah atau melalui pengadilan yang berwenang di Indonesia.</p>

                <h5>J. KETENTUAN LAINNYA</h5>
                <p>Jika ada ketentuan yang tidak sah, ketentuan lainnya tetap berlaku.</p>
                
                <div class="text-center mt-3">
                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="collapse" data-bs-target="#privacyContent">Tutup</button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <h5 class="footer-title">Informasi Kontak</h5>
                <div class="contact-info">
                    <p class="mb-2"><i class="bi bi-telephone"></i> 0821-8074-4966</p>
                    <p class="mb-2"><i class="bi bi-whatsapp"></i> 0821-8074-4966</p>
                    <p class="mb-2"><i class="bi bi-envelope"></i> support@akuntanmu.com</p>
                </div>
            </div>

            <div class="col-md-3">
                <h5 class="footer-title">Link Terkait</h5>
                <button class="footer-link" data-bs-toggle="collapse" data-bs-target="#privacyContent">Kebijakan Privasi</button>
                <button class="footer-link" data-bs-toggle="collapse" data-bs-target="#termsContent">Syarat dan Ketentuan</button>
            </div>

            <div class="col-md-3">
                <h5 class="footer-title">Head Office</h5>
                <p class="small mb-2 text-light">KBIG OFFICE - Jl. Sawo Raya - Lampung</p>
                <div class="map-container" style="height: 120px; border-radius: 8px; overflow: hidden;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d472.15062910337997!2d105.27275009310972!3d-5.3340326217695235!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e1!3m2!1sid!2sid!4v1769657243662!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>

            <div class="col-md-3">
                <h5 class="footer-title">Social Media</h5>
                <a href="#" class="social-link text-decoration-none text-secondary d-block mb-2"><i class="bi bi-facebook text-primary me-2"></i> Facebook</a>
                <a href="#" class="social-link text-decoration-none text-secondary d-block mb-2"><i class="bi bi-instagram text-danger me-2"></i> Instagram</a>
                <a href="#" class="social-link text-decoration-none text-secondary d-block mb-2"><i class="bi bi-youtube text-danger me-2"></i> Youtube</a>
                <a href="#" class="social-link text-decoration-none text-secondary d-block mb-2"><i class="bi bi-linkedin text-info me-2"></i> LinkedIn</a>
            </div>
        </div>
    </div>

    <div class="bottom-footer">
        <div class="container d-md-flex justify-content-between align-items-center text-center text-md-start">
            <span class="small text-light">Copyright © 2021 Akuntanmu. All Rights Reserved.</span>
            <span class="small text-light">Versi 2.0</span>
        </div>
    </div>
</footer>

  <!-- Vendor JS Files -->

  <script src="https://code.jquery.com/jquery-3.7.1.slim.js" integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>

  <script src="<?php echo (base_url('assets/assetLanding/vendor/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>

  <script src="<?php echo (base_url('assets/assetLanding/vendor/aos/aos.js')) ?>"></script>

  <script src="<?php echo (base_url('assets/assetLanding/vendor/glightbox/js/glightbox.min.js')) ?>"></script>

  <script src="<?php echo (base_url('assets/assetLanding/vendor/isotope-layout/isotope.pkgd.min.js')) ?>"></script>

  <script src="<?php echo (base_url('assets/assetLanding/vendor/swiper/swiper-bundle.min.js')) ?>"></script>

  <script src="https://kelasbrevet.com/assets/app-assets/template/cbt-malela/plugins/sweetalerts/sweetalert2.min.js"></script>


  <!-- Template Main JS File -->

  <script src="<?php echo (base_url('assets/assetLanding/js/main.js')) ?>"></script>

  <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.12/dist/js/splide.min.js"></script>

  <script>
    var splide = new Splide('.splide', {

      type: 'loop',

      perPage: 3,

      rewind: true,

      breakpoints: {

        640: {

          perPage: 2,

          gap: '.7rem',

          height: '12rem',

        },

        480: {

          perPage: 1,

          gap: '.7rem',

          height: '12rem',

        },

      },

    });

    splide.mount();

    // Enhanced scroll animations
    document.addEventListener('DOMContentLoaded', function() {
      // Intersection Observer for fade-in animations
      const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      };

      const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }
        });
      }, observerOptions);

      // Observe service items
      document.querySelectorAll('.service-item').forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        item.style.transition = 'all 0.6s ease';
        observer.observe(item);
      });

      // Add ripple effect to buttons
      document.querySelectorAll('.btn-join, .buy-btn').forEach(button => {
        button.addEventListener('click', function(e) {
          const ripple = document.createElement('span');
          const rect = this.getBoundingClientRect();
          const size = Math.max(rect.width, rect.height);
          const x = e.clientX - rect.left - size / 2;
          const y = e.clientY - rect.top - size / 2;
          
          ripple.style.width = ripple.style.height = size + 'px';
          ripple.style.left = x + 'px';
          ripple.style.top = y + 'px';
          ripple.classList.add('ripple');
          
          this.appendChild(ripple);
          
          setTimeout(() => {
            ripple.remove();
          }, 600);
        });
      });

      // Counter Animation for Stats
      function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-target'));
        const suffix = element.getAttribute('data-suffix') || '';
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
          current += increment;
          if (current >= target) {
            element.textContent = target + suffix;
            clearInterval(timer);
          } else {
            element.textContent = Math.floor(current) + suffix;
          }
        }, 16);
      }

      // Observe stats section
      const statsObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            document.querySelectorAll('.stat-number').forEach(stat => {
              if (!stat.classList.contains('counted')) {
                stat.classList.add('counted');
                animateCounter(stat);
              }
            });
          }
        });
      }, { threshold: 0.5 });

      const statsSection = document.querySelector('.stats-section');
      if (statsSection) {
        statsObserver.observe(statsSection);
      }
    });
  </script>



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



      fetch(scriptURL, {

          method: 'POST',

          body: new FormData(form)

        })

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

    setTimeout(function() {

      $('#iklanDepan').modal('show');

    }, 2000);



    const swiper = new Swiper('.sample-slider', {

      loop: true,

      autoplay: { //added

        delay: 2000, //added

      }, //added

      navigation: {

        nextEl: ".swiper-button-next",

        prevEl: ".swiper-button-prev",

      },

      pagination: {

        el: '.swiper-pagination',

      },

    })
  </script>

<script type="text/javascript">
    <?= session()->getFlashdata('pesan'); ?>
</script>
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