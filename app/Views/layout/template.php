<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $title ?? 'SIMA BAPELKES JAWA TENGAH' ?></title>
  <link rel="shortcut icon" type="image/png" href="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/images/logos/favicon.png') ?>" />
  <link rel="stylesheet" href="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/css/styles.min.css') ?>" />
  <!-- Tabler Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
  <style>
    .app-header, .left-sidebar {
      top: 0 !important;
    }
    .body-wrapper .container-fluid {
      padding-top: 100px !important;
    }
    @media (max-width: 991.98px) {
      .body-wrapper .container-fluid {
        padding-top: 110px !important;
      }
    }
    @media print {
      body * {
        visibility: hidden;
      }
      #print-area, #print-area * {
        visibility: visible;
      }
      #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
      }
      .no-print {
        display: none !important;
      }
    }
  </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between py-4">
          <a href="<?= base_url('dashboard') ?>" class="text-nowrap logo-img d-flex align-items-center gap-2 text-decoration-none">
            <i class="ti ti-heart-rate-monitor fs-8 text-primary"></i>
            <span class="fs-5 fw-bold text-dark" style="letter-spacing: 0.5px;">SIMA BAPELKES</span>
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-6"></i>
          </div>
        </div>
        
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">MENU UTAMA</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link <?= url_is('dashboard') ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>" aria-expanded="false">
                <i class="ti ti-layout-dashboard"></i>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            
            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">MANAJEMEN ADUAN</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link <?= (url_is('aduan') && !url_is('aduan/buat') && !url_is('aduan/rekap')) ? 'active' : '' ?>" href="<?= base_url('aduan') ?>" aria-expanded="false">
                <i class="ti ti-list-details"></i>
                <span class="hide-menu">Daftar Aduan</span>
              </a>
            </li>
            
            <?php if (session()->get('role') === 'user'): ?>
            <li class="sidebar-item">
              <a class="sidebar-link <?= url_is('aduan/buat') ? 'active' : '' ?>" href="<?= base_url('aduan/buat') ?>" aria-expanded="false">
                <i class="ti ti-message-report"></i>
                <span class="hide-menu">Buat Aduan</span>
              </a>
            </li>
            <?php endif; ?>

            <?php if (session()->get('role') === 'admin'): ?>
            <li class="sidebar-item">
              <a class="sidebar-link <?= url_is('aduan/rekap') ? 'active' : '' ?>" href="<?= base_url('aduan/rekap') ?>" aria-expanded="false">
                <i class="ti ti-file-analytics"></i>
                <span class="hide-menu">Rekapitulasi</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link <?= url_is('user*') ? 'active' : '' ?>" href="<?= base_url('user') ?>" aria-expanded="false">
                <i class="ti ti-users-group"></i>
                <span class="hide-menu">Kelola User</span>
              </a>
            </li>
            <?php endif; ?>

            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">AKUN</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link text-danger" href="<?= base_url('logout') ?>" aria-expanded="false">
                <i class="ti ti-logout text-danger"></i>
                <span class="hide-menu text-danger">Logout</span>
              </a>
            </li>
          </ul>
        </nav>
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->

    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
          </ul>
          
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              <div class="d-none d-md-flex flex-column align-items-end me-3">
                <span class="fw-semibold text-dark"><?= session()->get('fullname') ?></span>
                <span class="fs-2 text-muted text-capitalize"><?= session()->get('role') ?></span>
              </div>
              <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <img src="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/images/profile/user-1.jpg') ?>" alt="" width="35" height="35" class="rounded-circle">
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                  <div class="message-body">
                    <div class="p-3 text-center border-bottom d-block d-md-none">
                      <h6 class="mb-0 fw-semibold"><?= session()->get('fullname') ?></h6>
                      <span class="text-muted text-capitalize"><?= session()->get('role') ?></span>
                    </div>
                    <a href="<?= base_url('logout') ?>" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!--  Header End -->

      <div class="body-wrapper-inner">
        <div class="container-fluid">
          <!-- Flash messages -->
          <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="ti ti-circle-check fs-5 me-2"></i>
              <?= session()->getFlashdata('success') ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="ti ti-alert-triangle fs-5 me-2"></i>
              <?= session()->getFlashdata('error') ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <!-- Render Content -->
          <?= $this->renderSection('content') ?>

          <div class="py-6 px-6 text-center mt-5 border-top no-print">
            <p class="mb-0 fs-3">Sistem Informasi Manajemen Aduan (SIMA) &copy; <?= date('Y') ?> - BAPELKES Provinsi Jawa Tengah</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/libs/jquery/dist/jquery.min.js') ?>"></script>
  <script src="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/js/sidebarmenu.js') ?>"></script>
  <script src="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/js/app.min.js') ?>"></script>
  <script src="<?= base_url('flexy-bootstrap-lite-1.0.0/assets/libs/simplebar/dist/simplebar.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <?= $this->renderSection('scripts') ?>
</body>

</html>
