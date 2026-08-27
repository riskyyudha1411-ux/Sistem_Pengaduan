<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AduanSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Seed Users
        $users = [
            [
                'username'   => 'admin',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'fullname'   => 'Administrator Bapelkes',
                'role'       => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'peserta',
                'password'   => password_hash('peserta123', PASSWORD_DEFAULT),
                'fullname'   => 'Budi Utomo',
                'role'       => 'user',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $db->table('users')->insertBatch($users);

        // Get user_id for peserta
        $peserta = $db->table('users')->where('username', 'peserta')->get()->getRowArray();

        // Seed sample complaint (aduan)
        $aduan = [
            [
                'user_id'         => $peserta['id'],
                'no_tiket'        => 'ADU-' . strtoupper(bin2hex(random_bytes(3))),
                'nama_pelatihan'  => 'Pelatihan Kepemimpinan Pengawas (PKP) Angkatan V',
                'judul'           => 'AC Ruang Kelas Kelas A Kurang Dingin',
                'deskripsi'       => 'Selama kegiatan pembelajaran di Ruang Kelas A, AC seringkali mengeluarkan udara hangat. Mohon diperiksa demi kenyamanan belajar peserta pelatihan.',
                'kategori'        => 'Fasilitas',
                'status'          => 'Pending',
                'created_at'      => date('Y-m-d H:i:s', strtotime('-1 days')),
                'updated_at'      => date('Y-m-d H:i:s', strtotime('-1 days')),
            ],
            [
                'user_id'         => $peserta['id'],
                'no_tiket'        => 'ADU-' . strtoupper(bin2hex(random_bytes(3))),
                'nama_pelatihan'  => 'Pelatihan Jabatan Fungsional Bidan Ahli',
                'judul'           => 'Menu Makanan Kurang Variatif',
                'deskripsi'       => 'Makanan sore selama 3 hari terakhir selalu didominasi menu ayam. Mohon ada variasi protein lain seperti ikan atau daging sapi.',
                'kategori'        => 'Konsumsi',
                'status'          => 'Proses',
                'created_at'      => date('Y-m-d H:i:s', strtotime('-3 hours')),
                'updated_at'      => date('Y-m-d H:i:s', strtotime('-3 hours')),
            ]
        ];

        $db->table('aduan')->insertBatch($aduan);
    }
}
