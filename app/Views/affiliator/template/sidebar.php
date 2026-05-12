<?php
$level = session()->get('level');
?>
<!-- Sidebar -->
<!--<ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">-->
<ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion">

  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo (site_url()) ?>">
    <div class="sidebar-brand-icon">
      <!--<i class="fas fa-dice-d20"></i>-->
      <img src="<?= base_url() ?>/images/logo-akuntanmu1.png" width="50">
    </div>
    <div class="sidebar-brand-text mx-3">AKUNTANMU</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-2">

  <!-- Nav Item - Dashboard -->
  <li class="nav-item <?php echo ($menuaktif == 'Dashboard') ? 'active' : '' ?>">
    <a class="nav-link" href="<?php echo (site_url('Affiliator/Dashboard')) ?>">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span></a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider">

  <!-- Heading -->
  <div class="sidebar-heading">
    MENU UTAMA
  </div>

  <li class="nav-item <?php echo ($menuaktif == 'Pengguna') ? 'active' : '' ?> navtopmin">
    <a class="nav-link" href="<?php echo (site_url('Affiliator/Marketer')) ?>">
      <i class="fas fa-fw fa-user"></i>
      <span>Pengguna</span></a>
  </li>

  <li class="nav-item <?php echo ($menuaktif == 'Saldo') ? 'active' : '' ?> navtopmin">
    <a class="nav-link" href="<?php echo (site_url('Affiliator/Saldo')) ?>">
      <i class="fas fa-fw fa-money-bill"></i>
      <span>Saldo</span></a>
  </li>


  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block">

  <!-- Sidebar Toggler (Sidebar) -->
  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>

</ul>
<!-- End of Sidebar -->


<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

  <!-- Main Content -->
  <div id="content">