<!DOCTYPE html>
<html lang="id">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Verifikasi Jurnal Akuntanmu">
  <meta name="author" content="Akuntanmu">

  <title>Akuntanmu.com | Software Akuntansi Online Berstandar</title>
  
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://kelasbrevet.com/assets/app-assets/template/cbt-malela/plugins/sweetalerts/sweetalert.css" rel="stylesheet" type="text/css">
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
  <script type="text/javascript" src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
  <script src="https://kelasbrevet.com/assets/app-assets/template/cbt-malela/plugins/sweetalerts/sweetalert2.min.js"></script>

  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f4f7f6; /* Latar belakang abu-abu terang */
    }
    .card-custom {
      border: none;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08); /* Bayangan lembut */
    }
    .table-jurnal th {
      background-color: #f8f9fa;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 13px;
      color: #495057;
      vertical-align: middle;
    }
    .table-jurnal td {
      font-size: 14px;
      vertical-align: middle;
      color: #212529;
    }
    .info-label {
      font-weight: 500;
      color: #6c757d;
      width: 35%;
    }
    .info-value {
      font-weight: 600;
      color: #212529;
    }
  </style>
</head>

<body>

  <section class="min-vh-100 d-flex align-items-center py-5">
    <div class="container">
      <div class="row align-items-center justify-content-center g-5">
        
        <div class="col-12 col-lg-5 text-center d-none d-md-block">
          <img src="../../images/verifikasi.png" alt="Verifikasi Jurnal" class="img-fluid" style="max-width: 80%;">
        </div>

        <div class="col-12 col-md-10 col-lg-7">
          <div class="card card-custom p-4 p-md-5">
            <div class="card-body p-0">
              
              <div class="text-center mb-4">
                <a href="<?php echo base_url('/') ?>">
                  <img src="../../images/logo-akuntanmu.png" class="img-fluid" width="220" alt="Logo Akuntanmu">
                </a>
              </div>

              <h5 class="text-center fw-bold mb-4 text-primary">Detail Jurnal Transaksi</h5>

              <div class="table-responsive mb-4">
                <table class="table table-sm table-borderless m-0">
                  <tbody>
                    <tr>
                      <td class="info-label">Perusahaan</td>
                      <td>: <span class="info-value"><?= $namaperusahaan ?></span></td>
                    </tr>
                    <tr>
                      <td class="info-label">Tgl Transaksi</td>
                      <td>: <span class="info-value"><?= date('d-m-Y', strtotime($rsData->getRow()->tgljurnal)) ?></span></td>
                    </tr>
                    <tr>
                      <td class="info-label">Nomor Jurnal</td>
                      <td>: <span class="info-value"><?= $rsDataJurnal->idjurnal ?></span></td>
                    </tr>
                    <tr>
                      <td class="info-label">Referensi</td>
                      <td>: <span class="info-value"><?= ($rsData->getRow()->referensi == '' ? '-' : $rsData->getRow()->referensi) ?></span></td>
                    </tr>
                    <tr>
                      <td class="info-label">Keterangan</td>
                      <td>: <span class="info-value"><?= $rsData->getRow()->keterangan ?></span></td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <hr class="mb-4">

              <div class="table-responsive">
                <table class="table table-bordered table-hover table-jurnal mb-0">
                  <thead class="text-center">
                    <tr>
                      <th width="40%" class="text-start">Akun</th>
                      <th width="20%">No. Akun</th>
                      <th width="20%">Debit</th>
                      <th width="20%">Kredit</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                      $total1 = 0;
                      $total2 = 0;
                      foreach ($rsData->getResult() as $data) {
                        $total1 = $total1 + $data->debet;
                        $total2 = $total2 + $data->kredit;
                    ?>
                    <tr>
                      <td class="text-start">
                        <?= ($data->debet == 0 ? str_repeat("&nbsp;", 5) : "") . $data->nmakun ?>
                      </td>
                      <td class="text-center"><?= $data->kdakun ?></td>
                      <td class="text-end"><?= ($data->debet == 0 ? "-" : number_format($data->debet, 0, ',', '.')) ?></td>
                      <td class="text-end"><?= ($data->kredit == 0 ? "-" : number_format($data->kredit, 0, ',', '.')) ?></td>      
                    </tr>
                    <?php } ?>
                  </tbody>
                  <tfoot class="table-light fw-bold text-end">
                    <tr>
                      <td colspan="2" class="text-center text-uppercase">Total</td>
                      <td><?= number_format($total1, 0, ',', '.') ?></td>
                      <td><?= number_format($total2, 0, ',', '.') ?></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              
            </div>
          </div>
        </div>
        
      </div>
    </div>
  </section>

  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery/jquery.min.js')) ?>"></script>
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>

  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery-easing/jquery.easing.min.js')) ?>"></script>

  <script src="<?php echo (base_url('assets/sb-admin-2/js/sb-admin-2.min.js')) ?>"></script>
  
  <script type="text/javascript">
      // Cek apakah ada flashdata pesan, jika ada jalankan sweetalert/logikanya
      <?= session()->getFlashdata('pesan'); ?>
  </script>

</body>

</html>