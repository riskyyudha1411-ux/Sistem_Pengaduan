<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<!-- Card Stats Row -->
<div class="row">
  <!-- Total Card -->
  <div class="col-sm-6 col-xl-3">
    <div class="card overflow-hidden rounded-2 shadow-sm border-start border-primary border-4">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h6 class="card-title mb-1 text-muted fs-3">Total Aduan</h6>
            <h4 class="fw-bold mb-0 text-dark"><?= $total ?></h4>
          </div>
          <div class="text-bg-primary p-3 rounded-circle d-flex align-items-center justify-content-center">
            <i class="ti ti-list-details fs-6"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Pending Card -->
  <div class="col-sm-6 col-xl-3">
    <div class="card overflow-hidden rounded-2 shadow-sm border-start border-warning border-4">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h6 class="card-title mb-1 text-muted fs-3">Aduan Pending</h6>
            <h4 class="fw-bold mb-0 text-dark"><?= $pending ?></h4>
          </div>
          <div class="text-bg-warning p-3 rounded-circle d-flex align-items-center justify-content-center">
            <i class="ti ti-hourglass-low fs-6 text-white"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Diproses Card -->
  <div class="col-sm-6 col-xl-3">
    <div class="card overflow-hidden rounded-2 shadow-sm border-start border-info border-4">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h6 class="card-title mb-1 text-muted fs-3">Dalam Proses</h6>
            <h4 class="fw-bold mb-0 text-dark"><?= $proses ?></h4>
          </div>
          <div class="text-bg-info p-3 rounded-circle d-flex align-items-center justify-content-center">
            <i class="ti ti-settings-automation fs-6"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Selesai Card -->
  <div class="col-sm-6 col-xl-3">
    <div class="card overflow-hidden rounded-2 shadow-sm border-start border-success border-4">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h6 class="card-title mb-1 text-muted fs-3">Aduan Selesai</h6>
            <h4 class="fw-bold mb-0 text-dark"><?= $selesai ?></h4>
          </div>
          <div class="text-bg-success p-3 rounded-circle d-flex align-items-center justify-content-center">
            <i class="ti ti-checkbox fs-6"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if ($chartData): ?>
