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


      <form action="<?php echo (site_url('akun/store')) ?>" id="form" method="post" enctype="multipart/form-data">

        <input type="hidden" name="keyakun" id="keyakun" value="<?php echo (($keyakun)) ?>">

        <div class="row">
          <input type="hidden" name="ltambah" id="ltambah" value="<?php echo ($ltambah) ?>">
          <div class="col-md-4">
            <div class="form-group required">
              <label for="">Kode Akun</label>
              <input type="text" id="kdakun" name="kdakun" class="form-control" placeholder="Kode Akun">
            </div>
          </div>

          <div class="col-md-8">
            <div class="form-group required">
              <label for="">Nama Akun</label>
              <input type="text" id="nmakun" name="nmakun" class="form-control" placeholder="Nama Akun">
            </div>
          </div>

          <div class="col-4">
            <div class="form-group required">
              <label for="">Level</label>
              <select name="level" id="level" class="form-control">
                <option value="">Pilih level</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
              </select>
            </div>
          </div>

          <div class="col-8">
            <div class="form-group required">
              <label for="">Saldo Normal</label>
              <select name="saldonormal" id="saldonormal" class="form-control">
                <option value="">Pilih saldo normal...</option>
                <option value="D">Debet</option>
                <option value="K">Kredit</option>
              </select>
            </div>
          </div>

        </div>
        <hr>
        <div class="clearfix"></div>
        <div class="text-right">
          <a href="<?php echo (site_url('akun')) ?>" class="btn btn-danger">Kembali</a>
          <button type="submit" id="simpan" class="btn btn-success">Simpan</button>

        </div>
      </form>

    </div>
  </div>


</div>
<!-- /.container-fluid -->

<script type="text/javascript">
  var keyakun = "<?php echo ($keyakun) ?>";

  $(document).ready(function() {


    //---------------------------------------------------------> JIKA EDIT DATA
    if (keyakun != "") {
      //console.log(keyakun);
      $.ajax({
          type: 'POST',
          url: '<?php echo site_url("akun/get-edit") ?>',
          data: {
            keyakun: keyakun
          },
          dataType: 'json',
          encode: true
        })
        .done(function(result) {
          $('#kdakun').val(result.kdakun);
          $('#nmakun').val(result.nmakun);
          $('#level').val(result.level);
          $('#saldonormal').val(result.saldonormal);

        }); // end ajax.done

      $('#kdakun').attr("readonly", true);
      $('#nmakun').focus();
      $('#lbljudul').html('Edit Data Akun');
    } else {
      $('#kdakun').focus();
      $('#lbljudul').html('Tambah Data Akun');
    } //end !ltambah    


    //----------------------------------------------------------------- > validasi dan proses simpan data disini
    $('#form').bootstrapValidator({
      feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
      },
      fields: {
        nmakun: {
          validators: {
            notEmpty: {
              message: 'Nama Akun tidak boleh kosong'
            },
            stringLength: {
              min: 3,
              max: 100,
              message: 'Panjang Karakter diperbolehkan dari 3 sd 100'
            },
          }
        },
        kdakun: {
          validators: {
            notEmpty: {
              message: 'Kode Akun tidak boleh kosong'
            },
            stringLength: {
              min: 5,
              max: 6,
              message: 'Panjang Karakter diperbolehkan max 6 Karakter'
            },
          }
        },
        level: {
          validators: {
            notEmpty: {
              message: 'level tidak boleh kosong'
            },
          }
        },
        saldonormal: {
          validators: {
            notEmpty: {
              message: 'Saldo Normal tidak boleh kosong'
            },
          }
        },

      }
    });
    //------------------------------------------------------------------------> END VALIDASI DAN SIMPAN
    $('#kdakun').on('keyup', function(e) {
      let kdakun = $("#kdakun").val();
      if (kdakun.length == 5) {
        if (kdakun <= 73000) {
          // $("#kdakun").val()
          return true
        } else {
          $("#kdakun").val("");
        }
      } else {
        if (kdakun <= 730000) {
          return true;
        } else {
          // $nex = false;
          $("#kdakun").val("");
        }
      }
    })

    $("form").attr('autocomplete', 'off');
    $('#jlhterima').mask('000,000,000', {
      reverse: true
    });
  }); //end (document).ready
</script>
<?= $this->endSection() ?>