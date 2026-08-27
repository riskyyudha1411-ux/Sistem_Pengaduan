<?php if (isset($isLoggedIn) && $isLoggedIn): ?>
<?= $this->extend('layout/template') ?>
<?php else: ?>
<?= $this->extend('layout/public_template') ?>
<?php endif; ?>

<?= $this->section('content') ?>

<!-- Success Box with Ticket Details (Shown after public complaint is submitted) -->
<?php if (session()->getFlashdata('tiket_sukses')): ?>
  <div class="card shadow border-0 mb-4 bg-light-primary border-primary border-opacity-25">
    <div class="card-body p-4 text-center">
      <div class="mb-3">
        <span class="badge bg-success rounded-circle p-3 d-inline-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
          <i class="ti ti-check fs-7 text-white"></i>
        </span>
      </div>
      <h4 class="fw-bold text-dark mb-1">Aduan Anda Berhasil Dikirim!</h4>
      <p class="text-muted fs-3 mb-3">Terima kasih atas laporan yang Anda sampaikan. Simpan Nomor Tiket di bawah ini untuk memantau proses tindak lanjut:</p>
      
      <div class="d-inline-flex align-items-center gap-2 bg-white px-4 py-2 rounded-3 border border-primary border-2 shadow-sm mb-3">
        <span class="fs-6 fw-bold text-primary" id="tiketNumber"><?= session()->getFlashdata('tiket_sukses') ?></span>
        <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="navigator.clipboard.writeText('<?= session()->getFlashdata('tiket_sukses') ?>'); alert('Nomor tiket berhasil disalin!');">
          <i class="ti ti-copy"></i> Salin
        </button>
      </div>

      <div class="d-flex flex-wrap justify-content-center gap-2 mt-2">
        <a href="<?= base_url('aduan/lacak?tiket=' . session()->getFlashdata('tiket_sukses')) ?>" class="btn btn-primary d-flex align-items-center gap-2">
          <i class="ti ti-search fs-4"></i> Lacak Status Aduan Ini
        </a>
        <a href="<?= base_url('aduan/buat') ?>" class="btn btn-outline-dark">
          <i class="ti ti-plus fs-4"></i> Buat Aduan Lainnya
        </a>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
      <div>
        <h5 class="card-title fw-semibold mb-1 text-dark">Buat Aduan Baru</h5>
        <p class="text-muted mb-0 fs-2">Sampaikan keluhan, saran, atau aduan Anda seputar kegiatan pelatihan di BAPELKES Jawa Tengah.</p>
      </div>
      <?php if (!isset($isLoggedIn) || !$isLoggedIn): ?>
        <div>
          <a href="<?= base_url('aduan/lacak') ?>" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
            <i class="ti ti-search fs-4"></i> Lacak Aduan Sebelumnya
          </a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Validation Errors -->
    <?php if (session()->getFlashdata('errors')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0 ps-3">
          <?php foreach (session()->getFlashdata('errors') as $error): ?>
            <li><?= $error ?></li>
          <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Flash Error -->
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <form action="<?= base_url('aduan/simpan') ?>" method="POST" enctype="multipart/form-data">
      <?= csrf_field() ?>
      
      <div class="row mb-3">
        <?php if (!isset($isLoggedIn) || !$isLoggedIn): ?>
          <div class="col-md-6 mb-3">
            <label for="nama_pelapor" class="form-label fw-semibold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="nama_pelapor" id="nama_pelapor" class="form-control" placeholder="Tuliskan nama lengkap Anda" value="<?= old('nama_pelapor') ?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="no_telepon" class="form-label fw-semibold text-dark">Nomor WhatsApp / No. HP <span class="text-muted fw-normal fs-2">(Opsional)</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-success"><i class="ti ti-brand-whatsapp fs-4"></i></span>
              <input type="tel" name="no_telepon" id="no_telepon" class="form-control" placeholder="Contoh: 081234567890" value="<?= old('no_telepon') ?>">
            </div>
            <div class="form-text fs-2 text-muted">Nomor WhatsApp aktif untuk menerima info respon tindak lanjut dari admin.</div>
          </div>
        <?php endif; ?>

        <div class="col-md-6">
          <label for="nama_pelatihan" class="form-label fw-semibold text-dark">Nama Pelatihan yang Diikuti <span class="text-danger">*</span></label>
          <input type="text" name="nama_pelatihan" id="nama_pelatihan" class="form-control" placeholder="Contoh: Pelatihan Kepemimpinan Pengawas Angkatan V" value="<?= old('nama_pelatihan') ?>" required>
          <div class="form-text fs-2 text-muted">Tuliskan nama pelatihan secara lengkap.</div>
        </div>
        <div class="col-md-6">
          <label for="kategori" class="form-label fw-semibold text-dark">Kategori Aduan <span class="text-danger">*</span></label>
          <select name="kategori" id="kategori" class="form-select" required>
            <option value="" disabled selected>-- Pilih Kategori --</option>
            <option value="Fasilitas" <?= old('kategori') === 'Fasilitas' ? 'selected' : '' ?>>Fasilitas (AC, Kamar, Kelas, Toilet, Wifi, dll)</option>
            <option value="Pelayanan" <?= old('kategori') === 'Pelayanan' ? 'selected' : '' ?>>Pelayanan (Petugas, Panitia, Keamanan, dll)</option>
            <option value="Konsumsi" <?= old('kategori') === 'Konsumsi' ? 'selected' : '' ?>>Konsumsi (Makanan, Minuman, Snack, dll)</option>
            <option value="Lainnya" <?= old('kategori') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label for="judul" class="form-label fw-semibold text-dark">Judul Aduan <span class="text-danger">*</span></label>
        <input type="text" name="judul" id="judul" class="form-control" placeholder="Tuliskan ringkasan pokok aduan Anda" value="<?= old('judul') ?>" required>
      </div>

      <div class="mb-3">
        <label for="deskripsi" class="form-label fw-semibold text-dark">Detail Deskripsi Aduan <span class="text-danger">*</span></label>
        <textarea name="deskripsi" id="deskripsi" rows="5" class="form-control" placeholder="Ceritakan secara detail mengenai permasalahan yang dialami..." required><?= old('deskripsi') ?></textarea>
      </div>

      <div class="mb-4">
        <label for="lampiran" class="form-label fw-semibold text-dark">Bukti Lampiran (Opsional)</label>
        <input type="file" name="lampiran" id="lampiran" class="form-control">
        <div class="form-text fs-2 text-muted">Format yang didukung: JPG, JPEG, PNG, PDF, DOC, DOCX (Maksimal 2MB).</div>
      </div>

      <div class="d-flex justify-content-between align-items-center border-top pt-4">
        <?php if (isset($isLoggedIn) && $isLoggedIn): ?>
          <a href="<?= base_url('aduan') ?>" class="btn btn-outline-dark">Batal</a>
        <?php else: ?>
          <a href="<?= base_url('login') ?>" class="btn btn-outline-dark">Kembali ke Halaman Utama</a>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
          <i class="ti ti-send fs-4"></i> Kirim Aduan
        </button>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>

