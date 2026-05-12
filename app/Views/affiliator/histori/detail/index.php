<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->

    <div class="card shadow mb-4">
        <div class="card-header d-sm-flex align-items-center justify-content-between py-3">
            <h6 class="m-0 font-weight-bold text-info">Histori Detail Berlangganan</h6>
            <a href="<?php echo base_url('histori/') ?>" class="btn btn-danger"><i class="fa fa-solid fa-arrow-left"></i> Kembali</a>
        </div>

        <!-- unutk upload bukti bayar -->
        <div class="modal fade" id="Approv" tabindex="-1" aria-labelledby="approvLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approvLabel">Detail Bukti Pembayaran</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="<?php echo (site_url('histori/approve')) ?>" method="post" enctype="multipart/form-data">
                        <div class="mx-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Perusahaan</label>
                                        <input type="text" id="namaperusahaan" name="namaperusahaan" class="form-control" placeholder="" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Langganan</label>
                                        <input type="text" id="nama_langganan" name="nama_langganan" class="form-control" placeholder="" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Harga</label>
                                        <input type="text" id="nominal" class="form-control" placeholder="" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Kode Unik</label>
                                        <input type="text" id="kode_unik" class="form-control" placeholder="" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div id="bukti_pembayaran"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end upload bukti bayar -->


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
                            <th style="text-align: center;">Perusahaan</th>
                            <th style="text-align: center;">Pembayaran</th>
                            <th style="text-align: center;">Langganan</th>
                            <th style="text-align: center;">Nominal</th>
                            <th style="text-align: center;">Kode Unik</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center;">Opsi</th>
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
        var idperusahaan = "<?php echo ($idperusahaan) ?>";

        //defenisi datatable
        table = $('#table').DataTable({
            "select": true,
            "processing": true,
            "serverSide": true,
            "order": [],
            "lengthMenu": [10, 100, 250, 500],
            "pageLength": 100,
            "ajax": {
                "url": "<?php echo site_url('histori/datatablesourceDetail') ?>",
                data: {
                    idperusahaan: idperusahaan
                },
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
                {
                    "targets": [4],
                    "className": 'dt-body-center'
                },
                {
                    "targets": [5],
                    "className": 'dt-body-center'
                },
                {
                    "targets": [6],
                    "className": 'dt-body-center'
                },
            ],
            "language": {
                "infoFiltered": ""
            }

        });
    });

    $(document).on('click', '#approv', function(e) {
        e.preventDefault();
        var url = "<?php echo base_url('uploads/buktitransaksi/thumbnails/') ?>";
        var idpayment = $(this).data('idpayment');
        $.ajax({
                type: 'POST',
                url: '<?php echo site_url("histori/get_edit_dataDetail") ?>',
                data: {
                    idpayment: idpayment,
                },
                dataType: 'json',
                encode: true
            })
            .done(function(result) {
                $('#namaperusahaan').val(result.namaperusahaan);
                $('#nama_langganan').val(result.nama_langganan);
                $('#nominal').val(number_format(result.nominal, 0, ".", "."));
                $('#kode_unik').val(result.kode_unik);
                // $('#bukti_pembayaran').html('<img src="' + url + '/' + result.bukti_pembayaran + '" weight="100%" width="100%">');
                if(result.status == 'S'){
                    var bukti = `
                        <div class="form-group">
                            <label for="">Transaksi Terverifikasi</label>
                        </div>
                    `;
                } else {
                    var bukti = `
                        <div class="form-group">
                            <label for="">Bukti Pembayaran</label>
                            <div><img src="${url}/${result.bukti_pembayaran}" weight="100%" width="100%"></div>
                        </div>
                    `;
                }
                $('#bukti_pembayaran').html(bukti);


            });
    });
</script>
<?= $this->endSection() ?>