<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card shadow mb-4">
        <div class="card-header d-sm-flex align-items-center justify-content-between py-3">
            <h6 class="m-0 font-weight-bold text-info">Data Komisi</h6>
            <div>
                <a href="<?php echo ('komisi/tambah') ?>" class="btn btn-sm btn-success shadow-sm tooltips" data-toggle="tooltip" data-placement="left" title="Tambah data komisi"><i class="fas fa-plus fa-lg"></i></a>
            </div>
        </div>
        <div class="card-body">
            <?php
            $pesan = session()->getFlashData('pesan');
            if (!empty($pesan)) {
                echo $pesan;
            }
            ?>
            <div class="clearfix"></div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condesed" id="table">
                    <thead>
                        <tr class="success" style="background-color:#055F93; color: white;">
                            <th style="text-align: center;">Komisi %</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>

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
            "searching": false,
            "filter": false,
            "order": [],
            "lengthMenu": [10, 100, 250, 500],
            "pageLength": 100,
            "ajax": {
                "url": "<?php echo site_url('komisi/datatablesource') ?>",
                "type": "POST"
            },
            "columnDefs": [{
                    "targets": [0],
                    "orderable": false,
                    "className": 'dt-body-center'
                },
                {
                    "targets": [1],
                    "className": 'dt-body-center'
                },
            ],
            "language": {
                "infoFiltered": ""
            }

        });

    }); //end (document).ready
</script>
<?= $this->endSection() ?>