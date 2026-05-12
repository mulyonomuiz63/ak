<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card shadow mb-4">
        <div class="card-header d-sm-flex align-items-center justify-content-between py-3">
            <h6 class="m-0 font-weight-bold text-info">Data Marketer</h6>
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
                            <th style="text-align: center;">Nama</th>
                            <th style="text-align: center;">No Hp</th>
                            <th style="text-align: center;">Kode Referal</th>
                            <th style="text-align: center;">Status</th>
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
            "searching": true,
            "filter": true,
            "order": [],
            "lengthMenu": [10, 100, 250, 500],
            "pageLength": 100,
            "ajax": {
                "url": "<?php echo site_url('marketer/datatablesource') ?>",
                "type": "POST"
            },
            "columnDefs": [{
                    "targets": [0],
                    "orderable": false,
                    "className": 'dt-body-left'
                },
                {
                    "targets": [1],
                    "className": 'dt-body-center'
                },
                {
                    "targets": [2],
                    "className": 'dt-body-center'
                },
                {
                    "targets": [3],
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