<?php if (session()->get('id')): ?>
<?= $this->extend('layout/template') ?>
<?php else: ?>
<?= $this->extend('layout/public_template') ?>
<?php endif; ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
  <div class="col-lg-10">
    
    <!-- Search Ticket Card -->
    <div class="card shadow-sm mb-4">
      <div class="card-body p-4 text-center">
        <div class="mb-3">
          <span class="badge bg-light-primary text-primary rounded-circle p-3 d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="ti ti-search fs-7"></i>
          </span>
        </div>
        <h4 class="fw-bold text-dark mb-1">Lacak Status Aduan</h4>
        <p class="text-muted fs-3 mb-4">Masukkan Nomor Tiket aduan Anda untuk melihat perkembangan proses tindak lanjut oleh tim BAPELKES Jawa Tengah.</p>
        
        <form action="<?= base_url('aduan/lacak') ?>" method="GET" class="row justify-content-center">
          <div class="col-md-7 col-lg-6">
            <div class="input-group input-group-lg shadow-sm">
              <input type="text" name="tiket" class="form-control text-uppercase fw-bold" placeholder="Contoh: ADU-6EDAD8" value="<?= esc($no_tiket ?? '') ?>" required autofocus>
              <button class="btn btn-primary px-4 fw-semibold" type="submit">
                <i class="ti ti-search me-1"></i> Cari Tiket
              </button>
            </div>
            <div class="form-text fs-2 text-muted mt-2">Nomor tiket didapatkan saat Anda berhasil mengirimkan aduan.</div>
          </div>
        </form>
      </div>
    </div>

    <!-- Alert Not Found -->
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center gap-2">
          <i class="ti ti-alert-circle fs-5"></i>
          <div><?= session()->getFlashdata('error') ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Result Card -->
    <?php if (!empty($aduan)): ?>
      <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-white border-bottom p-4">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
              <span class="badge bg-light-primary text-primary fw-medium fs-2 text-capitalize mb-1"><?= esc($aduan['kategori']) ?></span>
              <h4 class="fw-bold text-dark mb-1"><?= esc($aduan['judul']) ?></h4>
              <p class="text-muted mb-0 fs-2">
                Nomor Tiket: <span class="fw-bold text-primary"><?= $aduan['no_tiket'] ?></span> &bull; 
                Diajukan pada <?= date('d M Y H:i', strtotime($aduan['created_at'])) ?> WIB
              </p>
            </div>
            <div>
              <?php if ($aduan['status'] === 'Pending'): ?>
                <span class="badge bg-warning text-dark px-3 py-2 fs-3 rounded-pill fw-semibold"><i class="ti ti-clock me-1"></i> Pending (Menunggu)</span>
              <?php elseif ($aduan['status'] === 'Proses'): ?>
                <span class="badge bg-info text-white px-3 py-2 fs-3 rounded-pill fw-semibold"><i class="ti ti-loader me-1"></i> Sedang Diproses</span>
              <?php else: ?>
                <span class="badge bg-success text-white px-3 py-2 fs-3 rounded-pill fw-semibold"><i class="ti ti-check me-1"></i> Selesai Ditangani</span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="card-body p-4">
          
          <!-- Status Progress Steps -->
          <div class="row text-center mb-4 py-3 bg-light rounded-3">
            <div class="col-4 position-relative">
              <div class="d-inline-flex align-items-center justify-content-center rounded-circle <?= in_array($aduan['status'], ['Pending', 'Proses', 'Selesai']) ? 'bg-primary text-white' : 'bg-secondary text-white' ?> mb-2" style="width: 38px; height: 38px;">
                <i class="ti ti-file-text fs-5"></i>
              </div>
              <h6 class="fw-bold fs-2 mb-0">1. Diterima</h6>
              <span class="text-muted fs-1"><?= date('d/m/Y', strtotime($aduan['created_at'])) ?></span>
            </div>
            <div class="col-4 position-relative">
              <div class="d-inline-flex align-items-center justify-content-center rounded-circle <?= in_array($aduan['status'], ['Proses', 'Selesai']) ? 'bg-info text-white' : 'bg-muted bg-light text-muted border' ?> mb-2" style="width: 38px; height: 38px;">
                <i class="ti ti-settings fs-5"></i>
              </div>
              <h6 class="fw-bold fs-2 mb-0">2. Ditindaklanjuti</h6>
              <span class="text-muted fs-1"><?= in_array($aduan['status'], ['Proses', 'Selesai']) ? 'Sedang diproses' : 'Menunggu antrean' ?></span>
            </div>
            <div class="col-4 position-relative">
              <div class="d-inline-flex align-items-center justify-content-center rounded-circle <?= $aduan['status'] === 'Selesai' ? 'bg-success text-white' : 'bg-muted bg-light text-muted border' ?> mb-2" style="width: 38px; height: 38px;">
                <i class="ti ti-circle-check fs-5"></i>
              </div>
              <h6 class="fw-bold fs-2 mb-0">3. Selesai</h6>
              <span class="text-muted fs-1"><?= $aduan['status'] === 'Selesai' ? 'Selesai' : 'Belum selesai' ?></span>
            </div>
          </div>

          <!-- Detail info -->
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="p-3 bg-light rounded border">
                <span class="fs-2 text-muted d-block mb-1">Nama Pelapor:</span>
                <span class="fw-semibold text-dark fs-3"><?= esc($aduan['fullname'] ?? $aduan['nama_pelapor'] . ' (Publik)') ?></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 bg-light rounded border">
                <span class="fs-2 text-muted d-block mb-1">Nama Pelatihan:</span>
                <span class="fw-semibold text-dark fs-3"><?= esc($aduan['nama_pelatihan']) ?></span>
              </div>
            </div>
          </div>

          <div class="mb-4">
            <h6 class="fw-semibold text-dark mb-2">Detail Aduan:</h6>
            <div class="p-3 bg-light rounded border text-dark fs-3" style="white-space: pre-line; line-height: 1.6;">
              <?= esc($aduan['deskripsi']) ?>
            </div>
          </div>

          <?php if ($aduan['lampiran']): ?>
            <div class="mb-4">
              <h6 class="fw-semibold text-dark mb-2">Lampiran Bukti:</h6>
              <div class="d-flex align-items-center gap-2 p-3 bg-light rounded border">
                <i class="ti ti-file-text fs-6 text-primary"></i>
                <div>
                  <a href="<?= base_url('uploads/lampiran/' . $aduan['lampiran']) ?>" target="_blank" class="fw-semibold text-primary">
                    Lihat / Unduh Lampiran
                  </a>
                  <p class="mb-0 text-muted fs-2">Klik untuk membuka file lampiran.</p>
                </div>
              </div>
            </div>
          <?php endif; ?>

        </div>
      </div>

      <!-- Timeline & Responses from Admin -->
      <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
          <h5 class="card-title fw-semibold mb-4 text-dark d-flex align-items-center gap-2">
            <i class="ti ti-messages text-primary fs-5"></i> Tanggapan Resmi Tim BAPELKES
          </h5>

          <?php if (empty($tanggapans)): ?>
            <div class="text-center py-4 bg-light rounded text-muted">
              <i class="ti ti-message-dots fs-7 d-block mb-2 text-muted"></i>
              Belum ada respon atau tanggapan untuk aduan ini.<br>
              <small class="fs-2">Tim admin BAPELKES akan segera meninjau dan menindaklanjuti laporan Anda.</small>
            </div>
          <?php else: ?>
            <div class="timeline-container ps-2" style="border-left: 2px solid #e9ecef;">
              <?php foreach ($tanggapans as $tanggapan): ?>
                <?php 
                  $isSystem = strpos($tanggapan['tanggapan'], '[SISTEM]') === 0;
                  $text = $isSystem ? str_replace('[SISTEM]', '', $tanggapan['tanggapan']) : $tanggapan['tanggapan'];
                ?>
                <?php if ($isSystem): ?>
                  <div class="mb-3 ms-3 py-2 px-3 bg-light-warning rounded-2 border border-warning border-opacity-25" style="border-left: 4px solid #ffae1f !important;">
                    <small class="text-muted d-block fs-1"><?= date('d M Y H:i', strtotime($tanggapan['created_at'])) ?></small>
                    <span class="fs-2 text-dark"><i class="ti ti-info-circle me-1 text-warning"></i><?= esc($text) ?></span>
                  </div>
                <?php else: ?>
                  <div class="mb-4 ms-3 position-relative">
                    <div class="position-absolute bg-white text-primary rounded-circle border border-primary border-2 d-flex align-items-center justify-content-center" 
                         style="left: -27px; top: 0; width: 14px; height: 14px; z-index: 1;">
                    </div>
                    
                    <div class="card shadow-none border bg-light mb-0">
                      <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <div class="d-flex align-items-center gap-2">
                            <span class="fw-semibold text-dark fs-3"><?= esc($tanggapan['fullname'] ?? 'Admin BAPELKES') ?></span>
                            <?php if (($tanggapan['role'] ?? '') === 'admin'): ?>
                              <span class="badge bg-danger rounded-3 fs-1 py-1">Admin</span>
                            <?php else: ?>
                              <span class="badge bg-primary rounded-3 fs-1 py-1">Petugas</span>
                            <?php endif; ?>
                          </div>
                          <span class="text-muted fs-2"><?= date('d M Y H:i', strtotime($tanggapan['created_at'])) ?></span>
                        </div>
                        <div class="text-dark fs-3" style="white-space: pre-line;">
                          <?= esc($text) ?>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

        </div>
      </div>
    <?php endif; ?>

    <!-- Navigation links -->
    <div class="text-center mt-3">
      <a href="<?= base_url('aduan/buat') ?>" class="btn btn-outline-primary me-2">
        <i class="ti ti-speakerphone me-1"></i> Buat Aduan Baru
      </a>
      <a href="<?= base_url('login') ?>" class="btn btn-outline-dark">
        <i class="ti ti-login me-1"></i> Login Pengguna
      </a>
    </div>

  </div>
</div>
<?= $this->endSection() ?>
