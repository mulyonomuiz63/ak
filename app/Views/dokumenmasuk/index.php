<?= $this->extend('template/admin') ?>

<?= $this->section('content') ?>

<!-- Begin Page Content -->

<div class="container-fluid">



  <!-- Page Heading -->

  <div class="card shadow mb-4">

    <form action="<?php echo site_url('dokumen-masuk/deleteAll') ?>" id="form-delete" method="post" enctype="multipart/form-data">

      <div class="card-header d-sm-flex align-items-center justify-content-between py-3">

        <h6 class="m-0 font-weight-bold text-info">Data Masuk <a href="#" class="text-decoration-none text-dark" onclick="copyText()"><i class="ml-2 fas fa-solid fa-copy"></i></a></h6>

        <div>
        <?php if(session()->get('level_nama') !='Supervisor' ): ?>
              <button type="button" class="btn btn-sm btn-danger tooltips mx-1" id="btn-delete" data-toggle="tooltip" data-placement="left" title="Hapus data dokumen"><i class="fa fa-trash"></i></button>
              <a href="<?= site_url(" dokumen-masuk/tambah") ?>" class="btn btn-sm btn-success shadow-sm tooltips mx-1" data-toggle="tooltip" data-placement="left" title="Tambah data dokumen"><i class="fas fa-plus fa-lg"></i></a>
        <?php endif; ?>
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

                <th style="text-align: left;">Tanggal</th>

                <th style="text-align: left;">Keterangan Dokumen</th>

                <th style="text-align: left;">Pemilik</th>

                <th style="text-align: center; width: 15%;">Opsi</th>

              </tr>

            </thead>

          </table>

        </div>

    </form>



  </div>

</div>

</div>



<!--modal cetak pdf-->
<div class="modal fade" id="modalcetakpdf" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="isiKonten"></div>
        </div>
    </div>
</div>
<!--modal cetak pdf-->

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

        "url": "<?php echo site_url('dokumen-masuk/datatablesource') ?>",

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
    alert("Link berhasil disalin.")

  }
  
  $(document).on("click", "#cetak-pdf", function(e) {
        const url = $(this).data('cetak_pdf');
          $(".isiKonten").html(`
            <div class="modal-header">
                <h5 class="modal-title">Dokumen Masuk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- Gunakan tinggi tetap misal 75vh agar modal tidak menembus layar -->
            <div class="modal-body p-0" style="position: relative; height: 75vh; overflow: hidden;">
                
                <!-- Indikator Loading (Tampil di tengah) -->
                <div id="loadingIframe" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; z-index: 10;">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="mt-3 font-weight-bold text-muted">Sedang menarik banyak data...<br>Mohon tunggu sebentar.</div>
                </div>

                <!-- Iframe (Awalnya disembunyikan menggunakan opacity) -->
                <iframe id="frameBukuBesar" src="${url}" width="100%" height="100%" style="border: none; opacity: 0; transition: opacity 0.5s; position: relative; z-index: 5;"></iframe>
                
            </div>
        `);

        // 2. Deteksi kapan Iframe selesai memuat SELURUH data dari server
        $("#frameBukuBesar").on("load", function() {
          // Sembunyikan loading
          $("#loadingIframe").fadeOut();

          // Tampilkan iframe secara halus
          $(this).css("opacity", "1");
        });
    });
</script>

<?= $this->endSection() ?>