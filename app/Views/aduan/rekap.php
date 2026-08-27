<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="no-print d-flex justify-content-between align-items-center mb-4">
  <div>
    <h5 class="card-title fw-semibold mb-1 text-dark">Rekapitulasi Aduan & Laporan</h5>
    <p class="text-muted mb-0 fs-2">Halaman rekapitulasi data aduan untuk bahan evaluasi manajemen BAPELKES Jawa Tengah.</p>
  </div>
  <div>
    <button onclick="window.print()" class="btn btn-success d-flex align-items-center gap-2">
      <i class="ti ti-printer fs-5"></i> Cetak Laporan
    </button>
  </div>
</div>

<!-- Print-Only Kop Surat Header -->
<div id="print-area">
  <!-- Kop Surat BAPELKES (Hidden in web, visible in print) -->
  <div class="d-none d-print-block text-center mb-4 pb-3 border-bottom border-dark border-3">
    <h4 class="fw-bold mb-1 text-uppercase text-dark" style="font-size: 18px;">Pemerintah Provinsi Jawa Tengah</h4>
    <h3 class="fw-bold mb-1 text-uppercase text-dark" style="font-size: 20px;">Dinas Kesehatan</h3>
    <h2 class="fw-bold mb-2 text-uppercase text-primary" style="font-size: 22px; letter-spacing: 0.5px;">Balai Pelatihan Kesehatan (BAPELKES)</h2>
    <p class="mb-0 text-muted fs-2" style="font-style: italic;">Jl. Tembalang Raya No.12, Tembalang, Kec. Tembalang, Kota Semarang, Jawa Tengah 50275</p>
    <p class="mb-0 text-muted fs-2" style="font-style: italic;">Telp: (024) 7478816 | Email: bapelkes.jateng@gmail.com</p>
  </div>

  <div class="d-none d-print-block text-center mb-4">
    <h4 class="fw-bold text-dark text-uppercase">Laporan Rekapitulasi Pengaduan Peserta Pelatihan</h4>
    <p class="mb-0 text-muted">Dicetak pada: <?= date('d F Y H:i') ?></p>
  </div>

  <!-- Summary Widgets (Category & Status) -->
  <div class="row">
    <!-- Status Stats Card -->
    <div class="col-md-6 mb-4">
      <div class="card shadow-sm h-100 border">
        <div class="card-body p-4">
          <h5 class="card-title fw-semibold mb-3 text-dark border-bottom pb-2">Berdasarkan Status</h5>
          <div class="table-responsive">
            <table class="table table-bordered mb-0 align-middle">
              <thead class="table-light">
                <tr>
                  <th class="fw-semibold text-dark">Status</th>
                  <th class="fw-semibold text-dark text-center" style="width: 100px;">Jumlah</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $statusCounts = ['Pending' => 0, 'Proses' => 0, 'Selesai' => 0];
                  foreach ($stats_status as $s) {
                      $statusCounts[$s['status']] = $s['total'];
                  }
                ?>
                <tr>
                  <td><span class="badge bg-warning rounded-3 fw-semibold fs-2">Pending</span></td>
                  <td class="text-center fw-bold text-dark"><?= $statusCounts['Pending'] ?></td>
                </tr>
                <tr>
                  <td><span class="badge bg-info rounded-3 fw-semibold fs-2">Diproses</span></td>
                  <td class="text-center fw-bold text-dark"><?= $statusCounts['Proses'] ?></td>
                </tr>
                <tr>
                  <td><span class="badge bg-success rounded-3 fw-semibold fs-2">Selesai</span></td>
                  <td class="text-center fw-bold text-dark"><?= $statusCounts['Selesai'] ?></td>
                </tr>
                <tr class="table-light fw-bold text-dark">
                  <td>TOTAL</td>
                  <td class="text-center"><?= array_sum($statusCounts) ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Category Stats Card -->
    <div class="col-md-6 mb-4">
      <div class="card shadow-sm h-100 border">
        <div class="card-body p-4">
          <h5 class="card-title fw-semibold mb-3 text-dark border-bottom pb-2">Berdasarkan Kategori</h5>
          <div class="table-responsive">
            <table class="table table-bordered mb-0 align-middle">
              <thead class="table-light">
                <tr>
                  <th class="fw-semibold text-dark">Kategori</th>
                  <th class="fw-semibold text-dark text-center" style="width: 100px;">Jumlah</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $categories = ['Fasilitas' => 0, 'Pelayanan' => 0, 'Konsumsi' => 0, 'Lainnya' => 0];
                  foreach ($stats_kategori as $k) {
                      $categories[$k['kategori']] = $k['total'];
                  }
                ?>
                <?php foreach ($categories as $catName => $catCount): ?>
                  <tr>
                    <td class="text-capitalize"><?= $catName ?></td>
                    <td class="text-center fw-bold text-dark"><?= $catCount ?></td>
                  </tr>
                <?php endforeach; ?>
                <tr class="table-light fw-bold text-dark">
                  <td>TOTAL</td>
                  <td class="text-center"><?= array_sum($categories) ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Detailed Table of Complaints -->
  <div class="card shadow-sm mt-2 border">
    <div class="card-body p-4">
      <h5 class="card-title fw-semibold mb-3 text-dark border-bottom pb-2">Daftar Detail Laporan Aduan</h5>
      <div class="table-responsive">
        <table class="table table-striped table-bordered text-nowrap mb-0 align-middle">
          <thead class="table-dark fs-2">
            <tr>
              <th>No. Tiket</th>
              <th>Tanggal</th>
              <th>Nama Pelapor</th>
              <th>Nama Pelatihan</th>
              <th>Judul Aduan</th>
              <th>Kategori</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody class="fs-2">
            <?php if (empty($all_aduan)): ?>
              <tr>
                <td colspan="7" class="text-center py-3 text-muted">Belum ada aduan.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($all_aduan as $aduan): ?>
                <tr>
                  <td class="fw-bold text-dark"><?= $aduan['no_tiket'] ?></td>
                  <td><?= date('d/m/Y H:i', strtotime($aduan['created_at'])) ?></td>
                  <td><?= esc($aduan['fullname'] ?? $aduan['nama_pelapor'] . ' (Publik)') ?></td>

                  <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= esc($aduan['nama_pelatihan']) ?></td>
                  <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= esc($aduan['judul']) ?></td>
                  <td class="text-capitalize"><?= esc($aduan['kategori']) ?></td>
                  <td>
                    <?php if ($aduan['status'] === 'Pending'): ?>
                      <span class="badge bg-warning text-dark rounded-3 fw-semibold">Pending</span>
                    <?php elseif ($aduan['status'] === 'Proses'): ?>
                      <span class="badge bg-info text-white rounded-3 fw-semibold">Diproses</span>
                    <?php else: ?>
                      <span class="badge bg-success text-white rounded-3 fw-semibold">Selesai</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Signatures (Visible only in print) -->
      <div class="d-none d-print-block mt-5 pt-4">
        <div class="row">
          <div class="col-8"></div>
          <div class="col-4 text-center">
            <p class="mb-5">Semarang, <?= date('d F Y') ?><br>Mengetahui,<br><strong>Kepala BAPELKES Provinsi Jawa Tengah</strong></p>
            <p class="mt-5 mb-0"><u>______________________________</u><br>NIP. </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
