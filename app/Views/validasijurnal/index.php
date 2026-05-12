<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">


  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="author" content="Akuntanmu">

  <title>Akuntanmu.com | Software Akuntansi Online Berstandar</title>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" />
  <!-- Google Fonts Roboto -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" />
  <!-- MDB -->
  <!-- <link rel="stylesheet" href="css/bootstrap-login-form.min.css" /> -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://kelasbrevet.com/assets/app-assets/template/cbt-malela/plugins/sweetalerts/sweetalert.css" rel="stylesheet" type="text/css">
  <script src="https://kelasbrevet.com/assets/app-assets/template/cbt-malela/plugins/sweetalerts/sweetalert2.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
  <script type="text/javascript" src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
</head>

<body>
  <!-- Start your project here-->

  <style>
    .divider:after,
    .divider:before {
      content: "";
      flex: 1;
      height: 1px;
      background: #eee;
    }

    .h-custom {
      height: calc(100% - 73px);
    }

    @media (max-width: 450px) {
      .h-custom {
        height: 100%;
      }
    }
  </style>
  <section class="vh-100">
    <div class="container-fluid h-custom">
      <div class="row  h-100 d-flex justify-content-center align-items-center mt-4">
        <div class="col-md-8 col-lg-6 col-xl-4 d-flex justify-content-center">
          <img src="../../images/verifikasi.png" alt="Sample image" width="70%">
        </div>
        <div class="col-md-9 col-lg-6 col-xl-5 offset-xl-1">
            <div class="form-content">
                <p>
                    <a href="<?php echo base_url('/') ?>">
                        <div class="text-center mb-2">
                          <img src="../../images/logo-akuntanmu.png" width="220">
                        </div>
                    </a>
                </p>
                <table id="datatable-table" class="table ">
                    <tr>
                        <td>Perusahaan</td>
                        <td><?= $namaperusahaan ?></td>
                    </tr>
                    <tr>
                        <td>Tgl Transaksi</td>
                        <td><?= date('d-m-Y', strtotime($rsData->getRow()->tgljurnal)) ?></td> 
                    </tr>
                     <tr>
                        <td>Nomor Jurnal</td> 
                        <td><?= $rsDataJurnal->idjurnal ?></td>
                    </tr>
                     <tr>
                        <td>Referensi</td>
                        <td><?= ($rsData->getRow()->referensi == '' ? '-' : $rsData->getRow()->referensi) ?></td>
                    </tr>
                    <tr>
                        <td>Keterangan</td>
                        <td><?= $rsData->getRow()->keterangan ?></td>
                    </tr>
                </table>
                <table id="datatable-table" class="table">
                    <thead>

						<tr>
							<th width="50%" style="font-size:12px; font-weight:bold; text-align:center;border:1px solid gray;">Akun</th>
							<th width="14%" style="font-size:12px; font-weight:bold; text-align:center;border:1px solid gray;">No. Akun</th>
							<th width="18%" style="font-size:12px; font-weight:bold; text-align:center;border:1px solid gray;">Debit</th>
							<th width="18%" style="font-size:12px; font-weight:bold; text-align:center;border:1px solid gray;">Kredit</th>
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
							<td width="50%" style="font-size:12px; text-align:left;border:1px solid gray;"><?= ($data->debet == 0 ? str_repeat("&nbsp;", 5) : "") . $data->nmakun ?></td>
							<td width="14%" style="font-size:12px; text-align:center;border:1px solid gray;"><?= $data->kdakun ?></td>
							<td width="18%" style="font-size:12px; text-align:right;border:1px solid gray;"><?= ($data->debet == 0 ? "" : number_format($data->debet)) ?></td>
							<td width="18%" style="font-size:12px; text-align:right;border:1px solid gray;"><?= ($data->kredit == 0 ? "" : number_format($data->kredit)) ?></td>			
						</tr>
						<?php } ?>
					<tbody>
                </table>
            </div>
        </div>
      </div>
    </div>

  </section>
  <!-- End your project here-->
  <!-- Bootstrap core JavaScript-->
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery/jquery.min.js')) ?>"></script>
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?php echo (base_url('assets/sb-admin-2/vendor/jquery-easing/jquery.easing.min.js')) ?>"></script>

  <!-- Custom scripts for all pages-->
  <script src="<?php echo (base_url('assets/sb-admin-2/js/sb-admin-2.min.js')) ?>"></script>
  <script type="text/javascript">
        <?= session()->getFlashdata('pesan'); ?>
  </script>

</body>

</html>