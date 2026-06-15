<?php
$level = session()->get('level');

// [UPDATE CI4 TERBARU]: Mengubah ->uri->getSegment(1) menjadi ->getUri()->getSegment(1, '')
$uri = \Config\Services::request()->getUri()->getSegment(1, '');

if ($uri == 'perusahaan' || $uri == 'pengguna' || $uri == 'iklan' || $uri == 'event') {
  $menuPerusahaan = true;
} else {
  $menuPerusahaan = false;
}

if ($uri == 'akun' || $uri == 'jurnal') {
  $menuJurnal = true;
} else {
  $menuJurnal = false;
}

// [UPDATE CI4 TERBARU]: Menambahkan ->getUri() dan parameter default
$uri_sub = \Config\Services::request()->getUri()->getSegment(2, '');

if ($uri_sub == 'lapjurnal' || $uri_sub == 'lapbukubesar' || $uri_sub == 'lapposisikeuangan' || $uri_sub == 'laplabarugi' || $uri_sub == 'lapperubahanekuitas' || $uri_sub == 'laprasio' || $uri_sub == 'lapkoreksifiskal'  || $uri_sub == 'lapobjekpajak') {
  $menuLaporan = true;
} else {
  $menuLaporan = false;
}

if ($uri == 'diskon' || $uri == 'komisi' || $uri == 'harga' || $uri == 'histori' || $uri == 'komisi-marketer' || $uri == 'marketer') {
  $menuLangganan = true;
} else {
  $menuLangganan = false;
}

// [UPDATE CI4 TERBARU]: Menambahkan ->getUri() dan parameter default
$uri_sub_dokumen = \Config\Services::request()->getUri()->getSegment(1, '');

