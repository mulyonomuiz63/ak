<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Event</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info" id="lbljudul"></h6>
        </div>
        <div class="card-body">


            <form action="<?php echo (site_url('Event/simpan')) ?>" id="form" method="post" enctype="multipart/form-data">
                <div class="row">
                    <input type="hidden" id="idevent" name="idevent" class="form-control" readonly="">


                    <div class="col-md-12"> 
                        <div class="form-group">
                            <div class="text-center">

                                <label for="" class="text-center">Foto Event</label>
                                <img src="<?php echo base_url('images/nofoto.png'); ?>" id="output1" class="img-thumbnail" style="max-width:50%;max-height:50%; display: block; margin: 0 auto;">
                            </div>

                            <div class="mt-3">
                                <div class="form-group">
                                    <input type="file" name="file" id="file" accept="image/*" onchange="loadFile1(event)">
                                    <input type="hidden" value="" name="file_lama" id="file_lama" class="form-control" /><br>
                                    <span style="color: red; font-size: 12px; font-weight: bold;"><i> Max ukuran file 2MB</i></span>
                                </div>
                            </div>

                            <script type="text/javascript">
                                var loadFile1 = function(event) {
                                    var output1 = document.getElementById('output1');
                                    output1.src = URL.createObjectURL(event.target.files[0]);
                                };
                            </script>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group required">
                                    <label for="">Nama Event</label>
                                    <input type="text" id="nama_event" name="nama_event" class="form-control" placeholder="Nama Event" autofocus="">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group required">
                                    <label for="">URL</label>
                                    <input type="text" id="url" name="url" class="form-control" placeholder="url" autofocus="">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group required">
                                    <label for="">Deskripsi Singkat Event</label>
                                    <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4" cols="50"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="clearfix"></div>
                <div class="text-right">
                    <a href="<?php echo (site_url('Event')) ?>" class="btn btn-danger">Kembali</a>
                    <button type="submit" id="simpan" class="btn btn-success">Simpan</button>

                </div>
            </form>

        </div>
    </div>


</div>
<!-- /.container-fluid -->

<script type="text/javascript">
    var idevent = "<?php echo ($idevent) ?>";

    $(document).ready(function() {


        //---------------------------------------------------------> JIKA EDIT DATA
        if (idevent != "") {
            //console.log(idevent);
            $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Event/get_edit_data") ?>',
                    data: {
                        idevent: idevent
                    },
                    dataType: 'json',
                    encode: true
                })
                .done(function(result) {
                    $('#idevent').val(result.idevent);
                    $('#nama_event').val(result.nama_event);
                    $('#url').val(result.url);
                    $('#deskripsi').val(result.deskripsi);
                    $('#file_lama').val(result.file);

                    if (result.file != '' && result.file != null) {
                        $("#output1").attr("src", "<?php echo (base_url('./uploads/event/thumbnails')) ?>/" + result.file);
                    } else {
                        $("#output1").attr("src", "<?php echo (base_url('./images/nofoto.png')) ?>");
                    }

                }); // end ajax.done

            $('#lbljudul').html('Edit Data Event');
            $('#form').bootstrapValidator({
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    nama_event: {
                        validators: {
                            notEmpty: {
                                message: 'Nama Event tidak boleh kosong'
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
                    nama_event: {
                        validators: {
                            notEmpty: {
                                message: 'Nama Event tidak boleh kosong'
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


                }
            });
            $('#lbljudul').html('Tambah Data Event');
        } //end !ltambah    


        //----------------------------------------------------------------- > validasi dan proses simpan data disini

        //------------------------------------------------------------------------> END VALIDASI DAN SIMPA
        $("form").attr('autocomplete', 'off');
    }); //end (document).ready
</script>
<?= $this->endSection() ?>