<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>

<!-- Tambahkan Library Cropper CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">

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
                    <!-- Update input file untuk hanya menerima gambar JPG/PNG dan diproses oleh javascript -->
                    <input type="file" id="upload_file_input" class="form-control" accept=".jpg, .jpeg, .png">
                    <!-- Input asli yang akan dikirim ke server (disembunyikan) -->
                    <input type="file" id="real_upload_file" name="upload_file" style="display: none;">
                    
                    <div id="cropped_preview_container" style="display:none; margin-top:10px;">
                      <label class="text-success" style="font-size: 12px;"><i class="fa fa-check"></i> Hasil Tanda Tangan (Background Terhapus):</label><br>
                      <img id="cropped_preview" src="" style="border: 1px dashed #ccc; max-height: 100px; padding: 5px; background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAAHElEQVQYV2NkYGD4z0AEYMQSAzWkEIM4wOQYAAA/JgEAA2rD2AAAAABJRU5ErkJggg==') repeat;">
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-6" id="tanda-tangan-edit">
                <div class="form-group">
                  <div id="ttd"></div>
                  <a id="tombol"></a>
                  <input type="hidden" name="hapusfile" value="" id="file-terpilih">
                </div>
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

<!-- Modal untuk Cropping Gambar -->
<div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Crop Tanda Tangan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="img-container">
          <img id="image_to_crop" src="" style="max-width: 100%; display: block;">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="crop_and_remove_bg">Proses & Hapus Background</button>
      </div>
    </div>
  </div>
</div>

<!-- Tambahkan Library Cropper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
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
  });

  // =========================================================================
  // SISTEM CROP DAN HAPUS BACKGROUND TANDA TANGAN
  // =========================================================================
  var cropper;
  var imageToCrop = document.getElementById('image_to_crop');
  var uploadFileInput = document.getElementById('upload_file_input');
  var realUploadFile = document.getElementById('real_upload_file');
  var croppedPreview = document.getElementById('cropped_preview');
  var croppedPreviewContainer = document.getElementById('cropped_preview_container');

  // Trigger saat file dipilih
  uploadFileInput.addEventListener('change', function(e) {
    var files = e.target.files;
    var done = function(url) {
      uploadFileInput.value = ''; // Kosongkan input agar bisa trigger ulang jika file sama
      imageToCrop.src = url;
      $('#cropModal').modal('show');
    };
    var reader;
    var file;

    if (files && files.length > 0) {
      file = files[0];
      // Validasi ekstensi
      var fileType = file.type;
      if (fileType !== 'image/jpeg' && fileType !== 'image/png' && fileType !== 'image/jpg') {
        alert("Hanya file JPG, JPEG, atau PNG yang diperbolehkan!");
        return;
      }

      if (URL) {
        done(URL.createObjectURL(file));
      } else if (FileReader) {
        reader = new FileReader();
        reader.onload = function(e) {
          done(reader.result);
        };
        reader.readAsDataURL(file);
      }
    }
  });

  // Inisialisasi Cropper saat modal terbuka
  $('#cropModal').on('shown.bs.modal', function() {
    cropper = new Cropper(imageToCrop, {
      viewMode: 1,
      dragMode: 'move',
      autoCropArea: 0.8,
      restore: false,
      guides: true,
      center: true,
      highlight: false,
      cropBoxMovable: true,
      cropBoxResizable: true,
      toggleDragModeOnDblclick: false,
    });
  }).on('hidden.bs.modal', function() {
    cropper.destroy();
    cropper = null;
  });

  // Fungsi mengubah DataURL (Base64) ke format File asli agar backend tidak error
  function dataURLtoFile(dataurl, filename) {
    var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
      bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
    while (n--) {
      u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, { type: mime });
  }

  // Proses saat klik Crop & Hapus Background
  document.getElementById('crop_and_remove_bg').addEventListener('click', function() {
    if (cropper) {
      var canvasCrop = cropper.getCroppedCanvas();
      
      // Proses hapus background putih (jadikan transparan)
      var ctx = canvasCrop.getContext('2d');
      var imageData = ctx.getImageData(0, 0, canvasCrop.width, canvasCrop.height);
      var data = imageData.data;

      // Iterasi setiap pixel gambar
      for (var i = 0; i < data.length; i += 4) {
        var r = data[i];
        var g = data[i + 1];
        var b = data[i + 2];
        
        // Deteksi warna putih / sangat terang (threshold: RGB > 200)
        // Ubah angka 200 menjadi lebih kecil (misal 180) jika background masih sedikit membekas
        if (r > 200 && g > 200 && b > 200) {
          data[i + 3] = 0; // Atur Alpha (Opasitas) menjadi 0 (Transparan)
        } else {
          // Opsional: mempertajam tinta tanda tangan menjadi lebih hitam (bisa dihapus jika tidak mau)
          data[i] = 0;     // R = 0
          data[i + 1] = 0; // G = 0
          data[i + 2] = 0; // B = 0
        }
      }
      ctx.putImageData(imageData, 0, 0);

      // Konversi hasil akhir menjadi data PNG transparan
      var processedDataUrl = canvasCrop.toDataURL('image/png');
      
      // Tampilkan Preview
      croppedPreview.src = processedDataUrl;
      croppedPreviewContainer.style.display = 'block';

      // Memasukkan hasil manipulasi kembali ke input type="file" secara virtual
      // Hal ini memastikan Controller menerima $_FILES['upload_file'] sama seperti sebelumnya
      var processedFile = dataURLtoFile(processedDataUrl, 'signature_cropped.png');
      var container = new DataTransfer();
      container.items.add(processedFile);
      realUploadFile.files = container.files;

      // Tutup Modal
      $('#cropModal').modal('hide');
    }
  });
  // =========================================================================

