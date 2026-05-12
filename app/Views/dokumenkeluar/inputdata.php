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


      <form action="<?php echo (site_url('dokumen-keluar/simpan')) ?>" id="form" method="post" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?php echo $id ?>">

        <div class="row">
          <input type="hidden" name="ltambah" id="ltambah" value="<?php echo ($ltambah) ?>">
          <div class="col-md-6">
            <div class="form-group required">
              <label for="">Keterangan Dokumen</label>
              <input type="text" id="nama_file" name="nama_file" class="form-control" placeholder="">
              <input type="hidden" id="idperusahaan" name="idperusahaan" class="form-control" value="<?= session()->get('idperusahaan'); ?>">
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group <?= $id == '' ? 'required' : ''; ?>">
              <label for="">File</label>
              <input type="file" id="file" name="file" class="form-control" accept="image/jpeg, image/jpg, image/png, application/pdf">
              <input type="hidden" id="file_lama" name="file_lama" class="form-control">
              <input type="hidden" id="status" name="status" value="keluar" class="form-control">
            </div>
          </div>

          <div class="col-6">
            <div class="form-group required">
              <label for="">Nama Vendor/Customer</label>
              <input type="text" id="nama_pengirim" name="nama_pengirim" class="form-control" placeholder="">
            </div>
          </div>
        </div>
        <hr>
        <div class="clearfix"></div>
        <div class="text-right">
          <a href="<?php echo (site_url('dokumen-keluar')) ?>" class="btn btn-danger">Kembali</a>
          <button type="submit" id="simpan" class="btn btn-success">Kirim</button>

        </div>
      </form>

    </div>
  </div>


</div>
<!-- /.container-fluid -->

<script type="text/javascript">
  var id = "<?php echo ($id) ?>";

  $(document).ready(function() {


    //---------------------------------------------------------> JIKA EDIT DATA
    if (id != "") {
      //console.log(id);
      $.ajax({
          type: 'POST',
          url: '<?php echo site_url("dokumen-keluar/get_edit_data") ?>',
          data: {
            id: id
          },
          dataType: 'json',
          encode: true
        })
        .done(function(result) {
          $('#id').val(result.id);
          $('#nama_file').val(result.nama_file);
          $('#file_lama').val(result.file);
          $('#status').val(result.status);
          $('#nama_pengirim').val(result.nama_pengirim);

        }); // end ajax.done


      $('#lbljudul').html('Edit Data Dokumen');
    } else {
      $('#lbljudul').html('Tambah Data Dokumen');
    } //end !ltambah    


    //----------------------------------------------------------------- > validasi dan proses simpan data disini
    if (id != "") {
      $('#form').bootstrapValidator({
        feedbackIcons: {
          valid: 'glyphicon glyphicon-ok',
          invalid: 'glyphicon glyphicon-remove',
          validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
          nama_file: {
            validators: {
              notEmpty: {
                message: 'Nama file tidak boleh kosong'
              },
              stringLength: {
                min: 3,
                max: 100,
                message: 'Panjang Karakter diperbolehkan dari 3 sd 100'
              },
            }
          },

        }
      });
    } else {
      $('#form').bootstrapValidator({
        feedbackIcons: {
          valid: 'glyphicon glyphicon-ok',
          invalid: 'glyphicon glyphicon-remove',
          validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
          nama_file: {
            validators: {
              notEmpty: {
                message: 'Nama file tidak boleh kosong'
              },
              stringLength: {
                min: 3,
                max: 100,
                message: 'Panjang Karakter diperbolehkan dari 3 sd 100'
              },
            }
          },
          file: {
            validators: {
              notEmpty: {
                message: 'File tidak boleh kosong'
              },

            }
          },
          nama_pengirim: {
            validators: {
              notEmpty: {
                message: 'Nama Vendor/Customer  tidak boleh kosong'
              },
              stringLength: {
                min: 3,
                max: 100,
                message: 'Panjang Karakter diperbolehkan dari 3 sd 100'
              },
            }
          },
        }
      });
    }
    //------------------------------------------------------------------------> END VALIDASI DAN SIMPAN


    $("form").attr('autocomplete', 'off');
  }); //end (document).ready
</script>
<?= $this->endSection() ?>