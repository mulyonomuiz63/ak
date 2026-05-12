<?= $this->extend('template/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info" id="lbljudul">Form Dokumen</h6>
        </div>
        <div class="card-body">
            <form action="<?= site_url('dokumen-masuk/simpan') ?>" id="form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?= $id ?>">
                <input type="hidden" name="ltambah" id="ltambah" value="<?= $ltambah ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label>Keterangan Dokumen</label>
                            <input type="text" id="nama_file" name="nama_file" class="form-control" placeholder="Keterangan Dokumen">
                            <input type="hidden" id="idperusahaan" name="idperusahaan" value="<?= session()->get('idperusahaan'); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group <?= $id == '' ? 'required' : ''; ?>">
                            <label>File Gabungan</label>
                            <input type="file" id="file" name="file" class="form-control mb-2" accept="image/jpeg, image/jpg, image/png, application/pdf">
                            
                            <!-- Penampung Link View File Saat Edit -->
                            <div id="file_view_container" class="mt-2"></div>
                            
                            <input type="hidden" id="file_lama" name="file_lama">
                            <input type="hidden" id="kode_file_lama" name="kode_file_lama">
                            <input type="hidden" id="status" name="status" value="masuk">
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group required">
                            <label>Nama Pengirim/Pemilik Dokumen</label>
                            <input type="text" id="nama_pengirim" name="nama_pengirim" class="form-control">
                        </div>
                    </div>
                </div>

                <hr>
                <div class="text-right">
                    <a href="<?= site_url('dokumen-masuk') ?>" class="btn btn-danger">Kembali</a>
                    <button type="submit" id="simpan" class="btn btn-success">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Viewer -->
<div class="modal fade" id="modalcetakpdf" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="isiKonten"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var id_data = "<?= $id ?>";

    $(document).ready(function() {
        // --- LOGIKA EDIT DATA ---
        if (id_data != "") {
            $.ajax({
                type: 'POST',
                url: '<?= site_url("dokumen-masuk/get_edit_data") ?>',
                data: { id: id_data },
                dataType: 'json'
            }).done(function(result) {
                $('#id').val(result.id);
                $('#nama_file').val(result.nama_file);
                $('#nama_pengirim').val(result.nama_pengirim);
                $('#file_lama').val(result.file);
                $('#kode_file_lama').val(result.kode_file); // Simpan ID Drive jika ada

                // Penentuan URL View (Drive vs Server)
                var finalUrl = "";
                var labelLink = "";

                if (result.kode_file && result.kode_file !== "") {
                    // Jika ada di Google Drive
                    finalUrl = "https://drive.google.com/file/d/" + result.kode_file + "/preview";
                    labelLink = '<i class="fab fa-google-drive"></i> Lihat di Drive: ' + result.nama_file;
                } else {
                    // Jika masih di server lokal
                    finalUrl = "<?= site_url('uploads/arsip/thumbnails/') ?>" + result.file;
                    labelLink = '<i class="fas fa-server"></i> Lihat di Server: ' + result.file;
                }

                if(result.file || result.kode_file) {
                    $('#file_view_container').html(`
                        <div class="alert alert-secondary p-2">
                            <small class="d-block mb-1 text-muted">File saat ini:</small>
                            <a href="javascript:void(0)" 
                               id="cetak-pdf" 
                               data-cetak_pdf="${finalUrl}" 
                               class="btn btn-sm btn-info text-white">
                               ${labelLink}
                            </a>
                        </div>
                    `);
                }

                $('#lbljudul').html('Edit Data Dokumen');
            });
        } else {
            $('#lbljudul').html('Tambah Data Dokumen');
        }

        // --- VALIDASI FORM ---
        $('#form').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                nama_file: { validators: { notEmpty: { message: 'Keterangan tidak boleh kosong' } } },
                nama_pengirim: { validators: { notEmpty: { message: 'Nama pengirim tidak boleh kosong' } } },
                file: {
                    validators: {
                        callback: {
                            message: 'File wajib diunggah',
                            callback: function(value, validator, $field) {
                                // Jika tambah baru (id_data kosong), file wajib diisi
                                return (id_data !== "") ? true : (value !== "");
                            }
                        }
                    }
                }
            }
        });

        $("form").attr('autocomplete', 'off');
    });

    // --- MODAL VIEWER LOGIC ---
    $(document).on("click", "#cetak-pdf", function(e) {
        let url = $(this).data('cetak_pdf');
        
        // Pastikan url Drive menggunakan /preview agar tidak diblokir iframe
        if (url.includes('drive.google.com')) {
            url = url.replace('/view', '/preview');
        }

        $(".isiKonten").html(`
            <div class="modal-header">
                <h5 class="modal-title">Pratinjau Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="position: relative; height: 75vh; overflow: hidden;">
                <div id="loadingIframe" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; z-index: 10;">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="mt-2 small">Memuat dokumen...</div>
                </div>
                <iframe id="frameBukuBesar" src="${url}" width="100%" height="100%" style="border: none; opacity: 0; transition: opacity 0.5s;"></iframe>
            </div>
        `);

        $('#modalcetakpdf').modal('show');

        $("#frameBukuBesar").on("load", function() {
            $("#loadingIframe").fadeOut();
            $(this).css("opacity", "1");
        });
    });
</script>

<?= $this->endSection() ?>