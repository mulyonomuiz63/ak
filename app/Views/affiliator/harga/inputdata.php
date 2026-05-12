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


            <form action="<?php echo (site_url('harga/simpan')) ?>" id="form" method="post" enctype="multipart/form-data">

                <input type="hidden" name="idlangganan" id="idlangganan" value="<?= $idlangganan; ?>">

                <div class="row">
                    <input type="hidden" name="ltambah" id="ltambah" value="<?php echo ($ltambah) ?>">
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label for="">Diskon</label>
                            <select name="iddiskon" id="iddiskon" class="form-control">
                                <option value="">Pilih Diskon</option>
                                <?php foreach ($diskon as $rows) : ?>
                                    <option value="<?= $rows->iddiskon; ?>"><?= $rows->diskon; ?> %</option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label for="">Komisi</label>
                            <select name="idkomisi" id="idkomisi" class="form-control">
                                <option value="">Pilih Komisi</option>
                                <?php foreach ($komisi as $rows) : ?>
                                    <option value="<?= $rows->idkomisi; ?>"><?= $rows->komisi; ?> % </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                        <div class="form-group required">
                            <label for="">Total Bulan</label>
                            <input type="text" id="bulan" name="bulan" class="form-control" placeholder="">
                        </div>
                    </div> -->
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label for="">Nama Langganan</label>
                            <input type="text" id="nama_langganan" name="nama_langganan" class="form-control" placeholder="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label for="">Nominal Langganan</label>
                            <input type="text" id="nominal" name="nominal" class="form-control" placeholder="0">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-right">
                            <a href="<?php echo (site_url('harga')) ?>" class="btn btn-danger">Kembali</a>
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
    var idlangganan = "<?php echo ($idlangganan) ?>";

    $(document).ready(function() {


        //---------------------------------------------------------> JIKA EDIT DATA
        if (idlangganan != "") {
            //console.log(idlangganan);
            $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("harga/get_edit_data") ?>',
                    data: {
                        idlangganan: idlangganan
                    },
                    dataType: 'json',
                    encode: true
                })
                .done(function(result) {
                    $('#iddiskon').val(result.iddiskon);
                    $('#idkomisi').val(result.idkomisi);
                    $('#bulan').val(result.bulan);
                    $('#nama_langganan').val(result.nama_langganan);
                    $('#nominal').val(result.nominal);

                }); // end ajax.done

            $('#lbljudul').html('Edit Data Harga');
        } else {
            $('#lbljudul').html('Tambah Data Harga');
        } //end !ltambah    


        //----------------------------------------------------------------- > validasi dan proses simpan data disini
        $('#form').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                iddiskon: {
                    validators: {
                        notEmpty: {
                            message: 'Diskon tidak boleh kosong'
                        },
                    }
                },

                idkomisi: {
                    validators: {
                        notEmpty: {
                            message: 'Diskon tidak boleh kosong'
                        },
                    }
                },
                bulan: {
                    validators: {
                        notEmpty: {
                            message: 'Total bulan tidak boleh kosong'
                        },
                        regexp: {
                            regexp: '^[0-9]+$',
                            message: 'Harus angka'
                        },
                    }
                },
                nama_langganan: {
                    validators: {
                        notEmpty: {
                            message: 'Diskon tidak boleh kosong'
                        },
                    }
                },
                nominal: {
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