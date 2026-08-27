<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
  <!-- Complaint Detail Card -->
  <div class="col-lg-8">
    <div class="card shadow-sm mb-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
          <div>
            <span class="badge bg-light-primary text-primary fw-medium fs-2 text-capitalize mb-1"><?= esc($aduan['kategori']) ?></span>
            <h4 class="fw-bold mb-1 text-dark"><?= esc($aduan['judul']) ?></h4>
            <p class="text-muted mb-0 fs-2">Nomor Tiket: <span class="fw-semibold text-dark"><?= $aduan['no_tiket'] ?></span> | Diajukan pada <?= date('d M Y H:i', strtotime($aduan['created_at'])) ?></p>
          </div>
          <div>
            <?php if ($aduan['status'] === 'Pending'): ?>
              <span class="badge bg-warning rounded-3 fw-semibold px-3 py-2 fs-3">Pending</span>
            <?php elseif ($aduan['status'] === 'Proses'): ?>
              <span class="badge bg-info rounded-3 fw-semibold px-3 py-2 fs-3">Diproses</span>
            <?php else: ?>
              <span class="badge bg-success rounded-3 fw-semibold px-3 py-2 fs-3">Selesai</span>
            <?php endif; ?>
          </div>
        </div>

        <div class="mb-4">
          <h6 class="fw-semibold text-dark mb-2">Nama Pelatihan:</h6>
          <p class="text-dark bg-light p-3 rounded border"><?= esc($aduan['nama_pelatihan']) ?></p>
        </div>

        <div class="mb-4">
          <h6 class="fw-semibold text-dark mb-2">Pelapor:</h6>
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-light p-3 rounded border gap-2">
            <div>
              <p class="mb-0 fw-semibold text-dark fs-3"><?= esc($aduan['fullname'] ?? $aduan['nama_pelapor'] . ' (Publik)') ?></p>
              <?php if (!empty($aduan['no_telepon'])): ?>
                <span class="fs-2 text-muted"><i class="ti ti-brand-whatsapp text-success me-1"></i>No. WA/Telp: <strong><?= esc($aduan['no_telepon']) ?></strong></span>
              <?php endif; ?>
            </div>
            <?php if (!empty($aduan['no_telepon'])): ?>
              <?php 
                $cleanPhone = preg_replace('/[^0-9]/', '', $aduan['no_telepon']);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '62' . substr($cleanPhone, 1);
                }
                $waText = urlencode("Halo " . ($aduan['nama_pelapor'] ?? 'Bpk/Ibu') . ", kami dari Admin SIMA BAPELKES Jawa Tengah ingin menindaklanjuti aduan Anda (No. Tiket: " . $aduan['no_tiket'] . ") terkait: \"" . $aduan['judul'] . "\".");
              ?>
              <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= $waText ?>" target="_blank" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1">
                <i class="ti ti-brand-whatsapp fs-4"></i> Hubungi via WhatsApp
              </a>
            <?php endif; ?>
          </div>
        </div>


        <div class="mb-4">
          <h6 class="fw-semibold text-dark mb-2">Detail Pengaduan:</h6>
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
                  Unduh / Lihat Lampiran
                </a>
                <p class="mb-0 text-muted fs-2">Klik link diatas untuk membuka lampiran.</p>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <div class="d-flex justify-content-start border-top pt-3">
          <a href="<?= base_url('aduan') ?>" class="btn btn-outline-dark">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar
          </a>
        </div>
      </div>
    </div>

    <!-- Timeline/Tanggapan Section -->
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h5 class="card-title fw-semibold mb-4 text-dark"><i class="ti ti-messages me-2"></i>Tindak Lanjut & Diskusi</h5>
        
        <!-- Responses List -->
        <div class="mb-4">
          <?php if (empty($tanggapans)): ?>
            <div class="text-center py-4 bg-light rounded text-muted">
              <i class="ti ti-message-2 fs-6 d-block mb-1"></i>
              Belum ada tanggapan untuk aduan ini.
            </div>
          <?php else: ?>
            <div class="timeline-container ps-2" style="border-left: 2px solid #e9ecef;">
              <?php foreach ($tanggapans as $tanggapan): ?>
                <?php 
                  $isSystem = strpos($tanggapan['tanggapan'], '[SISTEM]') === 0;
                  $text = $isSystem ? str_replace('[SISTEM]', '', $tanggapan['tanggapan']) : $tanggapan['tanggapan'];
                ?>
                <?php if ($isSystem): ?>
                  <!-- System Notification -->
                  <div class="mb-3 ms-3 py-2 px-3 bg-light-warning rounded-2 border border-warning border-opacity-25" style="border-left: 4px solid #ffae1f !important;">
                    <small class="text-muted d-block fs-1"><?= date('d M Y H:i', strtotime($tanggapan['created_at'])) ?></small>
                    <span class="fs-2 text-dark"><i class="ti ti-info-circle me-1 text-warning"></i><?= esc($text) ?></span>
                  </div>
                <?php else: ?>
                  <!-- Regular Response -->
                  <div class="mb-4 ms-3 position-relative">
                    <!-- Dot icon indicator -->
                    <div class="position-absolute bg-white text-primary rounded-circle border border-primary border-2 d-flex align-items-center justify-content-center" 
                         style="left: -27px; top: 0; width: 14px; height: 14px; z-index: 1;">
                    </div>
                    
                    <div class="card shadow-none border bg-light mb-0">
                      <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <div class="d-flex align-items-center gap-2">
                            <span class="fw-semibold text-dark fs-3"><?= esc($tanggapan['fullname']) ?></span>
                            <?php if ($tanggapan['role'] === 'admin'): ?>
                              <span class="badge bg-danger rounded-3 fs-1 py-1">Admin</span>
                            <?php else: ?>
                              <span class="badge bg-primary rounded-3 fs-1 py-1">Pelapor</span>
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

        <!-- Add Response Form -->
        <form action="<?= base_url('aduan/tanggapi/' . $aduan['id']) ?>" method="POST" class="border-top pt-4">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label for="tanggapan" class="form-label fw-semibold text-dark">Kirim Tanggapan / Komentar</label>
            <textarea name="tanggapan" id="tanggapan" rows="3" class="form-control" placeholder="Tuliskan tanggapan Anda di sini..." required></textarea>
          </div>
          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
              <i class="ti ti-message-share fs-4"></i> Kirim Tanggapan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Admin Action Sidebar -->
  <div class="col-lg-4">
    <?php if (session()->get('role') === 'admin'): ?>
      <div class="card shadow-sm mb-4 border border-primary border-opacity-50">
        <div class="card-header bg-primary text-white py-3">
          <h5 class="card-title fw-semibold text-white mb-0"><i class="ti ti-settings me-2"></i>Panel Kontrol Admin</h5>
        </div>
        <div class="card-body p-4">
          <form action="<?= base_url('aduan/update-status/' . $aduan['id']) ?>" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label for="update-status" class="form-label fw-semibold text-dark">Ubah Status Aduan</label>
              <select name="status" id="update-status" class="form-select">
                <option value="Pending" <?= $aduan['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Proses" <?= $aduan['status'] === 'Proses' ? 'selected' : '' ?>>Diproses</option>
                <option value="Selesai" <?= $aduan['status'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
              </select>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-danger d-flex align-items-center justify-content-center gap-2">
                <i class="ti ti-circle-check fs-5"></i> Simpan Perubahan Status
              </button>
            </div>
          </form>
          <div class="mt-4 pt-3 border-top text-muted fs-2">
            <p class="mb-1"><strong class="text-dark">Keterangan Status:</strong></p>
            <ul class="mb-0 ps-3">
              <li><strong>Pending:</strong> Aduan baru masuk dan belum diproses.</li>
              <li><strong>Diproses:</strong> Aduan sedang ditindaklanjuti oleh unit kerja terkait.</li>
              <li><strong>Selesai:</strong> Aduan sudah diberikan penyelesaian / ditutup.</li>
            </ul>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h5 class="card-title fw-semibold mb-3 text-dark">Informasi Pelatihan</h5>
        <div class="mb-3">
          <span class="fs-2 text-muted d-block">Nama Pelatihan</span>
          <span class="fw-semibold text-dark fs-3"><?= esc($aduan['nama_pelatihan']) ?></span>
        </div>
        <div class="mb-3">
          <span class="fs-2 text-muted d-block">Unit Penyelenggara</span>
          <span class="fw-semibold text-dark fs-3">BAPELKES Provinsi Jawa Tengah</span>
        </div>
        <div class="mb-0">
          <span class="fs-2 text-muted d-block">Kontak Bapelkes</span>
          <span class="fw-semibold text-dark fs-3"><i class="ti ti-phone me-1"></i>(0293) 364743</span>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
