<?= $this->extend('template/admin') ?>
<?= $this->section('css') ?>
    <style>
        .dataTable tbody tr td {
          padding: 3px 6px !important;
          line-height: 1 !important;
        }
    </style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Page Heading -->
  <div class="card shadow mb-4">
    <form action="<?php echo site_url('akun/delete-all') ?>" id="form-delete" method="post" enctype="multipart/form-data">
      <div class="card-header d-sm-flex align-items-center justify-content-between py-3">
        <!--<h6 class="m-0 font-weight-bold text-info">Data Akun </h6>-->
        <div class="row">
            <?php if (session()->get('idpengguna') === '8888888888') { ?>
                <div class="col-12">
                  <input type="hidden" id="idperusahaan" name="idperusahaan">
                  <input type="text" id="tampilperusahaan" class="form-control" value="" placeholder="Cari nama perusahaan..">
                </div>
            <?php }else{ ?>
                <div class="col-12">
                    <h6 class="m-0 font-weight-bold text-info">Data Akun </h6>
                </div>
            <?php } ?>
        </div>
        <?php if(session()->get('level_nama') !='Supervisor' ): ?>
            <?php if (session()->get('databaseHitAkun') <= session()->get('hitAkun')) { ?>
              <div>
                <button type="button" class="btn btn-sm btn-danger tooltips mx-1" id="btn-delete" data-toggle="tooltip" data-placement="left" title="Hapus data akun"><i class="fa fa-trash"></i></button>
                <?php if (session()->get('hitLanggana')) { ?>
                  <button type="button" class="btn btn-sm btn-primary tooltips mx-1" data-toggle="modal" data-target="#importExcel" data-placement="left" title="Untuk upload data akun">
                    <i class="fa fa-upload" aria-hidden="true"></i>
                  </button>
                  <a href="<?php echo (site_url('akun/export-excel')) ?>" class="btn btn-secondary btn-sm tooltips mx-1" target="_blank" data-toggle="tooltip" data-placement="left" title="Untuk export data"><i class="fa fa-file-excel"></i></a>
                <?php } else { ?>
                  <a href="<?php echo (site_url('histori')) ?>" class="btn btn-sm btn-primary tooltips mx-1">
                    <i class="fa fa-upload" aria-hidden="true"></i>
                  </a>
                  <a href="<?php echo (site_url('histori')) ?>" class="btn btn-secondary btn-sm tooltips mx-1" data-toggle="tooltip" data-placement="left" title="Untuk export data"><i class="fa fa-file-excel"></i></a>
                <?php } ?>
                <?php
                if (session()->get('level') == '1' || session()->get('level') == '2') {
                ?>
    
                  <a href="<?= site_url("akun/tambah"); ?>" class="btn btn-sm btn-success shadow-sm tooltips mx-1" data-toggle="tooltip" data-placement="left" title="Tambah data akun"><i class="fas fa-plus fa-lg"></i></a>
                  <?php
                  if (session()->get("idpengguna") != "8888888888") {
                    if (session()->get("status_akun") == "L") { ?>
                      <a href="javascript:;" onclick="isconfirm('<?php echo site_url('akun/update-kode-akun') ?>')" class="btn btn-sm btn-warning shadow-sm tooltips mx-1" data-toggle="tooltip" data-placement="left" title="Tambah jumlah kode akun"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-node-plus" viewBox="0 0 16 16">
                          <path fill-rule="evenodd" d="M11 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8M6.025 7.5a5 5 0 1 1 0 1H4A1.5 1.5 0 0 1 2.5 10h-1A1.5 1.5 0 0 1 0 8.5v-1A1.5 1.5 0 0 1 1.5 6h1A1.5 1.5 0 0 1 4 7.5zM11 5a.5.5 0 0 1 .5.5v2h2a.5.5 0 0 1 0 1h-2v2a.5.5 0 0 1-1 0v-2h-2a.5.5 0 0 1 0-1h2v-2A.5.5 0 0 1 11 5M1.5 7a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z" />
                        </svg></a>
                <?php
                    }
                  }
                }
                ?>
              </div>
            <?php } else { ?>
              <div>
                <button type="button" class="btn btn-sm btn-danger tooltips mx-1" id="btn-delete" data-toggle="tooltip" data-placement="left" title="Hapus data akun"><i class="fa fa-trash"></i></button>
                <a href="<?php echo (site_url('Histori')) ?>" class="btn btn-secondary btn-sm float-right tooltips mx-1" data-toggle="tooltip" data-placement="left" title="Untuk export data"><i class="fa fa-file-excel"></i></a>
                <a href="<?php echo (site_url('Histori')) ?>" class="btn btn-sm btn-primary tooltips mx-1">
                  <i class="fa fa-upload" aria-hidden="true"></i>
                </a>
                <?php
                if (session()->get('level') == '1' || session()->get('level') == '2') {
                  echo '
              <a href="' . site_url("Histori") . '" class="btn btn-sm btn-success shadow-sm tooltips mx-1" data-toggle="tooltip" data-placement="left" title="Tambah data akun"><i class="fas fa-plus fa-lg"></i></a>
            ';
                }
                ?>
              </div>
            <?php } ?>
        <?php endif; ?>
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
                <th style="text-align: center;">Kode Akun</th>
                <th style="text-align: center;">Nama Akun</th>
                <th style="text-align: center;">Saldo Normal</th>
                <th style="text-align: center;">Level</th>
                <th style="text-align: center;">Status</th>
                <th style="text-align: center; width: 15%;">Opsi</th>
              </tr>
            </thead>
          </table>
        </div>
    </form>

  </div>
