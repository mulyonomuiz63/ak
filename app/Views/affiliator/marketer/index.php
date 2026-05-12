<?= $this->extend('affiliator/template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">
  <!-- Page Heading -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-info">Data Pengguna</h6>
    </div>
    <div class="card-body">
      <?php
      $pesan = session()->getFlashData('pesan');
      if (!empty($pesan)) {
        echo $pesan;
      }
      ?>

      <form action="<?php echo (site_url('marketer/simpan')) ?>" id="form" method="post" enctype="multipart/form-data">

        <input type="hidden" name="idmarketer" id="idmarketer" value="<?php echo $marketer->idmarketer ?>">
        <input type="hidden" name="kode_referal" id="kode_referal" value="<?php echo $marketer->kode_referal ?>">

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="">Username</label>
              <input type="text" disabled value="<?= $marketer->username; ?>" class="form-control" placeholder="Username">
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label for="">Email</label>
              <input type="text" disabled value="<?= $marketer->email; ?>" class="form-control" placeholder="Email">
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group required">
              <label for="">Nama Lengkap</label>
              <input type="text" id="nama" name="nama" value="<?= $marketer->nama; ?>" class="form-control" placeholder="Nama Lengkap" <?= $marketer->status_kode_referal != '0' ? 'readonly' : '' ?>>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group required">
              <label for="">NIK</label>
              <input type="text" id="nik" name="nik" value="<?= $marketer->nik; ?>" onkeyup='lihatNik()' class="form-control" placeholder="NIK" <?= $marketer->status_kode_referal != '0' ? 'readonly' : '' ?>>
              <div>
                <span id='pesan_nik'></span>
              </div>
            </div>
          </div>

          <div class="col-4">
            <div class="form-group required">
              <label for="">Agama</label>
              <select name="agama" id="agama" class="form-control" <?= $marketer->status_kode_referal != '0' ? 'readonly' : '' ?>>
                <option value="Islam" <?= $marketer->agama == 'Islam' ? 'selected' : ''; ?>>Islam</option>
                <option value="Kristen" <?= $marketer->agama == 'Kristen' ? 'selected' : ''; ?>>Kristen</option>
                <option value="Katolik" <?= $marketer->agama == 'Katolik' ? 'selected' : ''; ?>>Katolik</option>
                <option value="Hindu" <?= $marketer->agama == 'Hindu' ? 'selected' : ''; ?>>Hindu</option>
                <option value="Budha" <?= $marketer->agama == 'Budha' ? 'selected' : ''; ?>>Budha</option>
              </select>
            </div>
          </div>

          <div class="col-4">
            <div class="form-group required">
              <label for="">Jenis Kelamin</label>
              <select name="jk" id="jk" class="form-control" <?= $marketer->status_kode_referal != '0' ? 'readonly' : '' ?>>
                <option value="L" <?= $marketer->jk == 'L' ? 'selected' : ''; ?>>Laki-Laki</option>
                <option value="P" <?= $marketer->jk == 'P' ? 'selected' : ''; ?>>Perempuan</option>
              </select>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group required">
              <label for="">No. Hp</label>
              <input type="text" id="nohp" name="nohp" value="<?= $marketer->nohp; ?>" onkeyup='lihatNohp()' class="form-control" placeholder="Nama Lengkap" <?= $marketer->status_kode_referal != '0' ? 'readonly' : '' ?>>
              <div>
                <span id='pesan_nohp'></span>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group required">
              <label for="">Tempat Lahir</label>
              <input type="text" id="tpt_lahir" name="tpt_lahir" value="<?= $marketer->tpt_lahir; ?>" class="form-control" placeholder="Tempat Lahir" <?= $marketer->status_kode_referal != '0' ? 'readonly' : '' ?>>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group required">
              <label for="">Tanggal Lahir</label>
              <input type="text" id="tanggal" name="tgl_lahir" value="<?= $marketer->tgl_lahir == null ? '' : date('d-m-Y', strtotime($marketer->tgl_lahir)) ?>" class="form-control" placeholder="Tanggal Lahir" <?= $marketer->status_kode_referal != '0' ? 'readonly' : '' ?>>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group required">
              <label for="">No Rekening</label>
              <input type="text" id="norek" name="norek" value="<?= $marketer->norek ?>" class="form-control" placeholder="No rek" <?= $marketer->status_kode_referal != '0' ? 'readonly' : '' ?>>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group required">
              <label for="">Bank</label>
              <input type="text" id="bank" name="bank" value="<?= $marketer->bank ?>" class="form-control" placeholder="Bank" <?= $marketer->status_kode_referal != '0' ? 'readonly' : '' ?>>
            </div>
          </div>

          <?php

          if ($marketer->kode_referal != null) { ?>
            <div class="col-md-4">
              <div class="form-group">
                <label for="">Kode Referal</label>
                <h6 class="font-weight-bold"><?= $marketer->kode_referal ?></h6>
                <!-- <input class="form-control" type="text" value="<?php echo base_url('Login/registrasi/' . $marketer->kode_referal) ?>"> -->
                <span style="font-weight: bold;">Share: <a target="_blank" href="<?= $share; ?>//send?text=<?php echo base_url('Login/registrasi/' . $marketer->kode_referal) ?>"> <svg style=";margin-left:10px;font-size:x-large;" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                      <style>
                        svg {
                          fill: #05ff50
                        }
                      </style>
                      <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
                    </svg></a></span>
              </div>
            </div>

          <?php }

          ?>

          <div class="col-md-4">
            <div class="form-group">
              <label for="">Password</label>
              <input type="text" id="password" name="password" value="" onkeyup='cek_password()' class="form-control" placeholder="Password">
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="">Ulangi Password</label>
              <input type="text" id="ulangiPassword" value="" onkeyup='cek_ulangiPassword()' class="form-control" placeholder="Ulangi Password">
              <span id='conf_pass'></span>
            </div>
          </div>



        </div>
        <hr>
        <div class="clearfix"></div>
        <div class="text-right">
          <a href="<?php echo (site_url('Affiliator/Marketer')) ?>" class="btn btn-danger">Kembali</a>
          <button type="submit" id="simpan" class="btn btn-success">Simpan</button>

        </div>
      </form>

    </div>
  </div>


</div>
<!-- /.container-fluid -->

<script type="text/javascript">
  function lihatNohp() {
    $("#pesan_nohp").hide();
    // ambil value nohp dari form
    var nohp = $("#nohp").val();
    // proses pengecekan nohp tersedia atau tidak.
    $.ajax({
      url: "<?php echo site_url() . 'marketer/cek-hp'; ?>",
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
      url: "<?php echo site_url() . 'marketer/cek-nik'; ?>",
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

  function cek_password() {
    var password = $("#password").val();
    var confirmPassword = $("#ulangiPassword").val();
    if (password != confirmPassword) {
      $("#conf_pass").css("color", "#fc5d32");
      $('#conf_pass').html('Sandi tidak sama');
      $('#simpan').prop('disabled', true);
    } else {
      $('#conf_pass').html('');
      $('#simpan').prop('disabled', false);
    }
    return true;
  };

  function cek_ulangiPassword() {
    var password = $("#password").val();
    var confirmPassword = $("#ulangiPassword").val();
    if (confirmPassword == "") {
      $('#simpan').prop('disabled', true);
    } else {
      if (password != confirmPassword) {
        $("#conf_pass").css("color", "#fc5d32");
        $('#conf_pass').html('Sandi tidak sama');
        $('#simpan').prop('disabled', true);
      } else {
        $('#conf_pass').html('');
        $('#simpan').prop('disabled', false);
      }
    }
    return true;
  };
</script>
<?= $this->endSection() ?>