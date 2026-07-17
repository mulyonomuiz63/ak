<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>

<!-- Tambahkan Library Cropper CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">

<!-- CSS Custom untuk Tampilan Profesional -->
<style>
  .upload-box {
    border: 2px dashed #36b9cc;
    border-radius: 10px;
    padding: 30px 15px;
    text-align: center;
    background: #f8f9fc;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  .upload-box:hover {
    background: #e3f2fd;
    border-color: #4e73df;
  }
  .upload-box i {
    font-size: 3.5rem;
    color: #36b9cc;
    margin-bottom: 15px;
    transition: all 0.3s ease;
  }
  .upload-box:hover i {
    color: #4e73df;
    transform: translateY(-5px);
  }
  .ttd-preview-box {
    border: 1px solid #e3e6f0;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    background: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
  }
  .ttd-preview-box img {
    max-height: 120px;
    max-width: 100%;
    object-fit: contain;
  }
  .bg-transparent-checker {
    background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAAHElEQVQYV2NkYGD4z0AEYMQSAzWkEIM4wOQYAAA/JgEAA2rD2AAAAABJRU5ErkJggg==') repeat;
  }
</style>

<!-- Begin Page Content -->
<div class="container-fluid">
  <!-- Page Heading -->
<?php if (session()->getFlashdata('error')) : ?>
  <div class="alert alert-danger">
      <?= session()->getFlashdata('error') ?>
  </div>
<?php endif; ?>
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
              
              <!-- Bagian Upload Baru -->
              <div class="col-6" id="tanda-tangan">
                <div class="form-group">
                  <label for="">Tanda Tangan Pengguna </label><br />
                  <select name="" id="pilih-tanda-tangan" class="form-control mb-3">
                    <option value="">Pilih Jenis Tanda Tangan</option>
                    <option value="1">Tanda Tangan Digital (Canvas)</option>
                    <option value="2">Upload Foto Tanda Tangan</option>
                  </select>

                  <!-- Pad TTD Digital Canvas -->
                  <div id="signature-pad" style="display:none;">
                    <div class="ttd-preview-box p-0 position-relative" style="width:360px; height:110px;">
                      <div id="note" class="text-muted" style="position:absolute; top:40%; left:0; right:0;" onmouseover="my_function();">Goreskan Tanda Tangan Disini</div>
                      <canvas id="the_canvas" width="350px" height="100px" style="position:relative; z-index:2;"></canvas>
                    </div>
                    <div class="mt-2">
                      <span id="info_ttd" class="text-success font-weight-bold d-block mb-2"><i class="fa fa-check"></i> Tanda tangan berhasil ditempel!</span>
                      <input type="hidden" id="signature" name="signature">
                      <input type="hidden" id="file_lama" name="file_lama" value="">
                      <button type="button" id="clear_btn" class="btn btn-sm btn-danger" data-action="clear"><i class="fa fa-times"></i> Ulangi</button>
                      <button type="button" id="save_btn" class="btn btn-sm btn-primary" data-action="save-png"><i class="fa fa-check"></i> Tempel TTD</button>
                    </div>
                  </div>

                  <!-- Pad Upload Foto -->
                  <div id="upload-pad" style="display:none;">
                    <!-- Kotak Upload Profesional -->
                    <div class="upload-box" id="trigger_upload_box">
                      <i class="fas fa-cloud-upload-alt"></i>
                      <h5 class="font-weight-bold text-info mb-1">Klik untuk Memilih Foto TTD</h5>
                      <p class="text-muted small mb-0">Hanya format JPG, JPEG, PNG</p>
                    </div>

                    <input type="file" id="upload_file_input" class="d-none" accept=".jpg, .jpeg, .png">
                    <!-- Input asli yang akan dikirim ke server -->
                    <input type="file" id="real_upload_file" name="upload_file" class="d-none">
                    
                    <!-- Preview Tanda Tangan Setelah di Crop & Hapus BG -->
                    <div id="cropped_preview_container" style="display:none; margin-top:15px;">
                      <div class="ttd-preview-box">
                        <label class="text-success font-weight-bold d-block border-bottom pb-2 mb-2">
                          <i class="fa fa-check-circle"></i> TTD Siap Disimpan
                        </label>
                        <img id="cropped_preview" src="" class="bg-transparent-checker mb-2">
                        <button type="button" class="btn btn-sm btn-outline-danger w-100" id="reset_upload_btn">
                          <i class="fa fa-sync-alt"></i> Ganti Tanda Tangan
                        </button>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
              
              <!-- Bagian Menampilkan TTD Saat Diedit -->
              <div class="col-6" id="tanda-tangan-edit" style="display:none;">
                <div class="form-group">
                  <label class="text-secondary">Tanda Tangan Saat Ini</label>
                  <div class="ttd-preview-box">
                    <div id="ttd" class="mb-2 bg-transparent-checker p-2 rounded"></div>
                    <a href="#" id="tombol" class="btn btn-sm btn-danger w-100 text-white shadow-sm">
                      <i class="fa fa-trash"></i> Hapus Tanda Tangan Ini
                    </a>
                  </div>
                  <input type="hidden" name="hapusfile" value="" id="file-terpilih">
                </div>
              </div>

            </div>
          </div>
        </div><br />

        <hr>
        <div class="clearfix"></div>
        <div class="text-right">
          <a href="<?php echo (site_url('pengguna')) ?>" class="btn btn-danger shadow-sm">Kembali</a>
          <button type="submit" id="simpan" class="btn btn-success shadow-sm px-4">Simpan Data</button>
        </div>
      </form>

    </div>
  </div>