</div>
</div>
<div class="modal fade" id="importExcel" tabindex="-1" aria-labelledby="importExcelLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="<?php echo (site_url('akun/store-excel')) ?>" id="form" method="post" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="importExcelLabel">Import Data Akun</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="post" action="/siswa/simpanExcel" enctype="multipart/form-data">
          <div class="mx-4">
            <div class="form-group">
              <input type="file" style="color: #212121; border:none" name="fileexcel" class="form-control" id="file" required accept=".xls, .xlsx" /></p>
            </div>
            <div class="d-flex justify-content-end">
              <span class="mr-2">Download template</span>
              <a href="<?php echo (site_url('akun/export')) ?>" class="btn btn-secondary btn-sm float-right tooltips" target="_blank" data-toggle="tooltip" data-placement="left" title="Download template excel"><i class="fa fa-file-excel"></i></a>
            </div>

            <div class="form-group">
              <button class="btn btn-primary" type="submit">Upload</button>
            </div>
          </div>
        </form>
      </div>
    </form>
  </div>
</div>
<!-- /.container-fluid -->
<script src="<?php echo (base_url('assets/jquery-ui/jquery-ui-2.js')) ?>"></script>
<script type="text/javascript">
  var t;

  $(document).ready(function() {

    //defenisi datatable
    t = $('#table').DataTable({
      "select": true,
      "processing": true,
      "serverSide": true,
      "order": [],
      "lengthMenu": [10, 100, 250, 500],
      "pageLength": 100,
      "ajax": {
        "url": "<?php echo site_url('akun/datatablesource') ?>",
        "type": "POST",
        data: function(d) {
          d.idperusahaan = $('input[name=idperusahaan]').val();
        },
      },
      "columnDefs": [{
          "targets": [0],
          "orderable": false,
          "className": 'dt-body-center'
        },
        {
          "targets": [1],
          "className": 'dt-body-center'
        },
        {
          "targets": [3],
          "className": 'dt-body-center'
        },
        {
          "targets": [4],
          "className": 'dt-body-center'
        },
        {
          "targets": [5],
          "className": 'dt-body-center'
        },
        {
          "targets": [6],
          "orderable": false,
          "className": 'dt-body-center'
        },
      ],
      "language": {
        "infoFiltered": ""
      }

    });

  }); //end (document).ready


  $(document).on("click", "#hapus", function(e) {
    var link = $(this).attr("href");
    e.preventDefault();
    if (link == "#") {
      bootbox.confirm("Akun master tidak dapat dihapus", function(result) {

      });
    } else {
      bootbox.confirm("Anda yakin ingin menghapus data ini ?", function(result) {
        if (result) {
          document.location.href = link;
        }
      });
    }
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


  function isconfirm(url_val) {

    bootbox.confirm("Apakah anda yakin akan menambah jumlah nomor akun?", function(result) {
      if (result) {
        location.href = url_val;

      }
    });
  }
  
    $("#tampilperusahaan").autocomplete({

          minLength: 0,
    
          source: function(request, response) {
    
            $.ajax({
    
              type: "POST",
    
              url: "<?php echo site_url('jurnal/autocomplatePerusahaan'); ?>",
    
              dataType: "json",
    
              data: {
    
                term: request.term
    
              },
    
              success: function(data) {
                response(data);
    
              }
    
            });
    
          },
    
          select: function(event, ui) {
            $('#idperusahaan').val(ui.item.idperusahaan)
            $('#tampilperusahaan').val(ui.item.namaperusahaan)
            reload();
            return false;
    
          }
    
    }).autocomplete("instance")._renderItem = function(ul, item) {
      return $("<li>")
        .append("<div><b>" + item.namaperusahaan + " </b></div>")

        .appendTo(ul);

    };
    
    function reload() {
      t.draw();
    }
        
</script>
<?= $this->endSection() ?>