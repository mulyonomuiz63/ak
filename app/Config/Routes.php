<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Landing::index');
$routes->get('tes', 'Landing::tes');
$routes->get('pssk-atc', 'Landing::pssk_atc');
$routes->get('validasi-jurnal/(:any)', 'ValidasiJurnal::index/$1');



//login
$routes->get('login', 'LoginController::index', ['filter' => 'guestFilter']);
$routes->post('cek-login', 'LoginController::cekLogin', ['filter' => 'guestFilter']);
$routes->get('registrasi', 'LoginController::registrasi', ['filter' => 'guestFilter']);
$routes->post('registrasi/(:any)', 'LoginController::registrasi/$1', ['filter' => 'guestFilter']);
$routes->post('simpanregistrasi', 'LoginController::simpanregistrasi', ['filter' => 'guestFilter']);
$routes->post('kirim-reset-password', 'LoginController::kirimResetPassword', ['filter' => 'guestFilter']);
$routes->get('lupapassword', 'LoginController::lupapassword', ['filter' => 'guestFilter']);
$routes->get('ubah-password/(:any)', 'LoginController::ubahPassword/$1', ['filter' => 'guestFilter']);
$routes->get('Login/verifikasi/(:segment)', 'LoginController::verifikasi/$1', ['filter' => 'guestFilter']);
$routes->get('Login/verifikasi/(:segment)/(:segment)', 'LoginController::verifikasi/$1/$2', ['filter' => 'guestFilter']);

$routes->get('logout', 'LoginController::logout');

$routes->group('iklan', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('', 'Iklan::index');
    $routes->get('deleteAll', 'Iklan::deleteAll');
    $routes->get('tambah', 'Iklan::tambah');
    $routes->post('datatablesource', 'Iklan::datatablesource');
    $routes->post('simpan', 'Iklan::simpan');
    $routes->post('get_edit_data', 'Iklan::get_edit_data');
    $routes->get('edit/(:segment)', 'Iklan::edit/$1');
    $routes->get('delete/(:segment)', 'Iklan::delete/$1');
});

$routes->group('event', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('', 'Event::index');
    $routes->get('deleteAll', 'Event::deleteAll');
    $routes->get('tambah', 'Event::tambah');
    $routes->post('datatablesource', 'Event::datatablesource');
    $routes->post('simpan', 'Event::simpan');
    $routes->post('get_edit_data', 'Event::get_edit_data');
    $routes->get('edit/(:segment)', 'Event::edit/$1');
    $routes->get('delete/(:segment)', 'Event::delete/$1');
});

$routes->group('dashboard', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('', 'DashboardController::index');
    $routes->post('get_grafik_penjualan', 'DashboardController::get_grafik_penjualan');
    $routes->post('get_grafik_biaya', 'DashboardController::get_grafik_biaya');
});

//perusahaan
$routes->group('perusahaan', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('/', 'PerusahaanController::index');
    $routes->post('datatablesource', 'PerusahaanController::datatablesource');
    $routes->post('autocomplate', 'PerusahaanController::autocomplate');
    $routes->get('tambah', 'PerusahaanController::tambah');
    $routes->get('edit/(:segment)', 'PerusahaanController::edit/$1');
    $routes->post('get-edit', 'PerusahaanController::getEdit');
    $routes->post('store', 'PerusahaanController::store');
    $routes->post('delete-all', 'PerusahaanController::deleteAll');

    //export data
    // --- ROUTES UNTUK EXPORT DATA (CHUNK/BATCH PROCESSING) ---
    $routes->post('export-init', 'ExportDataController::export_init');
    $routes->post('export-process', 'ExportDataController::export_process');
    $routes->post('export-finalize', 'ExportDataController::export_finalize');
    $routes->get('download-zip/(:segment)', 'ExportDataController::download_zip/$1');
});


