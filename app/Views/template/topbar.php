<?php
    $db = Config\Database::connect();
    $idpenggunaN = session()->get('idpengguna');
    $notif_jurnal = $db->query("
                        SELECT COUNT(idjurnal) AS total
                        FROM v_jurnal
                        WHERE idpengguna = $idpenggunaN
                        AND (
                            approve = '2'
                            OR (
                                approve = '1'
                                AND keterangan_approve != ''
                            )
                        );
                        ");

    // SELECT count(idjurnal) total FROM v_jurnal where idpengguna = $idpenggunaN and approve='2'
    $ss = $notif_jurnal->getRowObject();
    if(!empty($ss)){
        if($ss->total < 10){
            $notif = $ss->total;
        }else{
            $notif = '9+';
        }
    }else{
        $notif = '0';
    }
?>
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-1 static-top shadow" id="top">

  <!-- Sidebar Toggle (Topbar) -->
  <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3 text-white">
    <i class="fa fa-bars"></i>
  </button>

  <form action="<?php echo  site_url('jurnal')  ?>" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" method="post" enctype="multipart/form-data">
    <div class="input-group">
      <input type="text" class="form-control bg-light border-0 small" placeholder="Pencarian..." aria-label="Search" aria-describedby="basic-addon2" name="cari" value="<?= service('uri')->getSegment(1) == "jurnal" && service('uri')->getSegment(2) == "" ? $cari : ""; ?>" id="search-form" autocomplete="off">
      <div class="input-group-append">
        <button class="btn btn-info" type="submit">
          <i class="fas fa-search fa-sm"></i>
        </button>
      </div>
    </div>
  </form>

  <!-- Topbar Navbar -->
  <ul class="navbar-nav ml-auto">

    <!-- Nav Item - Search Dropdown (Visible Only XS) -->
    <li class="nav-item dropdown no-arrow d-sm-none">
      <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-search fa-fw"></i>
      </a>
      <!-- Dropdown - Messages -->
      <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
        <form class="form-inline mr-auto w-100 navbar-search">
          <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
              <button class="btn btn-primary" type="button">
                <i class="fas fa-search fa-sm"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </li>


    <!-- Nav Item - User Information -->
    <?php if($notif != '0'){ ?>
    <div class="ms-auto d-flex align-items-center tooltips" data-toggle="tooltip" data-placement="left" title="<?= $notif ?> Jurnal perlu perbaikan.">
      <!-- Icon Lonceng -->
      <a href="<?= base_url('jurnal/notif') ?>" class=" position-relative">
        <i class="bi bi-bell-fill text-light" style="font-size: 1.3rem;"></i>
        <!-- Badge Notif -->
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-white">
            <?= $notif ?>
          </span>
      </a>
    </div>
    <?php } ?>
    
    <!-- Nav Item - User Information -->
    <li class="nav-item dropdown no-arrow ml-4">
      <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <div class="navbar-profile">
          <div class="profile-info d-none d-lg-inline text-white">
            <p class="name"><?php echo (session()->get('namapengguna')) ?></p>
            <p class="level"><?php echo (session()->get('level_nama')) ?></p>
          </div>
            <?php
            if (session()->get('foto') == '') {
    
              $foto = base_url('./images/avatar.jpeg');
            } else {
              $foto = base_url('./uploads/pengguna/' . session()->get('foto'));
            }
    
            ?>
            <img class="img-profile rounded-circle" src="<?php echo ($foto) ?>" width="30">
        </div>
      </a>
      <!-- Dropdown - User Information -->
      <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
        <a class="dropdown-item" href="<?php echo (site_url('logout')) ?>">
          <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
          Logout
        </a>
      </div>
    </li>

  </ul>

</nav>
<!-- End of Topbar -->