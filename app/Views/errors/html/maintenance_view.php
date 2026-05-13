<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Diperbarui</title>
    
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* Pengaturan Dasar dengan Animasi Background Gradasi */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(-45deg, #e2e8f0, #f8fafc, #dbeafe, #f1f5f9);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #334155;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Kotak Konten Utama (Gaya Modern Card) */
        .maintenance-container {
            text-align: center;
            padding: 50px 40px;
            max-width: 550px;
            width: 90%;
            background: #ffffff;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        /* Garis Aksen Halus di Atas */
        .maintenance-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #3b82f6, #60a5fa, #8b5cf6);
        }

        /* Badge Status Maintenance */
        .status-badge {
            display: inline-flex;
            align-items: center;
            background-color: #fffbeb;
            color: #d97706;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            border: 1px solid #fde68a;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #f59e0b;
            border-radius: 50%;
            margin-right: 8px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(245, 158, 11, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }

        /* Animasi Gambar Mengambang & Trik Blend Mode */
        .illustration {
            width: 100%;
            max-width: 280px;
            margin-bottom: 10px;
            animation: float 5s ease-in-out infinite; 
            /* Trik agar background putih pada gambar jpg/vektor jadi transparan! */
            mix-blend-mode: multiply; 
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
            100% { transform: translateY(0px); }
        }

        /* Tipografi */
        h1 {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .icon-gear {
            color: #3b82f6;
            animation: spin 6s linear infinite;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        p.message {
            font-size: 15px;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        /* Kotak Info Kontak (Desain Baru) */
        .contact-support {
            background: #f8fafc;
            border-radius: 16px;
            padding: 16px 24px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #334155;
            transition: all 0.3s ease;
            text-decoration: none;
            border: 1px solid #e2e8f0;
        }

        .contact-support:hover {
            background: #f1f5f9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .contact-support i {
            color: #3b82f6;
            margin-right: 10px;
            font-size: 20px;
        }

        .contact-support b {
            color: #0f172a;
            margin-left: 5px;
        }

        .footer-text {
            margin-top: 35px;
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="maintenance-container">
        
        <!-- Badge Laporan Status Sistem -->
        <div class="status-badge">
            <span class="pulse-dot"></span> System Maintenance
        </div>

        <!-- Gambar ilustrasi dengan efek blend mode (background putih hilang otomatis) -->
        <img src="https://img.freepik.com/free-vector/programmer-working-concept-illustration_114360-2300.jpg" alt="Teknisi IT Sedang Maintenance" class="illustration">        
        
        <h1><i class="fas fa-cog icon-gear"></i> Sedang Diperbarui</h1>
        
        <p class="message">
            Kami sedang melakukan peningkatan server dan pemeliharaan sistem rutin untuk performa yang lebih optimal. <strong>Mohon kembali lagi dalam beberapa saat.</strong>
        </p>

        <!-- Kotak Kontak bergaya Button -->
        <a href="mailto:support@<?= parse_url(base_url(), PHP_URL_HOST) ?>" class="contact-support">
            <i class="fas fa-envelope-open-text"></i> Butuh bantuan mendesak? Hubungi <b>support@<?= parse_url(base_url(), PHP_URL_HOST) ?></b>
        </a>

        <div class="footer-text">
            &copy; 2023 - Akuntanmu. All rights reserved.
        </div>
    </div>

</body>
</html>