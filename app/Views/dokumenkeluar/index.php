<?= $this->extend('template/admin') ?>

<?= $this->section('content') ?>

<!-- Begin Page Content -->

<div class="container-fluid">



  <!-- Page Heading -->

  <div class="card shadow mb-4">

    <form action="<?php echo site_url('dokumen-keluar/deleteAll') ?>" id="form-delete" method="post" enctype="multipart/form-data">

      <div class="card-header d-sm-flex align-items-center justify-content-between py-3">

        <h6 class="m-0 font-weight-bold text-info">Dokumen Keluar <a href="#" class="text-decoration-none text-dark" onclick="copyText()"><i class="ml-2 fas fa-solid fa-copy"></i></a>

        </h6>



        <div>

          <button type="button" class="btn btn-sm btn-danger tooltips mx-1" id="btn-delete" data-toggle="tooltip" data-placement="left" title="Hapus data dokumen"><i class="fa fa-trash"></i></button>



          <a href="<?= site_url(" dokumen-keluar/tambah") ?>" class="btn btn-sm btn-success shadow-sm tooltips mx-1" data-toggle="tooltip" data-placement="left" title="Tambah data dokumen"><i class="fas fa-plus fa-lg"></i></a>

        </div>



      </div>

      <div class="card-body">



        <?php

        $pesan = session()->getFlashData('pesan');

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

                <th style="text-align: left;">Tanggal Arsip</th>

                <th style="text-align: left;">Nama File</th>

                <th style="text-align: left;">Nama Penerima</th>

                <th style="text-align: center; width: 15%;">Opsi</th>

              </tr>

            </thead>

          </table>

        </div>

    </form>



  </div>

</div>

</div>





<script type="text/javascript">
  var table;



  $(document).ready(function() {



    //defenisi datatable

    table = $(' #table').DataTable({

      "select": true,

      "processing": true,

      "serverSide": true,

      "order": [],

      "lengthMenu": [10, 100, 250, 500],

      "pageLength": 10,

      "ajax": {

        "url": "<?php echo site_url('DokumenKeluar/datatablesource') ?>",

        "type": "POST"

      },

      "columnDefs": [{

          "targets": [0],

          "orderable": false,

          "className": 'dt-body-center'

        }, {

          "targets": [1],

          "className": 'dt-body-left'

        }, {

          "targets": [2],

          "className": 'dt-body-left'

        }, {

          "targets": [3],

          "className": 'dt-body-left'

        },

        {

          "targets": [4],

          "className": 'dt-body-center'

        },

      ],

      "language": {

        "infoFiltered": ""

      }

    });

  }); //end 

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



  function copyText() {

    navigator.clipboard.writeText("<?= base_url('dokumen-arsip/' . encrypt(session()->get('idperusahaan'))); ?>");

  }
</script>

<?= $this->endSection() ?>