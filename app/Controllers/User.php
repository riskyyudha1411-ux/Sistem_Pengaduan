<?php

namespace App\Controllers;

use App\Models\UserModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class User extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Halaman kelola user (admin only)
     */
    public function index()
    {
        // Cek apakah admin
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $users = $this->userModel->orderBy('created_at', 'DESC')->findAll();

        // Load WhatsApp settings
        $settingsPath = WRITEPATH . 'wa_settings.json';
        $waSettings = [
            'enabled'      => false,
            'token'        => '',
            'admin_number' => '',
        ];

        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true);
            $waSettings = array_merge($waSettings, $settings ?: []);
        }

        $data = [
            'title'       => 'Kelola User - SIMA BAPELKES',
            'users'       => $users,
            'waSettings'  => $waSettings,
        ];

        return view('user/kelola', $data);
    }

    /**
     * Download template Excel untuk import user
     */
    public function downloadTemplate()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import User');

        // Header styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '5D87FF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $noteStyle = [
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '888888'],
                'size' => 10,
            ],
        ];

        // Set headers
        $headers = ['No', 'Username', 'Password', 'Nama Lengkap', 'Role'];
        $columns = ['A', 'B', 'C', 'D', 'E'];

        foreach ($headers as $i => $header) {
            $sheet->setCellValue($columns[$i] . '1', $header);
        }

        // Apply header style
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(15);

        // Example data row
        $sheet->setCellValue('A2', 1);
        $sheet->setCellValue('B2', 'contoh_user');
        $sheet->setCellValue('C2', 'password123');
        $sheet->setCellValue('D2', 'Nama Lengkap Contoh');
        $sheet->setCellValue('E2', 'user');
        $sheet->getStyle('A2:E2')->applyFromArray($dataStyle);
        $sheet->getStyle('A2:E2')->applyFromArray($noteStyle);

        // Notes
        $sheet->setCellValue('A4', 'CATATAN:');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFCC0000'));
        $sheet->setCellValue('A5', '1. Baris pertama (header) dan baris kedua (contoh) akan otomatis di-skip saat import.');
        $sheet->setCellValue('A6', '2. Isi data mulai dari baris ke-3.');
        $sheet->setCellValue('A7', '3. Kolom "Role" hanya menerima nilai: user atau admin');
        $sheet->setCellValue('A8', '4. Kolom "Username" harus unik (tidak boleh duplikat).');
        $sheet->setCellValue('A9', '5. Password minimal 6 karakter, akan di-hash otomatis saat import.');
        $sheet->setCellValue('A10', '6. Semua kolom wajib diisi.');

        // Merge notes cells for readability
        foreach (range(5, 10) as $row) {
            $sheet->mergeCells("A{$row}:E{$row}");
        }

        // Download
        $filename = 'Template_Import_User_SIMA_BAPELKES.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    /**
     * Import user dari file Excel
     */
    public function import()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        // Validate file upload
        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid. Silakan upload file Excel (.xlsx) yang benar.');
        }

        // Check extension
        $ext = $file->getClientExtension();
        if (!in_array($ext, ['xlsx', 'xls'])) {
            return redirect()->back()->with('error', 'Format file tidak didukung. Gunakan format .xlsx atau .xls');
        }

        // Check file size (max 2MB)
        if ($file->getSize() > 2 * 1024 * 1024) {
            return redirect()->back()->with('error', 'Ukuran file terlalu besar. Maksimal 2MB.');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Remove header row (index 0) and example row (index 1)
            if (count($rows) <= 2) {
                return redirect()->back()->with('error', 'File tidak berisi data. Isi data mulai dari baris ke-3.');
            }

            $successCount = 0;
            $failedRows = [];
            $validRoles = ['user', 'admin'];

            // Process from row index 2 (baris ke-3 di Excel)
            for ($i = 2; $i < count($rows); $i++) {
                $row = $rows[$i];
                $rowNumber = $i + 1; // Nomor baris di Excel (1-indexed)

                // Skip completely empty rows
                if (empty(array_filter($row, fn($cell) => !empty(trim((string)$cell))))) {
                    continue;
                }

                $username = trim((string)($row[1] ?? ''));
                $password = trim((string)($row[2] ?? ''));
                $fullname = trim((string)($row[3] ?? ''));
                $role     = strtolower(trim((string)($row[4] ?? '')));

                // Validation
                $errors = [];

                if (empty($username)) {
                    $errors[] = 'Username kosong';
                } elseif (strlen($username) < 3) {
                    $errors[] = 'Username minimal 3 karakter';
                } elseif (strlen($username) > 50) {
                    $errors[] = 'Username maksimal 50 karakter';
                } elseif ($this->userModel->where('username', $username)->first()) {
                    $errors[] = "Username '{$username}' sudah terdaftar";
                }

                if (empty($password)) {
                    $errors[] = 'Password kosong';
                } elseif (strlen($password) < 6) {
                    $errors[] = 'Password minimal 6 karakter';
                }

                if (empty($fullname)) {
                    $errors[] = 'Nama lengkap kosong';
                } elseif (strlen($fullname) > 100) {
                    $errors[] = 'Nama lengkap maksimal 100 karakter';
                }

                if (empty($role)) {
                    $errors[] = 'Role kosong';
                } elseif (!in_array($role, $validRoles)) {
                    $errors[] = "Role '{$role}' tidak valid (gunakan: user/admin)";
                }

                if (!empty($errors)) {
                    $failedRows[] = [
                        'row'    => $rowNumber,
                        'name'   => $fullname ?: $username ?: '-',
                        'errors' => $errors,
                    ];
                    continue;
                }

                // Insert user
                try {
                    $this->userModel->save([
                        'username' => $username,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'fullname' => $fullname,
                        'role'     => $role,
                    ]);
                    $successCount++;
                } catch (\Exception $e) {
                    $failedRows[] = [
                        'row'    => $rowNumber,
                        'name'   => $fullname,
                        'errors' => ['Gagal menyimpan: ' . $e->getMessage()],
                    ];
                }
            }

            // Build result message
            $resultData = [
                'import_success' => $successCount,
                'import_failed'  => $failedRows,
            ];

            session()->setFlashdata('import_result', $resultData);

            if ($successCount > 0 && empty($failedRows)) {
                return redirect()->to('/user')->with('success', "Berhasil mengimport {$successCount} user.");
            } elseif ($successCount > 0 && !empty($failedRows)) {
                return redirect()->to('/user')->with('warning', "Berhasil mengimport {$successCount} user, namun " . count($failedRows) . " baris gagal.");
            } else {
                return redirect()->to('/user')->with('error', 'Tidak ada user yang berhasil diimport. Periksa kembali data Anda.');
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Hapus user
     */
    public function delete($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        // Prevent deleting own account
        if ((int)$id === (int)session()->get('id')) {
            return redirect()->to('/user')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/user')->with('error', 'User tidak ditemukan.');
        }

        $this->userModel->delete($id);

        return redirect()->to('/user')->with('success', "User '{$user['fullname']}' berhasil dihapus.");
    }

    /**
     * Simpan user baru satu per satu (manual)
     */
    public function store()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'fullname' => 'required|min_length[3]|max_length[100]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[user,admin]',
        ];

        if (!$this->validate($rules)) {
            $errors = implode(', ', $this->validator->getErrors());
            return redirect()->to('/user')->with('error', 'Gagal menambahkan user: ' . $errors)->withInput();
        }

        $this->userModel->save([
            'username' => $this->request->getPost('username'),
            'fullname' => $this->request->getPost('fullname'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => $this->request->getPost('role'),
        ]);

        return redirect()->to('/user')->with('success', 'User ' . $this->request->getPost('fullname') . ' berhasil ditambahkan.');
    }

    /**
     * Update user (manual)
     */
    public function update($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/user')->with('error', 'User tidak ditemukan.');
        }

        // Validate
        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'fullname' => 'required|min_length[3]|max_length[100]',
            'role'     => 'required|in_list[user,admin]',
        ];

        // Password is optional on edit
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            $errors = implode(', ', $this->validator->getErrors());
            return redirect()->to('/user')->with('error', 'Gagal memperbarui user: ' . $errors);
        }

        $updateData = [
            'id'       => $id,
            'username' => $this->request->getPost('username'),
            'fullname' => $this->request->getPost('fullname'),
            'role'     => $this->request->getPost('role'),
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->save($updateData);

        // If admin is updating their own role/username/fullname, update the session
        if ((int)$id === (int)session()->get('id')) {
            session()->set([
                'username'  => $updateData['username'],
                'fullname'  => $updateData['fullname'],
                'role'      => $updateData['role'],
            ]);
        }

        return redirect()->to('/user')->with('success', 'Data user ' . $updateData['fullname'] . ' berhasil diperbarui.');
    }

    /**
     * Simpan Konfigurasi WhatsApp Admin
     */
    public function saveWaSettings()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $enabled = $this->request->getPost('wa_enabled') ? true : false;
        $token = trim((string)$this->request->getPost('wa_token'));
        $adminNumber = trim((string)$this->request->getPost('wa_admin_number'));

        // Basic validation if enabled
        if ($enabled) {
            if (empty($token)) {
                return redirect()->to('/user')->with('error', 'Token Fonnte wajib diisi jika notifikasi diaktifkan.');
            }
            if (empty($adminNumber)) {
                return redirect()->to('/user')->with('error', 'Nomor WA Admin wajib diisi jika notifikasi diaktifkan.');
            }
        }

        $settings = [
            'enabled'      => $enabled,
            'token'        => $token,
            'admin_number' => $adminNumber,
        ];

        $settingsPath = WRITEPATH . 'wa_settings.json';
        if (file_put_contents($settingsPath, json_encode($settings, JSON_PRETTY_PRINT))) {
            return redirect()->to('/user')->with('success', 'Konfigurasi WhatsApp berhasil disimpan.');
        }

        return redirect()->to('/user')->with('error', 'Gagal menyimpan konfigurasi WhatsApp.');
    }
}
