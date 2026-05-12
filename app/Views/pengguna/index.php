<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Page Heading -->
  <div class="card shadow mb-4">
    <form action="<?php echo site_url('pengguna/deleteAll') ?>" id="form-delete" method="post" enctype="multipart/form-data">
      <div class="card-header d-sm-flex align-items-center justify-content-between py-3">
        <h6 class="m-0 font-weight-bold text-info">Data Pengguna</h6>
        <div>
          <?php if (session()->get('databaseHitPengguna') <= session()->get('hitPengguna')) { ?>
            <button type="button" class="btn btn-sm btn-danger tooltips" id="btn-delete" data-toggle="tooltip" data-placement="left" title="Hapus data pengguna"><i class="fa fa-trash"></i></button>
            <a href="<?php echo ('pengguna/tambah') ?>" class="btn btn-sm btn-success shadow-sm tooltips" data-toggle="tooltip" data-placement="left" title="Tambah data pengguna"><i class="fas fa-plus fa-lg"></i></a>
          <?php } else { ?>
            <a href="<?php echo (site_url('Histori')) ?>" class="btn btn-sm btn-danger tooltips" data-toggle="tooltip" data-placement="left" title="Hapus data pengguna"><i class="fa fa-trash"></i></a>
            <a href="<?php echo (site_url('Histori')) ?>" class="btn btn-sm btn-success shadow-sm tooltips" data-toggle="tooltip" data-placement="left" title="Tambah data pengguna"><i class="fas fa-plus fa-lg"></i></a>
          <?php } ?>
        </div>
      </div>
      <div class="card-body">

        <?php
        $pesan = session()->get('pesan');
        if (!empty($pesan)) {
          echo $pesan;
        }
        ?>

        <!-- datatable -->
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-condesed" id="table">
            <thead>
              <tr class="success" style="background-color:#055F93; color: white;">
                <th style="width: 5%; text-align: center;"><input type="checkbox" id="check-all"></th>
                <th style="text-align: center;">Email</th>
                <th style="text-align: center;">Nama Pengguna</th>
                <th style="text-align: center;">Usename</th>
                <th style="text-align: center;">Akses Level</th>
                <th style="text-align: center; width: 15%;">Opsi</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
    </form>


  </div>
</div>


</div>
<!-- /.container-fluid -->

<script type="text/javascript">
  var table;

  $(document).ready(function() {

    //defenisi datatable
    table = $('#table').DataTable({
      "select": true,
      "processing": true,
      "serverSide": true,
      "order": [],
      "ajax": {
        "url": "<?php echo site_url('pengguna/datatablesource') ?>",
        "type": "POST",
        "error": function (xhr, error, thrown) {
            // Menampilkan error detail di Console Browser
            console.log("Status: " + xhr.status);
            console.log("Error: " + thrown);
            console.log("Response (Detail dari CodeIgniter): ", xhr.responseText);
            
            // Menampilkan alert ke user
            alert("Terjadi kesalahan saat memuat data. Silakan cek Console (F12) untuk detailnya.");
        }
      },
      "columnDefs": [{
          "targets": [0],
          "orderable": false,
          "className": 'dt-body-center'
        },
        {
          "targets": [1],
          "className": 'dt-body-left'
        },
        {
          "targets": [4],
          "className": 'dt-body-left'
        },
        {
          "targets": [5],
          "orderable": false,
          "className": 'dt-body-center'
        },
      ],
      "language": {
        "infoFiltered": ""
      }, 

    });

  }); //end (document).ready


  $(document).on("click", "#hapus", function(e) {
    var link = $(this).attr("href");
    e.preventDefault();
    bootbox.confirm("Anda yakin ingin menghapus data ini ?", function(result) {
      if (result) {
        document.location.href = link;
      }
    });
  });

  $(document).ready(function() {
    $("#check-all").click(function() {
      if ($(this).is(":checked"))
        $(".check-item").prop("checked", true);
      else
        $(".check-item").prop("checked", false);
    });

    $("#btn-delete").click(function(e) {
      e.preventDefault();
      if ($("#check-all").is(":checked")) {
        bootbox.confirm("Anda yakin ingin menghapus data ini ?", function(result) {
          if (result) {
            $("#form-delete").submit();
          }
        });

      } else if ($(".check-item").is(":checked")) {
        bootbox.confirm("Anda yakin ingin menghapus data ini ?", function(result) {
          if (result) {
            $("#form-delete").submit();
          }
        });
      } else {
        bootbox.confirm("silahkan pilih data yang akan di hapus?", function(result) {

        });

      }
    });
  });
</script>
<?= $this->endSection() ?>