if ($uri_sub_dokumen == 'dokumen-masuk' || $uri_sub_dokumen == 'dokumen-keluar') {
  $menuDokumen = true;
} else {
  $menuDokumen = false;
}
?>
<!-- Sidebar -->
<!--<ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">-->
<ul class="navbar-nav nav-image bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">
  <div id="scroll">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo (site_url()) ?>">
      <div class="sidebar-brand-icon">
        <!--<i class="fas fa-dice-d20"></i>-->
        <?= img_lazy('images/logo-akuntanmu1.png', "loading", ['class' => 'logo-glow', 'width' => '50', 'height' => '50']) ?>
      </div>
      <div class="sidebar-brand-text mx-3">AKUNTANMU</div>
    </a>

    <!-- Divider -->
    <span class="font-size-perusahaan font-weight-bold text-white text-wrep mt-2 d-flex justify-content-center w-100 text-center"><?= session()->get('namaperusahaan') ?></span>
    <hr class="sidebar-divider my-2 mb-4 ogo-glow">

    <!-- Divider -->

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?php echo ($uri == 'Dashboard') ? 'active' : '' ?>">
      <a class="nav-link" href="<?php echo (site_url('dashboard')) ?>">
        <i class="fas fa-chart-bar"></i>
        <span class="font-size">Dashboard</span></a>
    </li>

    <!-- Divider -->

    <!-- Heading -->

    <?php if (session()->get('idpengguna') == '8888888888' || $level != '2') : ?>
      <li class="nav-item <?= $menuPerusahaan ? 'active' : ''; ?>">
        <a class="nav-link <?= $menuPerusahaan ? '' : 'collapsed'; ?>" href="#" data-toggle="collapse" data-target="#menuPerusahaan" aria-expanded="true" aria-controls="menuPerusahaan">
          <i class="fas fa-solid fa-landmark"></i>
          <span class="font-size">Perusahaan</span>
        </a>
        <div id="menuPerusahaan" class="collapse <?= $menuPerusahaan ? 'show' : ''; ?> mt-2" aria-labelledby=" headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <?php if (session()->get('idpengguna') == '8888888888') { ?>
              <a class="collapse-item <?php echo activate_menu('perusahaan', $uri); ?>" href="<?php echo (site_url('perusahaan')) ?>">
                <i class="fas fa-building"></i>
                <span class="ml-2">Profile</span>
              </a>
            <?php } else { ?>
              <?php if ($level != '2') : ?>
                <a class="collapse-item <?php echo activate_menu('perusahaan', $uri); ?>" href="<?php echo (site_url('perusahaan/edit/' . encrypt(session()->get('idperusahaan')))) ?>">
                  <i class="fas fa-building"></i>
                  <span class="ml-2">Profile</span>
                </a>
            <?php endif;
            } ?>

            <?php if (session()->get('idpengguna') == '8888888888' || $level == '1') { ?>
              <a class="collapse-item <?php echo activate_menu('pengguna', $uri); ?>" href="<?php echo (site_url('pengguna')) ?>">
                <i class="fas fa-fw fa-user"></i>
                <span class="ml-2">Pengguna</span>
              </a>
            <?php } ?>

            <?php if (session()->get('idpengguna') == '8888888888') { ?>
              <a class="collapse-item <?php echo activate_menu('iklan', $uri); ?>" href="<?php echo (site_url('iklan')) ?>">
                <i class="fas fa-regular fa-image"></i>
                <span class="ml-2">Iklan</span>
              </a>
            <?php } ?>

            <?php if (session()->get('idpengguna') == '8888888888') { ?>
              <a class="collapse-item <?php echo activate_menu('event', $uri); ?>" href="<?php echo (site_url('event')) ?>">
                <i class="fas fa-regular fa-image"></i>
                <span class="ml-2">Event</span>
              </a>
            <?php } ?>

          </div>
        </div>
      </li>
    <?php endif ?>


    <li class="nav-item <?= $menuJurnal ? 'active' : ''; ?>">
      <a class="nav-link <?= $menuJurnal ? '' : 'collapsed'; ?>" href="#" data-toggle="collapse" data-target="#menuJurnal" aria-expanded="true" aria-controls="menuJurnal">
        <i class="fas fa-solid fa-book"></i>
        <span class="font-size">Jurnal</span>
      </a>
      <div id="menuJurnal" class="collapse <?= $menuJurnal ? 'show' : ''; ?> mt-2" aria-labelledby=" headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <a class="collapse-item <?php echo activate_menu('akun', $uri); ?>" href="<?php echo (site_url('akun')) ?>">
            <i class="fas fa-fw fa-bars"></i>
            <span class="ml-2">Daftar Akun</span>
          </a>

          <a class="collapse-item <?php echo activate_menu('jurnal', $uri); ?>" href="<?php echo (site_url('jurnal')) ?>">
            <i class="far fa-edit"></i>
            <span class="ml-2">Data Jurnal</span>
          </a>
        </div>
      </div>
    </li>

    <li class="nav-item <?= $menuLaporan ? 'active' : ''; ?>">
      <a class="nav-link <?= $menuLaporan ? '' : 'collapsed'; ?>" href="#" data-toggle="collapse" data-target="#laporanMenu" aria-expanded="true" aria-controls="laporanMenu">
        <i class="fas fa-fw fa-file-invoice"></i>
        <span class="font-size ml-1">Laporan</span>
      </a>
      <div id="laporanMenu" class="collapse <?= $menuLaporan ? 'show' : ''; ?> mt-1" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded shadow-sm">
          <a class="collapse-item <?php echo activate_menu('lapjurnal', $uri_sub); ?>" href="<?php echo (site_url('laporan/lapjurnal')) ?>">
            <i class="fas fa-fw fa-clipboard-list text-info"></i>
            <span class="ml-1">Jurnal</span>
          </a>

          <a class="collapse-item <?php echo activate_menu('lapbukubesar', $uri_sub); ?>" href="<?php echo (site_url('laporan/lapbukubesar')) ?>">
            <i class="fas fa-fw fa-book-open text-info"></i>
            <span class="ml-1">Buku Besar</span>
          </a>

          <a class="collapse-item <?php echo activate_menu('lapposisikeuangan', $uri_sub); ?>" href="<?php echo (site_url('laporan/lapposisikeuangan')) ?>">
            <i class="fas fa-fw fa-building text-info"></i>
            <span class="ml-1">Posisi Keuangan</span>
          </a>

          <a class="collapse-item <?php echo activate_menu('laplabarugi', $uri_sub); ?>" href="<?php echo (site_url('laporan/laplabarugi')) ?>">
            <i class="fas fa-fw fa-chart-pie text-info"></i>
            <span class="ml-1">Laba Rugi</span>
          </a>

          <a class="collapse-item <?php echo activate_menu('lapperubahanekuitas', $uri_sub); ?>" href="<?php echo (site_url('laporan/lapperubahanekuitas')) ?>">
            <i class="fas fa-fw fa-sync-alt text-info"></i>
            <span class="ml-1">Perubahan Ekuitas</span>
          </a>

          <a class="collapse-item <?php echo activate_menu('laprasio', $uri_sub); ?>" href="<?php echo (site_url('laporan/laprasio')) ?>">
            <i class="fas fa-fw fa-chart-line text-info"></i>
            <span class="ml-1">Rasio Keuangan</span>
          </a>

          

          <a class="collapse-item <?php echo activate_menu('lapobjekpajak', $uri_sub); ?>" href="<?php echo (site_url('laporan/lapobjekpajak')) ?>">
            <i class="fas fa-fw fa-coins text-info"></i>
            <span class="ml-1">Objek Pajak</span>
          </a>

          <a class="collapse-item <?php echo activate_menu('lapkoreksifiskal', $uri_sub); ?>" href="<?php echo (site_url('laporan/lapkoreksifiskal')) ?>">
            <i class="fas fa-fw fa-balance-scale text-info"></i>
            <span class="ml-1">Korfis Fiskal</span>
          </a>
        </div>
      </div>
    </li>

    <li class="nav-item <?= $menuDokumen ? 'active' : ''; ?>">
      <!-- <a class="nav-link <?= $menuDokumen ? '' : 'collapsed'; ?>" href="<?php echo (site_url('dokumen-masuk')) ?>" data-toggle="collapse" data-target="#dokumenmenu" aria-expanded="true" aria-controls="dokumenmenu"> -->
      <a class="nav-link <?= $menuDokumen ? '' : 'collapsed'; ?>" href="<?php echo (site_url('dokumen-masuk')) ?>">
        <i class=" fas fa-tasks"></i>
        <span class="font-size">Data Masuk</span>
      </a>
    </li>

    <!-- Divider -->
    <?php if (session()->get('idpengguna') == '8888888888' || $level != '2') : ?>

      <li class="nav-item <?= $menuLangganan ? 'active' : ''; ?>">
        <a class="nav-link <?= $menuLangganan ? '' : 'collapsed'; ?>" href="#" data-toggle="collapse" data-target="#menuLangganan" aria-expanded="true" aria-controls="menuLangganan">
          <i class="fas fa-trophy"></i>
          <span class="font-size">Langganan</span>
        </a>
        <div id="menuLangganan" class="collapse <?= $menuLangganan ? 'show' : ''; ?> mt-2" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <?php if (session()->get('idpengguna') == '8888888888') { ?>

              <a class="collapse-item <?php echo activate_menu('diskon', $uri); ?>" href="<?php echo (site_url('diskon')) ?>">
                <i class="fas fa-fw fa-percent"></i>
                <span class="ml-2">Diskon</span>
              </a>

              <a class="collapse-item <?php echo activate_menu('komisi', $uri); ?>" href="<?php echo (site_url('komisi')) ?>">
                <i class="fas fa-fw fa-money-bill"></i>
                <span class="ml-2">Komisi</span>
              </a>

              <a class="collapse-item <?php echo activate_menu('harga', $uri); ?>" href="<?php echo (site_url('harga')) ?>">
                <i class="fas fa-fw fa-money-bill"></i>
                <span class="ml-2">Harga</span>
              </a>

              <a class="collapse-item <?php echo activate_menu('marketer', $uri); ?>" href="<?php echo (site_url('marketer')) ?>">
                <i class="fas fa-fw fa-user"></i>
                <span class="ml-2">Marketer</span>
              </a>

              <a class="collapse-item <?php echo activate_menu('histori', $uri); ?>" href="<?php echo (site_url('histori')) ?>">
                <i class="fas fa-fw fa-globe"></i>
                <span class="ml-2">Histori</span>
              </a>

              <a class="collapse-item <?php echo activate_menu('komisi-marketer', $uri); ?>" href="<?php echo (site_url('komisi-marketer')) ?>">
                <i class="fas fa-fw fa-money-bill"></i>
                <span class="ml-2">Komisi Marketer</span>
              </a>



            <?php } else {  ?>
              <a class="collapse-item <?php echo activate_menu('histori', $uri); ?>" href="<?php echo (site_url('histori')) ?>">
                <i class="fas fa-fw fa-globe"></i>
                <span class="ml-2 font-size">Histori</span>
              </a>
            <?php } ?>
          </div>
        </div>
      </li>

    <?php endif; ?>

    <!--Petunjuk-->
    <li class="nav-item">
      <a class="nav-link" href="#">
        <i class="fas fa-question-circle"></i>
        <span class="badge-notif font-size tooltips" data-toggle="tooltip" data-placement="top" title="Coming Soon">Petunjuk</span>
        <!--<span class="ml-2 badge-notif" data-badge="Coming Soon">Petunjuk</span>-->
      </a>
    </li>

    <!--Petunjuk-->
    <li class="nav-item">
      <a class="nav-link" href="<?php echo (site_url('logout')) ?>" data-toggle="#" data-target="#" aria-expanded="true" aria-controls="#">
        <i class="fas fa-sign-out-alt"></i>
        <span class="font-size">Keluar</span>
      </a>
    </li>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="d-flex justify-content-center mb-4">
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>
    </div>
  </div>

</ul>
<!-- End of Sidebar -->


<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

  <!-- Main Content -->
  <div id="content">