<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="card shadow mb-4">
        <form action="<?php echo site_url('Iklan/deleteAll') ?>" id="form-delete" method="post" enctype="multipart/form-data">
            <div class="card-header d-sm-flex align-items-center justify-content-between py-3">
                <div class="row">
                    <div class="col-12">
                        <select class="form-control" name="status" id="status">
                            <option value="depan">Tampilan POP UP</option>
                            <option value="banner-kiri">Banner-Kiri</option>
                            <option value="banner-kanan">Banner-Kanan</option>
                            <option value="banner-full">Banner-Full</option>
                            <option value="navbar">Top Navbar</option>
                            <option value="sidebar">Sidebar kanan</option>
                            <option value="background">Background Login Desktop</option>
                            <option value="background-mobile">Background Login Mobile</option>
                        </select>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-danger tooltips" id="btn-delete" data-toggle="tooltip" data-placement="left" title="Hapus data iklan"><i class="fa fa-trash"></i></button>
                    <a href="<?php echo ('Iklan/tambah') ?>" class="btn btn-sm btn-success shadow-sm tooltips" data-toggle="tooltip" data-placement="left" title="Tambah data iklan"><i class="fas fa-plus fa-lg"></i></a>
                </div>
            </div>
            <div class="card-body">

                <?php
                $pesan = session()->get('pesan');
                if (!empty($pesan)) {
                    echo $pesan;
                }
                ?>

                <!-- datatable -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-condesed" id="table">
                        <thead>
                            <tr class="success" style="background-color:#055F93; color: white;">
                                <th style="width: 5%; text-align: center;"><input type="checkbox" id="check-all"></th>
                                <th style="width: 40%; text-align: left;">Nama Iklan</th>
                                <th style="width: 20%; text-align: center;">Status Iklan</th>
                                <th style="text-align: center;">Gambar</th>
                                <th style="text-align: center; width: 15%;">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
        </form>


    </div>
</div>


</div>
<!-- /.container-fluid -->

<script type="text/javascript">
    var table;

    $(document).ready(function() {

        //defenisi datatable
        table = $('#table').DataTable({
            "select": true,
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                "url": "<?php echo site_url('Iklan/datatablesource') ?>",
                "type": "POST",
                data: function(d) {
                  d.status = $('#status').val();
                }
            },
            "columnDefs": [{
                    "targets": [0],
                    "orderable": false,
                    "className": 'dt-body-center'
                },
                {
                    "targets": [1],
                    "className": 'dt-body-left'
                },
                {
                    "targets": [2],
                    "className": 'dt-body-left'
                },
                {
                    "targets": [3],
                    "className": 'dt-body-center'
                },
                {
                    "targets": [4],
                    "className": 'dt-body-center'
                },
            ],
            "language": {
                "infoFiltered": ""
            }

        });

    }); //end (document).ready
    
    $('#status').on('change', function(e){
        table.draw();
        e.preventDefault();
    })


    $(document).on("click", "#hapus", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm("Anda yakin ingin menghapus data ini ?", function(result) {
            if (result) {
                document.location.href = link;
            }
        });
    });

    $(document).ready(function() {
        $("#check-all").click(function() {
            if ($(this).is(":checked"))
                $(".check-item").prop("checked", true);
            else
                $(".check-item").prop("checked", false);
        });

        $("#btn-delete").click(function(e) {
            e.preventDefault();
            if ($("#check-all").is(":checked")) {
                bootbox.confirm("Anda yakin ingin menghapus data ini ?", function(result) {
                    if (result) {
                        $("#form-delete").submit();
                    }
                });

            } else if ($(".check-item").is(":checked")) {
                bootbox.confirm("Anda yakin ingin menghapus data ini ?", function(result) {
                    if (result) {
                        $("#form-delete").submit();
                    }
                });
            } else {
                bootbox.confirm("silahkan pilih data yang akan di hapus?", function(result) {

                });

            }
        });
    });
</script>
<?= $this->endSection() ?>