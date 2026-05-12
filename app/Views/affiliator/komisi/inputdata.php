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


            <form action="<?php echo (site_url('komisi/simpan')) ?>" id="form" method="post" enctype="multipart/form-data">

                <input type="hidden" name="idkomisi" id="idkomisi" value="<?= $idkomisi; ?>">

                <div class="row">
                    <input type="hidden" name="ltambah" id="ltambah" value="<?php echo ($ltambah) ?>">
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label for="">Komisi</label>
                            <input type="text" id="komisi" name="komisi" class="form-control" placeholder="Komisi">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-right">
                            <a href="<?php echo (site_url('komisi')) ?>" class="btn btn-danger">Kembali</a>
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
    var idkomisi = "<?php echo ($idkomisi) ?>";

    $(document).ready(function() {


        //---------------------------------------------------------> JIKA EDIT DATA
        if (idkomisi != "") {
            //console.log(idkomisi);
            $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("komisi/get_edit_data") ?>',
                    data: {
                        idkomisi: idkomisi
                    },
                    dataType: 'json',
                    encode: true
                })
                .done(function(result) {
                    $('#komisi').val(result.komisi);

                }); // end ajax.done

            $('#komisi').focus();
            $('#lbljudul').html('Edit Data komisi');
        } else {
            $('#komisi').focus();
            $('#lbljudul').html('Tambah Data komisi');
        } //end !ltambah    


        //----------------------------------------------------------------- > validasi dan proses simpan data disini
        $('#form').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                komisi: {
                    validators: {
                        notEmpty: {
                            message: 'Komisi tidak boleh kosong'
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