<?php

namespace App\Controllers;

use App\Models\AduanModel;
use App\Models\TanggapanModel;
use App\Models\UserModel;

class Aduan extends BaseController
{
    protected $aduanModel;
    protected $tanggapanModel;
    protected $userModel;

    public function __construct()
    {
        $this->aduanModel = new AduanModel();
        $this->tanggapanModel = new TanggapanModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $role = session()->get('role');
        $userId = session()->get('id');
        $statusFilter = $this->request->getGet('status');
        $searchFilter = $this->request->getGet('search');

        $query = $this->aduanModel->select('aduan.*, users.fullname')
            ->join('users', 'users.id = aduan.user_id', 'left');

        if ($role !== 'admin') {
            $query->where('aduan.user_id', $userId);
        }

        if ($statusFilter) {
            $query->where('aduan.status', $statusFilter);
        }

        if ($searchFilter) {
            $query->groupStart()
                  ->like('aduan.judul', $searchFilter)
                  ->orLike('aduan.no_tiket', $searchFilter)
                  ->orLike('aduan.nama_pelatihan', $searchFilter)
                  ->orLike('aduan.nama_pelapor', $searchFilter)
                  ->groupEnd();
        }

        $aduanList = $query->orderBy('aduan.created_at', 'DESC')->find();

        $data = [
            'title'        => 'Daftar Aduan - SIMA BAPELKES',
            'aduan_list'   => $aduanList,
            'status_filter'=> $statusFilter,
            'search_filter'=> $searchFilter,
        ];

        return view('aduan/index', $data);
    }

    public function buat()
    {
        $data = [
            'title'      => 'Buat Aduan Baru - SIMA BAPELKES',
            'isLoggedIn' => session()->get('id') ? true : false,
        ];
        return view('aduan/create', $data);
    }