//pengguna
$routes->group('pengguna', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('/', 'PenggunaController::index');
    $routes->post('datatablesource', 'PenggunaController::datatablesource');
    $routes->get('tambah', 'PenggunaController::tambah');
    $routes->get('edit/(:segment)', 'PenggunaController::edit/$1');
    $routes->post('get-edit', 'PenggunaController::getEdit');
    $routes->post('store', 'PenggunaController::store');
    $routes->post('delete-all', 'PenggunaController::deleteAll');
});

//akun
$routes->group('akun', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('/', 'AkunController::index');
    $routes->post('datatablesource', 'AkunController::datatablesource');
    $routes->post('autocomplate', 'AkunController::autocomplate');
    $routes->get('tambah', 'AkunController::tambah');
    $routes->get('edit/(:segment)', 'AkunController::edit/$1');
    $routes->post('get-edit', 'AkunController::getEdit');
    $routes->post('store', 'AkunController::store');
    $routes->post('store-excel', 'AkunController::storeExcel');
    $routes->post('delete-all', 'AkunController::deleteAll');
    $routes->get('delete/(:segment)', 'AkunController::delete/$1');
    $routes->get('update-kode-akun', 'AkunController::updateAkunAll');
    $routes->get('status/(:segment)/(:segment)', 'AkunController::status/$1/$2');
    $routes->get('export-excel', 'AkunController::exportExcel');
    $routes->get('export', 'AkunController::export');
});

//jurnal
$routes->group('jurnal', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('/', 'JurnalController::index');
    $routes->post('/', 'JurnalController::index');
    $routes->post('datatablesource', 'JurnalController::datatablesource');
    $routes->post('get_detail_jurnal', 'JurnalController::get_detail_jurnal');
    $routes->post('autocomplate', 'JurnalController::autocomplate');
    $routes->post('autocomplatePerusahaan', 'JurnalController::autocomplatePerusahaan');
    $routes->get('tambah', 'JurnalController::tambah');
    $routes->get('tambah/(:segment)', 'JurnalController::tambah/$1');
    $routes->get('edit/(:segment)', 'JurnalController::edit/$1');
    $routes->get('edit/(:segment)/(:segment)', 'JurnalController::edit/$1/$2');
    $routes->post('get-edit', 'JurnalController::getEdit');
    $routes->post('store', 'JurnalController::store');
    $routes->post('delete-all', 'JurnalController::deleteAll');
    $routes->get('delete/(:segment)', 'JurnalController::delete/$1');
    $routes->post('get-file', 'JurnalController::getFile');
    $routes->get('lihat/(:segment)', 'JurnalController::lihat/$1');
    $routes->get('delete-file-new/(:segment)/(:segment)/(:segment)', 'JurnalController::deleteFileNew/$1/$2/$3');
    $routes->get('delete-file/(:segment)/(:segment)', 'JurnalController::deleteFile/$1/$2');
    $routes->get('notif', 'JurnalController::notif');


    //supervisor
    $routes->post('simpanApprove', 'JurnalController::simpanApprove');

    //untuk validasi fiskal
    $routes->post('simpan-fiskal', 'JurnalController::simpanFiskal');
});

