<?= $this->extend('template/admin') ?>
<?= $this->section('css') ?>
<style>
    .pricing-card {
        background: #fff;
        border-radius: 22px;
        padding: 25px;
        height: 100%;
        transition: all .4s ease;
        box-shadow: 0 15px 35px rgba(0,0,0,.15);
        position: relative;
    }
    
    .pricing-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 30px 60px rgba(0,0,0,.25);
    }
    
    .pricing-header {
        text-align: center;
        margin-bottom: 15px;
    }
    
    .pricing-header h4 {
        font-weight: 700;
    }
    
    .pricing-header h2 {
        font-weight: 800;
    }
    
    .pricing-header small {
        font-size: 14px;
        color: #555;
    }
    
    .pricing-list {
        list-style: none;
        padding-left: 0;
        font-size: 14px;
    }
    
    .pricing-list li {
        position: relative;
        padding-left: 24px;
        margin-bottom: 8px;
    }
    
    .pricing-list li::before {
        content: "✔";
        position: absolute;
        left: 0;
        color: #0d6efd;
        font-weight: bold;
    }
    
    .btn-toggle {
        font-size: 13px;
        font-weight: 600;
        color: #0d6efd;
    }
    
    .gratis { border-top: 6px solid #adb5bd; }
    .tahunan {
        background: linear-gradient(135deg, #6fd3e8, #c9f2ff);
        border-top: 6px solid #0dcaf0;
    }
    .bulanan {
        background: linear-gradient(135deg, #ffc107, #ffe7a3);
        border-top: 6px solid #fd7e14;
    }
    
    .featured {
        transform: scale(1.05);
    }
    
    .ribbon {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 90px;
        height: 90px;
        overflow: hidden;
    }
    
    .ribbon span {
        position: absolute;
        top: 20px;
        right: -35px;
        transform: rotate(45deg);
        background: #dc3545;
        color: #fff;
        padding: 5px 40px;
        font-size: 12px;
        font-weight: bold;
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% { transform: rotate(45deg) scale(1); }
        50% { transform: rotate(45deg) scale(1.1); }
        100% { transform: rotate(45deg) scale(1); }
    }
    
    @media (max-width: 767px) {
        .featured { transform: none; }
    }

</style>
<style>
    .modal-content {
        border-radius: 14px;
    }
    
    .form-control {
        border-radius: 10px;
        height: 42px;
    }
    
    fieldset {
        background: #fafafa;
    }
    
    .modal-title {
        font-size: 18px;
    }
    .price-card {
        background: #f8f9fa;
        border-radius: 14px;
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .price-card + .price-card {
        margin-top: 10px;
    }
    
    .price-label {
        font-size: 13px;
        color: #6c757d;
    }
    
    .price-normal {
        border: none;
        background: transparent;
        font-size: 16px;
        font-weight: 500;
        color: #495057;
        text-align: right;
        pointer-events: none;
        width: 120px;
    }
    
    .price-discount {
        border: none;
        background: transparent;
        font-size: 20px;
        font-weight: 700;
        color: #28a745;
        text-align: right;
        pointer-events: none;
        width: 120px;
    }
    
    .price-card.discount {
        background: #e9f7ef;
    }


</style>

<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $uri = service('uri');
    $segment2 = $uri->getSegment(2);
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->

    <div class="card shadow mb-4">
        <div class="card-header d-sm-flex align-items-center justify-content-between py-3">
            <?php if (session()->get('idperusahaan') != '9999999999') { ?>
                <h6 class="m-0 font-weight-bold text-info">Histori Berlangganan</h6>
                <div>
                    <?php if( $caraBayar->total_v <= 0 &&  $caraBayar->total_p <= 0) :?>
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#informasiLangganan">
                            Mulai Berlangganan
                        </button>
                    <?php endif; ?>
                </div>
            <?php } else { ?>
                <ul class="nav nav-tabs">
                  <li class="nav-item ">
                    <a class="nav-link collapse-item  <?= $segment2 == ''? 'active btn btn-info':'' ?>" aria-current="page" href="<?= base_url('histori') ?>">Aktif</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link collapse-item <?= $segment2 == 'verifikasi'? 'active btn btn-info':'' ?>" href="<?= base_url('histori/verifikasi') ?>">Verifikasi</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link collapse-item <?= $segment2 == 'tidakaktif'? 'active btn btn-info':'' ?>" href="<?= base_url('histori/tidakaktif') ?>">Tdk Aktif</a>
                  </li>
                </ul>
                <label for="">Total Setelah Dikurang Komisi <b>Rp. <?= number_format($totalpayment->total, 0, ".", "."); ?></b></label>
                <input type="hidden" id="status" name="status" value="<?= $segment2 ?>">
            <?php } ?>

            <!-- cara pembayaran -->
            <div class="modal fade" id="caraPembayaran" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Cara Pembayaran</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="">Silahkan melakukan pembayaran melaui:</label>
                                    <ul>
                                        <li>Rekening mandiri <b>1660003837846</b></li>
                                        <li>Atas nama <b>PT. Legalyn Konsultan Indonesia</b></li>
                                        <li id="nominal_cara">Kode unik transaksi</li>
                                        <li id="kode_unik_cara">Harga langganan</li>
                                    </ul>
                                    <label for=""><b>Catatan:</b> Setelah anda melakukan pembayaran, harap konfirmasi dengan mengupload bukti transfer melalui menu opsi.</label>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Kembali</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- informasi langganan -->
            <div class="modal fade" id="informasiLangganan" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content border-0 bg-transparent">
            
                        <div class="modal-body">
                            <div class="row">
            
                                <!-- GRATIS -->
                                <div class="col-md-4 mb-4">
                                    <div class="pricing-card gratis">
                                        <div class="pricing-header">
                                            <h4>GRATIS</h4>
                                            <h2>Rp0 <small>/bulan</small></h2>
                                        </div>
            
                                        <ul class="pricing-list" data-limit="10">
                                            <li>Registrasi Awal Otomatis Dari Sistem Mendapat Paket GRATIS</li>
                                            <li>Pengguna Maksimal 3</li>
                                            <li>Daftar Akun Maksimal 500</li>
                                            <li>Jurnal Entri Maksimal 500</li>
                                            <li>Laporan Jurnal</li>
                                            <li>Laporan Buku Besar</li>
                                            <li>Laporan Posisi Keuangan</li>
                                            <li>Laporan Laba Rugi</li>
                                            <li>Laporan Perubahan Ekuitas</li>
                                            <li>Cetak PDF-Laporan Jurnal</li>
                                            <li>Cetak PDF-Laporan Buku Besar</li>
                                            <li>Cetak PDF-Laporan Posisi Keuangan</li>
                                            <li>Cetak PDF-Laporan Laba Rugi</li>
                                            <li>Cetak PDF-Laporan Perubahan Ekuitas</li>
                                            <li>Export Excel-Laporan Buku Besar</li>
                                            <li>Export Excel-Laporan Posisi Keuangan</li>
                                            <li>Export Excel-Laporan Laba Rugi</li>
                                            <li>Upload Lampiran Bukti Transaksi</li>
                                            <li>Import Daftar Akun</li>
                                            <li>Dashboard</li>
                                            <li>Promosi/Iklan</li>
                                        </ul>
            
                                        <button class="btn btn-toggle btn-sm w-100">Lihat Semua</button>
                                    </div>
                                </div>
            
                                <!-- PRO TAHUNAN -->
                                <div class="col-md-4 mb-4">
                                    <div class="pricing-card tahunan featured">
            
                                        <div class="ribbon"><span>50% OFF</span></div>
            
                                        <div class="pricing-header">
                                            <h4>PRO TAHUNAN</h4>
                                            <h2>Rp75.000 <small>/bulan</small></h2>
                                        </div>
            
                                        <ul class="pricing-list" data-limit="10">
                                            <li>Pengguna Unlimited</li>
                                            <li>Daftar Akun Unlimited</li>
                                            <li>Jurnal Entri Unlimited</li>
                                            <li>Laporan Jurnal</li>
                                            <li>Laporan Buku Besar</li>
                                            <li>Laporan Posisi Keuangan</li>
                                            <li>Laporan Laba Rugi</li>
                                            <li>Laporan Perubahan Ekuitas</li>
                                            <li>Cetak PDF-Laporan Jurnal</li>
                                            <li>Cetak PDF-Laporan Buku Besar</li>
                                            <li>Cetak PDF-Laporan Posisi Keuangan</li>
                                            <li>Cetak PDF-Laporan Laba Rugi</li>
                                            <li>Cetak PDF-Laporan Perubahan Ekuitas</li>
                                            <li>Export Excel-Laporan Buku Besar</li>
                                            <li>Export Excel-Laporan Posisi Keuangan</li>
                                            <li>Export Excel-Laporan Laba Rugi</li>
                                            <li>Upload Lampiran Bukti Transaksi</li>
                                            <li>Import Daftar Akun</li>
                                            <li>Dashboard</li>
                                            <li>Promosi/Iklan</li>
                                        </ul>
            
                                        <button class="btn btn-toggle btn-sm w-100">Lihat Semua</button>
            
                                        <button class="btn btn-primary btn-block mt-3 pilih-paket"
                                            data-paket="tahunan"
                                            data-toggle="modal"
                                            data-dismiss="modal"
                                            data-target="#staticBackdrop">
                                            Pilih Paket
                                        </button>
                                    </div>
                                </div>
            
                                <!-- PRO BULANAN -->
                                <div class="col-md-4 mb-4">
                                    <div class="pricing-card bulanan">
                                        <div class="pricing-header">
                                            <h4>PRO BULANAN</h4>
                                            <h2>Rp150.000 <small>/bulan</small></h2>
                                        </div>
            
                                        <ul class="pricing-list" data-limit="10">
                                            <li>Pengguna Unlimited</li>
                                            <li>Daftar Akun Unlimited</li>
                                            <li>Jurnal Entri Unlimited</li>
                                            <li>Laporan Jurnal</li>
                                            <li>Laporan Buku Besar</li>
                                            <li>Laporan Posisi Keuangan</li>
                                            <li>Laporan Laba Rugi</li>
                                            <li>Laporan Perubahan Ekuitas</li>
                                            <li>Cetak PDF-Laporan Jurnal</li>
                                            <li>Cetak PDF-Laporan Buku Besar</li>
                                            <li>Cetak PDF-Laporan Posisi Keuangan</li>
                                            <li>Cetak PDF-Laporan Laba Rugi</li>
                                            <li>Cetak PDF-Laporan Perubahan Ekuitas</li>
                                            <li>Export Excel-Laporan Buku Besar</li>
                                            <li>Export Excel-Laporan Posisi Keuangan</li>
                                            <li>Export Excel-Laporan Laba Rugi</li>
                                            <li>Upload Lampiran Bukti Transaksi</li>
                                            <li>Import Daftar Akun</li>
                                            <li>Dashboard</li>
                                            <li>Promosi/Iklan</li>
                                        </ul>
            
                                        <button class="btn btn-toggle btn-sm w-100">Lihat Semua</button>
            
                                        <button class="btn btn-warning btn-block mt-3 pilih-paket"
                                            data-paket="bulanan"
                                            data-toggle="modal"
                                            data-dismiss="modal"
                                            data-target="#staticBackdrop">
                                            Pilih Paket
                                        </button>
                                    </div>
                                </div>
            
                            </div>
                        </div>
            
                    </div>
                </div>
            </div>




            <!--proses langganan-->
            <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow border-0 rounded-lg">
            
                        <form action="<?= site_url('histori/simpan') ?>" id="form" method="post">
            
                            <!-- HEADER -->
                            <div class="modal-header border-0 pb-2">
                                <h5 class="modal-title font-weight-bold">
                                    Berlangganan
                                </h5>
                            </div>
            
                            <!-- BODY -->
                            <div class="modal-body pt-2">
                                <div class="row">
            
                                    <input type="hidden" name="ltambah" id="ltambah">
            
                                    <!-- LANGGANAN -->
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-semibold">Langganan</label>
                                        <select name="idlangganan" id="idlangganan" class="form-control">
                                            <option value="">Pilih langganan</option>
                                            <?php foreach ($langganan as $rows): ?>
                                                <option
                                                    value="<?= $rows->idlangganan ?>"
                                                    data-diskon="<?= $rows->diskon ?>"
                                                    data-komisi="<?= $rows->komisi ?>"
                                                    data-nominal="<?= $rows->nominal ?>"
                                                >
                                                    <?= $rows->nama_langganan ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
            
                                    <!-- DURASI -->
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-semibold">Durasi</label>
                                        <select name="bulan" id="bulan" class="form-control">
                                            <option value="">Pilih</option>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <option value="<?= $i ?>"><?= $i ?></option>
                                            <?php endfor ?>
                                        </select>
                                    </div>
            
            
                                    <!-- HARGA DISKON -->
                                    <div class="col-md-12 mb-3">

                                        <!-- HARGA NORMAL -->
                                        <div class="price-card mb-2">
                                            <span class="price-label">Harga Normal</span>
                                    
                                            <input
                                                type="text"
                                                id="nominalTampil"
                                                class="price-normal"
                                                readonly
                                                placeholder="0"
                                            >
                                    
                                            <input type="hidden" id="nominals" name="nominal">
                                        </div>
                                    
                                        <!-- HARGA DISKON -->
                                        <div class="price-card discount">
                                            <span id="diskon_tampil" class="price-label">
                                                Harga Setelah Diskon
                                            </span>
                                    
                                            <input
                                                type="text"
                                                id="nominalDiskon"
                                                class="price-discount"
                                                readonly
                                                placeholder="0"
                                            >
                                    
                                            <input type="hidden" id="diskon" name="diskon">
                                        </div>
                                    
                                    </div>


            
                                    <!-- KODE REFERAL -->
                                    <div class="col-md-12">
                                        <fieldset class="border rounded p-3">
                                            <legend class="w-auto px-2 small text-muted">
                                                Kode Referal (Opsional)
                                            </legend>
            
                                            <div class="frmSearch position-relative">
                                                <input
                                                    type="text"
                                                    id="kode_referal_tampil"
                                                    name="kode_referal"
                                                    class="form-control"
                                                    placeholder="Masukkan kode referal"
                                                    value="<?= session()->get('kode_referal'); ?>"
                                                    <?= session()->get('kode_referal') == 0 ? '' : 'readonly'; ?>
                                                >
                                                <div id="suggestion-box"></div>
                                            </div>
                                        </fieldset>
                                    </div>
            
                                </div>
                            </div>
            
                            <!-- FOOTER -->
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light" data-dismiss="modal">
                                    Batal
                                </button>
                                <button type="submit" class="btn btn-info px-4">
                                    Berlangganan
                                </button>
                            </div>
            
                        </form>
            
                    </div>
                </div>
            </div>
            <!-- endproses langganan-->

        </div>
        
        <!--untuk pilih pembayaran-->
        <div class="modal fade" id="pilihPembayaran" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <input type="hidden" id="idpl_selected">
        
                        <!--<div class="card mb-3 border-primary shadow-sm" style="cursor: pointer;" id="pay-button-midtrans">-->
                        <!--    <div class="card-body d-flex align-items-center">-->
                        <!--        <i class="bi bi-shield-check text-primary mr-3" style="font-size: 2rem;"></i>-->
                        <!--        <div>-->
                        <!--            <h6 class="mb-0 font-weight-bold">Pembayaran Otomatis</h6>-->
                        <!--            <small class="text-muted small-text">VA, E-Wallet, Kartu Kredit.</small>-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--</div>-->
        
                        <div class="card border-info shadow-sm" style="cursor: pointer;" id="btn-pilih-manual">
                            <div class="card-body d-flex align-items-center">
                                <i class="bi bi-bank text-info mr-3" style="font-size: 2rem;"></i>
                                <div>
                                    <h6 class="mb-0 font-weight-bold">Transfer Manual</h6>
                                    <small class="text-muted small-text">Upload bukti transfer manual.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<style>
    /* Styling agar tampilan lebih premium */
    .bg-primary-lighten { background-color: #e7f1ff; }
    .bg-info-lighten { background-color: #e1f5fe; }
    .hvr-shadow:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; 
        transition: all 0.3s ease;
    }
</style>
        <!--en untuk pilih pembayaran-->

        <!-- uploadFile bukti bayar -->
        <div class="modal fade" id="uploadFile" tabindex="-1" aria-labelledby="uploadFileLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadFileLabel">Upload Bukti Pembayaran</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="<?php echo (site_url('histori/uploadPembayaran')) ?>" method="post" enctype="multipart/form-data">
                        <div class="mx-4">
                            <div class="form-group">
                                <input type="file" style="color: #212121; border:none" name="fileBukti" class="form-control" required accept="image/jpeg, image/jpg, image/png" />
                                <input type="hidden" id="nama_langganan_upload" name="nama_langganan">
                                <input type="hidden" id="nominalUpload" name="nominalUpload">
                                <input type="hidden" id="kode_unik" name="kode_unik">
                            </div>

                            <div class="form-group">
                                <button class="btn btn-info" type="submit">Upload</button>
                                <button type="button" id="caraBayar" class="btn btn-warning" data-toggle="modal" data-target="#caraPembayaran" data-dismiss="modal" aria-label="Close">
                                    Cara Pembayaran
                                </button>
                                <a href="<?php echo (site_url('histori/batalkan-pesanan')) ?>" class="btn btn-danger">
                                    Batalkan Pesanan
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end uploadFile bukti bayar -->

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
                                        <label for="">Nama Perusahaan</label>
                                        <input type="hidden" id="idperusahaan" name="idperusahaan" class="form-control" placeholder="">
                                        <input type="text" id="namaperusahaan" name="namaperusahaan" class="form-control" placeholder="" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Nama Langganan</label>
                                        <input type="text" id="nama_langganan" name="nama_langganan" class="form-control" placeholder="" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Nominal</label>
                                        <input type="text" id="nominal_tampil" class="form-control" placeholder="" readonly>
                                        <input type="hidden" id="nominal" name="nominal" class="form-control" placeholder="" readonly>
                                        <input type="hidden" id="nominal_komisi" name="nominal_komisi">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Kode Unik</label>
                                        <input type="text" id="kode_unik_tampil" class="form-control" placeholder="" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label id="komisiPersent" for=""></label>
                                        <input type="text" id="nominal_dipotong_komisi" class="form-control" placeholder="" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Bukti Pembayaran</label>
                                        <div id="bukti_pembayaran"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group" id="approveSubmit">
                                <button class="btn btn-info" type="submit">Approve</button>
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
            } else { ?>
                <?php if (session()->get('hitAlert') == 'free') { ?>
                    <div class="alert alert-success" role="alert">
                        <strong>Segera berlangganan!</strong> Untuk mendapatkan akses fitur tanpa batas ☺
                    </div>
                <?php } elseif (session()->get('hitAlert') == 'tidak') { ?>
                    <div class="alert alert-success" role="alert">
                        <strong>Segera perpanjang paket langganan anda!</strong> Supaya dapat menggunakan fitut-fitur akuntanmu kembali ☺
                    </div>
                <?php } ?>

            <?php }
            ?>
            <div class="clearfix"></div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condesed" id="table">
                    <thead>
                        <tr class="success" style="background-color:#055F93; color: white;">
                            <th style="text-align: left;">Berlangganan</th>
                            <th style="text-align: left;">Perusahaan</th>
                            <!-- <th style="text-align: center;">Nominal</th> -->
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center;">Sisa Hari</th>
                            <th style="text-align: center;">Mulai</th>
                            <th style="text-align: center;">Berakhir</th>
                            <th style="text-align: center;">Opsi</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>


</div>
<!-- /.container-fluid -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js" integrity="sha256-0YPKAwZP7Mp3ALMRVB2i8GXeEndvCq3eSl/WsAl1Ryk=" crossorigin="anonymous"></script>
<script type="text/javascript">
    var table;

    $(document).ready(function() {

        //defenisi datatable
        table = $('#table').DataTable({
            "select": true,
            "processing": true,
            "serverSide": true,
            "order": [],
            "lengthMenu": [10, 100, 250, 500],
            "pageLength": 100,
            "ajax": {
                "url": "<?php echo site_url('histori/datatablesource') ?>",
                "type": "POST",
                data: function(d) {
                  d.status = $('input[name=status]').val();
                }
            },
            "columnDefs": [{
                    "targets": [0],
                    "orderable": false,
                    "className": 'dt-body-left'
                },
                {
                    "targets": [1],
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
                    "targets": [5],
                    "className": 'dt-body-center'
                },
            ],
            "language": {
                "infoFiltered": ""
            }

        });

        $('#form').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                idlangganan: {
                    validators: {
                        notEmpty: {
                            message: 'Tidak boleh kosong'
                        },
                    }
                },
                bulan: {
                    validators: {
                        notEmpty: {
                            message: 'Tidak boleh kosong'
                        },
                    }
                },
            }
        });

        $("#idlangganan").on("change", function(e) {
            e.preventDefault();
            var diskon = $('#idlangganan').find(':selected').data('diskon');
            var komisi = $('#idlangganan').find(':selected').data('komisi');
            var nominal = $('#idlangganan').find(':selected').data('nominal');
            var kode_referal = $('#kode_referal_tampil').val();
            var bulan = $('#bulan').val() == '' ? 0 : Number($('#bulan').val());
            var bulanNominal = nominal * bulan;
            if (kode_referal == '' || kode_referal == null) {
                var nominalDiskon = 0;
                var diskontampil = 0;
                $('#diskon_tampil').html('<label for="" > Harga Setelah Diskon </label>')
            } else {
                var nominalDiskon = bulanNominal - (bulanNominal * diskon / 100);
                var diskontampil = diskon;
                $('#diskon_tampil').html('<label for="" > Harga Setelah Diskon ' + diskon + ' %</label>')
            }
            $('#nominals').val(bulanNominal);
            $('#nominalTampil').val(number_format(bulanNominal, 0, ".", "."));
            $('#nominalDiskon').val(number_format(nominalDiskon, 0, ".", "."));
            $('#diskon').val(diskontampil);
        });

        $("#bulan").on("change", function(e) {
            e.preventDefault();
            var diskon = $('#idlangganan').find(':selected').data('diskon');
            var komisi = $('#idlangganan').find(':selected').data('komisi');
            var nominal = $('#idlangganan').find(':selected').data('nominal');
            var kode_referal = $('#kode_referal_tampil').val();
            var bulan = $('#bulan').val() == '' ? 0 : Number($('#bulan').val());
            var bulanNominal = nominal * bulan;

            if (kode_referal == '' || kode_referal == null) {
                var total = bulanNominal;
                var nominalDiskon = 0;
                var diskontampil = 0;
                $('#diskon_tampil').html('<label for="" > Harga Setelah Diskon </label>')
            } else {
                var total = bulanNominal - (bulanNominal * diskon / 100);
                var nominalDiskon = bulanNominal - (bulanNominal * diskon / 100);
                var diskontampil = diskon;
                $('#diskon_tampil').html('<label for="" > Harga Setelah Diskon ' + diskon + ' %</label>')
            }
            $('#nominals').val(bulanNominal);
            $('#nominalTampil').val(number_format(bulanNominal, 0, ".", "."));
            $('#nominalDiskon').val(number_format(nominalDiskon, 0, ".", "."));
            $('#diskon').val(diskontampil);
        });
        $("#kode_referal_tampil").on("keyup", function(e) {
            e.preventDefault();
            $('#nominalDiskon').val(number_format('0', 0, ".", "."));
            $('#diskon').val('0');
            $('#diskon_tampil').html('<label for="" > Harga Setelah Diskon </label>')
        });
    }); //end (document).ready


    $(document).on('click', '#uploadBukti', function(e) {
        e.preventDefault();
        var nama_langganan = $(this).data('nama');
        var nominal = $(this).data('nominal');
        var kode_unik = $(this).data('kode_unik');
        $('#nama_langganan_upload').val(nama_langganan);
        $('#nominalUpload').val(nominal);
        $('#kode_unik').val(kode_unik);

    });

    $(document).on('click', '#approv', function(e) {
        e.preventDefault();
        var url = "<?php echo base_url('uploads/buktitransaksi/thumbnails/') ?>";
        var idperusahaan = $(this).data('idperusahaan');
        $.ajax({
                type: 'POST',
                url: '<?php echo site_url("histori/get_edit_data") ?>',
                data: {
                    idperusahaan: idperusahaan,
                },
                dataType: 'json',
                encode: true
            })
            .done(function(result) {
                var komisi = result.nominal * result.komisi / 100;
                if (result.kode_referal == '' || result.kode_referal == '0' || result.kode_referal == null) {
                    var nominal_komisi = '0';
                    var nominal = '0';
                } else {
                    var nominal_komisi = komisi;
                    var nominal = result.nominal - (result.nominal * result.komisi / 100);
                }
                $('#idperusahaan').val(result.idperusahaan);
                $('#namaperusahaan').val(result.namaperusahaan);
                $('#nama_langganan').val(result.nama_langganan);
                $('#nominal').val(result.nominal);
                $('#kode_unik_tampil').val(result.kode_unik);
                $('#nominal_tampil').val(number_format(result.nominal, 0, ".", "."));
                $('#nominal_komisi').val(nominal_komisi);
                $('#nominal_dipotong_komisi').val(number_format(nominal, 0, ".", "."));
                $('#komisiPersent').html('<label for="" style="margin-bottom: 0rem;">  Potong Komisi  ' + result.komisi + ' % </label> ');
                $('#bukti_pembayaran').html('<img src="' + url + '/' + result.bukti_pembayaran + '" weight="100%" width="100%">');
                if (result.status == 'P') {
                    $('#approveSubmit').show();
                } else {
                    $('#approveSubmit').hide();
                }

            });
    });

    $(document).on('click', '#caraBayar', function(e) {
        e.preventDefault();
        $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Affiliator/Histori/get_caraBayar") ?>',
                dataType: 'json',
                encode: true
            })
            .done(function(result) {
                $('#nominal_cara').html('<li >  Kode unik transaksi  <b>' + result.kode_unik + '</b></li> ');
                $('#kode_unik_cara').html('<li >  Total yang harus dibayar Rp. <b>' + number_format(parseInt(result.nominal) + parseInt(result.kode_unik), 0, ".", ".") + '</b></li> ');
            });
    });


    $("#kode_referal_tampil").autocomplete({
        appendTo: "#suggestion-box",
        source: function(request, response) {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('Affiliator/Histori/autocomplate'); ?>",
                dataType: "json",
                data: {
                    term: request.term,
                },
                success: function(data) {
                    var results = $.map(data, function(v, i) {
                        v = $.extend(v, {
                            label: v.kode_referal + ' - ' + v.nama,
                            value: v.kode_referal
                        });
                        return v;
                    });
                    response(results);

                }
            }) //ajax
        }, //source
        select: function(event, ui) {
            $('[name="label"]').val(ui.item.kode_referal);
            var diskon = $('#idlangganan').find(':selected').data('diskon');
            var komisi = $('#idlangganan').find(':selected').data('komisi');
            var nominal = $('#idlangganan').find(':selected').data('nominal');
            var kode_referal = $('#kode_referal_tampil').val();
            var bulan = $('#bulan').val() == '' ? 0 : Number($('#bulan').val());
            var bulanNominal = nominal * bulan;

            if (ui.item.kode_referal == '' || ui.item.kode_referal == null) {
                var nominalDiskon = 0;
                var diskontampil = 0;
            } else {
                var nominalDiskon = bulanNominal - (bulanNominal * diskon / 100);
                var diskontampil = diskon;
            }
            $('#nominals').val(bulanNominal);
            $('#nominalTampil').val(number_format(bulanNominal, 0, ".", "."));
            $('#nominalDiskon').val(number_format(nominalDiskon, 0, ".", "."));
            $('#diskon').val(diskontampil);
            $('#diskon_tampil').html('<label for="" > Harga Setelah Diskon ' + diskon + ' %</label>')
        }
    });
