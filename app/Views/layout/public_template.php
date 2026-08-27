<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $title ?? 'SIMA BAPELKES Jawa Tengah' ?></title>
  <link rel="shortcut icon" type="image/png" href="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/images/logos/favicon.png') ?>" />
  <link rel="stylesheet" href="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/css/styles.min.css') ?>" />
  <!-- Tabler Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div class="position-relative overflow-hidden text-bg-light min-vh-100 d-flex flex-column align-items-center justify-content-center">
      
      <!-- Top header for public -->
      <div class="w-100 py-3 bg-white shadow-sm mb-4">
        <div class="container d-flex align-items-center justify-content-between">
          <a href="<?= base_url('login') ?>" class="d-flex align-items-center gap-2 text-decoration-none">
            <i class="ti ti-heart-rate-monitor fs-8 text-primary"></i>
            <span class="fs-5 fw-bold text-dark" style="letter-spacing: 0.5px;">SIMA BAPELKES</span>
          </a>
          <div class="d-flex align-items-center gap-2">
            <a href="<?= base_url('aduan/buat') ?>" class="btn btn-sm <?= url_is('aduan/buat') ? 'btn-primary' : 'btn-outline-dark' ?>">
              <i class="ti ti-plus"></i> Buat Aduan
            </a>
            <a href="<?= base_url('aduan/lacak') ?>" class="btn btn-sm <?= url_is('aduan/lacak') ? 'btn-primary' : 'btn-outline-dark' ?>">
              <i class="ti ti-search"></i> Lacak Aduan
            </a>
            <a href="<?= base_url('login') ?>" class="btn btn-outline-primary btn-sm">
              <i class="ti ti-login"></i> Login
            </a>
          </div>
        </div>
      </div>

      <div class="container mb-5">
        <?= $this->renderSection('content') ?>
      </div>
      
      <div class="text-center mt-auto mb-4 fs-2 text-muted">
        BAPELKES Jawa Tengah &copy; <?= date('Y') ?>
      </div>
    </div>
  </div>
  <script src="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/libs/jquery/dist/jquery.min.js') ?>"></script>
  <script src="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <?= $this->renderSection('scripts') ?>
</body>

</html>
