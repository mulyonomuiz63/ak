<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">
  <!-- Page Heading -->

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-info" id="lbljudul"></h6>
    </div>
    <div class="card-body">


      <form action="<?php echo (site_url('pengguna/store')) ?>" id="form" method="post" enctype="multipart/form-data">
        <div class="row">
          <input type="hidden" id="idpengguna" name="idpengguna" class="form-control" readonly="">
          <div class="col-md-12">
            <div class="row">

              <div class="col-6">
                <div class="form-group required">
                  <label for="">Nama Pengguna</label>
                  <input type="text" id="namapengguna" name="namapengguna" class="form-control" placeholder="Nama Pengguna" autofocus="">
                  <input type="hidden" name="file" id="file" accept="image/*" value="">
                </div>
              </div>
              <div class="col-6">
                <div class="form-group required">
                  <label for="">Akses Level</label>
                  <select name="level" id="level" class="form-control" <?php echo ($idpengguna == '8888888888') ? 'readonly=""' : '' ?>>
                    <option value="">Pilih akses level...</option>
                    <option value="1">Admin</option>
                    <option value="2">Staff</option>
                    <option value="3">Supervisor</option>
                    <?php
                    if ($idpengguna == '8888888888') {
                      echo  '<option value="9">Super Admin</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group required">
                  <label for="">Username</label>
                  <input type="text" id="username" name="username" onkeyup='lihatUsername()' class="form-control" placeholder="Username">
                  <span id='pesan_username'></span>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="">Email</label>
                  <input type="text" id="email" name="email" onkeyup='lihatEmail()' class="form-control" placeholder="Email">
                  <span id='pesan_email'></span>
                </div>
              </div>

              <div class="col-6">
                <div class="form-group">
                  <label for="">Password (Opsional)</label>
                  <div class="input-group" id="password">
                    <input type="password" name="password" class="form-control" placeholder="Password">
                    <div class="input-group-append input-group-text d-flex align-items-center">
                      <a href="" class=" text-decoration-none">
                        <i class="far fa-eye-slash"></i>
                      </a>
                    </div>
                  </div>
                </div>
                <input type="hidden" name="password_lama" id="password_lama">

              </div>
              <div class="col-6" id="tanda-tangan">
                <div class="form-group">
                  <label for="">Tanda Tangan Pengguna </label><br />
                  <select name="" id="pilih-tanda-tangan" class="form-control">
                    <option value="">Pilih Jenis Tanda Tangan</option>
                    <option value="1">Tanda Tangan Digital</option>
                    <option value="2">Upload Foto Tanda Tangan</option>
                  </select><br />

                  <div id="signature-pad">
                    <div style="border:solid 1px teal; width:360px;height:110px;padding:3px;position:relative;">
                      <div id="note" onmouseover="my_function();">Tanda Tangan Disini</div>
                      <canvas id="the_canvas" width="350px" height="100px"></canvas>

                    </div>
                    <div style="margin:5px;">
                      <span id="info_ttd" class="text-danger">Tanda tangan berhasil ditempel, silahkan simpan!</span><br>
                      <input type="hidden" id="signature" name="signature">
                      <input type="hidden" id="file_lama" name="file_lama" value="">
                      <button type="button" id="clear_btn" class="btn btn-danger" data-action="clear"><span class="glyphicon glyphicon-remove"></span> Ulangi</button>
                      <button type="button" id="save_btn" class="btn btn-primary" data-action="save-png"><span class="glyphicon glyphicon-ok"></span> Tempel ttd</button>
                    </div>
                  </div>

                  <div id="upload-pad">
                    <input type="file" ccept="image/png, image/jpeg" name="upload_file">
                  </div>
                </div>
              </div>
              <div class="col-6" id="tanda-tangan">
                <div class="form-group">
                  <div id="ttd"></div>
                  <a id="tombol"></a>
                  <input type="hidden" name="hapusfile" value="" id="file-terpilih">
                </div>
                <!--<div id="hapusfile"></div>-->
              </div>

            </div>
          </div>
        </div><br />


        <hr>
        <div class="clearfix"></div>
        <div class="text-right">
          <a href="<?php echo (site_url('pengguna')) ?>" class="btn btn-danger">Kembali</a>
          <button type="submit" id="simpan" class="btn btn-success">Simpan</button>

        </div>
      </form>

    </div>
  </div>


</div>

<!-- /.container-fluid -->
<script type="text/javascript" src="<?= base_url("assets/js/signature.js"); ?>"></script>
<script>
  $("#info_ttd").prop("hidden", true);
  $('#signature-pad').prop("hidden", true);
  $('#upload-pad').prop("hidden", true);
  var wrapper = document.getElementById("signature-pad");
  var clearButton = wrapper.querySelector("[data-action=clear]");
  var savePNGButton = wrapper.querySelector("[data-action=save-png]");
  var canvas = wrapper.querySelector("canvas");
  var el_note = document.getElementById("note");
  var signaturePad;
  signaturePad = new SignaturePad(canvas);

  clearButton.addEventListener("click", function(event) {
    document.getElementById("note").innerHTML = "Tanda tangan disini";
    signaturePad.clear();
    $("#info_ttd").prop("hidden", true);
  });
  savePNGButton.addEventListener("click", function(event) {
    if (signaturePad.isEmpty()) {
      alert("Tanda tangan tidak boleh kosong");
      event.preventDefault();
      $("#info_ttd").prop("hidden", true);
    } else {
      var canvas = document.getElementById("the_canvas");
      var dataUrl = canvas.toDataURL();
      document.getElementById("signature").value = dataUrl;
      $("#info_ttd").prop("hidden", false);
    }
  });

  function my_function() {
    document.getElementById("note").innerHTML = "";
  }

  $("#pilih-tanda-tangan").change(function() {
    var pilih = $(this).val();
    if (pilih == 1) {
      $('#signature-pad').prop("hidden", false);
      $('#upload-pad').prop("hidden", true);
    } else if (pilih == 2) {
      $('#signature-pad').prop("hidden", true);
      $('#upload-pad').prop("hidden", false);
    } else {
      $('#signature-pad').prop("hidden", true);
      $('#upload-pad').prop("hidden", true);
    }

  })
</script>


<script type="text/javascript">
  var idpengguna = "<?php echo ($idpengguna) ?>";

  $(document).ready(function() {


    //---------------------------------------------------------> JIKA EDIT DATA
    if (idpengguna != "") {
      //console.log(idpengguna);
      $.ajax({
          type: 'POST',
          url: '<?php echo site_url("pengguna/get-edit") ?>',
          data: {
            idpengguna: idpengguna
          },
          dataType: 'json',
          encode: true
        })
        .done(function(result) {

          $('#idpengguna').val(result.idpengguna);
          $('#namapengguna').val(result.namapengguna);
          $('#level').val(result.level);
          $('#username').val(result.username);
          $('#email').val(result.email);
          $('#password_lama').val(result.password);

          $('#file_lama').val(result.file_ttd);
          $("#tanda-tangan").prop("hidden", false);
          if (result.level != 3) {
            var ttd = result.file_ttd;
          } else {
            var ttd = result.pic_ttd;
          }
          if (ttd != null && ttd != '') {
            var url = "<?= base_url("uploads/ttd"); ?>/" + ttd;

            // var html = '<a style="position:absolute" href="<?php echo (site_url('pengguna/delete-file/')) ?>' + (result.idpengguna) + '/' + ttd + '/' + result.level + '" class="btn btn-danger btn_remove btn-sm mr-2 mt-2"><i class="fa fa-trash"></i></a>';


            $('#ttd').html('<div><img src="' + url + '" alt="" height="120px"><input type="hidden" value="' + ttd + '" id="file-hapus" ></div>');
            $("#tombol").html('<a style="position:absolute" href="#"  class="btn btn-danger btn_remove btn-sm mr-2 mt-2"><i class="fa fa-trash"></i></a>');
          }


          if (result.foto != '' && result.foto != null) {
            $("#output1").attr("src", "<?php echo (base_url('./uploads/pengguna')) ?>/" + result.foto);
          } else {
            $("#output1").attr("src", "<?php echo (base_url('./images/nofoto.png')) ?>");
          }

        }); // end ajax.done

      $('#lbljudul').html('Edit Data Pengguna');
    } else {
      $('#lbljudul').html('Tambah Data Pengguna');
    } //end !ltambah    


    //----------------------------------------------------------------- > validasi dan proses simpan data disini
    $('#form').bootstrapValidator({
      feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
      },
      fields: {
        namapengguna: {
          validators: {
            notEmpty: {
              message: 'Nama tidak boleh kosong'
            },
            stringLength: {
              min: 3,
              max: 100,
              message: 'Panjang Karakter diperbolehkan dari 3 sd 100'
            },
          }
        },
        level: {
          validators: {
            notEmpty: {
              message: 'Level akses belum dipilih'
            },
          }
        },
        username: {
          validators: {
            notEmpty: {
              message: 'Username tidak boleh kosong'
            },
            stringLength: {
              min: 4,
              max: 50,
              message: 'Panjang Karakter diperbolehkan dari 5 sd 50'
            },
          }
        },
        email: {
          validators: {

            stringLength: {
              max: 50,
              message: 'Panjang Karakter maksimal 50'
            },
            regexp: {
              regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
              message: 'Harus format email @ yang valid!'
            },

          }
        },
        // password: {
        //   validators: {
        //     notEmpty: {
        //       message: 'Password tidak boleh kosong'
        //     },
        //     stringLength: {
        //       min: 4,
        //       max: 25,
        //       message: 'Panjang Karakter diperbolehkan dari 8 sd 25'
        //     },
        //   }
        // },

      }
    });
    //------------------------------------------------------------------------> END VALIDASI DAN SIMPAN


    $("form").attr('autocomplete', 'off');

  }); //end (document).ready

  function lihatUsername() {
    $("#pesan_username").hide();
    // ambil value username dari form
    var user = $("#username").val();
    // proses pengecekan username tersedia atau tidak.
    $.ajax({
      url: "<?php echo site_url() . 'Login/cekUsername'; ?>",
      data: 'username=' + user,
      type: "POST",
      success: function(msg) {
        if (msg == 1) {
          $("#pesan_username").css("color", "#fc5d32");
          $("#pesan_username").html("Maaf username sudah digunakan.");
          // $('#username').val('');

        } else if (msg == 2) {
          $("#pesan_username").css("color", "#ced4da");
          $("#pesan_username").html("");

        } else {
          $("#pesan_username").css("color", "#fc5d32");
          $("#pesan_username").html("Username tidak valid");
          // $('#username').val('');

        }
        $("#pesan_username").fadeIn(1000);
      }
    });

  }

  function lihatEmail() {
    $("#pesan_email").hide();
    // ambil value email dari form
    var email = $("#email").val();
    // proses pengecekan email tersedia atau tidak.
    $.ajax({
      url: "<?php echo site_url() . 'Login/cekEmail'; ?>",
      data: 'email=' + email,
      type: "POST",
      success: function(msg) {
        if (msg == 1) {
          $("#pesan_email").css("color", "#fc5d32");
          $("#pesan_email").html("Maaf Email sudah digunakan.");
          // $('#email').val('');

        } else {
          $("#pesan_email").html("");

        }
        $("#pesan_email").fadeIn(1000);
      }
    });

  }
  $("#password a").on('click', function(event) {
    event.preventDefault();
    if ($('#password input').attr("type") == "text") {
      $('#password input').attr('type', 'password');
      $('#password i').addClass("far fa-eye-slash");
      $('#password i').removeClass("fa fa-solid fa-eye");
    } else if ($('#password input').attr("type") == "password") {
      $('#password input').attr('type', 'text');
      $('#password i').removeClass("far fa-eye-slash");
      $('#password i').addClass("fa fa-solid fa-eye");
    }
  });

  $("#tanda-tangan a").on('click', function(event) {
    event.preventDefault();
    var file = $('#file-hapus').val();
    $("#file-terpilih").val(file);
    $('#ttd').prop("hidden", true);
    $('#tombol').prop("hidden", true);

  });
</script>
<?= $this->endSection() ?>