</script>

<script>
document.querySelectorAll('.pricing-list').forEach(function(list){
    const limit = parseInt(list.dataset.limit || 10);
    const items = list.querySelectorAll('li');
    const btn = list.parentElement.querySelector('.btn-toggle');

    if (items.length <= limit) {
        btn.style.display = 'none';
        return;
    }

    items.forEach((item, i) => {
        if (i >= limit) {
            item.style.display = 'none';
            item.classList.add('extra');
        }
    });

    btn.addEventListener('click', function () {
        const hidden = list.querySelectorAll('.extra');
        const isHidden = hidden[0].style.display === 'none';

        hidden.forEach(el => {
            el.style.display = isHidden ? 'list-item' : 'none';
        });

        btn.innerText = isHidden
            ? 'Tampilkan Lebih Sedikit'
            : 'Lihat Semua';
    });
});
</script>

<script>
// PILIH PAKET 
let selectedPaket = null;
document.querySelectorAll('.pilih-paket').forEach(btn => {
    btn.addEventListener('click', function () {
        selectedPaket = this.dataset.paket;
    });
});

$('#staticBackdrop').on('shown.bs.modal', function () {

    if (selectedPaket) {
        $('#idlangganan option').each(function () {
            if ($(this).text().toLowerCase().includes(selectedPaket)) {
                $(this).prop('selected', true);
                var diskon = $('#idlangganan').find(':selected').data('diskon');
                var komisi = $('#idlangganan').find(':selected').data('komisi');
                var nominal = $('#idlangganan').find(':selected').data('nominal');
                var kode_referal = $('#kode_referal_tampil').val();
                var bulan = $('#bulan').val() == '' ? 0 : Number($('#bulan').val());
                var bulanNominal = nominal * bulan;
    
                if (kode_referal == '' || kode_referal == null) {
                    var total = bulanNominal;
                    var nominalDiskon = 0;
                    var diskontampil = 0;
                    $('#diskon_tampil').html('<label for="" > Harga Setelah Diskon </label>')
                } else {
                    var total = bulanNominal - (bulanNominal * diskon / 100);
                    var nominalDiskon = bulanNominal - (bulanNominal * diskon / 100);
                    var diskontampil = diskon;
                    $('#diskon_tampil').html('<label for="" > Harga Setelah Diskon ' + diskon + ' %</label>')
                }
                $('#nominals').val(bulanNominal);
                $('#nominalTampil').val(number_format(bulanNominal, 0, ".", "."));
                $('#nominalDiskon').val(number_format(nominalDiskon, 0, ".", "."));
                $('#diskon').val(diskontampil);
            }
        });
    }
});

