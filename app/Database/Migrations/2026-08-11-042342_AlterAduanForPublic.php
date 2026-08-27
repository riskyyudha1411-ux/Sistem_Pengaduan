<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterAduanForPublic extends Migration
{
    public function up()
    {
        $driver = $this->db->DBDriver;

        if ($driver === 'SQLite3') {
            // SQLite does not support ALTER COLUMN NULL directly, so recreate table
            $this->db->query("PRAGMA foreign_keys = OFF");

            $this->db->query("
                CREATE TABLE IF NOT EXISTS aduan_temp (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INT NULL,
                    nama_pelapor VARCHAR(100) NULL,
                    no_telepon VARCHAR(20) NULL,
                    no_tiket VARCHAR(50) NOT NULL,
                    nama_pelatihan VARCHAR(150) NOT NULL,
                    judul VARCHAR(200) NOT NULL,
                    deskripsi TEXT NOT NULL,
                    kategori VARCHAR(50) NOT NULL,
                    lampiran VARCHAR(255) NULL,
                    status VARCHAR(20) DEFAULT 'Pending',
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
                )
            ");

            // Copy existing columns
            $this->db->query("
                INSERT INTO aduan_temp (id, user_id, no_tiket, nama_pelatihan, judul, deskripsi, kategori, lampiran, status, created_at, updated_at)
                SELECT id, user_id, no_tiket, nama_pelatihan, judul, deskripsi, kategori, lampiran, status, created_at, updated_at FROM aduan
            ");

            $this->db->query("DROP TABLE aduan");
            $this->db->query("ALTER TABLE aduan_temp RENAME TO aduan");
            $this->db->query("PRAGMA foreign_keys = ON");
        } else {
            // For MySQL / MariaDB / PostgreSQL
            $fields = [
                'nama_pelapor' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'null'       => true,
                    'after'      => 'user_id',
                ],
                'no_telepon' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '20',
                    'null'       => true,
                    'after'      => 'nama_pelapor',
                ],
            ];
            $this->forge->addColumn('aduan', $fields);

            $modifyFields = [
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
            ];
            $this->forge->modifyColumn('aduan', $modifyFields);
        }
    }

    public function down()
    {
        $driver = $this->db->DBDriver;

        if ($driver === 'SQLite3') {
            $this->db->query("PRAGMA foreign_keys = OFF");
            $this->db->query("
                CREATE TABLE IF NOT EXISTS aduan_temp (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INT NOT NULL,
                    no_tiket VARCHAR(50) NOT NULL,
                    nama_pelatihan VARCHAR(150) NOT NULL,
                    judul VARCHAR(200) NOT NULL,
                    deskripsi TEXT NOT NULL,
                    kategori VARCHAR(50) NOT NULL,
                    lampiran VARCHAR(255) NULL,
                    status VARCHAR(20) DEFAULT 'Pending',
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
                )
            ");
            $this->db->query("
                INSERT INTO aduan_temp (id, user_id, no_tiket, nama_pelatihan, judul, deskripsi, kategori, lampiran, status, created_at, updated_at)
                SELECT id, COALESCE(user_id, 1), no_tiket, nama_pelatihan, judul, deskripsi, kategori, lampiran, status, created_at, updated_at FROM aduan
            ");
            $this->db->query("DROP TABLE aduan");
            $this->db->query("ALTER TABLE aduan_temp RENAME TO aduan");
            $this->db->query("PRAGMA foreign_keys = ON");
        } else {
            $this->forge->dropColumn('aduan', ['no_telepon', 'nama_pelapor']);
        }
    }
}