    public function simpan()
    {
        $isLoggedIn = session()->get('id') ? true : false;

        $rules = [
            'nama_pelatihan' => 'required|min_length[5]|max_length[150]',
            'judul'          => 'required|min_length[5]|max_length[200]',
            'deskripsi'      => 'required|min_length[10]',
            'kategori'       => 'required',
            'lampiran'       => 'max_size[lampiran,2048]|ext_in[lampiran,png,jpg,jpeg,pdf,doc,docx]',
        ];

        if (!$isLoggedIn) {
            $rules['nama_pelapor'] = 'required|min_length[3]|max_length[100]';
            $rules['no_telepon']   = 'permit_empty|min_length[8]|max_length[20]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $lampiranFile = $this->request->getFile('lampiran');
        $lampiranName = null;

        if ($lampiranFile && $lampiranFile->isValid() && !$lampiranFile->hasMoved()) {
            $lampiranName = $lampiranFile->getRandomName();
            $lampiranFile->move(FCPATH . 'uploads/lampiran', $lampiranName);
        }

        $noTiket = 'ADU-' . strtoupper(bin2hex(random_bytes(3)));
        $namaPelapor = $isLoggedIn ? null : trim((string)$this->request->getPost('nama_pelapor'));
        $noTelepon   = $isLoggedIn ? null : trim((string)$this->request->getPost('no_telepon'));

        $this->aduanModel->save([
            'user_id'        => $isLoggedIn ? session()->get('id') : null,
            'nama_pelapor'   => $namaPelapor,
            'no_telepon'     => $noTelepon ?: null,
            'no_tiket'       => $noTiket,
            'nama_pelatihan' => $this->request->getPost('nama_pelatihan'),
            'judul'          => $this->request->getPost('judul'),
            'deskripsi'      => $this->request->getPost('deskripsi'),
            'kategori'       => $this->request->getPost('kategori'),
            'lampiran'       => $lampiranName,
            'status'         => 'Pending',
        ]);

        // WhatsApp Notification to Admin
        helper('whatsapp');
        $settingsPath = WRITEPATH . 'wa_settings.json';
        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true);
            $waAdmin = $settings['admin_number'] ?? '';
            $waEnabled = filter_var($settings['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);

            if ($waEnabled && !empty($waAdmin)) {
                $fullname = $isLoggedIn ? session()->get('fullname') : ($namaPelapor . ' (Publik)');
                $namaPelatihan = $this->request->getPost('nama_pelatihan');
                $judul = $this->request->getPost('judul');
                $kategori = $this->request->getPost('kategori');
                $tanggal = date('d M Y H:i');
                $kontakPelapor = !empty($noTelepon) ? $noTelepon : '-';

                $waMessage = "📢 *ADUAN BARU MASUK - SIMA BAPELKES*\n\n"
                           . "🎫 *Nomor Tiket:* {$noTiket}\n"
                           . "👤 *Pelapor:* {$fullname}\n"
                           . "📱 *No. WA/HP:* {$kontakPelapor}\n"
                           . "📝 *Judul Aduan:* {$judul}\n"
                           . "🏷️ *Kategori:* {$kategori}\n"
                           . "🏫 *Nama Pelatihan:* {$namaPelatihan}\n"
                           . "⏰ *Tanggal:* {$tanggal}\n\n"
                           . "Silakan login ke SIMA BAPELKES untuk memproses aduan ini.\n"
                           . "Link: " . base_url('dashboard');

                send_whatsapp_notification($waAdmin, $waMessage);
            }

            // WhatsApp confirmation to Pelapor if phone provided
            if ($waEnabled && !empty($noTelepon)) {
                $pelaporMsg = "Halo *{$namaPelapor}*,\n\n"
                            . "Aduan Anda di *SIMA BAPELKES Jawa Tengah* telah berhasil kami terima.\n\n"
                            . "🎫 *Nomor Tiket:* {$noTiket}\n"
                            . "📝 *Judul:* {$this->request->getPost('judul')}\n"
                            . "📌 *Status:* Pending (Menunggu Tindak Lanjut)\n\n"
                            . "Simpan Nomor Tiket Anda untuk melacak perkembangan aduan melalui tautan berikut:\n"
                            . base_url('aduan/lacak?tiket=' . $noTiket) . "\n\n"
                            . "Terima kasih telah membantu kami meningkatkan kualitas layanan BAPELKES Jawa Tengah.";
                send_whatsapp_notification($noTelepon, $pelaporMsg);
            }
        }

        if (!$isLoggedIn) {
            return redirect()->to('/aduan/buat')
                ->with('success', 'Aduan berhasil dikirim!')
                ->with('tiket_sukses', $noTiket)
                ->with('nama_sukses', $namaPelapor);
        }

        return redirect()->to('/aduan')->with('success', 'Aduan berhasil dikirim dengan Nomor Tiket: ' . $noTiket);
    }

    /**
     * Lacak aduan publik berdasarkan nomor tiket
     */
    public function lacak()
    {
        $noTiket = trim((string)$this->request->getGet('tiket'));
        $aduan = null;
        $tanggapans = [];

        if (!empty($noTiket)) {
            $aduan = $this->aduanModel->select('aduan.*, users.fullname')
                ->join('users', 'users.id = aduan.user_id', 'left')
                ->where('aduan.no_tiket', strtoupper($noTiket))
                ->first();

            if ($aduan) {
                $tanggapans = $this->tanggapanModel->select('tanggapan.*, users.fullname, users.role')
                    ->join('users', 'users.id = tanggapan.user_id', 'left')
                    ->where('tanggapan.aduan_id', $aduan['id'])
                    ->orderBy('tanggapan.created_at', 'ASC')
                    ->find();
            } else {
                session()->setFlashdata('error', "Nomor tiket '{$noTiket}' tidak ditemukan. Pastikan nomor tiket sudah benar (contoh: ADU-6EDAD8).");
            }
        }

        $data = [
            'title'      => 'Lacak Status Aduan - SIMA BAPELKES',
            'no_tiket'   => $noTiket,
            'aduan'      => $aduan,
            'tanggapans' => $tanggapans,
        ];

        return view('aduan/lacak', $data);
    }

    public function detail($id)
    {
        $role = session()->get('role');
        $userId = session()->get('id');

        $aduan = $this->aduanModel->select('aduan.*, users.fullname, users.role as user_role')
            ->join('users', 'users.id = aduan.user_id', 'left')
            ->where('aduan.id', $id)
            ->first();

        if (!$aduan) {
            return redirect()->to('/aduan')->with('error', 'Aduan tidak ditemukan.');
        }

        // Security check: non-admin can only see their own complaint
        if ($role !== 'admin' && $aduan['user_id'] != $userId) {
            return redirect()->to('/aduan')->with('error', 'Akses ditolak.');
        }

        // Load responses
        $tanggapans = $this->tanggapanModel->select('tanggapan.*, users.fullname, users.role')
            ->join('users', 'users.id = tanggapan.user_id', 'left')
            ->where('tanggapan.aduan_id', $id)
            ->orderBy('tanggapan.created_at', 'ASC')
            ->find();

        $data = [
            'title'      => 'Detail Aduan - ' . $aduan['no_tiket'],
            'aduan'      => $aduan,
            'tanggapans' => $tanggapans,
        ];

        return view('aduan/detail', $data);
    }

    public function tanggapi($id)
    {
        $aduan = $this->aduanModel->find($id);
        if (!$aduan) {
            return redirect()->to('/aduan')->with('error', 'Aduan tidak ditemukan.');
        }

        $tanggapanText = $this->request->getPost('tanggapan');
        if (empty(trim($tanggapanText))) {
            return redirect()->back()->with('error', 'Tanggapan tidak boleh kosong.');
        }

        $this->tanggapanModel->save([
            'aduan_id'  => $id,
            'user_id'   => session()->get('id'),
            'tanggapan' => $tanggapanText,
        ]);

        // Auto-change status to "Proses" if Admin responds and current status is Pending
        if (session()->get('role') === 'admin' && $aduan['status'] === 'Pending') {
            $this->aduanModel->update($id, ['status' => 'Proses']);
        }

        // WhatsApp notification to reporter if Admin responds and reporter has phone number
        if (session()->get('role') === 'admin' && !empty($aduan['no_telepon'])) {
            helper('whatsapp');
            $pelaporName = $aduan['nama_pelapor'] ?: 'Bpk/Ibu';
            $msg = "📢 *TANGGAPAN BARU ATAS ADUAN ANDA - SIMA BAPELKES*\n\n"
                 . "Halo *{$pelaporName}*,\n"
                 . "Admin telah memberikan tanggapan untuk aduan Anda:\n\n"
                 . "🎫 *Nomor Tiket:* {$aduan['no_tiket']}\n"
                 . "📝 *Judul:* {$aduan['judul']}\n"
                 . "💬 *Tanggapan Admin:*\n\"{$tanggapanText}\"\n\n"
                 . "Pantau aduan Anda selengkapnya di:\n" . base_url('aduan/lacak?tiket=' . $aduan['no_tiket']);
            send_whatsapp_notification($aduan['no_telepon'], $msg);
        }

        return redirect()->to('/aduan/detail/' . $id)->with('success', 'Tanggapan berhasil ditambahkan.');
    }

    public function updateStatus($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/aduan')->with('error', 'Akses ditolak.');
        }

        $aduan = $this->aduanModel->find($id);
        if (!$aduan) {
            return redirect()->to('/aduan')->with('error', 'Aduan tidak ditemukan.');
        }

        $status = $this->request->getPost('status');
        if (!in_array($status, ['Pending', 'Proses', 'Selesai'])) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $this->aduanModel->update($id, ['status' => $status]);

        // Add system message as response
        $this->tanggapanModel->save([
            'aduan_id'  => $id,
            'user_id'   => session()->get('id'),
            'tanggapan' => '[SISTEM] Status aduan diubah menjadi *' . $status . '*.',
        ]);

        // WhatsApp notification to reporter about status change
        if (!empty($aduan['no_telepon'])) {
            helper('whatsapp');
            $pelaporName = $aduan['nama_pelapor'] ?: 'Bpk/Ibu';
            $msg = "📢 *STATUS ADUAN DIPERBARUI - SIMA BAPELKES*\n\n"
                 . "Halo *{$pelaporName}*,\n"
                 . "Status penanganan aduan Anda telah diperbarui:\n\n"
                 . "🎫 *Nomor Tiket:* {$aduan['no_tiket']}\n"
                 . "📝 *Judul:* {$aduan['judul']}\n"
                 . "📌 *Status Baru:* *{$status}*\n\n"
                 . "Pantau status aduan Anda melalui tautan:\n" . base_url('aduan/lacak?tiket=' . $aduan['no_tiket']);
            send_whatsapp_notification($aduan['no_telepon'], $msg);
        }

        return redirect()->to('/aduan/detail/' . $id)->with('success', 'Status aduan berhasil diperbarui.');
    }

    public function rekap()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/aduan')->with('error', 'Akses ditolak.');
        }

        // Stats by category
        $db = \Config\Database::connect();
        $statsKategori = $db->table('aduan')
            ->select('kategori, count(id) as total')
            ->groupBy('kategori')
            ->get()->getResultArray();

        // Stats by status
        $statsStatus = $db->table('aduan')
            ->select('status, count(id) as total')
            ->groupBy('status')
            ->get()->getResultArray();

        // All complaints
        $allAduan = $this->aduanModel->select('aduan.*, users.fullname')
            ->join('users', 'users.id = aduan.user_id', 'left')
            ->orderBy('aduan.created_at', 'DESC')
            ->find();

        $data = [
            'title'          => 'Rekapitulasi Laporan Aduan',
            'stats_kategori' => $statsKategori,
            'stats_status'   => $statsStatus,
            'all_aduan'      => $allAduan,
        ];

        return view('aduan/rekap', $data);
    }
}

