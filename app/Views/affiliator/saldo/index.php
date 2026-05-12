<?= $this->extend('affiliator/template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">
  <!-- Page Heading -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <small>Minimal penarikan dana Rp. 100.000, admin akan memproses penarikan dana di waktu jam oprasional kantor senin-jum'at jam 09.00 wib - 17.00 wib</small>
    </div>
    <div class="card-body">
      <?php
      $pesan = session()->getFlashData('pesan');
      if (!empty($pesan)) {
        echo $pesan;
      }
      ?>

      <div class="row">
        <div class="col-md-3">
          <label for="">Saldo</label>
          <h4><?= 'Rp. ' . number_format($marketer->saldo, 0, ".", "."); ?></h4>
        </div>
        <div class="col-md-3 mt-4">
          <?php if ($marketer->saldo >= 100000) { ?>
            <a href="<?php echo (site_url('Affiliator/Saldo/tarikdana/' . $marketer->encryptkey . '/' . $marketer->saldo)) ?>" class="btn btn-success">Ajukan Penarikan</a>
          <?php } else { ?>
            <button class="btn btn-success" disabled>Ajukan Penarikan</button>
          <?php } ?>
        </div>
      </div>
      <hr>
      <div class="clearfix"></div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-condesed" id="table">
          <thead>
            <tr class="success" style="background-color:#055F93; color: white;">
              <th style="text-align: center;">Marketer</th>
              <th style="text-align: center;">Status</th>
              <th style="text-align: center;">Pengajuan</th>
              <th style="text-align: center;">Pencairan</th>
              <th style="text-align: center;">Nominal</th>
            </tr>
          </thead>
        </table>
      </div>

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
      "searching": false,
      "filter": false,
      "order": [],
      "lengthMenu": [10, 100, 250, 500],
      "pageLength": 100,
      "ajax": {
        "url": "<?php echo site_url('Affiliator/Saldo/datatablesource') ?>",
        "type": "POST"
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
          "targets": [2],
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
      ],
      "language": {
        "infoFiltered": ""
      }

    });

  }); //end (document).ready


  function lihatNohp() {
    $("#pesan_nohp").hide();
    // ambil value nohp dari form
    var nohp = $("#nohp").val();
    // proses pengecekan nohp tersedia atau tidak.
    $.ajax({
      url: "<?php echo site_url() . 'Affiliator/Marketer/cekNohp'; ?>",
      data: 'nohp=' + nohp,
      type: "POST",
      success: function(msg) {
        if (msg == 1) {
          $("#pesan_nohp").css("color", "#fc5d32");
          $("#pesan_nohp").html("Maaf nohp sudah digunakan.");
          $('#nohp').val('');

        } else if (msg == 3) {
          $("#pesan_nohp").css("color", "#fc5d32");
          $("#pesan_nohp").html("No Hp harus angka");
          $('#nohp').val('');
        } else {
          $("#pesan_nohp").html("");
        }
        $("#pesan_nohp").fadeIn(1000);
      }
    });

  }

  function lihatNik() {
    $("#pesan_nik").hide();
    // ambil value nik dari form
    var nik = $("#nik").val();
    // proses pengecekan nik tersedia atau tidak.
    $.ajax({
      url: "<?php echo site_url() . 'Affiliator/Marketer/cekNik'; ?>",
      data: 'nik=' + nik,
      type: "POST",
      success: function(msg) {
        if (msg == 1) {
          $("#pesan_nik").css("color", "#fc5d32");
          $("#pesan_nik").html("Maaf nik sudah digunakan.");
          $('#nik').val('');

        } else if (msg == 3) {
          $("#pesan_nik").css("color", "#fc5d32");
          $("#pesan_nik").html("NIK tidak valid");
          $('#nik').val('');
        } else {
          $("#pesan_nik").html("");
        }
        $("#pesan_nik").fadeIn(1000);
      }
    });

  }

  $(document).ready(function() {


    //----------------------------------------------------------------- > validasi dan proses simpan data disini
    $('#form').bootstrapValidator({
      feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
      },
      fields: {

        nama: {
          validators: {
            notEmpty: {
              message: 'Nama tidak boleh kosong'
            },
            stringLength: {
              max: 50,
              message: 'Panjang Karakter maksimal 50'
            },
          }
        },
        nik: {
          validators: {
            notEmpty: {
              message: 'Nik tidak boleh kosong'
            },
            stringLength: {
              max: 16,
              message: 'Panjang Karakter maksimal 16'
            },
          }
        },
        agama: {
          validators: {
            notEmpty: {
              message: 'agama tidak boleh kosong'
            },

          }
        },
        jk: {
          validators: {
            notEmpty: {
              message: 'Jenis kelamin tidak boleh kosong'
            },
          }
        },
        nohp: {
          validators: {
            notEmpty: {
              message: 'No Hp tidak boleh kosong'
            },
            stringLength: {
              max: 15,
              message: 'Panjang Karakter maksimal 15'
            },
          }
        },
        tpt_lahir: {
          validators: {
            notEmpty: {
              message: 'Tempat lahir tidak boleh kosong'
            },
          }
        },
        tgl_lahir: {
          validators: {
            notEmpty: {
              message: 'Tanggal lahir tidak boleh kosong'
            },
          }
        },

        norek: {
          validators: {
            notEmpty: {
              message: 'No rekening tidak boleh kosong'
            },
            regexp: {
              regexp: '^[0-9]+$',
              message: 'Harus angka'
            },

          }
        },
        bank: {
          validators: {
            notEmpty: {
              message: 'Bank tidak boleh kosong'
            },
          }
        },
      }
    });
    //------------------------------------------------------------------------> END VALIDASI DAN SIMPAN
  }); //end (document).ready

  $(document).on("click", "#simpan", function(e) {
    e.preventDefault();
    bootbox.confirm("Pastikan data yang di inputkan benar, data yang sudah di simpan tidak bisa di ubah lagi. ?", function(result) {
      if (result) {
        $("#simpan").submit();
      }
    });
  });
</script>
<?= $this->endSection() ?>