<!-- Charts Row -->
<div class="row mt-4">
  <!-- Trend Chart -->
  <div class="col-lg-8 d-flex align-items-stretch">
    <div class="card w-100 shadow-sm">
      <div class="card-body p-4">
        <h5 class="card-title fw-semibold mb-1 text-dark">Tren Aduan Masuk</h5>
        <p class="text-muted mb-4 fs-2">Grafik jumlah aduan peserta dalam 7 hari terakhir.</p>
        <div id="chart-trend"></div>
      </div>
    </div>
  </div>
  
  <!-- Category Chart -->
  <div class="col-lg-4 d-flex align-items-stretch">
    <div class="card w-100 shadow-sm">
      <div class="card-body p-4">
        <h5 class="card-title fw-semibold mb-1 text-dark">Kategori Aduan</h5>
        <p class="text-muted mb-4 fs-2">Persentase aduan berdasarkan topik.</p>
        <div id="chart-kategori" class="d-flex align-items-center justify-content-center" style="min-height: 250px;"></div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="row mt-4">
  <!-- Recent Complaints Table -->
  <div class="col-lg-8 d-flex align-items-stretch">
    <div class="card w-100 shadow-sm">
      <div class="card-body p-4">
        <h5 class="card-title fw-semibold mb-4 text-dark">5 Aduan Terbaru</h5>
        <div class="table-responsive">
          <table class="table text-nowrap mb-0 align-middle">
            <thead class="text-dark fs-3">
              <tr>
                <th class="border-bottom-0"><h6 class="fw-semibold mb-0">Tiket & Tanggal</h6></th>
                <th class="border-bottom-0"><h6 class="fw-semibold mb-0">Pelatihan & Aduan</h6></th>
                <th class="border-bottom-0"><h6 class="fw-semibold mb-0">Kategori</h6></th>
                <th class="border-bottom-0"><h6 class="fw-semibold mb-0">Status</h6></th>
                <th class="border-bottom-0"><h6 class="fw-semibold mb-0">Aksi</h6></th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($recent_aduan)): ?>
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted">
                    <i class="ti ti-mood-empty fs-7 d-block mb-2"></i>
                    Belum ada aduan yang masuk.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($recent_aduan as $aduan): ?>
                  <tr>
                    <td class="border-bottom-0">
                      <span class="fw-semibold text-dark fs-3"><?= $aduan['no_tiket'] ?></span>
                      <p class="mb-0 fs-2 text-muted"><?= date('d M Y H:i', strtotime($aduan['created_at'])) ?></p>
                    </td>
                    <td class="border-bottom-0" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                      <p class="mb-1 fw-semibold text-dark"><?= esc($aduan['judul']) ?></p>
                      <span class="fs-2 text-muted"><?= esc($aduan['nama_pelatihan']) ?></span>
                    </td>
                    <td class="border-bottom-0">
                      <span class="badge bg-light-primary text-primary fw-medium fs-2 text-capitalize"><?= esc($aduan['kategori']) ?></span>
                    </td>
                    <td class="border-bottom-0">
                      <?php if ($aduan['status'] === 'Pending'): ?>
                        <span class="badge bg-warning rounded-3 fw-semibold">Pending</span>
                      <?php elseif ($aduan['status'] === 'Proses'): ?>
                        <span class="badge bg-info rounded-3 fw-semibold">Diproses</span>
                      <?php else: ?>
                        <span class="badge bg-success rounded-3 fw-semibold">Selesai</span>
                      <?php endif; ?>
                    </td>
                    <td class="border-bottom-0">
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
  </div>
  
  <!-- Quick Actions Card -->
  <div class="col-lg-4 d-flex align-items-stretch">
    <div class="card w-100 shadow-sm">
      <div class="card-body p-4">
        <h5 class="card-title fw-semibold mb-3 text-dark">Aksi Cepat</h5>
        <div class="d-grid gap-2">
          <?php if (session()->get('role') === 'user'): ?>
            <a href="<?= base_url('aduan/buat') ?>" class="btn btn-primary p-3 d-flex align-items-center justify-content-center gap-2">
              <i class="ti ti-circle-plus fs-5"></i> Buat Aduan Baru
            </a>
          <?php endif; ?>
          <a href="<?= base_url('aduan') ?>" class="btn btn-outline-dark p-3 d-flex align-items-center justify-content-center gap-2">
            <i class="ti ti-list fs-5"></i> Lihat Semua Aduan
          </a>
          <?php if (session()->get('role') === 'admin'): ?>
            <a href="<?= base_url('aduan/rekap') ?>" class="btn btn-outline-success p-3 d-flex align-items-center justify-content-center gap-2">
              <i class="ti ti-file-analytics fs-5"></i> Rekap & Cetak Laporan
            </a>
          <?php endif; ?>
        </div>
        
        <div class="mt-4 pt-3 border-top">
          <h6 class="fw-semibold text-dark mb-2">Petunjuk Penggunaan</h6>
          <p class="fs-2 text-muted mb-0">
            SIMA (Sistem Informasi Manajemen Aduan) digunakan untuk menyampaikan keluhan, saran, atau aduan seputar fasilitas, konsumsi, dan pelayanan selama mengikuti pelatihan di BAPELKES Jawa Tengah.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if ($chartData): ?>
  <!-- ApexCharts CDN -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // 1. Trend Chart (Area Chart)
      const trendOptions = {
        series: [{
          name: 'Jumlah Aduan',
          data: <?= json_encode($chartData['trend']['values']) ?>
        }],
        chart: {
          height: 300,
          type: 'area',
          toolbar: {
            show: false
          },
          fontFamily: 'inherit'
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'smooth',
          width: 3
        },
        colors: ['#5D87FF'],
        xaxis: {
          categories: <?= json_encode($chartData['trend']['labels']) ?>,
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false
          }
        },
        yaxis: {
          labels: {
            formatter: function(val) {
              return val.toFixed(0);
            }
          }
        },
        grid: {
          borderColor: '#f1f1f1',
          strokeDashArray: 4
        },
        tooltip: {
          theme: 'light'
        }
      };

      const trendChart = new ApexCharts(document.querySelector("#chart-trend"), trendOptions);
      trendChart.render();

      // 2. Category Chart (Donut Chart)
      const katOptions = {
        series: <?= json_encode($chartData['kategori']['values']) ?>,
        labels: <?= json_encode($chartData['kategori']['labels']) ?>,
        chart: {
          type: 'donut',
          height: 280,
          fontFamily: 'inherit'
        },
        colors: ['#5D87FF', '#13DEB9', '#FFAE1F', '#FA896B'],
        plotOptions: {
          pie: {
            donut: {
              size: '70%',
              labels: {
                show: true,
                total: {
                  show: true,
                  label: 'Total',
                  color: '#95a5a6',
                  formatter: function (w) {
                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                  }
                }
              }
            }
          }
        },
        dataLabels: {
          enabled: false
        },
        legend: {
          position: 'bottom',
          fontFamily: 'inherit'
        },
        tooltip: {
          theme: 'light'
        }
      };

      const katChart = new ApexCharts(document.querySelector("#chart-kategori"), katOptions);
      katChart.render();
    });
  </script>
<?php endif; ?>
<?= $this->endSection() ?>
