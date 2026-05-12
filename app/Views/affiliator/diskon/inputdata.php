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


            <form action="<?php echo (site_url('diskon/simpan')) ?>" id="form" method="post" enctype="multipart/form-data">

                <input type="hidden" name="iddiskon" id="iddiskon" value="<?= $iddiskon; ?>">

                <div class="row">
                    <input type="hidden" name="ltambah" id="ltambah" value="<?php echo ($ltambah) ?>">
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label for="">Diskon</label>
                            <input type="text" id="diskon" name="diskon" class="form-control" placeholder="Diskon">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-right">
                            <a href="<?php echo (site_url('diskon')) ?>" class="btn btn-danger">Kembali</a>
                            <button type="submit" id="simpan" class="btn btn-success">Simpan</button>

                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>


</div>
<!-- /.container-fluid -->

<script type="text/javascript">
    var iddiskon = "<?php echo ($iddiskon) ?>";

    $(document).ready(function() {


        //---------------------------------------------------------> JIKA EDIT DATA
        if (iddiskon != "") {
            //console.log(iddiskon);
            $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("diskon/get_edit_data") ?>',
                    data: {
                        iddiskon: iddiskon
                    },
                    dataType: 'json',
                    encode: true
                })
                .done(function(result) {
                    $('#diskon').val(result.diskon);

                }); // end ajax.done

            $('#diskon').focus();
            $('#lbljudul').html('Edit Data Diskon');
        } else {
            $('#diskon').focus();
            $('#lbljudul').html('Tambah Data Diskon');
        } //end !ltambah    


        //----------------------------------------------------------------- > validasi dan proses simpan data disini
        $('#form').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                diskon: {
                    validators: {
                        notEmpty: {
                            message: 'Diskon tidak boleh kosong'
                        },
                        regexp: {
                            regexp: '^[0-9]+$',
                            message: 'Harus angka'
                        },
                    }
                },


            }
        });
        //------------------------------------------------------------------------> END VALIDASI DAN SIMPAN
    }); //end (document).ready
</script>
<?= $this->endSection() ?>