</div>
<!-- /.container-fluid -->

<!-- Modal untuk Cropping Gambar -->
<div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalLabel"><i class="fa fa-crop-alt mr-2"></i>Crop Tanda Tangan</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body bg-light">
        <p class="text-muted small mb-2"><i class="fa fa-info-circle"></i> Paskan kotak area ke bagian tanda tangan saja agar background dapat dihapus dengan rapi.</p>
        <div class="img-container shadow-sm bg-white p-2">
          <img id="image_to_crop" src="" style="max-width: 100%; display: block;">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary px-4" id="crop_and_remove_bg">
          <i class="fa fa-magic mr-1"></i> Proses & Hapus Background
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Tambahkan Library Cropper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script type="text/javascript" src="<?= base_url("assets/js/signature.js"); ?>"></script>

<script>
  $("#info_ttd").prop("hidden", true);
  
  var wrapper = document.getElementById("signature-pad");
  var clearButton = wrapper.querySelector("[data-action=clear]");
  var savePNGButton = wrapper.querySelector("[data-action=save-png]");
  var canvas = wrapper.querySelector("canvas");
  var el_note = document.getElementById("note");
  var signaturePad;
  signaturePad = new SignaturePad(canvas);

  clearButton.addEventListener("click", function(event) {
    document.getElementById("note").innerHTML = "Goreskan Tanda Tangan Disini";
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

  // Animasi Pilihan Jenis TTD
  $("#pilih-tanda-tangan").change(function() {
    var pilih = $(this).val();
    if (pilih == 1) {
      $('#signature-pad').slideDown(300);
      $('#upload-pad').slideUp(300);
    } else if (pilih == 2) {
      $('#signature-pad').slideUp(300);
      $('#upload-pad').slideDown(300);
    } else {
      $('#signature-pad').slideUp(300);
      $('#upload-pad').slideUp(300);
    }
  });

  // =========================================================================
  // SISTEM CROP DAN HAPUS BACKGROUND TANDA TANGAN (UI PROFESIONAL)
  // =========================================================================
  var cropper;
  var imageToCrop = document.getElementById('image_to_crop');
  var uploadFileInput = document.getElementById('upload_file_input');
  var realUploadFile = document.getElementById('real_upload_file');
  var croppedPreview = document.getElementById('cropped_preview');
  var croppedPreviewContainer = document.getElementById('cropped_preview_container');
  var triggerBox = document.getElementById('trigger_upload_box');

  // Trigger click dari kotak desain ke input file asli
  triggerBox.addEventListener('click', function() {
    uploadFileInput.click();
  });

  // Tombol reset/ganti tanda tangan
  document.getElementById('reset_upload_btn').addEventListener('click', function() {
    uploadFileInput.value = '';
    realUploadFile.value = '';
    croppedPreviewContainer.style.display = 'none';
    triggerBox.style.display = 'block';
  });

  // Trigger saat file dipilih
  uploadFileInput.addEventListener('change', function(e) {
    var files = e.target.files;
    var done = function(url) {
      uploadFileInput.value = ''; 
      imageToCrop.src = url;
      $('#cropModal').modal('show');
    };
    var reader;
    var file;

    if (files && files.length > 0) {
      file = files[0];
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

  function dataURLtoFile(dataurl, filename) {
    var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
      bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
    while (n--) {
      u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, { type: mime });
  }

  // Proses Crop dan Manipulasi Canvas
  document.getElementById('crop_and_remove_bg').addEventListener('click', function() {
    if (cropper) {
      var canvasCrop = cropper.getCroppedCanvas();
      var ctx = canvasCrop.getContext('2d');
      var imageData = ctx.getImageData(0, 0, canvasCrop.width, canvasCrop.height);
      var data = imageData.data;

      // Hapus pixel putih
      for (var i = 0; i < data.length; i += 4) {
        var r = data[i];
        var g = data[i + 1];
        var b = data[i + 2];
        if (r > 200 && g > 200 && b > 200) {
          data[i + 3] = 0; 
        } else {
          data[i] = 0;    
          data[i + 1] = 0; 
          data[i + 2] = 0; 
        }
      }
      ctx.putImageData(imageData, 0, 0);

      var processedDataUrl = canvasCrop.toDataURL('image/png');
      croppedPreview.src = processedDataUrl;
      
      // Sembunyikan kotak upload, tampilkan preview
      triggerBox.style.display = 'none';
      croppedPreviewContainer.style.display = 'block';

      var processedFile = dataURLtoFile(processedDataUrl, 'signature_cropped.png');
      var container = new DataTransfer();
      container.items.add(processedFile);
      realUploadFile.files = container.files;

      $('#cropModal').modal('hide');
    }
  });
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
          
          if (result.level != 3) {
            var ttd = result.file_ttd;
          } else {
            var ttd = result.pic_ttd;
          }
          
          if (ttd != null && ttd != '') {
            // SOLUSI CACHE BROWSER BUSTER (?t=waktu)
            var timeStamp = new Date().getTime();
            var url = "<?= base_url("uploads/ttd"); ?>/" + ttd + "?t=" + timeStamp;
            
            $("#tanda-tangan-edit").show();
            // Input file-hapus sengaja ditambahkan di dalam div agar logic hapus Anda tidak berubah
            $('#ttd').html('<img src="' + url + '" alt="TTD"><input type="hidden" value="' + ttd + '" id="file-hapus" >');
          } else {
            $("#tanda-tangan-edit").hide();
          }

          if (result.foto != '' && result.foto != null) {
            var timeStamp = new Date().getTime();
            $("#output1").attr("src", "<?php echo (base_url('./uploads/pengguna')) ?>/" + result.foto + "?t=" + timeStamp);
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
        namapengguna: { validators: { notEmpty: { message: 'Nama tidak boleh kosong' }, stringLength: { min: 3, max: 100, message: 'Panjang Karakter diperbolehkan dari 3 sd 100' }, } },
        level: { validators: { notEmpty: { message: 'Level akses belum dipilih' }, } },
        username: { validators: { notEmpty: { message: 'Username tidak boleh kosong' }, stringLength: { min: 4, max: 50, message: 'Panjang Karakter diperbolehkan dari 5 sd 50' }, } },
        email: { validators: { stringLength: { max: 50, message: 'Panjang Karakter maksimal 50' }, regexp: { regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$', message: 'Harus format email @ yang valid!' }, } },
      }
    });
    //------------------------------------------------------------------------> END VALIDASI DAN SIMPAN

    $("form").attr('autocomplete', 'off');

  }); //end (document).ready

  function lihatUsername() {
    $("#pesan_username").hide();
    var user = $("#username").val();
    $.ajax({
      url: "<?php echo site_url() . 'pengguna/cek-username'; ?>",
      data: 'username=' + user,
      type: "POST",
      success: function(msg) {
        if (msg == 1) {
          $("#pesan_username").css("color", "#fc5d32").html("Maaf username sudah digunakan.");
        } else if (msg == 2) {
          $("#pesan_username").css("color", "#ced4da").html("");
        } else {
          $("#pesan_username").css("color", "#fc5d32").html("Username tidak valid");
        }
        $("#pesan_username").fadeIn(1000);
      }
    });
  }

  function lihatEmail() {
    $("#pesan_email").hide();
    var email = $("#email").val();
    $.ajax({
      url: "<?php echo site_url() . 'pengguna/cek-email'; ?>",
      data: 'email=' + email,
      type: "POST",
      success: function(msg) {
        if (msg == 1) {
          $("#pesan_email").css("color", "#fc5d32").html("Maaf Email sudah digunakan.");
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
      $('#password i').addClass("far fa-eye-slash").removeClass("fa fa-solid fa-eye");
    } else if ($('#password input').attr("type") == "password") {
      $('#password input').attr('type', 'text');
      $('#password i').removeClass("far fa-eye-slash").addClass("fa fa-solid fa-eye");
    }
  });

  // Tombol Hapus Tanda Tangan Lama (Saat Edit)
  $("#tanda-tangan-edit a#tombol").on('click', function(event) {
    event.preventDefault();
    var file = $('#file-hapus').val();
    $("#file-terpilih").val(file); // Kirim flag ke controller untuk dihapus
    
    // Sembunyikan container edit dengan efek animasi
    $('#tanda-tangan-edit .ttd-preview-box').slideUp(300, function(){
       $(this).parent().hide();
    });
  });
</script>
<?= $this->endSection() ?>