</script>

<script type="text/javascript">
  var idpengguna = "<?php echo ($idpengguna) ?>";

  $(document).ready(function() {

    //---------------------------------------------------------> JIKA EDIT DATA
    if (idpengguna != "") {
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
          $("#tanda-tangan-edit").prop("hidden", false);
          if (result.level != 3) {
            var ttd = result.file_ttd;
          } else {
            var ttd = result.pic_ttd;
          }
          if (ttd != null && ttd != '') {
            var url = "<?= base_url("uploads/ttd"); ?>/" + ttd;

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
      }
    });
    //------------------------------------------------------------------------> END VALIDASI DAN SIMPAN

    $("form").attr('autocomplete', 'off');

  }); //end (document).ready

  function lihatUsername() {
    $("#pesan_username").hide();
    var user = $("#username").val();
    $.ajax({
      url: "<?php echo site_url() . 'Login/cekUsername'; ?>",
      data: 'username=' + user,
      type: "POST",
      success: function(msg) {
        if (msg == 1) {
          $("#pesan_username").css("color", "#fc5d32");
          $("#pesan_username").html("Maaf username sudah digunakan.");
        } else if (msg == 2) {
          $("#pesan_username").css("color", "#ced4da");
          $("#pesan_username").html("");
        } else {
          $("#pesan_username").css("color", "#fc5d32");
          $("#pesan_username").html("Username tidak valid");
        }
        $("#pesan_username").fadeIn(1000);
      }
    });
  }

  function lihatEmail() {
    $("#pesan_email").hide();
    var email = $("#email").val();
    $.ajax({
      url: "<?php echo site_url() . 'Login/cekEmail'; ?>",
      data: 'email=' + email,
      type: "POST",
      success: function(msg) {
        if (msg == 1) {
          $("#pesan_email").css("color", "#fc5d32");
          $("#pesan_email").html("Maaf Email sudah digunakan.");
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

  $("#tanda-tangan-edit a").on('click', function(event) {
    event.preventDefault();
    var file = $('#file-hapus').val();
    $("#file-terpilih").val(file);
    $('#ttd').prop("hidden", true);
    $('#tombol').prop("hidden", true);
  });
</script>
<?= $this->endSection() ?>