//laporan
$routes->group('laporan', ['filter' => 'authFilter'], function ($routes) {
    //laporan jurnal
    $routes->get('lapjurnal', 'Laporan\LapJurnalController::index');
    $routes->post('lapjurnal', 'Laporan\LapJurnalController::index');
    $routes->get('lapjurnal-cetak/(:segment)/(:segment)/(:segment)', 'Laporan\LapJurnalController::lapJurnalCetak/$1/$2/$3');
    $routes->get('lapjurnal-cetak-excel/(:segment)/(:segment)/(:segment)', 'Laporan\LapJurnalController::LapJurnalCetakExcel/$1/$2/$3');
    $routes->post('fetch', 'Laporan\LapJurnalController::fetch');
    $routes->post('total-fetch', 'Laporan\LapJurnalController::totalFetch');

    //laporan buku besar
    $routes->get('lapbukubesar', 'Laporan\LapBukuBesarController::index');
    $routes->post('lapbukubesar', 'Laporan\LapBukuBesarController::index');
    $routes->get('lapbukubesar-cetak/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\LapBukuBesarController::lapBukuBesarCetak/$1/$2/$3/$4/$5');
    $routes->get('lapbukubesar-cetak-semua/(:segment)/(:segment)/(:segment)', 'Laporan\LapBukuBesarController::lapBukuBesarCetakSemua/$1/$2/$3');
    $routes->get('lapbukubesar-excel/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\LapBukuBesarController::lapBukuBesarExcel/$1/$2/$3/$4/$5');
    $routes->get('lapbukubesar-excel-semua/(:segment)/(:segment)/(:segment)', 'Laporan\LapBukuBesarController::lapBukuBesarExcelSemua/$1/$2/$3');
    $routes->post('fetchLapbukuBesar', 'Laporan\LapBukuBesarController::fetchLapbukuBesar');
    $routes->post('totalFetchBukubesar', 'Laporan\LapBukuBesarController::totalFetchBukubesar');

    //laporan posisi keuangan
    $routes->get('lapposisikeuangan', 'Laporan\LapPosisiKeuanganController::index');
    $routes->post('lapposisikeuangan', 'Laporan\LapPosisiKeuanganController::index');
    $routes->get('lapposisikeuangan-cetak/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\LapPosisiKeuanganController::lapPosisiKeuanganCetak/$1/$2/$3/$4');
    $routes->get('lapposisikeuangan-excel/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\LapPosisiKeuanganController::lapPosisiKeuanganExcel/$1/$2/$3/$4');

    //laporan laba rugi
    $routes->get('laplabarugi', 'Laporan\LapLabaRugiController::index');
    $routes->post('laplabarugi', 'Laporan\LapLabaRugiController::index');
    $routes->get('laplabarugi-cetak/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\LapLabaRugiController::lapLabaRugiCetak/$1/$2/$3/$4');
    $routes->get('laplabarugi-excel/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\LapLabaRugiController::lapLabaRugiugiExcel/$1/$2/$3/$4');

    //laporan ekuitas
    $routes->get('lapperubahanekuitas', 'Laporan\LapPerubahanEkuitasController::index');
    $routes->post('lapperubahanekuitas', 'Laporan\LapPerubahanEkuitasController::index');
    $routes->get('lapperubahanekuitas-cetak/(:segment)/(:segment)/(:segment)', 'Laporan\LapPerubahanEkuitasController::lapPerubahanEkuitasCetak/$1/$2/$3');

    //laporan laprasio
    $routes->get('laprasio', 'Laporan\LapRasioController::index');
    $routes->post('laprasio', 'Laporan\LapRasioController::index');
    $routes->get('laprasio-cetak/(:segment)/(:segment)', 'Laporan\LapRasioController::lapRasioCetak/$1/$2');
    $routes->get('laprasio-excel/(:segment)/(:segment)', 'Laporan\LapRasioController::lapRasioExcel/$1/$2');

    //laporan koreksi fiskal
    $routes->get('lapkoreksifiskal', 'Laporan\LapKoreksiFiskalController::index');
    $routes->post('lapkoreksifiskal', 'Laporan\LapKoreksiFiskalController::index');
    $routes->get('lapkoreksifiskal-cetak/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\LapKoreksiFiskalController::lapKoreksiFiskalCetak/$1/$2/$3/$4');
    $routes->get('lapkoreksifiskal-excel/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\LapKoreksiFiskalController::lapKoreksiFiskalExcel/$1/$2/$3/$4');
    
    //laporan objek pajak
    $routes->get('lapobjekpajak', 'Laporan\LapObjekPajakController::index');
    $routes->post('lapobjekpajak', 'Laporan\LapObjekPajakController::index');
    $routes->get('lapobjekpajak-cetak/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\LapObjekPajakController::lapObjekPajakCetak/$1/$2/$3/$4');
    $routes->get('lapobjekpajak-excel/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\LapObjekPajakController::lapObjekPajakExcel/$1/$2/$3/$4');

});

$routes->group('migrasi', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('/', 'MigrasiController::index');
    $routes->post('datatablesource', 'MigrasiController::datatablesource');
    $routes->post('proses-upload', 'MigrasiController::prosesUpload');
    $routes->post('autocomplatePerusahaan', 'MigrasiController::autocomplatePerusahaan');
});

