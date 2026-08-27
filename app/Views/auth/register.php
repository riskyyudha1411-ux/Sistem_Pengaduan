<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrasi Akun - SIMA BAPELKES Jawa Tengah</title>
  <link rel="shortcut icon" type="image/png" href="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/images/logos/favicon.png') ?>" />
  <link rel="stylesheet" href="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/css/styles.min.css') ?>" />
  <!-- Tabler Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div class="position-relative overflow-hidden text-bg-light min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0 shadow">
              <div class="card-body">
                <div class="text-center mb-4">
                  <div class="d-flex align-items-center justify-content-center gap-2 mb-2 text-decoration-none">
                    <i class="ti ti-heart-rate-monitor fs-9 text-primary"></i>
                    <span class="fs-6 fw-bold text-dark" style="letter-spacing: 0.5px;">SIMA BAPELKES</span>
                  </div>
                  <h5 class="text-muted fs-3 mb-0">Pendaftaran Peserta Pelatihan</h5>
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

                <form action="<?= base_url('register') ?>" method="POST">
                  <?= csrf_field() ?>
                  <div class="mb-3">
                    <label for="fullname" class="form-label">Nama Lengkap</label>
                    <input type="text" name="fullname" class="form-control" id="fullname" value="<?= old('fullname') ?>" required autocomplete="off">
                  </div>
                  <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" id="username" value="<?= old('username') ?>" required autocomplete="off">
                  </div>
                  <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" required>
                  </div>
                  <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Daftar</button>
                  
                  <div class="d-flex align-items-center justify-content-center mb-3">
                    <p class="fs-3 mb-0 fw-bold">Sudah memiliki akun?</p>
                    <a class="text-primary fw-bold ms-2 fs-3" href="<?= base_url('login') ?>">Masuk</a>
                  </div>

                  <hr class="my-4">
                  <div class="text-center mb-3">
                    <span class="fs-3 text-muted">Atau lapor sebagai publik</span>
                  </div>
                  
                  <a href="<?= base_url('aduan/buat') ?>" class="btn btn-outline-dark w-100 py-8 fs-4 rounded-2 d-flex align-items-center justify-content-center gap-2">
                    <i class="ti ti-speakerphone fs-5"></i> Ajukan Aduan Tanpa Login
                  </a>

                </form>
              </div>
            </div>
            <div class="text-center mt-3 fs-2 text-muted">
              BAPELKES Jawa Tengah &copy; <?= date('Y') ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/libs/jquery/dist/jquery.min.js') ?>"></script>
  <script src="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>
