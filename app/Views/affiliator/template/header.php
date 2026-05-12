<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">


  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Akuntanmu.com | Software Akuntansi Online Berstandar</title>
  <link rel="shortcut icon" href="favicon.ico" />
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


  <style>
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
      /*background-color: #4CAF50;*/
      /*color: white;*/
      /*font-size: 16px;*/
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

    /*#accordionSidebar {
    height: 700px;
    overflow-y: scroll
    }*/

    /*#top {
    top:0px;
    min-width:0px;
  }*/



    /*scrool hanya div tertentu aja*/
    body {
      height: 100%;
      overflow: hidden;
      overflow-y: hidden;
    }


    #accordionSidebar {
      overflow: hidden;
    }

    .container-fluid {
      /*position:fixed;*/
      height: 78vh;
      overflow: scroll;
      overflow-x: hidden;

      /*width: 100%;*/
      /*width: fit-content; */
      /* To adjust the height as well */
      /*height: fit-content;*/
      /*height:400px;*/
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
  </style>

</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">