$routes->group('migrasi-arsip', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('/', 'MigrasiArsipController::index');
    $routes->post('datatablesource', 'MigrasiArsipController::datatablesource');
    $routes->post('proses-upload', 'MigrasiArsipController::prosesUpload');
    $routes->post('autocomplatePerusahaan', 'MigrasiArsipController::autocomplatePerusahaan');
});

$routes->group('migrasi-file-utama', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('/', 'MigrasiFileUtamaController::index');
    $routes->post('datatablesource', 'MigrasiFileUtamaController::datatablesource');
    $routes->post('proses-upload', 'MigrasiFileUtamaController::prosesUpload');
    $routes->post('autocomplatePerusahaan', 'MigrasiFileUtamaController::autocomplatePerusahaan');
});


//reload
$routes->post('check-reload', 'CheckReload::index');

//unutk registrasi menggunakan kode referal


//dokumen masuk
$routes->group('dokumen-masuk', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('', 'DokumenMasuk::index');
    $routes->post('datatablesource', 'DokumenMasuk::datatablesource');
    $routes->get('edit/(:any)', 'DokumenMasuk::edit/$1');
    $routes->get('tambah', 'DokumenMasuk::tambah');
    $routes->get('delete/(:any)', 'DokumenMasuk::delete/$1');
    $routes->post('deleteAll', 'DokumenMasuk::deleteAll');
    $routes->post('get_edit_data', 'DokumenMasuk::get_edit_data');
    $routes->post('simpan', 'DokumenMasuk::simpan');
    $routes->get('viewfile/(:any)', 'DokumenMasuk::viewfile/$1');
});

//dokumen keluar
$routes->get('dokumen-keluar', 'DokumenKeluar::index', ['filter' => 'authFilter']);
$routes->get('dokumen-keluar/edit/(:any)', 'DokumenKeluar::edit/$1', ['filter' => 'authFilter']);
$routes->get('dokumen-keluar/tambah', 'DokumenKeluar::tambah', ['filter' => 'authFilter']);
$routes->get('dokumen-keluar/delete/(:any)', 'DokumenKeluar::delete/$1', ['filter' => 'authFilter']);
$routes->post('dokumen-keluar/deleteAll', 'DokumenKeluar::deleteAll', ['filter' => 'authFilter']);
$routes->post('dokumen-keluar/get_edit_data', 'DokumenKeluar::get_edit_data', ['filter' => 'authFilter']);
$routes->post('dokumen-keluar/simpan', 'DokumenKeluar::simpan', ['filter' => 'authFilter']);
$routes->get('dokumen-keluar/viewfile/(:any)', 'DokumenKeluar::viewfile/$1', ['filter' => 'authFilter']);

$routes->get('dokumen-arsip/viewfile/(:any)', 'DokumenMasuk::viewfilejurnal/$1', ['filter' => 'authFilter']);
$routes->get('dokumen-arsip/(:any)', 'DokumenMasuk::tambahArsip/$1');
$routes->post('dokumen-arsip/simpan', 'DokumenMasuk::simpanArsip');






//affiliator
$routes->get('affiliator/registrasi', 'Affiliator\Login::registrasi');
$routes->get('affiliator/lupapassword', 'Affiliator\Login::lupapassword');




// $routes->get('/', 'Login::index');
//diskon
$routes->group('diskon', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('', 'Affiliator\Diskon::index');
    $routes->post('datatablesource', 'Affiliator\Diskon::datatablesource');
    $routes->get('edit/(:any)', 'Affiliator\Diskon::edit/$1');
    $routes->get('tambah', 'Affiliator\Diskon::tambah');
    $routes->get('delete/(:any)', 'Affiliator\Diskon::delete/$1');
    $routes->post('get_edit_data', 'Affiliator\Diskon::get_edit_data');
    $routes->post('simpan', 'Affiliator\Diskon::simpan');
});

