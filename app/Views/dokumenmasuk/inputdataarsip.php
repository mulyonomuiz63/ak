<!doctype html>
<html lang="id">
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Upload Dokumen Arsip - Akuntanmu</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            color: #333;
        }

        .login-box {
            background: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin: 5vh auto;
            min-height: 80vh;
        }

        .left-side {
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .right-side {
            background: linear-gradient(135deg, #055F93 0%, #3BC3FF 100%);
            padding: 50px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .logo-font {
            font-weight: 700;
            color: #055F93;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            font-size: 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3BC3FF;
            box-shadow: 0 0 0 0.2rem rgba(59, 195, 255, 0.25);
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }

        .btn-success {
            background-color: #0F9D58;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background-color: #0b8043;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(15, 157, 88, 0.3);
        }

        .btn-danger {
            background-color: #f1f3f4;
            color: #5f6368;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #e8eaed;
            color: #202124;
        }

        .note-box {
            background-color: #e8f0fe;
            border-left: 4px solid #4285f4;
            padding: 15px;
            border-radius: 4px;
            font-size: 13px;
            color: #1967d2;
            margin-top: 30px;
        }

        /* Carousel Styling */
        .carousel-item img {
            max-height: 200px;
            object-fit: contain;
            margin-bottom: 20px;
        }
        .carousel-indicators {
            bottom: -40px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-box row g-0">
            
            <!-- KIRI: Form Input -->
            <div class="col-md-7 left-side">
                <div class="mb-4">
                    <h4 class="logo-font"><i class="fas fa-building me-2"></i> <?= $namaperusahaan; ?></h4>
                    <p class="text-muted">Silakan unggah dokumen arsip Anda di sini.</p>
                </div>

                <?php if (!empty(session()->getFlashData('pesan'))) : ?>
                    <?= session()->getFlashData('pesan'); ?>
                <?php endif; ?>

                <form action="<?= site_url('dokumen-arsip/simpan') ?>" id="form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="ltambah" id="ltambah" value="1">
                    <input type="hidden" id="idperusahaan" name="idperusahaan" value="<?= $idperusahaan ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Keterangan Dokumen <span class="text-danger">*</span></label>
                            <input type="text" id="nama_file" name="nama_file" class="form-control" placeholder="Contoh: Invoice Bulan Mei" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">File Gabungan <span class="text-danger">*</span></label>
                            <input type="file" id="file" name="file" class="form-control" required accept="image/jpeg, image/jpg, image/png, application/pdf">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jenis Dokumen <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="" selected disabled>-- Pilih Jenis --</option>
                                <option value="masuk">Tagihan Masuk</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Pengirim/Pemilik <span class="text-danger">*</span></label>
                            <input type="text" id="nama_pengirim" name="nama_pengirim" class="form-control" placeholder="Nama entitas pengirim" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 gap-2">
                        <a href="<?= site_url('/') ?>" class="btn btn-danger"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                        <button type="submit" id="simpan" class="btn btn-success"><i class="fas fa-cloud-upload-alt me-1"></i> Kirim Dokumen</button>
                    </div>
                </form>

                <div class="note-box">
                    <strong><i class="fas fa-info-circle me-1"></i> Catatan Penting:</strong><br>
                    Jika dokumen dan lampiran tagihan lebih dari satu file, maka seluruh dokumen harus <b>digabung menjadi satu file</b> (misal: PDF). Seperti: Invoice/Kuitansi, Faktur Pajak, PO/SPH/SPK, BAST/DO, dll.
                </div>
            </div>

            <!-- KANAN: Slider / Info -->
            <div class="col-md-5 right-side d-none d-md-flex">
                <div id="demo" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#demo" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#demo" data-bs-slide-to="1"></button>
                    </div>
                    
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="<?= base_url('images/upload.jpg'); ?>" class="img-fluid" alt="Upload">
                            <h3 class="mt-4 fw-bold">Upload Dokumen Masuk & Keluar</h3>
                            <p class="text-light opacity-75">Simpan bukti transaksi perusahaan dengan aman menggunakan sistem penyimpanan cloud.</p>
                        </div>
                        <div class="carousel-item">
                            <img src="https://i.imgur.com/Yi5KXKM.png" class="img-fluid" alt="Arsip">
                            <h3 class="mt-4 fw-bold">Memudahkan Pengarsipan</h3>
                            <p class="text-light opacity-75">Cari dan kelola dokumen tagihan masuk perusahaan dengan cepat kapan saja.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>