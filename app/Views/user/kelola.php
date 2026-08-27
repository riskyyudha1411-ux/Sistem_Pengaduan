<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="card shadow-sm">
  <div class="card-body p-4">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 border-bottom pb-3">
      <div>
        <h5 class="card-title fw-semibold mb-1 text-dark">Kelola Pengguna & Sistem</h5>
        <p class="text-muted mb-0 fs-2">Manajemen data pengguna dan konfigurasi notifikasi otomatis WhatsApp.</p>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <a href="<?= base_url('user/download-template') ?>" class="btn btn-outline-success d-flex align-items-center gap-2">
          <i class="ti ti-file-spreadsheet fs-5"></i> Template Excel
        </a>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalImport">
          <i class="ti ti-upload fs-5"></i> Import Excel
        </button>
        <button type="button" class="btn btn-dark d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
          <i class="ti ti-circle-plus fs-5"></i> Tambah User
        </button>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4 gap-2 bg-light p-2 rounded" id="userSettingsTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active d-flex align-items-center gap-2" id="user-list-tab" data-bs-toggle="tab" data-bs-target="#user-list" type="button" role="tab" aria-controls="user-list" aria-selected="true">
          <i class="ti ti-users fs-4"></i> Daftar Pengguna
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link d-flex align-items-center gap-2" id="wa-config-tab" data-bs-toggle="tab" data-bs-target="#wa-config" type="button" role="tab" aria-controls="wa-config" aria-selected="false">
          <i class="ti ti-brand-whatsapp fs-4"></i> WhatsApp Notifikasi Admin
        </button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="userSettingsTabContent">
      
      <!-- TAB 1: Daftar Pengguna -->
      <div class="tab-pane fade show active" id="user-list" role="tabpanel" aria-labelledby="user-list-tab">
        
        <!-- Import Result -->
        <?php $importResult = session()->getFlashdata('import_result'); ?>
        <?php if ($importResult): ?>
          <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <h6 class="fw-semibold mb-2">
              <i class="ti ti-file-analytics fs-5 me-1"></i> Hasil Import Excel
            </h6>
            <div class="d-flex gap-4 mb-2">
              <span class="badge bg-success rounded-3 fs-2 fw-semibold px-3 py-2">
                <i class="ti ti-check me-1"></i> Berhasil: <?= $importResult['import_success'] ?>
              </span>
              <span class="badge bg-danger rounded-3 fs-2 fw-semibold px-3 py-2">
                <i class="ti ti-x me-1"></i> Gagal: <?= count($importResult['import_failed']) ?>
              </span>
            </div>

            <?php if (!empty($importResult['import_failed'])): ?>
              <div class="table-responsive mt-3" style="max-height: 200px; overflow-y: auto;">
                <table class="table table-sm table-bordered mb-0 bg-white">
                  <thead class="table-light">
                    <tr>
                      <th class="fw-semibold fs-2" style="width: 80px;">Baris</th>
                      <th class="fw-semibold fs-2">Nama/Username</th>
                      <th class="fw-semibold fs-2">Keterangan Error</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($importResult['import_failed'] as $fail): ?>
                      <tr>
                        <td class="text-center fw-semibold fs-2"><?= $fail['row'] ?></td>
                        <td class="fs-2"><?= esc($fail['name']) ?></td>
                        <td class="fs-2 text-danger">
                          <ul class="mb-0 ps-3">
                            <?php foreach ($fail['errors'] as $err): ?>
                              <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                          </ul>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <!-- Warning flash -->
        <?php if (session()->getFlashdata('warning')): ?>
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-triangle fs-5 me-2"></i>
            <?= session()->getFlashdata('warning') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <div class="card border bg-light-primary mb-0">
              <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                  <i class="ti ti-users fs-6 text-white"></i>
                </div>
                <div>
                  <h3 class="fw-bold mb-0 text-dark"><?= count($users) ?></h3>
                  <p class="mb-0 text-muted fs-2">Total User</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card border bg-light-success mb-0">
              <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                  <i class="ti ti-user fs-6 text-white"></i>
                </div>
                <div>
                  <h3 class="fw-bold mb-0 text-dark"><?= count(array_filter($users, fn($u) => $u['role'] === 'user')) ?></h3>
                  <p class="mb-0 text-muted fs-2">User Biasa</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card border bg-light-warning mb-0">
              <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                  <i class="ti ti-shield-check fs-6 text-white"></i>
                </div>
                <div>
                  <h3 class="fw-bold mb-0 text-dark"><?= count(array_filter($users, fn($u) => $u['role'] === 'admin')) ?></h3>
                  <p class="mb-0 text-muted fs-2">Admin</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Table of Users -->
        <div class="table-responsive">
          <table class="table text-nowrap mb-0 align-middle">
            <thead class="text-dark fs-3">
              <tr>
                <th class="border-bottom-1" style="width: 50px;"><h6 class="fw-semibold mb-0">No</h6></th>
                <th class="border-bottom-1"><h6 class="fw-semibold mb-0">Username</h6></th>
                <th class="border-bottom-1"><h6 class="fw-semibold mb-0">Nama Lengkap</h6></th>
                <th class="border-bottom-1"><h6 class="fw-semibold mb-0">Role</h6></th>
                <th class="border-bottom-1"><h6 class="fw-semibold mb-0">Tanggal Dibuat</h6></th>
                <th class="border-bottom-1 text-center"><h6 class="fw-semibold mb-0">Aksi</h6></th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($users)): ?>
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="ti ti-users-minus fs-8 d-block mb-2"></i>
                    Belum ada data user.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($users as $i => $user): ?>
                  <tr>
                    <td class="border-bottom-0">
                      <span class="fw-semibold text-muted fs-3"><?= $i + 1 ?></span>
                    </td>
                    <td class="border-bottom-0">
                      <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-light-primary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                          <i class="ti ti-user fs-5 text-primary"></i>
                        </div>
                        <span class="fw-semibold text-dark fs-3"><?= esc($user['username']) ?></span>
                      </div>
                    </td>
                    <td class="border-bottom-0">
                      <span class="text-dark fs-3"><?= esc($user['fullname']) ?></span>
                    </td>
                    <td class="border-bottom-0">
                      <?php if ($user['role'] === 'admin'): ?>
                        <span class="badge bg-warning rounded-3 fw-semibold fs-2 text-dark">
                          <i class="ti ti-shield-check me-1"></i>Admin
                        </span>
                      <?php else: ?>
                        <span class="badge bg-light-primary text-primary fw-semibold fs-2">
                          <i class="ti ti-user me-1"></i>User
                        </span>
                      <?php endif; ?>
                    </td>
                    <td class="border-bottom-0">
                      <span class="fs-3"><?= $user['created_at'] ? date('d M Y H:i', strtotime($user['created_at'])) : '-' ?></span>
                    </td>
                    <td class="border-bottom-0 text-center">
                      <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-dark d-flex align-items-center gap-1 btn-edit-user"
                                data-id="<?= $user['id'] ?>"
                                data-username="<?= esc($user['username']) ?>"
                                data-fullname="<?= esc($user['fullname']) ?>"
                                data-role="<?= $user['role'] ?>">
                          <i class="ti ti-edit fs-4"></i> Edit
                        </button>
                        
                        <?php if ((int)$user['id'] !== (int)session()->get('id')): ?>
                          <form action="<?= base_url('user/delete/' . $user['id']) ?>" method="POST" class="d-inline mb-0" onsubmit="return confirm('Yakin ingin menghapus user \'<?= esc($user['fullname']) ?>\'?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
                              <i class="ti ti-trash fs-4"></i> Hapus
                            </button>
                          </form>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- TAB 2: Konfigurasi WhatsApp -->
      <div class="tab-pane fade" id="wa-config" role="tabpanel" aria-labelledby="wa-config-tab">
        <div class="row">
          <div class="col-lg-7">
            <div class="card border shadow-none mb-0">
              <div class="card-body p-4">
                <h5 class="fw-semibold text-dark mb-3">Pengaturan API WhatsApp</h5>
                
                <form action="<?= base_url('user/wa-settings') ?>" method="POST">
                  <?= csrf_field() ?>
                  
                  <!-- Enable / Disable Toggle -->
                  <div class="mb-4 bg-light p-3 rounded d-flex align-items-center justify-content-between">
                    <div>
                      <h6 class="fw-semibold mb-0 text-dark">Aktifkan Notifikasi WhatsApp</h6>
                      <small class="text-muted">Kirim notifikasi otomatis ke nomor WA Admin setiap ada aduan masuk.</small>
                    </div>
                    <div class="form-check form-switch fs-5">
                      <input class="form-check-input" type="checkbox" name="wa_enabled" id="waEnabled" value="1" <?= ($waSettings['enabled'] ?? false) ? 'checked' : '' ?>>
                    </div>
                  </div>

                  <!-- Admin WhatsApp Number -->
                  <div class="mb-3">
                    <label for="waAdminNumber" class="form-label fw-semibold text-dark fs-3">Nomor WhatsApp Penerima (Admin)</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light text-muted fw-bold">+62</span>
                      <input type="text" class="form-control" name="wa_admin_number" id="waAdminNumber" placeholder="Contoh: 8123456789" value="<?= esc($waSettings['admin_number'] ?? '') ?>">
                    </div>
                    <small class="text-muted">Gunakan nomor WA aktif Admin (masukkan tanpa angka 0 di depan, contoh: 8123456789).</small>
                  </div>

                  <!-- Fonnte Token -->
                  <div class="mb-4">
                    <label for="waToken" class="form-label fw-semibold text-dark fs-3">Fonnte API Token</label>
                    <input type="password" class="form-control" name="wa_token" id="waToken" placeholder="Masukkan token API Fonnte Anda" value="<?= esc($waSettings['token'] ?? '') ?>">
                    <small class="text-muted">Token autentikasi dari dashboard Fonnte Anda.</small>
                  </div>

                  <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="ti ti-device-floppy fs-5"></i> Simpan Konfigurasi
                  </button>
                </form>
              </div>
            </div>
          </div>

          <!-- Documentation / Guide -->
          <div class="col-lg-5">
            <div class="card border shadow-none bg-light-primary mb-0">
              <div class="card-body p-4">
                <h6 class="fw-bold text-primary mb-3">
                  <i class="ti ti-help-circle-filled fs-5 me-1"></i> Cara Mendapatkan API Token
                </h6>
                <ol class="ps-3 fs-3 text-dark mb-4">
                  <li class="mb-2">Daftar / Buat akun di situs resmi <a href="https://fonnte.com" target="_blank" class="fw-semibold text-decoration-underline">Fonnte.com</a>.</li>
                  <li class="mb-2">Login ke dashboard Fonnte dan sambungkan nomor WA pengirim (Device).</li>
                  <li class="mb-2">Salin <strong>API Token</strong> yang ada di dashboard utama Fonnte Anda.</li>
                  <li class="mb-2">Tempel token tersebut pada input di sebelah kiri, isi nomor WA penerima notifikasi, centang aktifkan, lalu klik simpan.</li>
                </ol>
                
                <div class="bg-white p-3 rounded border">
                  <h6 class="fw-semibold text-dark mb-2 fs-2"><i class="ti ti-message-code me-1 text-success"></i> Format Pesan Otomatis:</h6>
                  <p class="fs-2 text-muted mb-0 bg-light p-2 rounded font-monospace" style="font-size: 11px !important;">
                    📢 *ADUAN BARU MASUK - SIMA BAPELKES*<br><br>
                    🎫 *Nomor Tiket:* ADU-XXXXXX<br>
                    👤 *Pelapor:* [Nama Pelapor]<br>
                    📝 *Judul Aduan:* [Judul]<br>
                    🏷️ *Kategori:* [Kategori]<br>
                    🏫 *Nama Pelatihan:* [Pelatihan]<br>
                    ⏰ *Tanggal:* [Waktu Masuk]<br><br>
                    Silakan login ke SIMA BAPELKES untuk memproses...
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Modal 1: Import Excel -->
<div class="modal fade" id="modalImport" tabindex="-1" aria-labelledby="modalImportLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalImportLabel">
          <i class="ti ti-upload me-2"></i>Import User dari Excel
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('user/import') ?>" method="POST" enctype="multipart/form-data" id="formImport">
        <?= csrf_field() ?>
        <div class="modal-body p-4">
          <!-- Instructions -->
          <div class="alert alert-light border mb-4">
            <h6 class="fw-semibold mb-2"><i class="ti ti-info-circle me-1 text-primary"></i> Petunjuk:</h6>
            <ol class="mb-0 ps-3 fs-2">
              <li>Download template Excel terlebih dahulu.</li>
              <li>Isi data user mulai dari <strong>baris ke-3</strong>.</li>
              <li>Kolom <strong>Role</strong> hanya menerima: <code>user</code> atau <code>admin</code></li>
              <li>Password akan di-hash otomatis saat import.</li>
              <li>Upload file yang sudah diisi ke sini.</li>
            </ol>
          </div>

          <!-- File Upload Area -->
          <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center" id="uploadArea" style="border-color: #5D87FF; cursor: pointer; transition: all 0.3s ease;">
            <input type="file" name="file_excel" id="fileExcel" accept=".xlsx,.xls" class="d-none" required>
            <div id="uploadPlaceholder">
              <i class="ti ti-cloud-upload fs-7 text-primary d-block mb-2"></i>
              <p class="mb-1 fw-semibold text-dark">Klik atau drag file ke sini</p>
              <p class="mb-0 fs-2 text-muted">Format: .xlsx atau .xls (Maks. 2MB)</p>
            </div>
            <div id="uploadFileInfo" class="d-none">
              <i class="ti ti-file-check fs-7 text-success d-block mb-2"></i>
              <p class="mb-1 fw-semibold text-dark" id="uploadFileName">-</p>
              <p class="mb-0 fs-2 text-muted" id="uploadFileSize">-</p>
              <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeFile">
                <i class="ti ti-x me-1"></i>Hapus File
              </button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="btnImport" disabled>
            <i class="ti ti-upload me-1"></i> Import Sekarang
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal 2: Tambah User Manual (Satu Per Satu) -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="modalTambahUserLabel">
          <i class="ti ti-circle-plus me-2"></i>Tambah User Baru
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('user/store') ?>" method="POST" id="formTambahUser">
        <?= csrf_field() ?>
        <div class="modal-body p-4">
          
          <!-- Username -->
          <div class="mb-3">
            <label for="username" class="form-label fw-semibold text-dark fs-3">Username</label>
            <input type="text" class="form-control" name="username" id="username" placeholder="Masukkan username" required minlength="3" maxlength="50">
            <small class="text-muted">Hanya huruf kecil, angka, dan underscore. Tanpa spasi.</small>
          </div>

          <!-- Fullname -->
          <div class="mb-3">
            <label for="fullname" class="form-label fw-semibold text-dark fs-3">Nama Lengkap</label>
            <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Masukkan nama lengkap" required minlength="3" maxlength="100">
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label for="password" class="form-label fw-semibold text-dark fs-3">Password</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan password (Min. 6 karakter)" required minlength="6">
          </div>

          <!-- Role selection -->
          <div class="mb-1">
            <label for="role" class="form-label fw-semibold text-dark fs-3">Role / Peran</label>
            <select name="role" id="role" class="form-select" required>
              <option value="user" selected>User (Pelapor)</option>
              <option value="admin">Administrator (Petugas)</option>
            </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-dark" id="btnSimpanUser">
            <i class="ti ti-device-floppy me-1"></i> Simpan User
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal 3: Edit User Modal -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-labelledby="modalEditUserLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="modalEditUserLabel">
          <i class="ti ti-edit me-2"></i>Edit Data User
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="" method="POST" id="formEditUser">
        <?= csrf_field() ?>
        <div class="modal-body p-4">
          
          <!-- Username -->
          <div class="mb-3">
            <label for="edit_username" class="form-label fw-semibold text-dark fs-3">Username</label>
            <input type="text" class="form-control" name="username" id="edit_username" placeholder="Masukkan username" required minlength="3" maxlength="50">
            <small class="text-muted">Hanya huruf kecil, angka, dan underscore. Tanpa spasi.</small>
          </div>

          <!-- Fullname -->
          <div class="mb-3">
            <label for="edit_fullname" class="form-label fw-semibold text-dark fs-3">Nama Lengkap</label>
            <input type="text" class="form-control" name="fullname" id="edit_fullname" placeholder="Masukkan nama lengkap" required minlength="3" maxlength="100">
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label for="edit_password" class="form-label fw-semibold text-dark fs-3">Password</label>
            <input type="password" class="form-control" name="password" id="edit_password" placeholder="Masukkan password baru (Min. 6 karakter)">
            <small class="text-warning"><i class="ti ti-alert-circle me-1"></i>Kosongkan password jika tidak ingin diubah.</small>
          </div>

          <!-- Role selection -->
          <div class="mb-1">
            <label for="edit_role" class="form-label fw-semibold text-dark fs-3">Role / Peran</label>
            <select name="role" id="edit_role" class="form-select" required>
              <option value="user">User (Pelapor)</option>
              <option value="admin">Administrator (Petugas)</option>
            </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-dark" id="btnUpdateUser">
            <i class="ti ti-device-floppy me-1"></i> Perbarui User
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // --- Excel Upload Drag & Drop Logics ---
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileExcel');
    const placeholder = document.getElementById('uploadPlaceholder');
    const fileInfo = document.getElementById('uploadFileInfo');
    const fileName = document.getElementById('uploadFileName');
    const fileSize = document.getElementById('uploadFileSize');
    const removeBtn = document.getElementById('removeFile');
    const btnImport = document.getElementById('btnImport');

    // Click to upload
    uploadArea.addEventListener('click', function (e) {
      if (e.target !== removeBtn && !removeBtn.contains(e.target)) {
        fileInput.click();
      }
    });

    // Drag & Drop event listeners
    uploadArea.addEventListener('dragover', function (e) {
      e.preventDefault();
      uploadArea.style.backgroundColor = '#EBF3FE';
      uploadArea.style.borderColor = '#3B6BF5';
    });

    uploadArea.addEventListener('dragleave', function (e) {
      e.preventDefault();
      uploadArea.style.backgroundColor = '';
      uploadArea.style.borderColor = '#5D87FF';
    });

    uploadArea.addEventListener('drop', function (e) {
      e.preventDefault();
      uploadArea.style.backgroundColor = '';
      uploadArea.style.borderColor = '#5D87FF';

      const files = e.dataTransfer.files;
      if (files.length > 0) {
        fileInput.files = files;
        handleFileSelect(files[0]);
      }
    });

    // File selected
    fileInput.addEventListener('change', function () {
      if (this.files.length > 0) {
        handleFileSelect(this.files[0]);
      }
    });

    function handleFileSelect(file) {
      const validExtensions = ['.xlsx', '.xls'];
      const ext = '.' + file.name.split('.').pop().toLowerCase();

      if (!validExtensions.includes(ext)) {
        alert('Format file tidak didukung. Gunakan file .xlsx atau .xls');
        resetUpload();
        return;
      }

      if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file terlalu besar. Maksimal 2MB.');
        resetUpload();
        return;
      }

      placeholder.classList.add('d-none');
      fileInfo.classList.remove('d-none');
      fileName.textContent = file.name;
      fileSize.textContent = formatFileSize(file.size);
      btnImport.disabled = false;
    }

    function resetUpload() {
      fileInput.value = '';
      placeholder.classList.remove('d-none');
      fileInfo.classList.add('d-none');
      btnImport.disabled = true;
    }

    removeBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      resetUpload();
    });

    function formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Submit animation for excel import
    document.getElementById('formImport').addEventListener('submit', function () {
      btnImport.disabled = true;
      btnImport.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengimport...';
    });

    // Submit animation for manual insert
    document.getElementById('formTambahUser').addEventListener('submit', function () {
      const btn = document.getElementById('btnSimpanUser');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    });

    // Clean manual form inputs on modal hide
    const modalTambahUser = document.getElementById('modalTambahUser');
    modalTambahUser.addEventListener('hidden.bs.modal', function () {
      document.getElementById('formTambahUser').reset();
    });

    // --- Edit User Logics ---
    const btnEditUsers = document.querySelectorAll('.btn-edit-user');
    const modalEditUserEl = document.getElementById('modalEditUser');
    const modalEditUser = new bootstrap.Modal(modalEditUserEl);
    const formEditUser = document.getElementById('formEditUser');

    btnEditUsers.forEach(btn => {
      btn.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const username = this.getAttribute('data-username');
        const fullname = this.getAttribute('data-fullname');
        const role = this.getAttribute('data-role');

        // Populate fields
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_fullname').value = fullname;
        document.getElementById('edit_role').value = role;
        document.getElementById('edit_password').value = ''; // clear password input

        // Update form action path
        formEditUser.setAttribute('action', '<?= base_url("user/update") ?>/' + id);

        // Show Modal
        modalEditUser.show();
      });
    });

    // Submit animation for edit user
    formEditUser.addEventListener('submit', function () {
      const btn = document.getElementById('btnUpdateUser');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memperbarui...';
    });

    // Reset edit form when hidden
    modalEditUserEl.addEventListener('hidden.bs.modal', function () {
      formEditUser.reset();
    });
  });
</script>
<?= $this->endSection() ?>
