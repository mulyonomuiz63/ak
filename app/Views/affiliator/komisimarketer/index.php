<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card shadow mb-4">
        <div class="card-header d-sm-flex align-items-center justify-content-between py-3">
            <h6 class="m-0 font-weight-bold text-info">Data Komisi Marketer</h6>
            <label for="">Total Komisi <b>Rp. <?= number_format($totaltd->total, 0, ".", "."); ?></b></label>
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
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center;">Pengajuan</th>
                            <th style="text-align: center;">Pencairan</th>
                            <th style="text-align: center;">Nominal Komisi</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>


    <!-- unutk upload bukti bayar -->
    <div class="modal fade" id="tarikDana" tabindex="-1" aria-labelledby="tarikDanaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tarikDanaLabel">Detail Penarikan Dana Marketer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo (site_url('Komisi-marketer/approve')) ?>" method="post" enctype="multipart/form-data">
                    <div class="mx-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Nama Marketer</label>
                                    <input type="hidden" id="idtd" name="idtd" class="form-control" placeholder="">
                                    <input type="text" id="nama" name="nama" class="form-control" placeholder="" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Nominal</label>
                                    <input type="text" id="nominal" name="nominal" class="form-control" placeholder="" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Tanggal Pengajuan</label>
                                    <input type="text" id="tgl_pengajuan" name="tgl_pengajuan" class="form-control" placeholder="" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Tanggal Pencairan</label>
                                    <input type="text" id="tgl_pencairan" name="tgl_pencairan" class="form-control" placeholder="" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">No Rekening</label>
                                    <input type="text" id="norek" name="norek" class="form-control" placeholder="" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Nama Bank</label>
                                    <input type="text" id="bank" name="bank" class="form-control" placeholder="" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">No Hp</label>
                                    <input type="text" id="nohp" name="nohp" class="form-control" placeholder="" readonly>
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Approve</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end upload bukti bayar -->

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
                "url": "<?php echo site_url('komisi-marketer/datatablesource') ?>",
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
                {
                    "targets": [2],
                    "className": 'dt-body-center'
                },
                {
                    "targets": [3],
                    "className": 'dt-body-center'
                },
                {
                    "targets": [4],
                    "className": 'dt-body-center'
                },
                {
                    "targets": [5],
                    "className": 'dt-body-center'
                },
            ],
            "language": {
                "infoFiltered": ""
            }

        });

    }); //end (document).ready

    $(document).on('click', '#tarikdana', function(e) {
        e.preventDefault();
        var idtd = $(this).data('id');
        $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Affiliator/KomisiMarketer/get_edit_data") ?>',
                data: {
                    idtd: idtd
                },
                dataType: 'json',
                encode: true
            })
            .done(function(result) {
                console.log(result)

                $('#idtd').val(result.idtd);
                $('#nama').val(result.nama);
                $('#status').val(result.status);
                $('#nominal').val(number_format(result.nominal, 0, ".", "."));
                $('#tgl_pengajuan').val(result.tgl_pengajuan);
                $('#tgl_pencairan').val(result.tgl_pencairan);
                $('#norek').val(result.norek);
                $('#bank').val(result.bank);
                $('#nohp').val(result.nohp);

            });
    });
</script>
<?= $this->endSection() ?>