//komisi
$routes->group('komisi', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('', 'Affiliator\Komisi::index');
    $routes->post('datatablesource', 'Affiliator\Komisi::datatablesource');
    $routes->get('edit/(:any)', 'Affiliator\Komisi::edit/$1');
    $routes->get('tambah', 'Affiliator\Komisi::tambah');
    $routes->get('delete/(:any)', 'Affiliator\Komisi::delete/$1');
    $routes->post('get_edit_data', 'Affiliator\Komisi::get_edit_data');
    $routes->post('simpan', 'Affiliator\Komisi::simpan');
});

//Harga
$routes->group('harga', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('', 'Affiliator\Harga::index');
    $routes->post('datatablesource', 'Affiliator\Harga::datatablesource');
    $routes->get('edit/(:any)', 'Affiliator\Harga::edit/$1');
    $routes->get('tambah', 'Affiliator\Harga::tambah');
    $routes->get('delete/(:any)', 'Affiliator\Harga::delete/$1');
    $routes->post('get_edit_data', 'Affiliator\Harga::get_edit_data');
    $routes->post('simpan', 'Affiliator\Harga::simpan');
});

//Histori
$routes->group('histori', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('', 'Affiliator\Histori::index');
    $routes->post('datatablesource', 'Affiliator\Histori::datatablesource');
    $routes->post('datatablesourceDetail', 'Affiliator\Histori::datatablesourceDetail');
    $routes->get('verifikasi', 'Affiliator\Histori::verifikasi');
    $routes->get('tidakaktif', 'Affiliator\Histori::tidakaktif');
    $routes->post('get_edit_data', 'Affiliator\Histori::get_edit_data');
    $routes->post('simpan', 'Affiliator\Histori::simpan');
    $routes->post('uploadPembayaran', 'Affiliator\Histori::uploadPembayaran');
    $routes->post('approve', 'Affiliator\Histori::approve');
    //detail
    $routes->get('detail/(:any)', 'Affiliator\Histori::detail/$1');
    $routes->post('get_edit_dataDetail', 'Affiliator\Histori::get_edit_dataDetail');
    $routes->get('batalkan-pesanan', 'Affiliator\Histori::batalkan_pesanan');
});

//Komisi marketer
$routes->group('komisi-marketer', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('', 'Affiliator\KomisiMarketer::index');
    $routes->post('datatablesource', 'Affiliator\KomisiMarketer::datatablesource');
    $routes->post('get_edit_data', 'Affiliator\KomisiMarketer::get_edit_data');
    $routes->post('simpan', 'Affiliator\KomisiMarketer::simpan');
    $routes->post('uploadPembayaran', 'Affiliator\KomisiMarketer::uploadPembayaran');
    $routes->post('approve', 'Affiliator\KomisiMarketer::approve');
});


//marketer
$routes->group('marketer', ['filter' => 'authFilter'], function ($routes) {
    $routes->get('', 'Affiliator\MarketerList::index');
    $routes->post('datatablesource', 'Affiliator\MarketerList::datatablesource');
    $routes->get('simpan', 'Affiliator\Marketer::simpan');
    $routes->get('cek-hp', 'Affiliator\Marketer::cekNohp');
    $routes->get('cek-nik', 'Affiliator\Marketer::cekNik');
});

//laporan
$routes->group('laporan', ['filter' => 'authFilter'], function ($routes) {
    $routes->post('fetch', 'Laporan::fetch');
    $routes->post('fetchLapbukuBesar', 'Laporan::fetchLapbukuBesar');
});

$routes->group('payment', ['filter' => 'authFilter'], function ($routes) {
    $routes->post('token-proses', 'Payment::tokenProses');
    $routes->get('getToken/(:any)', 'Payment::getToken/$1');
    $routes->get('notification', 'Payment::prosesVerifikasi');
    $routes->get('updateToManual/(:any)', 'Payment::updateToManual/$1');
});


$routes->get('auth/admin-drive', 'LoginController::authAdminDrive');
$routes->get('google-callback-admin', 'LoginController::adminDriveCallback');

//maintenance mode
$routes->get('maintenance', 'Maintenance::index');
