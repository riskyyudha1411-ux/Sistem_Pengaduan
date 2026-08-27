<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="card shadow-sm">
  <div class="card-body p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
      <div>
        <h5 class="card-title fw-semibold mb-1 text-dark">Daftar Laporan Aduan</h5>
        <p class="text-muted mb-0 fs-2">Kelola dan pantau seluruh status aduan peserta pelatihan.</p>
      </div>
      <?php if (session()->get('role') === 'user'): ?>
        <div>
          <a href="<?= base_url('aduan/buat') ?>" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="ti ti-circle-plus fs-5"></i> Buat Aduan Baru
          </a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Filters & Search Form -->
    <form action="<?= base_url('aduan') ?>" method="GET" class="row g-3 mb-4 p-3 bg-light rounded">
      <div class="col-md-3">
        <label for="status" class="form-label fs-2 fw-semibold text-muted">Filter Status</label>
        <select name="status" id="status" class="form-select fs-3" onchange="this.form.submit()">
          <option value="">-- Semua Status --</option>
          <option value="Pending" <?= $status_filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
          <option value="Proses" <?= $status_filter === 'Proses' ? 'selected' : '' ?>>Diproses</option>
          <option value="Selesai" <?= $status_filter === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
        </select>
      </div>
      <div class="col-md-6">
        <label for="search" class="form-label fs-2 fw-semibold text-muted font-weight-bold">Cari Aduan</label>
        <div class="input-group">
          <input type="text" name="search" id="search" class="form-control fs-3" placeholder="Cari judul, nomor tiket, atau nama pelatihan..." value="<?= esc($search_filter) ?>">
          <button type="submit" class="btn btn-dark">
            <i class="ti ti-search me-1"></i> Cari
          </button>
        </div>
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <?php if ($status_filter || $search_filter): ?>
          <a href="<?= base_url('aduan') ?>" class="btn btn-outline-danger w-100 fs-3 py-2">
            <i class="ti ti-rotate"></i> Reset Filter
          </a>
        <?php endif; ?>
      </div>
    </form>

    <!-- Table of Complaints -->
    <div class="table-responsive">
      <table class="table text-nowrap mb-0 align-middle">
        <thead class="text-dark fs-3">
          <tr>
            <th class="border-bottom-1"><h6 class="fw-semibold mb-0">No. Tiket</h6></th>
            <th class="border-bottom-1"><h6 class="fw-semibold mb-0">Nama Pelapor</h6></th>
            <th class="border-bottom-1"><h6 class="fw-semibold mb-0">Aduan & Pelatihan</h6></th>
            <th class="border-bottom-1"><h6 class="fw-semibold mb-0">Kategori</h6></th>
            <th class="border-bottom-1"><h6 class="fw-semibold mb-0">Tanggal Masuk</h6></th>
            <th class="border-bottom-1"><h6 class="fw-semibold mb-0">Status</h6></th>
            <th class="border-bottom-1 text-center"><h6 class="fw-semibold mb-0">Aksi</h6></th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($aduan_list)): ?>
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">
                <i class="ti ti-mood-empty fs-8 d-block mb-2"></i>
                Tidak ada laporan aduan yang ditemukan.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($aduan_list as $aduan): ?>
              <tr>
                <td class="border-bottom-0">
                  <span class="fw-semibold text-dark fs-3"><?= $aduan['no_tiket'] ?></span>
                </td>
                <td class="border-bottom-0">
                  <span class="text-dark fs-3"><?= esc($aduan['fullname'] ?? $aduan['nama_pelapor'] . ' (Publik)') ?></span>
                </td>

                <td class="border-bottom-0" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                  <p class="mb-1 fw-semibold text-dark fs-3"><?= esc($aduan['judul']) ?></p>
                  <span class="fs-2 text-muted"><?= esc($aduan['nama_pelatihan']) ?></span>
                </td>
                <td class="border-bottom-0">
                  <span class="badge bg-light-primary text-primary fw-medium fs-2 text-capitalize"><?= esc($aduan['kategori']) ?></span>
                </td>
                <td class="border-bottom-0">
                  <span class="fs-3"><?= date('d M Y H:i', strtotime($aduan['created_at'])) ?></span>
                </td>
                <td class="border-bottom-0">
                  <?php if ($aduan['status'] === 'Pending'): ?>
                    <span class="badge bg-warning rounded-3 fw-semibold fs-2">Pending</span>
                  <?php elseif ($aduan['status'] === 'Proses'): ?>
                    <span class="badge bg-info rounded-3 fw-semibold fs-2">Diproses</span>
                  <?php else: ?>
                    <span class="badge bg-success rounded-3 fw-semibold fs-2">Selesai</span>
                  <?php endif; ?>
                </td>
                <td class="border-bottom-0 text-center">
                  <a href="<?= base_url('aduan/detail/' . $aduan['id']) ?>" class="btn btn-sm btn-primary">
                    <i class="ti ti-eye me-1"></i> Detail
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
