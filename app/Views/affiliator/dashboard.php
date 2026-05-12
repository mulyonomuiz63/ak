<?= $this->extend('affiliator/template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Page Heading -->

  <!-- Content Row -->
  <div class="row">

    <!-- Earnings (Monthly) Card Example -->
    <?php if ($marketer->saldo >= 100000) { ?>
      <a class="col-xl-3 col-md-6 mb-4 text-decoration-none" href="<?php echo (site_url('Affiliator/Saldo/tarikdana/' . $marketer->encryptkey . '/' . $marketer->saldo)) ?>">
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Komisi</div>
                <div class="h5 mb-0 mt-2 font-weight-bold text-gray-800" style="font-size: 16px;"><?= 'Rp. ' . number_format($marketer->saldo, 0, ".", "."); ?></div>
              </div>
              <div class="col-auto">
                <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </a>
    <?php } else { ?>
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Komisi</div>
                <div class="h5 mb-0 mt-2 font-weight-bold text-gray-800" style="font-size: 16px;"><?= 'Rp. ' . number_format($marketer->saldo, 0, ".", "."); ?></div>
              </div>
              <div class="col-auto">
                <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>


  </div>

</div>
<!-- /.container-fluid -->
<?= $this->endSection() ?>