</script>

<?php 
    $isProd = env('midtrans.isProduction');
    $snapUrl = $isProd ? "https://app.midtrans.com/snap/snap.js" : "https://app.sandbox.midtrans.com/snap/snap.js";
    $clientKey = env('midtrans.clientKey');
?>
<script src="<?= $snapUrl ?>" data-client-key="<?= $clientKey ?>"></script>
<script type="text/javascript">
$(document).ready(function() {
    
    
    $(document).on("click", ".btn-pilih-idpl", function () {
        var idpl = $(this).data('idpl');
        $("#idpl_selected").val(idpl);
    });
    
    // 1. Tangkap ID dari tombol Datatables
    $(document).on("click", ".btn-pilih-bayar", function (e) {
        e.preventDefault();
        var $btn = $(this);
        
        // Ambil data dari atribut tombol
        var nama_langganan = $(this).data('nama');
        var nominal = $(this).data('nominal');
        var kode_unik = $(this).data('kode_unik');
        var idpl    = $(this).data('idpl');
    
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
    
        // Kirim data menggunakan metode POST
        fetch('<?= base_url('payment/token-proses') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest' // Biar CI4 tahu ini request AJAX
            },
            body: JSON.stringify({
                nama_langganan: nama_langganan,
                nominal: nominal,
                kode_unik: kode_unik,
                idpl:idpl
            })
        })
        .then(res => res.json())
        .then(data => {
            $btn.prop('disabled', false).html(originalText);
    
            if (data.status === 'deleted') {
                window.location.reload(); 
                return;
            }
    
            if (data.token) {
                window.snap.pay(data.token, {
                    onSuccess: function(result) { window.location.reload(); },
                    onPending: function(result) { window.location.reload(); },
                    onClose: function() { $btn.prop('disabled', false); }
                });
            }
        })
        .catch(err => {
            $btn.prop('disabled', false).html(originalText);
            alert("Gagal memproses pembayaran");
        });
    });

    // 2. Event Klik untuk Pembayaran Otomatis (Midtrans)
    $(document).on("click", "#pay-button-midtrans", function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var idpl = $("#idpl_selected").val();

        

        if (!idpl) {
            alert("ID Pelanggan tidak valid!");
            return;
        }

        // Efek Loading
        $btn.css({'opacity': '0.5', 'pointer-events': 'none'});
        var originalContent = $btn.html();
        $btn.find('h6').html('<i class="bi bi-hourglass-split"></i> Loading Token...');

        // Fetch Token dari Controller
        fetch('<?= base_url('payment/getToken') ?>/' + idpl)
            .then(res => res.json())
            .then(data => {
                // Sembunyikan modal pilihan agar Snap tampil bersih
                $('#pilihPembayaran').modal('hide');
                // Eksekusi Snap
                table.draw();
                window.snap.pay(data.token,{});
            })
            .catch(err => {
                console.error(err);
                alert("Gagal mengambil data dari server!");
                $btn.css({'opacity': '1', 'pointer-events': 'auto'});
                $btn.html(originalContent);
            });
    });

    // 3. Event Klik untuk Transfer Manual
    $(document).on("click", "#btn-pilih-manual", function() {
        var idpl = $("#idpl_selected").val();
        
        // Gunakan loading state jika perlu
        $(this).prop('disabled', true).text('Processing...');
    
        fetch('<?= base_url('payment/updateToManual') ?>/' + idpl)
            .then(response => {
                if (!response.ok) throw new Error('Gagal menghubungkan ke server');
                return response.json();
            })
            .then(result => {
                if (result.status === 'success') {
                    // 1. Refresh table (jika menggunakan datatables)
                    if (typeof table !== 'undefined') table.draw();
    
                    // 2. Sembunyikan modal pilih dan munculkan modal upload
                    $('#pilihPembayaran').modal('hide');
                    $('#uploadFile').modal('show');
    
                    // 3. GUNAKAN DATA DARI CONTROLLER
                    // Contoh mengisi input di modal upload:
                    $('#idpl_upload_input').val(result.data.idpl);
                    $('#nama_langganan_upload').val(result.data.nama_langganan);
                    $('#nominalUpload').val(result.data.nominal);
                    $('#kode_unik').val(result.data.kode_unik);
                    
                }
            });
    });
});
</script>


<?= $this->endSection() ?>