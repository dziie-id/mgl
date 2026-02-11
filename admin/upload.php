<?php include 'header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Upload Galeri Baru</h5>
            </div>
            <div class="card-body p-4">
                <form action="proses-upload.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nama Projek / Pelanggan</label>
                        <input type="text" name="project_name" class="form-control form-control-lg border-primary" placeholder="Contoh: Branding Bus Transjakarta" required>
                        <div class="form-text">Nama ini akan menjadi nama file otomatis.</div>
                    </div>
                    <div class="mb-4 text-center p-4 border border-dashed rounded">
                        <i class="fa-solid fa-file-image fa-3x text-muted mb-3"></i>
                        <input type="file" name="fotos[]" multiple class="form-control" required>
                        <div class="form-text mt-2">Pilih file dari Galeri atau File Manager (Format: JPG, PNG, WEBP).</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Kategori Layanan:</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="kategori[]" value="Branding Mobil" id="cat1">
                                <label class="form-check-label" for="cat1">Branding Mobil</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="kategori[]" value="Kaca Film" id="cat2">
                                <label class="form-check-label" for="cat2">Kaca Film</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="kategori[]" value="Sticker" id="cat3">
                                <label class="form-check-label" for="cat3">Sticker</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Opsi Watermark:</label>
                            <select name="use_watermark" class="form-select border-primary shadow-sm">
                                <option value="1">🔥 Ya, Pakai Watermark</option>
                                <option value="0">❌ Polos (Tanpa Watermark)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-muted">Aksi:</label>
                            <button type="submit" class="btn btn-primary w-100 shadow">
                                <i class="fa-solid fa-save me-2"></i> Proses Upload
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>