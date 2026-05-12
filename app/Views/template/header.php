<!DOCTYPE html>
<html lang="en">

<head>
  <!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> -->


  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <meta http-equiv="<?php echo cek_langganan(); ?>" content="5" />



  <title>Akuntanmu.com | Software Akuntansi Online Berstandar</title>
  <link rel="shortcut icon" href="<?= base_url("favicon.ico") ?>" />
  <!-- Custom fonts for this template-->
  <link href="<?php echo (base_url('assets/sb-admin-2/vendor/fontawesome-free/css/all.min.css')) ?>" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="<?php echo (base_url('assets/sb-admin-2/css/sb-admin-2.css')) ?>" rel="stylesheet">

  <!-- datatables -->
  <link href="<?php echo (base_url('assets/datatables/css/jquery.dataTables.min.css')) ?>" rel="stylesheet">

  <!-- jquery-confirm -->
  <link rel="stylesheet" href="<?php echo (base_url('assets/jquery-confirm/css/jquery-confirm.min.css')) ?>">

  <!-- jquery-ui -->
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery/jquery.min.js')) ?>"></script>
  <link rel="stylesheet" href="<?php echo (base_url('assets/jquery-ui/themes/base/jquery-ui.css')) ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php
        $db = Config\Database::connect();
        $nav_imgs = $db->query("SELECT file FROM iklan where status = 'navbar' LIMIT 1 ");
        $nav = $nav_imgs->getRowObject();
        if(!empty($nav)){
            $nav_img = base_url('uploads/iklan/thumbnails/'.$nav->file);
        }else{
            $nav_img = base_url('uploads/nav-sidebar/top.jpg?v=3');
        }
        
        $sidebar_imgs = $db->query("SELECT file FROM iklan where status = 'sidebar' LIMIT 1 ");
        $sidebar = $sidebar_imgs->getRowObject();
        if(!empty($sidebar)){
            $sidebar_img = base_url('uploads/iklan/thumbnails/'.$sidebar->file);
        }else{
            $sidebar_img = base_url('uploads/nav-sidebar/sidebar.jpg?v=3');
        }
    ?>
  <style>
    .navbar-nav .nav-link {
        padding-right: 0;
        padding-left: 0;
    }
    .font-size{
        font-size:14px !important;
    }
    .ui-autocomplete {
      max-height: 400px;
      overflow-y: auto;
      /* prevent horizontal scrollbar */
      overflow-x: hidden;
    }

    /* IE 6 doesn't support max-height
   * we use height instead, but this forces the menu to always be this tall
   */
    * html .ui-autocomplete {
      height: 100px;
    }

    .has-error .help-block {
      color: red;
    }


    .required label {
      font-weight: bold;
    }

    .required label:after {
      color: #e32;
      content: ' * wajib';
      font-style: italic;
      font-size: 12px;
      display: inline;
    }

    .materijudul {
      font-size: 16px;
      font-weight: bold;
      color: #242323;
      text-align: center;
    }

    .cardmateri .image {
      opacity: 1;
      display: block;
      width: 100%;
      height: auto;
      transition: .5s ease;
      backface-visibility: hidden;
    }

    .cardmateri:hover .image {
      opacity: 0.3;
    }

    .cardmateri .middle {
      transition: .5s ease;
      opacity: 0;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      -ms-transform: translate(-50%, -50%);
      text-align: center;
    }

    .cardmateri:hover .middle {
      opacity: 1;
    }

    .cardmateri .text {
      padding: 16px 32px;
    }

    .cardmateri .premium {
      transition: .5s ease;
      position: absolute;
      top: 5%;
      left: 35%;
      transform: translate(-50%, -50%);
      -ms-transform: translate(-50%, -50%);
      height: 20px;
    }

    .cardmateri .premium .imagepremium {
      opacity: 1;
      display: block;
      width: 35%;
      transition: .5s ease;
      backface-visibility: hidden;
      left: 10%;
    }

    .botpage {
      position: absolute;
      right: 0;
      bottom: 0;
    }

    .th-jurnal th {
      height: 40px;
      font-weight: bold;
    }


    /*scrool hanya div tertentu aja*/
    body {
      height: 100%;
      overflow: hidden;
      overflow-y: hidden;
    }


    /*#accordionSidebar {*/
    /*  overflow: hidden;*/
    /*}*/

    .container-fluid {
      /*position:fixed;*/
      height: 90vh;
      overflow: scroll;
      overflow-x: hidden;
    }


    .nav-item {
      margin-top: -10px;
    }

    .sidebar-heading {
      margin-bottom: 20px;
    }

    .dropdown-item:hover {
      color: #fff;
      background-color: #055F93;
    }


    #scroll {
      position: relative;   /* penting biar z-index jalan */
      z-index: 1040;
      will-change: transform;
    }
    
    
    .tooltip-inner {
      background-color: #FF0000; /* contoh: biru */
      color: #fff;               /* teks tetap putih */
      font-size: 0.9rem;         /* optional */
    }
    
    /* Optional: ganti border/panah tooltip */
    .tooltip.bs-tooltip-top .arrow::before {
      border-top-color: #FF0000;
    }
    .tooltip.bs-tooltip-bottom .arrow::before {
      border-bottom-color: #FF0000;
    }
    .tooltip.bs-tooltip-left .arrow::before {
      border-left-color: #FF0000;
    }
    .tooltip.bs-tooltip-right .arrow::before {
      border-right-color: #FF0000;
    }







    /* baru */
    .loader {
      margin: auto;
      border: 10px solid #EAF0F6;
      border-radius: 50%;
      border-top: 10px solid #055F93;
      width: 50px;
      height: 50px;
      animation: spinner 4s linear infinite;
      position: absolute;
    }

    @keyframes spinner {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    .badge-notif {
      position: relative;
    }

    .badge-notif[data-badge]:after {
      content: attr(data-badge);
      position: absolute;
      top: -15px;
      right: -50px;
      padding: 1px;
      font-size: .7em;
      background: #e53935;
      color: white;
      text-align: center;
      border-radius: 10%;
    }
    
    #loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5); /* hitam transparan */
      z-index: 9999;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    
    .navbar  {
      background: url('<?= $nav_img ?>') center/cover no-repeat;
      height: 56px;
    }
    
    .nav-image  {
      background: url('<?= $sidebar_img ?>') center/cover no-repeat;
      height: 56px;
    }
    
     /*//untuk profil*/
    .navbar-profile {
      display: flex;
      align-items: center;
    }
    .navbar-profile img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      margin-left: 10px;
    }
    .profile-info {
      display: flex;
      flex-direction: column;
      text-align: right;
    }
    .profile-info .name {
      font-weight: bold;
      margin: -2px;
    }
    .profile-info .level {
      font-size: 0.75rem;
      font-weight: 200;
      margin: -2px;
    }
    
    .font-size-perusahaan{
        font-size:10px;
    }
    .logo-glow {
        box-shadow: 0 0 5px rgba(255, 255, 255, 0.8),
                    0 0 1px rgba(255, 255, 255, 0.6),
                    0 0 2px rgba(255, 255, 255, 0.4);
        border-radius: 12px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .logo-glow:hover {
        transform: scale(1.08);
        box-shadow: 0 0 15px rgba(255, 255, 255, 1),
                    0 0 30px rgba(255, 255, 255, 0.8),
                    0 0 60px rgba(255, 255, 255, 0.6);
    }
    .dataTable tbody tr td {
      padding: 8px 6px !important;
      line-height: 1 !important;
    }
    
    
    /* Chat box */
    .chat-box {
      position: fixed;
      bottom: 70px;
      left: 20px;
      width: 320px;
      max-height: 500px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      display: none;
      flex-direction: column;
      overflow: hidden;
      z-index: 1050;
    }
    
    .whatsapp-button {
        position: fixed;
        width: 50;
        height: 50;
        bottom: 20px;
        left:50px;
        background-color:	#4169E1;
        color: #fff;
        border-radius: 100%;
        text-align: center;
        font-size: 20px;
        z-index: 100;
         transition: all 0.3s ease;
         animation: zoomGlow 2s ease-in-out infinite;
    }
    @keyframes zoomGlow {
      0% {
        transform: scale(1);
        box-shadow: 0 0 10px rgba(37, 211, 102, 0.5);
      }
      50% {
        transform: scale(1.2);
        box-shadow: 0 0 25px rgba(37, 211, 102, 0.9);
      }
      100% {
        transform: scale(1);
        box-shadow: 0 0 10px rgba(37, 211, 102, 0.5);
      }
    }
  </style>
  
  <?= $this->renderSection('css'); ?>

</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">